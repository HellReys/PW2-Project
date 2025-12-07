<?php
header('Content-Type: application/json');
session_start();
require_once '../classes/User.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(!isset($data['username'], $data['email'], $data['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Username, email and password required']);
        exit;
    }
    
    $user = new User();
    if($user->register($data['username'], $data['email'], $data['password'])) {
        echo json_encode(['status' => 'success', 'message' => 'Registration Successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration Failed. Email may already exist.']);
    }
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Registration error: ' . $e->getMessage()]);
}
?>