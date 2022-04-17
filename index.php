<?php 
session_start();
$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">

    <?php
    require "inc/functions.php";
    $conn = db_connect();
    // if (db_login($conn, "imBenjamin741", "Beniamino2003")) {
    //     echo $_SESSION["role"];
    // }

    // if (db_register($conn, "imBenjamin741bi", "Beniamino2003", "emails")) {
    //     echo "adsf";
    // }

    ?>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>