<?php
/**
 * This handles all the actions/hooks and other various WP related functions for
 * the AttendeeList.
 *
 * @package brown-paper-tickets
 */

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'attendee-list-ajax.php' );

/**
 * This class handles all of the Attendee List stuff.
 */
class AttendeeList extends \BrownPaperTickets\Modules\Module {


	public function __construct() {
		$this->module_name = '_attendee_list';

		parent::__construct();
	}

	/**
	 * Give the section a unique suffixe.
	 * @var string
	 */
	public $module_name = '_attendee_list';

	/**
	 * This module has just one section. Give it a name.
	 * @var string
	 */
	public $section_title = 'Attendees';

	/**
	 * Register the sections.
	 */
	public function register_sections() {

		add_settings_section(
			$this->section_title,
			$this->section_title,
			array( 'BrownPaperTickets\Modules\AttendeeList\Menu', 'render' ),
			$this->menu_slug . $this->module_name
		);
	}

	public function load_menus() {
		$page = add_submenu_page(
			$this->menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Attendee List',
			'Attendee List',
			'manage_options',
			$this->menu_slug . $this->module_name,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function render_menu() {
		require_once( __DIR__ . '/assets/attendee-list.php' );
	}

	public function add_help() {
		// $screen->add_help_tab( array(
		// 	'id' => 'bpt-apperance-event-list-help',
		// 	'title' => 'Event List Selectors',
		// 	'callback' => array( $this->inputs, 'event_list_help' ),
		// ) );
		//
		// $screen->add_help_tab( array(
		// 	'id' => 'bpt-apperance-calendar-help',
		// 	'title' => 'Calendar Selectors',
		// 	'callback' => array( $this->inputs, 'calendar_help' ),
		// ) );
	}


	/**
	 * Add all the actions for the admin side.
	 */
	public function load_admin_ajax_actions() {
		$ajax = new AttendeeList\Ajax();

		add_action(
			'wp_ajax_bpt_attendee_list_get_events',
			array( $ajax, 'get_events' )
		);

		add_action(
			'wp_ajax_bpt_attendee_list_get_attendees',
			array( $ajax, 'get_attendees' )
		);
	}

	public function load_admin_js( $hook ) {

		// if ( 'bpt-settings_page_brown_paper_tickets_settings_attendee_list' !== $hook ) {
		// 	return;
		// }
		//
		if ( ! $this->is_current_menu( $hook ) ) {
			return;
		}

		$localized_variables = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bpt-attendee-list-nonce' ),
			'dateFormat' => get_option( $this->setting_prefix . 'date_format' ),
			'timeFormat' => get_option( $this->setting_prefix . 'time_format' ),
		);

		wp_register_style(
			'attendee_list_css',
			plugins_url( '/assets/css/attendee-list.css', __FILE__ )
		);

		wp_register_script(
			'attendee_list_js',
			plugins_url( '/assets/js/attendee-list.js', __FILE__ ),
			array( 'ractive_js', 'jquery', 'moment_with_langs_min' ),
			null,
			true
		);

		wp_localize_script(
			'attendee_list_js',
			'bptAttendeeList',
			$localized_variables
		);

		wp_enqueue_script( 'attendee_list_js' );
		wp_enqueue_style( 'attendee_list_css' );
	}
}
