<?php

namespace BrownPaperTickets\Modules;
require_once( plugin_dir_path( __FILE__ ).'../bpt-module-class.php' );

class SetupWizard extends Module {

	public $module_name = '_setup_wizard';

	public function load_menus() {
		$page = add_submenu_page(
			null,  //or 'options.php'
			'Brown Paper Tickets Setup Wizard',
			'Setup Wizard',
			'manage_options',
			$this->menu_slug . '_setup_wizard',
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function add_help() {

	}

	public function render_menu() {
		require_once( __DIR__ . '/setup-wizard-menu.php' );
	}


	public function load_admin_js($hook) {

		if ( $hook !== 'admin_page_brown_paper_tickets_settings_setup_wizard' ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );

		// wp_enqueue_style( 'bpt_setup_wizard_css', plugins_url( 'assets/css/setup-wizard.css', __FILE__ ) );

		wp_enqueue_script(
			'bpt_setup_wizard_js',
			plugins_url( 'assets/js/setup-wizard.js', __FILE__ ),
			array(
				'jquery',
				'ractive_js',
				'ractive_transitions_slide_js',
				'moment_with_langs_min',
			),
			true,
			true
		);

		wp_localize_script(
			'bpt_setup_wizard_js', 'bptSetupWizardAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'bpt-account' ),
			)
		);
	}
}
