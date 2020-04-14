<?php
/**
Plugin Name: Paid Memberships Pro - Member Homepages Add On
Plugin URI: http://www.paidmembershipspro.com/pmpro-member-homepages/
Description: Redirect members to a unique homepage/landing page based on their level.
Version: .2
Author: Stranger Studios
Author URI: https://paidmembershipspro.com

@package PMPro
 */

define( 'PMPRO_MEMBER_HOMEPAGES_VERSION', '.2' );

/**
 * Redirect member on login to their membership level's homepage
 *
 * @param string           $redirect_to The redirect destination URL.
 * @param string           $request     The requested redirect destination URL passed as a parameter.
 * @param WP_User|WP_Error $user        User object if logged in, WP_Error if not.
 *
 * @return string New redirect URL.
 */
function pmpromh_login_redirect( $redirect_to, $request, $user ) {
	// check level.
	if ( ! empty( $user ) && ! empty( $user->ID ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$level              = pmpro_getMembershipLevelForUser( $user->ID );
		$member_homepage_id = pmpromh_getHomepageForLevel( $level->id );

		if ( ! empty( $member_homepage_id ) ) {
			$redirect_to = get_permalink( $member_homepage_id );
		}
	}

	return $redirect_to;
}
add_filter( 'login_redirect', 'pmpromh_login_redirect', 10, 3 );

/**
 * Function to redirect member to their membership level's homepage when
 * trying to access your site's front page (static page or posts page).
 */
function pmpromh_template_redirect_homepage() {
	global $current_user;
	// is there a user to check?
	if ( ! empty( $current_user->ID ) && is_front_page() ) {
		$member_homepage_id = pmpromh_getHomepageForLevel();
		if ( ! empty( $member_homepage_id ) && ! is_page( $member_homepage_id ) ) {
			wp_redirect( get_permalink( $member_homepage_id ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'pmpromh_template_redirect_homepage' );

/**
 * Function to get a homepage for level.
 *
 * @param int|null $level_id The level ID for the user.
 *
 * @return int The membership homepage ID.
 */
function pmpromh_getHomepageForLevel( $level_id = null ) {
	if ( empty( $level_id ) && function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		global $current_user;
		$level = pmpro_getMembershipLevelForUser( $current_user->ID );
		if ( ! empty( $level ) ) {
			$level_id = $level->id;
		}
	}

	// look up by level.
	if ( ! empty( $level_id ) ) {
		$member_homepage_id = get_option( 'pmpro_member_homepage_' . $level_id );
	} else {
		$member_homepage_id = false;
	}

	return $member_homepage_id;
}

/**
 * Membership Settings.
 */
function pmpromh_pmpro_membership_level_after_other_settings() {
	?>
	<table>
		<tbody class="form-table">
			<tr>
				<td>
					<tr>
						<th scope="row" valign="top"><label for="member_homepage"><?php esc_html_e( 'Member Homepage', 'pmpromh' ); ?>:</label></th>
						<td>
							<?php
								$level_id           = intval( $_REQUEST['edit'] );
								$member_homepage_id = pmpromh_getHomepageForLevel( $level_id );
							?>
							<?php
							wp_dropdown_pages(
								array(
									'name'             => 'member_homepage_id',
									'show_option_none' => '-- ' . esc_html__( 'Choose One', 'pmpro' ) . ' --',
									'selected'         => absint( $member_homepage_id ),
								)
							);
							?>
						</td>
					</tr>
				</td>
			</tr> 
		</tbody>
	</table>
	<?php
}
add_action( 'pmpro_membership_level_after_other_settings', 'pmpromh_pmpro_membership_level_after_other_settings' );

/**
 * Save the member homepage.
 *
 * @param int $level_id The level ID to save the membershpi level for.
 */
function pmpromh_pmpro_save_membership_level( $level_id ) {
	if ( isset( $_REQUEST['member_homepage_id'] ) ) {
		update_option( 'pmpro_member_homepage_' . absint( $level_id ), absint( $_REQUEST['member_homepage_id'] ) );
	}
}
add_action( 'pmpro_save_membership_level', 'pmpromh_pmpro_save_membership_level' );

/**
 * Function to add links to the plugin row meta.
 *
 * @param array  $links Plugin row links.
 * @param string $file The filename to check.
 *
 * @return array Updated links array.
 */
function pmpromh_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-member-homepages.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url( 'http://www.paidmembershipspro.com/add-ons/plus-add-ons/member-homepages/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro' ) . '</a>',
			'<a href="' . esc_url( 'http://paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro' ) . '</a>',
		);
		$links     = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmpromh_plugin_row_meta', 10, 2 );
