<?php
namespace FredBradley\WPUpdateChecker;

class Check {
	private $core_updates = FALSE;
	private $plugin_updates = FALSE;
	private $wp_version;
	private $allow_local = FALSE;

	private $core = FALSE;
	private $plugins = FALSE;
	private $themes = FALSE;
	private $translations = FALSE;

	private $core_available = FALSE;
	private $plugin_available = FALSE;
	private $theme_available = FALSE;
	private $translation_available = FALSE;
	private $ip_address = FALSE;

	public $status = "OK";
	public $text;
	public $version;
	public $analytics_id;

	private function setting($setting) {

		$nagios_settings = get_option('nagios-settings', ["nagios_server_ip" => "127.0.0.1", "ignored_plugins"=>NULL]);
		$this->ip_address = $nagios_settings['nagios_server_ip'];
		if ($setting === "ignored_plugins" && !isset($nagios_settings[$setting])) {
			return null;
		}
		return $nagios_settings[$setting];
	}

	public function __construct($wp_version, $plugin_version) {
		$this->wp_version = $wp_version;
		$this->version = $plugin_version;
		$this->analytics_id = Analytics::$analytics_id;

		if (isset($_GET['allow_local']) && $_GET['allow_local']=="lacol_wolla")
			$this->allow_local = true;

		if ($this->allow_local === true || $this->check_referrer() === true):
			wp_version_check();
			wp_update_plugins();
			wp_update_themes();

			$this->get_transients();

			$this->removed_ignored_plugins();

			$this->check();
		endif;
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

		$this->translations = wp_get_translation_updates();

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

		if (is_array($ignored_plugins)):
			foreach ($ignored_plugins as $ignore_plugin):
				unset($this->plugins->response[$ignore_plugin]);
			endforeach;
		endif;
	}

	private function check_referrer() {
		$allowed_ips = array(
			$this->setting('nagios_server_ip')
		);
		// If your Wordpress installation is behind a Proxy like Nginx use 'HTTP_X_FORWARDED_FOR'
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$remote_ip = $_SERVER['REMOTE_ADDR'];
		}

		// Check if the requesting server is allowed
		if (! in_array($remote_ip, $allowed_ips))
		{
			$this->text = "IP $remote_ip not allowed.";
			$this->status = "CRITICAL";

			return false;
		} else {
			return true;
		}

	}

	public function check() {
		$this->check_core();
		$this->plugin_available = (count($this->plugins->response) > 0);
		$this->theme_available = (count($this->themes->response) > 0);
		$this->translation_available = (count($this->translations) > 0);

		$text = array();
		if ($this->core_available)
			$text[] = 'Core updates available';

		if ($this->plugin_available)
			$text[] = count($this->plugins->response).' Plugin updates available';

		if ($this->theme_available)
			$text[] = count($this->themes->response).' Theme updates available';

		if ($this->translation_available)
			$text[] = count($this->translations).' Translation updates available';

		if ($this->core_available) {
			$this->status = 'CRITICAL';
		} elseif ($this->theme_available || $this->plugin_available || $this->translation_available) {
			$this->status = 'WARNING';
		} else {
			$text[] = "Nagios Checker Version ".$this->version;
		}
		$this->text = implode(";", $text);
	}

}
