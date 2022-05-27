<!-- <h3>Sito creato dagli studenti del Montani</h3> -->
<!-- <h3>Ciccola Alessio</h3> -->
<!-- <h3>Iena Alessandro</h3> -->
<!-- <h3>Vagnarelli Beniamino</h3> -->
<?php if (basename($_SERVER['PHP_SELF']) == "rules.php" || basename($_SERVER['PHP_SELF']) == "leaderboard.php" || basename($_SERVER['PHP_SELF']) == "challenges.php") : ?>
<div id="countdown-timer">
    <?php if (!is_event_started($conn)) : ?>
        <label>Starts in<label>
    <?php else : ?>
        <label>Ends in<label>
    <?php endif; ?>
    <p id="time"></p>
    <script>

        let date = <?php
            if (!is_event_started($conn)) echo json_encode(get_upcoming_event_start_date($conn));
            else echo json_encode(get_current_event_end_date($conn)); ?>;

        const timeElem = document.getElementById("time");
        const ctfDate = new Date(date);

        if(date) {
            updateTime();
            setInterval(updateTime, 1000);
        } else {
            document.getElementById("countdown-timer").remove()
        }

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
        
    </script>
</div>
<?php endif; ?>
<h3>H4ckUs4t1 Team © ITT Montani</h3>