<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Tühjendab sessiooni andmed
$_SESSION = array();

// Kustutab sessiooni küpsise
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
// Hävitab sessiooni
session_destroy();
header("Location: login.php");
exit();
?>