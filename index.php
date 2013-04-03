<?php
/*
Plugin Name: Up2 Map Places
Description: Up2 Map Places allows you to display Google Maps in your content easily.
Version: 1.2
Author: Up2Technology
Author URI: http://www.up2technology.com/
License: GPLv3
*/

/** Constants */

if ( ! defined( 'UP2_PLUGIN_VERSION' ) )
	define( 'UP2_PLUGIN_VERSION', '1.2' );

if ( ! defined( 'UP2_PLUGIN_DIR' ) )
	define( 'UP2_PLUGIN_DIR', trailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'UP2_PLUGIN_URL' ) )
	define( 'UP2_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );

if ( ! defined( 'UP2_INCLUDE_DIR' ) )
	define( 'UP2_INCLUDE_DIR', trailingslashit( UP2_PLUGIN_DIR . 'include' ) );

if ( ! defined( 'UP2_LANGUAGES_DIR' ) )
	define( 'UP2_LANGUAGES_DIR', trailingslashit( UP2_PLUGIN_DIR . 'languages' ) );

/** Upgrade */

require_once UP2_INCLUDE_DIR . 'upgrade.php';

/** Pages */

function up2_portal_settings() {
	add_menu_page( __( 'Map Places Settings', 'up2' ), __( 'Map Places Settings', 'up2' ), 'manage_options', 'up2-map-places-settings', 'up2_map_places_settings' );

	add_submenu_page( 'up2-map-places-settings', __( 'Map Places Form', 'up2' ), __( 'Map Places Form', 'up2' ), 'manage_options', 'up2-place-form', 'up2_place_form' );
	add_submenu_page( 'up2-map-places-settings', __( 'Map Direction', 'up2' ), __( 'Map Direction', 'up2' ), 'manage_options', 'up2-map-direction', 'up2_map_direction' );
	add_submenu_page( 'up2-map-places-settings', __( 'CSV Upload', 'up2' ), __( 'CSV Upload', 'up2' ), 'manage_options', 'up2-csv-upload', 'up2_csv_upload' );

	add_action( 'admin_init', 'up2_register_settings' );
}
add_action( 'admin_menu', 'up2_portal_settings' );

/** Settings */

function up2_register_settings() {
	register_setting( 'up2_map_places_settings', 'up2_map_places_settings' );
}

/** Localization */

function up2_plugin_init() {
  load_plugin_textdomain( 'up2', false, UP2_LANGUAGES_DIR );
}
add_action( 'plugins_loaded', 'up2_plugin_init' );

require_once UP2_INCLUDE_DIR . 'posttype.php';
require_once UP2_INCLUDE_DIR . 'shortcode.php';
require_once UP2_INCLUDE_DIR . 'widget.php';

/** Settings Options */

function up2_map_places_settings() {
?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e( 'Map Places Shortcode Generator', 'up2' ); ?></h2>
		<form method="post" action="">
			
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Map Type Control', 'up2' ); ?></th>
					<td>
						<label><input type="checkbox" name="MapTypeControl" value="1" checked="checked" onclick="up2Map.typeControl();" /> <?php _e( 'Yes', 'up2' ); ?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'Navigation Control', 'up2' ); ?></th>
					<td>
						<label><input type="checkbox" name="NavigationControl" value="1" checked="checked" onclick="up2Map.navControl();" /> <?php _e( 'Yes', 'up2' ); ?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'Scrollwheel', 'up2' ); ?></th>
					<td>
						<label><input type="checkbox" name="scrollwheel" value="1" onclick="up2Map.scrollWheelControl();" checked="checked"/> <?php _e('Yes', 'up2')?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'StreetView Control', 'up2' ); ?></th>
					<td>
						<label><input type="checkbox" name="StreetViewControl" value="1" onclick="up2Map.streetView();" /> <?php _e( 'Yes', 'up2' ); ?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'Clustering', 'up2' ); ?></th>
					<td>
						<label><input type="checkbox" name="cluster" value="1" checked="checked" onclick="up2Map.clustering();" /> <?php _e( 'Yes', 'up2' ); ?></label>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'Map Type', 'up2' ); ?></th>
					<td>
						<select name="MapTypeId" onchange="up2Map.mapType();">
							<option value="terrain"><?php _e( 'Terrain', 'up2' ); ?></option>
							<option value="satellite"><?php _e( 'Satellite', 'up2' ); ?></option>
							<option value="roadmap" selected="selected"><?php _e( 'Roadmap', 'up2' ); ?></option>
							<option value="hybrid"><?php _e( 'Hybrid', 'up2' ); ?></option>
						</select>
					</td>
				 </tr>
				<tr>
					<th scope="row"><?php _e( 'Map Type Control Options', 'up2' ); ?></th>
					<td>
						<select name="MapTypeControlStyle" onchange="up2Map.mapControlStyle();">
							<option value="default"><?php _e( 'Default', 'up2' ); ?></option>
							<option value="drowpdown"><?php _e( 'Dropdown Menu', 'up2' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>	
					<th scope="row"><?php _e( 'Map Type Control Position', 'up2' ); ?></th>
					<td>
						<select name="MapTypeControlPosition" onchange="up2Map.mapControlPosition();">
							<option value="bc"><?php _e( 'Positioned in the center of the bottom row', 'up2' ); ?></option>
							<option value="bl"><?php _e( 'Positioned in the bottom left and flow towards the middle', 'up2' ); ?></option>
							<option value="br"><?php _e( 'Positioned in the bottom right and flow towards the middle', 'up2' ); ?></option>
							<option value="lb"><?php _e( 'Positioned on the left, above bottom-left elements, and flow upwards', 'up2' ); ?></option>
							<option value="lc"><?php _e( 'Positioned in the center of the left side', 'up2' ); ?></option>
							<option value="lt"><?php _e( 'Positioned on the left, below top-left elements, and flow downwards', 'up2' ); ?></option>
							<option value="rb"><?php _e( 'Positioned on the right, above bottom-right elements, and flow upwards', 'up2' ); ?></option>
							<option value="rc"><?php _e( 'Positioned in the center of the right side', 'up2' ); ?></option>
							<option value="rt"><?php _e( 'Positioned on the right, below top-right elements, and flow downwards', 'up2' ); ?></option>
							<option value="tc"><?php _e( 'Positioned in the center of the top row', 'up2' ); ?></option>
							<option value="tl"><?php _e( 'Positioned in the top left and flow towards the middle', 'up2' ); ?></option>
							<option value="tr" selected="selected"><?php _e( 'Positioned in the top right and flow towards the middle', 'up2' ); ?></option>
						</select>
					</td>
				 </tr>
				 <tr>
				 	<th scope="row"><?php _e( 'Width/Height (in pixels)', 'up2' ); ?></th>
					<td>
					  <input type="text" name="width" value="600" onblur="up2Map.changeWidth();" /> 
					  <input type="text" name="height" value="600" onblur="up2Map.changeHeight();" />
					</td>
				</tr>
				 <tr>
				 	<th scope="row"><?php _e( 'Map Categories', 'up2' ); ?></th>
					<td>
					<?php $taxonomies = get_terms( 'up2_map_category', array( 'hide_empty' => false ) );  ?>
						<select name="markerCategories" onchange="up2Map.markerCategories();">
							<option value=""><?php _e( 'Select Map Category...', 'up2' ); ?></option>
							<?php foreach( $taxonomies as $v ) : ?>
							<option value="<?php echo $v->term_id; ?>"><?php echo $v->name; ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				 <tr>
				 	<th scope="row"><?php _e( 'Map Places', 'up2' ); ?></th>
					<td>
					<?php $places = get_posts( array( 'post_type' => 'map_place', 'numberposts' => -1 ) ); ?>
						<select name="place" onchange="up2Map.markerPlace();">
							<option value=""><?php _e( 'Select Place...', 'up2' ); ?></option>
							<?php foreach ( $places as $place ) : ?>
							<option value="<?php echo $place->ID; ?>"><?php echo esc_textarea( $place->post_title ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
				 	<th scope="row"><?php _e( 'Shortcode', 'up2' ); ?></th>
					<td><?php _e( 'Copy this code and paste it into your post or page content.', 'up2' ); ?><br />
						<textarea readonly="readonly" onfocus="this.select();" id="up2-map-places-shortcode" class="widefat" ></textarea>
					</td>
				</tr>
				<tr>
				 	<th scope="row"><?php _e( 'Map Places View', 'up2' ); ?></th>
					<td>
					   <div id="up2-view-demo-map"></div>
					</td>
				</tr>
				
			</table>
		</form>
	</div>
  <?php
}

/** Up2 Place Form */

function up2_place_form() {
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e( 'Map Place Form Shortcode Generator', 'up2' ); ?></h2><br />
	<?php $isSimpleCaptcha = class_exists( 'ReallySimpleCaptcha' ); ?>
	<table class="form-table">
		<?php if ( ! $isSimpleCaptcha ) : ?>
	    <tr>
	    	<td colspan="2"><span style="color: #f00;"><?php _e( 'To use CAPTCHA, you have to install additional plugin.', 'up2' ); ?> - <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple CAPTCHA</a></span></td>
	    </tr>
		<?php endif;?>
		<tr>
			<th scope="row"><?php _e( 'CAPTCHA', 'up2' ); ?></th>
			<td>
				<label><input type="checkbox" name="up2_captcha" value="1"<?php if ( ! $isSimpleCaptcha ) echo ' disabled="disabled"'; ?> onclick="up2MapForm.setCaptcha();" /> <?php _e( 'Yes', 'up2' ); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php _e( 'Registered users only', 'up2' ); ?></th>
			<td>
				<label><input type="checkbox" name="up2_registerusers" value="1" onclick="up2MapForm.setRegisterUsers();" /> <?php _e( 'Yes', 'up2' ); ?></label>
			</td>
		 </tr>
		<tr>
		 	<th scope="row"><?php _e( 'Shortcode', 'up2' ); ?></th>
			<td><?php _e( 'Copy this code and paste it into your post or page content.', 'up2' ); ?><br />
				<textarea readonly="readonly" onfocus="this.select();" id="up2-map-places-form-shortcode" class="widefat" ></textarea>
			</td>
		</tr>
	</table>
	<h3><?php _e( 'Screenshot', 'up2' ); ?></h3>
	<img src="<?php echo UP2_PLUGIN_URL; ?>images/form_map_place_frontend.png" style="border: 1px solid #ccc;" alt="<?php _e( 'new place form', 'up2' ) ;?>" title="<?php _e( 'new place form', 'up2' ); ?>" />
</div>
<?php 	
}

function up2_map_direction() {
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e( 'Map Direction Shortcode Generator', 'up2' ); ?></h2><br />
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Travel Mode', 'up2' ); ?></th>
			<td>
				<select name="travelMode" onchange="up2MapDirection.setTravelMode();">
					<option value="driving" selected="selected"><?php _e( 'Driving', 'up2' ); ?></option>
					<option value="walking"><?php _e( 'Walking', 'up2' ); ?></option>
				</select>
			</td>
		 </tr>
		 <tr>
		 	<th scope="row"><?php _e( 'Width/Height (in pixels)', 'up2' ); ?></th>
			<td>
			  <input type="text" name="width" value="600" onblur="up2MapDirection.changeWidth();" /> 
			  <input type="text" name="height" value="600" onblur="up2MapDirection.changeHeight();" />
			</td>
		</tr>
	   <tr>
	 	 <th scope="row"><?php _e( 'Map Place', 'up2' ); ?></th>
		 <td>
		<?php $places = get_posts( array( 'post_type' => 'map_place', 'numberposts' => -1 ) ); ?>
			<select name="place" onchange="up2MapDirection.markerPlace();">
				<?php foreach ( $places as $place ) : ?>
				<option value="<?php echo $place->ID; ?>"><?php echo esc_textarea( $place->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
		</tr>
		<tr>
		 	<th scope="row"><?php _e( 'Shortcode', 'up2' ); ?></th>
			<td><?php _e( 'Copy this code and paste it into your post or page content.', 'up2' ); ?><br />
				<textarea readonly="readonly" onfocus="this.select();" id="up2-map-direction-shortcode" class="widefat"></textarea>
			</td>
		</tr>
		<tr>
		 	<th scope="row"><?php _e( 'Map Direction View', 'up2' ); ?></th>
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
	if ( isset( $_POST['upload'] ) )
		up2_upload_csv();
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e( 'Map Places - CSV Import', 'up2' ); ?></h2>
		<form method="post" action="" enctype="multipart/form-data">
			<table class="">
			    <tr>
					<td colspan="2">
						<h3><?php _e( 'Basic CSV Headers', 'up2' ); ?></h3>
						<code>category;name;address;content;</code> 
						<h3><?php _e( 'Usage Limits', 'up2' ); ?></h3>
						<div><?php _e( 'Finding location on the map uses Google Geocoding API.', 'up2' ); ?><br />
							<div style="color: #f00;"><?php _e( 'Usage of the Google Geocoding API is limited to 2,500 geolocation requests per day.', 'up2' ); ?> <a href="https://developers.google.com/maps/documentation/geocoding/" target="_blank">Geocoding API</a></code>.
							</div>
						</div>
						<h3><?php _e( 'Advanced CSV Headers', 'up2' ); ?></h3>
						<code>category;name;address;content;lat;lng;</code>
						<span style="color: #f00;"><?php _e( '(recommended)', 'up2' ); ?></span>
						<br /><br />
						* <?php _e( '<strong>category</strong> - numeric field - use map category ID.', 'up' ); ?>&nbsp;&nbsp; <a href="edit-tags.php?taxonomy=up2_map_category&post_type=map_place"><?php _e( 'View Map Categories', 'up2' ); ?></a><br />
						* <strong>content</strong> - summary text shown in infobox, when pin is clicked.<br />
						* <strong>name</strong> - map place name.<br />
						* <strong>address</strong> - valid address.<br />
						* <strong>lat</strong> - <?php _e( 'Latitude - example:', 'up2' ); ?> 52.37<br />
						* <strong>lng</strong> - <?php _e( 'Longitude - example:', 'up2' ); ?> 4.89<br />
					</td>
				</tr>
				<tr>
					<td><h3><?php _e( 'Import CSV File', 'up2' ); ?></h3><input type="file" name="map_places" /> <input type="submit" class="button-primary" name="upload" value="<?php _e( 'Import', 'up2' ); ?>" /></td>
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

function up2_save_places( $csvData, $headers ) {
	if ( ! is_array( $csvData ) )
		return;

	foreach ( $csvData as $v ) {
		// Create post object
		$my_post = array(
			'post_title'    => $v['name'],
			'post_content'  => $v['content'],
			'post_status'   => 'publish',
			'post_type'   => 'map_place',
		);

		$post_id = wp_insert_post( $my_post );

		if ( $post_id ) {
			$metaData['address'] = $v['address'];

			if ( $headers == 6 ) {
				$metaData['lat'] = $v['lat'];
				$metaData['lng'] = $v['lng'];
			}

			$metaData['map-icon'] = '';

			update_post_meta( $post_id, '_up2_map_place_data', $metaData );

			if ( $v['category'] != 0 ) {
				wp_set_post_terms( $post_id, $v['category'], 'up2_map_category' );
			}
		}
	}
}

/** Show CPT in Right Now Dashboard widget */

function up2_right_now_content_table_end() {
	$args = array(
			'public' => true ,
			'_builtin' => false
	);
	$output = 'object';
	$operator = 'and';

	$post_types = get_post_types( $args , $output , $operator );

	foreach ( $post_types as $post_type ) {
		$num_posts = wp_count_posts( $post_type->name );
		$num = number_format_i18n( $num_posts->publish );
		$text = _n( $post_type->labels->singular_name, $post_type->labels->name , intval( $num_posts->publish ) );

		if ( current_user_can( 'edit_posts' ) ) {
			$num = "<a href='edit.php?post_type=$post_type->name'>$num</a>";
			$text = "<a href='edit.php?post_type=$post_type->name'>$text</a>";
		}

		echo '<tr><td class="first b b-' . $post_type->name . '">' . $num . '</td>';
		echo '<td class="t ' . $post_type->name . '">' . $text . '</td></tr>';

		if ( ! empty( $post_type->taxonomies ) ) {
			foreach ( $post_type->taxonomies as $pt_tax ) {
				$taxonomy = get_taxonomy( $pt_tax );
				$num_terms = wp_count_terms( $taxonomy->name );
				$num = number_format_i18n( $num_terms );
				$text = _n( $taxonomy->labels->singular_name, $taxonomy->labels->name , intval( $num_terms ) );
		
				if ( current_user_can( 'manage_categories' ) ) {
					$num = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$num</a>";
					$text = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$text</a>";
				}
		
				echo '<tr><td class="first b b-' . $taxonomy->name . '">' . $num . '</td>';
				echo '<td class="t ' . $taxonomy->name . '">' . $text . '</td></tr>';
			}
		}
	}
}
add_action( 'right_now_content_table_end' , 'up2_right_now_content_table_end' );