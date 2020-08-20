<?php
/*
Plugin Name: Paid Memberships Pro - Member Homepages Add On
Plugin URI: https://www.paidmembershipspro.com/add-ons/member-homepages/
Description: Redirect members to a unique homepage or landing page based on their level.
Version: 0.3
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
Text Domain: pmpro-member-homepages
Domain Path: /languages
*/

define( 'PMPRO_MEMBER_HOMEPAGES_VERSION', '0.2' ); 

/**
 * Load text domain
 * pmpromh_load_plugin_text_domain
 */
function pmpromh_load_plugin_text_domain() {
	load_plugin_textdomain( 'pmpro-member-homepages', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'pmpromh_load_plugin_text_domain' ); 

/*
	Function to redirect member on login to their membership level's homepage
*/
function pmpromh_login_redirect( $redirect_to, $request, $user ) {

	// Check level
	if ( ! empty( $user ) && ! empty( $user->ID ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$level = pmpro_getMembershipLevelForUser( $user->ID );

		// Member has a level, does their level have a homepage?
		if ( ! empty( $level ) && isset( $level->id ) ) {
			$member_homepage_id = pmpromh_getHomepageForLevel( $level->id );
			$ignore_redirect_to = pmpromh_ignore_redirect_to( $level->id );
			// Member has a member homepage, override the redirect_to if level set to ignore other redirects.
			if ( ! empty( $member_homepage_id ) && ! is_page( $member_homepage_id ) && ! empty( $ignore_redirect_to ) ) {
				$redirect_to = get_permalink( $member_homepage_id );
			}
		}
	}

	return $redirect_to;
}
add_filter('login_redirect', 'pmpromh_login_redirect', 9, 3);

/*
	Function to redirect member to their membership level's homepage when 
	trying to access your site's front page (static page or posts page).
*/

function pmpromh_template_redirect_homepage() {
	global $current_user;
	//is there a user to check?
	if( !empty($current_user->ID) && is_front_page() && pmpromh_allow_homepage_redirect() ) {
		$member_homepage_id = pmpromh_getHomepageForLevel();
		if(!empty($member_homepage_id) && !is_page( $member_homepage_id ) && ! empty( get_post( $member_homepage_id ) ) ) {
			wp_redirect( get_permalink( $member_homepage_id ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'pmpromh_template_redirect_homepage' );

/**
 * Function to determine if a user should be redirected from the homepage or not.
 *
 * @param int|null $level_id The level ID for the user.
 *
 * @return bool true if yes, false if no.
 */
function pmpromh_allow_homepage_redirect( $level_id = null ) {
	if ( empty( $level_id ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		global $current_user;
		$level = pmpro_getMembershipLevelForUser( $current_user->ID );
		if ( ! empty( $level ) ) {
			$level_id = $level->id;
		}
	}

	// look up by level.
	if ( ! empty( $level_id ) ) {
		$homepage_redirect = filter_var( get_option( 'pmpro_member_homepage_redirect_' . $level_id, true ), FILTER_VALIDATE_BOOLEAN );
	} else {
		$homepage_redirect = true;
	}

	return $homepage_redirect;
}

/*
	Function to get a homepage for level
*/
function pmpromh_getHomepageForLevel( $level_id = NULL ) {
	if(empty($level_id) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		global $current_user;
		$level = pmpro_getMembershipLevelForUser( $current_user->ID );
		if( !empty( $level ) ) {
			$level_id = $level->id;
		}
	}
	
	//look up by level
	if( !empty( $level_id ) ) {
		$member_homepage_id = get_option( 'pmpro_member_homepage_' . $level_id );
	} else {
		$member_homepage_id = false;
	}

	/**
	 * Filter to allow the Member Homepage ID to be set to any post ID, including a Custom Post Type.
	 *
	 * @param $member_homepage_id int The level's asssigned homepage ID. False is not set for this level.
	 * @param $level_id int The ID of the current user's membership level.
	 *
	 * @return $member_homepage_id int The member homepage ID.
	 *
	 */
	$member_homepage_id = apply_filters( 'pmpro_member_homepage_id', $member_homepage_id, $level_id );

	return $member_homepage_id;
}

/**
 * Function to determine if login redirection should ignore other redirect_to vars or not.
 *
 * @param int|null $level_id The level ID for the user.
 *
 * @return bool true if yes, false if no.
 */
function pmpromh_ignore_redirect_to( $level_id = null ) {
	if ( empty( $level_id ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		global $current_user;
		$level = pmpro_getMembershipLevelForUser( $current_user->ID );
		if ( ! empty( $level ) ) {
			$level_id = $level->id;
		}
	}

	// look up by level.
	if ( ! empty( $level_id ) ) {
		$ignore_redirect_to = filter_var( get_option( 'pmpro_member_homepage_ignore_redirect_to_' . $level_id, true ), FILTER_VALIDATE_BOOLEAN );
	} else {
		$ignore_redirect_to = true;
	}

	return $ignore_redirect_to;
}

/**
 * Membership Settings.
 */
function pmpromh_pmpro_membership_level_after_other_settings() {
	?>
	<hr />
	<h3><?php esc_html_e( 'Membership Homepage', 'pmpro-member-homepages' ); ?></h3>
	<p><?php _e( "Use these settings to redirect members to a specific page on login or any time they visit your site's homepage/frontpage.", 'pmpro-member-homepages' );?></p>
	<table>
		<tbody class="form-table">
			<tr>
				<th scope="row" valign="top"><label for="member_homepage"><?php esc_html_e( 'Member Homepage', 'pmpro-member-homepages' ); ?>:</label></th>
				<td>
					<?php
						$level_id           = absint( filter_input( INPUT_GET, 'edit', FILTER_DEFAULT ) );
						$member_homepage_id = pmpromh_getHomepageForLevel( $level_id );
					?>
					<?php
					wp_dropdown_pages(
						array(
							'name'             => 'member_homepage_id',
							'show_option_none' => '-- ' . esc_html__( 'Choose One', 'pmpro-member-homepages' ) . ' --',
							'selected'         => absint( $member_homepage_id ),
						)
					);
					?>
					<p class="description"><?php _e( 'Unless another "redirect_to" value is set, members of this level will be redirected to this page on login.', 'pmpro-member-homepages' );?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php esc_html_e( 'Homepage Redirect', 'pmpro-member-homepages' ); ?>:</th>
				<td>
					<?php
						$checked = filter_var( get_option( 'pmpro_member_homepage_redirect_' . $level_id, true ), FILTER_VALIDATE_BOOLEAN );
					?>
					<input type="hidden" value="0" name="member_homepage_redirect" />
					<input type="checkbox" value="1" id="member_homepage_redirect" name="member_homepage_redirect" <?php checked( true, $checked, true ); ?> /> <label for="member_homepage_redirect"><?php esc_html_e( "Check to have members always redirected away from your site's homepage/frontpage.", 'pmpro-member-homepages' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><?php esc_html_e( 'Override Other Redirects', 'pmpro-member-homepages' ); ?>:</th>
				<td>
					<?php
						$checked = filter_var( get_option( 'pmpro_member_homepage_ignore_redirect_to_' . $level_id, true ), FILTER_VALIDATE_BOOLEAN );
					?>
					<input type="hidden" value="0" name="member_homepage_ignore_redirect_to" />
					<input type="checkbox" value="1" id="member_homepage_ignore_redirect_to" name="member_homepage_ignore_redirect_to" <?php checked( true, $checked, true ); ?> /> <label for="member_homepage_ignore_redirect_to"><?php esc_html_e( 'Check to ignore other "redirect_to" values on login and always redirect members to their homepage on login', 'pmpro-member-homepages' ); ?></label>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'pmpro_membership_level_after_other_settings', 'pmpromh_pmpro_membership_level_after_other_settings' );

/*
	Save the member homepage.
*/
function pmpromh_pmpro_save_membership_level($level_id)
{
	if(isset($_REQUEST['member_homepage_id']))
		update_option('pmpro_member_homepage_' . $level_id, $_REQUEST['member_homepage_id']);
	if ( isset( $_REQUEST['member_homepage_redirect'] ) ) {
		update_option( 'pmpro_member_homepage_redirect_' . absint( $level_id ), absint( $_REQUEST['member_homepage_redirect'] ) );
	}
	if ( isset( $_REQUEST['member_homepage_ignore_redirect_to'] ) ) {
		update_option( 'pmpro_member_homepage_ignore_redirect_to_' . absint( $level_id ), absint( $_REQUEST['member_homepage_ignore_redirect_to'] ) );
	}
}
add_action("pmpro_save_membership_level", "pmpromh_pmpro_save_membership_level");

/*
	Function to add links to the plugin row meta
*/
function pmpromh_plugin_row_meta($links, $file) {
	if( strpos($file, 'pmpro-member-homepages.php') !== false ) {
		$new_links = array(
			'<a href="' . esc_url('https://www.paidmembershipspro.com/add-ons/member-homepages/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-member-homepages' ) ) . '">' . __( 'Docs', 'pmpro-member-homepages' ) . '</a>',
			'<a href="' . esc_url('https://www.paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-member-homepages' ) ) . '">' . __( 'Support', 'pmpro-member-homepages' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pmpromh_plugin_row_meta', 10, 2);
