<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\History;
use App\Models\Menu;

class MenuController extends BaseController
{

    protected $menuModel, $historyModel;

    public function __construct()
    {
        $this->menuModel = new Menu();
        $this->historyModel = new History();
    }

    public function index()
    {
        return $this->response->setJSON([
            'status' => 'success',
            'data' => $this->menuModel->findAll()
        ], true);
    }

    public function popular()
    {
        $popularMenus = $this->menuModel
            ->select('menus.*, histories.sold')
            ->join('histories', 'histories.menu_id = menus.id', 'left')
            ->orderBy('histories.sold', 'desc')
            ->findAll();

        return $this->response->setJSON($popularMenus);
    }

    public function store()
    {
        $image = $this->menuModel->uploadFile($this->request->getFile('image'));
		if($image['status']){
			$data = [
				'name' => $this->request->getPost('name'),
				'price' => $this->request->getPost('price'),
				'description' => $this->request->getPost('description'),
				'image' =>  'uploads/'.$image['filename'],
			];
			
			$insertId = $this->menuModel->insert($data);
            $menu = $this->menuModel->getId($insertId)->getRow();
            return $this->response->setJSON([
                'message' => 'success',
                'data' => $menu
            ]);
		}else{
			return $this->response->setJSON([
                'message' => 'error',
                'data' => ''
            ]);
		}
    }

}
