<?php 
require "inc/init.php";

if (!isset($_SESSION["user_id"])) exit(header("Location: login.php?redirect=challenges.php"));

$user_id = isset($_GET["username"]) ? get_user_id_from_username($conn, $_GET["username"]) : (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL);
if (!check_if_user_exists($conn, $user_id)) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
$username = get_username_from_id($conn, $user_id);

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
                <h2 class="title">User</h2><br>
                <h3 style="display: inline;">Username: </h3><h4 style="display: inline;"><?php echo $username; ?><h4>
                <h3 style="display: inline;">Score: </h3><h4 style="display: inline;"><?php echo get_user_score($conn, $user_id); ?><h4>
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