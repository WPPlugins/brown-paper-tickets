<?php
namespace BrownPaperTickets;

use \BrownPaperTickets\BptWordpress as Utilities;

$show_prices = get_option( '_bpt_show_prices' );
$show_dates = get_option( '_bpt_show_dates' );
$show_full_description = get_option( '_bpt_show_full_description' );
$show_location_after_description = get_option( '_bpt_show_location_after_description' );
$shipping_countries    = get_option( '_bpt_shipping_countries' );
$shipping_methods = get_option( '_bpt_shipping_methods' );
$currency = get_option( '_bpt_currency' );
$date_format = esc_html( get_option( '_bpt_date_format' ) );
$time_format = esc_html( get_option( '_bpt_time_format' ) );
$show_end_time = get_option( '_bpt_show_end_time' );
$event_list_style = get_option( '_bpt_event_list_style' );
$credit_cards = get_option( '_bpt_credit_cards_displayed' );

if ( $date_format === 'custom' ) {
	$date_format = esc_html( get_option( '_bpt_custom_date_format' ) );
}

if ( $time_format === 'custom' ) {
	$time_format = esc_html( get_option( '_bpt_custom_time_format' ) );
}

if ( $currency === 'cad' ) {
	$currency = 'CAD$ ';
}

if ( $currency === 'usd' ) {
	$currency = '$';
}

if ( $currency === 'gbp' ) {
	$currency = '£';
}

if ( $currency === 'eur' ) {
	$currency = '€';
}

$countries = Utilities::get_country_list();

if ( $event_list_style ) {
	$use_style = ( isset( $event_list_style['use_style'] ) ? true : false );

	if ( $use_style ) {
		$css = '<style type="text/css">' . esc_html( $event_list_style['custom_css'] ) . '</style>';
	}
}

ob_start();

if ( isset( $css ) ) {
	$allowed_html = array(
		'style' => array(
			'type' => array(),
		),
	);

	echo wp_kses( $css, $allowed_html );
}

?>
<div class="bpt-loading-<?php esc_attr_e( $post->ID );?> hidden">
	Loading Events
	<br />
	<img src="<?php echo esc_url( plugins_url( 'assets/img/loading.gif', dirname( __DIR__ ) ) ); ?>">
</div>

<div id="bpt-event-list-<?php esc_attr_e( $post->ID );?>" class="bpt-event-list" data-post-id="<?php esc_attr_e( $post->ID );?>">

</div>
<script type="text/html" id="bpt-event-template">

