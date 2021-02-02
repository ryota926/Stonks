<?php
// Initialize the session
session_start();
include "../inc/dbinfo.inc"; 

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login-usr.php");
    exit;
}
// Check connection
if ($link->connect_error) {
  die("Connection failed: " . $link->connect_error);
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
	    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<h1> Hello </h1>
</body>
</html>
