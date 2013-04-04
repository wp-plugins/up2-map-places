<?php
/**
 *  [up_map_places id="MAP_PLACES_ID" width=600 height=600]
 *  @attr: id null|number|number1,number2
 *  @attr: width number default 600
 *  @attr: height number default 600
 *
 */
function up_map_places_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'id' => 0,
		'width' => 600,
		'height' => 600,
		'map_type_control' => true,
		'navigation_control' => true,
		'scrollwheel' => false,
		'street_view_control' => false,
		'map_type_id' => "roadmap",
		'map_type_control_style' => "default",
		'map_type_controlpos' => "tr",
		'zoom_control_style' => "default",
		"cluster" => true,
		"categories" => 0,
	), $atts ) );

	$args['post_type'] = 'map_place';
	$args['post_status'] = 'publish';
	$args['nopaging'] = true;
		
	if ( $categories ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'up2_map_category',
				'terms' => array( $categories ),
				'field' => 'term_id'
			)
		);
		
	} elseif ( $id == -1 ) {
		$args['numberposts'] = trim($id);
	} else {
		$args['p'] = trim($id);
	}

	$custom_query = new WP_Query( $args );
	$markers = array();
	$i = 0;

	while ( $custom_query->have_posts() ) : $custom_query->the_post();
		$place_meta = get_post_meta( get_the_ID(), '_up2_map_place_data', true );

		if ( ! $place_meta )
			$place_meta = array();

		$markerUrl = 'http://maps.google.com/mapfiles/';
		$markerDefault = $markerUrl . 'marker.png';
		$icon = ( isset( $place_meta['map-icon'] ) && $place_meta['map-icon'] != '' ) ? $place_meta['map-icon'] : $markerDefault;

		// html for info window
		$data = "<div class='up2-infowindow' style='padding: 10px; width: " . ( $width / 3 ) * 2 . "px;'>
					<h3>" . get_the_title() . "</h3>
					" . get_the_content() . "
				</div>";

		$markers[$i]['data'] = $data;

		if ( ! empty( $place_meta['lat'] ) && ! empty( $place_meta['lng'] ) ) {
			$markers[$i]['lat'] = $place_meta['lat'];
			$markers[$i]['lng'] = $place_meta['lng'];
		} else {
			$markers[$i]['address'] = $place_meta['address'];
		}

		$markers[$i]['options'] = array( 'icon' => $icon );
		$i++;
	endwhile;
	
	wp_reset_postdata();

	$_idmap = "gmap-" . wp_generate_password( 4, false );
	$map = "<script type='text/javascript'>jQuery(function($){";
	$map .= "jQuery('#" . $_idmap . "').gmap3({";
	$_mapTypeControlStyle = array( 'default' => "DEFAULT", 'drowpdown' => 'DROPDOWN_MENU' );
	$_style = array_key_exists($map_type_control_style, $_mapTypeControlStyle) ? $_mapTypeControlStyle[$map_type_control_style] : 'DEFAULT';
	$_mapTypeId = array(
		"terrain" => "TERRAIN",
		"satellite" => "SATELLITE",
		"roadmap" => "ROADMAP",
		"hybrid" => "HYBRID"
	);
	$_typeId = array_key_exists( $map_type_id, $_mapTypeId ) ? $_mapTypeId[$map_type_id] : 'TERRAIN';
	$_controlPos = array(
		"tl" => "TOP_LEFT",
		"tc" => "TOP_CENTER",
		"tr" => "TOP_RIGHT",
		"lt" => "LEFT_TOP",
		"rt" => "RIGHT_TOP",
		"lc" => "LEFT_CENTER",
		"rc" => "RIGHT_CENTER",
		"lb" => "LEFT_BOTTOM",
		"rb" => "RIGHT_BOTTOM",
		"bl" => "BOTTOM_LEFT",
		"bc" => "BOTTOM_CENTER",
		"br" => "BOTTOM_RIGHT"
	);
	$controlPosition = array_key_exists($map_type_controlpos, $_controlPos) ? $_controlPos[$map_type_controlpos] : 'TOP_RIGHT';
	$map .= "map:{ options:{ ";
	$map .= "minZoom: 2,";
	$map .= "mapTypeId: google.maps.MapTypeId." . strtoupper($_typeId) .", ";
	$map .= "mapTypeControl: " . ( $map_type_control == 'true' ? 'true' : 'false' ) .", ";
	$map .= "mapTypeControlOptions: { 
			style: google.maps.MapTypeControlStyle.". $_style .",
			position: google.maps.ControlPosition.".$controlPosition."
		},";
	$map .= "navigationControl: " . ( $navigation_control == 'true' ? 'true' : 'false' ) .", ";
	$map .= "scrollwheel: " . ( $scrollwheel == 'true' ? 'true' : 'false' ) .", ";
	$map .= "streetViewControl: " . ( $street_view_control == 'true' ? 'true' : 'false' ) ." ";
	 
	if ( count( $markers ) == 1 ) {
		if ( array_key_exists( 'lat', $markers[0] ) && array_key_exists( 'lng', $markers[0] ) )
			$map .= ", center: [" . $markers[0]['lat'] . "," . $markers[0]['lng'] . "]";
		
		$map .= ', zoom: 15';
	}
 
	$map .= " }";

	if ( count( $markers ) == 1 && array_key_exists( 'address', $markers[0] ) ) 
		$map .= ', address: "' . $markers[0]['address'] . '"';

	$map .= " } ";

	// set the markers
	if ( count( $markers ) ) {
		$map .= ", marker: {
					values: " . json_encode($markers) . ",
					events:{
				      click: function(marker, event, context){
				        var map = $(this).gmap3('get'),
				        infowindow = $(this).gmap3({get:{name:'infowindow'}});
				        if (infowindow){
				          infowindow.open(map, marker);
				          infowindow.setContent(context.data);
				        } else {
				          $(this).gmap3({
				            infowindow:{
				              anchor:marker,
				              options:{ content: context.data }
				            }
				          });
				        }
				      },
					  closeclick: function(infowindow){
							infowindow.close();
				      }
				  }";
 	
		if ( $cluster == "true" && count( $markers ) > 1 ) {
			$map .= ', cluster:{
				      radius:100,
				      0: {
				        content: "<div class=\'cluster cluster-1\'>CLUSTER_COUNT</div>",
				        width: 53,
				        height: 52
				      },
				      20: {
				        content: "<div class=\'cluster cluster-2\'>CLUSTER_COUNT</div>",
				        width: 56,
				        height: 55
				      },
				      50: {
				        content: "<div class=\'cluster cluster-3\'>CLUSTER_COUNT</div>",
				        width: 66,
				        height: 65
				      },
				      100: {
				        content: "<div class=\'cluster cluster-4\'>CLUSTER_COUNT</div>",
				        width: 66,
				        height: 65
				      },
				      500: {
				        content: "<div class=\'cluster cluster-5\'>CLUSTER_COUNT</div>",
				        width: 66,
				        height: 65
				      }
				    }';
		}
	}	
	$map .= "}});";

	$map .= "}); </script>";
		
	return $map . "<div id='{$_idmap}' class='up2-map-places' style='width:{$width}px;height:{$height}px;'> " . __( 'There are no places to show on the map', 'up2' ) . " </div>"; 
}

