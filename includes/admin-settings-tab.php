<?php

add_filter( 'buddyforms_admin_tabs', 'buddyforms_buddypress_admin_tab', 1, 1 );
function buddyforms_buddypress_admin_tab( $tabs ) {

	$tabs['buddypress'] = 'BuddyPress';
	return $tabs;

}

add_action( 'buddyforms_settings_page_tab', 'buddyforms_buddypress_settings_page_tab' );
function buddyforms_buddypress_settings_page_tab( $tab ) {
    global $buddyforms;

	if ( $tab != 'buddypress' ) {
		return $tab;
	}
	$buddypress_settings = get_option( 'buddyforms_buddypress_settings' );

	?>

    <div class="metabox-holder">
        <div class="postbox buddyforms-metabox">
            <div class="inside">
                <form method="post" action="options.php">

					<?php settings_fields( 'buddyforms_buddypress_settings' ); ?>

                    <table class="form-table">

                        <tbody>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="buddyforms_buddypress">Login Page</label>
                                <span class="buddyforms-help-tip"></span></th>
                            <td class="forminp forminp-select">
								<?php
								$pages = buddyforms_get_all_pages( 'id', 'settings' );
								$login_page  = empty( $buddypress_settings['login_page'] ) ? '' : $buddypress_settings['login_page'];

								if ( isset( $pages ) && is_array( $pages ) ) {
									echo '<select name="buddyforms_buddypress_settings[login_page]" id="buddyforms_buddypress_login_page">';
									$pages['none'] = 'WordPress Default';
									foreach ( $pages as $page_id => $page_name ) {
										if ( ! empty( $page_name ) ) {
											echo '<option ' . selected( $login_page, $page_id ) . 'value="' . $page_id . '">' . $page_name . '</option>';
										}
									}
									echo '</select>';
								} ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="buddyforms_buddypress_lavel_1">Display Login Form?</label>
                                <span class="buddyforms-help-tip"></span></th>
                            <td class="forminp forminp-select">
		                        <?php
		                        $display_login_form  = empty( $buddypress_settings['display_login_form'] ) ? '' : $buddypress_settings['display_login_form'];
		                        ?>
                                <select name="buddyforms_buddypress_settings[display_login_form]" id="buddyforms_buddypress_display_login_form">
                                    <option <?php selected( $display_login_form, 'overwrite'); ?> value="overwrite">Overwrite Page Content</option>
                                    <option <?php selected( $display_login_form, 'above'); ?> value="above">Above the Content</option>
                                    <option <?php selected( $display_login_form, 'under'); ?> value="under">Under the Content</option>
                                    <option <?php selected( $display_login_form, 'shortcode'); ?> value="shortcode">I use the Shortcode</option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="buddyforms_buddypress_lavel_1">Display Registration Link?</label>
                                <span class="buddyforms-help-tip"></span></th>
                            <td>
		                        <?php
		                        $register_page  = empty( $buddypress_settings['register_page'] ) ? '' : $buddypress_settings['register_page'];

		                        if ( isset( $pages ) && is_array( $pages ) ) {
			                        echo '<select name="buddyforms_buddypress_settings[register_page]" id="buddyforms_registration_form">';
			                        echo '<option value="default">' . __( 'WordPress Default', 'buddyforms' ) . '</option>';
			                        echo '<option value="none">' . __( 'None', 'buddyforms' ) . '</option>';
			                        foreach ( $pages as $page_id => $page_name ) {
				                        if ( ! empty( $page_name ) ) {
					                        echo '<option ' . selected( $register_page, $page_id ) . 'value="' . $page_id . '">' . $page_name . '</option>';
				                        }
			                        }
			                        echo '</select>';
		                        }
		                        ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="buddyforms_buddypress_lavel_1">Redirect after Login</label>
                                <span class="buddyforms-help-tip"></span></th>
                            <td class="forminp forminp-select">
		                        <?php
		                        $redirect_page  = empty( $buddypress_settings['redirect_page'] ) ? '' : $buddypress_settings['redirect_page'];
		                        if ( isset( $pages ) && is_array( $pages ) ) {
			                        echo '<select name="buddyforms_buddypress_settings[redirect_page]" id="buddyforms_buddypress_redirect_page">';
			                        $pages['default'] = 'WordPress Default';
			                        foreach ( $pages as $page_id => $page_name ) {
				                        if ( ! empty( $page_name ) ) {
					                        echo '<option ' . selected( $redirect_page, $page_id ) . 'value="' . $page_id . '">' . $page_name . '</option>';
				                        }
			                        }
			                        echo '</select>';
		                        } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
					<?php submit_button(); ?>

                </form>
            </div><!-- .inside -->
        </div><!-- .postbox -->
    </div><!-- .metabox-holder -->
	<?php
}

add_action( 'admin_init', 'buddyforms_buddypress_register_option' );
function buddyforms_buddypress_register_option() {
	// creates our settings in the options table
	register_setting( 'buddyforms_buddypress_settings', 'buddyforms_buddypress_settings', 'buddyforms_buddypress_settings_default_sanitize' );
}

// Sanitize the Settings
function buddyforms_buddypress_settings_default_sanitize( $new ) {
	return $new;
}
