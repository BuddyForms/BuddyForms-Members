<?php
/**
 * Run on a disposable site with BuddyPress (xProfile active), BuddyForms and BuddyForms Members active:
 * wp eval-file tests/security/xprofile-field-selector.php
 * Calls the real AJAX handler, each case in its own process as a given user, and checks
 * whether the JSON reply carries xProfile field data. Refs TKT-114.
 */
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

if ( ! empty( $args ) ) {
	$case = json_decode( base64_decode( $args[0] ), true );
	wp_set_current_user( $case['user'] );
	if ( ! defined( 'DOING_AJAX' ) ) {
		define( 'DOING_AJAX', true );
	}
	add_filter( 'wp_die_ajax_handler', function () {
		return function () {
			exit( 0 );
		};
	} );
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_SERVER['REQUEST_URI']    = '/wp-admin/admin-ajax.php';
	$_POST                     = wp_slash( $case['post'] );
	if ( ! empty( $case['nonce'] ) ) {
		$_POST['nonce'] = wp_create_nonce( $case['nonce'] );
	}
	$_REQUEST = $_POST;
	buddyforms_update_xprofile_field_selector();
	return;
}

if ( ! function_exists( 'buddyforms_update_xprofile_field_selector' ) || ! function_exists( 'xprofile_insert_field_group' ) ) {
	WP_CLI::error( 'BuddyForms Members and the BuddyPress xProfile component must be active.' );
}

$users  = array();
$passed = $failed = 0;
$marker = 'bfxp' . wp_rand( 100000, 999999 );
$group  = 0;

try {
	foreach ( array( 'editor' => 'editor', 'subscriber' => 'subscriber' ) as $who => $role ) {
		$id = wp_insert_user( array( 'user_login' => $marker . '-' . $who, 'user_email' => $who . '-' . $marker . '@example.com', 'user_pass' => wp_generate_password(), 'role' => $role ) );
		if ( is_wp_error( $id ) ) {
			throw new Exception( 'Unable to create user fixture' );
		}
		$users[ $who ] = $id;
	}

	$group = xprofile_insert_field_group( array( 'name' => $marker . ' group' ) );
	$field = xprofile_insert_field( array( 'field_group_id' => $group, 'name' => $marker . ' secret field', 'type' => 'textbox' ) );
	if ( ! $group || ! $field ) {
		throw new Exception( 'Unable to create xProfile fixture' );
	}

	$post  = array( 'action' => 'buddyforms_update_xprofile_field_selector', 'group_id' => (string) $group );
	$tests = array(
		'editor with nonce gets the fields'        => array( array( 'user' => $users['editor'], 'post' => $post, 'nonce' => 'bf_xprofile_field_selector' ), true ),
		'editor with "none" gets an empty list'    => array( array( 'user' => $users['editor'], 'post' => array_merge( $post, array( 'group_id' => 'none' ) ), 'nonce' => 'bf_xprofile_field_selector' ), 'empty' ),
		'editor without nonce is refused'          => array( array( 'user' => $users['editor'], 'post' => $post ), false ),
		'editor with another action nonce is refused' => array( array( 'user' => $users['editor'], 'post' => $post, 'nonce' => 'bf_tax_loading' ), false ),
		'subscriber with nonce is refused'         => array( array( 'user' => $users['subscriber'], 'post' => $post, 'nonce' => 'bf_xprofile_field_selector' ), false ),
		'subscriber without nonce is refused'      => array( array( 'user' => $users['subscriber'], 'post' => $post ), false ),
		'anonymous with nonce is refused'          => array( array( 'user' => 0, 'post' => $post, 'nonce' => 'bf_xprofile_field_selector' ), false ),
	);

	foreach ( $tests as $name => $test ) {
		list( $case, $expect ) = $test;
		$result = WP_CLI::launch_self( 'eval-file', array( __FILE__, base64_encode( wp_json_encode( $case ) ) ), array(), false, true );
		$json   = json_decode( trim( $result->stdout ), true );
		$data   = ( is_array( $json ) && is_string( $json['data'] ?? null ) ) ? json_decode( $json['data'], true ) : null;
		$fields = is_array( $data ) && isset( $data['group_fields'] ) ? (array) $data['group_fields'] : null;
		$leaks  = false !== strpos( $result->stdout, $marker . ' secret field' );

		if ( true === $expect ) {
			$ok = ! empty( $json['success'] ) && isset( $fields[ $field ] ) && $fields[ $field ] === $marker . ' secret field';
		} elseif ( 'empty' === $expect ) {
			$ok = ! empty( $json['success'] ) && array() === $fields;
		} else {
			$ok = is_array( $json ) && false === $json['success'] && null === $fields && ! $leaks;
		}

		$ok ? $passed++ : $failed++;
		WP_CLI::line( ( $ok ? 'PASS ' : 'FAIL ' ) . $name );
		if ( ! $ok ) {
			WP_CLI::line( '     ' . trim( $result->stdout . ' ' . $result->stderr ) );
		}
	}
} catch ( Exception $e ) {
	$failed++;
	WP_CLI::line( 'FAIL setup: ' . $e->getMessage() );
}

if ( $group ) {
	xprofile_delete_field_group( $group );
}
foreach ( $users as $id ) {
	wp_delete_user( $id );
}

WP_CLI::line( sprintf( 'Results: %d passed, %d failed', $passed, $failed ) );
if ( $failed ) {
	WP_CLI::halt( 1 );
}