add_shortcode( 'up_map_places', 'up_map_places_shortcode' );

/**
 *  [up_create_map_place captcha="true" registerusers="true"]
 *  @attr: captcha true|false
 *  @attr: registerusers true|false
 *
 */

function up_create_map_place( $atts ) {
	extract( shortcode_atts( array(
		'captcha' => false,
		'registerusers' => false,
	), $atts ) );

	$isRegister = ( $registerusers == "true" ) ? true : false;

	if ( $isRegister && is_user_logged_in() == false )
		return;
	
	$name = isset( $_POST['up2_name'] ) ? esc_attr( $_POST['up2_name'] ) : '';
	$content = isset( $_POST['up2_content'] ) ? esc_attr( $_POST['up2_content'] ) : '';
	$address = isset( $_POST['up2_address'] ) ? esc_attr( $_POST['up2_address'] ) : '';
	$lat = isset( $_POST['up2_lat'] ) ? esc_attr( $_POST['up2_lat'] ) : '';
	$lng = isset( $_POST['up2_lng'] ) ? esc_attr( $_POST['up2_lng'] ) : '';
	
	$taxonomies = get_terms( 'up2_map_category', array( 'hide_empty' => false ) );
	
	$html = '<div id="up_create_map_place"><p id="up2_create_map_notify"></p><form id="map_create_place" method="post" action="#up_create_map_place">'; 
	$html .= '<input type="hidden" id="up2_lat" name="up2_lat" value="' . $lat . '" />
			  <input type="hidden" id="up2_lng" name="up2_lng" value="' . $lng . '>" />';
	$html .= '<p><label for="up2_name">' . __( 'Name', 'up2' ) . ' </label>
				 <input type="text" id="up2_name" name="up2_name" value="' . $name . '" /></p>';
	
	if ( $taxonomies ) {
		$html .= '<p><label for="up2_categories">' . __( 'Category', 'up2' ) . '</label>';
		$html .= '<select name="up2_categories" id="up2_categories">
					<option value="">' . __( 'Select Category...', 'up2' ) . '</option>';
			foreach ( $taxonomies as $v ) {
				$html .= '<option value="' . $v->term_id . '">' . $v->name . '</option>';
			}
		$html .= "</select></p>";
	}
	
	$html .= '<p><label for="up2_address">' . __( 'Address', 'up2' ) . '</label>
				 <input type="text" id="up2_address" name="up2_address" value="' . $address . '" class="up2-address-field" /> <a href="javascript:;" class="up2-find-address">' . __( 'Find Address', 'up2' ) . '</a></p>';
	$html .= '<div id="up2_map_demo"></div>';
	$html .= '<p><label for="up2_content">'. __( 'Content', 'up2' ) . '</label>
				 <textarea id="up2_content" name="up2_content" rows="8" cols="45">' . $content . '</textarea></p>';
	
	if ( class_exists( 'ReallySimpleCaptcha' ) && $captcha ) {
		$rsc = new ReallySimpleCaptcha();
		$rsc->img_size = array( 90, 33 );
		$rsc->base = array( 14, 21 );
		$word = $rsc->generate_random_word();
		$prefix = mt_rand();
		$captcha = $rsc->generate_image( $prefix, $word );

		$html .= '<p><label for="up2_captcha">' . __( 'Security Code', 'up2' ) . '</label>
					<img src="/wp-content/plugins/really-simple-captcha/tmp/' . $captcha . '" class="up2-captcha-image" />
					<input type="hidden" name="_up2_captcha_challenge" id="_up2_captcha_challenge" value="' . $prefix . '" />
				 	<input type="text" id="up2_captcha" name="up2_captcha" class="up2-captcha" value="" /></p>';
	}

	$html .= '<p><input type="submit" id="up2_submit" name="up2_submit" value="' . __( 'Submit', 'up2' ) . '" /></p>';
	$html .= '</form></div>';

	return $html;
}
add_shortcode( 'up_create_map_place', 'up_create_map_place' );

