<?php
/*
Plugin Name: University Communications Assets Registration
Plugin URI: http://ucomm.wsu.edu/assets/
Description: Allows users to register for assets.
Author: washingtonstateuniversity, jeremyfelt
Author URI: http://web.wsu.edu/
Version: 0.1
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class WSU_UComm_Assets_Registration {

	/**
	 * Setup the hooks.
	 */
	public function __construct() {
		add_filter( 'wsuwp_sso_create_new_user', array( $this, 'wsuwp_sso_create_new_user' ) );
		add_filter( 'wsuwp_sso_new_user_role',   array( $this, 'wsuwp_sso_new_user_role'   ) );
		add_action( 'wsuwp_sso_user_created',    array( $this, 'remove_user_roles'         ) );
	}

	/**
	 * Enable the automatic creation of a new user if authentication is handled
	 * via WSU Network ID and no user exists.
	 *
	 * @return bool
	 */
	public function wsuwp_sso_create_new_user() {
		return true;
	}

	/**
	 * Set an automatically created user's role as subscriber.
	 *
	 * @return string New role for the new user.
	 */
	public function wsuwp_sso_new_user_role() {
		return 'subscriber';
	}

	/**
	 * Remove all roles from a new user when they are automatically created.
	 *
	 * @param int $user_id A user's ID.
	 */
	public function remove_user_roles( $user_id ) {
		$user = get_userdata( $user_id );
		$user->set_role( '' );
	}
}
new WSU_UComm_Assets_Registration();