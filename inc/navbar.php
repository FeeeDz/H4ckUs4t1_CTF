<nav id="nav">
    <input type="checkbox" id="nav__checkbox">
    <label for="nav__checkbox" id="nav__toggle">
        <span id="nav__toggle__menu" class="material-icons">menu</span>
        <span id="nav__toggle__close" class="material-icons">close</span>
    </label>
    <ul id="nav__menu">
        <li><a id="nav__logo" href="index.php">
            <span class="material-icons">polymer</span>
        </a></li>
        <li><div id="nav__nav">
            <a href="index.php">
                <span class="material-icons">home</span>
                <span>Home</span>
            </a>
            <a href="scoreboard.php">
                <span class="material-icons">scoreboard</span>
                <span>Classifica</span>
            </a>
            <a href="challenges.php">
                <span class="material-icons">polymer</span>
                <span>Challenges</span>
            </a>
        </div></li>
        <li><div id="nav__account">
        <?php if (isset($_SESSION["logged"])) : ?>
            <a id="account__button" href="account.php">
                <span class="material-icons">person</span>
                <span><?php echo $_SESSION["logged"]; ?></span>
            </a>
            <a id="team__button" href="team.php">
                <span class="material-icons">group</span>
                <span>Team</span>
            </a>
            <a id="logout__button" href="logout.php">
                <span class="material-icons">logout</span>
                <span>Logout</span>
            </a>
        <?php else : ?>
            <a id="login__button" href="login.php?redirect=<?php echo basename($_SERVER['PHP_SELF']);?>">
                <span class="material-icons">login</span>
                <span>Login</span>
            </a>
            <a id="register__button" href="register.php">
                <span class="material-icons">edit_note</span>
                <span>Register</span>
            </a>
        <?php endif; ?>
        </div></li>
    </ul>
    
    
</nav>