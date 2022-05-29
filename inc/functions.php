<?php

// Server scuola
// $site_directory = "~quintaa2122/informatica/CTF_h4ckus4t1";
// $private_dir = "/home/quintaa2122/informatica/CTF_h4ckus4t1_private";

// Server privato
$site_directory = "";
$private_dir = "/var/www/CTF_h4ckus4t1_private";

$hash_options = [
    'cost' => 10,
];

function db_connect() {
    global $private_dir;
    require $private_dir."/config/db-config.php";

    if ($conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_servername)) 
        return $conn;
    return false;
}

function check_credentials($conn, $email, $password) {
    $query = "SELECT user_id, password_hash FROM CTF_user WHERE email = ?";
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
        return true;
    }
    return false;
}

function check_if_user_exists($conn, $user_id) {
    $query = "SELECT 1 FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if(!$stmt->execute()) return false;
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return true;
}

function check_if_team_exists($conn, $team_id) {
    $query = "SELECT 1 FROM CTF_team WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);

    if(!$stmt->execute()) return false;
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return true;
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
    if(strpos($username, ' ') !== false) return false;
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
    return $row["username"];
}

function get_user_id_from_username($conn, $username) {
    $query = "SELECT user_id FROM CTF_user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return $row["user_id"];
}

function get_user_email($conn, $user_id) {
    $query = "SELECT email FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return $row["email"];
}

function get_team_token($conn, $team_id) {
    $query = "SELECT token FROM CTF_team WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row["token"];
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
    return $row["team_name"];
}

function get_user_role($conn, $user_id) {
    $query = "SELECT role FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row["role"];
}

function count_team_members($conn, $team_id) {
    $query = "SELECT COUNT(username) AS members FROM CTF_user WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row["members"];
}

function get_user_team_id($conn, $user_id) {
    $query = "SELECT team_id FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row["team_id"];
}

function get_team_id_from_token($conn, $token) {
    $query = "SELECT team_id FROM CTF_team WHERE token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["team_id"];
}

function get_team_id_from_team_name($conn, $team_name) {
    $query = "SELECT team_id FROM CTF_team WHERE team_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $team_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["team_id"];
}

function get_team_name($conn, $team_id) {
    $query = "SELECT team_name FROM CTF_team WHERE team_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["team_name"];
}

function register_team($conn, $team_name) {
    if(strlen($team_name) < 3 || strlen($team_name) > 32) return false;
    if(strpos($team_name, ' ') !== false) return false;

    do {
        $token = bin2hex(random_bytes(16));
        
        $query = "SELECT 1 FROM CTF_team WHERE token = ?";
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
    $team_id = get_team_id_from_token($conn, $token);

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
    return $_SERVER["HTTP_HOST"];
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

    $challenges_on_server = array();
    foreach (array_diff(scandir($challenges_dir), array('.', '..')) as $category) {
        $challenges = scandir($challenges_dir."/".$category);
        $challenges = array_diff($challenges, array('.', '..'));
        foreach ($challenges as $challenge) 
            if(is_dir($challenges_dir."/".$category."/".$challenge)) array_push($challenges_on_server, $challenge);
    }

    return array_diff($challenges_on_server, $db_challenge_list);
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
    $query = "SELECT 1 FROM CTF_resource WHERE challenge_id = ? AND filename = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $challenge_id, $filename);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return true;
}

function get_events($conn) {
    $query = "SELECT event_id, start_date, end_date FROM CTF_event ORDER BY start_date";
    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_event_data($conn, $event_id) {
    $query = "SELECT event_id, start_date, end_date FROM CTF_event WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row;
}

function get_challenge_data($conn, $challenge_id) {
    $query = "SELECT challenge_name, flag, description, service, type, category, initial_points, minimum_points, points_decay FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row;
}

function get_hints($conn, $challenge_id) {
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
    $team_id = get_user_team_id($conn, $user_id);
    if (is_event_started($conn)) {
        if (!$team_id) return false;
        
        $query = "SELECT 1 FROM CTF_unlocked_hint WHERE hint_id = ? AND team_id = $team_id";
    }
    else {
        $query = "SELECT 1 FROM CTF_unlocked_hint WHERE hint_id = ? AND user_id = $user_id";
    }
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hint_id);
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
    if ($type) $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category' AND type = '$type' ORDER BY initial_points";
    else $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category'";
    $result = $conn->query($query);
    $rows = $result->fetch_all();

    if(!$rows) return false;

    $challenges = array();
    foreach ($rows as $item) array_push($challenges, array_values($item)[0]);
    return $challenges;
}

function add_challenge($conn, $challenge_name, $flag, $description, $service, $type, $category, $initial_points, $minimum_points, $points_decay) {
    if($initial_points < $minimum_points) return false;

    $query = "INSERT INTO CTF_challenge (challenge_name, flag, description, service, type, category, initial_points, minimum_points, points_decay) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssiii", $challenge_name, $flag, $description, $service, $type, $category, $initial_points, $minimum_points, $points_decay);
    
    if (!$stmt->execute()) return false;
    return $conn->insert_id;
}