{{ #error }}
	<div intro="slide" class="bpt-event bpt-default-theme">
	<h2>Sorry, an error has occurred while loading events.</h2>
	<p>{{ bptError }}</p>
	</div>
{{ /error }}

{{ #events }}
	{{ ^.error }}
	<div intro="slide" class="bpt-event bpt-default-theme">
		<h2 id="bpt-event-{{id}}" class="bpt-event-title">{{{ unescapeHTML(title) }}}</h2>


		<?php if ( $show_location_after_description === 'false' ) { ?>

			<div class="bpt-event-location">
				<div class="address1">{{ address1 }}</div>
				<div class="address2">{{ address2 }}</div>
				<div>
					{{#city}}<span class="city">{{ city }}</span>,{{/city}} {{#state}}<span class="state">{{ state }}</span>{{/state}} {{#zip}}<span class="zip">{{ zip }}</span>{{/zip}}
				</div>
			</div>

		<?php } ?>

		<div class="bpt-event-short-description">
			<p>
				{{ shortDescription }}
				<br />
			</p>
		</div>

		<?php if ( $show_full_description === 'false' ) { ?>
		<p>
			<a href="#" class="bpt-show-full-description" on-click="showFullDescription">Show Full Description</a>
		</p>
		<div class="bpt-event-full-description hidden">
			<p>{{{ unescapeHTML(fullDescription) }}}</p>
		</div>

		<?php } else { ?>

		<div class="bpt-event-full-description">
			<p>{{{ unescapeHTML(fullDescription) }}}</p>
		</div>

		<?php }

	if ( $show_location_after_description === 'true' ) { ?>

			<div class="bpt-event-location">
				<div class="address1">{{ address1 }}</div>
				<div class="address2">{{ address2 }}</div>
				<div>
					<span class="city">{{ city }}</span>, <span class="state">{{ state }}</span> <span class="zip">{{ zip }}</span>
				</div>
			</div>

		<?php }

	if ( $show_dates === 'true' ) { ?>

			{{ #if dates }}
			<form data-event-id="{{ id }}" data-event-title="{{ title }}" method ="post" class="add-to-cart" action="https://www.brownpapertickets.com/addtocart.html" target="_blank">
				<input type="hidden" name="event_id" value="{{ id }}" />
				<div class="event-dates">
					<label for="dates-{{ id }}">Select a Date:</label>
					<select class="bpt-date-select" id="dates-{{ id }}" value="{{ .selectedDate }}">
					{{ #dates }}
						<option class="event-date" value="{{ . }}">
							{{ formatDate( '<?php esc_attr_e( $date_format ); ?>', dateStart ) }}
							{{ formatTime( '<?php esc_attr_e( $time_format ); ?>', timeStart ) }}
							<?php echo ( $show_end_time === 'true' ? 'to {{ formatTime( \'' . $time_format . '\', timeEnd ) }}' : '' ); ?>
						</option>
					{{ /dates }}
					</select>
				</div>
				<fieldset>
				{{ #.selectedDate }}
					<input name="date_id" value="{{ id }}" type="hidden">
					<table id="price-list-{{ id }}" class="bpt-event-list-prices">
					<tr>
						<th>Admission Level</th>
						<th>Price</th>
						<th>Quantity</th>
					</tr>
					{{ #prices }}
					<tr data-price-id="{{ id }}" class="{{ isHidden(hidden) }}" >
						<td class="bpt-price-name" data-price-name="{{name}}" data-event-title="{{ title }}">
						{{{ unescapeHTML(name) }}}

						</td>
						<td class="bpt-price-value" data-price-value="{{ formatPrice(priceValue(.), '<?php esc_attr_e( $currency ); ?>' ) }}">
							{{ formatPrice(priceValue(.), '<?php esc_attr_e( $currency ); ?>' ) }}
							{{^.includeFee}}
							{{ #value }}
							<small>({{formatServiceFee(.serviceFee + .value, '<?php esc_attr_e( $currency ); ?>')}} w/service fee)</small>
							{{/value}}
							{{ /.includeFee }}
						</td>
						<td>
							<select class="bpt-price-qty" name="price_{{ id }}" data-price-id="{{ id }}">
								{{{ getQuantityOptions( . ) }}}
								<option value="0" selected="true">0</option>
							</select>

						</td>
					</tr>
					<?php if ( Utilities::is_user_an_admin() ) { ?>
					<tr class="bpt-admin-option">
						<td colspan="3">
							<!-- <h5>Price Options</h5> -->
							<span>
								<label for="bpt-price-hidden-{{ id }}" class="bpt-admin-option bpt-price-hidden">Display Price</label>
								<select id="bpt-price-hidden-{{ id }}" on-change="togglePriceVisibility" class="bpt-admin-option bpt-price-hidden" data-price-id="{{ id }}" data-price-name="{{ name }}">
									<option {{ ^hidden }}selected{{ /hidden }} value="true">Yes</option>
									<option {{ #hidden }}selected{{ /hidden }}value="false">No</option>
								</select>
							</span>
							<span>
								<label class="bpt-admin-option bpt-price-max-quantity" for="bpt-price-max-quantity-{{ id }}">Set Max Quantity</label>
								<input id="bpt-price-max-quantity-{{ id }}" type="text" value="{{ .maxQuantity }}" class="bpt-admin-option bpt-price-max-quantity" on-change="setPriceMaxQuantity" placeholder="20">
							</span>
							<span>
								<label class="bpt-admin-option bpt-price-interval" for="bpt-price-interval-{{ id }}">Set Interval</label>
								<input id="bpt-price-interval-{{ id }}" type="text" value="{{ .interval }}" class="bpt-admin-option bpt-price-interval" on-change="setPriceIntervals" placeholder="1">
							</span>
							<span>
								<label for="bpt-price-include-fee-{{ id }}" class="bpt-admin-option bpt-price-include-fee">Include Service Fee</label>
								<select id="bpt-price-include-fee-{{ id }}" on-change="toggleFee" class="bpt-admin-option bpt-price-inclue-fee" data-price-id="{{ id }}" data-price-name="{{ name }}">
									<option {{ #includeFee }}selected{{ /includeFee }} value="true">Yes</option>
									<option {{ ^includeFee }}selected{{ /includeFee }} value="false">No</option>
								</select>
							</span>
						</td>
					</tr>
					<?php } ?>
					{{ / }}
					{{ ^prices }}
					<tr>
						<td>Sorry, no prices available for the selected date.</td>
					</tr>
					{{ / }}
					</table>
					<div class="shipping-info">
						<label class="bpt-shipping-method" for="shipping_{{ id }}">Delivery Method</label>
						<select class="bpt-shipping-method" id="shipping_{{ id }}" name="shipping_{{ id }}">
		<?php
		foreach ( $shipping_methods as $shipping_method ) {
			switch ( $shipping_method ) {
				case 'print_at_home':
					echo '<option value="5">Print-At-Home (No Additional Fee)</option>';
					break;

				case 'will_call':
					echo '<option value="4">Will-Call (No additional fee!)</option>';
					break;

				case 'mobile':
					echo '<option value="7">Mobile Delivery (No additional fee!)</option>';
					break;

				case 'physical':
					echo '<option value="1">Physical Tickets - USPS 1st Class (No additional fee!)</option>';
					echo '<option value="2">Physical Tickets - USPS Priority Mail ($5.05)</option>';
					echo '<option value="3">Physical Tickets - USPS Express Mail ($18.11)</option>';
					break;
			}
		}
		?>
						</select>
						<br />
						<label class="bpt-shipping-country" class="bpt-shipping-country-label" for="country-id-{{ id }}">Delivery Country</label>
						<select class="bpt-shipping-country" id="country-id-{{ id }}" name="country_id">
		<?php
								$country_incr = 1;
		foreach ( $countries as $country ) {

			if ( $country === 'Azores' ) {
				echo '<option value="243"' . ($country === get_option( '_bpt_shipping_countries' ) ? 'selected' : '' ) . '>' . $country . '</option>';

				continue;
			}

				echo '<option value="' . $country_incr . '"' . ($country === get_option( '_bpt_shipping_countries' ) ? 'selected' : '' ) . '>' . $country . '</option>';
				$country_incr++;
		}
		?>
						</select>
					</div>
				{{ / }}
				</fieldset>
				<div class="bpt-event-footer">
					<div class="bpt-add-to-cart">
						<button class="bpt-submit" type="submit">Add to Cart</button>
						<span class="bpt-cc-logos">
						<?php foreach ($credit_cards as $card) { ?>
							<img src="<?php echo esc_url( plugins_url( 'img/' . $card . '_icon.png', __DIR__ ) ); ?>" />
						<?php } ?>
						</span>
					</div>
				</div>
			</form>
		{{ /if }}
		{{ ^dates }}
			<p>Sorry, there are no dates available.</p>
		{{ /dates }}

		<?php } ?>
		<div class="bpt-powered-by">
			<a href="http://www.brownpapertickets.com/event/{{ id }}" target="_blank"><span>View Event on </span><img src="<?php echo esc_url( plugins_url( 'img/bpt-footer-logo.png', __DIR__ ) ); ?>" /></a>
		<div>
	</div>
	{{ /.error }}
{{ /events }}



</script>
<?php
	$event_list = ob_get_clean();

	return $event_list;
?>
