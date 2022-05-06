<?php 
session_start();

if($_SESSION["role"] != 'A') {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    header("Location: $redirect");
}

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    <?php if (!isset($_GET["action"])) { ?>
        <form method="GET">
            <input type="submit" value="Add challenge">
            <input type="hidden" name="action" value="add">
        </form>
        <form method="GET">
            <input type="submit" value="Edit challenge">
            <input type="hidden" name="action" value="edit">
        </form>
    <?php } elseif ($_GET["action"] == "add") {
        if (isset($_POST["token"])) { 
            if (join_team($conn, $_POST["token"])) header("Location: ".basename($_SERVER['PHP_SELF']));
            echo "token non valido";
        } ?>
        <form method="POST">
            <input type="text" name="token" minlength="3" required>
            <input type="submit" value="Join team">
        </form>       
    <?php } elseif ($_GET["action"] == "edit") {
        require "inc/functions.php";
        $conn = db_connect();
        
        if (isset($_POST["challenge"])) { 
            echo "modifica".$_POST["challenge"];
        } else { ?>
            <form method="GET">
                <select name="challenge">
                <?php
                    $query = "SELECT challenge_name FROM CTF_challenge";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $token);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                ?>
                    <option value="volvo">Volvo</option>
                </select>
                <input type="submit" value="Edit challenge">
            </form>
    <?php }
    } ?>
    </div>
    <?php require "inc/footer.php"; ?>
    <script src="js/script.js"></script> 
</body>
</html>