<?php
/**
 * Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

function up2_delete_plugin() {
	global $wpdb;

	// Delete options
	delete_option( 'up2_map_places_version' );

	// Delete terms
	$terms = get_terms( array( 'up2_map_category' ), array( 'hide_empty' => false, 'fields' => 'ids' ) );

	if ( $terms ) {
		foreach ( $terms as $term ) {
			wp_delete_term( $term, 'up2_map_category' );
		}
	}

	// Delete posts
	$posts = get_posts( array(
			'numberposts' => -1,
			'post_type' => 'map_place',
			'post_status' => 'any'
		)
	);

	if ( $posts ) {
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}
}

up2_delete_plugin();