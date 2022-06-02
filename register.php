<?php 
require "inc/init.php";

if(!isset($_SESSION["user_id"]) && isset($_POST["submit"])) {
    $error = register_user($conn, $_POST["username"], $_POST["email"], $_POST["password"]);
    $registered = false;
    switch($error) {
        case -1:
            $form_error = "Username already used";
            break;

        case -2:
            $form_error = "Email already used";
            break;

        case -8:
            $form_error = "Invalid email";
            break;

        case 1:
            $registered = true;
            // exit(header("Location: " . basename($_SERVER['PHP_SELF'])."?"));
            // login($conn, $_POST["email"], $_POST["password"]);
            break;
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
    <?php if($registered): ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Account successfully registered!</h2>
            <h3>Check your email to verify your account and be able to log in</h3>
        </form>
    <?php else: ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Register</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
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
    <?php endif; ?>
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