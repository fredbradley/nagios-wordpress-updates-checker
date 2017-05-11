<?php

/*
 * Load Wordpress Scripts
 * This has potential to break if someone has set up their plugins folder to be in a different place to the standard.
 * But you can't help everyone!
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php'); global $wp_version;

// Include your Nagios server IP below
// It is safe to keep 127.0.0.1
$allowed_ips = array(
	get_option('nagios-settings', array('nagios_server_ip'=>'1.2.3.4'))['nagios_server_ip']
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


$core_updates = FALSE;
$plugin_updates = FALSE;

wp_version_check();
wp_update_plugins();
wp_update_themes();

if (function_exists('get_transient'))
{
	$core = get_transient('update_core');
	$plugins = get_transient('update_plugins');
	$themes = get_transient('update_themes');

	if ($core == FALSE)
	{
		$core = get_site_transient('update_core');
		$plugins = get_site_transient('update_plugins');
		$themes = get_site_transient('update_themes');
	}
}
else
{
	$core = get_site_transient('update_core');
	$plugins = get_site_transient('update_plugins');
	$themes = get_site_transient('update_themes');
}

$core_available = FALSE;
$plugin_available = FALSE;
$theme_available = FALSE;

foreach ($core->updates as $core_update)
{
	if ($core_update->current != $wp_version)
	{
		$core_available = TRUE;
	}
}

/* 
 * There are sometimes plugins that we want to ignore from the Nagios check,
 * largely because there's nothing that we can do about them. For example premium
 * plugins like Visual Composer or Rev Slider that come bundled with a theme but we 
 * can't update ourselves!
 */
$ignored_plugins = array(
	"js_composer/js_composer.php"
);
foreach ($ignored_plugins as $ignore_plugin):
	unset($plugins->response[$ignore_plugin]);
endforeach;
 
$plugin_available = (count($plugins->response) > 0);
$theme_available = (count($themes->response) > 0);

$text = array();

if ($core_available)
	$text[] = 'Core updates available';

if ($plugin_available)
	$text[] = 'Plugin updates available';

if ($theme_available)
	$text[] = 'Theme updates available';

$status = 'OK';

if ($core_available)
{
	$status = 'CRITICAL';
}
elseif ($theme_available OR $plugin_available)
{
	$status = 'WARNING';
}

echo $status . '#' . implode($text, ';');

