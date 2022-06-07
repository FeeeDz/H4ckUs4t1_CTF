<?php
require_once __DIR__ . "/../inc/init.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["hint_id"])) exit(json_encode(false));
if (!unlock_hint($conn, $_GET["hint_id"], $_SESSION["user_id"])) exit(json_encode(false));

exit(json_encode(get_hint_description($conn, $_GET["hint_id"])))
?>