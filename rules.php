<?php 
require_once "inc/init.php";

$title = "Rules - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>  
    <div id="main" class="rules">
        <div class="rules-container">
            <h1>Rules</h1>
            <h4><b>Do NOT:</b></h4>
            <ul>
                <li class="cross">Share flags with or give help to other participants</li>
                <li class="cross">Use automated scanning and probing tools such as Nmap, Gobuster, Dirb, sqlmap, etc. <i>unless specified in the challenge description</i></li>
                <li class="cross">Attempt to (or succeed at) DoSing or DDoSing any infrastructure or other participants.</li>
                <li class="cross">Try to brute force flag submissions - it will not work</li>
                <li class="cross">Hoard flags. Any user that solves multiple challenges but doesn't turn in the flag until the last moment to trick others into thinking they will win will be punished. <b>If you're really good enough to win, you don't need to do this.</b></li>
            </ul>
            <br>
            <h4>Notes:</h4>
            <ul>
                <li class="arrow">The flag format is <code>ITT{[A-Za-z0-9_]*}</code></li>
                <li class="arrow">All flags are case sensitive unless specified.</li>
                <li class="arrow">Scoring is dynamic and decreases in value as more participants solve the problem. Most challenges start at 500 points.</li>
                <!-- <li class="arrow">Each problem has a tag telling you whether it's "Easy", "Medium", or "Hard" - these may not be perfect, but they are generally correct.</li> -->
                <li class="arrow">If any challenges are broken, you feel the flag you have is correct, or you have any other questions, please reach out to an admin on our <a class="link" href="https://discord.gg/dkZvT4exTV">Discord</a>.</li>
                <li class="arrow">If you do anything that we believe to be directly against the spirit of the competition, we reserve the right to remove anyone at any point. Please don't make us do that. This is for your learning and benefit.</li>
            </ul>
        </div>
        <!-- <iframe src="https://discord.com/widget?id=979304888112664667&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe> -->
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/@widgetbot/crate@3' async defer>
    new Crate({
        server: '979304888112664667', // Hackusati CTF
        channel: '979307370868002866' // #rules
    })
    </script>
</body>
</html>