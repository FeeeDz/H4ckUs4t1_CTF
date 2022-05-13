<?php

$site_directory = "~quintaa2122/informatica/CTF_h4ckus4t1";
$private_dir = "/home/quintaa2122/informatica/CTF_h4ckus4t1_private";

$hash_options = [
    'cost' => 10,
];

function db_connect() {
    require __DIR__."/db-config.php";

    if ($conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_servername)) 
        return $conn;
    return false;
}

function check_credentials($conn, $email, $password) {
    $query = "SELECT user_id, password_hash, role FROM CTF_user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);

    if(!$stmt->execute()) return false;
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) return $row;
    return false;
}

function login($conn, $email, $password) {
    if ($row = check_credentials($conn, $email, $password)) {
        $_SESSION['user_id'] = $row["user_id"];
        $_SESSION['role'] = $row['role'];
        return true;
    }
    return false;
}

function logout() {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    
    return true;
}

function register_user($conn, $username, $email, $password) {
    if(strlen($username) < 3 || strlen($username) > 16) return false;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    if(strlen($password) < 8 || strlen($password) > 128) return false;

    global $hash_options;

    $role = 'U';
    $password_hash = password_hash($password, PASSWORD_DEFAULT, $hash_options);
    
    $query = "INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
        VALUES (?, ?, ?, NOW(), NOW(), ?, NULL)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $password_hash, $email, $role);

    if (!$stmt->execute()) return false;
    return true;
}

function get_username_from_id($conn, $user_id) {
    $query = "SELECT username FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return $row['username'];
}

function get_team_token($conn, $team_name) {
    $query = "SELECT token FROM CTF_team WHERE team_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $team_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row['token'];
}

function get_user_team_name($conn, $user_id) {
    $team_id = get_user_team_id($conn, $user_id);

    $query = "SELECT team_name FROM CTF_team WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row['team_name'];
}

function count_team_members($conn, $team_id) {
    $query = "SELECT COUNT(username) AS members FROM CTF_user WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row['members'];
}

function get_user_team_id($conn, $user_id) {
    $query = "SELECT team_id FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row['team_id'];
}

function get_team_id($conn, $token) {
    $query = "SELECT team_id FROM CTF_team WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row['team_id'];
}

function register_team($conn, $team_name) {
    if(strlen($team_name) < 3 || strlen($team_name) > 32) return false;

    do {
        $token = bin2hex(random_bytes(16));
        
        $query = "SELECT team_id FROM CTF_team WHERE token = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    } while($row);

    $query = "INSERT INTO CTF_team (team_name, token, registration_date)
    VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $team_name, $token);
    if(!$stmt->execute()) return false;

    return $token;
}

function join_team($conn, $user_id, $token) {
    if(get_user_team_id($conn, $user_id)) return false;
    $team_id = get_team_id($conn, $token);

    if(count_team_members($conn, $team_id) > 3) return false;

    $query = "UPDATE CTF_user SET team_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $team_id, $user_id);
    if(!$stmt->execute()) return false;

    return true;
}

function quit_team($conn, $user_id) {
    $team_id = get_user_team_id($conn, $user_id);
    
    $query = "UPDATE CTF_user SET team_id = NULL WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    if(count_team_members($conn, $team_id) == 0) {
        $query = "DELETE FROM CTF_team WHERE team_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $team_id);
        $stmt->execute();
    }
    
    return true;
}

function get_base_url() {
    return $_SERVER['HTTP_HOST'];
}

function redirect_if_logged() {
    if(isset($_SESSION["user_id"])) {
        $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
        header("Location: $redirect");
    }
}

function redirect_if_not_admin() {
    if($_SESSION["role"] != 'A') {
        $redirect = isset($_GET["redirect"]) ? $_GET["redirect"] : "index.php";
        header("Location: $redirect");
    }
}

function get_challenge_id($conn, $challenge_name) {
    $query = "SELECT challenge_id FROM CTF_challenge WHERE challenge_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $challenge_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row["challenge_id"];
}

