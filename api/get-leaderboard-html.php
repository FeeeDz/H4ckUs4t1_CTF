<?php 
require_once __DIR__ . "/../inc/init.php";

if (isset($_GET["type"])) $leaderboard_type = $_GET["type"];
else $leaderboard_type = is_event_started($conn) ? "official" : "training";

if (isset($_GET["event_id"])) $event_id = $_GET["event_id"];
else $event_id = NULL;

if ($leaderboard_type != "training" && $leaderboard_type != "official") exit(json_encode(false));
?>
<tr>
    <th>Position</th>
    <th><?php if ($leaderboard_type == "training") echo "Username"; else echo "Team Name"; ?></th>
    <th>Score</th>
</tr>
<?php $leaderboard_data = $leaderboard_type == "training" ? get_training_leaderboard($conn) : get_official_leaderboard($conn, $event_id);   
    foreach ($leaderboard_data as $index => $row): ?>
    <tr>
        <td><?php echo $index+1; ?></td>
        <td>
            <?php if ($leaderboard_type == "training"): ?>
                <a class="link" href="user.php?username=<?php echo $row["username"]; ?>"><?php echo $row["username"]; ?></a>
            <?php else: ?>
                <a class="link" href="team.php?team_name=<?php echo $row["team_name"]; ?>"><?php echo $row["team_name"]; ?></a>
            <?php endif; ?>
        <td><?php echo $row["score"]; ?></td>
    </tr>
<?php endforeach; ?>