function add_hint($conn, $challenge_id, $hint_description, $hint_cost) {
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

function add_event($conn, $start_date, $end_date) {
    if (strtotime($start_date) > strtotime($start_date)) return false;
    echo $start_date;
    var_dump(strtotime($start_date));

    $query = "INSERT INTO CTF_event (start_date, end_date) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);

    if (!$stmt->execute()) return false;
    return true;
}

function delete_challenge($conn, $challenge_id) {
    $query = "DELETE FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    if (!$stmt->execute()) return false;

    return true;
}

function delete_hint($conn, $hint_id) {
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

function delete_event($conn, $event_id) {
    $query = "DELETE FROM CTF_event WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);

    if (!$stmt->execute()) return false;
    return true;
}

function edit_challenge_data($conn, $challenge_id, $description, $service, $type, $initial_points, $minimum_points, $points_decay) {
    if($initial_points < $minimum_points) return false;
    if(get_challenge_type($conn, $challenge_id) != $type) {
        $challenge_data = get_challenge_data($conn, $challenge_id);
        $hints = get_hints($conn, $challenge_id);
        $resources = get_db_challenge_resources($conn, $challenge_id);
        
        delete_challenge($conn, $challenge_id);
        $challenge_id = add_challenge($conn, $challenge_data["challenge_name"], $challenge_data["flag"], $description, $service, $type, $challenge_data["category"], $initial_points, $minimum_points, $points_decay);
        foreach ($hints as $hint) {
            add_hint($conn, $challenge_id, $hint["description"], $hint["cost"]);
        }
        foreach ($resources as $resource) {
            add_challenge_resource($conn, $challenge_id, $resource["filename"]);
        }

        return true;
    }
    
    $query = "UPDATE CTF_challenge SET description = ?, service = ?, type = ?, initial_points = ?, minimum_points = ?, points_decay = ? WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiiii", $description, $service, $type, $initial_points, $minimum_points, $points_decay, $challenge_id);
    if(!$stmt->execute()) return false;

    return true;
}

function edit_hint($conn, $hint_id, $hint_description, $hint_cost) {
    $query = "UPDATE CTF_hint SET description = ?, cost = ? WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $hint_description, $hint_cost, $hint_id);
    if(!$stmt->execute()) return false;

    return true;
}

function edit_event($conn, $event_id, $start_date, $end_date) {
    if (strtotime($start_date) > strtotime($start_date)) return false;
    
    $query = "UPDATE CTF_event SET start_date = ?, end_date = ? WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $start_date, $end_date, $event_id);
    if(!$stmt->execute()) return false;

    return true;
}

function is_challenge_name_used($conn, $challenge_name) {
    $query = "SELECT 1 FROM CTF_challenge WHERE challenge_name = ?";
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

function get_current_event_id($conn) {
    $query = "SELECT event_id FROM CTF_event WHERE start_date < NOW() AND end_date > NOW()";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return NULL;
    return $row["event_id"];
}

function get_current_event_start_date($conn) {
    $query = "SELECT start_date FROM CTF_event WHERE start_date < NOW() AND end_date > NOW()";
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

function get_last_event_start_date($conn) {
    $query = "SELECT start_date FROM CTF_event WHERE start_date < NOW() ORDER BY start_date DESC";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["start_date"];
}

function get_last_event_end_date($conn) {
    $query = "SELECT end_date FROM CTF_event WHERE start_date < NOW() ORDER BY start_date DESC";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["end_date"];
}

function get_last_event_id($conn) {
    $query = "SELECT event_id FROM CTF_event WHERE start_date < NOW() ORDER BY start_date DESC";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["event_id"];
}

function is_event_started($conn) {
    $query = "SELECT 1 FROM CTF_event WHERE start_date < NOW() AND end_date > NOW()";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return true;
}

function get_hint_description($conn, $hint_id) {
    $query = "SELECT description FROM CTF_hint WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["description"];
}

function get_hint_cost($conn, $hint_id) {
    $query = "SELECT cost FROM CTF_hint WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["cost"];
}

function get_hint_challenge_id($conn, $hint_id) {
    $query = "SELECT challenge_id FROM CTF_hint WHERE hint_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hint_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["challenge_id"];
}

function get_challenge_type($conn, $challenge_id) {
    $query = "SELECT type FROM CTF_challenge WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["type"];
}

function get_team_score($conn, $team_id) {
    $query = "SELECT IFNULL( (SELECT SUM(points) 
        FROM CTF_submit 
        WHERE CTF_submit.team_id = $team_id AND CTF_submit.event_id = ".get_last_event_id($conn)."), 0)
        - 
        IFNULL( (SELECT SUM(cost)
        FROM CTF_unlocked_hint
        INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
        WHERE CTF_unlocked_hint.team_id = $team_id), 0) AS score";
    
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["score"];
}

function get_user_score($conn, $user_id) {
    $query = "SELECT IFNULL( (SELECT SUM(points) 
        FROM CTF_submit 
        WHERE team_id IS NULL 
        AND CTF_submit.user_id = $user_id), 0)
        - 
        IFNULL( (SELECT SUM(cost)
        FROM CTF_unlocked_hint
        INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
        WHERE team_id IS NULL AND CTF_unlocked_hint.user_id = $user_id), 0) AS score";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["score"];
}

