<?php
require_once '../classes/User.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['username'], $data['email'], $data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing space']);
    exit;
}

$user = new User();
if($user->register($data['username'], $data['email'], $data['password'])) {
    echo json_encode(['status' => 'success', 'message' => 'Register Successful']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Register Unsuccessful']);
}
?>
