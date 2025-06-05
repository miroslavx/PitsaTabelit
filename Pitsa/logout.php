<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Täielikult puhasta sessioon
$_SESSION = array();

// Kui kasutatakse küpsiste sessioone, kustuta ka need
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hävita sessioon
session_destroy();

// Suuna avalehele
header('Location: index.php');
exit();
?>