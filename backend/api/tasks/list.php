<?php
require_once '../../classes/Task.php';
session_start();
header('Content-Type: application/json');



if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Login required']);
    exit;
}



$task = new Task();
$tasks = $task->list($_SESSION['user_id']);
echo json_encode($tasks);
?>
