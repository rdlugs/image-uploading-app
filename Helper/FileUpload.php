<?php

namespace Helper;

class FileUpload
{
    public string $original_filename = '';
    public string $filename = '';
    public array $errors = [];
    private array $extensions = [
        'jpeg', 'jpg', 'png'
    ];

    public function __construct()
    {
    }

    public function uploadFile($file, $folder = '/Assets/images/')
    {
        $ext = $this->getExtension($file);

        if (!in_array($ext, $this->extensions))
            $errors[] = "File is not allowed";

        if (empty($errors)) {
            $this->original_filename = $file['name'];
            $this->filename = $folder . base64_encode(date('YmdH:i:s') . uniqid() . trim($file['name'])) . '.' . $ext;

            move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $this->filename);

            return [
                'filename' => $this->filename,
                'original_filename' => $this->original_filename,
            ];
        }
    }

    public function getExtension($file)
    {
        if ($file['name']) {
            $tmp = explode('.', $file['name']);
            return strtolower(end($tmp));
        }

        return null;
    }
}
