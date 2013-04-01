<?php
add_action( 'init', 'up2_custom_postype' );

$up2_post_type_labels = array(
	'name' => _x( 'Places', 'map_place' ),
	'singular_name' => _x( 'Place', 'map_place' ),
	'add_new' => _x( 'Add New', 'map_place' ),
	'add_new_item' => _x( 'Add New Place', 'map_place' ),
	'edit_item' => _x( 'Edit Place', 'map_place' ),
	'new_item' => _x( 'New Place', 'map_place' ),
	'view_item' => _x( 'View Place', 'map_place' ),
	'search_items' => _x( 'Search Places', 'map_place' ),
	'not_found' => _x( 'No places found', 'map_place' ),
	'not_found_in_trash' => _x( 'No places found in Trash', 'map_place' ),
	'parent_item_colon' => _x( 'Parent Place:', 'map_place' ),
	'menu_name' => _x( 'Map Places', 'map_place' ),
);

function up2_custom_postype() {
	global $up2_post_type_labels;

	$args = array(
		'labels' => $up2_post_type_labels,
		'hierarchical' => false,
		'supports' => array( 'title', 'editor' ),
		'public' => true,
		'show_in_menu' => true,
		'menu_position' => 10,
		'show_in_nav_menus' => false,
		'exclude_from_search' => true,
		'rewrite' => false,
		'register_meta_box_cb' => 'up2_places_meta'
	);

	register_post_type( 'map_place', $args );
}

add_action( 'init', 'up2_map_taxonomies', 0 );

function up2_map_taxonomies() {

	/** Category */
	$labels = array(
		'name' => 'Categories',
		'singular_name' => 'Category',
		'search_items' => 'Search Categories',
		'popular_items' => 'Popular Categories',
		'all_items' => 'All Categories',
		'parent_item' => 'Parent Category',
		'parent_item_colon' => 'Parent Category:',
		'edit_item' => 'Edit Category',
		'update_item' => 'Update Category',
		'add_new_item' => 'Add New Category',
		'new_item_name' => 'New Category Name',
		'menu_name' => 'Categories'
	);
	register_taxonomy( 'up2_map_category', array( 'map_place' ), array(
		'labels' => $labels,
		'hierarchical' => true,
		'show_in_nav_menus' => true,
		'rewrite' => array( 'slug' => 'up2_map_category', 'hierarchical' => true )
	));
}

/** Map Places metaboxes */

function up2_places_meta() {
	add_meta_box( 'up2_paces_fields', 'Map Places Options', 'up2_paces_fields', 'map_place', 'normal', 'high' );
}

function up2_paces_fields( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'up2_paces_fields' );

	$place_meta = get_post_meta( $post->ID, 'up2_paces_fields', true );
	
	$markerDefault = "http://maps.google.com/mapfiles/marker";
	
	$icons = array('marker', 'marker_black', 'marker_grey', 'marker_orange', 'marker_white', 'marker_yellow', 'marker_purple', 'marker_green');
	
	$markerIcon = ( isset($place_meta['map-icon']) && $place_meta['map-icon'] != '' ) ? $place_meta['map-icon'] : $markerDefault. '.png';
	
	$isDefautlMakrer = ( preg_match("#google.com#", $markerIcon) ) ? true : false;

	$filename = preg_replace('/\.[^.]*$/', '', basename($markerIcon) );
?>
	<p><label>Address: </label>
		<input type="text" id="address" name="up2_paces[address]" value="<?php if( isset($place_meta['address']) ) echo esc_attr($place_meta['address']); ?>" style="width:60%" />
		<input type="submit" name="find" class="button-primary" value="<?php _e("Find", "up2");?>" />
	</p>
	<p><label><?php _e('Icon style', 'up2')?>: </label>
		<input type="text" id="up2-custom-icon" name="up2_paces[map-icon]" style="display: none;" value="<?php echo $place_meta['map-icon']; ?>" class="regular-text" /> 
		<input id="up2-custom-icon-button" name="up2-custom-icon-button" type="button" value="Select" class="button up2-uploader-button" />
		<img src="<?php echo $markerIcon; ?>" id="map-icon-view" class="marker-icons <?php if($isDefautlMakrer): ?>isDefaultMarker<?php else: ?>marker-icon-active<?php endif;?>" />
		<?php foreach($icons as $v):?>
		<img src="http://maps.google.com/mapfiles/<?php echo $v; ?>.png" class="marker-icons <?php if( $filename == $v): ?>marker-icon-active<?php endif;?>" />
		<?php endforeach;?>
	</p>
	<div id="my_map"></div>
	<p>
	 <input type="hidden" id="lat" name="up2_paces[lat]" value="<?php if( isset($place_meta['lat']) ) echo $place_meta['lat']; ?>" class="widefat" />
	 <input type="hidden" id="lng" name="up2_paces[lng]" value="<?php if( isset($place_meta['lat']) ) echo $place_meta['lng']; ?>" class="widefat" />
	</p>
	<p><input type="submit" name="submit" value="<?php _e("Save marker", "up2") ?>" class="button-primary" /></p>
