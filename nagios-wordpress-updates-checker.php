<?php
/**
Plugin Name: Nagios WordPress Updates Checker
Description: Nagios WordPress Updates Checker
Author: Fred Bradley
Version: 1.10.1
Author URI: http://fred.im/
Network: true
 */

namespace FredBradley\WPUpdateChecker;

require_once 'vendor/autoload.php';

$settings_api = new SettingsAPI();

$plugin_data = get_file_data( __FILE__, array( 'Version' ), 'plugin' );


new Plugin( $plugin_data[0] );
