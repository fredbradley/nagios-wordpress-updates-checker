<?php
namespace FredBradley\WPUpdateChecker;

/*
 * Load Wordpress Scripts
 * This has potential to break if someone has set up their plugins folder to be in a different place to the standard.
 * But you can't help everyone!
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php');
require_once('src/class.Settings.php');
require_once('src/class.Check.php');

$settings = new Settings();

$allowed_ips = array(
	$settings->nagios_server_ip
);
// If your Wordpress installation is behind a Proxy like Nginx use 'HTTP_X_FORWARDED_FOR'
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$remote_ip = $_SERVER['REMOTE_ADDR'];
}

// Check if the requesting server is allowed
if (! in_array($remote_ip, $allowed_ips))
{
	echo "CRITICAL#IP $remote_ip not allowed.";
	exit;
}

/*
 * Load Check Class
 */

global $wp_version;

$check = new Check($wp_version);

echo $check->status."#".$check->text;


