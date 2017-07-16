<?php

class BuddyForms_Members_Extention extends BP_Component {

	public $id = 'buddyforms';

	/**
	 * Initiate the class
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	public function __construct() {
		global $bp;

		parent::start(
			$this->id,
			'BuddyForms',
			BUDDYFORMS_MEMBERS_INSTALL_PATH
		);

		$bp->active_components[ $this->id ] = '1';
		$this->setup_hooks();
	}

	function setup_hooks() {
		add_action( 'bp_located_template', array( $this, 'buddyforms_load_template_filter' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'buddyforms_members_enqueue_scripts' ), 10, 2 );
	}

	/**
	 * Setup globals
	 *
	 * @since     Marketplace 0.9
	 * @global    object $bp The one true BuddyPress instance
	 */
	public function setup_globals( $args = Array() ) {
		$globals = array(
			'path'          => BUDDYFORMS_MEMBERS_INSTALL_PATH,
			'slug'          => 'buddyforms',
			'has_directory' => false
		);
		parent::setup_globals( $globals );
	}

	/**
	 * Setup profile navigation
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	public function setup_nav( $main_nav = Array(), $sub_nav = Array() ) {
		global $buddyforms, $buddyforms_member_tabs, $bp;

		if ( ! bp_is_user() ) {
			return;
		}

		wp_get_current_user();

		$position = 20;

		if ( empty( $buddyforms ) ) {
			return;
		}

		foreach ( $buddyforms as $key => $member_form ) {
			$position ++;

			if ( isset( $member_form['profiles_integration'] ) ) {

				if ( $member_form['post_type'] == 'bf_submissions' && ! bp_is_my_profile() ) {
					continue;
				}

				if ( current_user_can( 'buddyforms_' . $key . '_create' ) || user_can( bp_displayed_user_id(), 'buddyforms_' . $key . '_create' ) ) {


					$post_type_object = get_post_type_object( $member_form['post_type'] );
					$count            = $this->get_user_posts_count( $bp->displayed_user->id, $member_form['post_type'], $key );

					if ( isset( $post_type_object->labels->name ) ) {
						$name = $post_type_object->labels->name;
					}

					if ( isset( $member_form['name'] ) ) {
						$name = $member_form['name'];
					}

					$parent_tab = buddyforms_members_parent_tab( $member_form );

					if ( $parent_tab ) {

						$buddyforms_member_tabs[ $parent_tab ][ $member_form['slug'] ] = $key;
						$parent_tab_name                                               = $name;

						$attached_page = false;
						if ( isset( $member_form['profiles_parent_tab'] )
						     && isset( $member_form['attached_page'] )
						     && isset( $parent_tab )
						) {
							$attached_page   = $member_form['attached_page'];
							$parent_tab_page = get_post( $attached_page, 'OBJECT' );
							$parent_tab_name = $parent_tab_page->post_title;
						}

						// Check the profile visibility
						$profile_visibility = bp_is_my_profile();
						if ( isset( $member_form['profile_visibility'] ) ) {
							if ( $member_form['profile_visibility'] == 'private' && ! bp_is_my_profile() ) {
								return;
							}
							if ( $member_form['profile_visibility'] == 'any' ) {
								$profile_visibility = true;
							}
							if ( $member_form['profile_visibility'] == 'logged_in_user' ) {
								if ( is_user_logged_in() ) {
									$profile_visibility = true;
								} else {
									return;
								}
							}
						}

						if ( ! array_key_exists( $parent_tab, (array) $bp->bp_nav ) && ! isset( $done )
						     || ! array_key_exists( $parent_tab, (array) $bp->bp_nav ) && isset( $done ) && $done != $attached_page
						     || ! array_key_exists( $parent_tab, (array) $bp->bp_nav ) && ! isset( $member_form['profiles_parent_tab'] )
						) {
							$done                                                     = $attached_page;
							$main_nav                                                 = array(
								'name'                => $parent_tab_name,
								'slug'                => $parent_tab,
								'position'            => $position,
								'default_subnav_slug' => $key . '-posts',
								'user_has_access'     => $profile_visibility,
								'position' => $position,
							);
							$buddyforms_member_tabs[ $parent_tab ][ $key . '-posts' ] = $key;
							$position ++;
						}

						$sub_nav[]                                                = array(
							'name'            => sprintf( '%s <span>%d</span>', $name, $count ),
							'slug'            => $key . '-posts',
							'parent_slug'     => $parent_tab,
							'parent_url'      => trailingslashit( bp_displayed_user_domain() . $parent_tab ),
							'item_css_id'     => 'sub_nav_home',
							'screen_function' => array( $this, 'buddyforms_screen_settings' ),
							'user_has_access' => $profile_visibility,
							'position' => $position,
						);
						$buddyforms_member_tabs[ $parent_tab ][ $key . '-posts' ] = $key;
						$position ++;

						$sub_nav[]                                                 = array(
							'name'            => sprintf( __( ' Add %s', 'buddyforms' ), $member_form['singular_name'] ),
							'slug'            => $key . '-create',
							'parent_slug'     => $parent_tab,
							'parent_url'      => trailingslashit( bp_displayed_user_domain() . $parent_tab ),
							'item_css_id'     => 'add_sub_nav_' . $key,
							'screen_function' => array( $this, 'load_members_post_create' ),
							'user_has_access' => bp_is_my_profile(),
							'position' => $position,
						);
						$buddyforms_member_tabs[ $parent_tab ][ $key . '-create' ] = $key;
						$position ++;

						$sub_nav[]                                               = array(
							'name'            => sprintf( __( ' Edit %s', 'buddyforms' ), $member_form['singular_name'] ),
							'slug'            => $key . '-edit',
							'parent_slug'     => $parent_tab,
							'parent_url'      => trailingslashit( bp_displayed_user_domain() . $parent_tab ),
							'item_css_id'     => 'sub_nav_edit',
							'screen_function' => array( $this, 'buddyforms_screen_settings' ),
							'user_has_access' => bp_is_my_profile(),
							'position' => $position,
						);
						$buddyforms_member_tabs[ $parent_tab ][ $key . '-edit' ] = $key;
						$position ++;

						$sub_nav[]                                                   = array(
							'name'            => sprintf( __( ' Revision %s', 'buddyforms' ), $member_form['singular_name'] ),
							'slug'            => $key . '-revision',
							'parent_slug'     => $parent_tab,
							'parent_url'      => trailingslashit( bp_loggedin_user_domain() . $parent_tab ),
							'item_css_id'     => 'sub_nav_revison',
							'screen_function' => array( $this, 'buddyforms_screen_settings' ),
							'user_has_access' => bp_is_my_profile(),
							'position' => $position,
						);
						$buddyforms_member_tabs[ $parent_tab ][ $key . '-revision' ] = $key;
						$position ++;

						$sub_nav[]                                               = array(
							'name'            => sprintf( __( ' Page %s', 'buddyforms' ), $member_form['singular_name'] ),
							'slug'            => $key . '-page',
							'parent_slug'     => $parent_tab,
							'parent_url'      => trailingslashit( bp_loggedin_user_domain() . $parent_tab ),
							'item_css_id'     => 'sub_nav_page',
							'screen_function' => array( $this, 'buddyforms_screen_settings' ),
							'position' => $position,
						);
						$buddyforms_member_tabs[ $parent_tab ][ $key . '-page' ] = $key;
						$position ++;
					}



					$buddyforms_members_parent_setup_nav  = apply_filters( 'buddyforms_members_parent_setup_nav', true, $key  );

					if($buddyforms_members_parent_setup_nav){
						parent::setup_nav( $main_nav, $sub_nav );
					} else {
						foreach ( $sub_nav as $nav ){
							bp_core_new_subnav_item($nav);
						}
					}

				}

			}
		}
	}

	/**
	 * Get the user posts count
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	function get_user_posts_count( $user_id, $post_type, $form_slug ) {
		global $buddyforms;

		$args['author']         = $user_id;
		$args['post_type']      = $post_type;
		$args['fields']         = 'ids';
		$args['posts_per_page'] = - 1;

		if ( isset( $buddyforms[ $form_slug ]['list_posts_option'] ) && $buddyforms[ $form_slug ]['list_posts_option'] == 'list_all_form' ) {
			$args['meta_key']   = '_bf_form_slug';
			$args['meta_value'] = $form_slug;
		}

		$post_status_array   = buddyforms_get_post_status_array();

		unset( $post_status_array['trash'] );

		$args['post_status'] = array_keys( $post_status_array );

		return count( get_posts( $args ) );

	}

	/**
	 * Display the posts or the edit screen
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	 */
	public function buddyforms_screen_settings() {
		global $bp, $buddyforms_member_tabs;

		$form_slug = $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ];

