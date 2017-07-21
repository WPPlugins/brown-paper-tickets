<?php

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ) . '../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-list-inputs.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-list-ajax.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-list-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . '/event-list-help.php' );


/**
 * The Event List class sets the options for the EventList.
 */
class EventList extends \BrownPaperTickets\Modules\Module {

	public $module_name = '_event';

	private $ajax;

	private $event_section_title  = 'Event Display Settings';
	private $date_section_title  = 'Date Display Settings';
	private $price_section_title = 'Price Display Settings';
	private $hidden_prices_section_title  = 'Password Protected Prices';

	public function __construct() {
		$this->module_name = '_event';
		$this->ajax = new EventList\Ajax();

		parent::__construct();
	}

	public function load_shortcode() {
		add_shortcode( 'list-event', array( 'BrownPaperTickets\Modules\EventList\Shortcode', 'list_event_shortcode' ) );
		add_shortcode( 'list_event', array( 'BrownPaperTickets\Modules\EventList\Shortcode', 'list_event_shortcode' ) );

		add_shortcode( 'list-events', array( 'BrownPaperTickets\Modules\EventList\Shortcode', 'list_event_shortcode' ) );
		add_shortcode( 'list_events', array( 'BrownPaperTickets\Modules\EventList\Shortcode', 'list_event_shortcode' ) );
	}

	public function register_settings() {

		// Event Settings
		//
		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_non_live_events'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_location_after_description'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_full_description'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'sort_events'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'credit_cards_displayed'
		);

