<?php 
require "inc/init.php";

$title = "Get Started - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="get-started">
        <?php 
        require "inc/Parsedown.php";
        $Parsedown = new Parsedown();

        echo $Parsedown->text(file_get_contents("docs/pwn/00-intro.md")); # prints: <p>Hello <em>Parsedown</em>!</p>
        
        ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
</body>
</html>