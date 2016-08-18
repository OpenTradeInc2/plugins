function gmwCalcRoute( gmwFormId, formId ) {

	//remove directions if exists from another window
	if ( typeof directionsDisplay !== 'undefined' ) {
		directionsDisplay.setMap(null);
	}
	
	//var directionsDisplay;
	directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: false});		
	directionsService = new google.maps.DirectionsService();
	
	directionsDisplay.setMap(gmwMapObjects[gmwFormId]['map']);
	directionsDisplay.setPanel(document.getElementById('directions-panel-wrapper-'+formId));
	jQuery('#directions-panel-wrapper-'+formId).html('');

  	var start 	      = jQuery('#gmw-directions-start-point-'+formId).val();
  	var end 	      = jQuery('#gmw-directions-end-coords-'+formId).val();
	var travelMode    = jQuery('#travel-mode-options-'+formId+' li a.active').attr('id');
	var unitsSystem   = jQuery('#gmw-get-directions-form-'+formId+' .unit-system-trigger:checked').val();
	var avoidHighways = ( jQuery('#route-avoid-highways-trigger-'+formId).is(':checked') ) ? true : false;
	var avoidTolls 	  = ( jQuery('#route-avoid-tolls-trigger-'+formId).is(':checked') ) ? true : false;
	
	var request = {
		origin: start,
		destination: end,
		travelMode: google.maps.TravelMode[travelMode],
		unitSystem: google.maps.UnitSystem[unitsSystem],
		provideRouteAlternatives:true,
		avoidHighways:avoidHighways,
		avoidTolls:avoidTolls
	};

  	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
	  		directionsDisplay.setDirections(response);		      					      		
		} else {
	      // alert an error errorMessage when the route could nog be calculated.
	      if (status == 'ZERO_RESULTS') {
	    	  var errorMessage = 'No route could be found between the origin and destination.';
	      } else if (status == 'UNKNOWN_ERROR') {
	    	  var errorMessage = 'A directions request could not be processed due to a server error. The request may succeed if you try again.';
	      } else if (status == 'REQUEST_DENIED') {
	    	  var errorMessage = 'This webpage is not allowed to use the directions service.';
	      } else if (status == 'OVER_QUERY_LIMIT') {
	    	  var errorMessage = 'The webpage has gone over the requests limit in too short a period of time.';
	      } else if (status == 'NOT_FOUND') {
	    	  var errorMessage = 'At least one of the origin, destination, or waypoints could not be geocoded.';
	      } else if (status == 'INVALID_REQUEST') {
	    	  var errorMessage = 'The DirectionsRequest provided was invalid.';         
	      } else {
	    	  var errorMessage = "There was an unknown error in your request. Requeststatus: nn"+status;
	      }
	
	      jQuery('#directions-panel-wrapper-'+formId).html('<div id="error-message">'+errorMessage+'</div>');
		}
  	});
}
jQuery(document).ready(function($) {
	//gmwCalcRoute();

	$(document).on('click', '.get-directions-submit', function(e) {
		e.preventDefault();
		formId 	  = $(this).closest('form').find('.gmw-directions-form-id').val();
		gmwFormId = $(this).closest('form').find('.gmw-form-id').val();
		gmwCalcRoute( gmwFormId, formId );
	});
	
	$(document).on('keypress', '.gmw-directions-start-point', function(e) {
		if (e.keyCode == 13){	
			e.preventDefault();	
		    formId 	  = $(this).closest('form').find('.gmw-directions-form-id').val();
			gmwFormId = $(this).closest('form').find('.gmw-form-id').val();
			gmwCalcRoute( gmwFormId, formId );
	    }
	});

	$(document).on('click', '.travel-mode-options a.travel-mode-trigger', function(e) {		
		e.preventDefault();
		$('.travel-mode-options li a').removeClass('active');
		$(this).addClass('active');
		formId 	  = $(this).closest('form').find('.gmw-directions-form-id').val();
		gmwFormId = $(this).closest('form').find('.gmw-form-id').val();
		gmwCalcRoute( gmwFormId, formId );
	});

	$(document).on('change', '.unit-system-options .unit-system-trigger', function(e) {		
		formId 	  = $(this).closest('form').find('.gmw-directions-form-id').val();
		gmwFormId = $(this).closest('form').find('.gmw-form-id').val();
		gmwCalcRoute( gmwFormId, formId );
	});

	$(document).on('click', '.gmw-iw-close-button', function(e) {	
		if ( typeof directionsDisplay !== 'undefined' ) {
			directionsDisplay.setMap(null);
		}
	});

	$(document).on('click', '.route-avoid-trigger', function(e) {		
		formId 	  = $(this).closest('form').find('.gmw-directions-form-id').val();
		gmwFormId = $(this).closest('form').find('.gmw-form-id').val();
		gmwCalcRoute( gmwFormId, formId );
	});
});