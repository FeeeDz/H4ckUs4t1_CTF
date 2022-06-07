<?php 
require_once __DIR__ . "/../inc/init.php";

if (isset($_GET["type"])) $leaderboard_type = $_GET["type"];
else $leaderboard_type = is_event_started($conn) ? "official" : "training";

if ($leaderboard_type != "training" && $leaderboard_type != "official") exit(json_encode(false));
echo json_encode($leaderboard_type == "training" ? get_training_leaderboard($conn) : get_official_leaderboard($conn));
?>