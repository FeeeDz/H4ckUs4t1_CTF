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
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="login">
        <form method="POST" class="login-box">
            <h2>Login</h2>
            <div class="user-box">
                <input type="email" name="email" required> 
                <label>Email</label>
            </div>
            <div class="user-box">
                <input type="password" name="password" required>
                <label>Password</label>
            </div>
            <input type="submit" value="Login" class="submit">
        </form>
    </div>
    <footer id="footer">
        <?php require "inc/footer.php"; ?>
    </footer>  
</body>
</html>