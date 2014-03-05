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
	 * @var array The array of font slugs, quantities, and names.
	 */
	var $fonts = array(
		'office_support_qty'      => array( 'qty' => 0, 'name' => 'Office Support Package' ),
		'stone_sans_nocharge_qty' => array( 'qty' => 0, 'name' => 'Stone Sans II (no charge)' ),
		'stone_sans_charge_qty'   => array( 'qty' => 0, 'name' => 'Stone Sans II ($30)' ),
		'full_stone_nocharge_qty' => array( 'qty' => 0, 'name' => 'Full Stone Font Family (no charge)' ),
		'full_stone_charge_qty'   => array( 'qty' => 0, 'name' => 'Full Stone Font Family ($60)' ),
	);

	/**
	 * Setup the hooks.
	 */
	public function __construct() {
		add_filter( 'wsuwp_sso_create_new_user', array( $this, 'wsuwp_sso_create_new_user' ), 10, 1 );
		add_filter( 'wsuwp_sso_new_user_role',   array( $this, 'wsuwp_sso_new_user_role'   ), 10, 1 );
		add_filter( 'user_has_cap',              array( $this, 'map_asset_request_cap'     ), 10, 4 );

		add_action( 'wsuwp_sso_user_created',       array( $this, 'remove_user_roles'    ), 10, 1 );
		add_action( 'init',                         array( $this, 'register_post_type'   ), 10, 1 );
		add_action( 'init',                         array( $this, 'temp_redirect'        ),  5, 1 );
		add_action( 'wp_ajax_submit_asset_request', array( $this, 'submit_asset_request' ), 10, 1 );
		add_action( 'transition_post_status',       array( $this, 'grant_asset_access'   ), 10, 3 );
		add_action( 'add_meta_boxes',               array( $this, 'add_meta_boxes'       ), 10, 2 );

		add_shortcode( 'ucomm_asset_request',    array( $this, 'ucomm_asset_request_display' ) );
	}

	/**
	 * Add a temporary redirect to force all /assets/ traffic to /assets/font-request/ as that will
	 * be the only asset available at first.
	 */
	public function temp_redirect() {
		if ( '/assets/' === $_SERVER['REQUEST_URI'] ) {
			wp_safe_redirect( site_url( '/font-request/' ) );
			die();
		}
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
			'supports'           => array( 'title' ),
		);

		register_post_type( $this->post_type_slug, $args );
	}

	/**
	 * Map capabilities for users that are requesting access to assets.
	 *
	 * @param array   $allcaps An array of all the role's capabilities.
	 * @param array   $caps    Actual capabilities for meta capability.
	 * @param array   $args    Optional parameters passed to has_cap(), typically object ID.
	 * @param WP_User $user    The user object.
	 *
	 * @return array Modified list of capabilities for a user.
	 */
	public function map_asset_request_cap( $allcaps, $cap, $args, $user ) {
		$request_asset_cap = get_user_meta( $user->ID, $this->user_meta_key, true );

		// Loop through the assets this user has access to and set the capabilities.
		foreach( (array) $request_asset_cap as $asset_cap ) {
			$allcaps[ 'request_asset_' . $asset_cap ] = true;
		}

		return $allcaps;
	}

	/**
	 * Handle the display of the ucomm_asset_request shortcode.
	 *
	 * @param array @args Arguments used with the shortcode.
	 *
	 * @return string HTML output
	 */
	public function ucomm_asset_request_display( $args ) {
		// If a default type is not specified, we check for fonts access.

		if ( empty( $args['type'] ) ) {
			$asset_type = 'fonts';
		} else {
			$asset_type = sanitize_key( $args['type'] );
		}

		$capability = 'request_asset_' . $asset_type;

		// Build the output to return for use by the shortcode.
		ob_start();
		?>
		<div id="asset-request">
			<?php

			if ( is_user_member_of_blog() ) {
				if ( current_user_can( $capability ) ) {
					// Retrieve assets attached to this page and display them in a list for download.
					$available_assets = get_attached_media( 'application/zip', get_queried_object_id() );
					echo '<h3>Available Assets</h3><ul>';
					foreach( $available_assets as $asset ) {
						echo '<li><a href="' . esc_url( wp_get_attachment_url( $asset->ID ) ) .'">' . esc_html( $asset->post_title ) . '</a></li>';
					}
					echo '</ul>';
				} else {
					$user_requests = new WP_Query(
						array(
						'post_type'      => $this->post_type_slug,
						'author'         => get_current_user_id(),
						'post_status'    => array( 'publish', 'pending' ),
						'posts_per_page' => 1,
						'meta_query'     => array(
							array(
								'key'       => '_ucomm_asset_type',
								'value'     => $asset_type,
							),
						),
					));

					if ( $user_requests->have_posts() ) {
						echo 'We have received your request for access. You should receive verification and instructions shortly.';
					} else {
						$this->asset_form_output( $asset_type );
					}
				}
			} else {
				if ( is_user_logged_in() ) {
					// To ease the workflow, anybody authenticated user that visits this site should be made a subscriber.
					add_existing_user_to_blog( array( 'user_id' => get_current_user_id(), 'role' => 'subscriber' ) );
					$this->asset_form_output( $asset_type );
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
	 *
	 * @param string $asset_type Type of the asset being requested.
	 */
	private function asset_form_output( $asset_type ) {
		wp_enqueue_script( 'ucomm_asset_request', plugins_url( '/js/asset-request.js', __FILE__ ), array( 'jquery' ), $this->script_version, true );
		wp_localize_script( 'ucomm_asset_request', 'ucomm_asset_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		?>
		<form id="asset-request-form" class="asset-request">
			<input type="hidden" id="asset-type" value="<?php echo esc_attr( $asset_type ); ?>" />
			<input type="hidden" id="asset-request-nonce" value="<?php echo esc_attr( wp_create_nonce( 'asset-request' ) ); ?>" />
			
			<label for="first_name">First Name:</label><br />
			<input type="text" name="first_name" id="first-name" value="" style="width:100%;" />

			<label for="last_name">Last Name:</label><br />
			<input type="text" name="last_name" id="last-name" value="" style="width:100%;" />

			<label for="email_address">Email Address:</label><br>
			<input type="text" name="email_address" id="email-address" value="" style="width:100%;" />

			<label for="area">Area Number:</label><br>
			<input type="text" name="area" id="area" value="" style="width:100%;" />			

			<label for="deparatment">College/Department:</label><br>
			<input type="text" name="department" id="department" value="" style="width:100%;" />

			<label for="job_description">Job Description:</label><br>
			<input type="text" name="job_description" id="job-description" value="" style="width:100%;" />			

			<ol>
				<li>
					<p><strong>Office Support Package.</strong> Includes the basic Stone Sans and Stone Serif font families, which are necessary for creating office communications/memorandum for both internal and external audiences in compliance with University brand standards.</p>
					<input type="text" name="office_support_qty" id="office-support-qty" size="2" value="0" />
					<label for="office_support_qty">Office Support Package (no charge)</label>
				</li>
				<li>
					<p><strong>Stone Sans II Font Family.</strong> Includes regular Stone Sans plus Stone Sans Condensed. Users who already have regular Stone Sans and Stone Serif installed on their machines may download this package to add Stone Sans Condensed to their font library. This package is only used by those involved with creating visual designs in support of the University brand as part of their prescribed work activities.</p>
					<input type="text" name="stone_sans_nocharge_qty" id="stone-sans-nocharge-qty" size="2" value="0" />
					<label for="stone_sans_nocharge_qty">Stone Sans II (no charge to University design staff)*</label>
					<br />
					<input type="text" name="stone_sans_charge_qty" id="stone-sans-charge-qty" size="2" value="0" />
					<label for="stone_sans_charge_qty">Stone Sans II ($30 for non-design staff)</label>
					<p>*If you are requesting this package at no charge and your current position description does not explicitly include visual design responsibilities, or if you are requesting the package on behalf of such an individual or individuals, please provide a brief justification in support of your request in the field below.</p>
				</li>
				<li>
					<p><strong>Full Stone Font Family.</strong> Includes Stone Sans II (which includes Stone Sans Condensed) and Stone Serif families. This package is for new users who do not currently have the regular Stone Sans and Stone Serif fonts installed on their machines. This package is only used by those involved with creating visual designs in support of the University brand as part of their prescribed work activities.</p>
					<input type="text" name="full_stone_nocharge_qty" id="full-stone-nocharge-qty" size="2" value="0" />
					<label for="full_stone_nocharge_qty">Full Stone Font Family (no charge to University design staff)**</label>
					<br />
					<input type="text" name="full_stone_charge_qty" id="full-stone-charge-qty" size="2" value="0" />
					<label for="full_stone_charge_qty">Full Stone Font Family ($60 for non-design staff)**</label>
					<p>**If you are requesting this package at no charge and your current position description does not explicitly include visual design responsibilities, or are requesting the package on behalf of such an individual or individuals, please provide brief justification in support of your request in the field below.</p>
				</li>
			</ol>

			<label for="notes">Justification for font family:</label><br>
			<textarea name="notes" id="request-notes" rows="5" style="width:100%;"></textarea>

			<input type="submit" id="submit-asset-request" value="Request Assets" style="float:right">
			<div class="clear"></div>
		</form>
		<?php
	}

	/**
	 * Handle the submission of an asset request form through AJAX.
	 *
	 * Asset type and email address are added to the post title for quick identification
	 * in the admin. Notes submitted by the requesting user are added as post content.
	 *
	 * Additional fields should find their way to post meta so that they can be displayed
	 * as part of the request in the admin.
	 */
	public function submit_asset_request() {
		wp_verify_nonce( 'asset-request' );

		$post = array(
			'post_status' => 'pending',
			'post_type' => $this->post_type_slug,
			'post_author' => get_current_user_id(),
		);

		// An asset type is required to grant access to an asset type.
		if ( empty( $_POST['asset_type'] ) ) {
			echo json_encode( array( 'error' => 'No asset type was supplied.' ) );
			die();
		} else {
			$asset_type = sanitize_text_field( $_POST['asset_type'] );
		}

		// We should have at least one font quantity specified for the request if it is valid.
		$font_check = false; // Aids in verification that a quantity has been requested.
		foreach ( $this->fonts as $font_slug => $font_data ) {
			if ( ! empty( $_POST[ $font_slug ] ) ) {
				$this->fonts[ $font_slug ][ 'qty' ] = absint( $_POST[ $font_slug ] );
				$font_check = true;
			} else {
				$this->fonts[ $font_slug ][ 'qty' ] = 0;
			}
		}

		if ( false === $font_check ) {
			echo json_encode( array( 'error' => 'Please enter a quantity for at least one font.' ) );
			die();
		}

		if ( empty( $_POST['first_name'] ) ) {
			echo json_encode( array( 'error' => 'Please enter first name.' ) );
			die();
		} else {
			$first_name = sanitize_text_field( $_POST['first_name'] );
		}

		if ( empty( $_POST['last_name'] ) ) {
			echo json_encode( array( 'error' => 'Please enter last name.' ) );
			die();
		} else {
			$last_name = sanitize_text_field( $_POST['last_name'] );
		}

		if ( empty( $_POST[ 'email_address'] ) ) {
			echo json_encode( array( 'error' => 'Please enter email address.' ) );
			die();
		} else {
			$user = get_userdata( get_current_user_id() );
			$email = sanitize_email( $_POST['email_address'] );
			$post['post_title'] = sanitize_text_field( 'Request from ' . $user->user_login . ' ' . $email );
		}

		if ( empty( $_POST['area'] ) ) {
			echo json_encode( array( 'error' => 'Please enter area number.' ) );
			die();
		} else {
			$area = sanitize_text_field( $_POST['area'] );
		}

		if ( empty( $_POST['department'] ) ) {
			echo json_encode( array( 'error' => 'Please enter department name.' ) );
			die();
		} else {
			$department = sanitize_text_field( $_POST['department'] );
		}

		if ( empty( $_POST['job_description'] ) ) {
			echo json_encode( array( 'error' => 'Please enter job description.' ) );
			die();
		} else {
			$job_description = sanitize_text_field( $_POST['job_description'] );
		}

		if ( empty( $_POST['notes'] ) ) {
			$post['post_content'] = 'No justification notes included in request.';
		} else {
			$post['post_content'] = wp_kses_post( $_POST['notes'] );
		}

		$post_id = wp_insert_post( $post );

		if ( is_wp_error( $post_id ) ) {
			echo json_encode( array( 'error' => 'There was an error creating the request.' ) );
			die();
		}

		//field meta data stuff
		update_post_meta( $post_id, '_ucomm_request_first_name', $first_name );
		update_post_meta( $post_id, '_ucomm_request_last_name',  $last_name );
		update_post_meta( $post_id, '_ucomm_request_email', $email );
		update_post_meta( $post_id, '_ucomm_request_area', $area );
		update_post_meta( $post_id, '_ucomm_request_department', $department );
		update_post_meta( $post_id, '_ucomm_request_job_description', $job_description );
		update_post_meta( $post_id, '_ucomm_font_qty_request', $this->fonts );
		update_post_meta( $post_id, '_ucomm_asset_type', $asset_type );

		// Basic notification email text.
		$message =  "Thank you for completing the font request form.\r\n\r\n";
		$message .= "University Communications has been notified of your request and you should be hearing something shortly.\r\n\r\n";
		$message .= "Once a request has been approved, you will receive another email with a link to download the font files.\r\n\r\n";
		$message .= "Thank you,\r\nUniversity Communications\r\n";

		// Notify the requestor with an email that a request has been received.
		wp_mail( $email, 'Font Download Request Received', $message );

		echo json_encode( array( 'success' => 'Request received.' ) );
		die();
	}

	/**
	 * Add meta boxes where required.
	 *
	 * @param string $post_type Post type slug.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( 'ucomm_asset_request' === $post_type ) {
			add_meta_box( 'ucomm-asset-request-details', 'Asset Request Details:', array( $this, 'asset_request_details' ), null, 'normal', 'high' );
		}
	}

	/**
	 * Display the details for the loaded asset request in a meta box.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function asset_request_details( $post ) {
		$first_name  = get_post_meta( $post->ID, '_ucomm_request_first_name', true );
		$last_name   = get_post_meta( $post->ID, '_ucomm_request_last_name',  true );
		$email       = get_post_meta( $post->ID, '_ucomm_request_email',      true );
		$area        = get_post_meta( $post->ID, '_ucomm_request_area',       true );
		$department  = get_post_meta( $post->ID, '_ucomm_request_department', true );
		$job_desc    = get_post_meta( $post->ID, '_ucomm_request_job_desc',   true );
		$this->fonts = get_post_meta( $post->ID, '_ucomm_font_qty_request',   true );
		?>
		<ul>
			<li>First Name: <?php echo esc_html( $first_name ); ?></li>
			<li>Last Name:  <?php echo esc_html( $last_name ); ?></li>
			<li>Email: <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
			<li>Area:       <?php echo esc_html( $area ); ?></li>
			<li>Department: <?php echo esc_html( $department ); ?></li>
			<li>Job Description: <?php echo esc_html( $job_desc ); ?></li>
		</ul>
		<h4>Requested Fonts:</h4>
		<table>
			<thead><tr><th align="left">Font</th><th align="right">Quantity</th></tr></thead>
		<?php foreach( $this->fonts as $font ) : ?>
			<tr><td><?php echo esc_html( $font['name'] ); ?></td><td align="right"><?php echo absint( $font['qty'] ); ?></td></tr>
		<?php endforeach; ?>
		</table>

		<h4>Notes for use justification:</h4>
		<?php echo $post->post_content; ?>
		<?php
	}

	/**
	 * Grant a user access to asset downloads when their asset request is published. Remove
	 * user access to asset downloads when their asset request is unpublished.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Current post object.
	 */
	public function grant_asset_access( $new_status, $old_status, $post ) {
		if ( $this->post_type_slug !== $post->post_type ) {
			return;
		}

		// Don't accidentally revoke your own access.
		if ( get_current_user_id() == $post->post_author ) {
			return;
		}

		$user_id = absint( $post->post_author );

		$asset_type = get_post_meta( $post->ID, '_ucomm_asset_type', true );
		if ( empty( $asset_type ) ) {
			$asset_type = 'fonts';
		}

		// Add access to assets.
		if ( 'pending' === $old_status && 'publish' === $new_status ) {
			$current_access = get_user_meta( $user_id, $this->user_meta_key, true );
			if ( empty( $current_access ) ) {
				$update_access = array( $asset_type );
			} else {
				$update_access = (array) $current_access;
				$update_access[] = $asset_type;
			}
			update_user_meta( $user_id, $this->user_meta_key, $update_access );
		}

		// Remove access to assets.
		if ( 'publish' === $old_status && 'publish' !== $new_status ) {
			$current_access = get_user_meta( $user_id, $this->user_meta_key, true );
			$new_access = (array) $current_access;

			if ( array_key_exists( $asset_type, $new_access ) ) {
				unset( $new_access[ $asset_type ] );
			}

			if ( empty( $new_access ) ) {
				delete_user_meta( $user_id, $this->user_meta_key );
			} else {
				update_user_meta( $user_id, $this->user_meta_key, $new_access );
			}
		}
	}
}
new WSU_UComm_Assets_Registration();