<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

redirect_if_not_admin();

if (isset($_POST["submit"]) && isset($_POST["challenge_name"]) && !empty($_POST["challenge_name"])) {
    $challenge_id = get_challenge_id($conn, $_POST["challenge_name"]);
    $success = true;
    switch ($_POST["action"]) {
        case "add":
            $challenge_id = add_challenge($conn, $_POST["challenge_name"], $_POST["flag"], $_POST["description"], $_POST["type"], $_POST["category"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"]);
            if (!$challenge_id){
                $success = false;
                break;
            } 

            if(isset($_POST["add_hint_description"]))
                for($i = 0; $i < count($_POST["add_hint_description"]); $i++)
                    if (!add_challenge_hint($conn, $challenge_id, $_POST["add_hint_description"][$i], $_POST["add_hint_cost"][$i])) $success = false;

            if(isset($_POST["add_resource"]))
                foreach ($_POST["add_resource"] as $resource_filename)
                    if (!add_challenge_resource($conn, $challenge_id, $resource_filename)) $success = false;

            if (!$success) header("Refresh:0");

            break;

        case "delete":
            delete_challenge($conn, $challenge_id);
            break;

        case "edit":
            if (!edit_challenge_data($conn, $challenge_id, $_POST["description"], $_POST["type"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"])) $success = false;

            if(isset($_POST["delete_hint"]))
                foreach ($_POST["delete_hint"] as $hint_id)
                    if (!delete_challenge_hint($conn, $hint_id)) $success = false;

            if(isset($_POST["delete_resource"]))
                foreach ($_POST["delete_resource"] as $resource_id)
                    if (!delete_challenge_resource($conn, $resource_id)) $success = false;

            if(isset($_POST["add_hint_description"]))
                for($i = 0; $i < count($_POST["add_hint_description"]); $i++)
                    if (!add_challenge_hint($conn, $challenge_id, $_POST["add_hint_description"][$i], $_POST["add_hint_cost"][$i])) $success = false;

            if(isset($_POST["add_resource"]))
                foreach ($_POST["add_resource"] as $resource_filename)
                    if (!add_challenge_resource($conn, $challenge_id, $resource_filename)) $success = false;

            if(isset($_POST["edit_hint_id"]))
                for($i = 0; $i < count($_POST["edit_hint_id"]); $i++)
                    if (!edit_challenge_hint($conn, $_POST["edit_hint_id"][$i], $_POST["edit_hint_description"][$i], $_POST["edit_hint_cost"][$i])) $success = false;

            if (!$success) header("Refresh:0");

            break;
        }
    if ($success) header("Location: ".basename($_SERVER['PHP_SELF']));
}

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main">
    <?php if (!isset($_GET["action"])) { ?>
        <form method="GET">
            <input type="hidden" name="action" value="add">
            <input type="submit" value="Add challenge">
        </form>
        <form method="GET">
            <input type="hidden" name="action" value="edit">
            <input type="submit" value="Edit challenge">
        </form>
        <form method="GET">
            <input type="hidden" name="action" value="delete">
            <input type="submit" value="Delete challenge">
        </form>
    <?php } elseif (!isset($_GET["challenge_name"])) { ?>
        <?php if($_GET["action"] == "delete") : ?>
            <form method="POST">
        <?php else : ?>
            <form method="GET">
        <?php endif; ?>
                <input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
                <select name="challenge_name">
                <?php
                    if($_GET["action"] == "add") {
                        $challenges = get_db_missing_challenges($conn);
                        if (!$challenges) {
                            echo "<option value=''></option>";
                        } else {
                            foreach ($challenges as $challenge)
                                echo "<option value='".$challenge."'>".$challenge."</option>";
                        }
                    } else {
                        $rows = get_challenge_list($conn);
                        foreach ($rows as $row)
                            echo "<option value='".$row["challenge_name"]."'>".$row["challenge_name"]."</option>";
                    }
                ?>
                </select>
                <?php if($_GET["action"] == "add") : ?>
                    <input type="submit" value="Add challenge">
                <?php elseif($_GET["action"] == "edit") : ?>
                    <input type="submit" value="Edit challenge">
                <?php elseif($_GET["action"] == "delete") : ?>
                    <input type="hidden" name="submit">
                    <input type="submit" value="Delete challenge">
                <?php endif; ?>
            </form>
    <?php } elseif ($_GET["action"] == "add") {
        $challenge_name = $_GET["challenge_name"];
        $category = get_local_challenge_category($challenge_name);
        $flag = get_local_challenge_flag($challenge_name);
        if (is_challenge_name_used($conn, $challenge_name) || !$category || !$flag ) header("Location: ".basename($_SERVER['PHP_SELF']));
    ?>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="sumbit">
            <input type="text" name="challenge_name" value="<?php echo $challenge_name; ?>" readonly>
            <input type="text" name="category" value="<?php echo $category; ?>" readonly>
            <input type="text" name="flag" value="<?php echo $flag; ?>" readonly>
            <input type="text" name="description" required>
            <input type="number" name="initial_points" required>
            <input type="number" name="minimum_points" required>
            <input type="number" name="points_decay" required>
            <select name="type" required>
                <option value="T">Training</option>
                <option value="O">Official</option>
            </select>
            <div>
                <span class="material-icons" id="add-hint" onclick="add_hint()">add</span>
            </div>
            <div>
                <div id="add-resource">
                    <select id="select-resource">
                    <?php
                        $filenames = get_local_challenge_resources($challenge_name);
                        foreach ($filenames as $filename)
                            echo "<option value='".$filename."'>".$filename."</option>";
                    ?>
                    </select>
                    <span class="material-icons" onclick="add_resource()">add</span>
                </div>
            </div>
            <input type="submit" name="submit" value="Add challenge">
        </form>
    <?php } elseif ($_GET["action"] == "edit") {
        $challenge_name = $_GET["challenge_name"];
        $challenge_id = get_challenge_id($conn, $challenge_name);
        if(!$challenge_id) header("Location: ".basename($_SERVER['PHP_SELF']));

        $challenge_data = get_challenge_data($conn, $challenge_id);
        $hints = get_challenge_hints($conn, $challenge_id);
        $resources = get_db_challenge_resources($conn, $challenge_id);
    ?>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="sumbit">
            <input type="text" name="challenge_name" value="<?php echo $challenge_name; ?>" readonly>
            <input type="text" name="category" value="<?php echo $challenge_data["category"]; ?>" readonly>
            <input type="text" name="flag" value="<?php echo $challenge_data["flag"]; ?>" readonly>
            <input type="text" name="description" value="<?php echo $challenge_data["description"]; ?>" required>
            <input type="number" name="initial_points" value="<?php echo $challenge_data["initial_points"]; ?>" required>
            <input type="number" name="minimum_points" value="<?php echo $challenge_data["minimum_points"]; ?>" required>
            <input type="number" name="points_decay" value="<?php echo $challenge_data["points_decay"]; ?>" required>
            <select name="type">
                <option value="T" <?php if($challenge_data["type"] == "T") echo "selected=\"selected\""?>>Training</option>
                <option value="O" <?php if($challenge_data["type"] == "O") echo "selected=\"selected\""?>>Official</option>
            </select>
            <div>
            <?php if($hints) foreach($hints as $hint) : ?>
                <span class="challenge-hint">
                    <input type="hidden" name="edit_hint_id[]" value="<?php echo $hint["hint_id"]?>" required>
                    <input type="text" name="edit_hint_description[]" value="<?php echo $hint["description"]?>" required>
                    <input type="number" min="0" name="edit_hint_cost[]" value="<?php echo $hint["cost"]?>" required>
                    <span class="material-icons" onclick="delete_hint(this.parentNode)">remove</span>
                </span>
            <?php endforeach; ?>
                <span class="material-icons" id="add-hint" onclick="add_hint()">add</span>
            </div>
            <div>
            <?php if($resources) foreach($resources as $resource) : ?>
                <span class="challenge-resource">
                    <input type="text" name="edit_resource_filename[]" value="<?php echo $resource["filename"]?>" readonly>
                    <input type="hidden" name="edit_resource_id[]" value="<?php echo $resource["resource_id"]?>">
                    <span class="material-icons" onclick="delete_resource(this.parentNode)">remove</span>
                </span>
            <?php endforeach; ?>
                <div id="add-resource">
                    <select id="select-resource">
                    <?php
                        $local_resources = get_local_challenge_resources($challenge_name);
                        foreach ($local_resources as $filename)
                            echo "<option value='".$filename."'>".$filename."</option>";
                    ?>
                    </select>
                    <span class="material-icons" onclick="add_resource()">add</span>
                </div>
            </div>
            <input type="submit" name="submit" value="Edit challenge">
        </form>
    <?php } ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        remove_duplicated_resources();

        function remove_duplicated_resources() {
            const select_resource = document.getElementById("select-resource");
            const challenge_resources = document.getElementsByClassName("challenge-resource");

            for (const resource of challenge_resources) {
                const resource_input = resource.querySelector("input");

                for (let i = 0; i < select_resource.options.length; i++) {
                    if (select_resource.options[i].value == resource_input.value)
                        select_resource.removeChild(select_resource.options[i]);
                }
            }
        }

        function add_hint() {
            const elem = document.createElement("span");
            elem.classList.add("challenge-hint");

            elem.innerHTML = '<input type="text" name="add_hint_description[]" required><input type="number" min="0" name="add_hint_cost[]" required><span class="material-icons" onclick="delete_hint(this.parentNode)">remove</span>';

            const target = document.getElementById("add-hint");
            target.parentNode.insertBefore(elem, target);
        }

        function delete_hint(parent_node) {
            const inputs = parent_node.querySelectorAll("input");

            if(inputs[0].name.includes("add")) {
                parent_node.remove();
                return;
            }

            const delete_hint = document.createElement("input");
            delete_hint.setAttribute("name", "delete_hint[]");
            delete_hint.setAttribute("value", inputs[0].value);
            delete_hint.style.display = "none";

            parent_node.parentNode.appendChild(delete_hint);
            parent_node.remove();
        }

        function add_resource() {
            const select_resource = document.getElementById("select-resource");
            if(select_resource.options.length <= 0) return;

            const elem = document.createElement("span");
            elem.classList.add("challenge-resource");

            elem.innerHTML = '<input type="text" name="add_resource[]" value="'+select_resource.value+'"><span class="material-icons" onclick="delete_resource(this.parentNode)">remove</span>';

            select_resource.removeChild(select_resource.options[select_resource.selectedIndex]);

            const target = document.getElementById("add-resource");

            target.parentNode.insertBefore(elem, target);
        }

        function delete_resource(parent_node) {
            const inputs = parent_node.querySelectorAll("input");

            var option = document.createElement("option");
            option.value = inputs[0].value;
            option.innerHTML = inputs[0].value;

            const select_resource = document.getElementById("select-resource");
            select_resource.appendChild(option);

            if(inputs[0].name.includes("add")) {
                parent_node.remove();
                return;
            }

            const delete_resource = document.createElement("input");
            delete_resource.setAttribute("name", "delete_resource[]");
            delete_resource.setAttribute("value", inputs[1].value);
            delete_resource.style.display = "none";

            parent_node.parentNode.appendChild(delete_resource);
            parent_node.remove();
        }

    </script>
</body>
</html>