<?php 
require "inc/init.php";

if(!isset($_SESSION["user_id"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    if(register_user($conn, $_POST["username"], $_POST["email"], $_POST["password"])) {
        login($conn, $_POST["email"], $_POST["password"]);
    }
}

redirect_if_logged();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="register-user">
        <form method="POST" class="generic-form">
            <h2 class="title">Register</h2>
            <div class="generic-form__box">
                <input type="text" name="username" placeholder=" " minlength="3" maxlength="16" pattern="[\x00-\x7F]+" required>
                <label>Username</label>
            </div>
            <div class="generic-form__box">
                <input type="email" name="email" placeholder=" " required>
                <label>Email</label>
            </div>
            <div class="generic-form__box">
                <input type="password" id="password" name="password" placeholder=" " minlength="8" maxlength="128" required>
                <label>Password</label>
            </div>
            <div class="generic-form__box">
                <input type="password" id="confirm_password" placeholder=" " required>
                <label>Confirm Password</label>
            </div>
            <input type="submit" value="Register" class="generic-form__submit">
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