<?php
function buddyforms_members_admin_settings_sidebar_metabox() {
	add_meta_box( 'buddyforms_members', __( "BP Member Profiles", 'buddyforms-members' ), 'buddyforms_members_admin_settings_sidebar_metabox_html', 'buddyforms', 'normal', 'low' );
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

	$bp_profile_member_message = false;
	if ( isset( $buddyform['bp_profile_member_message'] ) ) {
		$bp_profile_member_message = $buddyform['bp_profile_member_message'];
	}


	$form_setup[] = new Element_Checkbox( "<b>" . __( 'Add this form as Profile Tab', 'buddyforms-members' ) . "</b>", "buddyforms_options[profiles_integration]", array( "integrate" => "Integrate this Form" ), array(
		'value'     => $attache,
		'shortDesc' => __( 'Many forms can share the same attached page. All Forms with the same attached page can be grouped together with this option. All Forms will be listed as sub nav tabs of the page main nav', 'buddyforms-members' )
	) );
	$form_setup[] = new Element_Checkbox( "<br><b>" . __( 'Use Attached Page as Parent Tab and make this form a sub tab of the parent', 'buddyforms-members' ) . "</b>", "buddyforms_options[profiles_parent_tab]", array( "attached_page" => "Use Attached Page as Parent" ), array(
		'value'     => $profiles_parent_tab,
		'shortDesc' => __( 'Many Forms can have the same attached Page. All Forms with the same page with page as parent enabled will be listed as sub forms. This why you can group forms.', 'buddyforms-members' )
	) );

	if ( isset( $buddyform['form_type'] ) && $buddyform['form_type'] === 'contact' ) {
		$form_setup[]  = new Element_Checkbox( "<br><b>" . __( 'Send Message to Member', 'buddyforms-members' ) . "</b>", "buddyforms_options[bp_profile_member_message]", array( "bp_profile_member_message" => "Integrate this form as a member contact form" ), array(
			'value'     => $bp_profile_member_message,
			'shortDesc' => __( 'Visibility: This tab is only visible in other user profiles. You will not see it in your profile. It is visible to your profile visitors only. A new send to option is added to the notifications. Please make sure you have at least one notification send to the member.' )
		) );
	}

	$element      = new Element_Checkbox( "<br><b>" . __( 'Activity Support', 'buddyforms-members' ) . "</b>", "buddyforms_options[bp_activity_stream]", array( "bp_activity_stream" => "Add Activity Support" ), array(
		'value'     => $bp_activity_stream,
		'shortDesc' => __( 'Every time a new Post is created with this Form a BuddyPress Activity Item will be added to the Members Activity Stream', 'buddyforms-members' )
	) );

	if ( buddyforms_members_fs()->is_not_paying() ) {
		$element->setAttribute( 'disabled', 'disabled' );
	}
	$form_setup[] = $element;


	$form_setup[] = new Element_Select( "<br><b>" . __( 'Visibility', 'buddyforms-members' ) . "</b>", "buddyforms_options[profile_visibility]", array(
		"private"        => "Private - Only the logged in member in his profile.",
		"logged_in_user" => "Community - Logged in user can see other users profile posts",
		"any"            => "Public Visible - Unregistered users can see user profile posts"
	), array(
		'value'     => $profile_visibility,
		'shortDesc' => __( 'Who can see submissions in Profiles?', 'buddyforms-members' )
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
		return $elements_select_options;
	}

	$elements_select_options['buddyforms']['label']                     = 'BuddyPress';
	$elements_select_options['buddyforms']['class']                     = 'bf_show_if_f_type_all';
	$elements_select_options['buddyforms']['fields']['bp_member_type']  = array(
		'label'  => __( 'Member Types', 'buddyforms-members' ),
		'unique' => 'unique'
	);
	$elements_select_options['buddyforms']['fields']['member_taxonomy'] =
		array(
			'label' => __( 'Member Taxonomy', 'buddyforms-members' ),
		);
	$elements_select_options['buddyforms']['fields']['xprofile_field']  = array(
		'label' => __( 'xProfile Field', 'buddyforms-members' ),
		'is_pro' => true,
	);
	$elements_select_options['buddyforms']['fields']['xprofile_group']  = array(
		'label' => __( 'xProfile Field Group', 'buddyforms-members' ),
		'is_pro' => true,
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

			$name                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['name'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['name'] : 'Member Type';
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Name', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array( 'value' => $name ) );


			$member_types_object = bp_get_member_types(array(),'objects');

			$member_types_select = array();
			if( is_array( $member_types_object ) ){
				foreach ( $member_types_object as $key => $value ){
					$member_types_select[$key] = $value->labels['name'];
				}
			}


			$member_types        = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_types'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_types'] : '';
			$element             = new Element_Checkbox( '<b>Allow the User to select Member Types</b>', "buddyforms_options[form_fields][" . $field_id . "][member_types]", $member_types_select, array( 'value' => $member_types ) );
			if ( buddyforms_members_fs()->is_not_paying() ) {
				$element->setAttribute( 'disabled', 'disabled' );
			}
			$form_fields['general']['member_types'] = $element;

			$member_type_hidden       = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_type_hidden'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_type_hidden'] : '';
			$member_type_hidden_prop  = array( 'value' => buddyforms_members_fs()->is_not_paying() ? true : $member_type_hidden, 'shortDesc' => 'Hide this form element to auto assign the default member type');
			$element                  = new Element_Checkbox( '<b>Hide this form element</b>', "buddyforms_options[form_fields][" . $field_id . "][member_type_hidden]", array( 'hide' => 'Hidden' ), $member_type_hidden_prop );
			
			if ( buddyforms_members_fs()->is_not_paying() ) {
				$element->setAttribute( 'disabled', 'disabled' );
			}
			$form_fields['general']['member_type_hidden'] = $element;

			$member_types                     = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_type_default'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_type_default'] : '';
			$form_fields['general']['hidden'] = new Element_Select( '<b>Default Member Type For this Form</b>', "buddyforms_options[form_fields][" . $field_id . "][member_type_default]", $member_types_select, array( 'value'     => $member_types,
			                                                                                                                                                                                                     'shortDesc' => 'Only works in combination with the Hidden option',
			) );


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

			if ( class_exists( 'BP_XProfile_Group' ) ) {

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				$groups_select['none'] = 'Select the xProfile Group';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					$groups_select[ $group->id ] = $group->name;
				endforeach; endif;

				$xprofile_group = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] : '';
				$element        = new Element_Select( '<b>xProfile Group</b>', "buddyforms_options[form_fields][" . $field_id . "][xprofile_group]", $groups_select, array( 'value' => $xprofile_group ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_group'] = $element;

			} else {
				$form_fields['general']['notice'] = new Element_HTML( __( 'You need to enable BuddyPress Groups to use this form element', 'buddyforms-members' ) );
			}
			break;
		case 'xprofile_field':

			unset( $form_fields );

			$name = 'xProfile Field';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] ) && $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] != 'none' ) {
				$xfield = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'];
				$field  = new BP_XProfile_Field( $xfield );
				$name   = $field->name;;
			}
			$form_fields['general']['name'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][name]", $name );
			$form_fields['general']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'xprofile_field' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );


			if ( class_exists( 'BP_XProfile_Group' ) ) {

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				$groups_select['none'] = 'Select the xProfile Group';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					$groups_select[ $group->id ] = $group->name;
				endforeach; endif;

				$xprofile_group = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_group'] : '';
				$element        = new Element_Select( '<b>xProfile Group</b>', "buddyforms_options[form_fields][" . $field_id . "][xprofile_group]", $groups_select, array( 'value' => $xprofile_group ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_group'] = $element;

				$group_fields_select['none'] = 'Select the xProfile Field';
				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :

					if ( $group->id == $xprofile_group ) {

						if ( ! empty( $group->fields ) ) :
							foreach ( $group->fields as $field ) {
								$group_fields_select[ $field->id ] = $field->name;
							}
						endif;

					}
				endforeach; endif;

				$xprofile_field = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['xprofile_field'] : '';
				$element        = new Element_Select( '<b>xProfile Field</b>', "buddyforms_options[form_fields][" . $field_id . "][xprofile_field]", $group_fields_select, array( 'value' => $xprofile_field ) );
				if ( buddyforms_members_fs()->is_not_paying() ) {
					$element->setAttribute( 'disabled', 'disabled' );
				}
				$form_fields['general']['xprofile_field'] = $element;

			} else {
				$form_fields['general']['notice'] = new Element_HTML( __( 'You need to enable BuddyPress Groups to use this form element', 'buddyforms-members' ) );
			}

			break;
		case 'member_taxonomy':

			unset( $form_fields['advanced'] );

			$form_fields['general']['slug'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'member-taxonomy' );
			$form_fields['general']['type'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", 'member_taxonomy' );

			$taxonobuddyforms_membersobjects = get_taxonomies();

			$member_taxonomy                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_taxonomy'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['member_taxonomy'] : '';
			$form_fields['general']['member_taxonomy'] = new Element_Select( '<b>' . __( 'Member Taxonomy', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][member_taxonomy]", $taxonobuddyforms_membersobjects, array(
				'value'    => $member_taxonomy,
				'class'    => 'bf_tax_select',
				'field_id' => $field_id,
				'id'       => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
			) );

			$placeholder                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['placeholder'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['placeholder'] ) : 'Select an Option';
			$form_fields['general']['placeholder'] = new Element_Textbox( '<b>' . __( 'Taxonomy Placeholder', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][placeholder]", array(
				'data'      => $field_id,
				'value'     => $placeholder,
				'shortDesc' => __( 'You can change the placeholder to something meaningful like Select a Category or what make sense for your taxonomy.' )
			) );

			$multiple                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple'] : 'false';
			$form_fields['general']['multiple'] = new Element_Checkbox( '<b>' . __( 'Multiple Selection', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple]", array( 'multiple' => '<b>' . __( 'Multiple', 'buddyforms-members' ) . '</b>' ), array(
				'value' => $multiple,
				'class' => ''
			) );

			$tmaximumSelectionLength                          = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['maximumSelectionLength'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['maximumSelectionLength'] ) : 0;
			$form_fields['general']['maximumSelectionLength'] = new Element_Number( '<b>' . __( 'Limit Selections', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][maximumSelectionLength]", array(
				'data'      => $field_id,
				'value'     => $tmaximumSelectionLength,
				'shortDesc' => __( 'Add a number to limit the Selection amount' )
			) );

			$create_new_tax                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['create_new_tax'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['create_new_tax'] : 'false';
			$form_fields['general']['create_new_tax'] = new Element_Checkbox( '<b>' . __( 'New Taxonomy Item', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][create_new_tax]", array( 'user_can_create_new' => '<b>' . __( 'User can create new', 'buddyforms-members' ) . '</b>' ), array(
				'value' => $create_new_tax,
				'class' => ''
			) );

			$is_ajax                        = isset($buddyforms[$form_slug]['form_fields'][$field_id]['ajax']) ? $buddyforms[$form_slug]['form_fields'][$field_id]['ajax'] : 'false';
			$form_fields['general']['ajax'] = new Element_Checkbox('<b>' . __('Ajax', 'buddyforms-members') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][ajax]", array('is_ajax' => '<b>' . __('Enabled Ajax', 'buddyforms-members') . '</b>'), array(
				'value' => $is_ajax,
				'data'  => $field_id,
				'class' => 'bf_taxonomy_ajax_ready'
			));

			$minimum_input_length                         = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['minimumInputLength'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['minimumInputLength'] : 0;
			$form_fields['general']['minimumInputLength'] = new Element_Number( '<b>' . __( 'Minimum characters ', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][minimumInputLength]", array(
				'data'      => $field_id,
				'value'     => $minimum_input_length,
				'shortDesc' => __( 'Minimum number of characters required to start a search.', 'buddyforms-members' ),
				'class'     => 'bf_hide_if_not_ajax_ready'
			) );

			break;

	}

	return $form_fields;
}

