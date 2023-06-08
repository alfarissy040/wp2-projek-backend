<?php

namespace App\Models;

use CodeIgniter\Model;

class Order extends Model
{
    protected $table            = 'orders';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    public function getOrderId($id)
    {
        return $this->db->table($this->table)->getWhere(['order_id' => $id]);
    }

    public function store($params)
    {
        return $this->db->table($this->table)->insert($params);
    }

    public function updateStatusOrder($params, $id)
    {
            return $this->db->table($this->table)->where('unique_id', $id)->update($params);
    }
}
