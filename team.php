<?php 
require "inc/init.php";

if (!isset($_SESSION["user_id"])) exit(header("Location: index.php"));

$team_name = get_user_team_name($conn, $_SESSION['user_id']);
$token = get_team_token($conn, $team_name);

if ($team_name && isset($_GET["redirect"])) exit(header("Location: ".$_GET["redirect"]));

$title = "Team - H4ckUs4t1 CTF";
require "inc/head.php";

?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="team">
    <?php
    if ($team_name) { 
        if(isset($_GET["action"])) {
            if($_GET["action"] == "quit") quit_team($conn, $_SESSION['user_id']);
            exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
        }
    ?>    
        <form method="GET" class="generic-form">
            <h2 class="title">Team</h2>
            <br>
            <h3 style="display: inline;">Team Name: </h3><h4 style="display: inline;"><?php echo $team_name; ?><h4>
            <h3 style="display: inline;">Token: </h3><h4 style="display: inline;"><?php echo $token; ?><h4>
            <button type="submit" name="action" value="quit" class="generic-form__button">Leave</button><br>
        </form>
    <?php } elseif (!isset($_GET["action"])) { ?>
        <form method="GET" class="generic-form">
            <button type="submit" name="action" value="create" class="generic-form__button no-margin">Create Team</button><br>
            <button type="submit" name="action" value="join" class="generic-form__button">Join Team</button>
            <?php if (isset($_GET["redirect"])) echo "<input type=\"hidden\" name=\"redirect\" value=\"".$_GET["redirect"]."\">" ?>
        </form>
    <?php } elseif ($_GET["action"] == "join") {
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
    <?php } elseif ($_GET["action"] == "create") {
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
                <input type="text" name="team_name" placeholder=" " minlength="3" maxlength="32" pattern="[\x00-\x7F]+" required>
                <label>Team Name</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Create</button>
        </form>
    <?php } ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>