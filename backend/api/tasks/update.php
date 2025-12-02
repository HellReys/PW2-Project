<?php
require_once '../../classes/Task.php';
session_start();
header('Content-Type: application/json');



// Login Control
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'Login required']);
    exit;
}



// Taking json by using POST
$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['id'], $data['title'], $data['description'], $data['category_id'], $data['due_date'])) {
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}



$task = new Task();
$result = $task->update($data['id'], $data['title'], $data['description'], $data['category_id'], $data['due_date']);

echo json_encode($result ? ['status'=>'success','message'=>'Task updated'] : ['status'=>'error','message'=>'Update failed']);
?>
