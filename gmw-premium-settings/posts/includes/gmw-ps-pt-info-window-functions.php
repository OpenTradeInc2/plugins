<?php
/**
 * GMW PS posts function - load info window information
 * @param unknown_type $gmw
 * @param unknown_type $post_info
 */
//function gmw_ps_pt_load_template( $gmw, $location ) {
	
global $post;

//get additional post information
$post = (object) array_merge( ( array ) $location, get_post( $location['ID'], ARRAY_A ) );

//do some hooking
$post = apply_filters( 'gmw_pt_post_before_info_window', $post, $gmw );
$gmw  = apply_filters( 'gmw_pt_form_before_info_window', $gmw, $post );
	
do_action( 'gmw_pt_before_info_window', $gmw, $post );

/**
 * load info window template file
 * @var unknown_type
 */
$iwType = ( $gmw['info_window']['iw_type']  == 'infobox' ) ? 'infobox' :'popup';

//get custom info window template file
if( strpos( $gmw['info_window'][$iwType.'_template'], 'custom_' ) !== false ) {
	include( STYLESHEETPATH. '/geo-my-wp/posts/info-window-templates/'.$iwType.'/'.str_replace( 'custom_','',$gmw['info_window'][$iwType.'_template'] ).'/content.php' );
//get stylesheet and results template from plugin's folder
} else {
	include GMW_PS_PATH . '/posts/templates/'.$iwType.'/'.$gmw['info_window'][$iwType.'_template'].'/content.php';
}

do_action( 'gmw_pt_after_info_window', $gmw, $post );
//}
//add_action( 'gmw_ps_pt_info_window_display', 'gmw_ps_pt_load_template', 10, 2 );