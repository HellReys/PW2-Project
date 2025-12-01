<?php
require_once '../classes/User.php';
header('Content-Type: application/json');

$user = new User();
$user->logout();

echo json_encode(['status' => 'success', 'message' => 'Closing']);
?>
