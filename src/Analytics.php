<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 26/09/2017
 * Time: 14:13
 */

namespace FredBradley\WPUpdateChecker;


class Analytics {

	public static $analytics_id = "UA-24018806-32";

	public static function run( string $analytics_id=null ) {
		if ($analytics_id !== null)
			self::$analytics_id = $analytics_id;

		add_action('wp_head', array(__CLASS__, 'googleanalytics'));
		add_action( 'admin_head', array(__CLASS__, 'googleanalytics'));
	}

	public static function googleanalytics() {
		if (is_admin()) {
			$loadType = "adminLoad";
		} else {
			$loadType = "frontendLoad";
		}
		
	?>
		<!-- Global Site Tag (gtag.js) - Google Analytics -->
		<script async src="//www.googletagmanager.com/gtag/js?id=<?php echo self::$analytics_id; ?>"></script>
		<script>
			var url = '<?php echo $_SERVER['REQUEST_URI']; ?>';
			var loadType = '<?php echo $loadtype; ?>';
			
			window.dataLayer = window.dataLayer || [];
			function frbtagnagios(){dataLayer.push(arguments)};
			frbtagnagios('js', new Date());

			frbtagnagios('config', '<?php echo self::$analytics_id; ?>');
			frbtagnagios('event', loadType, {
				'event_category': "<?php echo get_bloginfo('name'); ?>",
				'event_label': url,
				'transport_type': 'beacon'
			});
		</script>
		<?php
	}
}
