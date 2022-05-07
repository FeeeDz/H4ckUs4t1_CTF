<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

if(!isset($_SESSION["user_id"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    login($conn, $_POST["email"], $_POST["password"]);
}

redirect_if_logged();

$title = "Login CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required> 
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
    <?php require "inc/footer.php"; ?>
</body>
</html>