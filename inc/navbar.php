<nav id="navbar">
    <div id="navbar-logo" style="height: 90px;">
        <a href="index.php">
            <img src="img/logo.svg">
        </a>
    </div>
    <div id="navbar-menu">
        <button id="navbar-menu-button" onclick="navbar_menu_button()"></button>
    </div>
    <div id="navbar-nav">
        <a href="index.php">
            <span id="navbar-nav-home" class="navbar-nav-icon"></span>
            <span>Home</span>
        </a>
        <a href="scoreboard.php">
            <span id="navbar-nav-scoreboard" class="navbar-nav-icon"></span>
            <span>Classifica</span>
        </a>
        <a href="challenges.php">
            <span id="navbar-nav-flag" class="navbar-nav-icon"></span>
            <span>Challenges</span>
        </a>
    </div>
    <div id="navbar-login-menu">
    <?php if (isset($_SESSION["logged"])) : ?>
        <button id="account-button" onclick="location.href = 'account.php'">
            <?php echo $_SESSION["logged"]; ?>
        </button>
        <button id="logout-button" onclick="location.href = 'logout.php'">
            Logout
        </button>
    <?php else : ?>
        <button id="login-button" onclick="location.href = 'login.php?redirect=<?php echo basename($_SERVER['PHP_SELF']);?>'">
            Login
        </button>
        <button id="register-button" onclick="location.href = 'register.php'">
            Register
        </button>
    <?php endif; ?>
    </div>
    
</nav>