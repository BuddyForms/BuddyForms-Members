<div id="item-body">
<?php 
global $bp, $buddyforms;

$post_id = 0;
$post_parent_id = 0;
$revision_id = '';
$current_component = $bp->current_component;
if(bp_current_action() == 'create' || bp_current_action() == $current_component . '-create'){
    if(isset($bp->action_variables[1]))
        $post_parent_id = $bp->action_variables[1];
}
if(bp_current_action() == 'edit' || bp_current_action() == $current_component . '-edit'){
    if(isset($bp->action_variables[1]))
        $post_id = $bp->action_variables[1];
}
if(bp_current_action() == 'revision'){
    if(isset($bp->action_variables[2]))
        $revision_id = $bp->action_variables[2];
}

$form_slug = $bp->current_component;
//echo do_shortcode('[buddyforms_form post_type="'.$post_type.'" form_slug="'.$form_slug.'" post_id="'.$post_id.'" ]');

foreach ($buddyforms as $key => $member_form) {
    if ( 'noparent' != $member_form['profiles_parent_tab'] && $current_component . '-create' ==  $bp->current_action ) {
        $form_slug = $member_form['slug'];
    }
    if ( 'noparent' != $member_form['profiles_parent_tab'] && $current_component . '-edit' ==  $bp->current_action ) {
        $form_slug = $member_form['slug'];
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
