<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <?php
  //set cookie default value
  setcookie('user', '0', time() + (86400 * 30), "/");
  ?>

  <div id="window">
    <h1> Login </h1>
    <br>
    <br>
    <br>
    <div class="data">
      <label for="username">
        <br>
        <form action="" method="post">
          <input name="username" type="text" name="" placeholder="Username" required>
      </label>
      <br>
      <br>
      <br>

      <label for"user-pin">

        <br>
        <input type="password" name="user-pin" placeholder="Password" required>
        <br>
        <br>
        <br>
        <button name="invia" class="LoginBtn type=" submit"> Login </button>

        <br>
        <br>
        <br>
        <div style="text-align: center;">
          <a href=""> password forgotten? </a>
        </div>
      </label>
      </form>
      <?php

      if (isset($_POST['invia'])) {
        $username = $_POST['username'];
        $userpin = $_POST['user-pin'];
        if ($username == "admin" && $userpin == "admin") {
          echo "Nice try bro! :)";
        } else {
          echo "Wrong username or password";
        }
      }
      ?>

      <?php
      //if cookie value is 129, redirect to index.php
      $flag = "ITT{4r3_c00k13s_s3cur3?}";
      if (isset($_COOKIE['user'])) {
        if ($_COOKIE['user'] == 129) {
          echo $flag;
        }
      }
      ?>
    </div>


  </div>