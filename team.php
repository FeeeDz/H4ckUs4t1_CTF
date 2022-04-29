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

    db_register_team($conn, "adsfafa");

    ?>

        <button>Join team</button>
        <button>Crea team</button>
        
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>