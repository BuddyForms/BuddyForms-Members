<?php

/**
 * Add the forms to the admin bar
 *
 * @package BuddyForms
 * @since 0.3 beta
 *
 * @uses add_action()
 */
add_action( 'wp_before_admin_bar_render', 'buddyforms_members_wp_before_admin_bar_render', 99, 1 );
function buddyforms_members_wp_before_admin_bar_render() {
	global $wp_admin_bar, $buddyforms;

	if ( empty( $buddyforms ) ) {
		return;
	}

	foreach ( $buddyforms as $key => $buddyform ) {

		if ( ! isset( $buddyform['post_type'] ) || $buddyform['post_type'] == 'none' ) {
			continue;
		}

		if ( isset( $buddyform['profiles_integration'] ) ) :

			$parent_tab = buddyforms_members_parent_tab( $buddyform );

			$slug = $key;
			if ( isset( $buddyform['slug'] ) ) {
				$slug = $parent_tab . '/';
			}

			$post_type_object = get_post_type_object( $key );

			if ( isset( $post_type_object->labels->name ) ) {
				$name = $post_type_object->labels->name;
			}

			if ( isset( $buddyform['name'] ) ) {
				$name = $buddyform['name'];
			}

			if ( isset( $buddyform['admin_bar'][0] ) ) {
				if ( current_user_can( 'buddyforms_' . $key . '_create' ) ) {
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-buddypress',
							'id'     => 'my-account-buddypress-' . $key,
							'title'  => __( $name, 'buddypress' ),
							'href'   => trailingslashit( bp_loggedin_user_domain() . $slug ),
						)
					);
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-buddypress-' . $key,
							'id'     => 'my-account-buddypress-' . $key . '-view',
							'title'  => __( 'View my ', 'buddyforms-members' ) . $buddyform['name'],
							'href'   => trailingslashit( bp_loggedin_user_domain() . $slug ),
						)
					);
					$wp_admin_bar->add_menu(
						array(
							'parent' => 'my-account-buddypress-' . $key,
							'id'     => 'my-account-buddypress-' . $key . '-new',
							'title'  => __( 'New ', 'buddyforms-members' ) . $buddyform['singular_name'],
							'href'   => trailingslashit( bp_loggedin_user_domain() . $slug . $key . '-create' ),
						)
					);
				}
			}
		endif;
	}
}

add_action( 'wp_before_admin_bar_render', 'buddyforms_admin_bar_members', 10, 1 );
/**
 * Remove forms from the admin used by BuddyForms. They will be added to the BuddyPress menu
 *
 * @package BuddyForms
 * @since 0.3 beta
 *
 * @uses add_action()
 */
function buddyforms_admin_bar_members() {
	global $wp_admin_bar, $buddyforms;

	if ( ! isset( $buddyforms ) ) {
		return;
	}

	if ( ! is_array( $buddyforms ) ) {
		return;
	}

	foreach ( $buddyforms as $key => $buddyform ) {

		if ( isset( $buddyform['profiles_integration'] ) ) {
			$wp_admin_bar->remove_menu( 'my-account-' . $key );
		}
	}

}