function unlock_hint($conn, $hint_id, $user_id) {
    if (is_hint_unlocked($conn, $hint_id, $user_id)) return true;
    
    $challenge_type = get_challenge_type($conn, get_hint_challenge_id($conn, $hint_id));
    $team_id = get_user_team_id($conn, $user_id);
    if ($challenge_type == "O") {
        if (!$team_id) return false;
        if (get_hint_cost($conn, $hint_id) > get_team_score($conn, $team_id)) return false;

        $query = "INSERT INTO CTF_unlocked_hint (hint_id, user_id, team_id, unlock_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $hint_id, $user_id, $team_id);
    } else {
        if (get_hint_cost($conn, $hint_id) > get_user_score($conn, $user_id)) return false;

        $query = "INSERT INTO CTF_unlocked_hint (hint_id, user_id, unlock_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $hint_id, $user_id);
    }

    if(!$stmt->execute()) return false;
    return true;
}

function is_challenge_solved($conn, $challenge_id, $user_id) {
    $challenge_type = get_challenge_type($conn, $challenge_id);
    $team_id = get_user_team_id($conn, $user_id);

    if ($challenge_type == "O") {
        if (!$team_id) return false;

        $query = "SELECT 1 FROM CTF_submit WHERE challenge_id = ? AND team_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $challenge_id, $team_id);
    } else if($challenge_type == "T") {
        $query = "SELECT 1 FROM CTF_submit WHERE challenge_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $challenge_id, $user_id);
    } else return false;
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return true;
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
    $solves = get_challenge_solves($conn, $challenge_id);

    return ceil(((($minimum - $initial)/($decay*2)) * ($solves*2)) + $initial);
}

function get_challenge_solves($conn, $challenge_id) {
    $query = "SELECT COUNT(challenge_id) AS solves FROM CTF_submit WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $challenge_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["solves"];
}

function check_flag($conn, $challenge_id, $flag) {
    $query = "SELECT flag FROM CTF_challenge WHERE challenge_id = ? AND flag = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $challenge_id, $flag);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return true;
}

function submit_flag($conn, $challenge_id, $user_id, $flag) {
    if (!check_flag($conn, $challenge_id, $flag)) return false;
    if (is_challenge_solved($conn, $challenge_id, $user_id)) return false;
    
    $challenge_type = get_challenge_type($conn, $challenge_id);
    $team_id = get_user_team_id($conn, $user_id);
    $event_id = get_current_event_id($conn);
    $points = compute_challenge_points($conn, $challenge_id);

    if ($event_id) {
        if ($challenge_type != "O") return false;
        if (!$team_id) return false;

        $query = "INSERT INTO CTF_submit (user_id, team_id, challenge_id, event_id, points, submit_date) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiii", $user_id, $team_id, $challenge_id, $event_id, $points);
    } else {
        if ($challenge_type != "T") return false;

        $query = "INSERT INTO CTF_submit (user_id, challenge_id, points, submit_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $challenge_id, $points);
    }
    
    if (!$stmt->execute()) return false;
    return true;
}

