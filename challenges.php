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
            <?php foreach (get_challenge_categories($conn) as $category) : ?>
                <span class="challenges__category"><?php echo $category?></span>
                <div class="challenges__grid">
                    <?php foreach (get_challenges_from_category($conn, $category) as $challenge_id) {
                        $challenge_data = get_challenge_data($conn, $challenge_id);
                    ?>
                        <div class="challenge_name"><?php echo $challenge_data["challenge_name"]; ?></div>
                        <div class="description"><?php echo $challenge_data["description"]; ?></div>
                        <div class="points"><?php echo $compute_challenge_points($conn, $challenge_id); ?></div>
                        <?php foreach (get_challenge_hints($conn, $challenge_id) as $hint) {
                        } ?>
                    <?php } ?>
                </div>
            <?php endforeach; ?>

            <span class="challenges__category">Miscellaneous</span>
            <div class="challenges__grid">
                <div class="challenge__box">
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