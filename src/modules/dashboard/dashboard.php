<?php

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ) . '/dashboard-ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . '/dashboard-inputs.php' );
require_once( plugin_dir_path( __FILE__ ) . '/dashboard-help.php' );

class Dashboard extends Module {
	public $module_name;
	public $ajax;
	public $help;

	public function __construct() {
		$this->module_name = '_dashboard';
		$this->ajax = new Dashboard\Ajax();
		$this->help = new Dashboard\Help();

		parent::__construct();
	}

	public function load_admin_ajax_actions() {
		add_action(
			'wp_ajax_bpt_dashboard_get_account', array( $this->ajax, 'get_account' )
		);

		add_action(
			'wp_ajax_bpt_dashboard_get_all_options', array( $this->ajax, 'get_all_options' )
		);
	}

	public function load_menus() {
		add_menu_page(
			'Brown Paper Tickets',
			'BPT Settings',
			'administrator',
			$this->menu_slug,
			array( $this, 'render_menu' ),
			'dashicons-tickets-alt'
		);

		$page = add_submenu_page(
			$this->menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Dashboard',
			'Dashboard',
			'manage_options',
			$this->menu_slug,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function render_menu() {
		require_once( __DIR__ . '/dashboard-menu.php' );
	}

	public function add_help() {
		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id' => 'dashboard-debug',
			'title' => 'Debug',
			'callback' => array( $this->help, 'debug' ),
		) );

		$screen->add_help_tab( array(
			'id' => 'dashboard-setup-wizard',
			'title' => 'Setup Wizard',
			'callback' => array( $this->help, 'setup_wizard' ),
		) );

		$screen->add_help_tab( array(
			'id' => 'dashboard-saved-options',
			'title' => 'Saved Options',
			'callback' => array( $this->help, 'saved_options' ),
		) );

		$screen->add_help_tab( array(
			'id' => 'dashboard-credits',
			'title' => 'Credits',
			'callback' => array( $this->help, 'credits' ),
		) );
	}

	public function load_admin_css($hook) {
		// Check the hook string manually.
		if ( $hook !== 'toplevel_page_brown_paper_tickets_settings' ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );
	}

	public function load_admin_js($hook) {
		// Check the hook string manually.
		if ( $hook !== 'toplevel_page_brown_paper_tickets_settings' ) {
			return;
		}

		wp_register_script(
			'bpt_dashboard_js',
			plugins_url( '/assets/js/dashboard.js', __FILE__ ),
			array( 'ractive_js', 'jquery' ),
			null,
			true
		);

		$localized_variables = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bpt-dashboard' ),
		);

		wp_localize_script(
			'bpt_dashboard_js',
			'bptDashboard',
			$localized_variables
		);

		wp_enqueue_script( 'bpt_dashboard_js' );
	}
}
