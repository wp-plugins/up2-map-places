jQuery(function($){
	var address = $('#up2_address').val();
	if( address != "" && jQuery('form#map_create_place').size() ) {
		up2CreatePlace.setMap(address);
	}
	
	jQuery('.up2-find-address').click(function(){
		var address = jQuery('#up2_address').val();
		up2CreatePlace.setMap(address);
	});
	
	jQuery('form#map_create_place').submit(function(e){
		e.preventDefault();
		
		up2CreatePlace.valid();

		return false;
	});
	
	jQuery('input[type="text"], textarea').bind('keyup', function(){
		if( jQuery(this).val().length )
			up2CreatePlace.clearError(jQuery(this));
	});
	
});

var up2CreatePlace = {
		
	isError: false, 
	errorBorder: "#FF0000",
	normalBorder: '#CCCCCC',
	
	show: function() {
		document.getElementById("map_create_place").reset();
		jQuery('#up2_map_demo').empty().hide();
		
		jQuery('#up2_create_map_notify').empty().fadeOut(400, function(){
			jQuery('#map_create_place').fadeIn();
		});
	},
	
	valid: function() {
		
		var name = jQuery('#up2_name'), 
		    address = jQuery('#up2_address'), 
		    content = jQuery('#up2_content'),
		    captchaChallenge = null,
		    captchaValue = null;
		
		if( jQuery('#_up2_captcha_challenge').length ) {
			captchaChallenge = jQuery('#_up2_captcha_challenge').val();
			captchaValue = jQuery('#up2_captcha');

			this.setError(captchaValue);
		}
		
		this.setError(name);
		this.setError(address);
		this.setError(content);
		
		this.setMap(address.val());
		
		if( !this.isError ) {
			createPlace();	
		}
		
	},
	
	setMap: function(address) {
		
		if( address == '' || address.length < 3 ) return;
		
		 $('#up2_map_demo').show().gmap3({
		    clear: {
		      name:["marker"],
		      last: true
		    }
		});
		 
		 up2CreatePlace.placeMarker(address);
	},
	
	setError: function(element) {
		if( element.val() == "" ) {
			this.isError = true;
			element.css({'borderColor': this.errorBorder});
		} else {
			this.isError = false;
			element.css({'borderColor': this.normalBorder});
		}
	},
	
	clearError: function(element) {
		element.css({'borderColor': this.normalBorder});
	},
	
	setLatLng: function(location) {
		 jQuery('#up2_lat').val(location.lat());
	     jQuery('#up2_lng').val(location.lng());
	},
	
	
	placeMarker: function(address) {
		jQuery('#up2_address').css({'borderColor': this.normalBorder});
		
		jQuery("#up2_map_demo").gmap3({
			
			getlatlng:{
				address: address,
				callback: function(results){
					if ( !results ) return;
					
					up2CreatePlace.setLatLng(results[0].geometry.location);
					
					jQuery(this).gmap3({
						marker:{
							latLng: results[0].geometry.location,
							options:{ draggable:true },
							events:{
								dragend: function(marker){
									jQuery(this).gmap3({
										getaddress:{
											latLng:marker.getPosition(),
											callback:function(results){
												var content = results && results[0] ? results && results[0].formatted_address : "no address";
												jQuery('#up2_address').val(content);
												up2CreatePlace.setLatLng(results[0].geometry.location);
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
		
		//center new marker ponint
		setTimeout(function(){
			var marker = jQuery("#up2_map_demo").gmap3({get:"marker"});
			var map = jQuery("#up2_map_demo").gmap3("get");
			
			if( map != undefined && marker != undefined )
				map.panTo(marker.getPosition());
			
		}, 300);
		
	}
		
};