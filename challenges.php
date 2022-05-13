<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

if (!isset($_SESSION["user_id"])) header("Location: login.php");

if (is_event_started($conn)) $challenge_type = "O";
else $challenge_type = "T";

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main">
        <div class="challenges__container">
            <?php foreach (get_challenge_categories($conn) as $category) : ?>
                <span class="challenges__category"><?php echo $category?></span>
                <div class="challenges__grid">
                    <?php foreach (get_challenges_from_category($conn, $category, $challenge_type) as $challenge_id) {
                        $challenge_data = get_challenge_data($conn, $challenge_id);
                    ?>
                    <div class="challenge__box">
                        <div class="challenge_name"><?php echo $challenge_data["challenge_name"]; ?></div>
                        <div class="description" style="display: none;"><?php echo $challenge_data["description"]; ?></div>
                        <div class="solves"><?php echo get_challenge_solves($conn, $challenge_id) ?></div>
                        <div class="points"><?php echo compute_challenge_points($conn, $challenge_id); ?></div>
                        <?php foreach (get_challenge_hints($conn, $challenge_id) as $hint) { ?>
                            <div class="hint <?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo "unlocked_hint"; else echo "locked_hint"; ?>" style="display: none;">
                                <div class="hint_id"><?php echo $hint["hint_id"]; ?></div>
                                <div class="hint_description"><?php if (is_hint_unlocked($conn, $hint["hint_id"], $_SESSION["user_id"])) echo $hint["description"]; ?></div>
                                <div class="hint_cost"><?php echo $hint["cost"]; ?></div>
                            </div>
                        <?php } ?>
                        <?php foreach (get_db_challenge_resources($conn, $challenge_id) as $resource) { ?>
                            <div class="resource" style="display: none;">
                                <div class="resource_filename"><?php echo $resource["filename"]; ?></div>
                                <div class="resource_link"><?php echo get_base_url()."/".$site_directory."/api/get-challenge-file.php?challenge_name=".$challenge_data["challenge_name"]."&filename=".$resource["filename"]; ?></div>
                                <div class="hint_cost"><?php echo $hint["cost"]; ?></div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>