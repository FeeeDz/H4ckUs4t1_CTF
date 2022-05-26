<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    session_start();
    require "functions.php";
    $conn = db_connect();
    if (isset($_SESSION["user_id"]) && !check_if_user_exists($conn, $_SESSION["user_id"])) {
        logout();
        header("Refresh:0");
    } 
?>