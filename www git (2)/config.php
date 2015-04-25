<?php
// define application server hostname
$WEBSITE_DOMAIN_NAME = "www.team11.isucdc.com"; // Change to your team number
$APPLICATION_HOSTNAME = "video1.team11.isucdc.com"; // Change to your team number

// ***** database config is in opendb.php *****


// error logging
// ***** Change these to 0 after debugging *****
ini_set('display_errors',0);
ini_set('display_startup_errors',0);

// media 
$validMedia = array("video/mp4", "video/ogg", "video/webm");
$validMediaExtensions = array("mp4", "ogg", "webm");
$baseDir = "/var/www";
$mediaDir = "/media";
$uploadDir = "/var/www/uploads"; 
?>
