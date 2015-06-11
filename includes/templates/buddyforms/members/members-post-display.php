<div id="item-body">
	<?php 
	global $wp_query, $current_user, $the_lp_query, $bp, $buddyforms, $form_slug;
    $temp_query = $the_lp_query;
    $post_type = $buddyforms['buddyforms'][$bp->current_component]['post_type'];

	$form_slug = $bp->current_component;

	if ($bp->displayed_user->id == $current_user->ID){
		$args = array(
			'post_type'			=> $post_type,
			'form_slug'         => $form_slug,
			'post_status'		=> array('publish', 'pending', 'draft'),
			'posts_per_page'	=> 5,
			'post_parent'		=> 0,
            'paged'				=> $paged,
			'author'			=> get_current_user_id(),
            'meta_key'          => '_bf_form_slug',
            'meta_value'        => $form_slug
		);
	} else {
		$args = array(
			'post_type'			=> $post_type,
			'form_slug'         => $form_slug,
			'post_status'		=> array('publish'),
			'posts_per_page'	=> 5,
			'post_parent'		=> 0,
			'paged'				=> $paged,
			'author'			=> $bp->displayed_user->id,
            'meta_key'          => '_bf_form_slug',
            'meta_value'        => $form_slug
        );
	}

    $args =  apply_filters('bf_post_to_display_args',$args);

	$the_lp_query = new WP_Query( $args );

    buddyforms_locate_template('buddyforms/the-loop.php');

	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}
    $the_lp_query = $temp_query;
	?>              
</div><!-- #item-body -->