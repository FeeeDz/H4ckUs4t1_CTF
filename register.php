<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

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
    <div id="main">
        <form method="POST">
            <input type="text" name="username" placeholder="Username" minlength="3" maxlength="16" pattern="[\x00-\x7F]+" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" minlength="8" maxlength="128" required>
            <input type="password" id="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
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