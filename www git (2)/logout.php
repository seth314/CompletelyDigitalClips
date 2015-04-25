<?php
include 'sessions.php';
// clear session
if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Email']))
{
	session_unset();
	session_destroy();
	header('Location: /index.php');
	exit();
}
?>
