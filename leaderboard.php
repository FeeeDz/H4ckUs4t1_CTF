<?php 
require_once "inc/init.php";

$title = "Leaderboard - H4ckUs4t1 CTF";
require "inc/head.php";

if (isset($_GET["type"])) $leaderboard_type = $_GET["type"];
else $leaderboard_type = is_event_started($conn) ? "official" : "training";

if ($leaderboard_type != "training" && $leaderboard_type != "official") exit(header("Location: ".basename($_SERVER["PHP_SELF"])));

?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="leaderboard">
        <div class="leaderboard__buttons">
            <a href="leaderboard.php?type=training" class="<?php if ($leaderboard_type == "training") echo "selected"; ?>">Training</a>
            <a href="leaderboard.php?type=official" class="<?php if ($leaderboard_type == "official") echo "selected"; ?>">Official</a>
        </div>
        <table>
            <?php require "api/get-leaderboard-html.php" ?>
        </table>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        var web_server_url = window.location.origin + "<?php echo $site_directory; ?>";

        function refresh_leaderboard() {
            fetch(web_server_url + "/api/get-leaderboard-html.php")
            .then(response => response.text())
            .then((response) => {
                    document.querySelector('table').innerHTML = response;
                });
        }

        setInterval(function() { 
            refresh_leaderboard();
        } , 5000);

    </script>
</body>
</html>