<?php 
require_once "inc/init.php";

if (get_user_role($conn, $_SESSION["user_id"]) != 'A') exit(header("Location: index.php"));

if (isset($_POST["submit"])) {
    $success = true;
    switch ($_GET["action"]) {
        case "add_challenge":
            $challenge_id = add_challenge($conn, $_POST["challenge_name"], $_POST["flag"], $_POST["description"], $_POST["service"], $_POST["type"], $_POST["category"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"], $_POST["author"]);
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

            break;

        case "edit_challenge":
            $challenge_id = get_challenge_id($conn, $_POST["challenge_name"]);

            if (!edit_challenge_data($conn, $challenge_id, $_POST["description"], $_POST["service"], $_POST["type"], $_POST["initial_points"], $_POST["minimum_points"], $_POST["points_decay"], $_POST["author"])) $success = false;

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

            break;

        
        case "delete_challenge":
            $challenge_id = get_challenge_id($conn, $_POST["challenge_name"]);

            if (!delete_challenge($conn, $challenge_id)) $success = false;
            break;

        // case "reset_solves_hints":
        //     if (!check_credentials($conn, get_user_email($conn, $_SESSION["user_id"]), $_POST["password"])) {
        //         $success = false;
        //         break;
        //     }

        //     reset_ctf_solves($conn);
        //     reset_ctf_unlocked_hints($conn);

        //     break;

        case "add_event":
            if (!add_event($conn, $_POST["start_date"], $_POST["end_date"])) $success = false;
            break;

        case "edit_event":
            if (!edit_event($conn, $_GET["event_id"], $_POST["start_date"], $_POST["end_date"])) $success = false;
            break;

        case "delete_event":
            if (!delete_event($conn, $_POST["event_id"])) $success = false;
            break;

        }
    if (!$success) exit(header("Refresh:0"));
    exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
}

$title = "Admin Panel - H4ckUs4t1 CTF";
require "inc/head.php";
?>
<body>
    <nav id="nav">
        <?php require "inc/navbar.php"; ?>
    </nav>
    <div id="main" class="<?php if (strpos($_GET["action"], "view") === false) echo "admin-panel"; else echo "leaderboard"; ?>">
    <?php if (!isset($_GET["action"])) { ?>
        <form method="GET" class="generic-form">
            <button type="submit" name="action" value="add_challenge" class="generic-form__button no-margin">Add challenge</button><br>
            <button type="submit" name="action" value="edit_challenge" class="generic-form__button">Edit challenge</button><br>
            <button type="submit" name="action" value="delete_challenge" class="generic-form__button">Delete challenge</button><br>
            <button type="submit" name="action" value="add_event" class="generic-form__button">Add event</button><br>
            <button type="submit" name="action" value="edit_event" class="generic-form__button">Edit event</button><br>
            <button type="submit" name="action" value="delete_event" class="generic-form__button">Delete event</button><br>
            <!-- <button type="submit" name="action" value="reset_solves_hints" class="generic-form__button">Reset CTF solves and hints</button><br> -->
            <button type="submit" name="action" value="view_users" class="generic-form__button">View Users</button><br>
            <button type="submit" name="action" value="view_teams" class="generic-form__button">View Teams</button>
        </form>
    <?php } elseif ($_GET["action"] == "add_challenge") {
        if (!isset($_GET["challenge_name"])) { ?>
            <form method="GET" class="generic-form">
                <select name="challenge_name">
                <?php
                    $challenges = get_db_missing_challenges($conn);
                    if (!$challenges) {
                        echo "<option value=''></option>";
                    } else {
                        foreach ($challenges as $challenge)
                            echo "<option value='".$challenge."'>".$challenge."</option>";
                    }
                ?>
                </select>
                <button type="submit" name="action" value="add_challenge" class="generic-form__button">Add challenge</button>
            </form>
        <?php } else {
            $challenge_name = $_GET["challenge_name"];
            $category = get_local_challenge_category($challenge_name);
            $flag = get_local_challenge_flag($challenge_name);
            if (is_challenge_name_used($conn, $challenge_name) || !$category || !$flag ) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));
        ?>
        <form method="POST" class="generic-form" style="min-width: 40%;">
            <h2 class="title">Add challenge</h2>
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
                <textarea type="text" name="description" placeholder=" " maxlength="1024" rows="5"></textarea>
                <label>Description</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="service" placeholder=" " maxlength="256">
                <label>Service</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="initial_points" class="initial_points" placeholder=" " required>
                <label>Initial Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="minimum_points" class="minimum_points" placeholder=" " required>
                <label>Minimum Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="points_decay" placeholder=" " min="1" required>
                <label>Points Decay</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="author" placeholder=" " maxlength="64" pattern="[ -~]+">
                <label>Author</label>
            </div>
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Challenge Type</h3>
                <select name="type" required>
                    <option value="O">Official</option>
                    <option value="T">Training</option>
                    <option value="I">Inactive</option>
                </select>
            </div>
            <div class="generic-form__box">
                <h3 style="margin: 0;">Hints</h3>
                <button type="button" class="generic-form__button no" id="add-hint" onclick="add_hint()">Add Hint</button>
            </div>
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
            <button type="submit" name="submit" class="generic-form__button">Add challenge</button>
        </form>
        <?php } ?>
    <?php } elseif ($_GET["action"] == "edit_challenge") {
        if (!isset($_GET["challenge_name"])) { ?>
            <form method="GET" class="generic-form">
                <select name="challenge_name">
                <?php
                    $rows = get_challenge_list($conn);
                    foreach ($rows as $row)
                        echo "<option value='".$row["challenge_name"]."'>".$row["challenge_name"]."</option>";
                ?>
                </select>
                <button type="submit" name="action" value="edit_challenge" class="generic-form__button">Edit challenge</button>
            </form>
        <?php } else {
            $challenge_name = $_GET["challenge_name"];
            $challenge_id = get_challenge_id($conn, $challenge_name);
            if(!$challenge_id) exit(header("Location: ".basename($_SERVER['PHP_SELF'])));

            $challenge_data = get_challenge_data($conn, $challenge_id);
            $hints = get_hints($conn, $challenge_id);
            $resources = get_db_challenge_resources($conn, $challenge_id);
        ?>
        <form method="POST" class="generic-form" style="min-width: 40%;">
            <h2 class="title">Edit challenge</h2>
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
                <textarea type="text" name="description" placeholder=" " maxlength="1024" rows="5"><?php echo $challenge_data["description"]; ?></textarea>
                <label>Description</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="service" placeholder=" " value="<?php echo $challenge_data["service"]; ?>" maxlength="256">
                <label>Service</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="initial_points" class="initial_points" placeholder=" " value="<?php echo $challenge_data["initial_points"]; ?>" required>
                <label>Initial Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="minimum_points" class="minimum_points" placeholder=" " value="<?php echo $challenge_data["minimum_points"]; ?>" required>
                <label>Minimum Points</label>
            </div>
            <div class="generic-form__input-box">
                <input type="number" name="points_decay" placeholder=" " value="<?php echo $challenge_data["points_decay"]; ?>" min="1" required>
                <label>Points Decay</label>
            </div>
            <div class="generic-form__input-box">
                <input type="text" name="author" placeholder=" " value="<?php echo $challenge_data["author"]; ?>" maxlength="64" pattern="[ -~]+">
                <label>Author</label>
            </div>
            <div class="generic-form__box">
                <h3 style="margin-top: 0;">Challenge Type</h3>
                <select name="type" required>
                    <option value="O" <?php if($challenge_data["type"] == "O") echo "selected=\"selected\""?>>Official</option>
                    <option value="T" <?php if($challenge_data["type"] == "T") echo "selected=\"selected\""?>>Training</option>
                    <option value="I" <?php if($challenge_data["type"] == "I") echo "selected=\"selected\""?>>Inactive</option>
                </select>
            </div>

            <div class="generic-form__box">
                <h3 style="margin: 0;">Hints</h3>
                <?php if($hints) foreach($hints as $hint) : ?>
                    <div class="challenge-resource generic-form__box">
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
            </div>

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

            <button type="submit" name="submit" class="generic-form__button">Edit challenge</button>            
        </form>
        <?php } ?>
    <?php } elseif ($_GET["action"] == "delete_challenge") { ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Delete challenge</h2>
            <select name="challenge_name">
            <?php
                $rows = get_challenge_list($conn);
                foreach ($rows as $row)
                    echo "<option value='".$row["challenge_name"]."'>".$row["challenge_name"]."</option>";
            ?>
            </select>
            <button type="submit" name="submit" class="generic-form__button">Delete challenge</button>
        </form>
    <?php } elseif ($_GET["action"] == "add_event") {  ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Add event</h2>
            <div class="generic-form__input-box">
                <input type="datetime-local" name="start_date" value="<?php echo date('Y-m-d H:i'); ?>" placeholder=" " required>
                <label>Start Date</label>
            </div>
            <div class="generic-form__input-box">
                <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d H:i'); ?>" placeholder=" " required>
                <label>End Date</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Add event</button>
        </form>
    <?php } elseif ($_GET["action"] == "edit_event") {
        if (!isset($_GET["event_id"])) { ?>
           <form method="GET" class="generic-form">
                <h2 class="title">Edit event</h2>
                <select name="event_id">
                <?php
                    $rows = get_events($conn);
                    foreach ($rows as $row)
                        echo "<option value='" . $row["event_id"]."'>ID: " . $row["event_id"] . " (" . $row["start_date"] . ", " . $row["end_date"] . ")" . "</option>";
                ?>
                </select>
                <button type="submit" name="action" value="edit_event" class="generic-form__button">Edit event</button>
            </form>
        <?php } else { 
            $event_data = get_event_data($conn, $_GET["event_id"]);
        ?>
            <form method="POST" class="generic-form">
                <h2 class="title">Edit event</h2>
                <div class="generic-form__input-box">
                    <input type="datetime-local" name="start_date" value="<?php echo $event_data["start_date"]; ?>" placeholder=" " required>
                    <label>Start Date</label>
                </div>
                <div class="generic-form__input-box">
                    <input type="datetime-local" name="end_date" value="<?php echo $event_data["end_date"]; ?>" placeholder=" " required>
                    <label>End Date</label>
                </div>
                <button type="submit" name="submit" class="generic-form__button no-margin">Edit event</button>
            </form>
        <?php } ?>
    <?php } elseif ($_GET["action"] == "delete_event") { ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Edit event</h2>
            <select name="event_id">
            <?php
                $rows = get_events($conn);
                foreach ($rows as $row)
                    echo "<option value='" . $row["event_id"]."'>ID: " . $row["event_id"] . " (" . $row["start_date"] . ", " . $row["end_date"] . ")" . "</option>";
            ?>
            </select>
            <button type="submit" name="submit" class="generic-form__button">Delete event</button>
        </form>
    <!-- <?php //} elseif ($_GET["action"] == "reset_solves_hints") { ?>
        <form method="POST" class="generic-form">
            <h2 class="title">Reset CTF solves and hints</h2>
            <div class="generic-form__input-box">
                <input type="password" name="password" placeholder=" " autocomplete="current-password" required>
                <label>Password</label>
            </div>
            <button type="submit" name="submit" class="generic-form__button no-margin">Reset</button>
        </form> -->
    <?php } elseif ($_GET["action"] == "view_users") { ?>
        <table>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Team Name</th>
                <th>Email</th>
                <th>Score</th>
            </tr>
            <?php $users_data = get_users_data($conn);   
                foreach ($users_data as $index => $row): ?>
                <tr>
                    <td><?php echo $row["user_id"]; ?></td>
                    <td><a class="link" href="user.php?username=<?php echo $row["username"]; ?>"><?php echo $row["username"]; ?></a></td>
                    <td><a class="link" href="team.php?team_name=<?php echo $row["team_name"]; ?>"><?php echo $row["team_name"]; ?></a></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $row["score"]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php } elseif ($_GET["action"] == "view_teams") { ?>
        <table>
            <tr>
                <th>Team ID</th>
                <th>Team Name</th>
                <th>Score</th>
            </tr>
            <?php $teams_data = get_teams_data($conn);   
                foreach ($teams_data as $index => $row): ?>
                <tr>
                    <td><?php echo $row["team_id"];; ?></td>
                    <td><a class="link" href="team.php?team_name=<?php echo $row["team_name"]; ?>"><?php echo $row["team_name"]; ?></a></td>
                    <td><?php echo $row["score"]; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php } else exit(header("Location: ".basename($_SERVER['PHP_SELF']))); ?>
    </div>
    <div id="footer">
        <?php require "inc/footer.php"; ?>
    </div>
    <script>

        var initial_points, minimum_points, start_date, end_date;

        try {
            initial_points = document.querySelector(".initial_points");
            minimum_points = document.querySelector(".minimum_points");
            // start_date = document.querySelector(".start_date");
            // end_date = document.querySelector(".end_date");

            initial_points.onchange = check_points_input;
            minimum_points.onkeyup = check_points_input;
            // start_date.onchange = check_date_input;
            // end_date.onkeyup = check_date_input;
        }
        catch(err) {
            // do nothing
        }

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

            elem.innerHTML = `
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

        function check_points_input(){
            if(initial_points.value < minimum_points.value) 
                minimum_points.setCustomValidity("Minimum points must be greater than the initial one");
            else
                minimum_points.setCustomValidity('');
        }

        function check_date_input(){
            if(Date.parse(start_date.value) > Date.parse(end_date.value)) 
                minimum_points.setCustomValidity("End date must be greater than the start one");
            else
                minimum_points.setCustomValidity('');
        }

    </script>
</body>
</html>