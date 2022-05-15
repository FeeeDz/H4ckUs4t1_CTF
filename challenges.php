<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

// if (!isset($_SESSION["user_id"])) header("Location: login.php");

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
                <div class="challenge__box closed" onclick="open_challenge(event, this)">
                    <div class="flexbox-info">
                        <div class="solves"><?php echo get_challenge_solves($conn, $challenge_id) ?><span class="material-icons">flag</span></div>
                        <div class="points"><?php echo compute_challenge_points($conn, $challenge_id); ?><span class="material-icons">military_tech</span></div>
                    </div>
                    <div class="challenge_name"><?php echo $challenge_data["challenge_name"]; ?></div>
                    <div class="description"><?php echo $challenge_data["description"]; ?></div>
                    <div class="service"><?php echo $challenge_data["service"]; ?></div>
                    <?php foreach (get_db_challenge_resources($conn, $challenge_id) as $resource) { ?>
                        <a class="resource" href="<?php echo "api/get-challenge-file.php?challenge_name=".$challenge_data["challenge_name"]."&filename=".$resource["filename"]; ?>"><?php echo $resource["filename"]; ?><span class="material-icons">file_download</span></a>
                    <?php } ?>
                    <div class="flag">
                        <input class="flag__input" type="text" placeholder="ITT{...}">
                        <span class="flag__submit material-icons" onclick="submit_flag(event, this)">done</span>
                    </div>
                    <?php foreach (get_challenge_hints($conn, $challenge_id) as $hint) { ?>
                        <div class="hint <?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo "unlocked"; else echo "locked"; ?>">
                            <div class="hint_id"><?php echo $hint["hint_id"]; ?></div>
                            <div class="hint_description"><?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo $hint["description"]; ?></div>
                            <div class="hint_cost"><?php echo $hint["cost"]; ?></div>
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

        opened_challenge = null;

        function open_challenge(event, elem) {
            if (opened_challenge) close_challenge(event, opened_challenge);
            elem.classList.replace("closed", "open");
            opened_challenge = elem;
        }

        function close_challenge(event, elem){
            elem.classList.replace("open", "closed");
            event.stopPropagation();
        }

        function submit_flag(event, elem) {
            flag = elem.parentElement.querySelector('.flag__input').value
            console.log(flag);
            event.stopPropagation();
        }

    </script>
</body>
</html>