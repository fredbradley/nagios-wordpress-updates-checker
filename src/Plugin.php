<?php
namespace FredBradley\WPUpdateChecker;

use Puc_v4_Factory;

class Plugin {

	private $version;
	public function __construct( $version ) {
		$this->version = $version;
		Analytics::run();
		add_action( 'rest_api_init', array( $this, 'wpshout_register_routes' ) );

		$this->plugin_update_check( 'nagios-wordpress-updates-checker' );
	}

	public function plugin_update_check( $plugin_name ) {
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/fredbradley/' . $plugin_name . '/',
			dirname( dirname( __FILE__ ) ) . '/' . $plugin_name . '.php',
			$plugin_name
		);
	}

	public function wpshout_register_routes() {
		register_rest_route(
			'nagios',
			'check',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'check_4_updates' ),
			)
		);
	}

	public function get_settings( $setting ) {
		return ( new Settings() )->$setting;
	}

	public function check_4_updates() {

		global $wp_version;

		$check = new Check( $wp_version, $this->version );

		return $check;
	}
}
