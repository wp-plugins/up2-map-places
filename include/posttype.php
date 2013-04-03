<?php

/** Map Places Post Type */

function up2_custom_postype() {
	$labels = array(
		'name' => __( 'Map Places', 'up2' ),
		'singular_name' => __( 'Map Place', 'up2' ),
		'add_new' => __( 'Add New', 'up2' ),
		'add_new_item' => __( 'Add New Map Place', 'up2' ),
		'edit_item' => __( 'Edit Map Place', 'up2' ),
		'new_item' => __( 'New Map Place', 'up2' ),
		'view_item' => __( 'View Map Place', 'up2' ),
		'search_items' => __( 'Search Map Places', 'up2' ),
		'not_found' => __( 'No Map Places found', 'up2' ),
		'not_found_in_trash' => __( 'No Map Places found in Trash', 'up2' ),
		'parent_item_colon' => __( 'Parent Map Place:', 'up2' ),
		'menu_name' => __( 'Map Places', 'up2' )
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'menu_position' => 5,
		'show_in_nav_menus' => false,
		'exclude_from_search' => true,
		'rewrite' => false,
		'taxonomies' => array( 'up2_map_category' ),
		'register_meta_box_cb' => 'up2_places_meta'
	);

	register_post_type( 'map_place', $args );
}
add_action( 'init', 'up2_custom_postype' );

/** Map Places Taxonomies */

function up2_map_taxonomies() {
	$labels = array(
		'name' => __( 'Map Categories', 'up2' ),
		'singular_name' => __( 'Map Category', 'up2' ),
		'search_items' => __( 'Search Map Categories', 'up2' ),
		'popular_items' => __( 'Popular Map Categories', 'up2' ),
		'all_items' => __( 'All Map Categories', 'up2' ),
		'parent_item' => __( 'Parent Map Category', 'up2' ),
		'parent_item_colon' => __( 'Parent Map Category:', 'up2' ),
		'edit_item' => __( 'Edit Map Category', 'up2' ),
		'update_item' => __( 'Update Map Category', 'up2' ),
		'add_new_item' => __( 'Add New Map Category', 'up2' ),
		'new_item_name' => __( 'New MAp Category Name', 'up2' ),
		'menu_name' => __( 'Map Categories', 'up2' )
	);

	register_taxonomy( 'up2_map_category', array( 'map_place' ), array(
		'labels' => $labels,
		'hierarchical' => true,
		'show_in_nav_menus' => true,
		'rewrite' => array(
			'slug' => 'up2_map_category',
			'hierarchical' => true
		)
	));
}
add_action( 'init', 'up2_map_taxonomies', 0 );

/** Map Places Metaboxes */

function up2_places_meta() {
	add_meta_box( 'up2_map_place_data', __( 'Map Place Settings', 'up2' ), 'up2_map_place_data', 'map_place', 'normal', 'high' );
}

function up2_map_place_data( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'up2_map_place_data' );

	$icons = array( 'marker', 'marker_black', 'marker_grey', 'marker_orange', 'marker_white', 'marker_yellow', 'marker_purple', 'marker_green' );
	$place_meta = get_post_meta( $post->ID, '_up2_map_place_data', true );
	$markerUrl = 'http://maps.google.com/mapfiles/';
	$markerDefault = $markerUrl . 'marker.png';
	$markerIcon = ( isset( $place_meta['map-icon'] ) && $place_meta['map-icon'] != '' ) ? esc_attr( $place_meta['map-icon'] ) : $markerDefault;
	$markerAddress = ( isset( $place_meta['address'] ) ) ? esc_attr( $place_meta['address'] ) : '';
	$markerLat = ( isset( $place_meta['lat'] ) ) ? esc_attr( $place_meta['lat'] ) : '';
	$markerLng = ( isset( $place_meta['lng'] ) ) ? esc_attr( $place_meta['lng'] ) : '';
	$isDefaultMarker = ( preg_match( "#google.com#", $markerIcon ) ) ? true : false;
	$markerClass = ( $isDefaultMarker ) ? 'isDefaultMarker' : 'marker-icon-active';
	$filename = preg_replace('/\.[^.]*$/', '', basename( $markerIcon ) );