		// Date Settings
		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_dates'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'date_format'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'time_format'
		);

		// custom_date_field is registered but it doesn't have a settings filed added.
		// That is added manually in the settings-fields.
		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'custom_date_format'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'custom_time_format'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_sold_out_dates'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_past_dates'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_end_time'
		);

		// Price Settings
		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_prices'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'shipping_methods'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'shipping_countries'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'currency'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'price_sort'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'show_sold_out_prices'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'include_service_fee'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'price_max_quantity'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'hidden_prices'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'price_intervals'
		);

		register_setting(
			$this->menu_slug . $this->module_name,
			$this->setting_prefix . 'price_include_fee'
		);
	}

	public function register_sections() {

		$input = new EventList\Inputs;

		add_settings_section(
			$this->event_section_title,
			$this->event_section_title,
			array($input, 'section'),
			$this->menu_slug . $this->module_name
		);

		add_settings_section(
			$this->date_section_title,
			$this->date_section_title,
			null,
			$this->menu_slug . $this->module_name
		);

		add_settings_section(
			$this->price_section_title,
			$this->price_section_title,
			null,
			$this->menu_slug . $this->module_name
		);

		add_settings_section(
			$this->hidden_prices_section_title,
			$this->hidden_prices_section_title,
			null,
			$this->menu_slug . $this->module_name
		);

		// Add the settings fields.
		// Event Fields

		add_settings_field(
			$this->setting_prefix . 'show_non_live_events',
			'Show Non-Live Events',
			array( $input, 'show_non_live_events'),
			$this->menu_slug . $this->module_name, $this->event_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_full_description',
			'Display Full Description by Default',
			array( $input, 'show_full_description' ),
			$this->menu_slug . $this->module_name, $this->event_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_location_after_description',
			'Display Location After Description',
			array( $input, 'show_location_after_description' ),
			$this->menu_slug . $this->module_name, $this->event_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'sort_events',
			'Sort Events',
			array( $input, 'sort_events' ),
			$this->menu_slug . $this->module_name, $this->event_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'credit_cards_displayed',
			'Display Credit Card Icons',
			array( $input, 'credit_cards_displayed' ),
			$this->menu_slug . $this->module_name, $this->event_section_title
		);

		// Date Fields

		add_settings_field(
			$this->setting_prefix . 'show_dates',
			'Display Dates',
			array( $input, 'show_dates' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_past_dates',
			'Display Past Dates',
			array( $input, 'show_past_dates' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_end_time',
			'Display Event End Time',
			array( $input, 'show_end_time' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_sold_out_dates',
			'Display Sold Out Dates',
			array( $input, 'show_sold_out_dates' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'date_format',
			'Date Format',
			array( $input, 'date_format' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'time_format',
			'Time Format',
			array( $input, 'time_format' ),
			$this->menu_slug . $this->module_name, $this->date_section_title
		);

		// Price Fields
		add_settings_field(
			$this->setting_prefix . 'show_prices',
			'Display Prices',
			array( $input, 'show_prices' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'show_sold_out_prices',
			'Display Sold Out Prices',
			array( $input, 'show_sold_out_prices' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'shipping_methods',
			'Shipping Methods',
			array( $input, 'shipping_methods' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'shipping_countries',
			'Default Shipping Country',
			array( $input, 'shipping_countries' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'currency',
			'Currency',
			array( $input, 'currency' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'price_sort',
			'Price Sort',
			array( $input, 'price_sort' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'include_service_fee',
			'Include Service Fee',
			array( $input, 'include_service_fee' ),
			$this->menu_slug . $this->module_name, $this->price_section_title
		);

		add_settings_field(
			$this->setting_prefix . 'hidden_prices',
			'Hidden Prices',
			array( $input, 'hidden_prices' ),
			$this->menu_slug . $this->module_name, $this->hidden_prices_section_title
		);
	}

	public function set_default_setting_values() {

		update_option( $this->setting_prefix . 'show_non_live_events', 'false' );
		update_option( $this->setting_prefix . 'show_full_description', 'false' );
		update_option( $this->setting_prefix . 'show_location_after_description', 'false' );
		update_option( $this->setting_prefix . 'credit_cards_displayed', array( 'visa', 'mc', 'discover', 'amex' ) );

		// Date Settings.
		update_option( $this->setting_prefix . 'show_dates', 'true' );
		update_option( $this->setting_prefix . 'date_format', 'MMMM Do, YYYY' );
		update_option( $this->setting_prefix . 'time_format', 'hh:mm A' );
		update_option( $this->setting_prefix . 'show_sold_out_dates', 'false' );
		update_option( $this->setting_prefix . 'show_past_dates', 'false' );
		update_option( $this->setting_prefix . 'show_end_time', 'true' );

		// Price Settings.
		update_option( $this->setting_prefix . 'show_prices', 'true' );
		update_option( $this->setting_prefix . 'shipping_methods', array( 'print_at_home', 'will_call' ) );
		update_option( $this->setting_prefix . 'shipping_countries', 'United States' );
		update_option( $this->setting_prefix . 'currency', 'usd' );
		update_option( $this->setting_prefix . 'price_sort', 'value_asc' );
		update_option( $this->setting_prefix . 'show_sold_out_prices', 'false' );
		update_option( $this->setting_prefix . 'include_service_fee', 'false' );

		update_option( $this->setting_prefix . 'hidden_prices', array() );

		update_option( $this->setting_prefix . 'price_max_quantity', array() );
		update_option( $this->setting_prefix . 'price_intervals', array() );
		update_option( $this->setting_prefix . 'price_include_fee', array() );
	}

	public function load_admin_ajax_actions() {

		add_action(
			'wp_ajax_bpt_set_price_max_quantity',
			array( $this->ajax, 'set_price_max_quantity' )
		);

		add_action(
			'wp_ajax_bpt_hide_prices',
			array( $this->ajax, 'hide_prices' )
		);

		add_action(
			'wp_ajax_bpt_unhide_prices',
			array( $this->ajax, 'unhide_prices' )
		);

		add_action(
			'wp_ajax_bpt_get_events',
			array( $this->ajax, 'get_events' )
		);

		add_action(
			'wp_ajax_bpt_set_price_intervals',
			array( $this->ajax, 'set_price_intervals' )
		);

		add_action(
			'wp_ajax_bpt_set_price_include_fee',
			array( $this->ajax, 'set_price_include_fee' )
		);
	}

	public function load_public_ajax_actions() {
		add_action(
			'wp_ajax_nopriv_bpt_get_events', array( $this->ajax, 'get_events' )
		);
	}

	public function load_menus() {
		$page = add_submenu_page(
			$this->menu_slug,  // Or 'options.php'.
			'Brown Paper Tickets Event List Settings',
			'Event List',
			'manage_options',
			$this->menu_slug . $this->module_name,
			array( $this, 'render_menu' )
		);

		add_action( 'load-' . $page, array( $this, 'add_help' ) );
	}

	public function render_menu() {
		require_once( __DIR__ . '/event-list-menu.php' );
	}

	public function add_help() {
		$screen = get_current_screen();

		$screen->add_help_tab( array(
			'id' => 'event-list-shipping-options-help',
			'title' => 'Shipping Options',
			'callback' => array( 'BrownPaperTickets\Modules\EventList\Help', 'shipping_options' ),
		) );
	}

	public function load_admin_css($hook) {
		if ( 'bpt-settings_page_brown_paper_tickets_settings' . $this->module_name !== $hook ) {
			return;
		}

		wp_enqueue_style( 'bpt_admin_css' );
	}

	public function load_admin_js($hook)
	{
		if ( 'bpt-settings_page_brown_paper_tickets_settings' . $this->module_name !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'bpt_event_list_admin',
			plugins_url( '/assets/js/event-list-admin.js', __FILE__ ),
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
			'bpt_event_list_admin',
			'eventListAdmin',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'event-list-admin' ),
			)
		);
	}
}
