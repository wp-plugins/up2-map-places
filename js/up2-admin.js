jQuery(function($){
	$('.place-modified').hide();
	var address = $('#address').val();
	var pin = $('.marker-icon-active').attr('src');
	if( address != '' ) {
		placeMarker(address);
	}
	
	$('#find-address').click(function(e){
		e.preventDefault();
		
		var newaddress = $('#address').val();
		var newpin = $('.marker-icon-active').attr('src');
		if( newaddress == '' ) {
			$('#address').css({'border': "1px solid red"});
			return;
		}
		if ( newaddress != address || newpin != pin ) {
			$('.place-modified').fadeIn();
		} else {
			$('.place-modified').fadeOut();
		}
		$('#up2-map').gmap3({
			clear: {
				name:["marker"],
				last: true
			}
		});
		 
		placeMarker(newaddress);

		return false;
	});
	
	$('.marker-icons').click(function(){
		var $this = $(this);
		$('.marker-icons').removeClass('marker-icon-active');
		$this.addClass('marker-icon-active');
		$('#up2-custom-icon').val($this.attr('src'));

		var marker = $("#up2-map").gmap3({get:"marker"});
		if( marker != undefined ) {
			marker.setIcon($this.attr('src'));
		}

		var newaddress = $('#address').val();
		var newpin = $('.marker-icon-active').attr('src');
		if ( newaddress != address || newpin != pin ) {
			$('.place-modified').fadeIn();
		} else {
			$('.place-modified').fadeOut();
		}
	});
	
});

function placeMarker(address) {
	jQuery('#address').css({'border': "1px solid #DFDFDF"});
	
	var icons = jQuery('#map-icon-view').attr('src');
	
	jQuery("#up2-map").width("600px").height("350px").gmap3({
		
		getlatlng:{
		    address: address,
		    callback: function(results){
		      if ( !results ) return;
		      
			  setLatLng(results[0].geometry.location);
			  
		      jQuery(this).gmap3({
		        marker:{
		          latLng: results[0].geometry.location,
		          options:{
		              draggable:true,
		              icon: icons
		            },
		            events:{
		              dragend: function(marker){
		                jQuery(this).gmap3({
		                  getaddress:{
		                    latLng:marker.getPosition(),
		                    callback:function(results){
		                    	var content = results && results[0] ? results && results[0].formatted_address : 'no address';
		                    	jQuery('#address').val(content);
		                        setLatLng(results[0].geometry.location);
		                    }
		                  }
		                });
		              }
		            }
		        },
				map:{ options: { zoom: 15 } }
				}); 
			}
		}
	});

	// center new marker point
	setTimeout(function(){
		var marker = jQuery('#up2-map').gmap3({get:'marker'});
		var map = jQuery('#up2-map').gmap3('get');

		if( map != undefined && marker != undefined ) {
			map.panTo(marker.getPosition());
			marker.setIcon(jQuery('.marker-icon-active').attr('src'));
		}
	}, 100);
}

function setLatLng(location) {
	 jQuery('#lat').val(location.lat());
     jQuery('#lng').val(location.lng());
}

var up2MapDirection = {
	id: 0,
	width: 600,
	height: 600,
	travelMode: "driving",
	
	shortcode: function() {
		var _shortcode = '[up_map_direction';
		
		this.id = jQuery('select[name="place"] option:selected').val();
		
		if( this.travelMode )
			_shortcode += ' travel_mode="'+this.travelMode + '"';
		
		if( this.id != 0 )
			_shortcode += ' id="'+this.id + '"';
		
		_shortcode += ' width="'+this.width+'" height="'+this.height + '"';
		
		_shortcode += ']';
		
		this.viewDemo(_shortcode);
		
		jQuery('#up2-map-direction-shortcode').val(_shortcode);
		
	},
	setTravelMode: function() {
		this.travelMode = jQuery('select[name="travelMode"] option:selected').val();
		this.shortcode();
	},
	markerPlace : function() {
		this.id = jQuery('select[name="place"] option:selected').val();
		this.shortcode();
	},
	changeWidth: function() {
		this.width = jQuery('input[name="width"]').val();
		this.shortcode();
	},
	changeHeight: function() {
		this.height = jQuery('input[name="height"]').val();
		this.shortcode();
	},
	viewDemo: function(shortcode) {
		var data = {
			action: "show_map",
			shortcode: shortcode,
			_ajax_nonce: view_demo_ajax_nonce
		};

		jQuery.post(ajaxurl , data, function(response) {
			jQuery(".up2-map-places").gmap3('destroy').remove();
			jQuery('#up2-view-demo-map-direction').html(response);
		});
	}
};

