<?php
require_once "inc/init.php";

// if (activate_user($conn, $_GET["activation_code"])) exit(header("refresh:5; url=index.php"));
// else exit(header("refresh:5;url=index.php"));

$username = activate_user($conn, $_GET["activation_code"]);

$title = "Account - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="activation-page">
        <?php if ($username): ?>
        <h3>Account <?php echo $username; ?> activated!</h3>
        <?php else: ?>
        <h3>Invalid activation code!</h3>
        <?php endif; ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>

?>