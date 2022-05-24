<?php 
require "inc/init.php";

$title = "Leaderboard";
require "inc/head.php";

if (isset($_GET["type"])) $leaderboard_type = $_GET["type"];
else $leaderboard_type = is_event_started($conn) ? "official" : "training";

if ($leaderboard_type != "training" && $leaderboard_type != "official") exit(header("Location: ".basename($_SERVER["PHP_SELF"])));

?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="leaderboard">
        <div class="leaderboard__buttons">
            <a href="leaderboard.php?type=training" class="<?php if ($leaderboard_type == "training") echo "selected"; ?>">Training</a>
            <a href="leaderboard.php?type=official" class="<?php if ($leaderboard_type == "official") echo "selected"; ?>">Official</a>
        </div>
        <table>
            <tr>
                <th>Position</th>
                <th><?php if ($leaderboard_type == "training") echo "Username"; else echo "Team Name"; ?></th>
                <th>Score</th>
            </tr>
            <?php $leaderboard_data = $leaderboard_type == "training" ? get_training_leaderboard($conn) : get_official_leaderboard($conn);   
                foreach ($leaderboard_data as $index => $row): ?>
                <tr>
                    <td><?php echo $index+1; ?></td>
                    <td><?php if ($leaderboard_type == "training") echo $row["username"]; else echo $row["team_name"]; ?></td>
                    <td><?php echo $row["score"]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>