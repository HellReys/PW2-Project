<?php
header('Content-Type: application/json');
require_once '../security/session_security.php';
require_once '../classes/User.php';

// Start secure session
secureSessionStart();

// Check authentication
if(!isLoggedIn()) {
    echo json_encode(['status' => 'unauthenticated', 'message' => 'Login required']);
    exit;
}

// Validate session fingerprint
if(!validateSessionFingerprint()) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
    exit;
}

// Check session timeout
if(!setSessionTimeout(1800)) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired']);
    exit;
}

// Return authenticated user info
echo json_encode([
    'status' => 'authenticated',
    'user_id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'role' => $_SESSION['role'] ?? 'user'
]);
?>