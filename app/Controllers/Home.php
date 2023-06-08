<?php

namespace App\Controllers;
use CodeIgniter\HTTP\ResponseInterface;


class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function about()
    {
        return $this->response->setJSON([
            'name' => 'Fadlie Ferdiyansah', 
            'age' => 21
        ]);
    }
}
