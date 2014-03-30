<?php

class BuddyForms_Members_Extention extends BP_Component{

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
			__( 'BuddyForms', 'buddyforms' ),
			BUDDYFORMS_MEMBERS_INSTALL_PATH
		);

		$bp->active_components[$this->id] = '1';
		
		$this->setup_hooks();
	
	}
	
	function setup_hooks() {

		add_action('bp_located_template',	array($this, 'buddyforms_load_template_filter'), 10, 2);
		add_action('wp_enqueue_scripts',	array($this, 'wp_enqueue_style'), 10, 2);
	
	}
	
	/**
     * Setup globals
     *
     * @since     Marketplace 0.9
     * @global    object $bp The one true BuddyPress instance
     */
    public function setup_globals($args = Array()) {
        global $buddyforms_members;

        $globals = array(
            'path'          => BUDDYFORMS_MEMBERS_INSTALL_PATH,
            'slug'          => 'buddyforms',
            'has_directory' => false
        );

        parent::setup_globals( $globals );
    }
	
	/**
	 * Get the user posts count
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	function get_user_posts_count($user_id, $post_type) {
		
		$args['author'] = $user_id;
		$args['post_type'] = $post_type;
		$args['fields'] = 'ids';
		$ps = get_posts($args);
		return count($ps);
		
	}

	/**
	 * Setup profile navigation
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	public function setup_nav( $main_nav = Array(), $sub_nav = Array() ) {
		global $buddyforms, $bp, $wp_admin_bar;

		if(!bp_is_user())
			return;

		get_currentuserinfo();

		$position = 20;

		if (empty($buddyforms['buddypress']))
			return;

		foreach ($buddyforms['buddypress'] as $key => $member_form) {
			$position++;
			
			if(isset($member_form['selected'])) :
				
				if(isset($buddyforms['buddyforms'][$key]['slug']))
					$slug = $buddyforms['buddyforms'][$key]['slug'];
				
				$post_type_object = get_post_type_object( $buddyforms['buddyforms'][$key]['post_type'] );
				
				if(isset($post_type_object->labels->name))
					$name = $post_type_object->labels->name;
				
				if(isset($buddyforms['buddyforms'][$key]['name']))
					$name = $buddyforms['buddyforms'][$key]['name'];
				
				$count = $this->get_user_posts_count($bp->displayed_user->id, $buddyforms['buddyforms'][$key]['post_type']);


				$main_nav = array(
					'name' => sprintf('%s <span>%d</span>',$name , $count),
					'slug' => $key,
					'position' => $position,
					'screen_function' => array($this, 'buddyforms_screen_settings'),
					'default_subnav_slug' => 'my-posts'
				);
				
				$sub_nav[] = array(
					'name'				=> sprintf(__(' Add %s', 'buddyforms'), $buddyforms['buddyforms'][$key]['singular_name']),
					'slug'				=> 'create',
					'parent_slug'		=> $slug,
					'parent_url'		=> trailingslashit(bp_loggedin_user_domain() . $slug),
					'item_css_id'		=> 'apps_sub_nav',
					'screen_function'	=> array($this,'load_members_post_create'),
					'user_has_access'	=> bp_is_my_profile()
				);
				$sub_nav[] = array(
					'name'				=> sprintf(__(' Edit %s', 'buddyforms'), $buddyforms['buddyforms'][$key]['singular_name']),
					'slug'				=> 'edit',
					'parent_slug'		=> $slug,
					'parent_url'		=> trailingslashit(bp_loggedin_user_domain() . $slug),
					'item_css_id'		=> 'sub_nav_edit',
					'screen_function'	=> array($this,'buddyforms_screen_settings'),
					'user_has_access'	=> bp_is_my_profile()
				);
				$sub_nav[] = array(
					'name'				=> sprintf(__(' Delete %s', 'buddyforms'), $buddyforms['buddyforms'][$key]['singular_name']),
					'slug'				=> 'delete',
					'parent_slug'		=> $slug,
					'parent_url'		=> trailingslashit(bp_loggedin_user_domain() . $slug),
					'item_css_id'		=> 'sub_nav_delete',
					'screen_function'	=> array($this,'buddyforms_screen_settings'),
					'user_has_access'	=> bp_is_my_profile(),
				);
				$sub_nav[] = array(
					'name'				=> sprintf(__(' Revison %s', 'buddyforms'), $buddyforms['buddyforms'][$key]['singular_name']),
					'slug'				=> 'revison',
					'parent_slug'		=> $slug,
					'parent_url'		=> trailingslashit(bp_loggedin_user_domain() . $slug),
					'item_css_id'		=> 'sub_nav_revison',
					'screen_function'	=> array($this,'buddyforms_screen_settings'),
					'user_has_access'	=> bp_is_my_profile(),
				);

			parent::setup_nav( $main_nav, $sub_nav );
		endif;
		}

	}

	/**
	 * Display the posts or the edit screen
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	*/
	public function buddyforms_screen_settings() {
		global $current_user, $bp;


		if($bp->current_action == 'my-posts')
			bp_core_load_template('buddyforms/members/members-post-display');	
			
		if($bp->current_action == 'edit')
			bp_core_load_template('buddyforms/members/members-post-create');
	
		if($bp->current_action == 'revison')
			bp_core_load_template('buddyforms/members/members-post-create');

		if($bp->current_action == 'delete'){

			get_currentuserinfo();
			$the_post = get_post($bp->action_variables[1]);
	
			if ($the_post->post_author != $current_user->ID) {
				echo '<div class="error alert">You are not allowed to delete this entry! What are you doing here?</div>';
				return;
			}
			
			do_action('buddyforms_delete_post',$bp->action_variables[1]);
			
			wp_delete_post($bp->action_variables[1]);
			bp_core_load_template('buddyforms/members/members-post-display');	

		}
			

	}

	/**
	 * Show the post create form
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	*/
	public function load_members_post_create() {
		bp_core_load_template('buddyforms/members/members-post-create');
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
	function buddyforms_load_template_filter($found_template, $templates) {
	global $bp, $wp_query;

	
			if (empty($found_template)) {
				// register our theme compat directory
				//
				// this tells BP to look for templates in our plugin directory last
				// when the template isn't found in the parent / child theme
				bp_register_template_stack('buddyforms_members_get_template_directory', 14);
	
				// locate_template() will attempt to find the plugins.php template in the
				// child and parent theme and return the located template when found
				//
				// plugins.php is the preferred template to use, since all we'd need to do is
				// inject our content into BP
				//
				// note: this is only really relevant for bp-default themes as theme compat
				// will kick in on its own when this template isn't found
				$found_template = locate_template('members/single/plugins.php', false, false);
	
				// add our hook to inject content into BP
				if ($bp->current_action == 'my-posts') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				"));
				} elseif ($bp->current_action == 'create') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				} elseif ($bp->current_action == 'edit') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				} elseif ($bp->current_action == 'revison') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-create' );
				"));
				} elseif ($bp->current_action == 'delete') {
					add_action('bp_template_content', create_function('', "
					bp_get_template_part( 'buddyforms/members/members-post-display' );
				"));
				} 
			}

	
		return apply_filters('buddyforms_members_load_template_filter', $found_template);
	}

	function wp_enqueue_style(){
    	wp_enqueue_style('member-profile-css', plugins_url('css/member-profile.css', __FILE__));
	}

}
?>