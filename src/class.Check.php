<?php
namespace FredBradley\WPUpdateChecker;

class Check {
	private $core_updates = FALSE;
	private $plugin_updates = FALSE;
	private $wp_version;

	private $core = FALSE;
	private $plugins = FALSE;
	private $themes = FALSE;

	private $core_available = FALSE;
	private $plugin_available = FALSE;
	private $theme_available = FALSE;
	private $ip_address = FALSE;

	public $status = "OK";
	public $text;

	private function setting($setting) {

		$nagios_settings = get_option('nagios-settings');
		$this->ip_address = $nagios_settings['nagios_server_ip'];
		return $nagios_settings[$setting];
	}

	public function __construct($wp_version) {
		$this->wp_version = $wp_version;

		wp_version_check();
		wp_update_plugins();
		wp_update_themes();

		$this->get_transients();

		$this->removed_ignored_plugins();

		$this->check();
	}

	private function get_transients() {

		if (function_exists('get_transient')) {
			$this->core = get_transient('update_core');
			$this->plugins = get_transient('update_plugins');
			$this->themes = get_transient('update_themes');

			if ($this->core == FALSE) {
				$this->core = get_site_transient('update_core');
				$this->plugins = get_site_transient('update_plugins');
				$this->themes = get_site_transient('update_themes');
			}

		} else {
			$this->core = get_site_transient('update_core');
			$this->plugins = get_site_transient('update_plugins');
			$this->themes = get_site_transient('update_themes');
		}
	}

	private function check_core() {
		foreach ($this->core->updates as $core_update) :
			if ($core_update->current != $this->wp_version):
				$this->core_available = TRUE;
			endif;
		endforeach;
	}

	private function removed_ignored_plugins() {
		/*
		 * There are sometimes plugins that we want to ignore from the Nagios check,
		 * largely because there's nothing that we can do about them. For example premium
		 * plugins like Visual Composer or Rev Slider that come bundled with a theme but we
		 * can't update ourselves!
		 */

		$ignored_plugins = $this->setting('ignored_plugins');

		if (NULL !== $ignored_plugins):
			foreach ($ignored_plugins as $ignore_plugin):
				unset($this->plugins->response[$ignore_plugin]);
			endforeach;
		endif;
	}

	public function check() {
		$this->plugin_available = (count($this->plugins->response) > 0);
		$this->theme_available = (count($this->themes->response) > 0);

		$text = array();
		if ($this->core_available)
			$text[] = 'Core updates available';

		if ($this->plugin_available)
			$text[] = 'Plugin updates available';

		if ($this->theme_available)
			$text[] = 'Theme updates available';

		if ($this->core_available) {
			$this->status = 'CRITICAL';
		} elseif ($this->theme_available || $this->plugin_available) {
			$this->status = 'WARNING';
		}

		$this->text = implode(";", $text);
	}

}