/**
 * Get the BuddyForms template directory
 *
 * @package BuddyForms
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_members_get_template_directory() {
	return apply_filters( 'buddyforms_members_get_template_directory', constant( 'BUDDYFORMS_MEMBERS_TEMPLATE_PATH' ) );
}

/**
 * Locate a template
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
function buddyforms_members_locate_template( $file ) {
	if ( locate_template( array( $file ), false ) ) {
		locate_template( array( $file ), true );
	} else {
		include BUDDYFORMS_MEMBERS_TEMPLATE_PATH . $file;
	}
}


add_filter( 'buddyforms_front_js_css_loader', 'buddyforms_front_js_loader_bp_members_support', 10, 1 );
function buddyforms_front_js_loader_bp_members_support( $found ) {
	global $bp, $buddyforms, $buddyforms_member_tabs;

	$form_slug = isset( $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ] ) ? $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ] : '';

	// check the post content for the short code
	if ( isset( $buddyforms[ $form_slug ] ) ) {
		$found = true;
	}

	return $found;
}

// add_filter('buddyforms_button_view_posts', 'buddyforms_members_button_view_posts', 10, 2);
function buddyforms_members_button_view_posts( $button, $args ) {
	global $buddyforms;

	extract(
		shortcode_atts(
			array(
				'form_slug' => '',
				'label'     => 'View',
			),
			$args
		)
	);

	if ( isset( $buddyforms[ $form_slug ]['profiles_integration'] ) ) {
		$url    = trailingslashit( bp_loggedin_user_domain() );
		$button = '<a class="button" href="' . $url . $form_slug . '/">' . __( $label, 'buddyforms-members' ) . ' </a>';
	}

	return $button;
}

// add_filter('buddyforms_button_add_new', 'buddyforms_members_button_add_new', 10, 2);
function buddyforms_members_button_add_new( $button, $args ) {
	global $buddyforms;

	extract(
		shortcode_atts(
			array(
				'form_slug' => '',
				'label'     => 'Add New',
			),
			$args
		)
	);

	if ( isset( $buddyforms[ $form_slug ]['profiles_integration'] ) ) {
		$url    = trailingslashit( bp_loggedin_user_domain() );
		$button = '<a class="button" href="' . $url . $form_slug . '/create/">' . __( $label, 'buddyforms-members' ) . '</a>';
	}

	return $button;
}


add_filter( 'buddyforms_mail_to_before_send_notification', 'buddyforms_send_notifiction_to_member', 10, 3 );
function buddyforms_send_notifiction_to_member( $mail_to, $notification, $form_slug ) {
	global $buddyforms;

	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return $mail_to;
	}

	$form = $buddyforms[ $form_slug ];

	if ( $form['form_type'] !== 'contact' || ! isset( $form['bp_profile_member_message'] ) ) {
		return $mail_to;
	}

	$member = get_userdata( bp_displayed_user_id() );

	if ( ! isset( $member->user_email ) ) {
		return $mail_to;
	}

	if ( isset( $notification['mail_to'] ) && in_array( 'member', $notification['mail_to'] ) ) {
		array_push( $mail_to, $member->user_email );
	}

	return $mail_to;
}

add_filter( 'buddyforms_notifications_send_mail_to_options', 'buddyforms_notification_send_mail_to_member_option', 10, 3 );
function buddyforms_notification_send_mail_to_member_option( $mail_to_options, $trigger, $form_slug ) {
	global $buddyforms;

	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return $mail_to_options;
	}

	$form = $buddyforms[ $form_slug ];

	if ( $form['form_type'] !== 'contact' || ! isset( $form['bp_profile_member_message'] ) ) {
		return $mail_to_options;
	}

	$mail_to_options['member'] = __( 'Member - send to displayed member', 'buddyforms' );

	return $mail_to_options;
}

add_action( 'post_submitbox_start', 'buddyforms_check_send_message_to_member_conditions' );
function buddyforms_check_send_message_to_member_conditions() {
	global $buddyform;

	$messages = array();

	if ( ! isset( $buddyform['form_type'] )
		 || $buddyform['form_type'] !== 'contact'
		 || ! isset( $buddyform['bp_profile_member_message'] )
	) {
		return;
	}

	$mail_to       = array();
	$bf_notice     = new BfAdminNotices();
	$notifications = $buddyform['mail_submissions'];

	if ( ! is_array( $notifications ) ) {
		return;
	}

	foreach ( $notifications as $notification ) {
		if ( isset( $notification['mail_to'] ) && is_array( $notification['mail_to'] ) ) {
			$mail_to = array_merge( $mail_to, $notification['mail_to'] );
		}
	}

	if ( ! in_array( 'member', $mail_to ) ) {
		$messages[] = __( 'At least one Mail Notification must have selected Send mail to Member.', 'buddyforms' );
	}

	if ( ! empty( $messages ) ) {
		$bf_notice->show_form_notices( $messages );
	}
}

add_action( 'buddyforms_before_update_form_options', 'buddyforms_enable_at_least_one_send_to_member_in_notifications', 10, 2 );
function buddyforms_enable_at_least_one_send_to_member_in_notifications( $buddyform, $post_id ) {

	$old_data = get_post_meta( $post_id, '_buddyforms_options', true );

	// Send message to member is about to be enabled?
	if ( isset( $buddyform['bp_profile_member_message'] ) && ! isset( $old_data['bp_profile_member_message'] ) ) {

		$mail_to       = array();
		$notifications = $buddyform['mail_submissions'];

		if ( ! is_array( $notifications ) && count( $notifications ) > 0 ) {
			return $buddyform;
		}

		foreach ( $notifications as $notification ) {
			$mail_to = array_merge( $mail_to, $notification['mail_to'] );
		}

		if ( ! in_array( 'member', $mail_to ) ) {

			foreach ( $notifications as &$notification ) {
				$notification['mail_to'][] = 'member';
				break;
			}

			$buddyform['mail_submissions'] = $notifications;
		}
	}

	return $buddyform;
}
