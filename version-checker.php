<?php
namespace FredBradley\WPUpdateChecker;

/*
 * Load Wordpress Scripts
 * This has potential to break if someone has set up their plugins folder to be in a different place to the standard.
 * But you can't help everyone!
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php');
require_once('src/Settings.php');
require_once('src/Check.php');


/*
 * Load Check Class
 */

global $wp_version;

$check = new Check($wp_version);

// Output one line of text
echo $check->status."#".$check->text;


