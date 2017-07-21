<?php

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-inputs.php' );
require_once( plugin_dir_path( __FILE__ ) . '/account-help.php' );

class Account extends Module {

	public $module_name;

	public function __construct() {
		$this->module_name = '_api';

		parent::__construct();
	}

	public function load_admin_ajax_actions() {
		add_action( 'wp_ajax_bpt_get_account', array( 'BrownPaperTickets\Modules\Account\Ajax', 'get_account' ) );
		add_action( 'wp_ajax_bpt_test_account', array( 'BrownPaperTickets\Modules\Account\Ajax', 'test_account' ) );
	}

	public function load_public_ajax_actions() {
		// Not sure if I need this?
		// add_action( 'wp_ajax_nopriv_bpt_get_account', array( 'BrownPaperTickets\Modules\Account\Ajax', 'get_account' ) );
	}

	public function register_sections() {

		$section_title  = 'API Credentials';

		$inputs = new Account\Inputs();

		add_settings_section(
			$section_title,
			null,
			array( $inputs, 'section' ),
			$this->menu_slug . $this->module_name
		);

		add_settings_field(
			$this->setting_prefix . 'dev_id' . $this->module_name,
			'Developer ID',
			array( $inputs, 'developer_id' ),
			$this->menu_slug . $this->module_name,
			$section_title
		);

		add_settings_field(
			$this->setting_prefix . 'client_id' . $this->module_name,
			'Client ID',
			array( $inputs, 'client_id' ),
			$this->menu_slug . $this->module_name,
			$section_title
		);
	}

	public function register_settings() {
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'dev_id' );
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'client_id' );
	}

	public function load_menus() {
		$page = add_submenu_page(
			$this->menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Account',
			'Account',
			'manage_options',
			$this->menu_slug . $this->module_name,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function render_menu() {
		require_once( __DIR__ . '/account-menu.php' );
	}

	public function add_help() {
		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id' => 'account-dev-id-help',
			'title' => 'Add Developer Tools',
			'callback' => array( 'BrownPaperTickets\Modules\Account\Help', 'add_developer_tools' ),
		) );

		$screen->add_help_tab( array(
			'id' => 'bpt-authorized-accounts-help',
			'title' => 'Authorized Accounts',
			'callback' => array( 'BrownPaperTickets\Modules\Account\Help', 'authorized_accounts' ),
		) );

		$screen->add_help_tab( array(
			'id' => 'bpt-client-id-help',
			'title' => 'Client ID Issues',
			'callback' => array( 'BrownPaperTickets\Modules\Account\Help', 'client_id' ),
		) );
	}

	public function load_admin_css( $hook ) {

		if ( ! $this->is_current_menu( $hook ) ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );
	}

	public function load_admin_js($hook) {

		if (  ! $this->is_current_menu( $hook ) ) {
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
