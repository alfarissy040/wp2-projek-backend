<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\History;
use App\Models\Menu;
use App\Models\Order;

class DashboardController extends BaseController
{
    public $orderModel, $historyModel, $menuModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->historyModel = new History();
        $this->menuModel = new Menu();
    }

    public function index()
    {

        $orders = $this->orderModel->findAll();
        $histories = $this->historyModel->findAll();
        $menus = $this->menuModel->findAll();
        $totalAmount = 0;
        $totalSold = 0;
        foreach ($histories as $history) {
            $totalSold += $history['sold'];
        }
        foreach ($orders as &$order) {
            $totalAmount += $order['total_amount'];
        }

        return $this->response->setJSON([
            'amount' => $totalAmount,
            'sold' => $totalSold,
            'total_menu' => count($menus)
        ]);


    }
}
