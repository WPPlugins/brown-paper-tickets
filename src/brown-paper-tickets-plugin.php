<?php
/**
 * Brown Paper Tickets
 */

namespace BrownPaperTickets;

require_once( plugin_dir_path( __FILE__ ) . '../lib/bptWordpress.php' );

/**
 * Load Modules
 *
 * @todo Switch over to some autoloader.
 */
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/cache/cache.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/account/account.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/appearance/appearance.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/purchase/purchase.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/event-list/event-list.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/calendar/calendar.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/setup-wizard/setup-wizard.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/dashboard/dashboard.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/attendee-list/attendee-list.php' );
require_once( plugin_dir_path( __FILE__ ) . '../src/modules/event-feed/event-feed.php' );

use BrownPaperTickets\BPTSettingsFields;
use BrownPaperTickets\BPTAjaxActions;
use BrownPaperTickets\BPTWidgets;
use BrownPaperTickets\BptWordpress as Utilities;

const BPT_VERSION = '0.7.3';
const PLUGIN_SLUG = 'brown_paper_tickets';
const TEXT_DOMAIN = 'brown-paper-tickets-plugin';

class BPTPlugin {
	protected static $instance = null;

	public $menu_slug;
	public $plugin_root;

	protected $settings_fields;

	protected $plugin_slug;

	protected $modules = array();
	protected $plugin_version;

	public function __construct() {

		$this->plugin_slug = PLUGIN_SLUG;

		$this->menu_slug = PLUGIN_SLUG . '_settings';

		$this->plugin_version = BPT_VERSION;

		$this->plugin_root = dirname( __FILE__ );

		$this->load_shared();

		if ( is_admin() ) {
			$this->load_admin();
		}

		$this->modules['dashboard'] = new Modules\Dashboard();
		$this->modules['account'] = new Modules\Account();
		$this->modules['appearance'] = new Modules\Appearance();
		$this->modules['cache'] = new Modules\Cache();
		$this->modules['calendar'] =  new Modules\Calendar();
		$this->modules['event_list'] = new Modules\EventList();
		$this->modules['purchase'] = new Modules\Purchase();
		$this->modules['attendee_list'] = new Modules\AttendeeList();
		$this->modules['setup_wizard'] = new Modules\SetupWizard();
		// $this->modules['event_feed'] = new Modules\EventFeed();
	}

	/**
	 * Get an instance of the main plugin.
	 * @return BrownPaperTickets\BPTPlugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	public function get_plugin_version() {
		return $this->plugin_version;
	}

	public function get_menu_slug() {
		return $this->menu_slug;
	}

	public function get_modules() {
		return $this->modules;
	}

	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( version_compare( PHP_VERSION, '5.3', '<' ) && is_admin() ) {
			exit( 'Sorry, the Brown Paper Tickets plugin requires PHP version 5.3 or higher but you are using '. PHP_VERSION . '. Please contact your hosting provider for more info.' );
		}

		if ( ! get_option( '_bpt_dev_id' ) && ! get_option( '_bpt_client_id' ) ) {
			update_option( '_bpt_show_wizard', 'true' );

			foreach (self::get_instance()->get_modules() as $module) {
				$module->activate();
			}
		}
	}

	public static function deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Important: Check if the file is the one
		// that was registered during the uninstall hook.
		if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
			return;
		}
	}

	public function uninstall() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		check_admin_referer( 'delete-selected' );

		// Important: Check if the file is the one
		// that was registered during the uninstall hook.
		if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
			return;
		}

		self::delete_options();
	}

	public function load_admin() {
		add_action( 'admin_init', array( $this, 'bpt_show_wizard' ) );
		add_action( 'admin_menu', array( $this, 'create_bpt_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	}

	public function load_shared() {
		add_action( 'init', array( $this, 'register_js_libs' ) );
		add_action( 'init', array( $this, 'register_css_libs' ) );
	}

	public function load_admin_scripts( $hook ) {

	}

	public function register_js_libs()
	{
		wp_register_script( 'ractive_js', plugins_url( '/public/assets/js/lib/ractive.min.js', dirname( __FILE__ ) ), array(), false, true );
		wp_register_script( 'ractive_transitions_slide_js', plugins_url( '/public/assets/js/lib/ractive-transitions-slide.js', dirname( __FILE__ ) ), array( 'ractive_js' ), false, true );
		wp_register_script( 'ractive_transitions_fade_js', plugins_url( '/public/ass2ets/js/lib/ractive-transitions-fade.js', dirname( __FILE__ ) ), array( 'ractive_js' ), false, true );
		wp_register_script( 'moment_with_langs_min', plugins_url( '/public/assets/js/lib/moment-with-langs.min.js', dirname( __FILE__ ) ), array(), false, true );
		wp_register_script( 'clndr_min_js', plugins_url( 'public/assets/js/lib/clndr.min.js', dirname( __FILE__ ) ), array( 'underscore', 'jquery' ), false, true );
		wp_register_script( 'bpt', plugins_url( 'public/assets/js/lib/bpt.min.js', dirname( __FILE__ ) ), array(), false, true );
	}

	public function register_css_libs()
	{
		wp_register_style(
			'bpt_admin_css',
			plugins_url( '/admin/assets/css/bpt-admin.css', dirname( __FILE__ ) ),
			null,
			BPT_VERSION
		);
	}

	public function create_bpt_settings() {
		foreach ( $this->modules as $module ) {
			$module->load_settings();
		}
	}

	public function bpt_show_wizard() {
		if ( get_option( '_bpt_show_wizard' ) === 'true' ) {
			if ( ! is_multisite() ) {
				update_option( '_bpt_show_wizard', 'false' );
				wp_redirect( 'admin.php?page=brown_paper_tickets_settings_setup_wizard' );
			}
		}
	}

	/**
	 * Delete all _bpt_ options directly from the database.
	 */
	private function delete_options() {

		global $wpdb;

		$bpt_options = $wpdb->get_results(
			'SELECT *
			FROM `wp_options`
			WHERE `option_name` LIKE \'%_bpt_%\'',
			OBJECT
		);

		if ( ! empty( $bpt_options ) ) {

			foreach ( $bpt_options as $bpt_option ) {

				$option_name = $bpt_option->option_name;

				delete_option( $option_name );
			}
		}
	}

}
