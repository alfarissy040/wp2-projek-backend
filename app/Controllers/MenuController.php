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

    public function update($id)
    {
        $menu = $this->menuModel->getId($id)->getRow();
         if($this->request->getFile('image')){
            $gambar = $this->menuModel->uploadFile($this->request->getFile('image'));
            if($_FILES['image']['error'] == 0){
                $file_path = FCPATH. $menu->image;
                unlink($file_path);
            }
         }else{
            $gambar = ['status' => false];
         }
            // return $this->response->setJSON($gambar);
			$data = [
				'name' => $this->request->getVar('name') ?? $menu->name,
				'price' => $this->request->getVar('price') ?? $menu->price,
				'description' => $this->request->getVar('description') ?? $menu->description,
				'image' =>  $gambar['status'] ? 'uploads/'.$gambar['filename'] : $menu->image,
			];
			
			$this->menuModel->updateMenu($data, $id);
            $menuUpdated = $this->menuModel->getId($id)->getRow();

			return $this->response->setJSON([
                'message' => 'success',
                'data' => $menuUpdated
            ]);
    }

    function delete($id){
        $menu = $this->menuModel->getId($id)->getRow();
        $file_path = base_url() . $menu->image;
        unlink($file_path);
        $this->menuModel->delete(['id' => $id]);

		return $this->response->setJSON([
            'message' => 'success',
            'data' => null
        ]);
    }

}
