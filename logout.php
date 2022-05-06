<?php 
session_start();
require "inc/functions.php";

logout();
header("Location: index.php");
?>