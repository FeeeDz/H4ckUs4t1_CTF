<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>

<style>
body {
  background-image: url('img/pexels-photo-904690.jpg');
}
</style>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    </div>
    <?php require "inc/footer.php"; ?>
</body>
</html>