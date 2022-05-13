<?php
session_start();
require "../inc/functions.php";
$conn = db_connect();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (!isset($_GET["challenge_name"]) || !isset($_GET["filename"])) return false;   
if (!($challenge_id = get_challenge_id($conn, $_GET["challenge_name"]))) return false;
if (!($file_path = challenge_file_path($_GET["challenge_name"], $_GET["filename"]))) return false;
if (!challenge_resource_exists_on_db($conn, $challenge_id, $_GET["filename"])) return false;

header("Content-Description: File Transfer"); 
header("Content-Type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=\"". basename($file_path) ."\""); 
readfile ($file_path);
?>