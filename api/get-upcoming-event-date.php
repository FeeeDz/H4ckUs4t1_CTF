<?php
session_start();
require "../inc/functions.php";
$conn = db_connect();

echo json_encode(get_upcoming_event_start_date($conn));
?>