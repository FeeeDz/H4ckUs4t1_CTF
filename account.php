<?php 
require "inc/init.php";

if (!isset($_SESSION["user_id"])) exit(header("Location: login.php?redirect=challenges.php"));

$user_id = isset($_GET["username"]) ? get_user_id_from_username($conn, $_GET["username"]) : (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : NULL);
if (!check_if_user_exists($conn, $user_id)) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));

$title = "Account - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main">
        <?php 
        if ($user_id == $_SESSION["user_id"]) {
            
        } else {

        }
        ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>