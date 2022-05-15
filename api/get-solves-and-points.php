<?php
require "../inc/init.php";

if (!isset($_SESSION["user_id"])) return false;

echo json_encode(get_challenges_solves_and_points($conn, $_SESSION["user_id"]));
?>