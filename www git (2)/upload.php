<?php

include 'config.php';
include 'sessions.php';

// open connection to the database
//include 'opendb.php';//included in sessions.php

function generateShortName($length = 11)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}
  // check user authentication
if(isUserLoggedIn())
{
	try
	{

		if ($_FILES["video"]["error"] == UPLOAD_ERR_OK)
		{

			// check for failed/corrupted post
			if(!isset($_POST["title"]) && !isset($_POST["title"]) && !isset($_POST["video"]))
			{
				header("Location: /post.php?message=" . urlencode("Upload failed, check video file."));
				exit();
			}

			// check title
			if(!isset($_POST["title"]))
			{
				header("Location: /post.php?message=" . urlencode("Missing title."));
				exit();
			}

			// check description
			if(!isset($_POST["description"]))
			{
				header("Location: /post.php?message=" . urlencode("Missing description."));
				exit();
			}

			// get filename
			$filename = str_replace("'","_",$_FILES["video"]["name"]);

			// move file to upload directory
			move_uploaded_file($_FILES["video"]["tmp_name"], "$uploadDir/$filename");

			// check upload success
			if(!file_exists("$uploadDir/$filename"))
			{
				header("Location: /post.php?message=" . urlencode("Upload failed."));
				exit();
			}

			// generate unique shortname for upload
			$shortname = generateShortName();
			$extension = pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
			while(file_exists("$uploadDir/$shortname.$extension"))
			{
				$shortname = generateShortName();
			}

			// check file type
			if(in_array($_FILES["video"]["type"], $validMedia) != 1 || in_array($extension, $validMediaExtensions) != 1)
			{
				exec("rm '$uploadDir/$filename'");
				header("Location: /post.php?message=" . urlencode("File format not supported."));
				exit();
			}
				
			// generate video thumbnail

			$output = array();
			$errcode = 1;
			// test with: sudo ffmpeg -i "/var/www/media/filename.mp4" -ss 00:00:04 -f image2 -s qvga "/var/www/media/filename.png"
			exec("ffmpeg -i '$uploadDir/$filename' -ss 00:00:04 -f image2 -s qvga \"$baseDir$mediaDir/$shortname.png\"", $output, $errcode);

			if ($errcode == 0)
			{
				// rename file upload to shortname
				rename("$uploadDir/$filename", "$baseDir$mediaDir/$shortname.$extension");
			}
			else
			{
				exec("avconv -i '$uploadDir/$filename' -ss 00:00:04 -vframes 1 -s qvga \"$baseDir$mediaDir/$shortname.png\"", $output, $errcode);
				if ($errcode == 0)
				{
					// rename file upload to shortname
					rename("$uploadDir/$filename", "$baseDir$mediaDir/$shortname.$extension");
				}
				else
				{
					exec("rm '$uploadDir/$filename'");
					exec("rm '$baseDir$mediaDir/$shortname.png'");
					header("Location: /post.php?message=" . urlencode("File format not supported."));
					exit();
				}
			}

			// save input fields
			$title = $_POST["title"];
			$description = $_POST["description"];
		}
		else
		{
			// file upload failed
			header("Location: /post.php?message=" . urlencode("No video imported."));
			exit();
		}


		$userID = emailToUserID($_SESSION['Email']);

		// insert video into clips table
		$insertResult = insertVideo($APPLICATION_HOSTNAME, $shortname, $title, $description, $userID, $extension);
		//$insertResult = mysql_query("INSERT INTO clips (host, shortname, title, description, user, extension) VALUES ('$APPLICATION_HOSTNAME', '$shortname', '$title', '$description', '$userID', '$extension')");
		if ($insertResult)
		{
			// success! view the video
			header("Location: /view.php?video=" . $shortname);
			exit();
		}
		else
		{
			exit();
		}
	}
	catch (Exception $e)
	{
		header("Location: /post.php?message=" . urlencode("Error: " . $e));
		exit();
	}
}
else
{
	header("Location: /post.php?message=" . urlencode("Unauthenticated user."));
	exit();
}

?>

