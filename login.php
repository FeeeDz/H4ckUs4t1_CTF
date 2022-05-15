<?php 
require "inc/init.php";

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
        <form method="POST" class="login__form">
            <h2>Login</h2>
            <div class="login__form__box">
                <input type="email" name="email" placeholder=" " required> 
                <label>Email</label>
            </div>
            <div class="login__form__box">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
            </div>
            <input type="submit" value="Login" class="login__form__submit">
        </form>
    </div>
    <footer id="footer">
        <?php require "inc/footer.php"; ?>
    </footer>  
</body>
</html>