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
    <?php } elseif ($_GET["action"] == "add") { ?>
        add     
    <?php } elseif ($_GET["action"] == "edit") {
        require "inc/functions.php";
        $conn = db_connect();
        
        if (isset($_GET["challenge"])) { 
            $query = "SELECT challenge_name, flag, description, type, category FROM CTF_challenge WHERE challenge_name = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $_GET["challenge"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if(!$row) header("Location: ".basename($_SERVER['PHP_SELF']));

            echo "edit";

        } else { ?>
            <form method="GET">
                <input type="hidden" name="action" value="edit">
                <select name="challenge">
                <?php
                    $query = "SELECT challenge_name FROM CTF_challenge";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc())
                        echo "<option value='".$row["challenge_name"]."'>".$row["challenge_name"]."</option>";
                ?>
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