<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Service/ImageService.php';

use Service\ImageService;

$imageService = new ImageService();
$method = isset($_POST['method']) ? $_POST['method'] : null;
$errors = [];


/**
 * Get All Datas from db
 * 
 */
if ($method == 'getAllDatas' && method_exists($imageService, $method)) {
    $result = $imageService->{$method}();
    echo json_encode($result);
    return;
}

/**
 * 
 * Edit Image
 * 
 */
if ($method == 'getData' && method_exists($imageService, $method)) {
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (!$id) {
        $errors['id'] = "Undefined ID";
        echo json_encode($errors);
        return;
    }

    $result = $imageService->{$method}($id);
    echo json_encode($result);
    return;
}


/**
 * 
 * Delete Image
 * 
 */
if ($method == 'deleteData' && method_exists($imageService, $method)) {
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (!$id) {
        $errors['id'] = "Undefined ID";
        return;
    }

    $result = $imageService->{$method}($id);
    echo json_encode($result);
    return;
}


/**
 * Insert New Datas
 * 
 * 
 */
if ($method == 'insertorUpdateImage' && method_exists($imageService, $method)) {


    if (!isset($_POST['title']) || !$_POST['title']) {
        $errors['title'] = 'This field is requried';
    }


    if (!isset($_POST['id']) || !$_POST['id']) {
        if (!isset($_FILES['file']) || !$_FILES['file']) {
            $errors['file'] = 'Please select Image to Upload';
        }
    }

    if (count($errors) > 0) {
        echo json_encode(['errors' => $errors]);
        return;
    }

    $datas = [
        'title' => $_POST['title'],
        'image' => isset($_FILES['file']) ? $_FILES['file'] : null,
        'id' => isset($_POST['id']) ? $_POST['id'] : null
    ];

    $result = $imageService->{$method}($datas);
    echo json_encode($result);
    return;
}


echo json_encode(['Undefined Method Name']);
