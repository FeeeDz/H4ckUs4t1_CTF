<?php

$hash_options = [
    'cost' => 10,
];

function db_connect() {
    require "inc/db_config.php";

    if ($conn = mysqli_connect($db_hostname, $db_username, $db_password, $db_servername)) 
        return $conn;
    return false;
}

function db_login($conn, $email, $password) {
    $query = "SELECT username, password_hash, role FROM CTF_user WHERE email = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['logged'] = $row["username"];
        $_SESSION['role'] = $row['role'];
        return true;
    }

    unset($_SESSION['logged']);
    unset($_SESSION['role']);
    return false;
}

function db_logout() {
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

function db_register_user($conn, $username, $password, $email) {
    if(strlen($username) < 3 || strlen($username) > 16) return false;
    if(strlen($password) < 8 || strlen($password) > 64) return false;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    global $hash_options;
    $role = 'U';

    $password_hash = password_hash($password, PASSWORD_DEFAULT, $hash_options);
    $query = "INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
        VALUES (?, ?, ?, NOW(), NOW(), ?, NULL)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $password_hash, $email, $role);

    if ($stmt->execute()) {
        $_SESSION['logged'] = $username;
        $_SESSION['role'] = $role;
        return true;    
    }

    return false;
}

function db_register_team($conn, $team_name) {
    if(strlen($team_name) < 3 || strlen($team_name) > 32) return false;

    $token = bin2hex(random_bytes(16));

    $query = "INSERT INTO CTF_team (team_name, token, registration_date)
    VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $team_name, $token);
    if(!$stmt->execute()) return false;

    $query = "UPDATE CTF_user SET team_name = ? WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $team_name, $_SESSION['logged']);
    $stmt->execute();

    return $token;
}

?>