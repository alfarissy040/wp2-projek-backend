<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\History;
use App\Models\Menu;
use App\Models\Order;
use CodeIgniter\I18n\Time;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;

class OrderController extends BaseController
{
    public $orderModel, $historyModel, $menuModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->historyModel = new History();
        $this->menuModel = new Menu();
    }

    public function index(){
        $responseData = [];

        foreach ($this->orderModel->findAll() as $order) {
            $menuItems = $this->menuModel->getMenuItemsByIds($order['menu_ids']);
    
            $menuList = [];
            foreach ($menuItems as $i => $menuItem) {
                $menuList[] = [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'price' => $menuItem->price,
                    'image' => $menuItem->image,
                    'description' => $menuItem->description,
                    'quantities' => json_decode($order['quantities'])[$i],
                    'status' => $menuItem->status,
                ];
            }
    
            $responseData[] = [
                'code_transaction' => $order['unique_id'],
                'status' => $order['status'],
                'total_price' => $order['total_amount'],
                'created_at' => $order['created_at'],
                'updated_at' => $order['updated_at'],
                'menu_items' => $menuList,
            ];
        }
    
        return $this->response->setJSON($responseData);
    }

    public function order()
    {
        $menu_ids = [];
        $prices = [];
        $quantities = [];
        $total_price = [];
        $menus = [];
        foreach ($this->request->getVar('orders') as $i => $menu) {
            $menu_ids[] = $menu->id;
            $prices[] = $menu->price;
            $quantities[] = $menu->quantities;
            $total_price[] = $menu->price * $menu->quantities;
            $menus[] = [
                'id' => $menu->id,
                'price' => $menu->price,
                'quantity' => $menu->quantities,
                'name' => $menu->name,
            ];
            
            $history = $this->historyModel->getHistoryByMenuId($menu->id)->getRow();
            if(!$history){
                $this->historyModel->store([
                    'menu_id' => $menu->id,
                    'sold' => $menu->quantities
                ]);
            }else{
                $this->historyModel->update($history->id, [
                    'sold' => $history->sold + $menu->quantities
                ]);
            }

        }
        $uniqueId = bin2hex(random_bytes(10));
        $this->orderModel->store([
            'menu_ids' =>  json_encode($menu_ids),
            'prices' => json_encode($prices),
            'quantities' => json_encode($quantities),
            'unique_id' => $uniqueId,
            'total_amount' => array_sum($total_price),
            'status' => 0,
            'created_at' => Time::now('Asia/Jakarta')
        ]);

        $body = [
            'transaction_details' => [
                "order_id" => $uniqueId,
                "gross_amount"=> array_sum($total_price)
            ], 
            'item_details' => $menus,
            'customer_details' => [
                'first_name' => $this->request->getVar('firstname'), 
                'last_name' => $this->request->getVar('lastname'),
                'email' => $this->request->getVar('email'),
                'phone' => $this->request->getVar('phone')
            ],
            'enabled_payments' => ['bca_va'],
            "expiry" => [
                "start_time" => Time::now('Asia/Jakarta')->format('Y-m-d H:i:s T'),
                "unit" => 'day',
                "duration" => 1
            ]
        ];
        $headers = [
              'Accept' => 'application/json',
              'Content-Type' => 'application/json',
              "Authorization" => 'Basic '. base64_encode('SB-Mid-server-9V2-7cdPsfeiAfR3EGxvq4QV')
        ];
        $client = new Client();
        $req = new Psr7Request('POST', 'https://app.sandbox.midtrans.com/snap/v1/transactions', $headers, json_encode($body));
        $promise = $client->sendAsync($req);
        $response = $promise->wait();
        // Get the response body as a string
        $responseBody = $response->getBody()->getContents();

        // Convert the JSON response to an associative array
        $data = json_decode($responseBody, true);
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function callback()
    {
        if($this->request->getVar('transaction_status') == 'settlement'){
            $signature = hash('sha512', $this->request->getVar('order_id') . $this->request->getVar('status_code') . $this->request->getVar('gross_amount') . 'SB-Mid-server-9V2-7cdPsfeiAfR3EGxvq4QV');

            if ($signature !== $this->request->getVar('signature_key')) {
                return 'not found';
            }
    
            $this->orderModel->updateStatusOrder(['status' => true, 'updated_at' => Time::now('Asia/Jakarta')], $this->request->getVar('order_id'));
        }
    }
}