/*
 * Display the new Form Element in the Frontend Form
 *
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_members_create_frontend_form_element', 1, 2 );
/**
 * @param Form $form
 * @param $form_args
 *
 * @return mixed
 */
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

			$custom_class = ! empty( $customfield['custom_class'] ) ? $customfield['custom_class'] : '';

			$element_attr = array(
				'value'     => is_user_logged_in() ? bp_get_member_type( get_current_user_id() ) : '',
				'class'     => 'settings-input ' . $custom_class,
				'shortDesc' => empty( $customfield['description'] ) ? '' : $customfield['description'],
				'data-form'  => $form_slug
			);
			if ( isset( $customfield['member_type_hidden'] ) ) {
				$form->addElement( new Element_Hidden( $customfield['slug'], $customfield['member_type_default'] ) );
			} else {
				if ( isset( $customfield['member_types'] ) ) {


					$member_types_object = bp_get_member_types(array(),'objects');
					$member_types = $customfield['member_types'];


					$member_types_select = array();
					if( is_array( $member_types_object ) ){
						foreach ( $member_types as $key => $value ){

								$member_types_select[$value] = $member_types_object[$value]->labels['name'];

						}
					}
					$form->addElement( new Element_Select( $customfield['name'], $customfield['slug'], $member_types_select, $element_attr ) );
				}
			}

			break;
		case 'xprofile_group':

			if ( class_exists( 'BP_XProfile_Group' ) ) {

				$groups = BP_XProfile_Group::get( array(
					'fetch_fields' => true
				) );

				if ( ! empty( $groups ) ) : foreach ( $groups as $group ) :
					if ( $group->id == $customfield['xprofile_group'] ) {
						if ( bp_has_profile( 'profile_group_id=' . $group->id ) ) :
							while ( bp_profile_groups() ) : bp_the_profile_group();
								while ( bp_profile_fields() ) : bp_the_profile_field();
									$field = new BP_XProfile_Field( bp_get_the_profile_field_id() );
									$form->addElement( new Element_HTML( '<div class="bf_field_group bf-input">' . buddyforms_members_edit_field_html( $form_slug, $field ) . '</div>' ) );
								endwhile;
							endwhile; endif;
					}
				endforeach;endif;
			}

			break;
		case 'xprofile_field':

			if ( class_exists( 'BP_XProfile_Field' ) ) {
				$field = new BP_XProfile_Field( $customfield['xprofile_field'] );
				$form->addElement( new Element_HTML( '<div class="bf_field_group bf-input">' . buddyforms_members_edit_field_html( $form_slug, $field ) . '</div>' ) );
			}

			break;
		case 'member_taxonomy' :

			if ( ! isset( $customfield['member_taxonomy'] ) ) {
				break;
			}

			$slug = $customfield['slug'];

			if ( isset( $customfield['mapped_xprofile_field'] ) ) {
				$slug = $customfield['mapped_xprofile_field'];
			}

			$taxonomy = isset( $customfield['member_taxonomy'] ) && $customfield['member_taxonomy'] != 'none' ? $customfield['member_taxonomy'] : '';
			$order    = isset( $customfield['taxonobuddyforms_membersorder'] ) ? $customfield['taxonobuddyforms_membersorder'] : 'DESC';
			$exclude  = isset( $customfield['taxonobuddyforms_membersexclude'] ) ? $customfield['taxonobuddyforms_membersexclude'] : '';
			$include  = isset( $customfield['taxonobuddyforms_membersinclude'] ) ? $customfield['taxonobuddyforms_membersinclude'] : '';

			$args = array(
				'hide_empty'    => 0,
				'id'            => $field_id,
				'child_of'      => 0,
				'echo'          => false,
				'selected'      => false,
				'hierarchical'  => 1,
				'name'          => $slug . '[]',
				'class'         => 'postform bf-select2-' . $field_id,
				'depth'         => 0,
				'tab_index'     => 0,
				'hide_if_empty' => false,
				'orderby'       => 'SLUG',
				'taxonomy'      => $taxonomy,
				'order'         => $order,
				'exclude'       => $exclude,
				'include'       => $include,
			);

			$placeholder = isset( $customfield['placeholder'] ) ? $customfield['placeholder'] : 'Select an option';
			if ( ! isset( $customfield['multiple'] ) ) {
				$args = array_merge( $args, Array( 'show_option_none' => $placeholder ) );
			}

			if ( isset( $customfield['multiple'] ) ) {
				$args = array_merge( $args, Array( 'multiple' => $customfield['multiple'] ) );
			}

			$args     = apply_filters( 'buddyforms_wp_dropdown_categories_args', $args, $post_id );
			$dropdown = wp_dropdown_categories( $args );

			if ( isset( $customfield['multiple'] ) && is_array( $customfield['multiple'] ) ) {
				$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );
			}

			if ( isset( $customfield['required'] ) && is_array( $customfield['required'] ) ) {
				$dropdown = str_replace( 'id=', 'required id=', $dropdown );
			}

			$dropdown = str_replace( 'id=', 'data-form="' . $form_slug . '" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'data-placeholder="' . $placeholder . '" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'style="width:100%;" id=', $dropdown );


			// Start getting the value
			$user_terms = xprofile_get_field_data( $slug, bp_loggedin_user_id() );

			if ( isset( $user_terms ) && is_array( $user_terms ) ) {

				foreach ( $user_terms as $key => $user_term ) {
					$term = get_term_by( 'name', $user_term, $customfield['member_taxonomy'] );

					$dropdown = str_replace( ' value="' . $term->term_id . '"', ' value="' . $term->term_id . '" selected="selected"', $dropdown );
				}
			} else {
				if ( isset( $customfield['taxonobuddyforms_membersdefault'] ) ) {
					foreach ( $customfield['taxonobuddyforms_membersdefault'] as $key => $tax ) {
						$dropdown = str_replace( ' value="' . $customfield['taxonobuddyforms_membersdefault'][ $key ] . '"', ' value="' . $tax . '" selected="selected"', $dropdown );
					}
				}
			}

			$required = '';
			if ( isset( $customfield['required'] ) && is_array( $customfield['required'] ) ) {
				$required = '<span class="required">* </span>';
			}

			$tags                   = isset( $customfield['create_new_tax'] ) ? 'tags: true,' : '';
			$maximumSelectionLength = isset( $customfield['maximumSelectionLength'] ) ? 'maximumSelectionLength: ' . $customfield['maximumSelectionLength'] . ',' : '';
			$minimumInputLength = isset( $customfield['minimumInputLength'] ) ? 'minimumInputLength: ' . $customfield['minimumInputLength'] . ',' : '';
			$ajax_options = '';
			$is_ajax      = isset($customfield['ajax']);
			if ($is_ajax) {
				$ajax_options .= $minimumInputLength;
				$ajax_options .= 'ajax:{ ' .
				                 'url: "' . admin_url('admin-ajax.php') . '", ' .
				                 'delay: 250, ' .
				                 'method : "POST", ' .
				                 'data: function (params) { ' .
				                 'var query = { ' .
				                 'search: params.term, ' .
				                 'type: "public", ' .
				                 'action: "bf_load_taxonomy", ' .
				                 'nonce: "' . wp_create_nonce('bf_tax_loading') . '", ' .
				                 'taxonomy: "' . $taxonomy . '", ' .
				                 'order: "' . $order . '", ' .
				                 'exclude: "' . $exclude . '", ' .
				                 'include: "' . $include . '" ' .
				                 '}; ' .
				                 'return query; ' .
				                 ' } ' .
				                 '}, ';
			}

			$name = '';
			if ( isset( $customfield['name'] ) ) {
				$name = stripcslashes( $customfield['name'] );
			}

			$description = '';
			if ( isset( $customfield['description'] ) ) {
				$description = stripcslashes( $customfield['description'] );
			}

			$dropdown = '
						<script>
							jQuery(document).ready(function () {
							    jQuery(".bf-select2-' . $field_id . '").select2({
//							            minimumResultsForSearch: -1,
										' . $maximumSelectionLength . '
										' . $ajax_options . '
										    placeholder: function(){
										        jQuery(this).data("placeholder");
										    },
                                     allowClear: true,
							        ' . $tags . '
							        tokenSeparators: [\',\']
							    });
						    });
						</script>
						<div class="bf_field_group">
	                        <label for="editpost-element-' . $field_id . '">
	                            ' . $required . $name . '
	                        </label>
	                        <div class="bf_inputs bf-input">' . $dropdown . '</div>
		                	<span class="help-inline">' . $description . '</span>
		                </div>';

			if ( isset( $customfield['hidden'] ) ) {
				if ( isset( $customfield['taxonobuddyforms_membersdefault'] ) ) {
					foreach ( $customfield['taxonobuddyforms_membersdefault'] as $key => $tax ) {
						$form->addElement( new Element_Hidden( $slug . '[' . $key . ']', $tax ) );
					}
				}
			} else {
				$form->addElement( new Element_HTML( $dropdown ) );
			}

			break;
	}

	return $form;
}

