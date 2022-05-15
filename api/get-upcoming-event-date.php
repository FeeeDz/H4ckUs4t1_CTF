<?php
require "../inc/init.php";

echo json_encode(get_upcoming_event_start_date($conn));
?>