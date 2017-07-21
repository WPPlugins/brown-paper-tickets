<?php
/**
 * Brown Paper Tickets Event List Plugin
 *
 * This plugin allows you to easily display your events in your
 * Wordpress posts, sidebar etc. Tickets can also be directly.
 *
 * @package   BrownPaperTickets
 * @author    Brown Paper Tickets <chandler@brownpapertickets.com>
 * @license   GPL-2.0+
 * @link      http://www.brownpapertickets.com
 * @copyright 2014 Brown Paper Tickets
 *
 * @wordpress-plugin
 * Plugin Name:       Brown Paper Tickets
 * Plugin URI:        http://www.brownpapertickets.com
 * Description:       Display your Brown Paper Tickets events using convenient shortcodes and widgets.
 * Version:           0.7.3
 * Author:            Brown Paper Tickets
 * Author URI:        http://www.brownpapertickets.com
 * Text Domain:       brown-paper-tickets-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

require_once( plugin_dir_path( __FILE__ ) . 'src/brown-paper-tickets-plugin.php' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook( __FILE__, array( 'BrownPaperTickets\BPTPlugin', 'activate' ) );

register_deactivation_hook( __FILE__, array( 'BrownPaperTickets\BPTPlugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'BrownPaperTickets\BPTPlugin', 'get_instance' ) );
