<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Order extends Migration
{
    public function up()
    {
        $this->forge->addField('id');
        $this->forge->addField([
            'menu_ids' => [
                'type' => 'ARRAY',
                'constraint' => 11,
            ],
            'unique_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'price' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'qty' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'total_price' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
        ]);
        $this->forge->createTable('menus');
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
