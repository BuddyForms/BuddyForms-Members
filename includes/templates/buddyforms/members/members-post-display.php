<div id="item-body">

	<?php 
	global $current_user, $the_lp_query, $bp, $buddyforms, $form_slug;

	foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
			if(isset($selected_post_type['form']) && $selected_post_type['form'] == $bp->current_component)
				$post_type = $selected_post_type['selected'][0];
			
			if($bp->current_component == $key)
				$post_type = $key;
	}
	
	if ($bp->displayed_user->id == $current_user->ID){	
		$args = array( 
			'post_type' => $post_type,
			'post_status' => array('publish', 'pending', 'draft'),
			'posts_per_page' => 10,
			'author' => get_current_user_id() );
	} else {
		$args = array( 
			'post_type' => $post_type,
			'post_status' => array('publish'),
			'posts_per_page' => 10,
			'author' => $bp->displayed_user->id );
	}
	
	
	$the_lp_query = new WP_Query( $args );
	
	$form_slug = $bp->current_component;
	buddyforms_locate_template('buddyforms/the-loop.php');
	
	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}
	
	?>              

</div><!-- #item-body -->