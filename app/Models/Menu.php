<?php

namespace App\Models;

use CodeIgniter\Model;

class Menu extends Model
{
    protected $table            = 'menus';
    protected $allowedFields = ['name', 'price', 'description', 'image'];


    public function store($data)
    {
        return $this->db->table($this->table)->insert($data);
    }

    public function getMenuItemsByIds($ids) {
        $builder = $this->db->table($this->table);
        $builder->whereIn('id', json_decode($ids));
        $query = $builder->get();

        return $query->getResult();
    }

    public function getId($id)
    {
        return $this->db->table($this->table)->getWhere(['id' => $id]);
    }

    public function updateMenu($data, $id)
    {
        return $this->db->table($this->table)->where('id', $id)->update($data);
    }

    public function uploadFile($imageFile)
    {
        // Get the uploaded file

        // Check if file is uploaded successfully
        if ($imageFile->isValid() && ! $imageFile->hasMoved())
        {
            // Move the uploaded file to a writable directory
            $newName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads', $newName);

            // Insert the file information into the database or perform other actions
            // ...

            // Redirect or return a response
            // return redirect()->to('/success');
            return ['status' => true, 'filename' => $newName];
        }
        else
        {
            // Handle file upload error
            // return redirect()->back()->with('error', 'Failed to upload file.');
            return ['status' => false];
        }
    }

}
