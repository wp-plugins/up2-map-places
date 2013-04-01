<?php
/*
Plugin Name: Up2 Map Places
Description: Up2 Map Places allows you to display Google Maps in your content easily.
Version: 1.0
Author: Up2Technology
Author URI: http://www.up2technology.com/
License: GPLv3
*/

if ( ! defined('UP2_PLUGIN_DIR') )
	define('UP2_PLUGIN_DIR', dirname( __FILE__ ));

if ( ! defined('UP2_PLUGIN_URL') )
	define('UP2_PLUGIN_URL', WP_PLUGIN_URL . '/up2_map_places/');

add_action('admin_menu', 'up2_portal_settings');

function up2_portal_settings() {
	add_menu_page('Map Places Settings', 'Map Places Settings', 'administrator', 'up2-map-places-settings', 'up2_map_places_settings');
	
	add_submenu_page('up2-map-places-settings', 'Map Places Form', 'Map Places Form', 'administrator', 'up2-place-form', 'up2_place_form');
	add_submenu_page('up2-map-places-settings', 'Map Direction', 'Map Direction', 'administrator', 'up2-map-direction', 'up2_map_direction');
	add_submenu_page('up2-map-places-settings', 'CSV Upload', 'CSV Upload', 'administrator', 'up2-csv-upload', 'up2_csv_upload');
	
	add_action( 'admin_init', 'up2_register_settings' );
}

function up2_register_settings(){
	register_setting( 'up2_map_places_settings', 'up2_map_places_settings' );
}

