<?php
require "../inc/init.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["hint_id"])) return false;

if (unlock_hint($conn, $_GET["hint_id"], $_SESSION["user_id"])) echo json_encode(get_hint_description($conn, $_GET["hint_id"]));
?>