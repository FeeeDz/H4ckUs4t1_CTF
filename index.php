<?php 
$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">

    <?php

    require "inc/functions.php";
    $conn = db_connect();
    if (db_login($conn, "imBenjamin741", "Beniamino2003")) {
        echo $_SESSION["role"];
    }

    if (db_register($conn, "imBenjamin741bis", "Beniamino2003", "email")) {
        echo "adsf";
    }

    // echo bin2hex(random_bytes(16));

    // echo password_hash("Beniamino2003", PASSWORD_DEFAULT, $hash_options);

    ?>
    </div>
    <?php require "inc/footer.php"; ?>

    <script src="js/script.js"></script> 
</body>
</html>