function get_challenges_solves_and_points($conn, $user_id, $from_date) {
    $team_id = get_user_team_id($conn, $user_id);
    
    if (is_event_started($conn)) {
        if (!$team_id) return false;
        
        $type = "O";
        $query = "SELECT submit.challenge_id, challenge_name, COUNT(submit.challenge_id) AS solves, 
                IF (EXISTS (SELECT 1 FROM CTF_submit WHERE team_id = $team_id AND submit.event_id = ".get_current_event_id($conn)."), TRUE, FALSE) AS solved
            FROM CTF_submit AS submit
            INNER JOIN CTF_challenge ON submit.challenge_id = CTF_challenge.challenge_id
            WHERE type = '$type' AND submit.event_id = ".get_current_event_id($conn)." AND submit_date >= '$from_date'
            GROUP BY submit.challenge_id";
    }
    else {
        $type = "T";
        $query = "SELECT submit.challenge_id, challenge_name, COUNT(submit.challenge_id) AS solves, 
                IF (EXISTS (SELECT 1 FROM CTF_submit WHERE user_id = $user_id AND submit.challenge_id = challenge_id), TRUE, FALSE) AS solved
            FROM CTF_submit AS submit
            INNER JOIN CTF_challenge ON submit.challenge_id = CTF_challenge.challenge_id
            WHERE type = '$type' AND submit_date >= '$from_date'
            GROUP BY submit.challenge_id";
    }
    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;

    foreach ($rows as &$row) {
        $row["points"] = compute_challenge_points($conn, $row["challenge_id"]);
        unset($row["challenge_id"]);
    }
    return $rows;
}

function get_unlocked_hints($conn, $user_id, $from_date) {
    $team_id = get_user_team_id($conn, $user_id);
    if (!is_event_started($conn)) return false;
    if (!$team_id) return false;
        
    $query = "SELECT CTF_hint.hint_id, CTF_hint.description 
        FROM CTF_unlocked_hint
        INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
        WHERE team_id = $team_id AND unlock_date >= '$from_date'";
    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_training_leaderboard($conn) {
    $query = "SELECT username, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE team_id IS NULL 
            AND CTF_submit.user_id = CTF_user.user_id), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE team_id IS NULL AND CTF_unlocked_hint.user_id = CTF_user.user_id), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_user ON CTF_submit.user_id = CTF_user.user_id
        WHERE CTF_user.role != 'A'
        GROUP BY username
        ORDER BY score DESC";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_official_leaderboard($conn) {
    $query = "SELECT team_name, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE CTF_submit.team_id = CTF_team.team_id AND CTF_submit.event_id = ".get_last_event_id($conn)."), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE CTF_unlocked_hint.team_id = CTF_team.team_id), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_team ON CTF_submit.team_id = CTF_team.team_id
        WHERE team_name != 'H4ckUs4t1'
        GROUP BY team_name
        ORDER BY score DESC";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_users_data($conn) {
    $query = "SELECT CTF_user.user_id, username, email, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE team_id IS NULL 
            AND CTF_submit.user_id = CTF_user.user_id), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE team_id IS NULL AND CTF_unlocked_hint.user_id = CTF_user.user_id), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_user ON CTF_submit.user_id = CTF_user.user_id
        GROUP BY username
        ORDER BY CTF_user.user_id";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_teams_data($conn) {
    $query = "SELECT CTF_team.team_id, team_name, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE CTF_submit.team_id = CTF_team.team_id AND CTF_submit.event_id = ".get_last_event_id($conn)."), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE CTF_unlocked_hint.team_id = CTF_team.team_id), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_team ON CTF_submit.team_id = CTF_team.team_id
        GROUP BY team_name
        ORDER BY CTF_team.team_id";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function reset_ctf_solves($conn) {
    $query = "DELETE FROM CTF_submit WHERE team_id IS NOT NULL";
    $result = $conn->query($query);

    if (!$result) return false;
    return true;
}

function reset_ctf_unlocked_hints($conn) {
    $query = "DELETE FROM CTF_unlocked_hint WHERE team_id IS NOT NULL";
    $result = $conn->query($query);

    if (!$result) return false;
    return true;
}

function get_user_solves($conn, $user_id) {
    $query = "SELECT challenge_name, points 
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE user_id = $user_id AND team_id IS NULL";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_team_solves($conn, $team_id) {
    $query = "SELECT challenge_name, points 
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE team_id = $team_id";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_num_user_solves($conn, $user_id) {
    $query = "SELECT COUNT(challenge_name) AS solves
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE user_id = $user_id AND team_id IS NULL";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["solves"];
}

function get_num_team_solves($conn, $team_id) {
    $query = "SELECT COUNT(challenge_name) AS solves
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE team_id = $team_id";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["solves"];
}

?>