function buddyforms_everything_in_tags( $string, $tagname ) {
	$pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
	preg_match( $pattern, $string, $matches );

	if ( isset( $matches[1] ) ) {
		return $matches[1];
	}

	return false;
}

function buddyforms_members_edit_field_html( $form_slug, $field ) {
	global $buddyforms;

	$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );

	ob_start();
	if ( isset( $buddyforms[ $form_slug ]['layout']['desc_position'] ) && $buddyforms[ $form_slug ]['layout']['desc_position'] == 'above_field' ) {
		echo '<span class="help-inline">' . bp_get_the_profile_field_description() . '</span>';
		$field_type->edit_field_html();
	} else {
		$field_type->edit_field_html();
		echo '<span class="help-inline">' . bp_get_the_profile_field_description() . '</span>';
	}
	$tmp = ob_get_clean();

	$tmp = str_replace( '<input', '<input class="form-control"', $tmp );

	if ( isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) && $buddyforms[ $form_slug ]['layout']['labels_layout'] == 'inline' ) {

		$label = buddyforms_everything_in_tags( $tmp, 'label' );

		$tmp = str_replace( $label, '', $tmp );

		$label = preg_replace( '/\s+/', '', strip_tags( $label ) );

		if ( strpos( $label, '(required)' ) !== false ) {
			$tmp   = str_replace( '<input', '<input required "', $tmp );
			$label = str_replace( '(required)', '', strip_tags( $label ) );
			$label = '* ' . $label;
		}
		$tmp = str_replace( '<input', '<input placeholder="' . $label . '"', $tmp );
	} else {
		if ( strpos( $tmp, '(required)' ) !== false ) {
			$tmp = str_replace( '<input', '<input required ', $tmp );
			$tmp = str_replace( '(required)', '<span class="required"> *</span>', $tmp );
		}

	}

	if ( $field->type == 'multiselect_custom_taxonomy' || $field->type == 'multiselect' || $field->type == 'select' ) {
		$tmp = str_replace( '<select', '<select class="bf-select2"', $tmp );
	}

	if ( $field->type == 'url' ) {
		$tmp = str_replace( 'type="text"', 'type="url"', $tmp );
	}
	if ( $field->type == 'number' ) {
		$tmp = str_replace( 'type="text"', 'type="number"', $tmp );
	}

	$tmp = str_replace( '<select', '<select data-form="' . $form_slug . '" ', $tmp );
	$tmp = str_replace( '<p class="description"', '<p class="description" style="display:none;"', $tmp );


	return $tmp;
}

