<?php 
require "inc/init.php";

$title = "CTF h4ckus4t1 Team";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="team">
    <?php
    $team_name = get_user_team_name($conn, $_SESSION['user_id']);
    $token = get_team_token($conn, $team_name);

    if (!isset($_SESSION["user_id"])) { 
        header("Location: index.php");
    } elseif ($team_name) { 
        if(isset($_GET["action"])) {
            if($_GET["action"] == "quit") quit_team($conn, $_SESSION['user_id']);
            header("Location: ".basename($_SERVER['PHP_SELF']));
        }
    ?>    
        <form method="GET" class="generic-form">
            <h2 class="title">Team</h2>
            <br>
            <h3 style="display: inline;">Team Name: </h3><h4 style="display: inline;"><?php echo $team_name; ?><h4>
            <h3 style="display: inline;">Token: </h3><h4 style="display: inline;"><?php echo $token; ?><h4>
            <button type="submit" name="action" value="quit" class="generic-form__submit">Leave</button><br>
        </form>
    <?php } elseif (!isset($_GET["action"])) { ?>
        <form method="GET" class="generic-form">
            <button type="submit" name="action" value="create" class="generic-form__submit no-margin">Create Team</button><br>
            <button type="submit" name="action" value="join" class="generic-form__submit">Join Team</button>
        </form>
    <?php } elseif ($_GET["action"] == "join") {
        if (isset($_POST["token"])) { 
            if (join_team($conn, $_SESSION['user_id'], $_POST["token"])) header("Location: ".basename($_SERVER['PHP_SELF']));
            $form_error = "Invalid token";
        } ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Join Team</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__box">
                <input type="text" name="token" placeholder=" " minlength="3" required>
                <label>Token</label>
            </div>
            <input type="submit" value="Join" class="generic-form__submit no-margin">
        </form>       
    <?php } elseif ($_GET["action"] == "create") {
        if (isset($_POST["team_name"])) { 
            $token = register_team($conn, $_POST["team_name"]);

            if ($token) {
                join_team($conn, $_SESSION['user_id'], $token);
                header("Location: ".basename($_SERVER['PHP_SELF']));  
            }
            $form_error = "Team name already exists";
        } ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Create Team</h2>
            <h3 class="error"><?php echo $form_error; ?></h3>
            <div class="generic-form__box">
                <input type="text" name="team_name" placeholder=" " minlength="3" maxlength="32" pattern="[\x00-\x7F]+" required>
                <label>Team Name</label>
            </div>
            <input type="submit" value="Create" class="generic-form__submit no-margin">
        </form>
    <?php } ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>