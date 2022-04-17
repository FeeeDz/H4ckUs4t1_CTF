<?php 
session_start();
require "inc/functions.php";
db_logout();
header("Location: index.php");
?>