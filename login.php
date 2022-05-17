<?php 
require "inc/init.php";

if(!isset($_SESSION["user_id"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    login($conn, $_POST["email"], $_POST["password"]);
}

if(isset($_SESSION["user_id"])) {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

$title = "Login CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="login">
        <form method="POST" class="generic-form">
            <h2 class="title">Login</h2>
            <div class="generic-form__box">
                <input type="email" name="email" placeholder=" " autocomplete="email" required> 
                <label>Email</label>
            </div>
            <div class="generic-form__box">
                <input type="password" name="password" placeholder=" " autocomplete="current-password" required>
                <label>Password</label>
            </div>
            <input type="submit" value="Login" class="generic-form__submit">
        </form>
    </div>
    <footer id="footer">
        <?php require "inc/footer.php"; ?>
    </footer>  
</body>
</html>