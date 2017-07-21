<?php namespace BrownPaperTickets\Modules\EventFeed;

use BrownPaperTickets\Modules\EventFeed\Api;
use BrownPaperTickets\Modules\EventFeed;

/**
 * Creates and handles updates to the Event Feed custom post type.
 */
class EventFeedType {
	/**
	 * The name of the post type.
	 *
	 * @var string
	 */
	private $post_type_name = 'bpt_event_feed';

	/**
	 * Meta fields to be handled auotmatically.
	 * @var array
	 */
	private $meta = array(
		'feed_id' => 'integer',
		'expiration' => 'integer',
		'template' => 'string',
	);

	/**
	 * Adds necessary actions.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'after_setup_theme', array( $this, 'theme_support' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post_' . $this->post_type_name, array( $this, 'update' ) );
	}

	/**
	 * Regster the post types.
	 *
	 * @return void
	 */
	public function register() {
		register_post_type( $this->post_type_name, $this->get_args() );
	}

	/**
	 * Update the various fields.
	 * @param  integer $id The custom post type's post_id.
	 * @return void
	 */
	public function update($id) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		foreach ( $this->meta as $meta => $type ) {
			if ( isset( $_POST[ $meta ] ) ) {
				update_post_meta( $id, $meta, wp_kses_post( $_POST[ $meta ] ) );

				if ( $_POST[ $meta ] === '' ) {
					$this->delete( $id );
				}
			}
		}
	}

	/**
	 * Delete a particular meta field.
	 *
	 * @param  integer $id   The post's ID.
	 * @param  string $meta The meta field key.
	 */
	public function delete($id, $meta) {
		delete_post_meta( $id, $meta );
	}

	/**
	 * Get the meta for the post.
	 *
	 * @param  integer $id The post's ID.
	 * @return array An array of meta fields.
	 */
	public function get($id) {
		$data = array('events' => '');

		foreach ( $this->meta as $meta => $type ) {
			$data[ $meta ] = get_post_meta( $id, $meta, true );

			if ( $data[ $meta ] === '' && $type === 'boolean' ) {
				$data[ $meta ] = false;
			}

			if ( $data[ $meta ] !== false && $type === 'boolean' ) {
				$data[ $meta ] = true;
			}
		}

		if ( isset( $data['feed_id'] ) && $data['feed_id'] !== '' ) {
			$manager = new EventFeedManager( $data['feed_id'] );
			$data['events'] = $manager->get();
		}

		return $data;
	}

	/**
	 * Add the meta boxes.
	 * @param object $type I don't even know what this is.
	 * @param WP_Post $post A wordpress post.
	 */
	public function add_meta_boxes( $type, $post ) {
		$data = $this->get( $post->ID );

		add_meta_box(
			'feed-settings-side',
			__( 'Feed Settings', \BrownPaperTickets\TEXT_DOMAIN ),
			function() use ($data) {
				$this->renderSide($data);
			},
			$this->post_type_name,
			$context = 'side',
			$priority = 'high'
		);

		add_meta_box(
			'feed-events',
			__( 'Feed Events', \BrownPaperTickets\TEXT_DOMAIN ),
			function() use ($data) {
				$this->renderEventsBox($data);
			},
			$this->post_type_name,
			$context = 'normal',
			$priority = 'high'
		);
	}

	/**
	 * Render the main editor area.
	 *
	 * @param  array $data The data you'll use in the template.
	 */
	public function renderEventsBox($data) {
        $manager = new EventFeedManager( $data['feed_id'] );
		include('assets/templates/meta/events-box.php');
	}

	/**
	 * Render the side meta area.
	 *
	 * @param  array $data The data you'll use in the template.
	 */
	public function renderSide($data) {
		include('assets/templates/meta/side-meta-box.php');
	}

	/**
	 * Add theme support stuff here.
	 */
	public function theme_support() {
		//
	}

	/**
	 * Return the labels used for the admin screens of the custom post type.
	 * @return array Labels used for labelling.
	 */
	public function get_labels() {
		return [
			'name' => _x( 'Event Feeds', 'post type general name', \BrownPaperTickets\TEXT_DOMAIN ),
			'singular_name' => _x( 'Event Feed', 'post type singular name', \BrownPaperTickets\TEXT_DOMAIN ),
			'menu_name' => _x( 'Event Feeds', 'admin menu', \BrownPaperTickets\TEXT_DOMAIN ),
			'name_admin_bar' => _x( 'Event Feed', 'add new on admin bar', \BrownPaperTickets\TEXT_DOMAIN ),
			'add_new' => _x( 'Add New', 'video', \BrownPaperTickets\TEXT_DOMAIN ),
			'add_new_item' => __( 'Add New Event Feed', \BrownPaperTickets\TEXT_DOMAIN ),
			'new_item' => __( 'New Event Feed', \BrownPaperTickets\TEXT_DOMAIN ),
			'edit_item' => __( 'Edit Event Feed', \BrownPaperTickets\TEXT_DOMAIN ),
			'view_item' => __( 'View Event Feed', \BrownPaperTickets\TEXT_DOMAIN ),
			'all_items' => __( 'Event Feeds', \BrownPaperTickets\TEXT_DOMAIN ),
			'search_items' => __( 'Search Event Feeds', \BrownPaperTickets\TEXT_DOMAIN ),
			'parent_item_colon' => __( 'Parent Event Feeds:', \BrownPaperTickets\TEXT_DOMAIN ),
			'not_found' => __( 'No Event Feeds found.', \BrownPaperTickets\TEXT_DOMAIN ),
			'not_found_in_trash' => __( 'No Event Feeds found in Trash.', \BrownPaperTickets\TEXT_DOMAIN ),
		];
	}

	/**
	 * Custom post type arguments.
	 * @return array All the arguments used for creating custom post types.
	 */
	public function get_args() {
		return $args = [
			'labels' => $this->get_labels(),
			'description' => __( 'Display your Brown Paper Tickets Event Feeds' ),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => 'brown_paper_tickets_settings',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'event-feeds' ),
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'menu_icon' => 'dashicons-format-video',
			'taxonomies' => [ 'category', 'post_tag' ],
			'supports' => [
				'title',
			],
		];
	}
}
