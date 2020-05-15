<?php

add_filter( 'buddyforms_admin_tabs', 'buddyforms_buddypress_admin_tab', 1, 1 );
function buddyforms_buddypress_admin_tab( $tabs ) {

	if ( ! $member_types = bp_get_member_types( array(), 'objects' ) ) {
	    return $tabs;
	}

	$tabs['buddypress'] = 'BuddyPress';
	return $tabs;

}

add_action( 'buddyforms_settings_page_tab', 'buddyforms_buddypress_settings_page_tab' );
function buddyforms_buddypress_settings_page_tab( $tab ) {
    global $buddyforms;

	if ( $tab != 'buddypress' ) {
		return $tab;
	}


	if ( ! $member_types = bp_get_member_types( array(), 'objects' ) ) {
	    return;
	}


	$mtypes = array();
	if ( $member_types = bp_get_member_types( array(), 'objects' ) ) {

		foreach ( $member_types as $member_type ){
			$mtypes[$member_type->name] = $member_type->labels['name'];
		}

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
                        <tr>
                            <th colspan="2">
                                <h3><span>Overwrite the BuddyPress edit profile form</span></h3>
                            </th>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label for="buddyforms_buddypress">No Member Type</label>
                                <span class="buddyforms-help-tip"></span></th>
                            <td class="forminp forminp-select">
		                        <?php
		                        if ( isset( $buddyforms ) && is_array( $buddyforms ) ) {
			                        echo '<select name="buddyforms_buddypress_settings[none]" id="buddyforms_registration_form_none">';
                                        echo '<option ' . selected( $buddypress_settings['none'], 'none' ) . '  value="none">' . __( 'BuddyPress Default', 'buddyforms-members' ) . '</option>';
			                        foreach ( $buddyforms as $form_slug => $form ) {
				                        if ( $form['form_type'] == 'registration' ) {
					                        echo '<option ' . selected( $buddypress_settings['none'], $form['slug'] ) . ' value="' . $form['slug'] . '">' . $form['name'] . '</option>';
				                        }
			                        }
			                        echo '</select>';
		                        }
		                        ?>
                            </td>
                        </tr>

                        <?php foreach ( $mtypes as $key => $type ){ ?>
                            <tr valign="top">
                                <th scope="row" class="titledesc">
                                    <label for="buddyforms_buddypress"><?php echo $type ?></label>
                                    <span class="buddyforms-help-tip"></span></th>
                                <td class="forminp forminp-select">
	                                <?php
	                                if ( isset( $buddyforms ) && is_array( $buddyforms ) ) {
		                                echo '<select name="buddyforms_buddypress_settings[' . $key . ']" id="buddyforms_registration_form_' . $key . '">';
		                                echo '<option value="none">' . __( 'BuddyPress Default', 'buddyforms-members' ) . '</option>';
		                                foreach ( $buddyforms as $form_slug => $form ) {
			                                if ( $form['form_type'] == 'registration' ) {
				                                echo '<option ' . selected( $buddypress_settings[$key], $form['slug'] ) . 'value="' . $form['slug'] . '">' . $form['name'] . '</option>';
			                                }
		                                }
		                                echo '</select>';
	                                }
	                                ?>
                                </td>
                            </tr>
                        <?php } ?>




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
