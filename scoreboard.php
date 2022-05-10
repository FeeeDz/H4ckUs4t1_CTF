<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main">
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>