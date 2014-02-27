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
	 * @var string Post type slug for asset requests.
	 */
	var $post_type_slug = 'ucomm_asset_request';

	/**
	 * @var string User meta key used to assign asset access.
	 */
	var $user_meta_key = '_ucomm_asset_access';

	/**
	 * Setup the hooks.
	 */
	public function __construct() {
		add_filter( 'wsuwp_sso_create_new_user', array( $this, 'wsuwp_sso_create_new_user' )        );
		add_filter( 'wsuwp_sso_new_user_role',   array( $this, 'wsuwp_sso_new_user_role'   )        );
		add_filter( 'map_meta_cap',              array( $this, 'map_asset_request_cap'     ), 10, 4 );

		add_action( 'wsuwp_sso_user_created',    array( $this, 'remove_user_roles'         ) );
		add_action( 'init',                      array( $this, 'register_post_type'        ) );

		add_shortcode( 'ucomm_asset_request',    array( $this, 'ucomm_asset_request_display' ) );
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

	/**
	 * Register the post type used to handle asset registration requests.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => 'Asset Request',
			'singular_name'      => 'Asset Request',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Asset Request',
			'edit_item'          => 'Edit Asset Request',
			'new_item'           => 'New Asset Request',
			'all_items'          => 'All Asset Requests',
			'view_item'          => 'View Asset Request',
			'search_items'       => 'Search Asset Requests',
			'not_found'          => 'No asset requests found',
			'not_found_in_trash' => 'No asset requests found in Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Asset Requests',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_admin_bar'  => false,
			'query_var'          => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => 5,
			'supports'           => array( 'title', 'editor' ),
		);

		register_post_type( $this->post_type_slug, $args );
	}

	/**
	 * Map capabilities for users that are requesting access to assets.
	 *
	 * @param array  $caps    Array of capabilities.
	 * @param string $cap     Capability being checked.
	 * @param int    $user_id ID of the user for which capabilities are being checked.
	 * @param array  $args    Array of arguments.
	 *
	 * @return array Modified list of capabilities for a user.
	 */
	public function map_asset_request_cap( $caps, $cap, $user_id, $args ) {
		if ( 'request_asset' === $cap ) {
			$request_asset_cap = get_user_meta( $user_id, '_ucomm_asset_access', true );

			if ( 'fonts' !== $request_asset_cap ) {
				$caps[] = 'do_not_allow';
			}
		}

		return $caps;
	}

	public function ucomm_asset_request_display() {
		// Build the output to return for use by the shortcode.
		ob_start();
		?>
		<div id="asset-request-form">
			<?php

			if ( current_user_can( 'request_asset' ) ) {
				echo 'allowed'; // display asset request form.
			} else {
				echo 'not allowed'; // display register link.
			}
			?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
new WSU_UComm_Assets_Registration();