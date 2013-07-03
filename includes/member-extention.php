<?php
class BuddyForms_Members_Extention {


 	public static function init() {
        $class = __CLASS__;
        new $class;
    }
	/**
	 * Initiate the class
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	*/
	public function __construct() {
		add_action('bp_setup_nav', array($this, 'profile_setup_nav'), 20, 1);
		add_action('bp_located_template', array($this, 'buddyforms_load_template_filter'), 10, 2);
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
	public function profile_setup_nav() {
		global $buddyforms, $bp, $wp_admin_bar;

		if(!bp_is_user())
			return;
		// echo '<pre>';
		// print_r($buddyforms);
		// echo '</pre>';
		get_currentuserinfo();
		
		if(session_id()) {
		  session_start('buddyforms');
		}
		$position = 20;

		if (empty($buddyforms['selected_post_types']))
			return;

		foreach ($buddyforms['selected_post_types'] as $key => $selected_post_type) {
			$position++;
			
			if(isset($selected_post_type['selected'])) :
				
				if(isset($selected_post_type['form'])){
					$form = $selected_post_type['form'];
				}
				$slug = $key;
				if(isset($form) && isset($buddyforms['buddyforms'][$form]['slug']))
					$slug = $buddyforms['buddyforms'][$form]['slug'];
				
				$post_type_object = get_post_type_object( $key );
				$name = $post_type_object->labels->name;
				
				if(isset($form) && isset($buddyforms['buddyforms'][$form]['name']))
					$name = $buddyforms['buddyforms'][$form]['name'];
				
				$count = $this->get_user_posts_count($bp->displayed_user->id, $key);

				bp_core_new_nav_item( array(
					'name' => sprintf('%s <span>%d</span>',$name , $count),
					'slug' => $slug,
					'position' => $position,
					'screen_function' => array($this, 'buddyforms_screen_settings')
				));
	
				if(isset($form) && $form != 'no-form') {
					bp_core_new_subnav_item( array(
						'name'				=> sprintf(__(' Add %s', 'buddyforms'), $buddyforms['buddyforms'][$form]['singular_name']),
						'slug'				=> 'create',
						'parent_slug'		=> $slug,
						'parent_url'		=> trailingslashit(bp_loggedin_user_domain() . $slug),
						'item_css_id'		=> 'apps_sub_nav',
						'screen_function'	=> array($this,'load_members_post_create'),
						'user_has_access'	=> bp_is_my_profile()
					));
				}
				
			endif;
		}

		// bp_core_remove_nav_item( 'groups' ); // @todo here needs to come one global option to turn groups nav on off
	}

	/**
	 * Display the posts or the edit screen
	 *
	 * @package BuddyForms
	 * @since 0.2 beta
	*/
	public function buddyforms_screen_settings() {
		global $current_user, $bp;
			
		if (isset($_GET['post_id'])) {
			$bp->current_action = 'create';
			bp_core_load_template('buddyforms/members/members-post-create');
			return;
		}
		if (isset($_GET['delete'])) {
			$bp->current_action = 'create';
			get_currentuserinfo();
			$the_post = get_post($_GET['delete']);

			if ($the_post->post_author != $current_user->ID) {
				echo '<div class="error alert">You are not allowed to delete this entry! What are you doing here?</div>';
				return;
			}
			
			do_action('buddyforms_delete_post',$_GET['delete']);
			
			wp_delete_post($_GET['delete']);

		}
		wp_enqueue_style('member-profile-css', plugins_url('css/member-profile.css', __FILE__));
		$bp->current_action = 'my-posts';
		bp_core_load_template('buddyforms/members/members-post-display');

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
	
	// echo '<pre>';
		// print_r($wp_query);
		// echo '</pre>';
	if ($bp->current_action == 'create' || $bp->current_action == 'my-posts') {
	
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
				}
			}
		}
	
		return apply_filters('buddyforms_members_load_template_filter', $found_template);
	}
}
add_action( 'buddyforms_init', array( 'BuddyForms_Members_Extention', 'init' ));

?>