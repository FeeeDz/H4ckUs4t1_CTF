<?php
session_start();
require "../inc/functions.php";
$conn = db_connect();

if($_SESSION["role"] != 'A') return false;

$query = "SELECT challenge_name FROM CTF_challenge";
$result = $conn->query($query);
$rows = $result->fetch_all();

echo json_encode($rows);
?>