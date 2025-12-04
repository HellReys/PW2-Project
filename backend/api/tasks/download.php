<?php
require_once '../../classes/Task.php';
require_once '../../security/session_security.php';
require_once '../../security/xss.php';

session_start();

if(!isset($_SESSION['user_id'])){
    die("Login required");
}

if(!isset($_GET['id'])){
    die("File ID required");
}

$task = new Task();
$file = $task->getFileById($_GET['id']);

if(!$file){
    die("File not found");
}

$filePath = __DIR__ . '/../../uploads/' . $file['file_path'];

if(file_exists($filePath)){
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    die("File missing on server");
}
?>
