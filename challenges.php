<?php 
require_once "inc/init.php";

if (!isset($_SESSION["user_id"])) exit(header("Location: login.php?redirect=challenges.php"));
else if (is_event_started($conn) && !get_user_team_id($conn, $_SESSION["user_id"])) exit(header("Location: team.php?redirect=challenges.php"));

if (is_event_started($conn)) $challenge_type = "O";
else $challenge_type = "T";

$title = "Challenges - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="challenges">
        <?php foreach (get_challenge_categories($conn) as $category) : ?>
            <span class="challenges__category"><?php echo $category?></span>
            <div class="challenges__grid">
                <?php foreach (get_challenges_from_category($conn, $category, $challenge_type) as $challenge_id) {
                    $challenge_data = get_challenge_data($conn, $challenge_id);
                ?>
                <div class="challenge closed <?php if (is_challenge_solved($conn, $challenge_id, $_SESSION["user_id"])) echo "solved"; ?>" onclick="open_challenge(event, this)">
                    <div class="flexbox-info">
                        <div class="solves"><?php echo get_challenge_solves($conn, $challenge_id) ?><span class="material-icons">flag</span></div>
                        <div class="points"><?php echo compute_challenge_points($conn, $challenge_id); ?><span class="material-icons">military_tech</span></div>
                    </div>
                    <div class="challenge-name"><?php echo $challenge_data["challenge_name"]; ?></div>
                    <div class="description"><?php echo nl2br($challenge_data["description"]); ?></div>
                    <div class="service"><?php echo $challenge_data["service"]; ?></div>
                    <?php foreach (get_db_challenge_resources($conn, $challenge_id) as $resource) { ?>
                        <a class="resource" href="<?php echo "api/get-challenge-file.php?challenge_name=".$challenge_data["challenge_name"]."&filename=".$resource["filename"]; ?>"><?php echo $resource["filename"]; ?><span class="material-icons">file_download</span></a>
                    <?php } ?>
                    <div class="flag">
                        <div class="flag__input-box">
                            <input class="flag__input" type="text" placeholder=" " onkeypress="if (event.keyCode == 13) submit_flag(event, this.parentElement.parentElement)">
                            <label>Flag ITT{...}</span>
                        </div>
                        <span class="flag__submit material-icons" onclick="submit_flag(event, this.parentElement)">done</span>
                    </div>
                    <?php foreach (get_hints($conn, $challenge_id) as $index=>$hint) { ?>
                        <div class="hint <?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo "unlocked"; else echo "locked"; ?>" onclick="toggle_popup(event, this)">
                            <label class="hint__id-label">Hint <?php echo $index+1; ?></label>
                            <div class="hint__id"><?php echo $hint["hint_id"]; ?></div>
                            <div class="hint__cost">Cost <?php echo $hint["cost"]; ?></div>
                            <div class="blurred-background hidden" onclick="toggle_popup(event, this.parentElement)"></div>
                            <div class="hint__popup popup hidden" onclick="event.stopPropagation()">
                                <div class="hint__description"><?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo $hint["description"]; ?></div>
                                <button class="hint__unlock-button" onclick="unlock_hint(event, this.parentElement.parentElement)">unlock hint</button>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($challenge_data["author"])): ?>
                    <div class="author">Author: <?php echo $challenge_data["author"]; ?></div>
                    <?php endif; ?>
                    <span class="close-button material-icons" onclick="close_challenge(event, this.parentElement)">expand_less</span>
                </div>
                <?php } ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        var refresh_hints_from_date = new Date().toLocaleString("zh-CN");
        // var web_server_url = window.location.origin + "/~quintaa2122/informatica/CTF_h4ckus4t1";
        var web_server_url = window.location.origin + "<?php echo $site_directory; ?>";
        var opened_challenge = null;

        function open_challenge(event, elem) {
            if (opened_challenge) close_challenge(event, opened_challenge);
            elem.classList.replace("closed", "open");
            opened_challenge = elem;
        }

        function close_challenge(event, elem){
            event.stopPropagation();
            elem.classList.replace("open", "closed");
        }

        function toggle_popup(event, parent_elem) {
            event.stopPropagation();
            parent_elem.querySelector(".blurred-background").classList.toggle("hidden");
            parent_elem.querySelector(".popup").classList.toggle("hidden");
        }

        function unlock_hint(event, hint_elem){
            var desc_elem = hint_elem.querySelector(".hint__description");
            var id = hint_elem.querySelector(".hint__id").innerHTML;
            fetch(web_server_url + "/api/unlock-hint.php?hint_id=" + id)
                .then(() => { refresh_unlocked_hints(); })
                .then(() => { refresh_hints_from_date = new Date().toLocaleString("zh-CN"); })
        }

        function submit_flag(event, flag_elem) {
            flag_input = flag_elem.querySelector('.flag__input');
            flag = flag_input.value
            flag_input.value = "";
            challenge_name = flag_elem.parentElement.querySelector('.challenge-name').innerHTML;
            challenge_elem = flag_elem.parentElement;

            fetch(web_server_url + "/api/submit-flag.php?challenge_name=" + challenge_name + "&flag=" + flag)
                .then(() => { refresh_solves_and_points(); })
        }

        function refresh_solves_and_points() {
            fetch(web_server_url + "/api/get-solves-and-points.php")
                .then(response => response.json())
                .then(data => {
                    if (!data) return;
                    document.querySelectorAll(".challenge").forEach(challenge_elem => {
                        var chall_name = challenge_elem.querySelector(".challenge-name").innerHTML;
                        var solves_elem = challenge_elem.querySelector(".solves");
                        var points_elem = challenge_elem.querySelector(".points");

                        var chall = data.find(chall => chall.challenge_name === chall_name);

                        if (chall) {
                            solves_elem.innerHTML = chall.solves + solves_elem.innerHTML.substring(solves_elem.innerHTML.indexOf("<"));
                            points_elem.innerHTML = chall.points + points_elem.innerHTML.substring(points_elem.innerHTML.indexOf("<"));
                            if (chall.solved == "1") challenge_elem.classList.add("solved");
                            else if (chall.solved == "0") challenge_elem.classList.remove("solved");
                        }
                    });
                });
        }

        function refresh_unlocked_hints() {
            fetch(web_server_url + "/api/get-unlocked-hints.php?from_date=" + encodeURIComponent(refresh_hints_from_date))
                .then(response => response.json())
                .then(data => {
                    if (!data) return;
                    document.querySelectorAll(".hint").forEach(hint_elem => {
                        var hint_id = hint_elem.querySelector(".hint__id").innerHTML;
                        var hint__description = hint_elem.querySelector(".hint__description");

                        
                        var hint = data.find(hint => hint.hint_id === hint_id);
                        
                        if (hint) {
                            hint__description.innerHTML = hint.description;
                            hint_elem.classList.replace("locked", "unlocked");
                        }
                    });
                });
        }

        setInterval(function() { 
            refresh_solves_and_points();
            refresh_unlocked_hints();
            refresh_hints_from_date = new Date().toLocaleString("zh-CN");
        } , 5000);

    </script>
</body>
</html>