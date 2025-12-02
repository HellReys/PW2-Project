<?php
require_once '../../classes/Task.php';
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Login required']);
    exit;
}

$keyword = $_GET['q'] ?? '';
$task = new Task();
$tasks = $task->search($_SESSION['user_id'], $keyword);
echo json_encode($tasks);
?>
