<?php
header('Content-Type: application/json');
session_start();
require_once '../classes/User.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(!isset($data['email'], $data['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password required']);
        exit;
    }
    
    $user = new User();
    if($user->login($data['email'], $data['password'])) {
        session_regenerate_id(true);
        echo json_encode(['status' => 'success', 'message' => 'Login Successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect email or password']);
    }
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Login error: ' . $e->getMessage()]);
}
?>