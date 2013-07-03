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


// handle custom page
// do flush if changing rule, then reload an admin page
add_action('admin_init', 'buddyforms_members_attached_page_rewrite_rules');
function buddyforms_members_attached_page_rewrite_rules(){
	global $buddyforms;

	if(!isset($buddyforms['buddyforms']))
		return;
	
		if(isset($buddyforms['selected_post_types'])){
		foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
				
			if(isset($buddyforms['buddyforms'][$selected_post_type['form']]['attached_page'])){
					
				$attached_page_id = $buddyforms['buddyforms'][$selected_post_type['form']]['attached_page'];
				$post_data = get_post($buddyform['attached_page'], ARRAY_A);
				
				add_rewrite_rule(bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/edit/([^/]+)/([^/]+)/?', 'index.php?pagename=members&bf_action=edit&bf_form_slug=$matches[1]&bf_post_id=$matches[2]', 'top');
			
				
			}

				
		}
	}

	flush_rewrite_rules();
}





/**
 * Get the redirect link
 *
 * @since 1.0.6
 */
function bf_members_get_redirect_link( $id = false ) {
	global $bp, $buddyforms, $wp_query;
//echo $id;
	// echo '<pre>';
		// print_r($bp);
		// echo '</pre>';
		
	if( ! $id )
		return false;
	
	$link = '';
	if(isset($buddyforms['selected_post_types'])){
		foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
				
			if(isset($buddyforms['buddyforms'][$selected_post_type['form']]['attached_page']))
				$attached_page_id = $buddyforms['buddyforms'][$selected_post_type['form']]['attached_page'];
			
			if(isset($attached_page_id) && $attached_page_id == $id){
				$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'];
				
				if(isset($bp->unfiltered_uri[1])){
					if($bp->unfiltered_uri[1] == 'edit')
						$link = bp_loggedin_user_domain() .$buddyforms['buddyforms'][$selected_post_type['form']]['slug'].'/'.$bp->unfiltered_uri[1].'/'.$bp->unfiltered_uri[2].'/'.$bp->unfiltered_uri[3];	
				}
				
			}
				
		}
	}
	//echo $link;
	// switch( $id ) {
		// case $cart_page_id:
			// $link = bp_loggedin_user_domain() .'shop/cart/';
			// break;
// 
		// default :
			// $link = '';
			// break;
	// }

	return apply_filters( 'bf_members_get_redirect_link', $link );
}

/**
 * Redirect the user to their respective profile page
 *
 * @since 1.0.6
 */
function bf_members_redirect_to_profile() {
	global $post, $wp_query;

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
 * @since 	1.0.6
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
//add_filter( 'page_link', 'bf_members_page_link_router', 10, 2 );