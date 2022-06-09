<?php
require_once __DIR__ . "/../inc/init.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["challenge_name"]) || !isset($_GET["flag"])) exit(json_encode(false));
if (!($challenge_id = get_challenge_id($conn, $_GET["challenge_name"]))) exit(json_encode(false));
if (!submit_flag($conn, $challenge_id, $_SESSION["user_id"], $_GET["flag"])) exit(json_encode(false));

exit(json_encode(true));
?>