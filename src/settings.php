<?php
namespace FredBradley\WPUpdateChecker;

use WeDevs_Settings_API;

class LoadSettings {

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
				'title' => __( 'Nagios', 'wedevs' )
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
					'label' => __( 'Ignored Plugins', 'fredbradley'),
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

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
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
