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

    public function getId($id)
    {
        return $this->db->table($this->table)->getWhere(['id' => $id]);
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