add_action( 'buddyforms_process_submission_end', 'buddyforms_members_process_submission_end', 10, 1 );
function buddyforms_members_process_submission_end( $args ) {
	extract( $args );

	if ( ! isset( $user_id ) ) {
		return;
	}

	global $buddyforms;

	if ( isset( $buddyforms[ $form_slug ] ) ) {
		if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {

			foreach ( $buddyforms[ $form_slug ]['form_fields'] as $field_key => $field ) {

				if ( isset( $field['mapped_xprofile_field'] ) && $field['mapped_xprofile_field'] != 'none' ) {
					$xfield = new BP_XProfile_Field( $field['mapped_xprofile_field'] );
					do_action('buddyforms_members_sync_mapped_xprofile_field', $user_id, $field, $xfield);
					switch ( $xfield->type ) {

						case 'datebox':
							$date = isset( $_POST[ $field['slug'] ] ) ? date( 'Y-m-d H:i:s', strtotime( $_POST[ $field['slug'] ] ) ) : '';
							if ( ! empty( $date ) ) {
								$field_data = xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $date );
							}
							break;

						case 'multiselectbox':
							if ( $field['type'] != 'member_taxonomy' ) {
								$options    = isset( $_POST[ $field['slug'] ] ) ? $_POST[ $field['slug'] ] : '';
								$field_data = xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $options );
							}
							break;

						case 'radio':
						case 'checkbox':
						case 'selectbox':
							$options = isset( $_POST[ $field['slug'] ] ) ? $_POST[ $field['slug'] ] : '';

							if ( ! is_array( $options ) ) {
								$options = array( $options );
							}
							$field_data = xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $options );
							break;

						default:
							$text       = isset( $_POST[ $field['slug'] ] ) ? $_POST[ $field['slug'] ] : '';
							$field_data = xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $text );
							break;
					}
				}

				if ( $field['type'] == 'member_taxonomy' ) {
					$options = isset( $_POST[ $field['mapped_xprofile_field'] ] ) ? $_POST[ $field['mapped_xprofile_field'] ] : '';

					$xfield   = new BP_XProfile_Field( $field['mapped_xprofile_field'] );
					$children = $xfield->get_children();

					$the_new_options = array();

					foreach ( (array)$options as $option ) {
						$term = get_term( $option, $field['member_taxonomy'] );
						if(! isset($term->name) ){
							continue;
						}

						$exist = false;
						if ( $children) {
							foreach ( $children as $child ) {
								if ( isset($child->name) && $child->name == $term->name ) {
									$exist = true;
								}
							}
						}

						if ( ! $exist ) {
							xprofile_insert_field( array(
								'field_group_id' => $xfield->group_id,
								'parent_id'      => $field['mapped_xprofile_field'],
								'type'           => $xfield->type,
								'name'           => $term->name,
							) );
						}
						$the_new_options[ $term->term_id ] = $term->name;
					}
					xprofile_set_field_data( $field['mapped_xprofile_field'], $user_id, $the_new_options );
				}

				if ( $field['type'] == 'bp_member_type' ) {
					bp_set_member_type( $user_id, $_POST[ $field['slug'] ] );
				}

				if ( $field['type'] == 'xprofile_group' ) {
					if ( isset( $field['xprofile_group'] ) ) {
						if ( bp_has_profile( 'profile_group_id=' . $field['xprofile_group'] ) ) :
							while ( bp_profile_groups() ) : bp_the_profile_group();
								while ( bp_profile_fields() ) : bp_the_profile_field();
									if ( isset( $_POST[ 'field_' . bp_get_the_profile_field_id() ] ) ) {
										$xprofile_value = $_POST[ 'field_' . bp_get_the_profile_field_id() ];
										xprofile_set_field_data( bp_get_the_profile_field_id(), $user_id, $xprofile_value );
									}
								endwhile;
							endwhile;
						endif;
					}
				}
				if ( $field['type'] == 'xprofile_field' && isset( $field['xprofile_field'] ) ) {
					$field_id = $field['xprofile_field'];
					if ( isset( $_POST[ 'field_' . $field_id ] ) ) {
						$xprofile_value = $_POST[ 'field_' . $field_id ];
						xprofile_set_field_data( $field_id, $user_id, $xprofile_value );
					}
				}
			}

		}
	}

}