function up2_plugin_init() {
  load_plugin_textdomain( 'up2', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'up2_plugin_init' );

require_once UP2_PLUGIN_DIR . '/places_posttype.php';
require_once UP2_PLUGIN_DIR . '/shortcode.php';
require_once UP2_PLUGIN_DIR . '/widget.php';

/*
 * Settings Options 
 */
function up2_map_places_settings() {
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Map Places Shortcode Generator', 'up2'); ?></h2>
		<form method="post" action="">
			
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('Map Type Control', 'up2')?></th>
					<td>
						<label><input type="checkbox" name="MapTypeControl" value="1" checked="checked" onclick="up2Map.typeControl();" /> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('Navigation Control', 'up2')?></th>
					<td>
						<label><input type="checkbox" name="NavigationControl" value="1" checked="checked" onclick="up2Map.navControl();" /> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('Scrollwheel', 'up2')?></th>
					<td>
						<label><input type="checkbox" name="scrollwheel" value="1" onclick="up2Map.scrollWheelControl();" checked="checked"/> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('StreetView Control', 'up2')?></th>
					<td>
						<label><input type="checkbox" name="StreetViewControl" value="1" onclick="up2Map.streetView();" /> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('Clustering', 'up2')?></th>
					<td>
						<label><input type="checkbox" name="cluster" value="1" checked="checked" onclick="up2Map.clustering();" /> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('Map Type', 'up2')?></th>
					<td>
						<select name="MapTypeId" onchange="up2Map.mapType();">
							<option value="terrain"><?php _e('Terrain', 'up2')?></option>
							<option value="satellite"><?php _e('Satellite', 'up2')?></option>
							<option value="roadmap" selected="selected"><?php _e('Roadmap', 'up2')?></option>
							<option value="hybrid"><?php _e('Hybrid', 'up2')?></option>
						</select>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e('Map Type Control Options', 'up2')?></th>
					<td>
						<select name="MapTypeControlStyle" onchange="up2Map.mapControlStyle();">
							<option value="default"><?php _e('Default', 'up2')?></option>
							<option value="drowpdown"><?php _e('Dropdown menu', 'up2')?></option>
						</select>
					</td>
				</tr>
				<tr>	
					<th scope="row"><?php _e('Map Type Control Position', 'up2')?></th>
					<td>
						<select name="MapTypeControlPosition" onchange="up2Map.mapControlPosition();">
							<option value="bc"><?php _e('Positioned in the center of the bottom row', 'up2')?></option>
							<option value="bl"><?php _e('Positioned in the bottom left and flow towards the middle', 'up2')?></option>
							<option value="br"><?php _e('Positioned in the bottom right and flow towards the middle', 'up2')?></option>
							<option value="lb"><?php _e('Positioned on the left, above bottom-left elements, and flow upwards', 'up2')?></option>
							<option value="lc"><?php _e('Positioned in the center of the left side', 'up2')?></option>
							<option value="lt"><?php _e('Positioned on the left, below top-left elements, and flow downwards', 'up2')?></option>
							<option value="rb"><?php _e('Positioned on the right, above bottom-right elements, and flow upwards', 'up2')?></option>
							<option value="rc"><?php _e('Positioned in the center of the right side', 'up2')?></option>
							<option value="rt"><?php _e('Positioned on the right, below top-right elements, and flow downwards', 'up2')?></option>
							<option value="tc"><?php _e('Positioned in the center of the top row', 'up2')?></option>
							<option value="tl"><?php _e('Positioned in the top left and flow towards the middle', 'up2')?></option>
							<option value="tr" selected="selected"><?php _e('Positioned in the top right and flow towards the middle', 'up2')?></option>
						</select>
					</td>
				 </tr>
				 <tr>
				 	<th scope="row"><?php _e('Width (px) / Height (px)', 'up2')?></th>
					<td>
					  <input type="text" name="width" value="600" onblur="up2Map.changeWidth();" /> 
					  <input type="text" name="height" value="600" onblur="up2Map.changeHeight();" />
					</td>
				</tr>
				 <tr>
				 	<th scope="row"><?php _e('Categories', 'up2')?></th>
					<td>
					<?php $taxonomies = get_terms( "up2_map_category", array('hide_empty' => false) );  ?>
						<select name="markerCategories" onchange="up2Map.markerCategories();">
							<option value=""><?php _e('Select category', 'up2');?></option>
							<?php foreach($taxonomies as $v): ?>
							<option value="<?php echo $v->term_id?>"><?php echo $v->name;?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				 <tr>
				 	<th scope="row"><?php _e('Places', 'up2')?></th>
					<td>
					<?php $places = get_posts( array('post_type' => 'map_place', 'numberposts' => -1 ) ); ?>
						<select name="place" onchange="up2Map.markerPlace();">
							<option value=""><?php _e('Select place', 'up2');?></option>
							<?php foreach($places as $post): setup_postdata($post);  ?>
							<option value="<?php echo $post->ID; ?>"><?php echo apply_filters('post_title', $post->post_title); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
				 	<th scope="row"><?php _e('Shortcode', 'up2')?></th>
					<td><?php _e('Copy this code and paste it into your post, page or text widget content.', 'up2')?><br />
						<textarea readonly="readonly" onfocus="this.select();" id="up2-map-places-shortcode" class="widefat" ></textarea>
					</td>
				</tr>
				<tr>
				 	<th scope="row"><?php _e('Map Places View', 'up2')?></th>
					<td>
					   <div id="up2-view-demo-map"></div>
					</td>
				</tr>
				
			</table>
		</form>
	</div>
  <?php
}

/** up2 place help */
function up2_place_form() {
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e("Map Place Form Shortcode Generator", "up2"); ?></h2><br />
	<?php $isSimpleCaptcha = class_exists( 'ReallySimpleCaptcha' ); ?>
	<table class="form-table">
		<?php if( !$isSimpleCaptcha ):?>
	    <tr>
	    	<td colspan="2"><span style="color: #FF0000;"><?php _e('Note: To use CAPTCHA, you need Really Simple CAPTCHA plugin installed.', 'up2')?></span></td>
	    </tr>
		<?php endif;?>
		<tr>
			<th scope="row"><?php _e('Use captcha', 'up2')?></th>
			<td>
				<label><input type="checkbox" name="up2_captcha" value="1" <?php if( !$isSimpleCaptcha ) : ?> disabled="disabled"<?php endif; ?> onclick="up2MapForm.setCaptcha();" /> <?php _e('Yes', 'up2')?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e('For register users', 'up2')?></th>
			<td>
				<label><input type="checkbox" name="up2_registerusers" value="1" onclick="up2MapForm.setRegisterUsers();" /> <?php _e('Yes', 'up2')?></label>
			</td>
		 </tr>
		<tr>
		 	<th scope="row"><?php _e('Shortcode', 'up2')?></th>
			<td><?php _e('Copy this code and paste it into your post, page or text widget content.', 'up2')?><br />
				<textarea readonly="readonly" onfocus="this.select();" id="up2-map-places-form-shortcode" class="widefat" ></textarea>
			</td>
		</tr>
	</table>
	
	<h3><?php _e('Screenshot', 'up2')?></h3>
	<img src="<?php echo UP2_PLUGIN_URL; ?>images/form_map_place_frontend.png" style="border: 1px solid #CCCCCC;" alt="<?php _e('new place form', 'up2');?>" title="<?php _e('new place form', 'up2');?>" />
</div>
<?php 	
}

function up2_map_direction() {
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e("Map Direction Shortcode Generator", "up2"); ?></h2><br />
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Travel Mode', 'up2')?></th>
			<td>
				<select name="travelMode" onchange="up2MapDirection.setTravelMode();">
					<option value="driving" selected="selected"><?php _e('Driving', 'up2')?></option>
					<option value="walking"><?php _e('Walking', 'up2')?></option>
				</select>
			</td>
		 </tr>
		 <tr>
		 	<th scope="row"><?php _e('Width (px) / Height (px)', 'up2')?></th>
			<td>
			  <input type="text" name="width" value="600" onblur="up2MapDirection.changeWidth();" /> 
			  <input type="text" name="height" value="600" onblur="up2MapDirection.changeHeight();" />
			</td>
		</tr>
	   <tr>
	 	 <th scope="row"><?php _e('Place', 'up2')?></th>
		 <td>
		<?php $places = get_posts( array('post_type' => 'map_place', 'numberposts' => -1 ) ); ?>
			<select name="place" onchange="up2MapDirection.markerPlace();">
				<?php foreach($places as $post): setup_postdata($post);  ?>
				<option value="<?php echo $post->ID; ?>"><?php echo apply_filters('post_title', $post->post_title); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		</tr>
		<tr>
		 	<th scope="row"><?php _e('Shortcode', 'up2')?></th>
			<td><?php _e('Copy this code and paste it into your post, page or text widget content.', 'up2')?><br />
				<textarea readonly="readonly" onfocus="this.select();" id="up2-map-direction-shortcode" class="widefat" ></textarea>
			</td>
		</tr>
		<tr>
		 	<th scope="row"><?php _e('Map Direction View', 'up2')?></th>
			<td>
			   <div id="up2-view-demo-map-direction"></div>
			</td>
		</tr>
	</table>
</div>
<?php 	
}

/** CSV Upload*/
function up2_csv_upload() {

	/* upload csv file*/
	if( isset($_POST['upload']) ) {
		up2_upload_csv();
	}
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e("Places - CSV Import", "up2"); ?></h2>
		<form method="post" action="" enctype="multipart/form-data">
			<table class="">
			    <tr>
					<td colspan="2"><h3><?php _e("CSV Headers:", "up2"); ?></h3>
						
						<?php _e("Basic columns:", "up2"); ?> <code>category;name;address;content</code> 
						
						<div><?php _e("For find location and put on map the plugin use Geocoding", "up2"); ?><br />
						<code><strong><?php _e("Usage Limits", "up2"); ?></strong><br />
							<span style="color: #f00;"><?php _e("Use of the Google Geocoding API is subject to a query limit of 2,500 geolocation requests per day", "up2"); ?>
							<a href="https://developers.google.com/maps/documentation/geocoding/" target="_blank">Geocoding API</a></code>.
							</span>
						</div><br /><br />
						
						<span style="color: #f00;"><?php _e('Recomended to use advance columns options.', 'up2')?></span><br />
						<?php _e("Advance columns:", "up2"); ?> <code>category;name;address;content;lat;lng</code><br /><br />
						
						* <em><?php _e("<strong>category</strong> field is nummeric", "up2"); ?></em>&nbsp;&nbsp; <a href="edit-tags.php?taxonomy=up2_map_category&post_type=map_place"><?php _e("View Map categories", "up2"); ?></a><br />
						* <em><strong>lat</strong> <?php _e("Latitude - example:", "up2"); ?> 52.37</em><br />
						* <em><strong>lng</strong> <?php _e("Longitude - example:", "up2"); ?> 4.89</em><br />
					</td>
				</tr>
				<tr>
					<td><h3><?php _e("CSV File:", "up2"); ?></h3><input type="file" name="map_places" /> <input type="submit" class="button-primary" name="upload" value="<?php _e( 'Upload and Save', 'up2' ); ?>" /></td>
				</tr>
			</table>
		</form>
		</div>
	</div>
<?php 	
}

function up2_upload_csv() {

	require_once 'DataSource.php';

	$csv = new File_CSV_DataSource;

	$settings = array(
	   'delimiter' => ';',
	   'eol' => ";",
	   'length' => 999999,
	   'escape' => '"'
	);
	$csv->settings($settings);

	$file = $_FILES['map_places'];
	$isFile = is_uploaded_file($file['tmp_name']);

	if ( $isFile && $csv->load($file['tmp_name']) ) {
		$csvData = $csv->connect();
	
		$headers = $csv->countHeaders();
		
   		$res = up2_save_places($csvData, $headers);
		echo '<div class="updated"><p><strong>' . __( 'New data imported.', 'up2' ) . '</strong></p></div>';
	} else {
		echo '<div class="updated"><p><strong>' . __( 'Invalid CSV file.', 'up2' ) . '</strong></p></div>';
	}
}

function up2_save_places($csvData, $headers) {

	if ( ! is_array($csvData) ) return;

	foreach ( $csvData as $v ) {
		
		// Create post object
		$my_post = array(
			'post_title'    => $v['name'],
			'post_content'  => $v['content'],
			'post_status'   => 'publish',
			'post_type'   => 'map_place',
		);
		
		$post_id = wp_insert_post( $my_post );
		
		if($post_id) {
			
			$metaData['address'] = $v['address'];
			
			if( $headers == 6 ) {
				$metaData['lat'] = $v['lat'];
				$metaData['lng'] = $v['lng'];
			}
			
			$metaData['map-icon'] = '';
			
			update_post_meta( $post_id, 'up2_paces_fields', $metaData );
			
			if( $v['category'] != 0 ) {
				wp_set_post_terms( $post_id, $v['category'], "up2_map_category" );
			}

		}
	}

}