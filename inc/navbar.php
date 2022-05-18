<input type="checkbox" id="nav__checkbox">
<label for="nav__checkbox" id="nav__toggle">
    <span id="nav__toggle__menu" class="material-icons">menu</span>
    <span id="nav__toggle__close" class="material-icons">close</span>
</label>
<ul id="nav__menu">
    <?php if (basename($_SERVER['PHP_SELF']) == "index.php") : ?>
        <li><a id="nav__logo" href="index.php">
            <img src="img/logo_montani2.png">
        </a></li>
    <?php else : ?>
        <li><a id="nav__logo" href="index.php">
            <img src="img/logo_h4ckus4t1_black_and_white.png">
        </a></li>
    <?php endif; ?>
    <li><div id="nav__nav">
        <a href="index.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "index.php") echo "active"; ?>">
            <span class="material-icons">home</span>
            <span>Home</span>
        </a>
        <a href="rules.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "rules.php") echo "active"; ?>">
            <span class="material-icons">description</span>
            <span>Rules</span>
        </a>
        <a href="leaderboard.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "leaderboard.php") echo "active"; ?>">
            <span class="material-icons">leaderboard</span>
            <span>Leaderboard</span>
        </a>
        <a href="challenges.php" class="<?php if(basename($_SERVER['PHP_SELF']) == "challenges.php") echo "active"; ?>">
            <span class="material-icons">polymer</span>
            <span>Challenges</span>
        </a>
    </div></li>
    <li><div id="nav__account">
    <?php if (isset($_SESSION["user_id"])) { ?>
        <a id="account-button" href="account.php">
            <span class="material-icons">person</span>
            <span><?php echo get_username_from_id($conn, $_SESSION["user_id"]); ?></span>
        </a>
        <a id="team-button" href="team.php">
            <span class="material-icons">group</span>
            <span>Team</span>
        </a>
        <?php if (get_user_role($conn, $_SESSION["user_id"]) == 'A') { ?>
        <a id="admin-panel-button" href="admin-panel.php">
            <span class="material-icons">admin_panel_settings</span>
            <span>Admin Panel</span>
        </a>
        <?php } ?>
        <a id="logout-button" href="logout.php">
            <span class="material-icons">logout</span>
            <span>Logout</span>
        </a>
    <?php } else { ?>
        <a id="login-button" href="login.php?redirect=<?php echo basename($_SERVER['PHP_SELF']);?>">
            <span class="material-icons">login</span>
            <span>Login</span>
        </a>
        <a id="register-button" href="register.php">
            <span class="material-icons">edit_note</span>
            <span>Register</span>
        </a>
    <?php } ?>
    </div></li>
</ul>