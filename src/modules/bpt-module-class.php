<?php

/**
 * Handle the registration of all things the module will need.
 *
 * This should be cleaned up.
 *
 * @package brown-paper-tickets
 */

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ).'../brown-paper-tickets-plugin.php' );

use BrownPaperTickets\BPTPlugin;
use BrownPaperTickets\BptWordpress as Utilities;

/**
 * The base module class.
 */
abstract class Module {
	public $version = \BrownPaperTickets\BPT_VERSION;

	/**
	 * Give the module a name. It should start with an underscore.
	 * @var string
	 */
	public $module_name;

	/**
	 * This is set in the constructor. It comes from the base plugin class.
	 * @var string
	 */
	public $menu_slug = null;

	/**
	 * The plugin root is set to make it easier to navigate the plugin's
	 * file structure.
	 * @var string
	 */
	protected $plugin_root;

	/**
	 * The prefix to use for all settings.
	 * @var string
	 */
	protected $setting_prefix = '_bpt_';

	/**
	 * The title to use for all the sections. I think this should be removed.
	 * @var string
	 */
	public $section_title;

	/**
	 * The suffix to use. I think that $module_name does the same thing.
	 * @var string
	 */
	public $section_suffix;

	/**
	 * I don't know why this is initialized.
	 * @var \BrownPaperTickets\Module\Inputs
	 */
	private $inputs;


	/**
	 * The constructor handles adding all of the necessary actions the modules
	 * make use of.
	 */
	public function __construct() {
		$this->menu_slug = \BrownPaperTickets\PLUGIN_SLUG . '_settings';

		$this->init_actions();

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_js' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_css' ) );
			add_action( 'admin_menu', array( $this, 'load_menus' ) );
			$this->load_admin_ajax_actions();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'load_public_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_public_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_shared_js' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_shared_css' ) );

		$this->load_public_ajax_actions();
		$this->load_shortcode();
	}

	/**
	 * Get the module's name.
	 * @return [type] [description]
	 */
	public function get_module_name() {
		return $this->module_name;
	}

	/**
	 * Any thing the module needs to do right away should be called/handled here.
	 */
	public function init_actions() {

	}

	/**
	 * Load all of the sections and settings.
	 */
	public function load_settings() {
		$this->register_settings();
		$this->register_sections();
		$this->custom_functions();
	}

	/**
	 * Register and enqueue all the necessary admin js.
	 * @param  string $hook The hook of the page being loaded.
	 */
	public function load_admin_js( $hook ) {

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
		 * add_action( 'wp_ajax_bpt_get_account', array( 'BrownPaperTicketsModulesAccountAjax', 'get_account' ) );
		 */
	}

	/**
	 * Add all of the necessary public side ajax actions.
	 */
	public function load_public_ajax_actions() {
		/**
		 * Add a publicly available ajax actions.
		 * add_action( 'wp_ajax_nopriv_bpt_get_account', array( 'BrownPaperTicketsModulesAccountAjax', 'get_account' ) );
		 */
	}

	/**
	 * Add the sub menus and add the help action.
	 */
	public function load_menus() {
		/**
		 * Example: add_submenu_page(
		 *     $this->menu_slug,  //or 'options.php'
		 *     'Brown Paper Tickets ' . $this->section_title,
		 *     $this->section_title,
		 *     'manage_options',
		 *     $this->menu_slug . $this->section_suffix,
		 *     array( $this, 'render_menu' )
		 * );
		 *
		 * Example: add_action( 'load-' . $page, array( $this, 'add_help' ) );
		 */
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

	}

	/**
	 * Register all the necessary settings sections.
	 */
	public function register_sections() {

	}

	/**
	 * If this module has any settings you want to manage through WordPress' settings
	 * API, then register them here.
	 */
	public function register_settings() {

	}

	/**
	 * Not sure if this is still in use.
	 */
	public function display_settings_sections() {

	}

	/**
	 * Not sure what this is here for.
	 */
	public function custom_functions() {

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


	/**
	 * Called upon plugin activation.
	 */
	public function activate() {
		$this->set_default_setting_values();
	}

	/**
	 * Called upon plugin deactivation.
	 */
	public function deactivate() {
		$this->remove_setting_values();
	}

	/**
	 * Render the modules menu that you've set.
	 */
	public function render_menu() {
		// Example: require_once( __DIR__ . '/some/template-file.php' );.
	}

	/**
	 * Add a help section to the module's page.
	 */
	public function add_help() {

	}

	/**
	 * Determines whether or not this module's menu is being loaded.
	 * @param  string $hook The page's hook.
	 * @return bool
	 */
	public function is_current_menu($hook) {
		if ( 'bpt-settings_page_brown_paper_tickets_settings' . $this->module_name === $hook ) {
			return true;
		}

		return false;
	}

	public function plugin_root() {
		return dirname( __DIR__ );
	}
}
