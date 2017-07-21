<div id="<?php esc_attr_e( $this->get_field_id( 'container' ) ) ?>" >
    <?php if ( ! empty( $instance['title'] ) ): ?>
    	<?php echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title']; ?>
    <?php endif ?>
    <div id="<?php esc_attr_e( $this->get_field_id( 'container-id' ) ) ?>" class="bpt-event-feed-widget"></div>
    <script type="text/html" id="<?php esc_attr_e( $this->get_field_id( 'feed-template' ) ); ?>">
        {{ #loading }}
        <p>
            <img src="<?php esc_attr_e( $plugin_root_url . '/public/assets/img/loading.gif' ); ?>">
            Loading Events
        </p>
        {{ /loading }}
        <?php echo wp_kses_post( $template ); ?>
    </script>
</div>
