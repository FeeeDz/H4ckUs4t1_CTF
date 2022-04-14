<?php
function db_connect() {
    require "inc/db_config.php";

    if ($conn = mysqli_connect($hostname, $username, $password, $servername)) 
        return $conn;
    return false;
}



?>