<?php

add_filter( 'wpmu_validate_user_signup', 'wsuwp_validate_user_signup' );
/**
 * Temporarily override user validation in anticipation of ticket #17904. In reality, we'll
 * be doing all of our authentication through active directory, so this won't be necessary,
 * but it does come in useful during initial testing.
 *
 * @param array $result Existing result from the wpmu_validate_user_signup() process
 *
 * @return array New results of our own validation
 */
function wsuwp_validate_user_signup( $result ) {
	global $wpdb;

	$user_login = $result['user_name'];
	$original_user_login = $user_login;
	$result = array();
	$result['errors'] = new WP_Error();

	// User login cannot be empty
	if( empty( $user_login ) )
		$result['errors']->add( 'user_name', __( 'Please enter a username.' ) );

	// User login must be at least 4 characters
	if ( strlen( $user_login ) < 4 )
		$result['errors']->add( 'user_name',  __( 'Username must be at least 4 characters.' ) );

	// Strip any whitespace and then match against case insensitive characters a-z 0-9 _ . - @
	$user_login = preg_replace( '/\s+/', '', sanitize_user( $user_login, true ) );

	// If the previous operation generated a different value, the username is invalid
	if ( $user_login !== $original_user_login )
		$result['errors']->add( 'user_name', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );

	// Check the user_login against an array of illegal names
	$illegal_names = get_site_option( 'illegal_names' );
	if ( false == is_array( $illegal_names ) ) {
		$illegal_names = array(  'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' );
		add_site_option( 'illegal_names', $illegal_names );
	}

	if ( true === in_array( $user_login, $illegal_names ) )
		$result['errors']->add( 'user_name',  __( 'That username is not allowed.' ) );

	// User login must have at least one letter
	if ( preg_match( '/^[0-9]*$/', $user_login ) )
		$result['errors']->add( 'user_name', __( 'Sorry, usernames must have letters too!' ) );

	// Check if the username has been used already.
	if ( username_exists( $user_login ) )
		$result['errors']->add( 'user_name', __( 'Sorry, that username already exists!' ) );

	if ( is_multisite() ) {
		// Is a signup already pending for this user login?
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE user_login = %s ", $user_login ) );
		if ( $signup != null ) {
			$registered_at =  mysql2date( 'U', $signup->registered );
			$now = current_time( 'timestamp', true );
			$diff = $now - $registered_at;
			// If registered more than two days ago, cancel registration and let this signup go through.
			if ( $diff > 2 * DAY_IN_SECONDS )
				$wpdb->delete( $wpdb->signups, array( 'user_login' => $user_login ) );
			else
				$result['errors']->add( 'user_name', __( 'That username is currently reserved but may be available in a couple of days.' ) );
		}
	}

	$result['user_login']          = $user_login;
	$result['original_user_login'] = $original_user_login;

	return $result;
}