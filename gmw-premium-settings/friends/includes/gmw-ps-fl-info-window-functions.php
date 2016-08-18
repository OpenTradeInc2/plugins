<?php
/**
 * GMW PS function - get information of the displayed member
 * @param unknown_type $gmw
 * @param unknown_type $member_info
 */
//function gmw_ps_fl_load_template( $gmw, $member_location ) {

/**
 * load info window template file
 * @var unknown_type
 */
if ( bp_has_members( array( 'include' => array( $location['ID'] ) ) ) ) {

	while ( bp_members() ) : bp_the_member();
	global $members_template;
	
	//merge member location wth member object
	$member = (object) array_merge( (array)$location, (array)$members_template->member );

	//modify form and member		
	$member = apply_filters( 'gmw_fl_member_before_info_window', $member, $gmw );
	$gmw    = apply_filters( 'gmw_fl_form_before_info_window'  , $gmw, $member );
	
	$iwType = ( $gmw['info_window']['iw_type']  == 'infobox' ) ? 'infobox' :'popup';
	
	do_action( 'gmw_fl_before_info_window', $gmw, $member );

	/**
	 * load info window template file
	 * @var unknown_type
	 */
	if ( strpos( $gmw['info_window'][$iwType.'_template'], 'custom_' ) !== false ) {
		include( STYLESHEETPATH. '/geo-my-wp/friends/info-window-templates/'.$iwType.'/'.str_replace( 'custom_','',$gmw['info_window'][$iwType.'_template'] ).'/content.php' );
		//get stylesheet and results template from plugin's folder
	} else {
		include GMW_PS_PATH . '/friends/templates/'.$iwType.'/'.$gmw['info_window'][$iwType.'_template'].'/content.php';
	}
	
	do_action( 'gmw_fl_after_info_window', $gmw, $member );
	
	endwhile;
}

//}
//add_action( 'gmw_ps_fl_info_window_display', 'gmw_ps_fl_load_template', 10, 2 );