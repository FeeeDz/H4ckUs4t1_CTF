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

    // db_register_team($conn, "adsfafa");
    ?>
    <?php if (!isset($_SESSION["logged"])) { ?>
        <a href="   login.php">fai il login</a>
    <?php } elseif (!isset($_GET["action"])) { ?>
        <form method="GET">
            <input type="submit" value="Join team">
            <input type="hidden" name="action" value="join">
        </form>
        <form method="GET">
            <input type="submit" value="Crea team">
            <input type="hidden" name="action" value="create">
        </form>
    <?php } elseif ($_GET["action"] == "join") { ?>
        join        
    <?php } elseif ($_GET["action"] == "create") {
        if (isset($_POST["team_name"])) { 
            $token = db_register_team($conn, $_POST["team_name"]);

            if (!$token) echo "Nome team già utilizzato!";
            else echo $token;
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