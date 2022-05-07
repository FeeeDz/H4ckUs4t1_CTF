<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

redirect_if_not_admin();

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
        if (isset($_GET["challenge_name"])) { 
            $query = "SELECT challenge_name, flag, description, type, category, CTF_hint.description AS hint_description, cost, link
                FROM CTF_challenge 
                INNER JOIN CTF_hint ON CTF_challenge.challenge_id = CTF_hint.challenge_id
                INNER JOIN CTF_resource ON CTF_challenge.challenge_id = CTF_resource.challenge_id
                WHERE challenge_name = ?";
            
            $challenge_id = get_challenge_id($conn, $_GET["challenge_name"]);
            if(!$challenge_id) header("Location: ".basename($_SERVER['PHP_SELF'])."?action=edit");

            $challenge_data = get_challenge_data($conn, $challenge_id);
            $hints = get_challenge_hints($conn, $challenge_id);
            $resources = get_challenge_resources($conn, $challenge_id);
            $categories = get_challenge_categories($conn);
        ?>
            <form method="POST" action="<?php echo basename($_SERVER['PHP_SELF'])."?action=edit&challenge_name=".$_GET["challenge_name"]; ?>">
                <input type="text" name="description" value="<?php echo $challenge_data["description"]; ?>">
                <select name="type">
                    <option <?php if($challenge_data["type"] == "T") echo "selected=\"selected\""?>>T</option>
                    <option <?php if($challenge_data["type"] == "O") echo "selected=\"selected\""?>>O</option>
                </select>
                <div>
                    <?php foreach($hints as $hint) : ?>
                        <span style="border: 3px solid black;">
                            <input type="text" name="hint_description_<?php echo $hint["hint_id"]?>" value="<?php echo $hint["description"]?>">
                            <input type="number" name="hint_cost_<?php echo $hint["hint_id"]?>" value="<?php echo $hint["cost"]?>">
                            <span class="material-icons">remove</span>
                        </span>
                    <?php endforeach; ?>
                    <span class="material-icons">add</span>
                </div>
                <div>
                    <?php foreach($resources as $resource) :?>
                        <span style="border: 3px solid black;">
                            <input type="text" name="resource_<?php echo $resource["resource_id"]?>" value="<?php echo $resource["link"]?>">
                            <span class="material-icons">remove</span>
                        </span>
                    <?php endforeach; ?>
                    <span class="material-icons">add</span>
                </div>
                <input type="submit" value="Edit challenge">
            </form>
        <?php } else { ?>
            <form method="GET">
                <input type="hidden" name="action" value="edit">
                <select name="challenge_name">
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
    <script>
        // let url = window.location;
        // let base_url = url .protocol + "//" + url.host + "/" + url.pathname.split('/')[1];
        // base_url += "/informatica/CTF_h4ckus4t1/";

        // function insertAfter(newNode, referenceNode) {
        //     referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        // }

        // function edit(button) {
        //     fetch(base_url + "api/get_challenge_names.php")
        //     .then(response => response.json())
        //     .then(data => console.log(data));
        // }

    </script>
</body>
</html>