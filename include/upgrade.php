<?php
/**
 * Upgrade
 */

function up2_upgrade() {
	$version = get_option( 'up2_map_places_version', 0 );

	if ( $version == UP2_PLUGIN_VERSION )
		return;

	$upgrade = 'up2_upgrade_' . str_replace( '.', '', UP2_PLUGIN_VERSION );
	$upgraded = call_user_func( $upgrade );

	if ( $upgraded )
		update_option( 'up2_map_places_version', UP2_PLUGIN_VERSION );
}
add_action( 'admin_init', 'up2_upgrade' );

/**
 * Version 1.2
 */

function up2_upgrade_12() {
	global $wpdb;

	$upgraded = $wpdb->update( $wpdb->postmeta, array( 'meta_key' => '_up2_map_place_data' ), array( 'meta_key' => 'up2_paces_fields' ), array( '%s' ), array( '%s' ) );

	return $upgraded;
}