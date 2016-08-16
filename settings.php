<?php
if ( !class_exists('FB_Nagios_WP_Update_Checker_Settings' ) ):
class FB_Nagios_WP_Update_Checker_Settings {

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
        add_options_page( 'Nagios', 'Nagios', 'manage_themes', 'nagios-wordpress-update-checker-settings', array($this, 'plugin_page') );
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
            ),
        );

        return $settings_fields;
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
$settings_api = new FB_Nagios_WP_Update_Checker_Settings();
endif;

