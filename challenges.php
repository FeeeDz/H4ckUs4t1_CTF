<?php 
require "inc/init.php";

if (!isset($_SESSION["user_id"])) header("Location: login.php?redirect=challenges.php");
else if (is_event_started($conn) && !get_user_team_id($conn, $_SESSION["user_id"])) header("Location: team.php?redirect=challenges.php");

if (is_event_started($conn)) $challenge_type = "O";
else $challenge_type = "T";

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="challenges_main">
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
                    <div class="description"><?php echo $challenge_data["description"]; ?></div>
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

        web_server_url = window.location.origin + "/~quintaa2122/informatica/CTF_h4ckus4t1";

        opened_challenge = null;

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
            console.log(hint_elem)
            desc_elem = hint_elem.querySelector(".hint__description");
            id = hint_elem.querySelector(".hint__id").innerHTML;
            fetch(web_server_url + "/api/unlock-hint.php?hint_id=" + id)
                .then(response => response.json())
                .then(data => {
                    desc_elem.innerHTML = data;

                    if (desc_elem.innerHTML) {
                        hint_elem.classList.replace("locked", "unlocked");
                    }   
                });
        }

        function submit_flag(event, parent_elem) {
            flag_input = parent_elem.querySelector('.flag__input');
            flag = flag_input.value
            flag_input.value = "";
            challenge_name = parent_elem.parentElement.querySelector('.challenge-name').innerHTML;
            challenge_elem = parent_elem.parentElement;

            fetch(web_server_url + "/api/submit-flag.php?challenge_name=" + challenge_name + "&flag=" + flag)
                .then(response => response.json())
                .then(data => {
                    if (data == true) {
                        challenge_elem.classList.add("solved");
                        refresh_solves_and_points();
                    }
                });
        }

        function refresh_solves_and_points() {
            fetch(web_server_url + "/api/get-solves-and-points.php")
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll(".challenge").forEach(challenge_elem => {
                        chall_name = challenge_elem.querySelector(".challenge-name").innerHTML;
                        solves_elem = challenge_elem.querySelector(".solves");
                        points_elem = challenge_elem.querySelector(".points");

                        let chall = data.find(chall => chall.challenge_name === chall_name);

                        solves_elem.innerHTML = chall.solves + solves_elem.innerHTML.substring(solves_elem.innerHTML.indexOf("<"));
                        points_elem.innerHTML = chall.points + points_elem.innerHTML.substring(points_elem.innerHTML.indexOf("<"));
                        if (chall.solved == "1") challenge_elem.classList.add("solved");
                    });
                });
        }

        setInterval(refresh_solves_and_points, 1000);

    </script>
</body>
</html>