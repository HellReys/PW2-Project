<?php
require_once '../classes/User.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['email'], $data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing']);
    exit;
}

$user = new User();
if($user->login($data['email'], $data['password'])) {
    echo json_encode(['status' => 'success', 'message' => 'Login Successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect email or password']);
}
?>
