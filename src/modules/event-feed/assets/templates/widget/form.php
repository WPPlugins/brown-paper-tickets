<?php if ( $feeds->have_posts() ): ?>
<p>
    <label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>">Title:</label>
    <input
        type="text"
        id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"
        name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>"
        class="widefat"
        value="<?php echo $title; ?>"
    >
</p>
<p>
    <label for="<?php esc_attr_e( $this->get_field_id( 'feed_id' ) ); ?>">Feed:</label>
    <select
        id="<?php esc_attr_e( $this->get_field_id( 'feed_id' ) ); ?>"
        name="<?php esc_attr_e( $this->get_field_name( 'feed_id' ) ); ?>"
        class="widefat"
    >
    <?php while ( $feeds->have_posts() ): $feeds->the_post(); ?>
        <option
            value="<?php echo the_ID(); ?>"
            <?php echo ( $feed_id == get_the_ID() ? 'selected' : '' ); ?>
        ><?php echo the_title(); ?></option>
    <?php endwhile; ?>
    </select>
</p>
<?php else: ?>
    No feeds.
<?php endif;?>
