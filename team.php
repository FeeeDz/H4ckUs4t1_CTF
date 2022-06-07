<?php 
require_once "inc/init.php";


$team_id = isset($_GET["team_name"]) ? get_team_id_from_team_name($conn, $_GET["team_name"]) : (isset($_SESSION["user_id"]) ? get_user_team_id($conn, $_SESSION["user_id"]) : NULL);
if (isset($_GET["team_name"]) && !check_if_team_exists($conn, $team_id)) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
$team_name = get_team_name($conn, $team_id);
$token = get_team_token($conn, $team_id);

if ($team_id != null && get_user_team_id($conn, $_SESSION["user_id"]) === $team_id && isset($_GET["redirect"])) exit(header("Location: ".$_GET["redirect"]));
if (!$team_id && !isset($_SESSION["user_id"])) exit(header("Location: login.php"));

$title = "Team - H4ckUs4t1 CTF";
require "inc/head.php";

?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="team">
    <?php if (!isset($_GET["action"])) {
        if ($team_id) { ?>    
            <form method="GET" class="generic-form">
                <h2 class="title">Team</h2>
                <div class="info-box">
                    <h3>Team Name: </h3>
                    <h4><?php echo $team_name; ?></h4>
                </div>
                <?php if ($team_id == get_user_team_id($conn, $_SESSION["user_id"])): ?>
                    <div class="info-box">
                        <h3>Token: </h3>
                        <h4><?php echo $token; ?></h4>
                    </div>
                <?php endif; ?>
                <div class="info-box">
                    <h3>Score: </h3>
                    <h4><?php echo get_team_score($conn, $team_id); ?></h4>
                </div>
                <div class="members">
                    <h3>Members: </h3>
                    <?php foreach (get_team_members($conn, $team_id) as $member): ?>
                        <a class="user-team-link" href="user.php?username=<?php echo $member["username"]; ?>"><?php echo $member["username"]; ?></a>
                    <?php endforeach; ?>
                </div>
                <?php if ($solves = get_team_solves($conn, $team_id)) : ?> 
                    <div class="solves-container">
                        <h3>Solves: </h3>
                        <?php foreach ($solves as $solve) : ?>
                            <div class="solve">
                                <div class="challenge-name"><?php echo $solve["challenge_name"]; ?></div>
                                <div class="points"><?php echo $solve["points"]; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?> 
                <?php if ($team_id == get_user_team_id($conn, $_SESSION["user_id"])): ?>
                <button type="submit" name="action" value="quit" class="generic-form__button">Leave</button><br>
                <?php endif; ?>
            </form>
        <?php } else { ?>
        <form method="GET" class="generic-form">
            <button type="submit" name="action" value="create" class="generic-form__button no-margin">Create Team</button><br>
            <button type="submit" name="action" value="join" class="generic-form__button">Join Team</button>
            <?php if (isset($_GET["redirect"])) echo "<input type=\"hidden\" name=\"redirect\" value=\"".$_GET["redirect"]."\">" ?>
        </form>
        <?php } ?>
    <?php } elseif ($_GET["action"] == "join" && !$team_id) {
        if (isset($_POST["submit"])) { 
            if (join_team($conn, $_SESSION['user_id'], $_POST["token"])) exit(header("Location: ".basename($_SERVER['REQUEST_URI'])));
            $form_error = "Invalid token";
        } ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Join Team</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__input-box">
                <input type="text" name="token" placeholder=" " minlength="3" required>
                <label>Token</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Join</button>
        </form>       
    <?php } elseif ($_GET["action"] == "create" && !$team_id) {
        if (isset($_POST["submit"])) { 
            $token = register_team($conn, $_POST["team_name"]);

            if ($token) {
                join_team($conn, $_SESSION['user_id'], $token);
                exit(header("Location: ".basename($_SERVER['REQUEST_URI'])));
            }
            $form_error = "Team name already exists";
        } ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Create Team</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__input-box">
                <input type="text" name="team_name" placeholder=" " minlength="3" maxlength="16" pattern="^(\d|\w)+$" title="string with 3 to 16 non-special characters and no spaces" required>
                <label>Team Name</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Create</button>
        </form>
    <?php } elseif ($_GET["action"] == "quit" && $team_id != null && get_user_team_id($conn, $_SESSION["user_id"]) === $team_id) {
        if (isset($_POST["submit"])) { 
            if (check_credentials($conn, get_user_email($conn, $_SESSION["user_id"]), $_POST["password"])) {
                quit_team($conn, $_SESSION['user_id']);
                exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
            }
            $form_error = "Wrong password";
        } ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Quit team</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__input-box">
                <input type="password" name="password" placeholder=" " autocomplete="current-password" required>
                <label>Password</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Quit</button>
        </form>
    <?php } else exit(header("Location: ".basename($_SERVER['PHP_SELF']))); ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>