<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

$title = "CTF h4ckus4t1 Team";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main">
    <?php
    $team_name = get_user_team_name($conn, $_SESSION['user_id']);
    $token = get_team_token($conn, $team_name);

    if (!isset($_SESSION["user_id"])) { 
        header("Location: index.php");
    } elseif ($team_name) { 
        if(isset($_GET["action"])) {
            if($_GET["action"] == "quit") quit_team($conn, $_SESSION['user_id']);
            header("Location: ".basename($_SERVER['PHP_SELF']));
        }

        echo $team_name."<br>";
        echo $token;
    ?>    
        <form method="GET">
            <input type="submit" value="Leave the team">
            <input type="hidden" name="action" value="quit">
        </form>
    <?php } elseif (!isset($_GET["action"])) { ?>
        <form method="GET">
            <input type="submit" value="Join team">
            <input type="hidden" name="action" value="join">
        </form>
        <form method="GET">
            <input type="submit" value="Create team">
            <input type="hidden" name="action" value="create">
        </form>
    <?php } elseif ($_GET["action"] == "join") {
        if (isset($_POST["token"])) { 
            if (join_team($conn, $_SESSION['user_id'], $_POST["token"])) header("Location: ".basename($_SERVER['PHP_SELF']));
            echo "Invalid token";
        } ?>
        <form method="POST">
            <input type="text" name="token" minlength="3" required>
            <input type="submit" value="Join team">
        </form>       
    <?php } elseif ($_GET["action"] == "create") {
        if (isset($_POST["team_name"])) { 
            $token = register_team($conn, $_POST["team_name"]);

            if ($token) {
                join_team($conn, $_SESSION['user_id'], $token);
                header("Location: ".basename($_SERVER['PHP_SELF']));  
            }
            echo "Team name already used!";
        } ?>
        <form method="POST">
            <input type="text" name="team_name" minlength="3" maxlength="32" pattern="[\x00-\x7F]+" required>
            <input type="submit" value="Create team">
        </form>
    <?php } ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>