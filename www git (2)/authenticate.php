<?php

// session utils
include 'config.php';
include 'sessions.php';

if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Email']))
{
	session_unset();
	session_destroy();
}

// get POST information from login form
$email=$_POST["email"];
$password=$_POST["password"];

// open connection to the database
//include 'opendb.php';//included in sessions.php

//create new session
//include 'usercheck.php';//included in sessions.php
if(AuthUser($email, $password))
{
	// set an active cookie for this username
	$_SESSION['LoggedIn'] = true;
	$_SESSION['Email'] = $_POST["email"];
	$_SESSION['Created'] = time();
	$_SESSION['LastActivity'] = time();
	$_SESSION['IP'] = getenv("REMOTE_ADDR");
	header('Location: /index.php');
	exit();
}
else
{
	// logout
	session_unset();
	session_destroy();
	header('Location: /login.php?message=Login%20Failed');
	exit();
}

// close connection to the database
include 'closedb.php';

?>
