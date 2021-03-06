/**
 * GMW main map function
 * @param gmwForm
 */
function gmwMapInit( mapObject ) {
	
	//make sure the map element exists to prevent JS error
	if ( !jQuery( '#'+mapObject['mapElement'] ).length ) {
		return;
	}

	//map id
	var mapID = mapObject.mapId;
	var markersClustereOk = false;

	//initiate map options
	mapObject['mapOptions']['zoom'] 	 = ( mapObject['zoomLevel'] == 'auto' ) ? 13 : parseInt( mapObject['zoomLevel'] );
	mapObject['mapOptions']['center'] 	 = new google.maps.LatLng( mapObject['userPosition']['lat'], mapObject['userPosition']['lng'] );
	mapObject['mapOptions']['mapTypeId'] = google.maps.MapTypeId[mapObject['mapOptions']['mapTypeId']];
	mapObject['bounds'] 				 = new google.maps.LatLngBounds();
		
	//merge custom map options if exsits
	if ( "undefined" != typeof gmwMapOptions[mapID] ) {
		jQuery.extend(mapObject['mapOptions'], gmwMapOptions[mapID] );
	}
	
	var form = mapObject['form'];
	
	//create the map
	mapObject['map'] = new google.maps.Map(document.getElementById( mapObject['mapElement'] ), mapObject['mapOptions'] );
	mapObject['infoBox'] = null;

	//initiate markers clusterer if needed ( for premium features )
	if ( mapObject['markersDisplay'] == 'markers_clusterer' && typeof MarkerClusterer === 'function' ) {

		psMc = new MarkerClusterer( mapObject['map'], mapObject['markers'] );	

    } else if ( mapObject['markersDisplay'] == 'markers_spiderfier' && typeof OverlappingMarkerSpiderfier === 'function' ) {

    	psOms = new OverlappingMarkerSpiderfier( mapObject['map'], {legWeight:2} );		
		
		psOms.addListener('click', function(markerClicked, event) {
			iwOnClick( markerClicked );
		});
	}	
 
	//create markers for locations
	for ( i = 0; i < mapObject['locations'].length; i++ ) {  
		
		//make sure location has coordinates to prevent JS error
		if ( mapObject['locations'][i]['lat'] == undefined || ( mapObject['locations'][i]['long'] == undefined && mapObject['locations'][i]['lng'] == undefined ) )
			continue;
		
		if ( mapObject['locations'][i]['long'] == undefined ) {
			mapObject['locations'][i]['long'] = mapObject['locations'][i]['lng'];
		}
		
		var gmwLocation = new google.maps.LatLng( mapObject['locations'][i]['lat'], mapObject['locations'][i]['long'] );
			
		mapObject['bounds'].extend(gmwLocation);
       
        //custom map icon only for forms
        if ( mapObject['form'] != false && mapObject['form']['results_map']['map_icon_usage'] != 'undefined' ) {

	        if ( mapObject['form']['results_map']['map_icon_usage'] != 'avatar' && mapObject['form']['results_map']['map_icon_usage'] != 'image' ) {                            
	            mapObject['locations'][i]['mapIcon'] = mapObject['form']['results'][i]['mapIcon'];
	        } else {              
	            mapObject['locations'][i]['mapIcon'] = new google.maps.MarkerImage(
	                form['results'][i].mapIcon,
	                new google.maps.Size(30, 30),
	                new google.maps.Point(0,0),
	                new google.maps.Point(9.5, 29),
	                new google.maps.Size(32,32)
	            );
	        }
	    }

	    //create marker
		mapObject['markers'][i] = new google.maps.Marker({
			position: gmwLocation,
			icon:mapObject['locations'][i]['mapIcon'],
			map:mapObject['map'],
			id:i 
		});

		 //add marker to clusterer if needed
		if ( mapObject['markersDisplay'] == 'markers_clusterer' ) {	

			psMc.addMarker( mapObject['markers'][i] );		

			//initiae marker click
			google.maps.event.addListener( mapObject['markers'][i], 'click', function() {
				iwOnClick( this )
			});

		//add marker to spiderfier if needed
		} else if ( mapObject['markersDisplay'] == 'markers_spiderfier' ) {	

			psOms.addMarker( mapObject['markers'][i] );
			mapObject['markers'][i].setMap(mapObject['map']);	
			
		} else {		

			mapObject['markers'][i].setMap(mapObject['map'] );

			google.maps.event.addListener( mapObject['markers'][i], 'click', function() {
				iwOnClick( this )
			});			
		}
	}
				
	//create user's location marker
	if ( mapObject['userPosition']['lat'] != false && mapObject['userPosition']['lng'] != false && mapObject['userPosition']['mapIcon'] != false ) {
	
		//user's location
		mapObject['userPosition']['location'] = new google.maps.LatLng( mapObject['userPosition']['lat'], mapObject['userPosition']['lng'] );
		
		//append user's location to bounds
		mapObject['bounds'].extend(mapObject['userPosition']['location']);
		
		//create user's marker
		mapObject['userPosition']['marker'] = new google.maps.Marker({
			position: mapObject['userPosition']['location'],
			map: mapObject['map'],
			icon:mapObject['userPosition']['mapIcon']
		});
		
		//create user's marker info-window
		if ( mapObject['userPosition']['iwContent'] != null ) {

			var iw = new google.maps.InfoWindow({
				content: mapObject['userPosition']['iwContent']
			});
		      					
			if ( mapObject['userPosition']['iwOpen'] == true ) {
				iw.open( mapObject['map'], mapObject['userPosition']['marker']);
			}
			
		    google.maps.event.addListener( mapObject['userPosition']['marker'], 'click', function() {
		    	iw.open( mapObject['map'], mapObject['userPosition']['marker']);
		    });     
		}
	}
					
	//after map was created
	google.maps.event.addListenerOnce(mapObject['map'], 'idle', function(){	
		
		//custom zoom point
		if ( mapObject['zoomPosition'] != false && mapObject['zoomLevel'] != 'auto' ) {

			mapObject['zoomPosition']['position'] = new google.maps.LatLng( mapObject['zoomPosition']['lat'], mapObject['zoomPosition']['lng'] );
			mapObject['map'].setZoom( parseInt( mapObject['zoomLevel'] ) );
			mapObject['map'].panTo( mapObject['zoomPosition']['position'] );

		} else if ( mapObject['locations'].length == 1 && mapObject['userPosition']['location'] == false ) {

			mapObject['map'].setZoom(13);
			mapObject['map'].panTo(mapObject['markers'][0].getPosition());

		} else if ( mapObject['zoomLevel'] != 'auto' && mapObject['userPosition']['location'] != false ) {

			mapObject['map'].setZoom( parseInt( mapObject['zoomLevel'] ) );
			mapObject['map'].panTo(mapObject['userPosition']['location']);

		} else if ( mapObject['zoomLevel'] == 'auto' || mapObject['userPosition']['location'] == false  ) { 
			
			mapObject['map'].fitBounds(mapObject['bounds']);
		}

		//fadeout the map loader if needed
		if ( mapObject['mapLoaderElement'] != false ) {
			jQuery(mapObject['mapLoaderElement']).fadeOut(1000);
		}
		
		//create map expand toggle if needed
		if ( mapObject['resizeMapElement'] != false ) {
			
			mapObject['resizeMapControl'] = document.getElementById(mapObject['resizeMapElement']);
			mapObject['resizeMapControl'].style.position = 'absolute';	
			mapObject['map'].controls[google.maps.ControlPosition.TOP_RIGHT].push(mapObject['resizeMapControl']);			
			mapObject['resizeMapControl'].style.display = 'block';
		
			//expand map function		    	
	    	jQuery('#'+mapObject['resizeMapElement']).click(function() {
	    		
	    		var mapCenter = mapObject['map'].getCenter();
	    		jQuery(this).closest('.gmw-map-wrapper').toggleClass('gmw-expanded-map');          		
	    		jQuery(this).toggleClass('fa-expand').toggleClass('fa-compress');
	    		
	    		setTimeout(function() { 			    		
	    			google.maps.event.trigger(mapObject['map'], 'resize');
	    			mapObject['map'].setCenter(mapCenter);							
				}, 100);            		
	    	});
		}
	});

	//on marker click function
	function iwOnClick( markerClicked ) {

		if ( typeof directionsDisplay !== 'undefined' ) {
			directionsDisplay.setMap(null);
		}

		if ( mapObject['infoWindowType'] == 'infobox' ) {
			
			mapObject['markerClicked'] = markerClicked;

			infoboxInfoDisplay();

		} else if ( mapObject['infoWindowType'] == 'popup' ) {
			
			if ( mapObject['markerClicked'] ) {
				if ( mapObject['markerClicked'].getAnimation() != null ) {
					mapObject['markerClicked'].setAnimation(null);
				}
			}
			
			mapObject['markerClicked'] = markerClicked;

			//set bounce animation
			mapObject['markerClicked'].setAnimation(google.maps.Animation.BOUNCE);

			popupWindowDisplay();				
		
		} else {

			mapObject['markerClicked'] = markerClicked;

			if ( mapObject['locations'][markerClicked.id]['info_window_content']  ) {
					
				if ( mapObject['infoWindow'] ) {
					mapObject['infoWindow'].close();
					mapObject['infoWindow'] = null;
				}
				
				mapObject['infoWindow'] = new google.maps.InfoWindow({
					content: mapObject['locations'][markerClicked.id]['info_window_content']
				});
				
				mapObject['infoWindow'].open(mapObject['map'], markerClicked);
			}
		}
	}
	
	//infobox info display function
	function infoboxInfoDisplay() {

		if ( mapObject['infoBox'] ) {
			mapObject['infoBox'].close();
			mapObject['infoBox'] = null;
		}

		mapObject['infoBox'] = new InfoBox({
			alignBottom : false,
			position: mapObject['map'].getCenter(),
			content: '<div id="gmw-ib-data-loader-'+form['ID']+'" class="gmw-ib-data-loader"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div></div>',
			disableAutoPan: false,
			boxClass:'gmw-ib-template-holder gmw-ib-template-holder-'+form['ID']+' gmw-'+form['prefix']+'-ib-'+form['info_window'][form['info_window']['iw_type']+'_template'].replace('custom_', '')+'-template-holder',
			maxWidth: 150,
			pixelOffset: new google.maps.Size(-140, -250),
			zIndex: null,
			closeBoxMargin: "12px 4px 2px 2px",
			closeBoxURL: '',
			infoBoxClearance: new google.maps.Size(20, 20)
		});
					
		mapObject['infoBox'].open( mapObject['map'], mapObject['markerClicked'] );
		
		jQuery.ajax({
			type       	: "post",
			data  		: {action:'gmw_ps_display_info_window', 'location_info': form['results'][mapObject['markerClicked']['id']], 'form': form },		
			url        	: form['ajaxurl'],
			success:function(data){

				setTimeout(function() {
					jQuery('.gmw-'+mapObject['infoBox']['prefix']+'-ib-close-button, #gmw-close-button-'+form['ID']).click(function() {
                    	mapObject['infoBox'].close();
                    });
            	}, 500);
			
				jQuery('.gmw-ib-template-holder-'+form['ID']).append(data);
				jQuery('#gmw-ib-data-loader-'+form['ID']).fadeToggle();
			
			}
		});
		
		return false;
	}
	
	//popup window info display function
	function popupWindowDisplay() {

		//remove open window		
		jQuery('.gmw-iw-template-holder').remove();
		
		//create new window
		jQuery('<div id="gmw-iw-template-holder" class="open gmw-iw-template-holder gmw-iw-template-holder-'+form['ID']+' gmw-'+form['prefix']+'-iw-'+form['info_window'][form['info_window']['iw_type']+'_template'].replace('custom_', '')+'-template-holder"><div id="gmw-iw-data-loader-'+form['ID']+'" class="gmw-iw-data-loader"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div></div>').appendTo('body');
	
		jQuery('.gmw-iw-template-holder-'+form['ID']).fadeToggle('fast');

		jQuery.ajax({
			type       	: "post",
			data  		: {action:'gmw_ps_display_info_window', 'location_info': form['results'][mapObject['markerClicked']['id']], 'form': form },		
			url        	: form['ajaxurl'],
			success:function(data){
				
				//append data into info window
				jQuery('.gmw-iw-template-holder-'+form['ID']).append(data);
				//hide loader
				jQuery('#gmw-iw-data-loader-'+form['ID']).fadeToggle();
				
				jQuery('.gmw-'+form['prefix']+'-iw-close-button, #gmw-close-button-'+form['ID']).click(function() {
					
					jQuery('.gmw-iw-template-holder').fadeToggle('fast', function(){jQuery('.gmw-iw-template-holder').remove();});

					//remove bounce animation if set
					if ( mapObject['markerClicked'] ) {
						if ( mapObject['markerClicked'].getAnimation() != null ) {
							mapObject['markerClicked'].setAnimation(null);
							mapObject['markerClicked'] = false;
						}
					}	
				});			
			}
		});
	
		return false;
	}
}

jQuery(document).ready(function($){ 	
	
	if ( typeof gmwMapObjects == 'undefined' ) 
		return false;
				
	$.each(gmwMapObjects, function( index, mapObject ) {
		
		if ( mapObject['triggerMap'] == true ) {
		
			//if map element is hidden show it first
			if ( mapObject['hiddenElement'] != false && jQuery(mapObject['hiddenElement']).is(':hidden') ) {
				jQuery(mapObject['hiddenElement']).slideToggle( 'fast', function() {
					gmwMapInit( mapObject );
				});
			} else {
				gmwMapInit( mapObject );
			} 	
		}
	});		
});