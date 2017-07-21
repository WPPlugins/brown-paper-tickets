<?php
namespace BrownPaperTickets;
/**
 * A utility library for Wordpress Stuff.
 */

class BptWordpress {

	public function __construct() {

	}

	/**
	 * Checks whether the given nonce is valid. If it isn't it sends a json error.
	 */
	public static function check_nonce( $nonce, $nonce_title ) {

		if ( ! wp_verify_nonce( htmlentities( $nonce ), $nonce_title ) ) {
			http_response_code(403);
			wp_send_json_error( array( 'message' => 'Invalid nonce.' ) );
		}

		return true;
	}

	/**
	 * @return boolean Returns whether or not the plugin should cache data.
	 */
	public static function cache_data( $key, $value ) {
		if ( ! self::cache_enabled() ) {
			return false;
		}
	}

	public static function cache_enabled() {
		$cache_time = get_option( '_bpt_cache_time' );

		if ( $cache_time === 'false' || $cache_time === false ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the amount of time to cache the data for. This should
	 * only be called if self::cache_data() is true.
	 *
	 * @return integer The amount of time in seconds the data should be
	 * cached.
	 */
	public static function cache_time() {

		$cache_time = get_option( '_bpt_cache_time' );

		if ( ! $cache_time ) {
			return false;
		}

		$cache_unit = get_option( '_bpt_cache_unit' );

		if ( $cache_unit === 'minutes' ) {
			return $cache_time * MINUTE_IN_SECONDS;
		}

		if ( $cache_unit === 'hours' ) {
			return $cache_time * HOUR_IN_SECONDS;
		}

		if ( $cache_unit === 'days' ) {

			return $cache_time * DAY_IN_SECONDS;
		}

		return 0;

	}

	/**
	 * Get the absolute path of the plugin root directory.
	 *
	 * @return string The path to the plugin's directory with
	 * trailing slash.
	 */
	static function plugin_root_dir() {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Get the URL to the plugins folder.
	 * @return string The plugin to the path's directory with
	 * trailing slash.
	 */
	static function plugin_root_url() {
		return plugins_url( '', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Determiner whether or not the user is an administrator.
	 * @param  integer  $user_id The Id of the user.
	 * @return boolean		   Returns true if user is an admin.
	 */
	static function is_user_an_admin( $user_id = null ) {

		if ( is_numeric( $user_id ) ) {
			$user = get_userdata( $user_id );
		} else {

			$user = wp_get_current_user();
		}

		if ( empty( $user ) ) {
			return false;
		}

		if ( in_array( 'administrator', (array) $user->roles ) ) {
			return true;
		}

	}

	static function get_country_list() {
		$countries = array(
			'Afghanistan',
			'Aland Islands',
			'Albania',
			'Algeria',
			'American Samoa',
			'Andorra',
			'Angola',
			'Anguilla',
			'Antarctica',
			'Antigua And Barbuda',
			'Argentina',
			'Armenia',
			'Aruba',
			'Australia',
			'Austria',
			'Azerbaijan',
			'Azores',
			'Bahamas',
			'Bahrain',
			'Bangladesh',
			'Barbados',
			'Belarus',
			'Belgium',
			'Belize',
			'Benin',
			'Bermuda',
			'Bhutan',
			'Bolivia',
			'Bosnia And Herzegovina',
			'Botswana',
			'Bouvet Island',
			'Brazil',
			'British Indian Ocean Territory',
			'Brunei Darussalam',
			'Bulgaria',
			'Burkina Faso',
			'Burundi',
			'Cambodia',
			'Cameroon',
			'Canada',
			'Cape Verde',
			'Cayman Islands',
			'Central African Republic',
			'Chad',
			'Chile',
			'China',
			'Christmas Island',
			'Cocos (keeling) Islands',
			'Colombia',
			'Comoros',
			'Congo',
			'Congo, The Democratic Republic Of The',
			'Cook Islands',
			'Costa Rica',
			'Cote Divoire',
			'Croatia',
			'Cyprus',
			'Czech Republic',
			'Denmark',
			'Djibouti',
			'Dominica',
			'Dominican Republic',
			'Ecuador',
			'Egypt',
			'El Salvador',
			'Equatorial Guinea',
			'Eritrea',
			'Estonia',
			'Ethiopia',
			'Falkland Islands',
			'Faroe Islands',
			'Fiji',
			'Finland',
			'France',
			'French Guiana',
			'French Polynesia',
			'French Southern Territories',
			'Gabon',
			'Gambia',
			'Georgia',
			'Germany',
			'Ghana',
			'Gibraltar',
			'Greece',
			'Greenland',
			'Grenada',
			'Guadeloupe',
			'Guam',
			'Guatemala',
			'Guernsey',
			'Guinea',
			'Guinea-Bissau',
			'Guyana',
			'Haiti',
			'Heard Island And Mcdonald Islands',
			'Holy See',
			'Honduras',
			'Hong Kong',
			'Hungary',
			'Iceland',
			'India',
			'Indonesia',
			'Iraq',
			'Ireland',
			'Isle Of Man',
			'Israel',
			'Italy',
			'Jamaica',
			'Japan',
			'Jersey',
			'Jordan',
			'Kazakhstan',
			'Kenya',
			'Kiribati',
			'Korea, Republic Of',
			'Kosovo',
			'Kyrgyzstan',
			'Latvia',
			'Lebanon',
			'Lesotho',
			'Liberia',
			'Libyan Arab Jamahiriya',
			'Liechtenstein',
			'Lithuania',
			'Luxembourg',
			'Macao',
			'Macedonia, The Former Yugoslav Republic Of',
			'Madagascar',
			'Madeira',
			'Malawi',
			'Malaysia',
			'Maldives',
			'Mali',
			'Malta',
			'Marshall Islands',
			'Martinique',
			'Mauritania',
			'Mauritius',
			'Mayotte',
			'Mexico',
			'Micronesia, Federated States Of',
			'Moldova',
			'Monaco',
			'Mongolia',
			'Montenegro',
			'Montserrat',
			'Morocco',
			'Mozambique',
			'Myanmar',
			'Namibia',
			'Nauru',
			'Nepal',
			'Netherlands',
			'Netherlands Antilles',
			'New Caledonia',
			'New Zealand',
			'Nicaragua',
			'Niger',
			'Nigeria',
			'Niue',
			'Norfolk Island',
			'Northern Mariana Islands',
			'Norway',
			'Oman',
			'Pakistan',
			'Palau',
			'Palestinian Territory, Occupied',
			'Panama',
			'Papua New Guinea',
			'Paraguay',
			'Peru',
			'Philippines',
			'Pitcairn',
			'Poland',
			'Portugal',
			'Puerto Rico',
			'Qatar',
			'Réunion',
			'Romania',
			'Russian Federation',
			'Rwanda',
			'Saint Barthélemy',
			'Saint Helena',
			'Saint Kitts And Nevis',
			'Saint Lucia',
			'Saint Martin',
			'Saint Pierre And Miquelon',
			'Saint Vincent And The Grenadines',
			'Samoa',
			'San Marino',
			'Sao Tome And Principe',
			'Saudi Arabia',
			'Senegal',
			'Serbia',
			'Seychelles',
			'Sierra Leone',
			'Singapore',
			'Slovakia',
			'Slovenia',
			'Solomon Islands',
			'Somalia',
			'South Africa',
			'South Georgia And The South Sandwich Islands',
			'Spain',
			'Sri Lanka',
			'Suriname',
			'Svalbard And Jan Mayen',
			'Swaziland',
			'Sweden',
			'Switzerland',
			'Taiwan',
			'Tajikistan',
			'Tanzania, United Republic Of',
			'Thailand',
			'Timor-Leste',
			'Togo',
			'Tokelau',
			'Tonga',
			'Trinidad And Tobago',
			'Tunisia',
			'Turkey',
			'Turkmenistan',
			'Turks And Caicos Islands',
			'Tuvalu',
			'Uganda',
			'Ukraine',
			'United Arab Emirates',
			'United Kingdom',
			'United States',
			'United States Minor Outlying Islands',
			'Uruguay',
			'Uzbekistan',
			'Vanuatu',
			'Venezuela',
			'Vietnam',
			'Virgin Islands, British',
			'Virgin Islands, US',
			'Wallis And Futuna',
			'Western Sahara',
			'Yemen',
			'Zambia',
			'Zimbabwe',
		);

		return $countries;
	}
	/**
	 * Utilites
	 */

	/**
	 * Determines whether or not an option matches a given value.
	 * @param  [type]  $value  [description]
	 * @param  [type]  $option [description]
	 * @param  [type]  $type   [description]
	 * @return boolean		 [description]
	 */
	static function is_selected( $value, $option = null, $type = null ) {

		$opt = get_option( $option );

		if ( is_array( $opt ) ) {

			foreach ( $opt as $single_opt ) {

				if ( $value === $single_opt && $type === null ) {
					return true;
				}

				if ( $value === $single_opt && $type === 'checked' ) {
					return 'checked';
				}

				if ( $value === $single_opt && $type === 'selected' ) {
					return 'selected="true"';
				}
			}
		}

		if ( $value === $opt && $type === null ) {
			return true;
		}

		if ( $value === $opt && $type === 'checked' ) {
			return 'checked';
		}

		if ( $value === $opt && $type === 'selected' ) {
			return 'selected="true"';
		}

		return false;
	}

	static function set_session_var( $key, $value ) {
		self::init_session();

		return $_SESSION['bpt_cart'][ sanitize_key( $key ) ] = self::sanitize_var($value);;

	}

	static function get_session_var( $key = null ) {
		self::init_session();

		if ( ! $key ) {
			return $_SESSION['bpt_cart'];
		}

		return $_SESSION['bpt_cart'][ $key ];
	}

	static function init_session()
	{
		if ( ! session_id() ) {
			session_start();
		}

		if ( ! isset( $_SESSION['bpt_cart'] ) ) {
			$_SESSION['bpt_cart'] = array();
		}
	}

	static function sanitize_var( $variable ) {

		if ( is_string( $variable ) ) {
			$variable = htmlentities( $variable );
		}

		if ( is_array( $variable ) ) {
			foreach ( $variable as $key => $value ) {

				if ( is_array( $value ) ) {
					$variable[ htmlentities( $key ) ] = self::sanitize_var( $value );
					continue;
				}

				$variable[ htmlentities( $key ) ] = htmlentities( $value );
			}
		}

		return $variable;
	}


	/**
	 * Date Methods
	 *
	 */
	public static function date_has_past( $date ) {

		if ( strtotime( $date['dateStart'] ) < time() ) {
			return true;
		}
		return false;
	}

	public static function date_is_live( $date ) {

		if ( ! $date['live'] ) {
			return false;
		} else {
			return true;
		}

	}

	public static function date_is_sold_out( $date ) {

		if ( self::date_has_past( $date ) === true && strtotime( $date['dateStart'] ) >= time() ) {
			return false;
		}

		return true;
	}

	/**
	 * Price Methods
	 */

	public static function price_is_live( $price ) {
		if ( ! $price['live'] ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Conversion Methods
	 */
	/**
	 * Convert Date. Converst the Date to a human readable date.
	 *
	 * @param  string $date The String that needs to be formatted.
	 * @return string	   The formatted date string.
	 */
	public static function convert_date( $date ) {
		return strftime( '%B %e, %Y', strtotime( $date ) );
	}

	/**
	 * Convert Time. Converst the Time to a human readable date.
	 * @param  string $time The string to be formated.
	 * @return string	   The formatted string.
	 */
	public static function convert_time( $date ) {
		return strftime( '%l:%M%p', strtotime( $date ) );
	}

	public static function remove_bad_events( $event_list ) {
		foreach ( $event_list as $eventIndex => $event ) {

			if ( ! $event['live'] ) {

				unset( $event_list[ $eventIndex ] );
			}

			$event_list = array_values( $event_list );
		}

		return $event_list;
	}

    public static function unescape_description($events) {
        foreach ($events as &$event) {
            $event['fullDescription'] = html_entity_decode( $event['fullDescription'] );
            $event['fullDescription'] = nl2br( $event['fullDescription'] );
        }

        return $events;
    }

	/**
	 * Removes past dates and deactivated from an array of events.
	 * @param  array   $event_list	 	   An array of events with dates.
	 * @param  boolean $remove_deactivated Pass false if you want to remove deactivated dates.
	 * @param  boolean $remove_past		Pass false if you want to remove past dates.
	 * @return array					   The modified event array with bad dates removed.
	 */
	public static function remove_bad_dates( $event_list, $remove_deactivated = true, $remove_past = true ) {

		foreach ( $event_list as $event_index => $event ) {

			if ( ! isset($event['dates'] ) ) {
				continue;
			}

			foreach ( $event['dates'] as $date_index => $date ) {

				$remove_date = false;

				if ( $remove_past && self::date_has_past( $date ) ) {
					$remove_date = true;
				}

				if ( $remove_deactivated && ! self::date_is_live( $date ) ) {
					$remove_date = true;
				}

				if ( $remove_date ) {
					unset( $event['dates'][ $date_index ] );
				}
			}

			$event['dates'] = array_values( $event['dates'] );

			$event_list[ $event_index ] = $event;
		}

		return $event_list;
	}

	public static function remove_bad_prices( $event_list ) {
		foreach ( $event_list as $event_index => $event ) {

			foreach ( $event['dates'] as $date_index => $date ) {

				foreach ( $date['prices'] as $priceIndex => $price ) {

					if ( self::price_is_live( $price ) === false ) {
						unset( $date['prices'][ $priceIndex ] );
					}
				}

				$date['prices'] = array_values( $date['prices'] );

				$event['dates'][ $date_index ] = $date;
			}

			$event_list[ $event_index ] = $event;
		}

		return $event_list;
	}


	public static function sort_prices( $event_list ) {
		$sort_method = get_option( '_bpt_price_sort' );

		foreach ( $event_list as $event_index => $event ) {

			foreach ( $event['dates'] as $date_index => $date ) {

				if ( $sort_method === 'alpha_asc' ) {
					$date['prices'] = self::sort_by_key( $date['prices'], 'name', true );
				}

				if ( $sort_method === 'alpha_desc' ) {
					$date['prices'] = self::sort_by_key( $date['prices'], 'name' );
				}

				if ( $sort_method === 'value_desc' ) {
					$date['prices'] = self::sort_by_key( $date['prices'], 'value', true );
				}

				if ( $sort_method === 'value_asc' )  {
					$date['prices'] = self::sort_by_key( $date['prices'], 'value' );
				}

				$event['dates'][ $date_index ] = $date;
			}

			$event_list[ $event_index ] = $event;
		}

		return $event_list;
	}

	public static function sort_by_key( $array, $key, $reverse = false ) {

		//Loop through and get the values of our specified key
		foreach ( $array as $k => $v ) {
			$b[] = strtolower( $v[ $key ] );
		}

		if ( $reverse === false ) {

			asort( $b );

		} else {

			arsort( $b );

		}

		foreach ( $b as $k => $v ) {
			$c[] = $array[ $k ];
		}

		return $c;
	}

	public static function xml_2_array( $xml ) {
		$converted = array();
		$properties = get_object_vars( $xml );

		foreach ( $properties as $key => &$value ) {
			if ( is_object( $value ) ) {
				$value = self::xml_2_array( $value );
				$converted[ (string) $key ] = $value;
			} elseif ( is_array( $value ) ) {
				$converted[ (string) $key ] = $value;
			} else {
				$converted[ (string) $key ] = (string) $value;
			}
		}

		return $converted;
	}
}
