<?php
/* ***** Delete from here... *****
  function authenticated_session($username)
  {
    return sha1(md5($username));
  }
// ***** ...to here, once code is secured *****/

session_start();

include 'opendb.php';
include 'usercheck.php';
// if session is set
//if(session_status() != PHP_SESSION_NONE)
//{

//Session handling for users

if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Created']))
{
	if(!isset($_SESSION['IP']) || $_SESSION['IP'] != getenv("REMOTE_ADDR"))
	{
		//If the SESSION['IP'] is not set or the value of SESSION['IP'] is not the same as the clients destroy the session
		session_unset();
		session_destroy();
		header('Location: /login.php?message=Session%20timed%20out.%20Please%20log%20in.');
		exit();
	}
	if ( !(EmailExists($_SESSION['Email'])) )
	{
		session_unset();
		session_destroy();
		header('Location: /login.php?message=Email%20no%20longer%20exists.%20Please%20log%20in.');
		exit();
	}
	
	if((time() - $_SESSION['Created']) > 10000)
	{
		//Session is too old. Destroy it.
		session_unset();
		session_destroy();
		header('Location: /login.php?message=Session%20timed%20out.%20Please%20log%20in.');
		exit();
	}
	elseif((time() - $_SESSION['LastActivity'] > 10) || (time() - $_SESSION['Created'] > 60))
	{
		//Set a new session id for our session.
		session_regenerate_id(true);
	}
	$_SESSION['LastActivity'] = time();
}
/* uncomment this later
elseif($_SERVER["RDP_IP"] != getenv("REMOTE_ADDR"))
{
	//Session exists but these values aren't set. Seems strange... kill it.
	session_unset();
	session_destroy();
	header('Location: /login.php?message=Session%20timed%20out.%20Please%20log%20in.');
}
//*/
//}

function isUserLoggedIn()
{
	return isset($_SESSION['LoggedIn']) && isset($_SESSION['Created']);
}



?>