add_filter( 'buddyforms_formbuilder_fields_options', 'buddyforms_members_formbuilder_fields_options', 10, 4 );

function buddyforms_members_formbuilder_fields_options( $form_fields, $field_type, $field_id, $form_slug = '' ) {
	global $buddyforms;


	if ( ! isset( $buddyforms[ $form_slug ]['form_type'] ) || $buddyforms[ $form_slug ]['form_type'] != 'registration' ) {
		return $form_fields;
	}

	if ( $field_type == 'xprofile_field' || $field_type == 'xprofile_group' ) {
		return $form_fields;
	}


//	$data_type = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['data_type'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['data_type'] : '';
//	$form_fields['BuddyPress']['data_type'] = new Element_Select( '<b>' . __( 'Store data as', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][data_type]", array('none' => __('Default Form Settings', 'buddyforms-members'), 'user-meta' => __('User Meta Data', 'buddyforms-members' ), 'xprofile' => __('BuddyPress xProfile Data', 'buddyforms-members')), array(
//		'value'    => $data_type,
//		'class'    => 'bf_tax_select',
//		'field_id' => $field_id,
//		'id'       => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//	) );

	$profile_groups = BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
	$bp_xp_fields   = array( 'none' => 'Select an existing field' );
	if ( ! empty( $profile_groups ) ) {
		foreach ( $profile_groups as $profile_group ) {
			if ( ! empty( $profile_group->fields ) ) {
				foreach ( $profile_group->fields as $field ) {
					$bp_xp_fields[ $field->id ] = $profile_group->name . ' - ' . $field->name;
				}
			}
		}
	}
	$mapped_xprofile_field                             = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['mapped_xprofile_field'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['mapped_xprofile_field'] : '';
	$form_fields['BuddyPress']['mapped_xprofile_field'] = new Element_Select( '<b>' . __( 'Map with existing xProfiele Field', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][mapped_xprofile_field]", $bp_xp_fields, array(
		'value'    => $mapped_xprofile_field,
		'class'    => 'bf_tax_select',
		'field_id' => $field_id,
		'id'       => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
	) );


//	$field_types = array();
//	foreach ( bp_xprofile_get_field_types() as $type => $class ) {
//		$field_types[$type] = $type;
//	}
//	$data_type = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['data_type'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['data_type'] : '';
//	$form_fields['BuddyPress']['create_new_xprofile_field'] = new Element_Select( '<b>' . __( 'Create a new xProfile field', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][data_type]",$field_types, array(
//		'value'     => $data_type,
//		'class'     => 'bf_tax_select',
//		'field_id'  => $field_id,
//		'id'        => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//		'shortDesc' => 'Select the xProfile field type with the correct data type for this form element.'
//	) );


//    $mtypes = array();
//	if ( $member_types = bp_get_member_types( array(), 'objects' ) ) {
//
//		foreach ( $member_types as $member_type ){
//			$mtypes[$member_type->name] = $member_type->labels['name'];
//		}
//
//		$form_fields['BuddyPress']['member_types'] = new Element_Checkbox( '<b>' . __( 'Member Types', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][member_types]",$mtypes, array(
//			'value'     => '',
//			'class'     => 'bf_tax_select',
//			'field_id'  => $field_id,
//			'id'        => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//			'shortDesc' => 'Select the Member Types this field should get displayed.'
//		) );
//	}
//
//	$visibility_levels = array();
//	foreach( bp_xprofile_get_visibility_levels() as $level ){
//		$visibility_levels[esc_attr( $level['id'] )] = esc_html( $level['label'] );
//	}
//	$form_fields['BuddyPress']['visibility_levels'] = new Element_Select( '<b>' . __( 'Visibility', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][visibility_levels]",$visibility_levels, array(
//		'value'    => '',
//		'class'    => 'bf_tax_select',
//		'field_id' => $field_id,
//		'id'       => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//	) );
//
//	$form_fields['BuddyPress']['visibility_levels_overwrite'] = new Element_Radio( '<b>' . __( '', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][member_types]",array( 'allowed' => __('Allow members to override', 'buddyforms-members'), 'disabled' => __('Enforce field visibility', 'buddyforms-members')), array(
//		'value'     => '',
//		'class'     => '',
//		'field_id'  => $field_id,
//		'id'        => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//		'shortDesc' => 'Select the Member Types this field should get displayed.'
//	) );
//
//
//	$form_fields['BuddyPress']['autolink'] = new Element_Select( '<b>' . __( 'Autolink', 'buddyforms-members' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][visibility_levels]",array('on' => 'Enabled', 'off' => 'Disabled'), array(
//		'value'    => '',
//		'class'    => 'bf_tax_select',
//		'field_id' => $field_id,
//		'id'       => 'member_taxonobuddyforms_membersfield_id_' . $field_id,
//	) );
	return $form_fields;
}


