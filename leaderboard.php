<?php 
require_once "inc/init.php";

$title = "Leaderboard - H4ckUs4t1 CTF";
require "inc/head.php";

if (isset($_GET["type"])) $leaderboard_type = $_GET["type"];
else $leaderboard_type = is_event_started($conn) ? "official" : "training";

if ($leaderboard_type != "training" && $leaderboard_type != "official") exit(header("Location: ".basename($_SERVER["PHP_SELF"])));

$events = get_events($conn);
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="leaderboard">
        <div class="leaderboard__buttons">
            <a id="training_type_button" onclick="leaderboard_type='training'; refresh_leaderboard(true);" class="<?php if ($leaderboard_type == "training") echo "selected"; ?>">Training</a>
            <a id="official_type_button" onclick="leaderboard_type='official'; refresh_leaderboard(true);" class="<?php if ($leaderboard_type == "official") echo "selected"; ?>">Official</a>
        </div>
        <div id="choose_event">
            <label>Choose event</label>
            <select name="challenge_name" onchange="refresh_leaderboard(true)">
                <?php
                foreach ($events as $key => $event) : ?>
                    <option value="<?php echo $event["event_id"]; ?>"><?php echo $event["event_name"]; ?></option>";
                <?php endforeach; ?>
            </select>
        </div>
        <table cellspacing="0" cellpadding="0">
        </table>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        var leaderboard_type = "<?php echo $leaderboard_type; ?>";
        var select = document.querySelector("select");

        function refresh_leaderboard(force_update = false) {
            if (force_update) {
                if (leaderboard_type == "training") {
                    document.querySelector("#official_type_button").classList.remove("selected");
                    document.querySelector("#training_type_button").classList.add("selected");
                    document.querySelector("#choose_event").classList.add("hidden");
                    select.selectedIndex = 0;

                    history.replaceState(null, '', "?type=" + encodeURIComponent(leaderboard_type));    
                } else if (leaderboard_type == "official") {
                    document.querySelector("#training_type_button").classList.remove("selected");
                    document.querySelector("#official_type_button").classList.add("selected");
                    document.querySelector("#choose_event").classList.remove("hidden");
                    
                    history.replaceState(null, '', "?type=" + encodeURIComponent(leaderboard_type) + "&event_id=" + encodeURIComponent(select.value));    
                }
            }
            var refresh_url;
            if (leaderboard_type == "training") refresh_url = web_server_url + "/api/get-leaderboard-html.php?type=" + encodeURIComponent(leaderboard_type)
            else if (leaderboard_type == "official") refresh_url = web_server_url + "/api/get-leaderboard-html.php?type=" + encodeURIComponent(leaderboard_type) + "&event_id=" + encodeURIComponent(select.value)
            fetch(refresh_url)
            .then(response => response.text())
            .then((response) => {
                    document.querySelector('table').innerHTML = response;
                });
        }

        setInterval(function() { 
            refresh_leaderboard();
        } , 5000);

        refresh_leaderboard(true);

    </script>
</body>
</html>