var up2MapForm = {
	captcha: false,
	registerusers: false,
	
	shortcode: function() {
		var _shortcode = '[up_create_map_place';
		
		if( this.captcha )
			_shortcode += ' captcha="'+this.captcha + '"';
		if( this.registerusers )
			_shortcode += ' registerusers="'+this.registerusers + '"';
		
		_shortcode += ']';
		
		jQuery('#up2-map-places-form-shortcode').val(_shortcode);
		
	},
	setCaptcha: function() {
		this.captcha = ( jQuery('input[name="up2_captcha"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	setRegisterUsers: function() {
		this.registerusers = ( jQuery('input[name="up2_registerusers"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	}
};

var up2Map = {
	
	width: 600,
	height: 600,
	mapTypeControl: true,
	navigationControl: true,
	scrollwheel: true,
	streetViewControl: false,
	cluster: true,
	mapTypeId: "roadmap",
	mapTypeControlStyle: "default",
	mapTypeControlPosition: "tr",
	zoomControlStyle: "default",
	categories: "",
	place: 0,
	
	shortcode: function() {
		
		var _shortcode = '[up_map_places';
		_shortcode += ' width="'+this.width+'" height="'+this.height + '"';
		_shortcode += ' map_type_control="' + ( this.mapTypeControl ? 'true' : 'false' ) + '"';
		_shortcode += ' navigation_control="' + ( this.navigationControl ? 'true' : 'false' ) + '"';
		_shortcode += ' scrollwheel="' + ( this.scrollwheel ? 'true' : 'false' ) + '"';
		_shortcode += ' street_view_control="' + ( this.streetViewControl ? 'true' : 'false' ) + '"';
		_shortcode += ' cluster="' + ( this.cluster ? 'true' : 'false' ) + '"';
		_shortcode += ' zoom_control_style="' + this.zoomControlStyle + '"';
		_shortcode += ' map_type_id="'+this.mapTypeId + '"';
		_shortcode += ' map_type_control_style="'+this.mapTypeControlStyle + '"';
		_shortcode += ' map_type_controlpos="'+this.mapTypeControlPosition + '"';
		
		if( this.categories && this.place == 0 )
			_shortcode += ' categories="'+this.categories + '"';
		
		if( this.place != 0 )
			_shortcode += ' id="'+this.place + '"';
		
		_shortcode += ']';
			
		this.viewDemo(_shortcode);
		
		jQuery('#up2-map-places-shortcode').val(_shortcode);
	},
	
	typeControl: function() {
		this.mapTypeControl = ( jQuery('input[name="MapTypeControl"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	navControl: function() {
		this.navigationControl = ( jQuery('input[name="NavigationControl"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	scrollWheelControl: function() {
		this.scrollwheel = ( jQuery('input[name="scrollwheel"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	streetView: function() {
		this.streetViewControl = ( jQuery('input[name="StreetViewControl"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	clustering: function() {
		this.cluster = ( jQuery('input[name="cluster"]').attr('checked') == undefined ) ? false : true;
		this.shortcode();
	},
	mapType: function() {
		this.mapTypeId = jQuery('select[name="MapTypeId"] option:selected').val();
		this.shortcode();
	},
	mapControlStyle: function() {
		this.mapTypeControlStyle = jQuery('select[name="MapTypeControlStyle"] option:selected').val();
		this.shortcode();
	},
	mapControlPosition: function() {
		this.mapTypeControlPosition = jQuery('select[name="MapTypeControlPosition"] option:selected').val();
		this.shortcode();
	},
	markerCategories: function() {
		this.categories = jQuery('select[name="markerCategories"] option:selected').val();
		this.shortcode();
	},
	markerPlace : function() {
		this.place = jQuery('select[name="place"] option:selected').val();
		this.shortcode();
	},
	changeWidth: function() {
		this.width = jQuery('input[name="width"]').val();
		this.shortcode();
	},
	changeHeight: function() {
		this.height = jQuery('input[name="height"]').val();
		this.shortcode();
	},
	
	viewDemo: function(shortcode) {
		var data = {
			action: "show_map",
			shortcode: shortcode,
			_ajax_nonce: view_demo_ajax_nonce
		};

		jQuery.post(ajaxurl , data, function(response) {
			jQuery(".up2-map-places").gmap3('destroy').remove();
			jQuery('#up2-view-demo-map').html(response);
		});
	}
		
};