function get_challenge_list($conn) {
    $query = "SELECT challenge_name FROM CTF_challenge";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_db_missing_challenges($conn) {
    global $private_dir;
    $challenges_dir = $private_dir."/challenges";

    $db_challenge_list = array();
    foreach (get_challenge_list($conn) as $item) {
        array_push($db_challenge_list, array_values($item)[0]);
    }

    $challenge_on_server = array();
    foreach (array_diff(scandir($challenges_dir), array('.', '..')) as $category) {
        $challenges = scandir($challenges_dir."/".$category);
        $challenges = array_diff($challenges, array('.', '..'));
        foreach ($challenges as $challenge) 
            if(is_dir($challenges_dir."/".$category."/".$challenge)) array_push($challenge_on_server, $challenge);
    }

    return array_diff($challenge_on_server, $db_challenge_list);
}

function get_local_challenge_category($challenge_name) {
    global $private_dir;
    $challenges_dir = $private_dir."/challenges";

    foreach (array_diff(scandir($challenges_dir), array('.', '..')) as $category) {
        $challenges = scandir($challenges_dir."/".$category);
        foreach ($challenges as $challenge) 
            if($challenge == $challenge_name) return $category; 
    }

    return false;
}

function get_local_challenge_flag($challenge_name) {
    global $private_dir;
    
    $category = get_local_challenge_category($challenge_name);
    $challenge_dir = "$private_dir/challenges/$category/$challenge_name";

    if(!file_exists("$challenge_dir/flag.txt")) return false;
    return file_get_contents("$challenge_dir/flag.txt");

    return false;
}

function get_local_challenge_resources($challenge_name) {
    global $private_dir;

    $category = get_local_challenge_category($challenge_name);
    $challenge_dir = "$private_dir/challenges/$category/$challenge_name";

    if(!is_dir($challenge_dir)) return false;
    return array_diff(scandir($challenge_dir), array('.', '..')); 
}

function get_challenge_resource_path($challenge_name, $filename) {
    global $private_dir;

    $category = get_local_challenge_category($challenge_name);
    $challenge_dir = "$private_dir/challenges/$category/$challenge_name";

    if (file_exists("$challenge_dir/$filename")) return "$challenge_dir/$filename"; 

    return false;
}

function is_challenge_resource ($conn, $challenge_id, $filename) {
    $query = "SELECT resource_id FROM CTF_resource WHERE challenge_id = ? AND filename = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $challenge_id, $filename);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return true;
}

function get_challenge_data($conn, $challenge_id) {
    $query = "SELECT challenge_name, flag, description, type, category, initial_points, minimum_points, points_decay FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row;
}

function get_challenge_hints($conn, $challenge_id) {
    $query = "SELECT hint_id, description, cost FROM CTF_hint WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    if(!$rows) return false;
    return $rows;
}

function is_hint_unlocked($conn, $hint_id, $user_id) {
    $query = "SELECT hint_id FROM CTF_unlock_hint WHERE hint_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $hint_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_assoc();
    
    if(!$rows) return false;
    return true;
}

function get_db_challenge_resources($conn, $challenge_id) {
    $query = "SELECT resource_id, filename FROM CTF_resource WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    if(!$rows) return false;
    return $rows;
}

function get_challenge_categories($conn) {
    $query = "SELECT category FROM CTF_challenge_category";
    $result = $conn->query($query);
    $rows = $result->fetch_all();

    if(!$rows) return false;

    $categories = array();
    foreach ($rows as $item) array_push($categories, array_values($item)[0]);
    return $categories;
}

function get_challenges_from_category($conn, $category, $type) {
    if ($type) $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category' AND type = '$type'";
    else $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category'";
    $result = $conn->query($query);
    $rows = $result->fetch_all();

    if(!$rows) return false;

    $challenges = array();
    foreach ($rows as $item) array_push($challenges, array_values($item)[0]);
    return $challenges;
}

function compute_challenge_points($conn, $challenge_id) {
    $query = "SELECT initial_points, minimum_points, points_decay FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if(!$row) return false;

    $initial = $row["initial_points"];
    $minimum = $row["minimum_points"];
    $decay = $row["points_decay"];
    $solve_count = get_challenge_solves($conn, $challenge_id);

    return ceil(((($minimum - $initial)/($decay*2)) * ($solve_count*2)) + $initial);
}

function get_challenge_solves($conn, $challenge_id) {
    $query = "SELECT COUNT(challenge_id) AS solve_count FROM CTF_submit WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if(!$row) return false;

    return $row["solve_count"];
}

function submit_flag($conn, $challenge_id, $flag) {
    $query = "SELECT points FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row["points"];
}

function add_challenge($conn, $challenge_name, $flag, $description, $type, $category, $initial_points, $minimum_points, $points_decay) {
    if($initial_points <= $minimum_points) return false;

    $query = "INSERT INTO CTF_challenge (challenge_name, flag, description, type, category, initial_points, minimum_points, points_decay) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssiii", $challenge_name, $flag, $description, $type, $category, $initial_points, $minimum_points, $points_decay);
    
    if (!$stmt->execute()) return false;
    return $conn->insert_id;
}

