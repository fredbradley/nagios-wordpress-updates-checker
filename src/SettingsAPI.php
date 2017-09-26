<?php
namespace FredBradley\WPUpdateChecker;

use WeDevs_Settings_API;

class SettingsAPI {

	private $settings_api;

	function __construct() {
		$this->settings_api = new WeDevs_Settings_API;

		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'admin_menu', array($this, 'admin_menu') );
	}

	function admin_init() {

		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

	function admin_menu() {
		add_options_page( 'Nagios Checker', 'Nagios Checker', 'manage_options', 'nagios-wordpress-update-checker-settings', array($this, 'plugin_page') );
	}

	function get_settings_sections() {
		$sections = array(
			array(
				'id' => 'nagios-settings',
				'title' => __( 'Nagios Checker Settings', 'wedevs' )
			),

		);
		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {
		$settings_fields = array(
			'nagios-settings' => array(
				array(
					'name'              => 'nagios_server_ip',
					'label'             => __( 'Nagios Server IP Address', 'wedevs' ),
					'desc'              => __( 'The IP Address of your Nagios Server where the checks are coming from.', 'wedevs' ),
					'type'              => 'text',
					'default'           => '0.0.0.0',
				),
				array(
					'name' => 'ignored_plugins',
					'label' => __( 'Ignore Plugins from Nagios Check', 'fredbradley'),
					'type' => 'multicheck',
	                'options' => $this->get_plugin_list()
				)
			),
		);

		return $settings_fields;
	}

	private function get_plugin_list() {
		$all_plugins = get_plugins();
		$output = array();
		foreach ($all_plugins as $plugin => $value):
			$output[$plugin] = $value['Name'];
		endforeach;

		return $output;
	}

	function plugin_page() {
		echo '<div class="wrap">';

		if (count($this->get_settings_sections()) > 1):
			$this->settings_api->show_navigation();
		endif;
		$this->instructions();
		$this->settings_api->show_forms();

		echo '</div>';
	}

	public function instructions() {
		echo '<div style="padding:5px;background:orange;">';
		echo '<h3>Instructions</h3>';
		echo '<ol>';
		echo '<li>Please read the <a href="https://github.com/fredbradley/nagios-wordpress-updates-checker" target="_blank">README.md</a></li>';
		echo '<li>You will need to download the <a href="'.plugins_url( 'check_wp_update', dirname(__FILE__) ).'" target="_blank">check_wp_update</a> from the plugin package and copy it to your Nagios Server. Instructions are on the README.md</li>';
		echo '<li>If you have any questions, suggestions, or issues please add them as <a href="https://github.com/fredbradley/nagios-wordpress-updates-checker/issues" target="_blank">Issues to Github here</a>. If you\'re a fancy pants developer and can see where improvements can be made, please feel free to <a href="https://github.com/fredbradley/nagios-wordpress-updates-checker/network" target="_blank">fork</a> and <a href="https://github.com/fredbradley/nagios-wordpress-updates-checker/pulls" target="_blank">submit a Pull Request</a>.</li>';
		echo '</ol>';
		echo '<p><em>If you find this plugin useful and are the sort person that is lovely, please <strong><a href="https://paypal.me/fredbradley">consider donating</a></strong> a fraction of the amount that it would cost to hire a developer to make this for you!</em></p>';
		echo '</div>';
		echo '<hr />';
	}

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	function get_pages() {
		$pages = get_pages();
		$pages_options = array();
		if ( $pages ) {
			foreach ($pages as $page) {
				$pages_options[$page->ID] = $page->post_title;
			}
		}

		return $pages_options;
	}

}