		if ( $bp->current_action == $form_slug . '-posts' ) {
			bp_core_load_template( 'buddyforms/members/members-post-display' );
		}

		if ( $bp->current_action == $form_slug . '-posts-all' ) {
			bp_core_load_template( 'buddyforms/members/members-post-display' );
		}

		if ( $bp->current_action == $form_slug . '-page' ) {
			bp_core_load_template( 'buddyforms/members/members-post-display' );
		}

		if ( $bp->current_action == $form_slug . '-create' ) {
			bp_core_load_template( 'buddyforms/members/members-post-create' );
		}

		if ( $bp->current_action == $form_slug . '-edit' ) {
			bp_core_load_template( 'buddyforms/members/members-post-create' );
		}

		if ( $bp->current_action == $form_slug . '-revision' ) {
			bp_core_load_template( 'buddyforms/members/members-post-create' );
		}

	}

	/**
	 * Show the post create form
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	 */
	public function load_members_post_create() {
		bp_core_load_template( 'buddyforms/members/members-post-create' );
	}

	/**
	 * BuddyForms template loader.
	 *
	 * I copied this function from the buddypress.org website and modified it for my needs.
	 *
	 * This function sets up BuddyForms to use custom templates.
	 *
	 * If a template does not exist in the current theme, we will use our own
	 * bundled templates.
	 *
	 * We're doing two things here:
	 *  1) Support the older template format for themes that are using them
	 *     for backwards-compatibility (the template passed in
	 *     {@link bp_core_load_template()}).
	 *  2) Route older template names to use our new template locations and
	 *     format.
	 *
	 * View the inline doc for more details.
	 *
	 * @since 1.0
	 */
	function buddyforms_load_template_filter( $found_template, $templates ) {
		global $bp, $buddyforms, $buddyforms_member_tabs;

		$form_slug = isset( $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ] ) ? $buddyforms_member_tabs[ $bp->current_component ][ $bp->current_action ] : '';

		if ( ! bp_current_component() ) {
			return apply_filters( 'buddyforms_members_load_template_filter', $found_template );
		}

		if ( is_array( $templates ) && is_array( $buddyforms ) && empty( $found_template ) && isset( $buddyforms ) && array_key_exists( $form_slug, $buddyforms ) ) {

			// register our theme compat directory
			//
			// this tells BP to look for templates in our plugin directory last
			// when the template isn't found in the parent / child theme
			bp_register_template_stack( 'buddyforms_members_get_template_directory', 14 );

			// locate_template() will attempt to find the plugins.php template in the
			// child and parent theme and return the located template when found
			//
			// plugins.php is the preferred template to use, since all we'd need to do is
			// inject our content into BP
			//
			// note: this is only really relevant for bp-default themes as theme compat
			// will kick in on its own when this template isn't found
			$found_template = locate_template( 'members/single/plugins.php', false, false );

			// add our hook to inject content into BP
			if ( $bp->current_action == $form_slug . '-posts' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				" ) );
			} elseif ( $bp->current_action == $form_slug . '-create' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				" ) );
			} elseif ( $bp->current_action == $form_slug . '-edit' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				" ) );
			} elseif ( $bp->current_action == $form_slug . '-revision' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				" ) );
			} elseif ( $bp->current_action == $form_slug . '-page' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				" ) );
			} elseif ( $bp->current_action == $form_slug . '-posts-all' ) {
				add_action( 'bp_template_content', create_function( '', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				" ) );
			}
		}


		return apply_filters( 'buddyforms_members_load_template_filter', $found_template );
	}

	function buddyforms_members_enqueue_scripts() {
		wp_enqueue_script( 'member-profile-js', plugins_url( 'js/member-profile.js', __FILE__ ) );
		wp_enqueue_style( 'member-profile-css', plugins_url( 'css/member-profile.css', __FILE__ ) );
	}

}

