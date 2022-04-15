<?php

$hash_options = [
    'cost' => 10,
];

function db_connect() {
    require "inc/db_config.php";

    if ($conn = mysqli_connect($hostname, $username, $password, $servername)) 
        return $conn;
    return false;
}

function db_login($conn, $username, $password) {

    $query = "SELECT password_hash, role FROM CTF_user WHERE username = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify("Beniamino2003", $row['password_hash'])) {
        $_SESSION['logged'] = $username;
        $_SESSION['role'] = $row['role'];
        return true;
    }

    unset($_SESSION['logged']);
    unset($_SESSION['role']);
    return false;
}

function db_logout(){
    unset($_SESSION['logged']);
    unset($_SESSION['role']);
    return true;
}

function db_register($conn, $username, $password, $email) {
    global $hash_options;

    $password_hash = password_hash($password, PASSWORD_DEFAULT, $hash_options);
    $query = "INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
        VALUES (?, ?, ?, NOW(), NOW(), 'U', NULL)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $password_hash, $email);

    if ($stmt->execute())
        return true;    
    return false;
}

?>