<?php

/**
 * Plugin Name: BuddyForms Members
 * Plugin URI: http://buddyforms.com/downloads/buddyforms-members/
 * Description: The BuddyForms Members Component. Let your members write right out of their profiles.
 * Version: 1.4.2
 * Author: ThemeKraft
 * Author URI: https://themekraft.com/buddyforms/
 * License: GPLv2 or later
 * Network: false
 * Text Domain: buddyforms
 *
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

//
// Check the plugin dependencies
//
add_action( 'init', function () {

	// Only Check for requirements in the admin
	if ( ! is_admin() ) {
		return;
	}

	// Require TGM
	require( dirname( __FILE__ ) . '/includes/resources/tgm/class-tgm-plugin-activation.php' );

	// Hook required plugins function to the tgmpa_register action
	add_action( 'tgmpa_register', function () {

		// Create the required plugins array
		$plugins['buddypress'] = array(
			'name'     => 'BuddyPress',
			'slug'     => 'buddypress',
			'required' => true,
		);

		if ( ! defined( 'BUDDYFORMS_PRO_VERSION' ) ) {
			$plugins['buddyforms'] = array(
				'name'     => 'BuddyForms',
				'slug'     => 'buddyforms',
				'required' => true,
			);
		}

		$config = array(
			'id'           => 'tgmpa',
			// Unique ID for hashing notices for multiple instances of TGMPA.
			'parent_slug'  => 'plugins.php',
			// Parent menu slug.
			'capability'   => 'manage_options',
			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,
			// Show admin notices or not.
			'dismissable'  => false,
			// If false, a user cannot dismiss the nag message.
			'is_automatic' => true,
			// Automatically activate plugins after installation or not.
		);

		// Call the tgmpa function to register the required plugins
		tgmpa( $plugins, $config );

	} );
}, 1, 1 );

// BuddyForms Members init
add_action( 'bp_loaded', 'buddyforms_members_init' );
function buddyforms_members_init() {
	global $wpdb, $buddyforms_members;

	if ( is_multisite() && BP_ROOT_BLOG != $wpdb->blogid ) {
		return;
	}

	require( dirname( __FILE__ ) . '/buddyforms-members.php' );
	$buddyforms_members = new BuddyForms_Members();

}

// Create a helper function for easy SDK access.
function buddyforms_members_fs() {
	global $buddyforms_members_fs;

	if ( ! isset( $buddyforms_members_fs ) ) {
		// Include Freemius SDK.
		if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php' ) ) {
			// Try to load SDK from parent plugin folder.
			require_once dirname( dirname( __FILE__ ) ) . '/buddyforms/includes/resources/freemius/start.php';
		} else if ( file_exists( dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php' ) ) {
			// Try to load SDK from premium parent plugin folder.
			require_once dirname( dirname( __FILE__ ) ) . '/buddyforms-premium/includes/resources/freemius/start.php';
		}

		$buddyforms_members_fs = fs_dynamic_init( array(
			'id'                  => '408',
			'slug'                => 'buddyforms-members',
			'type'                => 'plugin',
			'public_key'          => 'pk_0dc82cbd48e6935bba8e2ff431777',
			'is_premium'          => true,
			'has_paid_plans'      => true,
			'trial'               => array(
				'days'               => 7,
				'is_require_payment' => false,
			),
			'parent'              => array(
				'id'         => '391',
				'slug'       => 'buddyforms',
				'public_key' => 'pk_dea3d8c1c831caf06cfea10c7114c',
				'name'       => 'BuddyForms',
			),
			'menu'                => array(
				'first-path'     => 'plugins.php',
				'support'        => false,
			)
		) );
	}

	return $buddyforms_members_fs;
}

function buddyforms_members_fs_is_parent_active_and_loaded() {
	// Check if the parent's init SDK method exists.
	return function_exists( 'buddyforms_core_fs' );
}

function buddyforms_members_fs_is_parent_active() {
	$active_plugins_basenames = get_option( 'active_plugins' );

	foreach ( $active_plugins_basenames as $plugin_basename ) {
		if ( 0 === strpos( $plugin_basename, 'buddyforms/' ) ||
		     0 === strpos( $plugin_basename, 'buddyforms-premium/' )
		) {
			return true;
		}
	}

	return false;
}

function buddyforms_members_fs_init() {
	if ( buddyforms_members_fs_is_parent_active_and_loaded() ) {
		// Init Freemius.
		buddyforms_members_fs();

		// Parent is active, add your init code here.
	} else {
		// Parent is inactive, add your error handling here.
	}
}

if ( buddyforms_members_fs_is_parent_active_and_loaded() ) {
	// If parent already included, init add-on.
	buddyforms_members_fs_init();
} else if ( buddyforms_members_fs_is_parent_active() ) {
	// Init add-on only after the parent is loaded.
	add_action( 'buddyforms_core_fs_loaded', 'buddyforms_members_fs_init' );
} else {
	// Even though the parent is not activated, execute add-on for activation / uninstall hooks.
	buddyforms_members_fs_init();
}


// register the location of the plugin templates
function buddyforms_members_register_template_location() {
	return dirname( __FILE__ ) . '/includes/templates/';
}

// replace member-header.php with the template overload from the plugin
function buddyforms_members_maybe_replace_template( $templates, $slug, $name ) {
	global $post;

	$buddyforms_registration_page = get_option( 'buddyforms_registration_page' );
	$buddyforms_registration_form = get_option( 'buddyforms_registration_form' );

	if ( $post->ID != $buddyforms_registration_page && $buddyforms_registration_form != 'none' ) {
		if ( in_array( 'registration/register.php', $templates ) || in_array( 'members/register.php', $templates ) || in_array( 'register.php', $templates ) ) {
			return array( 'buddyforms/registration-form.php' );
		}
	}

	return $templates;

}

function buddyforms_members_start() {

	if ( function_exists( 'bp_register_template_stack' ) ) {
		bp_register_template_stack( 'buddyforms_members_register_template_location' );
	}

	// if viewing a member page, overload the template
	if ( bp_is_register_page() ) {
		add_filter( 'bp_get_template_part', 'buddyforms_members_maybe_replace_template', 10, 3 );
	}

}

add_action( 'bp_init', 'buddyforms_members_start' );

