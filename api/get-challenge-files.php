<?php
session_start();
require "../inc/functions.php";
$conn = db_connect();

if($_SESSION["role"] != 'A' || !isset($_GET["challenge_name"])) return false;

$challenge_id = get_challenge_id($conn, $_GET["challenge_name"]);
if(!$challenge_id) return false;

$challenge_data = get_challenge_data($conn, $challenge_id);

$resources = scandir("../challenges/".$challenge_data["category"]."/".$challenge_data["challenge_name"]);
$resources = array_values(array_diff($resources, array('.', '..')));

echo json_encode($resources);
?>