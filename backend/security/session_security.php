<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function secureSessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_ip'])) {
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['created_at'] = time();
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function validateSessionFingerprint() {
    if (!isset($_SESSION['user_ip'], $_SESSION['user_agent'])) {
        return false;
    }
    if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
        return false;
    }
    return true;
}

function setSessionTimeout($timeout = 1800) {
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
        return true;
    }
    $elapsed = time() - $_SESSION['created_at'];
    if ($elapsed > $timeout) {
        session_unset();
        session_destroy();
        return false;
    }
    return true;
}

function secureSession() {
    session_regenerate_id(true);
}
?>