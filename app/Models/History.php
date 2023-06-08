<?php

namespace App\Models;

use CodeIgniter\Model;

class History extends Model
{
    protected $table            = 'histories';
    protected $allowedFields = ['menu_id', 'sold'];

    public function store($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    public function getHistoryByMenuId($id)
    {
        return $this->db->table($this->table)->getWhere(['menu_id' => $id]);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
