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

echo $form_slug;
// echo '<pre>';
// print_r($form_slug);
// echo '</pre>';

// foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
		// if($selected_post_type['form'] == $bp->current_component)
			// $post_type = $selected_post_type['selected'][0];
			// $form_slug = $selected_post_type['form'];
// }
echo 'Da ' . $form_slug;
do_shortcode('[buddyforms_form post_type="'.$post_type.'" form_slug="'.$form_slug.'" post_id="'.$post_id.'" revision_id="'.$revision_id.'"]');


?>   
</div><!-- #item-body -->
