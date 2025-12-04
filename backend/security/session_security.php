<?php
session_start();

// IP and UserAgent Control
if(!isset($_SESSION['user_ip'])){
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

if($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] || 
   $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']){
    session_destroy();
    die("Session hijacking detected");
}

// session regenerate
function secureSession(){
    session_regenerate_id(true);
}
?>
