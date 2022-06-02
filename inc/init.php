<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


// Server scuola
// $site_directory = "~quintaa2122/informatica/CTF_h4ckus4t1";
// $private_dir = "/home/quintaa2122/informatica/CTF_h4ckus4t1_private";

// Server privato
$site_directory = "";
$private_dir = "/var/www/CTF_h4ckus4t1_private";

session_start();

require "functions.php";
$conn = db_connect();

if (isset($_SESSION["user_id"]) && !is_account_active($conn, $_SESSION["user_id"])) {
    logout();
    header("Refresh:0");
}

require $private_dir."/config/email-config.php";

?>