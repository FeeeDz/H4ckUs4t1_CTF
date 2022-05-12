<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main">
        <div class="challenges__container">
            <span class="challenges__category">Miscellaneous</span>
            <div class="challenges__grid">
                <div class="challenge__box">
                    <span>a</span>
                    <span>a</span>
                    <span>a</span>
                    <span>a</span>
                    <span>a</span>
                </div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
            </div>
            <span class="challenges__category">Web</span>
            <div class="challenges__grid">
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
            </div>
            <span class="challenges__category">Pwn</span>
            <div class="challenges__grid">
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
            </div>
            <span class="challenges__category">Pwn</span>
            <div class="challenges__grid">
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
                <div class="challenge__box">Web</div>
            </div>
        </div>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>