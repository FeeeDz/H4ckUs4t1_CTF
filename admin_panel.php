<?php 
session_start();

if(!isset($_SESSION["role"]) || $_SESSION["role"] != 'A') {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
        <a class="add-challenge-button" href="logout.php">
            <span class="material-icons">add</span>
            <span>Aggiungi challenge</span>
        </a>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>