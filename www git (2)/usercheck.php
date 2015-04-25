<?php
// Checking users against the database for various reasosns...

function UserExists($username)
{
	try
	{
		$handler = CreateHandler();
		$prepStatement = $handler->prepare("SELECT password FROM users WHERE username = ?");
		$prepStatement->execute(array($username));
		
		if($prepStatement->rowCount() > 0)
		{
			return true;
		}
	}
	catch(PDOException $exception)
	{
		echo $exception->getMessage();
	}
	return false;
}

function EmailExists($email)
{
	try
	{
		$handler = CreateHandler();
		$prepStatement = $handler->prepare("SELECT password FROM users WHERE email = ?");
		$prepStatement->execute(array($email));
		
		if($prepStatement->rowCount() > 0)
		{
			return true;
		}
	}
	catch(PDOException $exception)
	{
		print("We apologize for the inconvenience. Our Database is having connectivity issues.");
		exit();
	}
	return false;
}

function CreateUser($email, $username, $password)
{
	try
	{
		$salt = bin2hex(openssl_random_pseudo_bytes(22));
		$hash = crypt($password, "$2y$12$".$salt);
		$handler = CreateHandler();
		$prepStatement = $handler->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
		$prepStatement->bindValue(":email", $email);
		$prepStatement->bindValue(":username", $username);
		$prepStatement->bindValue(":password", $hash);
		$prepStatement->execute();
		return true;
	}
	catch(PDOException $exception)
	{
		print("We apologize for the inconvenience. Our Database is having connectivity issues.");
		exit();
	}
	return false;
}

function AuthUser($email, $password)
{
	try
	{
		$handler = CreateHandler();
		$prepStatement = $handler->prepare("SELECT password FROM users WHERE email = ?");
		$prepStatement->execute(array($email));
		
		if(!($prepStatement->rowCount() == 1))
		{
			return false;
		}
		$result = $prepStatement->fetch(PDO::FETCH_OBJ);
		$hash = $result->password;
		// crypt function needs salt from $hash
		if (crypt($password, $hash) == $hash)
		{
			return true;
		}
	}
	catch(PDOException $exception)
	{
		print("We apologize for the inconvenience. Our Database is having connectivity issues.");
		exit();
	}
	return false;
}

// ***** ONLY USE ON PLAIN TEXT PASSWORDS *****
function HASH_ALL_PASSWORDS()
{
	try
	{
		$handler = CreateHandler();
		$prepStatement = $handler->prepare("SELECT email, password FROM users");
		$prepStatement->execute();
		
		$clipsResult = $prepStatement;
		$creds = array();
		while($clipsRow = $clipsResult->fetch(PDO::FETCH_ASSOC))
		{
			$cred = array($clipsRow['email'],$clipsRow['password']);
			$creds[] = $cred;
		}
		echo "<pre>";
		var_dump($creds);
		echo "</pre>";
		foreach($creds as $cred)
		{
			$handler = CreateHandler();
			$prepStatement = $handler->prepare("UPDATE users SET password = ? WHERE email = ?");
			$salt = bin2hex(openssl_random_pseudo_bytes(22));
			$hash = crypt($cred[1], "$2y$12$".$salt);
			$prepStatement->execute(array($hash, $cred[0]));
		}
	}
	catch(PDOException $exception)
	{
		print("We apologize for the inconvenience. Our Database is having connectivity issues.");
		exit();
	}
}


?>
