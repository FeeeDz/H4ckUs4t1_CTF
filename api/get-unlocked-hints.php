<?php
require_once __DIR__ . "/../inc/init.php";

if (!isset($_SESSION["user_id"])) return false;

$from_date = isset($_GET["from_date"]) ? $_GET["from_date"] : (new DateTime())->setTimestamp(0)->format('Y-m-d H:i:s');
echo json_encode(get_unlocked_hints($conn, $_SESSION["user_id"], $from_date));
?>