<?php 
}

function up2_paces_fields_save( $post_id ) {
	if ( ! up2_meta_box_verify( 'up2_paces_fields' ) )
		return;

	if( isset($_POST['up2_paces']) ) 
		update_post_meta( $post_id, 'up2_paces_fields', $_POST['up2_paces'] );
// 	else
// 		delete_post_meta( $post_id, 'up2_paces_fields' );
}
add_action( 'save_post', 'up2_paces_fields_save' );

add_action('admin_head-post.php', 'up2_js_css');
add_action('admin_head-toplevel_page_up2-map-places-settings', 'up2_js_css');
add_action('admin_head-map-places-settings_page_up2-place-form', 'up2_js_css');
add_action('admin_head-map-places-settings_page_up2-map-direction', 'up2_js_css');

function up2_js_css($hook) {
 global $typenow;
 
 if( $typenow == 'map_place' || $typenow == '' ) :
 
?>
<link rel="stylesheet" href="<?php echo UP2_PLUGIN_URL; ?>places.css" type="text/css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=en"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/map_places_backend.js"></script>
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
$screen = get_current_screen();
if( $screen != null && $screen->post_type == "map_place" && isset($_GET['action']) && $_GET['action'] == 'edit' ) : ?>
	<script type="text/javascript">
	jQuery(function($) {
		//center new marker ponint
		setTimeout(function(){
			var marker = jQuery("#my_map").gmap3({get:"marker"});
			var map = jQuery("#my_map").gmap3("get");
	
			if( map != undefined && marker != undefined )
				map.panTo(marker.getPosition());
	
		}, 300);
	});
</script>
<?php endif;
}

add_action( 'wp_ajax_show_map', 'up2_show_map_callback' ); // for not-logged in users

function up2_show_map_callback() {
	check_ajax_referer( 'show_map' );
	
	$shortcode = stripslashes($_POST['shortcode']);
	
// 	echo $shortcode;
	echo do_shortcode($shortcode);
	exit;
}


/** Meta Box Verification */
if ( ! function_exists( 'up2_meta_box_verify' ) ) :

function up2_meta_box_verify( $nonce = '', $capability = 'edit_post' ) {
	if ( ! $nonce )
		return false;

	$nonce = ( isset($_POST[$nonce]) ) ? $_POST[$nonce] : 0;

	if ( ! wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) )
		return false;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return false;

	if ( ! current_user_can( $capability ) )
		return false;

	return true;
}

endif;

function up2_right_now_content_table_end() {
	
	global $up2_post_type_labels;
	
	$post_type = "map_place";
	$post_type_taxonomies = "up2_map_category";
	
	$num_posts = wp_count_posts( $post_type );
	$num = number_format_i18n( $num_posts->publish );
	$text = _n( $up2_post_type_labels['singular_name'], $up2_post_type_labels['name'] , intval( $num_posts->publish ) );
	if ( current_user_can( 'edit_posts' ) ) {
		$num = "<a href='edit.php?post_type={$post_type}'>{$num}</a>";
		$text = "<a href='edit.php?post_type={$post_type}'>{$text}</a>";
	}
	echo '<tr><td class="first b b-' . $post_type . '">' . $num . '</td>';
	echo '<td class="t ' . $post_type . '">' . $text . '</td></tr>';

}
add_action( 'right_now_content_table_end' , 'up2_right_now_content_table_end' );