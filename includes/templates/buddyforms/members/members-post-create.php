<div id="item-body">
<?php 
global $bp, $buddyforms;

$post_id = 0;
if(isset($bp->action_variables[1]))
	$post_id = $bp->action_variables[1];

$revision_id = '';
if(isset($bp->action_variables[2]))
	$revision_id = $bp->action_variables[2];

$form_slug = $bp->current_component;
do_shortcode('[buddyforms_form post_type="'.$post_type.'" form_slug="'.$form_slug.'" post_id="'.$post_id.'" revision_id="'.$revision_id.'"]');

?>   
</div><!-- #item-body -->