?>
	<p class="description">
		<?php _e( 'Address', 'up2' ); ?>
	</p>
	<p>
		<input type="text" id="address" name="up2_places[address]" value="<?php echo $markerAddress; ?>" style="width: 50%" />
		<input type="button" id="find-address" name="find" class="button-primary" value="<?php _e( 'Find Address', 'up2' ); ?>" />
	</p>
	<p class="description">
		<?php _e( 'Default Marker Styles', 'up2' ); ?>
	</p>
	<p>
		<?php foreach ( $icons as $v ) : ?>
		<img src="<?php echo $markerUrl . $v; ?>.png" class="marker-icons<?php if ( $filename == $v ) echo ' marker-icon-active'; ?>" alt="marker" />
		<?php endforeach; ?>
	</p>
	<p class="description">
		<?php _e( 'Custom Marker Style', 'up2' ); ?>
	</p>
	<p>
		<img src="<?php echo $markerIcon; ?>" id="map-icon-view" class="marker-icons <?php echo $markerClass; ?>" alt="marker" />
	</p>
	<p>
		<input type="hidden" id="up2-custom-icon" name="up2_places[map-icon]" value="<?php echo $markerIcon; ?>" /> 
		<input id="up2-custom-icon-button" name="up2-custom-icon-button" type="button" value="<?php _e( 'Select Custom Marker', 'up2' ); ?>" class="button up2-uploader-button" />
	</p>
	<div id="up2-map"></div>
	<p>
		<input type="hidden" id="lat" name="up2_places[lat]" value="<?php echo $markerLat; ?>" class="widefat" />
		<input type="hidden" id="lng" name="up2_places[lng]" value="<?php echo $markerLng; ?>" class="widefat" />
	</p>
	<p>
		<input type="submit" name="submit" value="<?php _e( 'Save Marker', 'up2' ); ?>" class="button-primary" />&nbsp;&nbsp;
		<span class="place-modified"><?php _e( 'Settings has been changed. Use "Save" button to update.', 'up2' ); ?></span>
	</p>
<?php 
}

function up2_map_place_data_save( $post_id ) {
	if ( ! up2_meta_box_verify( 'up2_map_place_data', 'upload_files' ) )
		return;

	if ( isset( $_POST['up2_places'] ) && ! empty( $_POST['up2_places'] ) )
		update_post_meta( $post_id, '_up2_map_place_data', $_POST['up2_places'] );
}
add_action( 'save_post', 'up2_map_place_data_save' );

/** Admin Scripts and Styles */

function up2_js_css( $hook ) {
	global $typenow;

	if ( $typenow == 'map_place' || $typenow == '' ) :
 
?>
<link rel="stylesheet" href="<?php echo UP2_PLUGIN_URL; ?>css/up2-style.css" type="text/css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=en"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/up2-admin.js"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/up2-uploader.js"></script>
<script type="text/javascript">
	var view_demo_ajax_nonce = '<?php echo wp_create_nonce( 'show_map' ); ?>';

	jQuery(function(){
		//set default shortcode settings
		switch(pagenow) {
			case 'toplevel_page_up2-map-places-settings':
				up2Map.shortcode();
				break;
			case 'map-places-settings_page_up2-place-form':
				up2MapForm.shortcode();
				break;
			case 'map-places-settings_page_up2-map-direction':
				up2MapDirection.shortcode();
				break;
		}
	});
</script>
<?php 
endif;
}
add_action( 'admin_head-post.php', 'up2_js_css' );
add_action( 'admin_head-toplevel_page_up2-map-places-settings', 'up2_js_css' );
add_action( 'admin_head-map-places-settings_page_up2-place-form', 'up2_js_css' );
add_action( 'admin_head-map-places-settings_page_up2-map-direction', 'up2_js_css' );

/** Show Map Callback */

function up2_show_map_callback() {
	check_ajax_referer( 'show_map' );

	$shortcode = stripslashes( $_POST['shortcode'] );

	echo do_shortcode( $shortcode );
	exit;
}
add_action( 'wp_ajax_show_map', 'up2_show_map_callback' );

/** Meta Box Verification */

if ( ! function_exists( 'up2_meta_box_verify' ) ) :

function up2_meta_box_verify( $nonce = '', $capability = 'edit_post' ) {
	if ( ! $nonce )
		return false;

	$nonce = ( isset($_POST[$nonce]) ) ? $_POST[$nonce] : 0;

	if ( ! wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) )
		return false;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return false;

	if ( ! current_user_can( $capability ) )
		return false;

	return true;
}

endif;