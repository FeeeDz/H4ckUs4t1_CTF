<?php
require "../inc/init.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["challenge_name"]) || !isset($_GET["flag"])) return false;
if (!($challenge_id = get_challenge_id($conn, $_GET["challenge_name"]))) return false;

if (submit_flag($conn, $challenge_id, $_SESSION["user_id"], $_GET["flag"])) echo json_encode(true);
?>