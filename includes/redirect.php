<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress, BuddyForms
 * @author		Sven Lehnert
 * @copyright	2013, Sven Lehnert
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the redirect link
 * 
 * @package BuddyForms
 * @since 0.3 beta
 */
function bf_members_get_redirect_link( $id = false ) {
	global $bp, $buddyforms;
		
	if( ! $id )
		return false;

    $link = '';
	if(isset($buddyforms['buddyforms'])){
		foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
				
			if(isset($buddyform['attached_page']))
				$attached_page_id = $buddyform['attached_page'];
			
			if(isset($buddyform['profiles_integration']) && isset($attached_page_id) && $attached_page_id == $id){

				$link = bp_loggedin_user_domain() .$buddyform['slug'].'/';
				
				if(isset($bp->unfiltered_uri[1])){
					if($bp->unfiltered_uri[1] == 'create')
                        if($bp->unfiltered_uri[2]){
                            $link = bp_loggedin_user_domain() .$buddyform['slug'].'/create/'.$bp->unfiltered_uri[2];
                        } else{
                           $link = bp_loggedin_user_domain() .$buddyform['slug'].'/create/';
                        }

					if($bp->unfiltered_uri[1] == 'edit')
						$link = bp_loggedin_user_domain() .$buddyform['slug'].'/edit/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];
					if($bp->unfiltered_uri[1] == 'revision')
                        $link = bp_loggedin_user_domain() .$buddyform['slug'].'/revision/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3].'/'.$bp->unfiltered_uri[4];
                    if($bp->unfiltered_uri[1] == 'page')
                        $link = bp_loggedin_user_domain() .$buddyform['slug'].'/page/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];
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

	if( ! isset( $post->ID ) || ! is_user_logged_in() )
		return false;

	$link = bf_members_get_redirect_link( $post->ID );

	if( ! empty( $link ) ) :
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
 * @uses	bp_get_option()
 * @uses	is_page()
 * @uses	bp_loggedin_user_domain()
 */
function bf_members_page_link_router( $link, $id )	{
	if( ! is_user_logged_in() || is_admin() )
		return $link;

	$new_link = bf_members_get_redirect_link( $id );

	if( ! empty( $new_link ) )
		$link = $new_link;

	return apply_filters( 'bf_members_router_link', $link );
}
add_filter( 'page_link', 'bf_members_page_link_router', 10, 2 );