function up2_frontend() {
?>
<link rel="stylesheet" href="<?php echo UP2_PLUGIN_URL; ?>css/up2-style.css" type="text/css" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true&amp;language=en"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo UP2_PLUGIN_URL; ?>js/up2-places.js"></script>
<script type="text/javascript">
<!--
	function createPlace() {
		var name = jQuery('#up2_name'), 
		    address = jQuery('#up2_address'), 
		    lat = jQuery('#up2_lat'), 
		    lng = jQuery('#up2_lng'), 
		    categories = jQuery('#up2_categories'), 
		    content = jQuery('#up2_content'),
		    captchaChallenge = null,
		    captchaValue = null;

		if( jQuery('#_up2_captcha_challenge').length ) {
			captchaChallenge = jQuery('#_up2_captcha_challenge').val();
			captchaValue = jQuery('#up2_captcha').val();
		};
		
		 var data = {
			 action: "create_place_action",
			 _ajax_nonce: "<?php echo wp_create_nonce( 'create_place_action' ); ?>",
			 up2_name: name.val(),
			 up2_categories: categories.val(),
			 up2_address: address.val(),
			 up2_content: content.val(),
			 up2_lat: lat.val(),
			 up2_lng: lng.val(),
			 _up2_captcha_challenge: captchaChallenge,
			 up2_captcha: captchaValue
		 };

		jQuery.post("<?php echo admin_url( 'admin-ajax.php' )?>", data, function(response) {

			if( !response.error ) {
				jQuery('#map_create_place').fadeOut(400, function(){
					jQuery('#up2_create_map_notify').html(response.msg).fadeIn();
				});
			} else {
				jQuery('#up2_create_map_notify').html(response.msg).fadeIn();
				jQuery.each(response.fields, function(i, el){
					jQuery('#'+el).css({'borderColor': '#FF0000'});
				});
			} 
			
		}, "json");
    }
//-->
</script>
<?php 
}
add_action( 'wp_head', 'up2_frontend' );
add_action( 'wp_ajax_nopriv_create_place_action', 'up2_create_place_action_callback' ); // for not-logged in users
add_action( 'wp_ajax_create_place_action', 'up2_create_place_action_callback' ); // for not-logged in users

