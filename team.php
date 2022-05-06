<?php 
session_start();

$title = "CTF h4ckus4t1 Team";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    <?php
    require "inc/functions.php";
    $conn = db_connect();

    $team_name = get_user_team_name($conn);
    $token = get_team_token($conn, $team_name);

    if (!isset($_SESSION["logged"])) { 
        header("Location: index.php");
    } elseif ($team_name) { 
        if(isset($_GET["action"])) {
            if($_GET["action"] == "quit") quit_team($conn);
            header("Location: ".basename($_SERVER['PHP_SELF']));
        }

        echo $team_name."<br>";
        echo $token;
    ?>    
        <form method="GET">
            <input type="submit" value="Esci dal team">
            <input type="hidden" name="action" value="quit">
        </form>
    <?php } elseif (!isset($_GET["action"])) { ?>
        <form method="GET">
            <input type="submit" value="Join team">
            <input type="hidden" name="action" value="join">
        </form>
        <form method="GET">
            <input type="submit" value="Crea team">
            <input type="hidden" name="action" value="create">
        </form>
    <?php } elseif ($_GET["action"] == "join") {
        if (isset($_POST["token"])) { 
            if (join_team($conn, $_POST["token"])) header("Location: ".basename($_SERVER['PHP_SELF']));
            echo "token non valido";
        } ?>
        <form method="POST">
            <input type="text" name="token" minlength="3" required>
            <input type="submit" value="Join team">
        </form>       
    <?php } elseif ($_GET["action"] == "create") {
        if (isset($_POST["team_name"])) { 
            $token = register_team($conn, $_POST["team_name"]);

            if ($token) header("Location: ".basename($_SERVER['PHP_SELF']));
            echo "Nome team già utilizzato!";
        } ?>
        <form method="POST">
            <input type="text" name="team_name" minlength="3" maxlength="32" pattern="[\x00-\x7F]+" required>
            <input type="submit" value="Crea team">
        </form>
    <?php } ?>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>