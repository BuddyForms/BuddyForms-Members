<?php

/**
 * hook the buddypress default single.php hooks into the form display field
 * 
 * this functions is support for the bp_default theme and an can be used as example for other theme/plugin developer
 * how to hook there theme plugin hooks. 
 *
 * @package buddyforms
 * @since 0.2-beta
*/
function buddyforms_form_element_single_hooks($buddyforms_form_element_hooks,$post_type,$field_id){
	if(get_template() != 'bp-default')
		return $buddyforms_form_element_hooks;
	 
		array_push($buddyforms_form_element_hooks,
			'bp_before_blog_single_post',
			'bp_after_blog_single_post'
		);

	return $buddyforms_form_element_hooks;
}
add_filter('buddyforms_form_element_hooks','buddyforms_form_element_single_hooks',1,3);

/**
 * Get the buddyforms template directory.
 *
 * @author Sven Lehnert
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_members_get_template_directory() {
	return apply_filters('buddyforms_members_get_template_directory', constant('BUDDYFORMS_MEMBERS_TEMPLATE_PATH'));
}

/**
 * Locate a template
 *
 * @package BuddyPress Custom Group Types
 * @since 0.1-beta
 */
function buddyforms_members_locate_template($file) {
	if (locate_template(array($file), false)) {
		locate_template(array($file), true);
	} else {
		include (BUDDYFORMS_MEMBERS_TEMPLATE_PATH . $file);
	}
}
?>