<?php
require_once '../../classes/Task.php';
session_start();
header('Content-Type: application/json');

// login Control
if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'error','message'=>'Login required']);
    exit;
}

// check file
if(!isset($_FILES['file']) || $_FILES['file']['error'] != 0){
    echo json_encode(['status'=>'error','message'=>'No file uploaded']);
    exit;
}

// task id
if(!isset($_POST['task_id'])){
    echo json_encode(['status'=>'error','message'=>'Task ID missing']);
    exit;
}

$task = new Task();
$uploadDir = __DIR__ . '/../../uploads/';
$filename = basename($_FILES['file']['name']);
$targetFile = $uploadDir . $filename;


// save thbe files
if(move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)){
    // Save in DB
    $task->addFile($_POST['task_id'], $filename);
    echo json_encode(['status'=>'success','message'=>'File uploaded']);
} else {
    echo json_encode(['status'=>'error','message'=>'Upload failed']);
}
?>
