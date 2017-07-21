<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( $this->menu_slug . $this->module_name ); ?>
		<h2>
			<img src="<?php echo esc_url( plugins_url( 'public/assets/img/bpt.png', $this->plugin_root() ) )?>">
		</h2>
		<?php do_settings_sections( $this->menu_slug . $this->module_name ); ?>
		<div class="account-test-results">
			<p class="message"></p>
			<p class="name"></p>
			<p class="events"></p>
			<p class="error"></p>
		</div>
		<button class="button" id="test-account">Test Account<img class="loading hidden" src="<?php echo esc_url( plugins_url( 'public/assets/img/loading.gif', $this->plugin_root() ) )?>"></button>
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Account Settings' ); ?>" />
	</form>
</div>
