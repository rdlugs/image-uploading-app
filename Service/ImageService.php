<?php

namespace Service;

require $_SERVER['DOCUMENT_ROOT'] . '/Database/DBConnection.php';
require $_SERVER['DOCUMENT_ROOT'] . '/Helper/FileUpload.php';

use Database\DBConnection;
use Helper\FileUpload;
use PDO;

class ImageService
{
    private $db;

    public function __construct()
    {
        $this->db = DBConnection::connect();
    }

    public function getAllDatas()
    {
        try {
            $query = $this->db->prepare("SELECT * FROM images WHERE deleted_at IS NULL ORDER BY created_at DESC");
            $query->execute();

            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getData($id)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM images WHERE deleted_at IS NULL AND id=:id");
            $query->execute(['id' => $id]);

            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteData($id)
    {
        try {
            $this->db->beginTransaction();
            $query = $this->db->prepare("UPDATE images SET deleted_at=now() WHERE id=:id");
            $query->execute([
                'id' => $id
            ]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    public function insertorUpdateImage($datas)
    {
        try {
            $this->db->beginTransaction();

            $file = new FileUpload();

            if ($datas['id']) {

                $query = $this->db->prepare("UPDATE images SET title=:title WHERE id=:id");
                $query->execute([
                    'id' => $datas['id'],
                    'title' => $datas['title'],
                ]);

                if ($datas['image']) {
                    $file->uploadFile($datas['image']);

                    if (empty($file->errors)) {
                        $update = $this->db->prepare("UPDATE images SET filename=:filename, image=:image WHERE id=:id");
                        $update->execute([
                            'id' => $datas['id'],
                            'filename' => $file->original_filename,
                            'image' => $file->filename
                        ]);
                    }
                }
            } else {
                $query = $this->db->prepare("INSERT INTO images(title) VALUES(:title) ");
                $query->execute([
                    'title' => $datas['title'],
                ]);

                $lastInsertId = $this->db->lastInsertId();

                $file->uploadFile($datas['image']);

                if (empty($file->errors)) {
                    $update = $this->db->prepare("UPDATE images SET filename=:filename, image=:image WHERE id=:id");
                    $update->execute([
                        'id' => $lastInsertId,
                        'filename' => $file->original_filename,
                        'image' => $file->filename
                    ]);
                }
            }


            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return "Error: " . $e->getMessage();
        }
    }
}
