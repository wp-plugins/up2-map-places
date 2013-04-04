<?php
/**
 * Upgrade
 */

function up2_upgrade() {
	// Get current version
	$version = get_option( 'up2_map_places_version', 0 );

	// Bail if up to date
	if ( $version == UP2_PLUGIN_VERSION )
		return;

	// Upgrade version 1.3
	up2_upgrade_13( $version );

	// Update version in database
	update_option( 'up2_map_places_version', UP2_PLUGIN_VERSION );
}
add_action( 'admin_init', 'up2_upgrade' );

/**
 * Version 1.3
 */

function up2_upgrade_13( $version ) {
	if ( '1.3' <= $version )
		return;

	global $wpdb;

	$wpdb->update( $wpdb->postmeta, array( 'meta_key' => '_up2_map_place_data' ), array( 'meta_key' => 'up2_paces_fields' ), array( '%s' ), array( '%s' ) );
}