function buddyforms_members_parent_tab( $member_form ) {

	$parent_tab_name = $member_form['slug'];

	if ( isset( $member_form['profiles_parent_tab'] ) ) {
		$parent_tab = $member_form['profiles_parent_tab'];
	}

	if ( isset( $member_form['attached_page'] ) && isset( $parent_tab ) ) {
		$attached_page   = $member_form['attached_page'];
		$parent_tab_page = get_post( $attached_page, 'OBJECT' );
		$parent_tab_name = $parent_tab_page->post_name;
	}

	return apply_filters('buddyforms_members_parent_tab', $parent_tab_name, $member_form['slug']);

}

function buddyforms_members_activity_stream_support() {
	global $buddyforms;

	// Check if the Activity component is active before using it.
	if ( ! bp_is_active( 'activity' ) ) {
		return;
	}

	if( isset($buddyforms) && is_array($buddyforms) ){
		foreach ( $buddyforms as $form_slug => $buddyform ){
			if( isset( $buddyform['bp_activity_stream'] )  && isset( $buddyform['post_type'] ) ){

				$name          = isset( $buddyform['name'] ) && ! empty( $buddyform['name'] ) ? $buddyform['name'] : $buddyform['post_type'];
				$name_singular = isset( $buddyform['singular_name'] ) && ! empty( $buddyform['singular_name'] ) ? $buddyform['singular_name'] : $name;

				// Set the activity tracking args
				bp_activity_set_post_type_tracking_args( $buddyform['post_type'] , array(
					'component_id'             => 'activity',
					'action_id'                => 'new_post_' . $buddyform['post_type'] ,
					'bp_activity_admin_filter' => __( 'Published a new ' . $name_singular , 'buddyforms' ),
					'bp_activity_front_filter' => __( $name_singular , 'buddyforms' ),
					'contexts'                 => array( 'activity', 'member' ),
					'activity_comment'         => true,
					'bp_activity_new_post'     => __( '%1$s posted a new <a href="%2$s">' . $name_singular . '</a>', 'buddyforms' ),
					'bp_activity_new_post_ms'  => __( '%1$s posted a new <a href="%2$s">' . $name_singular . '</a>, on the site %3$s', 'buddyforms' ),
					'position'                 => 100,
				) );

				// Don't forget to add the 'buddypress-activity' support to the filter select box!
				add_post_type_support( $buddyform['post_type'], 'buddypress-activity' );

			}
		}
	}

}
add_action( 'init', 'buddyforms_members_activity_stream_support', 999 );