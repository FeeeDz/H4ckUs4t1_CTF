<nav id="nav">
    <input type="checkbox" id="nav__checkbox">
    <label for="nav__checkbox" id="nav__toggle">
        <span id="nav__toggle__menu" class="material-icons">menu</span>
        <span id="nav__toggle__close" class="material-icons">close</span>
    </label>
    <ul id="nav__menu">
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
        <li><a href="challenges.php">
            <span class="material-icons">polymer</span>
            <span>Challenges</span>
        </a></li>
    </ul>
    
    <div id="nav__account">
    <?php if (isset($_SESSION["logged"])) : ?>
        <button id="account__button" onclick="location.href = 'account.php'">
            <?php echo $_SESSION["logged"]; ?>
        </button>
        <button id="team__button" onclick="location.href = 'team.php'">
            Team
        </button>
        <button id="logout__button" onclick="location.href = 'logout.php'">
            Logout
        </button>
    <?php else : ?>
        <button id="login__button" onclick="location.href = 'login.php?redirect=<?php echo basename($_SERVER['PHP_SELF']);?>'">
            Login
        </button>
        <button id="register__button" onclick="location.href = 'register.php'">
            Register
        </button>
    <?php endif; ?>
    </div>
    
</nav>