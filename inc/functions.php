<?php

$hash_options = [
    'cost' => 10,
];

function is_ascii($str) {
    return 0 == preg_match('/[^\x00-\x7F]/', $str);
}

function db_connect() {
    global $private_dir;
    require $private_dir."/config/db-config.php";

    if ($conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_servername)) 
        return $conn;
    return false;
}

function check_credentials($conn, $username_email, $password) {
    $query = "SELECT user_id, password_hash FROM CTF_user WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username_email, $username_email);

    if(!$stmt->execute()) return false;
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) return $row["user_id"];
    return false;
}

function is_account_active($conn, $user_id) {
    $query = "SELECT active FROM CTF_user WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if(!$stmt->execute()) return false;
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) return false;
    return $row["active"];
}

function login($conn, $username_email, $password) {
    $user_id = check_credentials($conn, $username_email, $password);
    if (!$user_id) return -1;
    if (!is_account_active($conn, $user_id)) return -2;

    set_last_login($conn, $user_id);

    $_SESSION['user_id'] = $user_id;
    return 1;
}

function set_last_login($conn, $user_id) {
    $query = "UPDATE CTF_user SET last_login = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if(!$stmt->execute()) return false;
    return true;
}

function send_reset_password_email($conn, $email) {
    $user_id = get_user_id_from_email($conn, $email);
    if (!$user_id) return -1;

    $username = get_username_from_id($conn, $user_id);

    do {
        $reset_password_code = bin2hex(random_bytes(32));
        
        $query = "SELECT 1 FROM CTF_user WHERE reset_password_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $reset_password_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    } while($row);

    $reset_password_expiry = date('y-m-d h:i:s', strtotime("+1 day"));
    
    $query = "UPDATE CTF_user SET password_hash = NULL, reset_password_code = ?, reset_password_expiry = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $reset_password_code, $reset_password_expiry, $user_id);
    if (!$stmt->execute()) return false;

    $link = $_SERVER['SERVER_NAME']."/forgot-password.php?reset_password_code=$reset_password_code";
    global $mail;
    $mail->IsHTML(true);
    $mail->AddAddress($email);
    $mail->Subject = "H4ckUs4t1 CTF password reset";
    $content = "<pre>Hello $username,
Here is your link to reset your password: <a href=\"" . $link . "\">" . $link . "</a>
The link will expire in 1 day</pre>";

    $mail->MsgHTML($content); 
    if(!$mail->Send()) return -2;

    return 1;
}

function get_user_id_from_reset_password_code($conn, $reset_password_code) {
    $query = "SELECT user_id FROM CTF_user WHERE reset_password_code = ? AND reset_password_expiry > NOW()";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $reset_password_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row) return $row["user_id"];

    //if reset password code is expired we delete it
    $query = "UPDATE CTF_user SET reset_password_code = NULL, reset_password_expiry = NULL WHERE reset_password_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $reset_password_code);
    $stmt->execute();
    return false;
}

function reset_password($conn, $reset_password_code, $new_password) {
    if(!is_ascii($new_password)) return -1;
    if(strlen($new_password) < 8 || strlen($new_password) > 128) return -2;

    $user_id = get_user_id_from_reset_password_code($conn, $reset_password_code);
    if (!$user_id) return -3;

    global $hash_options;
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT, $hash_options);

    $query = "UPDATE CTF_user SET password_hash = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $password_hash, $user_id);
    if(!$stmt->execute()) return false;

    return 1;
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
    delete_unverified_accounts($conn);

    if(check_if_username_is_used($conn, $username)) return -1;
    if(check_if_email_is_used($conn, $email)) return -2;
    if(strlen($username) < 3 || strlen($username) > 16) return -3;
    if(strpos($username, ' ') !== false) return -4;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return -5;
    if(strlen($password) < 8 || strlen($password) > 128) return -6;

    global $hash_options;
    $password_hash = password_hash($password, PASSWORD_DEFAULT, $hash_options);

    $role = 'U';

    do {
        $activation_code = bin2hex(random_bytes(32));
        
        $query = "SELECT 1 FROM CTF_user WHERE activation_code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $activation_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    } while($row);

    $activation_expiry = date('y-m-d h:i:s', strtotime("+1 day"));
    
    $query = "INSERT INTO CTF_user (username, password_hash, email, role, activation_code, activation_expiry)
        VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $username, $password_hash, $email, $role, $activation_code, $activation_expiry);

    if (!$stmt->execute()) return -7;

    global $mail;
    $mail->IsHTML(true);
    $mail->AddAddress($email);
    $mail->Subject = "H4ckUs4t1 CTF verification";
    $content = "<pre>Welcome $username!
