<?php

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

function get_url_base() {
    return $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
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

function get_challenges_on_server($conn) {
    $db_challenge_list = array();
    foreach (get_challenge_list($conn) as $item) {
        array_push($db_challenge_list, array_values($item)[0]);
    }

    $base_dir = __DIR__."/../challenges";
    $challenge_on_server = array();
    foreach (array_diff(scandir($base_dir), array('.', '..')) as $dir) {
        $challenges = scandir($base_dir."/".$dir);
        $challenges = array_diff($challenges, array('.', '..'));
        foreach ($challenges as $item) 
            if(is_dir($base_dir."/".$dir."/".$item)) 
                array_push($challenge_on_server, $item);
    }

    return array_diff($challenge_on_server, $db_challenge_list);
}

function get_challenge_category_on_server($challenge_name) {
    $base_dir = __DIR__."/../challenges";
    foreach (array_diff(scandir($base_dir), array('.', '..')) as $dir) {
        $challenges = scandir($base_dir."/".$dir);
        foreach ($challenges as $item) 
            if($item == $challenge_name) return $dir; 
    }

    return false;
}

function get_challenge_flag_on_server($challenge_name) {
    $base_dir = __DIR__."/../challenges";
    foreach (array_diff(scandir($base_dir), array('.', '..')) as $dir) {
        $challenges = scandir($base_dir."/".$dir);
        foreach ($challenges as $item) 
            if($item == $challenge_name) {
                if(!file_exists($base_dir."/".$dir."/".$challenge_name."/flag.txt")) return false;
                return file_get_contents($base_dir."/".$dir."/".$challenge_name."/flag.txt");
            } 
    }

    return false;
}

function get_challenge_filenames_on_server($challenge_name) {
    $base_dir = __DIR__."/../challenges";
    foreach (array_diff(scandir($base_dir), array('.', '..')) as $dir) {
        $challenges = scandir($base_dir."/".$dir);
        foreach ($challenges as $item) 
            if($item == $challenge_name) return array_diff(scandir($base_dir."/".$dir."/".$challenge_name), array('.', '..')); 
    }

    return false;
}

function get_challenge_filenames($conn, $challenge_id) {
    $challenge_data = get_challenge_data($conn, $challenge_id);
   
    $resources = scandir(__DIR__."/../challenges/".$challenge_data["category"]."/".$challenge_data["challenge_name"]);
    $resources = array_values(array_diff($resources, array('.', '..')));

    return $resources;
}

function get_challenge_data($conn, $challenge_id) {
    $query = "SELECT challenge_name, flag, description, points, type, category FROM CTF_challenge WHERE challenge_id = ?";
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

function get_challenge_resources($conn, $challenge_id) {
    $query = "SELECT resource_id, link FROM CTF_resource WHERE challenge_id = ?";
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
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    if(!$rows) return false;
    return $rows;
}

function add_challenge($conn, $challenge_name, $flag, $description, $points, $type, $category) {
    $query = "INSERT INTO CTF_challenge (challenge_name, flag, description, points, type, category) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiss", $challenge_name, $flag, $description, $points, $type, $category);
    
    var_dump($challenge_name, $description, $points, $type, $category);
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

function add_challenge_resource($conn, $challenge_id, $resource_link) {
    $query = "INSERT INTO CTF_resource (challenge_id, link) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $challenge_id, $resource_link);

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

function edit_challenge_data($conn, $challenge_id, $description, $points, $type) {
    $query = "UPDATE CTF_challenge SET description = ?, points = ?, type = ? WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sisi", $description, $points, $type, $challenge_id);
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

?>