<?php
/**
 * Widgets
 */

function up2_map_places_widgets_init() {
	register_widget( 'Up2_MapPlaces_Widget' );
	register_widget( 'Up2_MapDirection_Widget' );
}
add_action( 'widgets_init', 'up2_map_places_widgets_init' );

class Up2_MapPlaces_Widget extends WP_Widget {
	protected $fields = array(
			'title' => '',
			'mapControlType'=> 1,
			'navControl' => 1,
			'scrollwheel' => 1,
			'streetviewcontrol' => 0,
			'clustering' => 0,
			'gmaptype' => 'roadmap',
			'mapTypeControlStyle' => 'default',
			'mapTypeControlPos' => 'tr',
			'width'=> 600,
			'height' => 600,
			'categories' => 0,
			'place_id' => 0
	);

	function Up2_MapPlaces_Widget() {
		$widget_ops = array( 'classname' => 'up2map-places-widget-area', 'description' => __( 'Display Up2 Map Places', 'up2' ) );
		$this->WP_Widget( 'up2map-places-widget-area', __( 'Up2 Map Places', 'up2' ), $widget_ops, array( 'width' => 320 ) );
	}

	function widget( $args, $instance ) {
		extract($args);

		$title = isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$mapControlType = $instance['mapControlType'] == 1 ? 'true' : 'false';
		$navControl = $instance['navControl'] == 1 ? 'true' : 'false';
		$scrollwheel = $instance['scrollwheel'] == 1 ? 'true' : 'false';
		$streetviewcontrol = $instance['streetviewcontrol'] == 1 ? 'true' : 'false';
		$clustering = $instance['clustering'] == 1 ? 'true' : 'false';
		$gmaptype = isset($instance['gmaptype']) ? $instance['gmaptype'] : '';
		$mapTypeControlStyle = isset($instance['mapTypeControlStyle']) ? $instance['mapTypeControlStyle'] : '';
		$mapTypeControlPos = isset($instance['mapTypeControlPos']) ? $instance['mapTypeControlPos'] : '';
		$width = isset($instance['width']) ? $instance['width'] : '';
		$height = isset($instance['height']) ? $instance['height'] : '';
		$categories = isset($instance['categories']) ? $instance['categories'] : '';
		$place_id = isset($instance['place_id']) ? $instance['place_id'] : '';
		
		$shortcode = '[up_map_places width="'.$width.'" height="'.$height.'" map_type_control="'.$mapControlType.'" navigation_control="'.$navControl.'"';
		$shortcode .= ' scrollwheel="'.$scrollwheel.'" street_view_control="'.$streetviewcontrol.'" cluster="'.$clustering.'" zoom_control_style="'.$mapTypeControlStyle.'"';
		$shortcode .= ' map_type_id="'.$gmaptype.'" map_type_control_style="'.$mapTypeControlStyle.'" map_type_controlpos="'.$mapTypeControlPos.'"';
		
		if ( empty( $place_id ) && $categories ) {
			$shortcode .= ' categories="'.$categories.'"';
		} elseif ( ! empty( $place_id ) ) {
			$shortcode .= ' id="'.$place_id.'"';
		}

		$shortcode .= ']';
		
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

			echo do_shortcode($shortcode);
			//echo $shortcode;

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->fields );

		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['mapControlType'] = (int) $new_instance['mapControlType'];
		$instance['navControl'] = (int) $new_instance['navControl'];
		$instance['scrollwheel'] = (int) $new_instance['scrollwheel'];
		$instance['streetviewcontrol'] = (int) $new_instance['streetviewcontrol'];
		$instance['clustering'] = (int) $new_instance['clustering'];
		$instance['gmaptype'] = esc_attr( $new_instance['gmaptype'] );
		$instance['mapTypeControlStyle'] = esc_attr( $new_instance['mapTypeControlStyle'] );
		$instance['mapTypeControlPos'] = esc_attr( $new_instance['mapTypeControlPos'] );
		$instance['width'] = (int) $new_instance['width'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['categories'] = (int) $new_instance['categories'];
		$instance['place_id'] = (int) $new_instance['place_id'];
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->fields );
		$taxonomies = get_terms( 'up2_map_category' );
		$map_places = get_posts( array( 'numberposts' => -1, 'post_type' => 'map_place' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'up2' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'mapControlType' ); ?>" name="<?php echo $this->get_field_name( 'mapControlType' ); ?>" value="1" <?php checked( $instance['mapControlType'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'mapControlType' ); ?>"><?php _e( 'Map Type Control', 'up2' ); ?></label>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'navControl' ); ?>" name="<?php echo $this->get_field_name( 'navControl' ); ?>" value="1" <?php checked( $instance['navControl'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'navControl' ); ?>"><?php _e( 'Navigation Control', 'up2' ); ?></label>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'scrollwheel' ); ?>" name="<?php echo $this->get_field_name( 'scrollwheel' ); ?>" value="1" <?php checked( $instance['scrollwheel'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'scrollwheel' ); ?>"><?php _e( 'Scrollwheel', 'up2' ); ?></label>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'streetviewcontrol' ); ?>" name="<?php echo $this->get_field_name( 'streetviewcontrol' ); ?>" value="1" <?php checked( $instance['streetviewcontrol'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'streetviewcontrol' ); ?>"><?php _e( 'StreetView Control', 'up2' ); ?></label>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'clustering' ); ?>" name="<?php echo $this->get_field_name( 'clustering' ); ?>" value="1" <?php checked( $instance['clustering'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'clustering' ); ?>"><?php _e( 'Clustering', 'up2' ); ?></label>
		</p>
		<br />
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width', 'up2' ); ?></label>&nbsp;&nbsp;&nbsp;
			<input type="text" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']?>"/> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'up2' ); ?></label>&nbsp;
			<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']?>"/> px
		</p>
		<br />
		<p>
			<label for="<?php echo $this->get_field_id( 'gmaptype' ); ?>"><?php _e( 'Map Type', 'up2' ); ?></label><br />
			<select class="widefat" name="<?php echo $this->get_field_name( 'gmaptype' ); ?>" id="<?php echo $this->get_field_id( 'gmaptype' ); ?>">
				<option value="terrain" <?php selected($instance['gmaptype'], 'terrain' )?>><?php _e( 'Terrain', 'up2' ); ?></option>
				<option value="satellite" <?php selected($instance['gmaptype'], 'satellite' )?>><?php _e( 'Satellite', 'up2' ); ?></option>
				<option value="roadmap" <?php selected($instance['gmaptype'], 'roadmap' )?>><?php _e( 'Roadmap', 'up2' ); ?></option>
				<option value="hybrid" <?php selected($instance['gmaptype'], 'hybrid' )?>><?php _e( 'Hybrid', 'up2' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mapTypeControlStyle' ); ?>"><?php _e( 'Map Type Control Options', 'up2' ); ?></label><br />
			<select class="widefat" name="<?php echo $this->get_field_name( 'mapTypeControlStyle' ); ?>" id="<?php echo $this->get_field_id( 'mapTypeControlStyle' ); ?>">
				<option value="default" <?php selected($instance['mapTypeControlStyle'], 'default' )?>><?php _e( 'Default', 'up2' ); ?></option>
				<option value="drowpdown" <?php selected($instance['mapTypeControlStyle'], 'drowpdown' )?>><?php _e( 'Dropdown menu', 'up2' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mapTypeControlPos' ); ?>" ><?php _e( 'Map Type Control Position', 'up2' ); ?></label><br />
			<select class="widefat" name="<?php echo $this->get_field_name( 'mapTypeControlPos' ); ?>" id="<?php echo $this->get_field_id( 'mapTypeControlPos' ); ?>">
				<option value="bc" <?php selected( $instance['mapTypeControlPos'], 'bc' ); ?>><?php _e( 'Center of the bottom row', 'up2' ); ?></option>
				<option value="bl" <?php selected( $instance['mapTypeControlPos'], 'bl' ); ?>><?php _e( 'Bottom left and flow towards the middle', 'up2' ); ?></option>
				<option value="br" <?php selected( $instance['mapTypeControlPos'], 'br' ); ?>><?php _e( 'Bottom right and flow towards the middle', 'up2' ); ?></option>
				<option value="lb" <?php selected( $instance['mapTypeControlPos'], 'lb' ); ?>><?php _e( 'Left, above bottom-left elements, and flow upwards', 'up2' ); ?></option>
				<option value="lc" <?php selected( $instance['mapTypeControlPos'], 'lc' ); ?>><?php _e( 'Center of the left side', 'up2' ); ?></option>
				<option value="lt" <?php selected( $instance['mapTypeControlPos'], 'lt' ); ?>><?php _e( 'Left, below top-left elements, and flow downwards', 'up2' ); ?></option>
				<option value="rb" <?php selected( $instance['mapTypeControlPos'], 'rb' ); ?>><?php _e( 'Right, above bottom-right elements, and flow upwards', 'up2' ); ?></option>
				<option value="rc" <?php selected( $instance['mapTypeControlPos'], 'rc' ); ?>><?php _e( 'Center of the right side', 'up2' ); ?></option>
				<option value="rt" <?php selected( $instance['mapTypeControlPos'], 'rt' ); ?>><?php _e( 'Right, below top-right elements, and flow downwards', 'up2' ); ?></option>
				<option value="tc" <?php selected( $instance['mapTypeControlPos'], 'tc' ); ?>><?php _e( 'Center of the top row', 'up2' ); ?></option>
				<option value="tl" <?php selected( $instance['mapTypeControlPos'], 'tl' ); ?>><?php _e( 'Top left and flow towards the middle', 'up2' ); ?></option>
				<option value="tr" <?php selected( $instance['mapTypeControlPos'], 'tr' ); ?>><?php _e( 'Top right and flow towards the middle', 'up2' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e( 'Map Category', 'up2' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'categories' ); ?>" id="<?php echo $this->get_field_id( 'categories' ); ?>">
				<option value=""><?php _e( 'Select Map Category...', 'up2' ); ?></option>
				<?php foreach ( $taxonomies as $v ) : ?>
				<option value="<?php echo $v->term_id; ?>" <?php selected( $instance['categories'], $v->term_id ); ?>><?php echo esc_textarea( $v->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'place_id' ); ?>"><?php _e( 'Map Place', 'up2' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'place_id' ); ?>" name="<?php echo $this->get_field_name( 'place_id' ); ?>">
				<option value=""><?php _e( 'Select Map Place...', 'up2' ); ?></option>
				<?php foreach ( $map_places as $place ) : ?>
				<option value="<?php echo $place->ID; ?>"<?php selected( $instance['place_id'], $place->ID ); ?>><?php echo esc_textarea( $place->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}
}

class Up2_MapDirection_Widget extends WP_Widget {
	protected $fields = array(
		'title' => '',
		'travelMode' => 'driving',
		'place_id' => 0,
		'width'=> 600,
		'height' => 600
	);

	function Up2_MapDirection_Widget() {
		$widget_ops = array( 'classname' => 'up2map-direction-widget-area', 'description' => __( 'Display Up2 Map Direction', 'up2' ) );
		$this->WP_Widget( 'up2map-direction-widget-area', __( 'Up2 Map Direction', 'up2' ), $widget_ops, array( 'width' => 320 ) );
	}

	function widget( $args, $instance ) {
		extract($args);

		$title = isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$width = isset($instance['width']) ? $instance['width'] : 0;
		$height = isset($instance['height']) ? $instance['height'] : 0;
		$place_id = isset($instance['place_id']) ? $instance['place_id'] : 0;
		$travelMode = isset($instance['travelMode']) ? $instance['travelMode'] : $this->fields['travelMode'];

		$shortcode = '[up_map_direction travel_mode="'.$travelMode.'"';

		if ( $width )   $shortcode .= ' width="'.$width.'"';
		if ( $height )  $shortcode .= ' height="'.$height.'"';
		if ( $place_id) $shortcode .= ' id="'.$place_id.'"';

		$shortcode .= ']';

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode($shortcode);
		//echo $shortcode;

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->fields );

		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['travelMode'] = esc_attr( $new_instance['travelMode'] );
		$instance['width'] = (int) $new_instance['width'];
		$instance['height'] = (int) $new_instance['height'];
		$instance['place_id'] = (int) $new_instance['place_id'];
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->fields );

		$map_places = get_posts( array( 'numberposts' => -1, 'post_type' => 'map_place' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'up2' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width', 'up2' ); ?></label>&nbsp;&nbsp;&nbsp;
			<input type="text" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>"/> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height', 'up2' ); ?></label>&nbsp;
			<input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>"/> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'travelMode' ); ?>"><?php _e( 'Travel Mode', 'up2' ); ?></label><br />
			<select class="widefat" name="<?php echo $this->get_field_name( 'travelMode' ); ?>" id="<?php echo $this->get_field_id( 'travelMode' ); ?>">
				<option value="driving" <?php selected( $instance['travelMode'], 'driving' ); ?>><?php _e( 'Driving', 'up2' ); ?></option>
				<option value="walking" <?php selected( $instance['travelMode'], 'walking' ); ?>><?php _e( 'Walking', 'up2' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'place_id' ); ?>"><?php _e( 'Map Place', 'up2' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'place_id' ); ?>" name="<?php echo $this->get_field_name( 'place_id' ); ?>">
				<option value=""><?php _e( 'Select Map Place...', 'up2' ); ?></option>
				<?php foreach ( $map_places as $place ) : ?>
				<option value="<?php echo $place->ID; ?>"<?php selected( $instance['place_id'], $place->ID ); ?>><?php echo esc_textarea( $place->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}
}