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
	 * @var string Script version used to break cache when needed.
	 */
	var $script_version = '0.1.1';

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

		add_action( 'wsuwp_sso_user_created',       array( $this, 'remove_user_roles'         ) );
		add_action( 'init',                         array( $this, 'register_post_type'        ) );
		add_action( 'wp_ajax_submit_asset_request', array( $this, 'submit_asset_request'      ) );

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

	/**
	 * Handle the display of the ucomm_asset_request shortcode.
	 *
	 * @return string HTML output
	 */
	public function ucomm_asset_request_display() {
		// Build the output to return for use by the shortcode.
		ob_start();
		?>
		<div id="asset-request">
			<?php

			if ( is_user_member_of_blog() ) {
				if ( current_user_can( 'request_asset' ) ) {
					// Retrieve assets attached to this page and display them in a list for download.
					$available_assets = get_attached_media( 'application/zip', get_queried_object_id() );
					echo '<h3>Available Assets</h3><ul>';
					foreach( $available_assets as $asset ) {
						echo '<li><a href="' . esc_url( wp_get_attachment_url( $asset->ID ) ) .'">' . esc_html( $asset->post_title ) . '</a></li>';
					}
					echo '</ul>';
				} else {
					$user_requests = get_posts( array(
						'post_type'      => $this->post_type_slug,
						'author'         => get_current_user_id(),
						'post_status'    => 'pending',
						'posts_per_page' => 1,
					));

					if ( 1 <= count( $user_requests ) ) {
						echo 'We have received your request for access. You should receive verification and instructions shortly.';
					} else {
						$this->asset_form_output();
					}
				}
			} else {
				if ( is_user_logged_in() ) {
					// To ease the workflow, anybody authenticated user that visits this site should be made a subscriber.
					add_existing_user_to_blog( array( 'user_id' => get_current_user_id(), 'role' => 'subscriber' ) );
					$this->asset_form_output();
				} else {
					echo '<p>Please <a href="' . wp_login_url( network_site_url( $_SERVER['REQUEST_URI'] ), true ) . '">authenticate with your WSU Network ID</a> to request asset access.</p>';
				}
			}
			?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Display the HTML used to handle the asset request form.
	 */
	private function asset_form_output() {
		wp_enqueue_script( 'ucomm_asset_request', plugins_url( '/js/asset-request.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'ucomm_asset_request', 'ucomm_asset_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		?>
		<form id="asset-request-form" class="asset-request">
			<input type="hidden" id="asset-request-nonce" value="<?php echo esc_attr( wp_create_nonce( 'asset-request' ) ); ?>" />
			<label for="email_address">Email Address:</label><br>
			<input type="text" name="email_address" id="email-address" value="" style="width:100%;" />
			<label for="deparatment">College/Department:</label><br>
			<input type="text" name="department" id="department" value="" style="width:100%;" />
			<label for="notes">Request Notes:</label><br>
			<textarea name="notes" id="request-notes" rows="10" style="width:100%;"></textarea>
			<input type="submit" id="submit-asset-request" value="Request Assets" style="float:right">
			<div class="clear"></div>
		</form>
		<?php
	}

	/**
	 * Handle the submission of an asset request form through AJAX.
	 */
	public function submit_asset_request() {
		wp_verify_nonce( 'asset-request' );

		$post = array(
			'post_status' => 'pending',
			'post_type' => $this->post_type_slug,
			'post_author' => get_current_user_id(),
		);

		if ( isset( $_POST[ 'email_address'] ) ) {
			$post['post_title'] = sanitize_text_field( 'Request from ' . $_POST['email_address'] );
		} else {
			$user = get_userdata( get_current_user_id() );
			$post['post_title'] = sanitize_text_field( 'Request from ' . $user->user_login . ' ' . $_POST['email_address'] );
		}

		if ( isset( $_POST['notes'] ) ) {
			$post['post_content'] = wp_kses_post( $_POST['notes'] );
		}

		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			echo json_encode( array( 'error' => 'There was an error creating the request.' ) );
			die();
		}

		if ( isset( $_POST['department'] ) ) {
			$department = sanitize_text_field( $_POST['department'] );
			update_post_meta( $post_id, '_ucomm_request_department', $department );
		}

		echo json_encode( array( 'success' => 'Request received.' ) );
		die();
	}
}
new WSU_UComm_Assets_Registration();