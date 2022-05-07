<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();
require "../inc/functions.php";
$conn = db_connect();

if($_SESSION["role"] != 'A') return "";

$query = "SELECT challenge_name FROM CTF_challenge";
$result = $conn->query($query);
$rows = $result->fetch_all();

echo json_encode($rows);
?>