function buddyforms_memberstemplate_filter_init() {
	if(bp_is_current_action('edit')){

		if ( ! bp_get_member_types() ){
			return;
		}

		add_action( 'bp_template_content', 'buddyforms_membersfilter_template_content' );
		add_filter( 'bp_get_template_part', 'buddyforms_memberstemplate_part_filter', 10, 3 );
	}
}

add_action( 'bp_init', 'buddyforms_memberstemplate_filter_init' );

function buddyforms_memberstemplate_part_filter( $templates, $slug, $name ) {

	if ( 'members/single/profile/edit.php' == $templates[0] ) {

		if ( ! bp_get_member_types() ){
			return;
		}

		$member_type         = bp_get_member_type( get_current_user_id() );
		$buddypress_settings = get_option( 'buddyforms_buddypress_settings' );

		if ( ! $member_type
		     && isset( $buddypress_settings['none'] )
		     && $buddypress_settings['none'] != 'none'
		     || isset( $buddypress_settings[ $member_type ] )
		        && $buddypress_settings[ $member_type ] != 'none' ) {

			$templates = bp_get_template_part( 'members/single/plugins' );

		}
	}

	return $templates;
}

function buddyforms_members_get_form_by_member_type($member_type = 'none'){
	$buddypress_settings = get_option( 'buddyforms_buddypress_settings' );

	$form_slug = false;
	if ( isset( $buddypress_settings[ $member_type ] ) &&  $buddypress_settings[ $member_type ] !== 'none') {
		$form_slug = $buddypress_settings[ $member_type ];
	}

	return $form_slug;
}

function buddyforms_membersfilter_template_content() {
	$member_type         = bp_get_member_type( get_current_user_id() );

	$form_slug = buddyforms_members_get_form_by_member_type($member_type);

	if ( ! $form_slug) {
		$form_slug = buddyforms_members_get_form_by_member_type('none');
	}

	if ( $form_slug ) {
		echo do_shortcode( '[bf form_slug="' . $form_slug . '"]' );
	}
}
