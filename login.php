<?php 
session_start();


if(isset($_POST["email"]) && isset($_POST["password"])){
    require "inc/functions.php";

    $conn = db_connect();
    db_login($conn, $_POST["email"], $_POST["password"]);
}

if(isset($_SESSION["logged"])) {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

$title = "Login CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    <?php

    

    
    
    ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>

    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>