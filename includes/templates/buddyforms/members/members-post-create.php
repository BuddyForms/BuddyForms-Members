<div id="item-body">
<?php 
global $bp, $buddyforms;

$post_id = 0;
$post_parent_id = 0;
$revision_id = '';
$current_component = $bp->current_component;
if(bp_current_action() == 'create' || bp_current_action() == $member_form['slug'] . '-create'){
    if(isset($bp->action_variables[1]))
        $post_parent_id = $bp->action_variables[1];
}
if(bp_current_action() == 'edit' || bp_current_action() == $member_form['slug'] . '-edit'){
    if(isset($bp->action_variables[1]))
        $post_id = $bp->action_variables[1];
}
if(bp_current_action() == 'revision'){
    if(isset($bp->action_variables[2]))
        $revision_id = $bp->action_variables[2];
}

$form_slug = $bp->current_component;
//echo do_shortcode('[buddyforms_form post_type="'.$post_type.'" form_slug="'.$form_slug.'" post_id="'.$post_id.'" ]');

// for create sub nav for child tabs.
if ( $bp->current_action != 'create' && ! empty( strpos($bp->current_action, '-create') )  ) {
        $patterns[0] = '/-create/';
        $replacements[0] = '';
        $form_slug = preg_replace($patterns, $replacements, $bp->current_action);
}

// set formslug and post type
foreach ($buddyforms as $key => $member_form) {
    
    if ( $member_form['slug']  . '-create' ==  $bp->current_action ) {
        $post_type = $member_form['post_type']; // get post type for child tab 
    }
    if ( 'edit' ==  $bp->current_action ) {
        $post_type = $member_form['post_type']; // get post type for child tab 
        // $form_slug = $bp->current_component;
    }
    if ( 'noparent' != $member_form['profiles_parent_tab'] && 'edit' ==  $bp->current_action ) {
        $form_slug =  $bp->current_component;
    }
}

// for edit section child sub nav.
foreach ($buddyforms as $key => $member_form) {
    if ('edit' ==  $bp->current_action ) {
        $absolute_url = full_url( $_SERVER );
        $pieces = explode("/edit/", $absolute_url);
        if (!empty($pieces[1])) {
            $form_slug_string = $pieces[1];
            $form_slug_array = explode("/", $form_slug_string);
            $form_slug =  $form_slug_array[0];
            $member_form['slug'];
            if ($member_form['slug'] == $form_slug ) {
                $post_type = $member_form['post_type'];
                continue;
            } 
        }

    }
}

$args = array(
	'form_slug'		=> $form_slug,
	'post_id'		=> $post_id,
    'post_parent'	=> $post_parent_id,
    'post_type'		=> $post_type,
	'revision_id'	=> $revision_id
);

buddyforms_create_edit_form($args);

?>   
</div><!-- #item-body -->
