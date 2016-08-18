<?php
class GMW_PS_Per_Post_Map_Icon {
	
	function __construct() {
		
		$this->settings  = get_option( 'gmw_options' );
		
		add_action( 'admin_menu', array( $this, 'create_map_icons' ) );
		add_filter( 'gmw_gf_field_buttons', array( $this, 'add_map_icons_to_gf' ), 15, 2 );
	}
	
	public function create_map_icons() {
	
		if ( $this->settings['post_types_settings']['post_types'] && current_user_can( 'manage_options' ) )
			foreach( $this->settings['post_types_settings']['post_types'] as $page )
				add_meta_box('gmw-map-icons-meta-box', __( 'GMW Map Icons', 'GMW-PS '), array( $this, 'display_map_icons_box' ), $page, 'normal', 'high');
				
	}
	
	public function display_map_icons_box() {
		
		global $wpdb, $post;
		
		$mapIcon = $wpdb->get_col( $wpdb->prepare( "SELECT `map_icon` FROM " . $wpdb->prefix . "places_locator WHERE post_id = %d", $post->ID ), 0 );
		$mapIcon = ( isset( $mapIcon ) && !empty( $mapIcon ) ) ? $mapIcon[0] : '_default.png';

		echo 		'<table>';
		echo 			'<tr>';
		echo 				'<th style="width:20%;vertical-align: top;text-align: left;"><label for="gmw-map-icons-post">'. __('Map\'s Icons:','GMW-PS') . '</label></th>';
		echo				'<td>';
		$map_icons = glob(STYLESHEETPATH. '/geo-my-wp/posts/map-icons/*.png');
		if ( isset($map_icons) && !empty($map_icons) ) :
			$cic = 1;
			$display_icon = get_stylesheet_directory_uri() . '/geo-my-wp/posts/map-icons/';
			foreach ($map_icons as $map_icon) :
				echo '<span><input type="radio" name="gmw_map_icon" value="'.basename($map_icon).'"'; echo ( ( $mapIcon == basename( $map_icon ) ) || $cic == 1) ? "checked" : ""; echo ' />
				<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
				$cic++;
			endforeach;
		else :
			$map_icons      = glob(GMW_PS_PATH . '/posts/assets/map-icons/*.png');
			$display_icon   = GMW_PS_URL . '/posts/assets/map-icons/';
			$cic = 1;
			foreach ($map_icons as $map_icon) :
				echo '<span style=""><input type="radio" name="gmw_map_icon" value="'.basename($map_icon).'"'; echo ( ( $mapIcon == basename($map_icon) ) || $cic == 1) ? "checked" : ""; echo ' />
				<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
				$cic++;
			endforeach;
		endif;
		
		echo				'</td>';
		echo 			'</tr>';
		echo		'</table>';
	}
	
	/**
	 * Create Map icons for Gravity Forms in backend
	*/
	function map_icons_to_gf( $gmw_fields, $field_groups ) {
	
		$gmw_fields[] = array(
				"class"		=> "button",
				"value" 	=> __("GMW Map Icons", "GMW-PS"),
				"onclick" 	=> "StartAddField('mapIcons');"
		);
		return $gmw_fields;
	}
	
}
new GMW_PS_Per_Post_Map_Icon;