<?php
/*
 Plugin Name: BuddyForms Members
 Plugin URI: http://buddyforms.com
 Description: The BuddyForms Members Component. Let your members write right out of their profiles.    
 Version: 1.0
 Author: Sven Lehnert
 Author URI: http://themekraft.com/members/svenl77/
 License: GPLv2 or later
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

define('buddyforms_members', '1.0');

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