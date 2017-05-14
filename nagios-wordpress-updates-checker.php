<?php
/*
Plugin Name: Nagios Wordpress Updates Checker
Description: Nagios Wordpress Updates Checker
Author: Fred Bradley
Version: 1.7.11
Author URI: http://fred.im/
*/

namespace FredBradley\WPUpdateChecker;

require_once 'vendor/autoload.php';

$settings_api = new LoadSettings();

new Plugin();



