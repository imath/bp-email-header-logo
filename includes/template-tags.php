<?php
/**
 * Email Template Tags
 *
 * @since 1.0.0
 *
 * @package BP Email Header Logo
 * @subpackage includes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Prints the Site name
 *
 * @since  1.0.0
 *
 * @param  array $settings Email Settings
 */
function bp_email_hl_the_site_name( $settings = array() ) {
	echo bp_email_hl_get_site_name( $settings );
}

	/**
	 * Gets the Site name
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $settings Email Settings
	 * @return string The site name
	 */
	function bp_email_hl_get_site_name( $settings = array() ) {
		/**
		 * Filters the Site Name inside the Email Template
		 *
		 * @since  1.0.0
		 *
		 * @param string $value The Site Name
		 * @param array  $settings Email Settings
		 */
		return apply_filters( 'bp_email_hl_get_site_name', bp_get_option( 'blogname' ), $settings );
	}

/**
 * Prints the Recipient Salutation
 *
 * @since  1.0.0
 *
 * @param  array $settings Email Settings
 */
function bp_email_hl_the_salutation( $settings = array() ) {
	echo bp_email_hl_get_salutation( $settings );
}

	/**
	 * Gets the Recipient Salutation
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $settings Email Settings
	 * @return string The Recipient Salutation
	 */
	function bp_email_hl_get_salutation( $settings = array() ) {
		$token = '{{recipient.name}}';

		/**
		 * Filters The Recipient Salutation inside the Email Template
		 *
		 * @since  1.0.0
		 *
		 * @param string $value    The Recipient Salutation
		 * @param array  $settings Email Settings
		 * @param string $token    The Recipient token
		 */
		return apply_filters( 'bp_email_hl_get_salutation', sprintf( _x( 'Hi %s,', 'recipient salutation', 'bp-email-header-logo' ), $token ), $settings, $token );
	}

/**
 * Prints the Unsubscribe mention
 *
 * @since  1.0.0
 *
 * @param  array $settings Email Settings
 */
function bp_email_hl_the_unsubsribe_mention( $settings = array() ) {
	echo bp_email_hl_get_unsubsribe_mention( $settings );
}

	/**
	 * Gets the Unsubscribe mention
	 *
	 * @since  1.0.0
	 *
	 * @param  array  $settings Email Settings
	 * @return string The Unsubscribe mention
	 */
	function bp_email_hl_get_unsubsribe_mention( $settings = array() ) {
		/**
		 * Filters The Unsubscribe mention inside the Email Template
		 *
		 * @since  1.0.0
		 *
		 * @param string $value    The Unsubscribe mention
		 * @param array  $settings Email Settings
		 */
		return apply_filters( 'bp_email_hl_get_unsubsribe_mention', _x( 'unsubscribe', 'email', 'bp-email-header-logo' ), $settings );
	}
