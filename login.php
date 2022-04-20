<?php 
session_start();

$title = "Login CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    <?php
    require "inc/functions.php";

    $conn = db_connect();

    if (db_login($conn, "imBenjamin741", "***REMOVED***")) {
        echo $_SESSION["role"];
    }
    
    if(isset($_SESSION["logged"])) {
        $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
        
        header("Location: $redirect");
    }

    ?>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>