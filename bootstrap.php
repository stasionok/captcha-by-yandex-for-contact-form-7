<?php
/**
 * Plugin Name: Contact Form 7 Yandex Captcha
 * Description: Allow use Yandex captcha for your forms with Contact Form 7
 * Plugin URI:  https://qbein.net/chat-with-gpt
 * Requires at least: 5.0
 * Requires PHP: 8.0
 * Author:      stasionok
 * Author URI:  https://t.me/stasionok
 * Version:     1.0.0
 * License: GPLv2 or later
 * Text Domain: contact-form-7-yandex-captcha
 * Domain Path: /languages
 *
 * Network: false
 */

defined( 'ABSPATH' ) || exit;

const CFYC_REQUIRED_PHP_VERSION = '8.0';
const CFYC_REQUIRED_WP_VERSION  = '5.0';

/**
 * Checks if the system requirements are met
 *
 * @return array Array of errors or false if all is ok
 */
function cfyc_requirements_met(): array {
	global $wp_version;

	$errors = [];

	if ( version_compare( PHP_VERSION, CFYC_REQUIRED_PHP_VERSION, '<' ) ) {
		$errors[] = printf(
			esc_html__( 'Your server is running PHP version %1$s but this plugin requires at least PHP %2$s. Please run an upgrade.', 'chat-with-gpt' ),
			PHP_VERSION,
			CFYC_REQUIRED_PHP_VERSION
		);
	}

	if ( version_compare( $wp_version, CFYC_REQUIRED_WP_VERSION, '<' ) ) {
		$errors[] = printf(
			esc_html__( 'Your Wordpress running version is %1$s but this plugin requires at least version %2$s. Please run an upgrade.', 'chat-with-gpt' ),
			esc_html( $wp_version ),
			CFYC_REQUIRED_WP_VERSION
		);
	}

//	$extensions = get_loaded_extensions();
//
//	if ( ! in_array( 'curl', $extensions ) ) {
//		$errors[] = esc_html__( 'Your need to install curl php extension to continue plugin use. Please install it first.', 'chat-with-gpt' );
//	}
//
//	if ( ! in_array( 'json', $extensions ) ) {
//		$errors[] = esc_html__( 'Your need to install json php extension to continue plugin use. Please install it first.', 'chat-with-gpt' );
//	}

	return $errors;
}

/**
 * Begins execution of the plugin.
 *
 * Plugin run entry point
 */
function cfyc_run(): void {
	$plugin = new CFYC_Common();
	$plugin->run();
}


/**
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the plugin requirements are met.
 * Otherwise, older PHP installations could crash when trying to parse it.
 */
require_once( __DIR__ . '/controller/class-cfyc-common.php' );

$errors = cfyc_requirements_met();
if ( ! $errors ) {
	if ( method_exists( CFYC_Common::class, 'activate' ) ) {
		register_activation_hook( __FILE__, 'CFYC_Common::activate' );
	}

	cfyc_run();
} else {
	add_action( 'admin_notices', function () use ( $errors ) {
		require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
	} );
}

if ( method_exists( CFYC_Common::class, 'deactivate' ) ) {
	register_deactivation_hook( __FILE__, array( CFYC_Common::class, 'deactivate' ) );
}

if ( method_exists( CFYC_Common::class, 'uninstall' ) ) {
	register_uninstall_hook( __FILE__, array( CFYC_Common::class, 'uninstall' ) );
}

