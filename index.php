<?php 
require_once "inc/init.php";

$title = "H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body class="index-body">
    <nav id="nav" class="no-bg-color">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="no-bg-color">
        <img id="main__logo" src="images/logo_h4ckus4t1_white.png" width="300">
        <?php require "inc/countdown-timer.php"; ?>
    </div>
    <div id="footer" class="no-bg-color">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>