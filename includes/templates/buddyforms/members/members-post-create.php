    <div id="item-body">

  	<?php 
  	global $bp, $buddyforms;
	
	foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
			if($selected_post_type[form] == $bp->current_component)
				$post_type = $selected_post_type[selected][0];
	}
  	
  	do_shortcode('[buddyforms_form post_type="'.$post_type.'"]'); ?>
   
    </div><!-- #item-body -->
