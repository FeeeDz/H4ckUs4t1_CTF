<?php 
require "inc/init.php";

$user_id = isset($_GET["username"]) ? get_user_id_from_username($conn, $_GET["username"]) : (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL);
$username = get_username_from_id($conn, $user_id);
$team_name = get_user_team_name($conn, $user_id);

if (!$user_id && !isset($_SESSION["user_id"])) exit(header("Location: login.php"));
if (!check_if_user_exists($conn, $user_id)) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));

$title = "Account - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="user">
    <?php if (!isset($_GET["action"])) {
        if ($user_id) { ?>    
            <form method="GET" class="generic-form">
                <h2 class="title">User</h2>
                <div class="info-box">
                    <h3>Username: </h3>
                    <h4><?php echo $username; ?></h4>
                </div>
                <?php if ($team_name): ?>
                    <div class="info-box">
                        <h3>Team: </h3>
                        <a class="user-team-link" href="team.php?team_name=<?php echo $team_name; ?>"><?php echo $team_name; ?></a>
                    </div>
                <?php endif; ?>
                <div class="info-box">
                    <h3>Score: </h3>
                    <h4><?php echo get_user_score($conn, $user_id); ?></h4>
                </div>
                <?php if (get_num_user_solves($conn, $user_id) != 0) : ?> 
                    <div class="solves-container">
                        <h3 style="display: inline-block;">Solves: </h3><br>
                        <?php foreach (get_user_solves($conn, $user_id) as $solve) : ?>
                            <div class="solve">
                                <div class="challenge-name"><?php echo $solve["challenge_name"]; ?></div>
                                <div class="points"><?php echo $solve["points"]; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?> 
            </form>
        <?php }
    } else exit(header("Location: ".basename($_SERVER['PHP_SELF']))); ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>