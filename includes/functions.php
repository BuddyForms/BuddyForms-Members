<?php

function members_form($form, $post_type){
	global $buddyforms;

	$form = $buddyforms['selected_post_types'][$post_type]['form'];

	return $form;
}
add_filter('buddyforms_the_form_to_use','members_form',1,2);


function buddyforms_set_globals_new_slug($buddyforms,$new_slug,$old_slug){
	
	if(!isset($buddyforms['selected_post_types']))
		return $buddyforms;
	
	foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {	
		if($selected_post_type['form'] == $old_slug){
			$buddyforms['selected_post_types'][$key]['form'] = $new_slug;
		}
	}
	return $buddyforms;
}
add_filter('buddyforms_set_globals_new_slug','buddyforms_set_globals_new_slug',1,3);

function buddyforms_set_globals_members($buddyforms){
	
	if(!isset($buddyforms['selected_post_types']))
		return $buddyforms;
	
	$theposttypes = Array();
	foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
		
		if(isset($selected_post_type['selected'])){
			$theposttypes[$key] = $selected_post_type;
		}
	}
	$buddyforms['selected_post_types'] = $theposttypes;

	return $buddyforms;
}
add_filter('buddyforms_set_globals','buddyforms_set_globals_members',1,1);

function buddyforms_select_posttypes($form){
	global $buddyforms; 
		// Get all post types
    $args=array(
		'public' => true,
		'show_ui' => true
    ); 
    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types=get_post_types($args,$output,$operator); 
	
	if(is_array($buddyforms['buddyforms'])){
		$the_forms[] = 'Select the form to use';
		
		foreach ($buddyforms['buddyforms'] as $key => $buddyform) {
			$the_forms[] = $buddyform['slug'];
		}
		
		$form_fields = Array();
		$form->addElement( new Element_HTML('<ul class="buddyforms_members">'));
		$form->addElement( new Element_HTML('<p>Select the PostType you want to use in BuddyPress Profiles</p>'));
		foreach( $post_types as $key => $post_type) {
			$form->addElement( new Element_HTML('<li>'));
				$form->addElement( new Element_Checkbox("","buddyforms_options[selected_post_types][".$post_type."][selected]",array($post_type),array('id' => 'select_posttype_'.$post_type, 'class' => 'select_posttype', 'value' => $buddyforms['selected_post_types'][$post_type]['selected'])));
				
				$form->addElement( new Element_Select("", "buddyforms_options[selected_post_types][".$post_type."][form]", $the_forms, array('class' => 'select_posttype_'.$post_type.'-0 bf_select','value' => $buddyforms['selected_post_types'][$post_type]['form'])));
			$form->addElement(new Element_HTML('</li>'));
		}
		$form->addElement(new Element_HTML('</ul>'));
					
	}
return $form;	
}
add_filter('buddyforms_general_settings','buddyforms_select_posttypes',1,1);

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