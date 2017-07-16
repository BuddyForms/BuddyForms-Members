<?php
function buddyforms_members_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_members', __( "BP Member Profiles", 'buddyforms' ), 'buddyforms_members_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_members', 'buddyforms_metabox_class' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_members', 'buddyforms_metabox_hide_if_form_type_register' );
	add_filter( 'postbox_classes_buddyforms_buddyforms_members', 'buddyforms_metabox_show_if_attached_page' );
}


function buddyforms_members_admin_settings_sidebar_metabox_html() {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$buddyform = get_post_meta( get_the_ID(), '_buddyforms_options', true );

	$form_setup = array();

	$attache = '';
	if ( isset( $buddyform['profiles_integration'] ) ) {
		$attache = $buddyform['profiles_integration'];
	}

	$profiles_parent_tab = false;
	if ( isset( $buddyform['profiles_parent_tab'] ) ) {
		$profiles_parent_tab = $buddyform['profiles_parent_tab'];
	}

	$profile_visibility = 'any';
	if ( isset( $buddyform['profile_visibility'] ) ) {
		$profile_visibility = $buddyform['profile_visibility'];
	}

	$bp_activity_stream = false;
	if ( isset( $buddyform['bp_activity_stream'] ) ) {
		$bp_activity_stream = $buddyform['bp_activity_stream'];
	}


	$form_setup[] = new Element_Checkbox( "<b>" . __( 'Add this form as Profile Tab', 'buddyforms' ) . "</b>", "buddyforms_options[profiles_integration]", array( "integrate" => "Integrate this Form" ), array( 'value'     => $attache,
	                                                                                                                                                                                                             'shortDesc' => __( 'Many forms can share the same attached page. All Forms with the same attached page can be grouped together with this option. All Forms will be listed as sub nav tabs of the page main nav', 'buddyforms' )
	) );
	$form_setup[] = new Element_Checkbox( "<br><b>" . __( 'Use Attached Page as Parent Tab and make this form a sub tab of the parent', 'buddyforms' ) . "</b>", "buddyforms_options[profiles_parent_tab]", array( "attached_page" => "Use Attached Page as Parent" ), array( 'value'     => $profiles_parent_tab,
	                                                                                                                                                                                                                                                                          'shortDesc' => __( 'Many Forms can have the same attached Page. All Forms with the same page with page as parent enabled will be listed as sub forms. This why you can group forms.', 'buddyforms' )
	) );
	$element = new Element_Checkbox( "<br><b>" . __( 'Activity Support', 'buddyforms' ) . "</b>", "buddyforms_options[bp_activity_stream]", array( "bp_activity_stream" => "Add Activity Support" ), array( 'value'     => $bp_activity_stream,
	                                                                                                                                                                                                                                                                          'shortDesc' => __( 'Every time a new Post is created with this Form a BuddyPress Activity Item will be added to the Members Activity Stream', 'buddyforms' )
	) );
	if ( buddyforms_members_fs()->is_not_paying() ) {
		$element->setAttribute( 'disabled', 'disabled' );
	}
	$form_setup[] = $element;

	$form_setup[] = new Element_Select( "<br><b>" . __( 'Visibility', 'buddyforms' ) . "</b>", "buddyforms_options[profile_visibility]", array( "private"        => "Private - Only the logged in member in his profile.",
	                                                                                                                                            "logged_in_user" => "Community - Logged in user can see other users profile posts",
	                                                                                                                                            "any"            => "Public Visible - Unregistered users can see user profile posts"
	), array( 'value'     => $profile_visibility,
	          'shortDesc' => __( 'Who can see submissions in Profiles?', 'buddyforms' )
	) );

	buddyforms_display_field_group_table( $form_setup );

}

add_filter( 'add_meta_boxes', 'buddyforms_members_admin_settings_sidebar_metabox' );

/*
 * Add the xprofile Form ELement to the Form Element Select
 */
add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_members_add_form_element_to_select', 1, 2 );
function buddyforms_members_add_form_element_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}

	$elements_select_options['buddyforms']['label']                  = 'Buddyforms';
	$elements_select_options['buddyforms']['class']                = 'bf_show_if_f_type_all';
	$elements_select_options['buddyforms']['fields']['bp_member_type'] = array(
		'label'  => __( 'Member Types', 'buddyforms' ),
		'unique'    => 'unique'
	);
	$elements_select_options['buddyforms']['fields']['xprofile_field'] = array(
		'label'  => __( 'xProfile Field', 'buddyforms' ),
	);
	$elements_select_options['buddyforms']['fields']['xprofile_group'] = array(
		'label'  => __( 'xProfile Field Group', 'buddyforms' ),
	);

	return $elements_select_options;
}

