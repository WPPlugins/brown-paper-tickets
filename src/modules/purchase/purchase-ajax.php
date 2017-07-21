<?php

namespace BrownPaperTickets\Modules\Purchase;

use BrownPaperTickets\BptWordpress as Utilities;

class Ajax
{
	private static $nonce_title = 'bpt-purchase-tickets';

	public static function get_cart_contents()
	{
		Utilities::check_nonce( $_REQUEST['nonce'], self::$nonce_title );

		wp_send_json( Utilities::get_session_var() );
	}

	public static function init_cart() {
		Utilities::check_nonce( $_REQUEST['nonce'], self::$nonce_title );

		$dev_id = get_option( '_bpt_dev_id' );

		if ( $dev_id ) {
			$cart = new \BrownPaperTickets\APIv2\ManageCart( $dev_id );

			$cart_info = $cart->initCart();

			Utilities::set_session_var( 'cart_id', $cart_info['cartID'] ); 
			Utilities::set_session_var( 'cart_created_at', $cart_info['cartCreatedAt'] );

			wp_send_json_success( array( 'message' => 'Cart created.' ) );
		}

		wp_send_json_error( array( 'message' => 'Unable to create cart.' ) );
	}

	public static function add_prices() {
		Utilities::check_nonce( $_REQUEST['nonce'], self::$nonce_title );

		if ( ! isset( $_POST['prices'] ) ) {
			wp_send_json( array( 'success' => false, 'message' => 'No prices were sent.' ) );
		}

		if ( Utilities::set_session_var( 'prices', $_POST['prices'] ) ) {
			wp_send_json( array(
					'success' => true,
					'message' => 'Prices updated.',
					'prices' => Utilities::get_session_var( 'prices' )
				)
			);
		}

		wp_send_json( array( 'success' => false, 'message' => 'Unable to add prices.' ) );
	}

	public static function add_shipping() {

	}

	public static function add_billing() {

	}

	public static function get_receipt() {

	}
}
