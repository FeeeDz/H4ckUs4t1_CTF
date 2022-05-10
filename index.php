<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body class="index-body">
    <nav id="nav" class="no-bg-color">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="no-bg-color">
        <img id="main__logo" src="img/logo_h4ckus4t1_white.png" width="300">
        <div id="countdownTimer">
            <p id="time"></p>
        </div>
    </div>
    <div id="footer" class="no-bg-color">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>
        const timeElem = document.getElementById("time");
        const ctfDate = new Date("2022-06-03");
        function updateTime() {
            timeElem.innerHTML = formatTime((ctfDate.getTime() - new Date().getTime()) / 1000);
        }
        function formatTime(s) {
            s = Number(s);
            var d = Math.floor(s / (60 * 60 * 24));
            var h = Math.floor(s % (3600 * 24) / 3600);
            var m = Math.floor(s % 3600 / 60);
            var s = Math.floor(s % 60);

            var dDisplay = d > 0 ? String(d).padStart(2, '0') + "<label>DAYS</label> : " : "";
            var hDisplay = h >= 0 ? String(h).padStart(2, '0') + "<label>HOURS</label> : " : "";
            var mDisplay = m >= 0 ? String(m).padStart(2, '0') + "<label>MINUTES</label> : " : "";
            var sDisplay = s >= 0 ? String(s).padStart(2, '0') + "<label>SECONDS</label>" : "";
            return dDisplay + hDisplay + mDisplay + sDisplay;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>