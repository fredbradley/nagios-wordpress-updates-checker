<?php
/*
Plugin Name: Nagios Wordpress Updates Checker
Description: Nagios Wordpress Updates Checker
Author: Fred Bradley
Version: 1.9.11
Author URI: http://fred.im/
Network: true
*/

namespace FredBradley\WPUpdateChecker;

require_once 'vendor/autoload.php';

$settings_api = new SettingsAPI();

$plugin_data = get_file_data(__FILE__, array('Version'), 'plugin');


new Plugin($plugin_data[0]);
