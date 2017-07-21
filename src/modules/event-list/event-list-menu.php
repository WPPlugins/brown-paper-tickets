<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( $this->menu_slug . $this->module_name ); ?>
		<h2>
			<img src="<?php echo esc_url( plugins_url( 'public/assets/img/bpt.png', $this->plugin_root() ) )?>">
		</h2>
		<h1>Event List Display Settings</h1>
		<p>Configure how the event list displays your event information.</p>
		<?php do_settings_sections( $this->menu_slug . $this->module_name ); ?>
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Event List Settings' ); ?>" />
	</form>
</div>
