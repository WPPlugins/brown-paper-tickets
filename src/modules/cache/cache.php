<?php

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ).'../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ).'/cache-ajax.php' );
require_once( plugin_dir_path( __FILE__ ).'/cache-inputs.php' );

class Cache extends Module {

	public $module_name = '_cache';

	public function register_sections() {

		$section_title  = 'Cache Settings';

		$inputs = new Cache\Inputs();

		// Register the cached_data array. This is to keep track of the data we have cached.

		add_settings_section( $section_title, null, null, $this->menu_slug . $this->module_name );

		add_settings_field(
			$this->setting_prefix . 'cache_time',
			'Cache Settings',
			array( $inputs, 'cache_time' ),
			$this->menu_slug . $this->module_name,
			$section_title
		);
	}

	public function load_admin_ajax_actions() {
		add_action( 'wp_ajax_bpt_delete_cache', array( 'BrownPaperTickets\Modules\General\Ajax', 'delete_cache' ) );
	}

	public function register_settings() {
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'cached_data' );
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'show_wizard' );
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'cache_time' );
		register_setting( $this->menu_slug . $this->module_name, $this->setting_prefix . 'cache_unit' );
	}

	public function set_default_setting_values() {
		update_option( $setting_prefix . '_bpt_cache_time', null );
	}


	public function load_menus() {
		$page = add_submenu_page(
			$this->menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Cache Settings',
			'Cache',
			'manage_options',
			$this->menu_slug . $this->module_name,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function render_menu() {
		require_once( __DIR__ . '/cache-menu.php' );
	}

	public function add_help() {

	}

	public function load_admin_js($hook) {

		if ( 'bpt-settings_page_brown_paper_tickets_settings_cache' !== $hook ) {
			return;
		}

		$localized_variables = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bpt-cache-nonce' ),
		);

		wp_register_script(
			'bpt_cache_js',
			plugins_url( '/assets/js/cache.js', __FILE__ ),
			array( 'jquery' ),
			null,
			true
		);

		wp_localize_script(
			'bpt_cache_js',
			'bptCache',
			$localized_variables
		);

		wp_enqueue_script( 'bpt_cache_js' );
	}

	public function load_admin_css($hook) {
		if ( 'bpt-settings_page_brown_paper_tickets_settings_cache' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );
	}
}