Here is your link to activate your account: <a href=\"" . $_SERVER['SERVER_NAME']."/email-verification.php?activation_code=$activation_code" . "\">" . $_SERVER['SERVER_NAME']."/email-verification.php?activation_code=$activation_code" . "</a>
The link will expire in 1 day</pre>";

    $mail->MsgHTML($content); 
    if(!$mail->Send()) return -8;

    return 1;
}

function activate_user($conn, $activation_code) {
    $query = "SELECT username FROM CTF_user WHERE activation_code = ? AND activation_expiry > NOW() AND active = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $activation_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) return false;

    $query = "UPDATE CTF_user SET active = 1, activation_code = NULL, activation_expiry = NULL WHERE activation_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $activation_code);
    if(!$stmt->execute()) return false;
    return $row["username"];
}

function delete_unverified_accounts($conn) {
    $query = "DELETE FROM CTF_user WHERE active = 0 AND activation_expiry IS NOT NULL AND activation_expiry <= NOW()";
    if (!$conn->query($query)) return false;
    return true;
}

function check_if_username_is_used($conn, $username) {
    $query = "SELECT 1 FROM CTF_user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return true;
}

function check_if_email_is_used($conn, $email) {
    $query = "SELECT 1 FROM CTF_user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
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

function get_username_from_email($conn, $email) {
    $query = "SELECT username FROM CTF_user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return $row["username"];
}

function get_user_id_from_email($conn, $email) {
    $query = "SELECT user_id FROM CTF_user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) return false;
    return $row["user_id"];
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
    if(strlen($team_name) < 3 || strlen($team_name) > 16) return false;
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

    $query = "INSERT INTO CTF_team (team_name, token)
    VALUES (?, ?)";
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

    if(!$rows) return [];
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
        if (!is_dir($challenges_dir."/".$category)) continue;

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
        if (is_dir($challenges_dir."/".$category)){
            $challenges = scandir($challenges_dir."/".$category);
            foreach ($challenges as $challenge) 
            if($challenge == $challenge_name) return $category; 
        }
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
    $query = "SELECT event_id, event_name, start_date, end_date FROM CTF_event ORDER BY start_date DESC";
    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_event_data($conn, $event_id) {
    $query = "SELECT event_id, event_name, start_date, end_date FROM CTF_event WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row;
}

function get_event_csv($conn, $event_id) {
    
      
    // foreach ($rows as $team_points) {
    //     echo $team_points[0];
    //     $sumbits.array_push($team_points[0]);
    // }
    // // Open a file in write mode ('w') 
    // $fp = fopen('persons.csv', 'w'); 
    
    // // Loop through file pointer and a line 
    // foreach ($list as $fields) { 
    //     fputcsv($fp, $fields); 
    // }  

    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, ["Event Name", "Start Date", "End Date"]);
    fputcsv($df, array_slice(get_event_data($conn, $event_id), 1));
    fputcsv($df, []);

    $teams_leaderboard = get_official_leaderboard($conn, $event_id);
    fputcsv($df, ["Team", "Points"]);
    foreach ($teams_leaderboard as $team_data) {
        fputcsv($df, $team_data);
    }
    fputcsv($df, []);

    foreach ($teams_leaderboard as $team_data) {
        fputcsv($df, ["Team ".$team_data["team_name"]]);
        fputcsv($df, ["Challenge", "Points", "Submit date"]);
        foreach (get_team_submits($conn, get_team_id_from_team_name($conn, $team_data["team_name"])) as $sumbit) {
            fputcsv($df, $sumbit);
        }
        fputcsv($df, []);
    }

    fclose($df);
    return ob_get_clean();
}

function get_event_id($conn, $event_name) {
    $query = "SELECT event_id FROM CTF_event WHERE event_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $event_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
    return $row["event_id"];
}

function get_challenge_data($conn, $challenge_id) {
    $query = "SELECT challenge_name, flag, description, service, type, category, initial_points, minimum_points, points_decay, author FROM CTF_challenge WHERE challenge_id = ?";
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
    
    if(!$rows) return [];
    return $rows;
}

function is_hint_unlocked($conn, $hint_id, $user_id) {
    $team_id = get_user_team_id($conn, $user_id);
    if (is_event_started($conn)) {
        if (!$team_id) return false;
        
        $query = "SELECT 1 FROM CTF_unlocked_hint WHERE hint_id = ? AND team_id = $team_id AND CTF_unlocked_hint.event_id = ".get_current_event_id($conn);
    }
    else {
        $query = "SELECT 1 FROM CTF_unlocked_hint WHERE hint_id = ? AND user_id = $user_id AND CTF_unlocked_hint.event_id IS NULL";
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
    
    if(!$rows) return [];
    return $rows;
}

function get_challenge_categories($conn) {
    global $private_dir;
    $challenges_dir = $private_dir."/challenges";
    $categories = array();

    foreach (array_diff(scandir($challenges_dir), array('.', '..')) as $category) {
        if (is_dir($challenges_dir . '/' . $category)) array_push($categories, $category);
    }

    return $categories;
}

function get_challenges_from_category($conn, $category, $type) {
    if ($type) $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category' AND type = '$type' ORDER BY initial_points";
    else $query = "SELECT challenge_id FROM CTF_challenge WHERE category = '$category'";
    $result = $conn->query($query);
    $rows = $result->fetch_all();

    if(!$rows) return [];

    $challenges = array();
    foreach ($rows as $item) array_push($challenges, array_values($item)[0]);
    return $challenges;
}

function add_challenge($conn, $challenge_name, $flag, $description, $service, $type, $category, $initial_points, $minimum_points, $points_decay, $author) {
    if($initial_points < $minimum_points) return false;

    $query = "INSERT INTO CTF_challenge (challenge_name, flag, description, service, type, category, initial_points, minimum_points, points_decay, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssiiis", $challenge_name, $flag, $description, $service, $type, $category, $initial_points, $minimum_points, $points_decay, $author);
    
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

function add_event($conn, $event_name, $start_date, $end_date) {
    if (is_event_name_used($conn, $event_name)) return false;
    if (strlen($event_name) < 2 || strlen($event_name) > 64) return false;
    if (strtotime($start_date) >= strtotime($end_date)) return false;

    $query = "INSERT INTO CTF_event (event_name, start_date, end_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $event_name, $start_date, $end_date);

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

function edit_challenge_data($conn, $challenge_id, $description, $service, $type, $initial_points, $minimum_points, $points_decay, $author) {
    if($initial_points < $minimum_points) return false;
    // if(get_challenge_type($conn, $challenge_id) != $type) {
    //     $challenge_data = get_challenge_data($conn, $challenge_id);
    //     $hints = get_hints($conn, $challenge_id);
    //     $resources = get_db_challenge_resources($conn, $challenge_id);
        
    //     delete_challenge($conn, $challenge_id);
    //     $challenge_id = add_challenge($conn, $challenge_data["challenge_name"], $challenge_data["flag"], $description, $service, $type, $challenge_data["category"], $initial_points, $minimum_points, $points_decay, $author);
    //     foreach ($hints as $hint) {
    //         add_hint($conn, $challenge_id, $hint["description"], $hint["cost"]);
    //     }
    //     foreach ($resources as $resource) {
    //         add_challenge_resource($conn, $challenge_id, $resource["filename"]);
    //     }

    //     return true;
    // }
    
    $query = "UPDATE CTF_challenge SET description = ?, service = ?, type = ?, initial_points = ?, minimum_points = ?, points_decay = ?, author = ? WHERE challenge_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiiisi", $description, $service, $type, $initial_points, $minimum_points, $points_decay, $author, $challenge_id);
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

function edit_event($conn, $event_id, $event_name, $start_date, $end_date) {
    if (strlen($event_name) < 2 || strlen($event_name) > 64) return false;
    if (strtotime($start_date) >= strtotime($end_date)) return false;
    
    $query = "UPDATE CTF_event SET event_name = ?, start_date = ?, end_date = ? WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $event_name, $start_date, $end_date, $event_id);
    if(!$stmt->execute()) return false;

    return true;
}

function is_event_name_used($conn, $event_name) {
    $query = "SELECT 1 FROM CTF_event WHERE event_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $event_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if(!$row) return false;
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
        WHERE CTF_unlocked_hint.team_id = $team_id AND event_id = ".get_last_event_id($conn)."), 0) AS score";
    
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["score"];
}

function get_user_score($conn, $user_id) {
    $query = "SELECT IFNULL( (SELECT SUM(points) 
        FROM CTF_submit 
        WHERE event_id IS NULL 
        AND CTF_submit.user_id = $user_id), 0)
        - 
        IFNULL( (SELECT SUM(cost)
        FROM CTF_unlocked_hint
        INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
        WHERE event_id IS NULL AND CTF_unlocked_hint.user_id = $user_id), 0) AS score";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["score"];
}

