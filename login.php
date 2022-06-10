<?php 
require_once "inc/init.php";

if(!isset($_SESSION["user_id"]) && isset($_POST["submit"])) {
    $error = login($conn, $_POST["username_email"], $_POST["password"]);
    switch($error) {
        case -1:
            $form_error = "Wrong credentials";
            break;

        case -2:
            $form_error = "You account is not active";
            break;
    }
}

if(isset($_SESSION["user_id"])) {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

$title = "Login - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="login">
        <form method="POST" class="generic-form">
            <h2 class="title">Login</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div style="margin-bottom: 30px;">Don't have an account? <a href="register.php" class="link">Register</a></div>
            <div class="generic-form__input-box" style="margin-top: 20px;">
                <input type="text" name="username_email" placeholder=" " required> 
                <label>Username or Email</label>
            </div>
            <div class="generic-form__input-box">
                <input type="password" name="password" placeholder=" " autocomplete="current-password" required>
                <label>Password</label>
            </div>
            <div style="margin-bottom: 10px;"><a href="forgot-password.php" class="link">Forgot your password?</a></div>
            <button type="submit" name="submit" class="generic-form__button">Login</button>
        </form>
    </div>
    <footer id="footer">
        <?php require "inc/footer.php"; ?>
    </footer>  
</body>
</html>