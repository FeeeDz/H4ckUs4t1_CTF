<?php 
session_start();

$title = "CTF h4ckus4t1";
require "inc/head.php";

// require "inc/functions.php";
// $conn = db_connect();
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
        <div class="challenges__container">
            <span class="challenges__category">Miscellaneous</span>
            <div class="challenges__grid">
                <div class="challenge__box">Web</div>
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
        <form action="?" method="POST">
      <div class="g-recaptcha" data-sitekey="6Lcg97wfAAAAAHusVv8og846Xm4feb8Ur7fgGos-"></div>
      <br/>
      <input type="submit" value="Submit">
    </form>


    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script>
</body>
</html>