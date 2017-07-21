<?php
/**
 * This file is is the main module file.
 *
 * This class extends the main module class which upon construction will register
 * all the necessary actions.
 *
 * @package brown-paper-tickets
 */

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-inputs.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-help.php' );

class Example extends Module {

	/**
	 * The name of the module. It should be preceeded by an underscore.
	 * @var string
	 */
	public static $module_name = '_example';

	/**
	 * Add all of the necessary admin side ajax actions.
	 */
	public function load_admin_ajax_actions() {
		add_action( 'wp_ajax_bpt_get_account', array( 'BrownPaperTickets\Modules\Account\Ajax', 'get_account' ) );
		add_action( 'wp_ajax_bpt_test_account', array( 'BrownPaperTickets\Modules\Account\Ajax', 'test_account' ) );
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
	 * This function will register all the necessary settings sections.
	 */
	public function register_sections() {

		$section_title  = 'API Credentials';

		$inputs = new Account\Inputs();

		add_settings_section(
			$section_title,
			null,
			array( $inputs, 'section' ),
			self::$menu_slug . self::$module_name
		);

		add_settings_field(
			self::$setting_prefix . 'dev_id' . self::$module_name,
			'Developer ID',
			array( $inputs, 'developer_id' ),
			self::$menu_slug . self::$module_name,
			$section_title
		);

		add_settings_field(
			self::$setting_prefix . 'client_id' . self::$module_name,
			'Client ID',
			array( $inputs, 'client_id' ),
			self::$menu_slug . self::$module_name,
			$section_title
		);
	}

	/**
	 * If this module has any settings you want to manage through WordPress' settings
	 * API, then register them here.
	 */
	public function register_settings() {
		register_setting( self::$menu_slug . self::$module_name, self::$setting_prefix . 'dev_id' );
		register_setting( self::$menu_slug . self::$module_name, self::$setting_prefix . 'client_id' );
	}

	/**
	 * Add your menu pages and register the add_help method when the page is
	 * loaded.
	 */
	public function load_menus() {
		$page = add_submenu_page(
			self::$menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Account',
			'Account',
			'manage_options',
			self::$menu_slug . self::$module_name,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	/**
	 * Render the menu added in {@link load_menus}
	 */
	public function render_menu() {
		require_once( __DIR__ . '/account-menu.php' );
	}

	/**
	 * Add the help menu
	 */
	public function add_help() {
		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id' => 'example-help',
			'title' => 'An Example Module',
			'callback' => array( 'BrownPaperTickets\Modules\Example\Help', 'example_help' ),
		) );
	}

	/**
	 * Register and enqueue any CSS that you need on the admin side.
	 * Check to see if the hook matches the page being loaded.
	 *
	 * @param string $hook The page currently being loaded's hook.
	 */
	public function load_admin_css($hook) {

		if ( 'bpt-settings_page_brown_paper_tickets_settings' . self::$module_name !== $hook ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );
	}

	/**
	 * Register and enqueue any CSS that you need on the public side.
	 * Check to see if the hook matches the page being loaded.
	 *
	 * @param string $hook The page currently being loaded's hook.
	 */
	public function load_admin_js($hook) {

		if ( 'bpt-settings_page_brown_paper_tickets_settings_api' !== $hook ) {
			return;
		}

		$localized_variables = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bpt-account' ),
		);

		wp_register_script(
			'account_js',
			plugins_url( '/assets/js/account.js', __FILE__ ),
			array( 'ractive_js', 'jquery' ),
			null,
			true
		);

		wp_localize_script(
			'account_js',
			'bptAccount',
			$localized_variables
		);

		wp_enqueue_script( 'account_js' );
	}
}