function up2_create_place_action_callback() {
	check_ajax_referer( 'create_place_action' );

	$name = isset( $_POST['up2_name'] ) ? esc_attr( $_POST['up2_name'] ) : '';
	$content = isset( $_POST['up2_content'] ) ? esc_attr( $_POST['up2_content'] ) : '';
	$address = isset( $_POST['up2_address'] ) ? esc_attr( $_POST['up2_address'] ) : '';
	$lat = isset( $_POST['up2_lat'] ) ? esc_attr( $_POST['up2_lat'] ) : '';
	$lng = isset( $_POST['up2_lng'] ) ? esc_attr( $_POST['up2_lng'] ) : '';
	$categories = isset( $_POST['up2_categories'] ) ? esc_attr( $_POST['up2_categories'] ) : '';
	$captchaChallenge = isset( $_POST['_up2_captcha_challenge'] ) ? esc_attr( $_POST['_up2_captcha_challenge'] ) : '';
	$captchaValue = isset( $_POST['up2_captcha'] ) ? esc_attr( $_POST['up2_captcha'] ) : '';

	/*validation */
	$errorFields = array();

	if ( empty( $name ) ) $errorFields[] = 'up2_name';
	if ( empty( $content ) ) $errorFields[] = 'up2_content';
	if ( empty( $address ) ) $errorFields[] = 'up2_address';

	if ( $captchaChallenge != '' ) {
		$rsc = new ReallySimpleCaptcha();

		if ( ! $rsc->check( $captchaChallenge, $captchaValue ) )
			$errorFields[] = 'up2_captcha';
	}
	
	/*validation */

	if ( count( $errorFields ) ) {
		$response['error'] = true;
		$response['msg'] = __( 'Validation errors occurred. Please, verify the fields and submit the form again.', 'up2' );
		$response['fields'] = $errorFields; 
	} else {
		// Create post object
		$new_place = array(
			'post_title' => $name,
			'post_content' => $content,
			'post_status' => 'pending',
			'post_type' => 'map_place',
		);

		$post_id = wp_insert_post( $new_place );
		$response['error'] = false;

		if ( is_wp_error( $post_id ) ) {
			$response['error'] = false;
			$response['msg'] = __( 'You can not add a new place', 'up2' );
		} else {
			$metaData['address'] = $address;

			if ( $metaData['lat'] != '' )
				$metaData['lat'] = $lat;

			if ( $metaData['lng'] != '' )
				$metaData['lng'] = $lng;

			$metaData['map-icon'] = '';

			update_post_meta( $post_id, '_up2_map_place_data', $metaData );

			if ( $categories && is_numeric( $categories ) ) {
				wp_set_post_terms( $post_id, $categories, 'up2_map_category' );
			}

			$response['msg'] = '<strong>' . $name . '</strong> ' . __( 'was submitted successfully.', 'up2' );
			$response['msg'] .= " " . __( 'Submit New Place?', 'up2' ) . 
								" <a href='javascript:;' onclick='up2CreatePlace.show();'>" . __( 'Click Here', 'up2' ) . "</a>";
			
		}
	}

	echo json_encode( $response );
	exit;
}

/**
 *  [up_map_direction id="1" travel_mode="driving" width="600" height="600"]
 *  @attr: id int map place id
 *  @attr: travel_mode  BICYCLING|DRIVING|TRANSIT|WALKING
 *  @attr: width int 600
 *  @attr: height int 600
 */

