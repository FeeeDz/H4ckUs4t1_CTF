<?php 
require "inc/init.php";

if(!isset($_SESSION["user_id"]) && isset($_POST["submit"])) {
    if(register_user($conn, $_POST["username"], $_POST["email"], $_POST["password"])) {
        login($conn, $_POST["email"], $_POST["password"]);
    }
}

if(isset($_SESSION["user_id"])) {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    exit(header("Location: $redirect"));
}

$title = "Register - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="register-user">
        <form method="POST" class="generic-form">
            <h2 class="title">Register</h2>
            <div class="generic-form__input-box">
                <input type="text" name="username" placeholder=" " minlength="3" maxlength="16" pattern="^(\d|\w)+$" autocomplete="username" title="string with 16 non-special characters and no spaces" required>
                <label>Username</label>
            </div>
            <div class="generic-form__input-box">
                <input type="email" name="email" placeholder=" " autocomplete="email" required>
                <label>Email</label>
            </div>
            <div class="generic-form__input-box">
                <input type="password" id="password" name="password" placeholder=" " minlength="8" maxlength="128" autocomplete="new-password" required>
                <label>Password</label>
            </div>
            <div class="generic-form__input-box">
                <input type="password" id="confirm_password" placeholder=" " required>
                <label>Confirm Password</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button">Register</button>
        </form>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        const password = document.getElementById("password")
        const confirm_password = document.getElementById("confirm_password");

        function validatePassword(){
            if(password.value != confirm_password.value) 
                confirm_password.setCustomValidity("Passwords Don't Match");
            else
                confirm_password.setCustomValidity('');
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;

    </script>
</body>
</html>