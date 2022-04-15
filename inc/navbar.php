<nav id="navbar">
    <div id="navbar-logo" style="height: 90px;">
        <img src = "img/logo.svg">
    </div>
    <div id="navbar-menu">
        <button id="navbar-menu-button" onclick="navbar_menu_button()"></button>
    </div>
    <div id="navbar-nav">
        <a href="">
            <span id="navbar-nav-home" class="navbar-nav-icon"></span>
            <span>Home</span>
        </a>
        <a href="">
            <span id="navbar-nav-scoreboard" class="navbar-nav-icon"></span>
            <span>Classifica</span>
        </a>
        <a href="">
            <span id="navbar-nav-flag" class="navbar-nav-icon"></span>
            <span>Challenges</span>
        </a>
    </div>
    <div id="navbar-login-menu">
    <?php if ($_SESSION["logged"]) : ?>
        <button id="account-button">
            Account
        </button>
        <button id="logout-button">
            Logout
        </button>
    <?php else : ?>
        <button id="login-button">
            Login
        </button>
        <button id="register-button">
            Register
        </button>
    <?php endif; ?>
    </div>
    
</nav>