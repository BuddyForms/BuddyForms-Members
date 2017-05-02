<div id="item-body">
	<?php
	global $wp_query, $current_user, $the_lp_query, $bp, $buddyforms, $buddyforms_member_tabs, $form_slug, $paged;

	$temp_query = $the_lp_query;

	$form_slug = $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ];
	$post_type = $buddyforms[ $form_slug ]['post_type'];

	$current_component = $bp->current_component;

	$list_posts_option = $buddyforms[ $form_slug ]['list_posts_option'];
	$list_posts_style  = $buddyforms[ $form_slug ]['list_posts_style'];

	$query_args = array(
		'post_type'      => $post_type,
		'form_slug'      => $form_slug,
		'post_status'    => array( 'publish' ),
		'posts_per_page' => 5,
		'post_parent'    => 0,
		'paged'          => $paged,
		'author'         => $bp->displayed_user->id,
		'meta_key'       => '_bf_form_slug',
		'meta_value'     => $form_slug
	);

	if ( isset( $list_posts_option ) && $list_posts_option == 'list_all' ) {
		unset( $query_args['meta_key'] );
		unset( $query_args['meta_value'] );
	}

	if ( $bp->displayed_user->id == $current_user->ID ) {
		$query_args['post_status'] = array( 'publish', 'pending', 'draft' );
	}

	// New
	$query_args = apply_filters( 'buddyforms_user_posts_query_args', $query_args );
	// Deprecated
	$query_args = apply_filters( 'buddyforms_post_to_display_args', $query_args );

	if ( is_multisite() && isset( $buddyforms[ $form_slug ]['blog_id'] ) ) {
		switch_to_blog( $buddyforms[ $form_slug ]['blog_id'] );
	}

	$the_lp_query = new WP_Query( $query_args );
	$the_lp_query = apply_filters('buddyforms_the_lp_query', $the_lp_query );

	if ( $list_posts_style == 'table' ) {
		buddyforms_locate_template( 'the-table' );
	} elseif( $list_posts_style == 'list') {
		buddyforms_locate_template( 'the-loop' );
	} else {
		buddyforms_locate_template( $list_posts_style );
	}


	if ( is_multisite() && isset( $buddyforms[ $form_slug ]['blog_id'] ) ) {
		restore_current_blog();
	}

	$the_lp_query = $temp_query;


	?>
</div><!-- #item-body -->
