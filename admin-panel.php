<?php 
require "inc/init.php";

if(!isset($_SESSION["user_id"]) || get_user_role($conn, $_SESSION["user_id"]) != 'A') {
    $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
    exit(header("Location: $redirect"));
}

if (isset($_POST["action"]) && isset($_POST["challenge_name"]) && !empty($_POST["challenge_name"])) {
    $challenge_id = get_challenge_id($conn, $_POST["challenge_name"]);
    $success = true;
    switch ($_POST["action"]) {
        case "add":
            $challenge_id = add_challenge($conn, $_POST["challenge_name"], $_POST["flag"], $_POST["description"], $_POST["service"], $_POST["type"], $_POST["category"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"]);
            if (!$challenge_id){
                $success = false;
                break;
            } 

            if(isset($_POST["add_hint_description"]))
                for($i = 0; $i < count($_POST["add_hint_description"]); $i++)
                    if (!add_hint($conn, $challenge_id, $_POST["add_hint_description"][$i], $_POST["add_hint_cost"][$i])) $success = false;

            if(isset($_POST["add_resource"]))
                foreach ($_POST["add_resource"] as $resource_filename)
                    if (!add_challenge_resource($conn, $challenge_id, $resource_filename)) $success = false;

            if (!$success) exit(header("Refresh:0"));

            break;

        case "delete":
            if (!delete_challenge($conn, $challenge_id)) $success = false;
            break;

        case "edit":
            if (!edit_challenge_data($conn, $challenge_id, $_POST["description"], $_POST["service"], $_POST["type"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"])) $success = false;

            if(isset($_POST["delete_hint"]))
                foreach ($_POST["delete_hint"] as $hint_id)
                    if (!delete_hint($conn, $hint_id)) $success = false;

            if(isset($_POST["delete_resource"]))
                foreach ($_POST["delete_resource"] as $resource_id)
                    if (!delete_challenge_resource($conn, $resource_id)) $success = false;

            if(isset($_POST["add_hint_description"]))
                for($i = 0; $i < count($_POST["add_hint_description"]); $i++)
                    if (!add_hint($conn, $challenge_id, $_POST["add_hint_description"][$i], $_POST["add_hint_cost"][$i])) $success = false;

            if(isset($_POST["add_resource"]))
                foreach ($_POST["add_resource"] as $resource_filename)
                    if (!add_challenge_resource($conn, $challenge_id, $resource_filename)) $success = false;

            if(isset($_POST["edit_hint_id"]))
                for($i = 0; $i < count($_POST["edit_hint_id"]); $i++)
                    if (!edit_hint($conn, $_POST["edit_hint_id"][$i], $_POST["edit_hint_description"][$i], $_POST["edit_hint_cost"][$i])) $success = false;

            if (!$success) exit(header("Refresh:0"));

            break;
        }
    if ($success) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
}

$title = "CTF h4ckus4t1";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="admin-panel">
    <?php if (!isset($_GET["action"])) { ?>
        <form method="GET" class="generic-form">
            <button type="submit" name="action" value="add" class="generic-form__button no-margin">Add challenge</button><br>
            <button type="submit" name="action" value="edit" class="generic-form__button">Edit challenge</button><br>
            <button type="submit" name="action" value="delete" class="generic-form__button">Delete challenge</button>
        </form>
    <?php } elseif (!isset($_GET["challenge_name"])) { ?>
        <?php if($_GET["action"] == "delete") : ?>
            <form method="POST" class="generic-form">
        <?php else : ?>
            <form method="GET" class="generic-form">
        <?php endif; ?>
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
                    <button type="submit" name="action" value="add" class="generic-form__button">Add challenge</button>
                <?php elseif($_GET["action"] == "edit") : ?>
                    <button type="submit" name="action" value="edit" class="generic-form__button">Edit challenge</button>
                <?php elseif($_GET["action"] == "delete") : ?>
                    <button type="submit" name="action" value="delete" class="generic-form__button">Delete challenge</button>
                <?php endif; ?>
            </form>
    <?php } elseif ($_GET["action"] == "add") {
        $challenge_name = $_GET["challenge_name"];
        $category = get_local_challenge_category($challenge_name);
        $flag = get_local_challenge_flag($challenge_name);
        if (is_challenge_name_used($conn, $challenge_name) || !$category || !$flag ) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
    ?>
        <form method="POST" class="generic-form" style="min-width: 40%;">
            <div class="generic-form__input-box">
                <input type="text" name="challenge_name" placeholder=" " value="<?php echo $challenge_name; ?>" readonly>
                <label>Challenge Name</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="category"  placeholder=" " value="<?php echo $category; ?>" readonly>
                <label>Category</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="flag" placeholder=" " value="<?php echo $flag; ?>" readonly>
                <label>Flag</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="description" placeholder=" " maxlength="1024" required>
                <label>Description</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="service" placeholder=" " maxlength="256">
                <label>Service</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="initial_points" placeholder=" " required>
                <label>Initial Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="minimum_points" placeholder=" " required>
                <label>Minimum Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="points_decay" placeholder=" " required>
                <label>Points Decay</label>
            </div>
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Challenge Type</h3>
                <select name="type" required>
                    <option value="T">Training</option>
                    <option value="O">Official</option>
                    <option value="I">Inactive</option>
                </select>
            </div>
            <button type="button" class="generic-form__button" id="add-hint" onclick="add_hint()">Add Hint</button>
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Resources</h3>
                <div id="add-resource">
                    <select id="select-resource">
                    <?php
                        $filenames = get_local_challenge_resources($challenge_name);
                        foreach ($filenames as $filename)
                            echo "<option value='".$filename."'>".$filename."</option>";
                    ?>
                    </select>
                    <button type="button" class="generic-form__button" onclick="add_resource()">Add Resource</button>
                </div>
            </div>
            <button type="submit" name="action" value="add" class="generic-form__button">Add challenge</button>
        </form>
    <?php } elseif ($_GET["action"] == "edit") {
        $challenge_name = $_GET["challenge_name"];
        $challenge_id = get_challenge_id($conn, $challenge_name);
        if(!$challenge_id) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));

        $challenge_data = get_challenge_data($conn, $challenge_id);
        $hints = get_hints($conn, $challenge_id);
        $resources = get_db_challenge_resources($conn, $challenge_id);
    ?>
        <form method="POST" class="generic-form" style="min-width: 40%;">
            <div class="generic-form__input-box">
                <input type="text" name="challenge_name" placeholder=" " value="<?php echo $challenge_name; ?>" readonly>
                <label>Challenge Name</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="category" placeholder=" " value="<?php echo $challenge_data["category"]; ?>" readonly>
                <label>Category</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="flag" placeholder=" " value="<?php echo $challenge_data["flag"]; ?>" readonly>
                <label>Flag</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="description" placeholder=" " value="<?php echo $challenge_data["description"]; ?>" maxlength="1024" required>
                <label>Description</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="service" placeholder=" " value="<?php echo $challenge_data["service"]; ?>" maxlength="256">
                <label>Service</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="initial_points" placeholder=" " value="<?php echo $challenge_data["initial_points"]; ?>" required>
                <label>Initial Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="minimum_points" placeholder=" " value="<?php echo $challenge_data["minimum_points"]; ?>" required>
                <label>Minimum Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="points_decay" placeholder=" " value="<?php echo $challenge_data["points_decay"]; ?>" required>
                <label>Points Decay</label>
            </div>
            
      
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Challenge Type</h3>
                <select name="type" required>
                    <option value="T" <?php if($challenge_data["type"] == "T") echo "selected=\"selected\""?>>Training</option>
                    <option value="O" <?php if($challenge_data["type"] == "O") echo "selected=\"selected\""?>>Official</option>
                    <option value="I" <?php if($challenge_data["type"] == "I") echo "selected=\"selected\""?>>Inactive</option>
                </select>
            </div>
            <?php if($hints) foreach($hints as $hint) : ?>
                <div class="challenge-hint generic-form__box">
                    <h3 style="margin-top: 0;">Hint</h3>
                    <input type="hidden" name="edit_hint_id[]" placeholder=" " value="<?php echo $hint["hint_id"]?>" required>
                    <div class="generic-form__input-box">
                        <input type="text" name="edit_hint_description[]" value="<?php echo $hint["description"]?>" required>
                        <label>Hint Description</label>
                    </div>
                    <div class="generic-form__input-box">
                        <input type="number" min="0" name="edit_hint_cost[]" placeholder=" " value="<?php echo $hint["cost"]?>" required>
                        <label>Hint Cost</label>
                    </div>
                    <button type="button" class="generic-form__button no-margin" onclick="delete_hint(this.parentNode)">Remove Hint</button>
                </div>
            <?php endforeach; ?>
            <button type="button" class="generic-form__button" id="add-hint" onclick="add_hint()">Add Hint</button>
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Resources</h3>
            <?php if($resources) foreach($resources as $resource) : ?>
                <div class="challenge-resource generic-form__box" style="margin: 10px 10px 30px 10px;">
                    <div class="generic-form__input-box">
                        <input type="text" name="edit_resource_filename[]" placeholder=" " value="<?php echo $resource["filename"]?>" readonly>
                        <label>Filename</label>
                    </div>
                    <input type="hidden" name="edit_resource_id[]" value="<?php echo $resource["resource_id"]?>">
                    <button type="button" class="generic-form__button no-margin" onclick="delete_resource(this.parentNode)">Remove Resource</button>
                </div>
            <?php endforeach; ?>
                <div id="add-resource">
                    <select id="select-resource">
                    <?php
                        $filenames = get_local_challenge_resources($challenge_name);
                        foreach ($filenames as $filename)
                            echo "<option value='".$filename."'>".$filename."</option>";
                    ?>
                    </select>
                    <button type="button" class="generic-form__button" onclick="add_resource()">Add Resource</button>
                </div>
            </div>
            <button type="submit" name="action" value="edit" class="generic-form__button">Edit challenge</button>            
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
            const elem = document.createElement("div");
            elem.classList.add("challenge-hint");
            elem.classList.add("generic-form__box");

            elem.innerHTML = `<h3 style="margin-top: 0;">Hint</h3>
                <div class="generic-form__input-box">
                    <input type="text" placeholder=" " name="add_hint_description[]" required>
                    <label>Hint Description</label>
                </div>
                <div class="generic-form__input-box">
                    <input type="number" min="0" name="add_hint_cost[]" placeholder=" " required>
                    <label>Hint Cost</label>
                </div>
                <button type="button" class="generic-form__button no-margin" onclick="delete_hint(this.parentNode)">Remove Hint</button>`;

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

            const elem = document.createElement("div");
            elem.classList.add("challenge-resource");
            elem.classList.add("generic-form__box");
            elem.style.margin = "10px 10px 30px 10px";

            elem.innerHTML = `
                <div class="generic-form__input-box">
                    <input type="text" name="add_resource[]" placeholder=" " value="`+select_resource.value+`" readonly>
                    <label>Filename</label>
                </div>
                <button type="button" class="generic-form__button no-margin" onclick="delete_resource(this.parentNode)">Remove Resource</button>`;

            select_resource.removeChild(select_resource.options[select_resource.selectedIndex]);

            const target = document.getElementById("add-resource");

            target.parentNode.insertBefore(elem, target);
        }

        function delete_resource(parent_node) {
            const inputs = parent_node.querySelectorAll("input");

            var option = document.createElement("option");
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