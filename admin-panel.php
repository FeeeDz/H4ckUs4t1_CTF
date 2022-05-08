<?php 
session_start();
require "inc/functions.php";
$conn = db_connect();

redirect_if_not_admin();

if (isset($_POST["submin"])) {
    switch ($_POST["action"]) {
        case "add":

            break;

        case "delete":
            
            break;

        case "edit":

            break;
    }
}

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <?php require "inc/navbar.php"; ?>
    <div id="main">
    <?php if (!isset($_POST["action"])) { ?>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="submit" value="Add challenge">
        </form>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="submit" value="Edit challenge">
        </form>
        <form method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="submit" value="Delete challenge">
        </form>
    <?php } elseif ($_POST["action"] == "add") { ?>
        add
    <?php } elseif ($_POST["action"] == "delete") { ?>
        <form method="POST">
            <input type="hidden" name="sumbit">
            <input type="hidden" name="action" value="delete">
            <select name="challenge_name">
            <?php
                $rows = get_challenge_names($conn);
                foreach ($rows as $row)
                    echo "<option value='".$row["challenge_name"]."'>".$row["challenge_name"]."</option>";
            ?>
            </select>
            <input type="submit" value="Delete challenge">
        </form>
    <?php } elseif ($_POST["action"] == "edit") {
        if (isset($_POST["challenge_name"])) { 
            $challenge_id = get_challenge_id($conn, $_POST["challenge_name"]);
            if(!$challenge_id) header("Location: ".basename($_SERVER['PHP_SELF'])."?action=edit");

            $challenge_data = get_challenge_data($conn, $challenge_id);
            $hints = get_challenge_hints($conn, $challenge_id);
            $resources = get_challenge_resources($conn, $challenge_id);
            $categories = get_challenge_categories($conn);
        ?>
            <form method="POST" action="<?php echo basename($_SERVER['PHP_SELF'])."?action=edit&challenge_name=".$_POST["challenge_name"]; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="challenge_name" value="<?php echo $_POST["challenge_name"]; ?>">
                <input type="hidden" name="sumbit">
                <input type="text" name="description" value="<?php echo $challenge_data["description"]; ?>">
                <select name="type">
                    <option <?php if($challenge_data["type"] == "T") echo "selected=\"selected\""?>>T</option>
                    <option <?php if($challenge_data["type"] == "O") echo "selected=\"selected\""?>>O</option>
                </select>
                <div>
                <?php foreach($hints as $hint) : ?>
                    <span class="challenge-hint">
                        <input type="text" name="edit_hint_description_<?php echo $hint["hint_id"]?>" value="<?php echo $hint["description"]?>" required>
                        <input type="number" min="0" name="edit-hint-cost_<?php echo $hint["hint_id"]?>" value="<?php echo $hint["cost"]?>" required>
                        <span class="material-icons" onclick="delete_hint(this.parentNode)">remove</span>
                    </span>
                <?php endforeach; ?>
                    <span class="material-icons" id="add-hint" onclick="add_hint()">add</span>
                </div>
                <div>
                <?php foreach($resources as $resource) :?>
                    <span class="challenge-resource">
                        <input type="text" name="edit_resource_<?php echo $resource["resource_id"]?>" value="<?php echo $resource["link"]?>" readonly>
                        <span class="material-icons" onclick="delete_resource(this.parentNode)">remove</span>
                    </span>
                <?php endforeach; ?>
                    <div id="add-resource">
                        <select id="select-resource">
                        <?php
                            $filenames = get_challenge_filenames($conn, $challenge_id);
                            foreach ($filenames as $filename)
                                echo "<option value='".$filename."'>".$filename."</option>";
                        ?>
                        </select>
                        <span class="material-icons" onclick="add_resource()">add</span>
                    </div>
                </div>
                <input type="submit" name="submit" value="Edit challenge">
            </form>
        <?php } else { ?>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <select name="challenge_name">
                <?php
                    $rows = get_challenge_names($conn);
                    foreach ($rows as $row)
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
            const hint_inputs = parent_node.querySelectorAll("input");

            if(hint_inputs[0].name.includes("add")) {
                parent_node.remove();
                return;
            }

            const delete_name = hint_inputs[0].name.replace("edit_hint_description_", "delete_hint_");

            const delete_hint = document.createElement("input");
            delete_hint.setAttribute("name", delete_name);
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
            const resource_input = parent_node.querySelector("input");

            var option = document.createElement("option");
            option.value = resource_input.value;
            option.innerHTML = resource_input.value;

            const select_resource = document.getElementById("select-resource");
            select_resource.appendChild(option);

            if(resource_input.name.includes("add")) {
                parent_node.remove();
                return;
            }

            const delete_name = resource_input.name.replace("edit_resource_", "delete_resource_");

            const delete_resource = document.createElement("input");
            delete_resource.setAttribute("name", delete_name);
            delete_resource.style.display = "none";

            parent_node.parentNode.appendChild(delete_resource);
            parent_node.remove();
        }

    </script>
</body>
</html>