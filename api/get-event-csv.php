<?php
require_once __DIR__ . "/../inc/init.php";

if (!isset($_SESSION["user_id"]) || get_user_role($conn, $_SESSION["user_id"]) != 'A') return false;
if (!isset($_GET["event_name"])) return false;
 
$event_name = $_GET["event_name"];

if (!($event_id = get_event_id($conn, $event_name))) return false;

header("Content-Description: File Transfer"); 
header("Content-Type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=\"". $event_name .".csv\""); 
echo get_event_csv($conn, $event_id);
?>