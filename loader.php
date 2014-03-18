<?php
/*
 Plugin Name: BuddyForms Members
 Plugin URI: http://buddyforms.com
 Description: The BuddyForms Members Component. Let your members write right out of their profiles.    
 Version: 0.9.3
 Author: Sven Lehnert
 Author URI: http://themekraft.com/members/svenl77/
 Licence: GPLv3
 Network: false
 */

define('buddyforms_members', '0.9.3');

/**
 * Loads BuddyForms files only if BuddyPress is present
 *
 * @package BuddyForms
 * @since 0.1 beta
 */



function buddyforms_members_init() {
	global $wpdb, $buddyforms_members;

	if (is_multisite() && BP_ROOT_BLOG != $wpdb->blogid)
		return;

	require (dirname(__FILE__) . '/buddyforms-members.php');
	$buddyforms_members = new BuddyForms_Members();
}

add_action('bp_loaded', 'buddyforms_members_init');
