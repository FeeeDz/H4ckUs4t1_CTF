<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


// Server scuola
// $site_directory = "~quintaa2122/informatica/H4ckUs4t1_CTF";
// $private_dir = "/home/quintaa2122/informatica/H4ckUs4t1_CTF_private";

// Server privato
$site_directory = "";
//$private_dir = "/var/www/H4ckUs4t1_CTF_private";
$private_dir = "/root/H4ckUs4t1_CTF_private";

session_start();

require "functions.php";
$conn = db_connect();

if (isset($_SESSION["user_id"]) && !is_account_active($conn, $_SESSION["user_id"])) {
    logout();
    header("Refresh:0");
}

require $private_dir."/config/email-config.php";

?>
