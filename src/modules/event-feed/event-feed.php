<?php namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '/event-feed-ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-feed-type.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-feed-manager.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-feed-widget.php' );

/**
 * The Event List class sets the options for the EventList.
 */
class EventFeed extends \BrownPaperTickets\Modules\Module {

	public $module_name = '_event_feed';

	private $ajax;

	private $display_section_title  = 'Event Feed Display Settings';

	public function __construct() {
		$this->module_name = '_event_feed';
		$this->ajax = new EventFeed\Ajax();
		$this->event_feed_type = new EventFeed\EventFeedType();
		parent::__construct();
	}

    public function init_actions() {
        add_action( 'widgets_init', array( $this, 'load_widgets' ) );
    }

	/**
	 * Register and enqueue all the necessary admin js.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_admin_js( $hook ) {
		global $post;

		if ($post && $post->post_type === 'bpt_event_feed') {
			wp_enqueue_script(
				\BrownPaperTickets\PLUGIN_SLUG . '_event_feed',
				plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
				[ 'jquery' ],
				\BrownPaperTickets\BPT_VERSION,
				true
			);

			wp_localize_script(
				\BrownPaperTickets\PLUGIN_SLUG . '_event_feed',
				\BrownPaperTickets\PLUGIN_SLUG . '_event_feed_params',
				array(
					'url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce('event-feed'),
					'post' => json_encode($post),
				)
			);
		}
	}

	/**
	 * Register and enqueue any necessary public js.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_public_js( $hook ) {

	}

	/**
	 * Register and enqueue any necessary admin css.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_admin_css( $hook ) {

	}

	/**
	 * Register and enqueue any necessary public css.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_public_css( $hook ) {

	}

	/**
	 * Register and enqueue any necessary js that will be shared between sides.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_shared_js( $hook ) {

	}

	/**
	 * Register and enqueue any necessary css that will be shared between sides.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_shared_css( $hook ) {

	}

	/**
	 * Add all of the necessary admin side ajax actions.
	 */
	public function load_admin_ajax_actions() {
		/**
		 * Add an admin available ajax actions.
		 */
		add_action( 'wp_ajax_bpt_get_feed_events', array( 'BrownPaperTickets\Modules\EventFeed\Ajax', 'get_feed_events' ) );
	}

	/**
	 * Add all of the necessary public side ajax actions.
	 */
	public function load_public_ajax_actions() {
		/**
		 * Add a publicly available ajax actions.
		 */
		add_action( 'wp_ajax_nopriv_bpt_get_feed_events', array( 'BrownPaperTickets\Modules\EventFeed\Ajax', 'get_feed_events' ) );
	}

	/**
	 * Add the sub menus and add the help action.
	 */
	public function load_menus() {
		// $page = add_submenu_page(
		// 	$this->menu_slug,  // Or 'options.php'.
		// 	'Brown Paper Tickets Event Feed',
		// 	'Event Feed',
		// 	'manage_options',
		// 	$this->menu_slug . $this->module_name,
		// 	array( $this, 'render_menu' )
		// );
		//
		// add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	/**
	 * Load the shortcodes.
	 */
	public function load_shortcode() {

	}

	/**
	 * Load any widgets.
	 */
	public function load_widgets() {
        register_widget( '\BrownPaperTickets\Modules\EventFeed\Widget' );
	}

	/**
	 * Register all the necessary settings sections.
	 */
	public function register_sections() {
		add_settings_section(
			$this->section_title,
			$this->section_title,
			array( 'BrownPaperTickets\Modules\AttendeeList\Menu', 'render' ),
			$this->menu_slug . $this->module_name
		);
	}

	/**
	 * If this module has any settings you want to manage through WordPress' settings
	 * API, then register them here.
	 */
	public function register_settings() {

	}

	/**
	 * If you're using any settings and you need those settings to have default
	 * values set upon plugin activation, do that here.
	 */
	public function set_default_setting_values() {

	}

	/**
	 * Remove all setting values. This is called when plugin is deactivated.
	 */
	public function remove_setting_values() {

	}
}