/*
 * Create the new Form Builder Form Element
 */
add_filter( 'buddyforms_form_element_add_field', 'buddyforms_members_create_new_form_builder_form_element', 1, 5 );
function buddyforms_members_create_new_form_builder_form_element( $form_fields, $form_slug, $field_type, $field_id ) {
	global $buddyforms;

	switch ( $field_type ) {

		case 'bp_member_type':
			unset( $form_fields );

			$name = isset( $buddyforms_options[ $form_slug ]['form_fields'][ $field_id ]['name'] ) ? $buddyforms_options[ $form_slug ]['form_fields'][ $field_id ]['name'] : 'Member Type';
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array( 'value' => $name ) );

			$member_types_select = bp_get_member_types();
			$member_types = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_types']) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_types'] : '';
			$element = new Element_Checkbox('Member Types', "buddyforms_options[form_fields][" . $field_id . "][member_types]", $member_types_select, array( 'value' => $member_types ) );
			if ( buddyforms_members_fs()->is_not_paying() ) {
				$element->setAttribute( 'disabled', 'disabled' );
			}
			$form_fields['general']['member_types'] = $element;


			$form_fields['general']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'bp_member_type' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			break;
		case 'xprofile_group':
			unset( $form_fields );

			$name = 'xProfile Group';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] ) && $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] != 'none' ) {
				$name .= ' ' . $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'];
			}
			$form_fields['general']['name'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $name );
			$form_fields['general']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'xprofile_group' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );

			if( class_exists('BP_XProfile_Group')){

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				$groups_select['none'] = 'Select the xProfile Group';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					$groups_select[$group->id] = $group->name;
				endforeach; endif;

				$xprofile_group = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group']) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] : '';
				$element = new Element_Select('xProfile Group', "buddyforms_options[form_fields][" . $field_id . "][xprofile_group]", $groups_select, array( 'value' => $xprofile_group ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_group'] = $element;

			} else{
				$form_fields['general']['notice'] = new Element_HTML(__( 'You need to enable BuddyPress Groups to use this form element', 'buddyforms'));
			}
			break;
		case 'xprofile_field':


			unset( $form_fields );

			$name = 'xProfile Field';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] ) && $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] != 'none' ) {
				$name .= ' ' . $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'];
			}
			$form_fields['general']['name'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $name );
			$form_fields['general']['slug'] = new Element_Hidden("buddyforms_options[form_fields][" . $field_id . "][slug]", 'xprofile_field' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );


			if( class_exists('BP_XProfile_Group')){

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				$groups_select['none'] = 'Select the xProfile Group';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					$groups_select[$group->id] = $group->name;
				endforeach; endif;

				$xprofile_group = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group']) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] : '';
				$element = new Element_Select('xProfile Group', "buddyforms_options[form_fields][" . $field_id . "][xprofile_group]", $groups_select, array( 'value' => $xprofile_group ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_group'] = $element;

				$group_fields_select['none'] = 'Select the xProfile Field';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :

					if( $group->id == $xprofile_group ) {

						if ( ! empty( $group->fields ) ) :
							foreach ( $group->fields as $field ) {
								$group_fields_select[$field->id] = $field->name;
							}
						endif;

					}
				endforeach; endif;

				$xprofile_field = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field']) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] : '';
				$element = new Element_Select('xProfile Field', "buddyforms_options[form_fields][" . $field_id . "][xprofile_field]", $group_fields_select, array( 'value' => $xprofile_field ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_field'] = $element;

			} else{
				$form_fields['general']['notice'] = new Element_HTML(__( 'You need to enable BuddyPress Groups to use this form element', 'buddyforms'));
			}

			break;

	}

	return $form_fields;
}

/*
 * Display the new Form Element in the Frontend Form
 *
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_members_create_frontend_form_element', 1, 2 );
function buddyforms_members_create_frontend_form_element( $form, $form_args ) {
	global $buddyforms, $field;

	extract( $form_args );

	$post_type = $buddyforms[ $form_slug ]['post_type'];

	if ( ! $post_type ) {
		return $form;
	}

	if ( ! isset( $customfield['type'] ) ) {
		return $form;
	}

	switch ( $customfield['type'] ) {
		case 'bp_member_type':

			$element_attr = array(
				'value'     => is_user_logged_in() ? bp_get_member_type( get_current_user_id() ) : '',
				'class'     => 'settings-input',
				'shortDesc' => empty($customfield['description']) ? '' : $customfield['description'],
			);
			$form->addElement( new Element_Select( $customfield['name'], $customfield['slug'], $customfield['member_types'], $element_attr ) );
			break;
		case 'xprofile_group':

			if( class_exists('BP_XProfile_Group')) {

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					if( $group->id == $customfield['xprofile_group'] ) {
						if ( bp_has_profile( 'profile_group_id=' . $group->id ) ) :
							while ( bp_profile_groups() ) : bp_the_profile_group();
								while ( bp_profile_fields() ) : bp_the_profile_field();
									$field = new BP_XProfile_Field( bp_get_the_profile_field_id() );
									$form->addElement( new Element_HTML( '<div class="bf_field_group bf-input">' . buddyforms_members_edit_field_html($form_slug)  . '</div>') );
								endwhile;
						endwhile; endif;
					}
				endforeach;endif;
			}

			break;
		case 'xprofile_field':

			if( class_exists('BP_XProfile_Field')) {
				$field = new BP_XProfile_Field($customfield['xprofile_field']);
				$form->addElement( new Element_HTML( '<div class="bf_field_group bf-input">' . buddyforms_members_edit_field_html($form_slug) . '</div>') );
			}

			break;
	}

	return $form;
}
function buddyforms_everything_in_tags($string, $tagname)
{
	$pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
	preg_match($pattern, $string, $matches);
	return $matches[1];
}

function buddyforms_members_edit_field_html($form_slug){
	global $buddyforms, $field;

	$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );

	ob_start();
	if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
		echo '<span class="help-inline">' . bp_the_profile_field_description() . '</span>';
		$field_type->edit_field_html();
	} else {
		$field_type->edit_field_html();
		echo '<span class="help-inline">' . bp_the_profile_field_description() . '</span>';
	}
	$tmp = ob_get_clean();

	$tmp = str_replace( '<input', '<input class="form-control"', $tmp );

	if ( isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) && $buddyforms[ $form_slug ]['layout']['labels_layout'] == 'inline' ) {

		$label = buddyforms_everything_in_tags( $tmp, 'label' );

		$tmp = str_replace( $label, '', $tmp );

		$label = preg_replace( '/\s+/', '', strip_tags( $label ) );

		if ( strpos( $label, '(required)' ) !== false ) {
			$tmp = str_replace( '<input', '<input required "', $tmp );
			$label = str_replace( '(required)', '', strip_tags( $label ) );
			$label = '* ' . $label;
		}
		$tmp = str_replace( '<input', '<input placeholder="' . $label . '"', $tmp );
	} else {
		if ( strpos( $tmp, '(required)' ) !== false ) {
			$tmp = str_replace( '<input', '<input required "', $tmp );
			$tmp = str_replace( '(required)', '<span class="required"> *</span>', $tmp );
		}

	}

	return $tmp;
}

add_action( 'buddyforms_process_submission_end', 'buddyforms_members_process_submission_end', 10, 1 );
function buddyforms_members_process_submission_end( $args ) {
	global $buddyforms;

	extract( $args );

	if( isset( $buddyforms[$form_slug] ) ){
		if( isset( $buddyforms[$form_slug]['form_fields'] ) ){

			foreach ($buddyforms[$form_slug]['form_fields'] as $field_key => $field ){

				if( $field['type'] == 'bp_member_type' ){
					if( isset( $user_id ) ){
						bp_set_member_type( $user_id, $_POST[$field['slug']] );
					}
				}

				if( $field['type'] == 'xprofile_group' ){
					if( isset( $field['xprofile_group'] ) ){
						if ( bp_has_profile( 'profile_group_id=' . $field['xprofile_group'] ) ) :
							while ( bp_profile_groups() ) : bp_the_profile_group();
								while ( bp_profile_fields() ) : bp_the_profile_field();
									if( isset( $_POST['field_' . bp_get_the_profile_field_id()] ) ){
										$xprofile_value = $_POST['field_' . bp_get_the_profile_field_id()];
										xprofile_set_field_data( bp_get_the_profile_field_id(), bp_loggedin_user_id(),  $xprofile_value );
									}
								endwhile;
							endwhile;
						endif;
					}

				}
				if( $field['type'] == 'xprofile_field' && isset($field['xprofile_field'])){
					$field_id = $field['xprofile_field'];
					if( isset( $_POST['field_' . $field_id ] ) ){
						$xprofile_value = $_POST[ 'field_' . $field_id ];
						xprofile_set_field_data( $field_id, bp_loggedin_user_id(),  $xprofile_value );
					}
				}
			}

		}
	}

}