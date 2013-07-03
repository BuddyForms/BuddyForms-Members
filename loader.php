<?php
/*
 Plugin Name: BuddyForms Members
 Plugin URI: http://buddyforms.com
 Description: The BuddyForms Members Component. Let your members write right out of their profiles.    
 Version: 1.0 beta 3
 Author: Sven Lehnert
 Author URI: http://themekraft.com/members/svenl77/
 Licence: GPLv3
 Network: true
 */

define('buddyforms_members', '1.0 beta 3');

/**
 * Loads BuddyForms files only if BuddyPress is present
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
function buddyforms_members_init() {
	global $wpdb;

	if (is_multisite() && BP_ROOT_BLOG != $wpdb->blogid)
		return;

	require (dirname(__FILE__) . '/buddyforms_members.php');
	$buddyforms_members = new BuddyForms_Members();
}

add_action('bp_loaded', 'buddyforms_members_init', 0);