function add_challenge_hint($conn, $challenge_id, $hint_description, $hint_cost) {
    $query = "INSERT INTO CTF_hint (challenge_id, description, cost) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isi", $challenge_id, $hint_description, $hint_cost);

    if (!$stmt->execute()) return false;
    return true;
}

function add_challenge_resource($conn, $challenge_id, $filename) {
    $query = "INSERT INTO CTF_resource (challenge_id, filename) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $challenge_id, $filename);

    if (!$stmt->execute()) return false;
    return true;
}

function delete_challenge($conn, $challenge_id) {
    $query = "DELETE FROM CTF_challenge WHERE challenge_id = $challenge_id";
    if (!$conn->query($query)) return false;

    $query = "DELETE FROM CTF_hint WHERE challenge_id = $challenge_id";
    if (!$conn->query($query)) return false;

    $query = "DELETE FROM CTF_resource WHERE challenge_id = $challenge_id";
    if (!$conn->query($query)) return false;

    return true;
}

function delete_challenge_hint($conn, $hint_id) {
    $query = "DELETE FROM CTF_hint WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hint_id);

    if (!$stmt->execute()) return false;
    return true;
}

function delete_challenge_resource($conn, $resource_id) {
    $query = "DELETE FROM CTF_resource WHERE resource_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $resource_id);

    if (!$stmt->execute()) return false;
    return true;
}

function edit_challenge_data($conn, $challenge_id, $description, $type, $initial_points, $minimum_points, $points_decay) {
    if($initial_points <= $minimum_points) return false;
    
    $query = "UPDATE CTF_challenge SET description = ?, type = ?, initial_points = ?, minimum_points = ?, points_decay = ? WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiiii", $description, $type, $initial_points, $minimum_points, $points_decay, $challenge_id);
    if(!$stmt->execute()) return false;

    return true;
}

function edit_challenge_hint($conn, $hint_id, $hint_description, $hint_cost) {
    $query = "UPDATE CTF_hint SET description = ?, cost = ? WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $hint_description, $hint_cost, $hint_id);
    if(!$stmt->execute()) return false;

    return true;
}

function is_challenge_name_used($conn, $challenge_name) {
    $query = "SELECT challenge_name FROM CTF_challenge WHERE challenge_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $challenge_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return true;
}

function get_upcoming_event_start_date($conn) {
    $query = "SELECT MIN(start_date) AS start_date FROM CTF_event WHERE start_date > NOW()";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["start_date"];
}

function get_current_event_end_date($conn) {
    $query = "SELECT end_date FROM CTF_event WHERE start_date < NOW() AND end_date > NOW()";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["end_date"];
}

function is_event_started($conn) {
    $query = "SELECT event_id FROM CTF_event WHERE start_date < NOW() AND end_date > NOW()";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return true;
}

?>