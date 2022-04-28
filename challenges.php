<?php 
session_start();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php //require "inc/navbar.php"; ?>
    <div id="main">
    <?php
    require "inc/functions.php";
    $conn = db_connect();

    ?>
        <span>Miscellaneous</span>
        <div class="challenges__grid">
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
        </div>
        <span>Web</span>
        <div class="challenges__grid">
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
        </div>
        <span>Pwn</span>
        <div class="challenges__grid">
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
            <div class="challenge__box">Web</div>
        </div>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>