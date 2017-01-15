<?php
/**
 * @package        WordPress
 * @subpackage    BuddyPress, BuddyForms
 * @author        Sven Lehnert
 * @copyright    2013, Sven Lehnert
 * @license        http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the redirect link
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
function bf_members_get_redirect_link( $id = false ) {
	global $bp, $buddyforms;


	if ( ! $id ) {
		return false;
	}

	if ( ! isset( $bp->unfiltered_uri[2] ) ) {
		return false;
	}

	$form_slug = $bp->unfiltered_uri[2];

	if ( ! isset( $buddyforms[ $form_slug ] ) ) {
		return false;
	}

	$parent_tab = buddyforms_members_parent_tab( $buddyforms[ $form_slug ] );

	$link = '';
	if ( isset( $buddyforms ) && is_array( $buddyforms ) && isset( $bp->unfiltered_uri[2] ) ) {

		if ( isset( $buddyforms[ $form_slug ]['attached_page'] ) ) {
			$attached_page_id = $buddyforms[ $form_slug ]['attached_page'];
		}

		if ( isset( $buddyforms[ $form_slug ]['profiles_integration'] ) && isset( $attached_page_id ) && $attached_page_id == $id ) {

			$link = bp_loggedin_user_domain() . $buddyforms[ $form_slug ]['slug'] . '/';

			if ( isset( $bp->unfiltered_uri[1] ) ) {
				if ( $bp->unfiltered_uri[1] == 'create' ) {
					$link = bp_loggedin_user_domain() . $parent_tab . '/' . $form_slug . '-create/';
				}
				if ( $bp->unfiltered_uri[1] == 'edit' ) {
					$link = bp_loggedin_user_domain() . $parent_tab . '/' . $form_slug . '-edit/' . $bp->unfiltered_uri[3];
				}
				if ( $bp->unfiltered_uri[1] == 'revision' ) {
					$link = bp_loggedin_user_domain() . $parent_tab . '/' . $form_slug . '-revision/' . $bp->unfiltered_uri[3] . '/' . $bp->unfiltered_uri[4];
				}
				if ( $bp->unfiltered_uri[1] == 'view' ) {
					$link = bp_loggedin_user_domain() . '/' . $parent_tab . '/' . $form_slug . '-posts';
				}
				if ( $bp->unfiltered_uri[1] == 'page' ) {
					$link = bp_loggedin_user_domain() . '/' . $parent_tab . '/' . $form_slug . '-posts/' . $bp->unfiltered_uri[2] . '/' . $bp->unfiltered_uri[3];
				}

			}

		}

	}

	return apply_filters( 'bf_members_get_redirect_link', $link );
}

/**
 * Redirect the user to their respective profile page
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
function bf_members_redirect_to_profile() {
	global $post;

	if ( ! isset( $post->ID ) || ! is_user_logged_in() ) {
		return false;
	}

	$link = bf_members_get_redirect_link( $post->ID );

	if ( ! empty( $link ) ) :
		wp_safe_redirect( $link );
		exit;
	endif;
}

add_action( 'template_redirect', 'bf_members_redirect_to_profile' );

/**
 * Link router function
 *
 * @package BuddyForms
 * @since 0.3 beta
 * @uses    bp_get_option()
 * @uses    is_page()
 * @uses    bp_loggedin_user_domain()
 */
function bf_members_page_link_router( $link, $id ) {
	if ( ! is_user_logged_in() || is_admin() ) {
		return $link;
	}

	$new_link = bf_members_get_redirect_link( $id );

	if ( ! empty( $new_link ) ) {
		$link = $new_link;
	}

	return apply_filters( 'bf_members_router_link', $link );
}

add_filter( 'page_link', 'bf_members_page_link_router', 10, 2 );

function bf_members_page_link_router_edit( $link, $id ) {
	global $buddyforms;

	$form_slug = get_post_meta( $id, '_bf_form_slug', true );

	$buddyforms_posttypes_default = get_option( 'buddyforms_posttypes_default' );
	$post_type                    = get_post_type( $id );

	if ( isset( $buddyforms_posttypes_default[ $post_type ] ) ) {
		$form_slug = $buddyforms_posttypes_default[ $post_type ];
	}

	if ( ! $form_slug ) {
		return $link;
	}

	if ( ! isset( $buddyforms[ $form_slug ]['profiles_integration'] ) ) {
		return $link;
	}

	$parent_tab = buddyforms_members_parent_tab( $buddyforms[ $form_slug ] );

	return '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . $id . '" class="bf_edit_post" href="' . bp_loggedin_user_domain() . $parent_tab . '/' . $form_slug . '-edit/' . $id . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"></span></a>';
}

add_filter( 'buddyforms_loop_edit_post_link', 'bf_members_page_link_router_edit', 10, 2 );


function bf_members_page_link_router_pagination( $result ) {
	global $bp, $buddyforms_member_tabs;

	if ( is_admin() ) {
		return $result;
	}

	if ( isset( $bp->current_component ) && isset( $buddyforms_member_tabs[ $bp->current_component ] ) ) {
		$current_user = wp_get_current_user();
		$userdata     = get_userdata( $current_user->ID );

		$result    = rtrim( $result, "/" );
		$this_page = end( explode( '/', $result ) );

		$result = get_bloginfo( 'url' ) . '/' . $bp->pages->members->slug . '/' . $userdata->user_nicename . '/' . $bp->current_component . '/' . $bp->current_action . '/page/' . $this_page;
	}

	return $result;

}

add_filter( 'get_pagenum_link', 'bf_members_page_link_router_pagination', 10, 2 );
