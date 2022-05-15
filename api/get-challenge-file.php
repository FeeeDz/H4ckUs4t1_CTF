<?php
require "../inc/init.php";

if (!isset($_GET["challenge_name"]) || !isset($_GET["filename"])) return false;
 
$challenge_name = $_GET["challenge_name"];
$filename = $_GET["filename"]; 

if (!($challenge_id = get_challenge_id($conn, $challenge_name))) return false;
if (!($file_path = get_challenge_resource_path($challenge_name, $filename))) return false;
if (!is_challenge_resource($conn, $challenge_id, $filename)) return false;

header("Content-Description: File Transfer"); 
header("Content-Type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=\"". basename($file_path) ."\""); 
readfile ($file_path);
?>