function get_user_last_login($conn, $user_id) {
    $query = "SELECT last_login
        FROM CTF_user
        WHERE user_id = '$user_id'";

    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if(!$row) return false;
    return $row["last_login"];
}

function unlock_hint($conn, $hint_id, $user_id) {
    if (is_hint_unlocked($conn, $hint_id, $user_id)) return true;
    
    $challenge_type = get_challenge_type($conn, get_hint_challenge_id($conn, $hint_id));
    $team_id = get_user_team_id($conn, $user_id);
    if ($challenge_type == "O") {
        if (!is_event_started($conn)) return false;
        if (!$team_id) return false;
        if (get_user_role($conn, $user_id) != "A" && get_hint_cost($conn, $hint_id) > get_team_score($conn, $team_id)) return false;

        $query = "INSERT INTO CTF_unlocked_hint (hint_id, user_id, team_id, event_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiii", $hint_id, $user_id, $team_id, get_current_event_id($conn));
    } else {
        if (is_event_started($conn)) return false;
        if (get_user_role($conn, $user_id) != "A" && get_hint_cost($conn, $hint_id) > get_user_score($conn, $user_id)) return false;

        $query = "INSERT INTO CTF_unlocked_hint (hint_id, user_id) VALUES (?, ?)";
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
        if (!get_last_event_id($conn)) return false;
        if (!$team_id) return false;

        $query = "SELECT 1 FROM CTF_submit WHERE challenge_id = ? AND team_id = ? AND CTF_submit.event_id = ".get_last_event_id($conn);
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $challenge_id, $team_id);
    } else if($challenge_type == "T") {
        $query = "SELECT 1 FROM CTF_submit WHERE challenge_id = ? AND user_id = ? AND CTF_submit.event_id IS NULL";
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
    $query = "SELECT COUNT(challenge_id) AS solves 
        FROM CTF_submit 
        LEFT JOIN CTF_team ON CTF_submit.team_id = CTF_team.team_id
        INNER JOIN CTF_user ON CTF_submit.user_id = CTF_user.user_id
        WHERE team_name != 'H4ckUs4t1' AND role != 'A' AND challenge_id = ? AND CTF_submit.event_id = " . (get_current_event_id($conn) == NULL ? "NULL" : get_current_event_id($conn));
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

        $query = "INSERT INTO CTF_submit (user_id, team_id, challenge_id, event_id, points) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiii", $user_id, $team_id, $challenge_id, $event_id, $points);
    } else {
        if ($challenge_type != "T") return false;

        $query = "INSERT INTO CTF_submit (user_id, challenge_id, points) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $challenge_id, $points);
    }
    
    if (!$stmt->execute()) return false;
    return true;
}