function up_map_direction( $atts ) {
	extract( shortcode_atts( array(
		'id' => 0,
		'width' => 600,
		'height' => 600,
		'travel_mode' => 'driving'
	), $atts ) );

	if ( $id > 0 ) {
		$res = get_post( $id );
		$marker = array();

		if ( $res->ID ) {
			$place_meta = get_post_meta( $res->ID, '_up2_map_place_data', true );
			$icons = array( 'marker', 'marker_black', 'marker_grey', 'marker_orange', 'marker_white', 'marker_yellow', 'marker_purple', 'marker_green' );
			$markerUrl = 'http://maps.google.com/mapfiles/';
			$markerDefault = $markerUrl . 'marker.png';
			$markerIcon = ( isset( $place_meta['map-icon'] ) && $place_meta['map-icon'] != '' ) ? $place_meta['map-icon'] : $markerDefault;

			if ( ! empty( $place_meta['lat'] ) && ! empty( $place_meta['lng'] ) ) {
				$marker['lat'] = $place_meta['lat'];
				$marker['lng'] = $place_meta['lng'];
			} else {
				$marker['address'] = $place_meta['address'];
			}

			$marker['options'] = array( 'icon' => $markerIcon );

		$_travelMode = array(
			"bicycling" => "BICYCLING",
			"driving" => "DRIVING",
			"transit" => "TRANSIT",
			"walking" => "WALKING"
		);

		$travelMode = array_key_exists( $travel_mode, $_travelMode ) ? $_travelMode[$travel_mode] : 'DRIVING';
	
		$map = "<script type='text/javascript'>jQuery(function($){";
		$map .= 'var $map = jQuery("#googleMap"),
		 			 menu = new Gmap3Menu($map),
		             current,  // current click event (used to save as start / end position)
		             m2;       // marker "to"
		function updateMarker(marker, isM1){
			if (!isM1) {
				m2 = marker;
			}
			updateDirections();
		}
		function addMarker(isM1){
	          var clear = {name:"marker"};
	          if (!isM1 && m2){
	            clear.tag = "to";
	            $map.gmap3({clear:clear});
	          }
	          $map.gmap3({
	            marker:{
	              latLng:current.latLng,
	              options:{
	                draggable:true,
	                icon:new google.maps.MarkerImage("http://maps.gstatic.com/mapfiles/icon_greenB.png")
	              },
	              tag: "to",
	              events: {
	                dragend: function(marker){
	                  updateMarker(marker, isM1);
	                }
	              },
	              callback: function(marker){
	                updateMarker(marker, isM1);
	              }
	            }
	          });
	        }';
		
		if ( array_key_exists( 'address', $marker ) ) {
			$origin = '"' . $marker['address'] . '"';
		} elseif ( array_key_exists( 'lat', $marker ) && array_key_exists( 'lng', $marker ) ) {
			$origin = " new google.maps.LatLng(" . $marker['lat'] . ", " . $marker['lng'] . ")";
		}

		$map .= 'function updateDirections(){
	          if (!m2){  return; }
	          $map.gmap3({
	            getroute:{
	              options:{
	                origin:' . $origin . ',
	                destination:m2.getPosition(),
	                travelMode: google.maps.DirectionsTravelMode.' . $travelMode . '
	              },
	              callback: function(results){
	                if (!results) return;
	                $map.gmap3({get:"directionrenderer"}).setDirections(results);
	              }
	            }
	          });
	        }';
		
		$map .= 'menu.add("Direction to here", "itemB", 
	          function(){
	            menu.close();
	            addMarker(false);
	          });
			menu.add("Center here", "centerHere", 
	          function(){
	              $map.gmap3("get").setCenter(current.latLng);
	              menu.close();
	          });
			$map.gmap3({
		          map:{ ';
		
		if ( array_key_exists( 'address', $marker ) ) {
			$map .= 'address: "' . $marker['address'] . '", ';
		}
		
		 $map .= ' options:{ ';
		 
		 if ( array_key_exists( 'lat', $marker ) && array_key_exists( 'lng', $marker ) )
		 	 $map .= "center: [" . $marker['lat'] . "," . $marker['lng'] . "], ";
		 
		 $map .= 'zoom: 7
		            },
		            events:{
		              rightclick:function(map, event){
		                current = event;
		                menu.open(current);
		              },
		              click: function(){
		                menu.close();
		              },
		              dragstart: function(){
		                menu.close();
		              },
		              zoom_changed: function(){
		                menu.close();
		              }
		            }
		          },
		          directionsrenderer:{
		            divId:"up2_directions",
		            options:{
		              preserveViewport: true,
		              markerOptions:{
		                visible: false
		              }
		            }
		          },
				  marker:'. json_encode( $marker ) .'	
		        });';
		
		$map .= "}); </script>";
		}
	}

	$html = "<div id='googleMap' class='up2-map-places' style='width:{$width}px;height:{$height}px;'>" . __("You didn`t select a point for map direction", 'up2')."</div><div id='up2_directions' style='width:{$width}px;'></div>"; 

	return $map . $html;
}
add_shortcode( 'up_map_direction', 'up_map_direction' );