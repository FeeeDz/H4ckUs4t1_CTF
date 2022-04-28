<nav id="nav">
    <input type="checkbox" id="nav__checkbox">
    <label for="nav__checkbox" id="nav__toggle">
        <span class="material-icons">menu</span>
        <span class="material-icons">close</span>
    </label>
    <ul class="nav__menu">
        <li><a href="index.php">
            <span class="material-icons">polymer</span>
        </a></li>
        <li><a href="index.php">
            <span class="material-icons">home</span>
            <span>Home</span>
        </a></li>
        <li><a href="scoreboard.php">
            <span class="material-icons">scoreboard</span>
            <span>Classifica</span>
        </a></li>
        <li><a href="challenges.php">
            <span class="material-icons">polymer</span>
            <span>Challenges</span>
        </a></li>
    </ul>
        <!-- <div id="navbar-login-menu">
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
        </div> -->
    
</nav>