function get_challenges_solves_and_points($conn, $user_id) {
    $rows = get_challenge_list($conn);
    foreach ($rows as &$row) {
        $challenge_id = get_challenge_id($conn, $row["challenge_name"]);
        $row["solves"] = get_challenge_solves($conn, $challenge_id);
        $row["solved"] = is_challenge_solved($conn, $challenge_id, $user_id);
        $row["points"] = compute_challenge_points($conn, $challenge_id);
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
        WHERE team_id = $team_id AND unlocked_at >= '$from_date' AND event_id = ".get_current_event_id($conn);
    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_training_leaderboard($conn) {
    $query = "SELECT username, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE event_id IS NULL 
            AND CTF_submit.user_id = CTF_user.user_id), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE event_id IS NULL AND CTF_unlocked_hint.user_id = CTF_user.user_id), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_user ON CTF_submit.user_id = CTF_user.user_id
        WHERE CTF_user.role != 'A'
        GROUP BY username
        ORDER BY score DESC";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return [];
    return $rows;
}

function get_official_leaderboard($conn, $event_id = NULL) {
    if (!$event_id) $event_id = get_last_event_id($conn);
    $query = "SELECT team_name, 
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE CTF_submit.team_id = CTF_team.team_id AND CTF_submit.event_id = ". $event_id ."), 0)
            - IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE CTF_unlocked_hint.team_id = CTF_team.team_id AND event_id = ". $event_id ."), 0) AS score
        FROM CTF_submit
        RIGHT JOIN CTF_team ON CTF_submit.team_id = CTF_team.team_id
        WHERE team_name != 'H4ckUs4t1'
        GROUP BY team_name
        ORDER BY score DESC";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return [];
    return $rows;
}

