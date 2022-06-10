<?php 
require_once "inc/init.php";

if(isset($_SESSION["user_id"])) {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

if(isset($_POST["submit_email"])) {
    $error = send_reset_password_email($conn, $_POST["email"]);
    $email_sent = false;
    switch($error) {
        case -1:
            $form_error = "No account associated with the email address";
            break;

        case -2:
            $form_error = "Error while sending email";
            break;

        case 1:
            $email_sent = true;
            break;
    }
}

if (isset($_GET["reset_password_code"])) {
    $valid_code = true;
    $user_id = get_user_id_from_reset_password_code($conn, $_GET["reset_password_code"]);
    if (!$user_id) $valid_code = false;
    else if (isset($_POST["submit_new_password"])) {
        $error = reset_password($conn, $_GET["reset_password_code"], $_POST["new_password"]);
        $password_reset = false;
        switch($error) {
            case 1:
                $password_reset = true;
                break;
        }
    }
}

$title = "Login - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="login">
    <?php if($email_sent): ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Email sent successfully!</h2>
            <h3>You will receive a link to create a new password via email</h3>
        </form>
    <?php elseif($password_reset): ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Password reset successfully!</h2>
            <h3>You can now login <a href="login.php" class="link">here</a>!</h3>
        </form>
    <?php elseif($valid_code === false): ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Invalid reset password code</h2>
            <h3>You can send a new password reset request <a href="forgot-password.php" class="link">here</a>!</h3>
        </form>
    <?php elseif ($valid_code): ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Reset Password</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__input-box">
                <input type="password" id="password" name="new_password" placeholder=" " minlength="8" maxlength="128" autocomplete="new-password" required>
                <label>Password</label>
            </div>
            <div class="generic-form__input-box">
                <input type="password" id="confirm_password" placeholder=" " required>
                <label>Confirm Password</label>
            </div>
            <button type="submit" name="submit_new_password" class="generic-form__button">Reset</button>
        </form>
    <?php else: ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Reset Password</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__input-box">
                <input type="email" name="email" placeholder=" " autocomplete="email" required> 
                <label>Email</label>
            </div>
            <button type="submit" name="submit_email" class="generic-form__button no-margin">Get new password</button>
        </form>
    <?php endif; ?>
    </div>
    <footer id="footer">
        <?php require "inc/footer.php"; ?>
    </footer>  
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