<?php

namespace BrownPaperTickets\Modules;

require_once( plugin_dir_path( __FILE__ ).'../bpt-module-class.php' );
require_once( plugin_dir_path( __FILE__ ).'/purchase-inputs.php' );
require_once( plugin_dir_path( __FILE__ ).'/purchase-ajax.php' );
require_once( \BrownPaperTickets\BptWordpress::plugin_root_dir() . 'lib/BptAPI/vendor/autoload.php');

use BrownPaperTickets\APIv2\ManageCart;
use BrownPaperTickets\APIv2\CartInfo;


class Purchase extends Module {

	public function register_settings() {
		register_setting( $this->menu_slug, $this->setting_prefix . 'purchase_settings' );
	}

	public function register_sections() {
		$section_title = 'Purchase Settings';
		$section_suffix = '_purchase';

		$inputs = new Purchase\Inputs();

		add_settings_section(
			$section_title,
			$section_title,
			array( $inputs, 'section' ),
			$this->menu_slug . $section_suffix
		);

		add_settings_field(
			$this->setting_prefix . 'enable_sales', // The ID of the input.
			'Enable Sales', // The title of the field.
			array( $inputs, 'enable_sales' ), // Event HTML callback
			$this->menu_slug . $section_suffix, // The settings page.
			$section_title // The section that the field will be rendered in.
		);
	}

	public function load_public_js( $hook ) {
		global $post;

		$options = get_option( '_bpt_purchase_settings' );
		$sales_enabled = ( isset( $options['enable_sales'] ) ? $options['enable_sales'] : false );
		$require_all_info = ( isset( $options['require_all_info'] ) ? $options['require_all_info'] : false );

		if ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'list-events' ) || has_shortcode( $post->post_content, 'list_events' ) ) && $sales_enabled ) {

			wp_enqueue_script(
				'bpt_purchase_tickets',
				plugins_url( 'assets/js/bpt-purchase-tickets.js', __FILE__ ),
				array( 'jquery', 'event_feed_js_' . $post->ID, 'ractive_js' ),
				VERSION,
				true
			);

			wp_localize_script(
				'bpt_purchase_tickets', 'bptPurchaseTickets', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'bpt-purchase-tickets' ),
					'templateUrl' => plugins_url( 'assets/templates/shopping-cart.html', __FILE__ ),
					'requireAllInfo' => $require_all_info,
				)
			);
		}
	}

	public function load_public_css( $hook ) {
		global $post;
		$options = get_option( '_bpt_purchase_settings' );
		$sales_enabled = ( isset( $options['enable_sales'] ) ? $options['enable_sales'] : false );

		if ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'list-events' ) || has_shortcode( $post->post_content, 'list_events' ) ) && $sales_enabled ) {

			wp_enqueue_style(
				'bpt_shopping_cart',
				plugins_url( 'assets/css/bpt-shopping-cart.css', __FILE__ ),
				array(),
				VERSION
			);
		}
	}

	public function set_default_setting_values() {

		$purchase_settings = get_option(
			$this->setting_prefix . 'purchase_settings'
		);

		if ( ! $purchase_settings ) {

			$settings = array(
				'enable_sales' => false,
			);

			update_option( $this->menu_slug . $this->setting_prefix . 'purchase_settings', $settings );
		}
	}

	public function remove_setting_values() {
		delete_option( $this->menu_slug . $this->setting_prefix . 'purchase_settings' );
	}

	public function load_public_ajax_actions() {

		add_action( 'wp_ajax_nopriv_bpt_get_cart_contents', array( 'BrownPaperTickets\Modules\Purchase\Ajax', 'get_cart_contents' ) );

		add_action( 'wp_ajax_nopriv_bpt_add_prices', array( 'BrownPaperTickets\Modules\Purchase\Ajax', 'add_prices' ) );
	}

	public function load_admin_ajax_actions() {
		add_action( 'wp_ajax_bpt_get_cart_contents', array( 'BrownPaperTickets\Modules\Purchase\Ajax', 'get_cart_contents' ) );
		add_action( 'wp_ajax_bpt_add_prices', array( 'BrownPaperTickets\Modules\Purchase\Ajax', 'add_prices' ) );
	}

	/**
	 * The cart contents BPT api call returns all tickets individually. This
	 * will combine tickets with the same price ID into one entry with a
	 * quantity parameter.
	 *
	 * @param  array $prices_in_cart The contents of the cart.
	 * @return array
	 */

	private function squish_prices( $prices_in_cart ) {
		$squished_prices = array();

		foreach ( $prices_in_cart as $price ) {

			if ( ! isset($squished_prices[ $price['priceID'] ] ) ) {

				$squished_prices[ (string) $price['priceID'] ] = array();
				$squishee = $squished_prices[ $price['priceID'] ];
				$squishee['tickets'] = array();
				$squishee['quantity'] = 0;

			} else {

				$squishee = $squished_prices[ $price['priceID'] ];

			}

			array_push( $squishee['tickets'], $price );

			$squishee['quantity'] = count( $squishee['tickets'] );

			$squished_prices[ $price['priceID'] ] = $squishee;
		}

		if ( count( $squished_prices ) === 0 ) {
			return false;
		}

		return $squished_prices;
	}
}