function get_users_data($conn) {
    $query = "SELECT CTF_user.user_id, username, email, team_name,
            IFNULL( (SELECT SUM(points) 
            FROM CTF_submit 
            WHERE event_id IS NULL 
            AND CTF_submit.user_id = CTF_user.user_id), 0)
            - 
            IFNULL( (SELECT SUM(cost)
            FROM CTF_unlocked_hint
            INNER JOIN CTF_hint ON CTF_unlocked_hint.hint_id = CTF_hint.hint_id
            WHERE event_id IS NULL AND CTF_unlocked_hint.user_id = CTF_user.user_id), 0) AS score
        FROM CTF_user
        LEFT JOIN CTF_team ON CTF_user.team_id = CTF_team.team_id
        ORDER BY user_id";

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
            WHERE CTF_unlocked_hint.team_id = CTF_team.team_id AND CTF_unlocked_hint.event_id = ".get_last_event_id($conn)."), 0) AS score
        FROM CTF_team
        ORDER BY team_id";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function reset_ctf_solves($conn) {
    $query = "DELETE FROM CTF_submit WHERE event_id IS NOT NULL";
    $result = $conn->query($query);

    if (!$result) return false;
    return true;
}

function reset_ctf_unlocked_hints($conn) {
    $query = "DELETE FROM CTF_unlocked_hint WHERE event_id IS NOT NULL";
    $result = $conn->query($query);

    if (!$result) return false;
    return true;
}

function get_user_solves($conn, $user_id) {
    $query = "SELECT challenge_name, points 
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE user_id = $user_id AND event_id IS NULL";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_team_solves($conn, $team_id) {
    $query = "SELECT challenge_name, points 
        FROM CTF_submit
        INNER JOIN CTF_challenge ON CTF_submit.challenge_id = CTF_challenge.challenge_id
        WHERE team_id = $team_id AND CTF_submit.event_id = ".get_last_event_id($conn);

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_team_members($conn, $team_id) {
    $query = "SELECT username
        FROM CTF_user
        WHERE team_id = $team_id";

    $result = $conn->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    if(!$rows) return false;
    return $rows;
}

function get_team_submits($conn, $team_id, $event_id = NULL) {
    if (!$event_id) $event_id = get_last_event_id($conn);
    $query = "SELECT CTF_challenge.challenge_name, points, submit_date FROM CTF_submit RIGHT JOIN CTF_challenge
        ON CTF_challenge.challenge_id = CTF_submit.challenge_id WHERE team_id = ? AND event_id = ? ORDER BY submit_date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $team_id, $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    return $rows;
}

?>