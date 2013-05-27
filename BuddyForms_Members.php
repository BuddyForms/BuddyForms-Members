<?php
class BuddyForms_Members {
	
	/**
	 * Initiate the class
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function __construct() {
		$this->init_hook();
		$this->load_constants();

		add_action('bp_include'				, array($this, 'includes')					, 4, 1);
		add_action('init'					, array($this, 'load_plugin_textdomain')	, 10, 1);

	}

	/**
	 * Defines buddyforms_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of WP,
	 * as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function init_hook() {
		do_action('buddyforms_init');
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_constants() {
			
		if (!defined('BUDDYFORMS_MEMBERS_INSTALL_PATH'))
			define('BUDDYFORMS_MEMBERS_INSTALL_PATH', dirname(__FILE__) . '/');

		if (!defined('BUDDYFORMS_MEMBERS_INCLUDES_PATH'))
			define('BUDDYFORMS_MEMBERS_INCLUDES_PATH', BUDDYFORMS_MEMBERS_INSTALL_PATH . 'includes/');

		if (!defined('BUDDYFORMS_MEMBERS_TEMPLATE_PATH'))
			define('BUDDYFORMS_MEMBERS_TEMPLATE_PATH', BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'templates/');
		
	}

	/**
	 * Includes files needed by buddyforms
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function includes() {
		
		require_once (BUDDYFORMS_INCLUDES_PATH . 'functions.php');
		require_once (BUDDYFORMS_INCLUDES_PATH . 'member-extention.php');
		
		if (!class_exists('BP_Theme_Compat'))
			require_once (BUDDYFORMS_INCLUDES_PATH . 'bp-backwards-compatibililty-functions.php');
	}

	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('buddyforms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
}
