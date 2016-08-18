<?php 
/**
 * GMW PT search results function - Custom map icons for "Memebrs Locator"
 * @param $gmw
 * @param $gmw_options
 * @param $gmw_query
 */
function gmw_ps_fl_map_icons( $members_template, $gmw, $settings ) {
	
	//set default map icon usage if not exist
	if ( empty( $gmw['results_map']['map_icon_usage'] ) )
		$gmw['results_map']['map_icon_usage'] = 'same';

	//set default map_icon if not exists
	if ( empty( $gmw['results_map']['map_icon'] ) )
		$gmw['results_map']['map_icon'] = '_default.png';
	 
	//same global map icon
	if ( $gmw['results_map']['map_icon_usage'] == 'same' ) {
		$members_template->member->mapIcon = $gmw['results_map']['map_icon'];
	//per member map icon	
	} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_member' && !empty( $members_template->member->map_icon ) ) {
		$members_template->member->mapIcon = $members_template->member->map_icon;
	//avatar map icon
	} elseif ( $gmw['results_map']['map_icon_usage'] == 'avatar' ) {
		$avatar = bp_core_fetch_avatar( array( 'item_id' => $members_template->member->ID, 'type' => 'thumb', 'width' => 10, 'height' => 10, 'html' => false ) );
		$members_template->member->mapIcon = ( $avatar ) ? $avatar : GMW_PS_URL . '/friends/assets/map-icons/_no_avatar.png';
	//oterwise, default map icon	
	} else {
		$members_template->member->mapIcon = '_default.png';
	}
	
	//generate the map icon
	if ( $gmw['results_map']['map_icon_usage'] != 'avatar' ) {
		$members_template->member->mapIcon = ( empty( $members_template->member->mapIcon ) || $members_template->member->mapIcon == '_default.png' ) ? 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='. $members_template->member->member_count .'|FF776B|000000' : $settings['ml_map_icons']['url'].$members_template->member->mapIcon;
	}

	return $members_template;
}
add_action( 'gmw_fl_modify_member', 'gmw_ps_fl_map_icons', 10, 4 );

/**
 * Modify the map element with map 
 * @param  array $mapElements  the original map element
 * @param  array $gmw          the form being displayed
 * @return array               modifyed map element
 */
function gmw_fl_map_elements( $mapElements, $gmw ) {
		
	$settings = get_option( 'gmw_options' );

	if ( !isset( $gmw['results_map']['your_location_icon'] ) )
		$gmw['results_map']['your_location_icon'] = '_default.png';
	
	//set the user location marker
	$mapElements['userPosition']['mapIcon'] = $settings['ml_map_icons']['url']. $gmw['results_map']['your_location_icon'];
	
	//disable the map control. We will enable each one based on the form settings
	$mapElements['mapOptions'] = array_merge( $mapElements['mapOptions'], array(
			'zoomControl' 	 	 => false,
			'mapTypeControl' 	 => false,
			'streetViewControl'  => false,
			'scrollwheel'		 => false,
			'panControl'		 => false,
			'scaleControl'		 => false,
			'overviewMapControl' => false
	) );

	//resize map element
	$mapElements['resizeMapElement'] = false;

	//enabled map controls
	if ( !empty( $gmw['results_map']['map_controls'] ) ) {

		foreach ( $gmw['results_map']['map_controls'] as $value ) {

			if ( $value == 'resizeMapControl' ) {
				$mapElements['resizeMapElement'] = 'gmw-resize-map-trigger-'.$gmw['ID'];
			} else {
				$mapElements['mapOptions'][$value] = true;
			}
		}
	}

	return $mapElements;		
}
add_filter( "gmw_fl_map_element", 'gmw_fl_map_elements', 10, 2 );

/**
 * Query keywords
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_fl_query_keywords( $gmw ) {
	 
	if ( !isset( $gmw['search_form']['keywords_field'] ) || $gmw['search_form']['keywords_field'] == 'dont' || empty( $_GET[$gmw['url_px'].'keywords'] ) )
		return $gmw;
	 	 
	$gmw['query_args']['search_terms'] = $_GET[$gmw['url_px'].'keywords'];
	 
	return $gmw;
}
add_filter( 'gmw_fl_form_before_members_query', 'gmw_ps_fl_query_keywords', 15, 1 );

/**
 * GMW FL search results function - Display choosen user's address fields
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_member_address_fields( $address, $member, $gmw) {
	
	if ( empty( $gmw['search_results']['address_fields'] ) || $gmw['prefix'] != 'fl' )
		return $address;
	
	if ( count( $gmw['search_results']['address_fields'] ) == 6 ) 
		return $members_template->member->address;
	
	return gmw_get_member_info( array( 'info' => implode( ',', array_keys( $gmw['search_results']['address_fields'] ) ) ) );
	
}
add_filter( 'gmw_location_address', 'gmw_fl_member_address_fields', 10, 3 );

/**
 * GMW FL function - disaply profile fields for each member in results
 */
function gmw_fl_append_xprofile_to_form( $gmw ) {

	if ( empty( $gmw['search_results']['results_profile_fields'] ) && empty( $gmw['search_results']['results_profile_fields_date'] ) ) 
		return $gmw;

	$total_results_xprofile_fields = ( isset( $gmw['search_results']['results_profile_fields'] ) ) ? $gmw['search_results']['results_profile_fields'] : array();
	if ( isset( $gmw['search_results']['results_profile_fields_date'] ) && !empty( $gmw['search_results']['results_profile_fields_date'] ) ) array_unshift( $total_results_xprofile_fields, $gmw['search_results']['results_profile_fields_date'] );
	
	$gmw['search_results']['total_results_xprofile_fields'] = $total_results_xprofile_fields;

	return $gmw;
}
add_filter( 'gmw_fl_form_before_members_query', 'gmw_fl_append_xprofile_to_form', 10, 1 );

/**
 * GMW function - display xprofile fields in results 
 * @param unknown_type $gmw
 */
function gmw_fl_results_xprofile_fields( $gmw ) {
	if ( !empty( $gmw['search_results']['total_results_xprofile_fields'] ) ) {
		gmw_fl_member_xprofile_fields( $gmw, $gmw['search_results']['total_results_xprofile_fields'], 'div' );
	}
}
add_action( 'gmw_fl_search_results_member_items', 'gmw_fl_results_xprofile_fields' );
add_action( 'gmw_fl_directory_member_item', 'gmw_fl_results_xprofile_fields' );