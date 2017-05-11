<?php
namespace FredBradley\WPUpdateChecker;

use Puc_v4_Factory;

class Plugin {
	public function __construct() {
		add_action( 'rest_api_init', array($this,'wpshout_register_routes') );
		$this->plugin_update_check();
	}

	public function plugin_update_check() {
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/fredbradley/nagios-wordpress-updates-checker',
			__FILE__,
			'nagios-wordpress-update-checker'
		);
	}

	public function wpshout_register_routes() {
	    register_rest_route(
	        'nagios',
	        'check',
	        array(
	            'methods' => 'GET',
	            'callback' => array($this,'check_4_updates'),
	        )
	    );
	}

	public function get_settings($setting) {
		return (new Settings())->$setting;
	}

	public function check_4_updates() {

		global $wp_version;

		$check = new Check($wp_version);

		return $check;

	}



}
