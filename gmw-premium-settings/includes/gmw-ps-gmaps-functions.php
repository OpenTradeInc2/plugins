<?php
/**
 * custom map icons for Posts Global Maps
 * @param type $gmw
 * @return type
 */
function gmaps_posts_map_icons( $gmw ) {

	//get the icons url
	$icons_url = gmw_get_option( 'pt_map_icons', 'url' );

	//Custom user location icon for Posts Global Maps
	if ( isset( $gmw['results_map']['your_location_icon'] ) ) {
		$gmw['ul_icon'] = $icons_url.$gmw['results_map']['your_location_icon'];
	}

	//abort if no map icons usage set
	if ( empty( $gmw['results_map']['map_icon_usage'] ) ) 
		return $gmw;

	$settings 	 = gmw_get_options_group();
	$pt_settings = $settings['post_types_settings'];
	
	//reset the posts count
	$gmw['post_count'] = 1;
	
	if ( empty( $gmw['results_map']['map_icon'] ) ) {
		$gmw['results_map']['map_icon'] = '_default.png';
	}

	/**
	 * Loop through posts and add custom map icons to each one
	 */
	foreach ( $gmw['results'] as $key => $post ) {
		 
		if ( empty( $post->mapIcon ) ) {
			$post->mapIcon = '';
		}

		//if showing same map icon
		if ( empty( $gmw['results_map']['map_icon_usage'] ) || $gmw['results_map']['map_icon_usage'] == 'same' ) {
		
			$post->mapIcon = $gmw['results_map']['map_icon'];

		//per post map icon
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_post' && !empty( $post->map_icon ) ) {

			$post->mapIcon = $post->map_icon;
		
		//per post type map icons
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_post_type' && !empty( $pt_settings['post_types_icons'][$post->post_type] ) ) {
			
			$post->mapIcon = $pt_settings['post_types_icons'][$post->post_type];
			
		//per category map icons
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_category' && !empty( $pt_settings['per_category_icons']['taxonomies'] ) ) {
							
			//if using same icons for category and for map grab it from the category icons folder
			if ( isset( $pt_settings['per_category_icons']['same_icons'] ) && !empty( $settings['pt_category_icons']['set_icons'] ) ) {

				$category_icons = $settings['pt_category_icons']['set_icons'];
				$icons_url   	= $settings['pt_category_icons']['url'];

			} elseif ( !isset( $pt_settings['per_category_icons']['same_icons'] ) && !empty( $settings['pt_map_icons']['set_icons'] ) ) {

				$category_icons = $settings['pt_map_icons']['set_icons'];
				$icons_url   	= $settings['pt_map_icons']['url'];
			} else {

				$post->mapIcon = '_default.png';
			}
				
			if ( $post->mapIcon != '_default.png' ) {

				$orderby = ( !empty( $pt_settings['per_category_icons']['terms_orderby'] ) ) ? $pt_settings['per_category_icons']['terms_orderby'] : 'term_id';
				$order   = ( !empty( $pt_settings['per_category_icons']['terms_order']   ) ) ? $pt_settings['per_category_icons']['terms_order']   : 'ASC';

				$post_term_ids = wp_get_object_terms( 
						$post->ID, $pt_settings['per_category_icons']['taxonomies'], 
						apply_filters( 'gmw_pt_post_category_icons_args', array( 
							'fields'  => 'ids', 
							'orderby' => $orderby, 
							'order'   => $order
						), $post, $gmw, $settings )
				);

				if ( is_wp_error( $post_term_ids ) ) {
					$post->mapIcon = '_default.png';
				} else {
					$post->mapIcon = ( !empty( $post_term_ids[0] ) && !empty( $category_icons[$post_term_ids[0]] ) ) ? $category_icons[$post_term_ids[0]]: '_default.png';
				}
			}
			
		//map icon as featured image
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'image' ) {

			if ( has_post_thumbnail( $post->ID ) ) {

				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( 30,30 ) );
				$post->mapIcon = $thumb[0];

			} else {
				$post->mapIcon = GMW_PS_URL.'/posts/assets/map-icons/_no_image.png';
			}
		} else {
			$post->mapIcon = '_default.png';
		}

		$post->post_count = $gmw['post_count'];
		$gmw['post_count']++;

		if ( $gmw['results_map']['map_icon_usage'] != 'image' ) {
			$post->mapIcon = ( empty( $post->mapIcon ) || $post->mapIcon == '_default.png' ) ? 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='. $post->post_count .'|FF776B|000000' : $icons_url.$post->mapIcon;
		}
	}
	 
	return $gmw;
}
add_filter( 'gmaps_gmpt_after_posts_query', 'gmaps_posts_map_icons' );

/**
 * Custom Members Locator map icons
 * @param $gmw
 * @param $gmw_options
 * @param $gmw_query
 */
function gmaps_members_map_icons( $gmw ) {

	//get the icons url
	$icons_url = gmw_get_option( 'ml_map_icons', 'url' );

	//Custom "Your Location" icon for Posts Global Maps
	if ( isset( $gmw['results_map']['your_location_icon'] ) ) {
		$gmw['ul_icon'] = $icons_url.$gmw['results_map']['your_location_icon'];
	}

	//abort if no map icons usage set
	if ( empty( $gmw['results_map']['map_icon_usage'] ) ) 
		return $gmw;
	
	//reset the members count
	$gmw['member_count'] = 1;

	/**
	 * Loop through members and add custom map icons to each one
	 */
	foreach ( $gmw['results'] as $key => $member ) {

		//if ( empty( $member->mapIcon ) ) {
		//	$member->mapIcon = '';
		//}

		//if same global icon
		if ( $gmw['results_map']['map_icon_usage'] == 'same' ) {

			$member->mapIcon = $gmw['results_map']['map_icon'];	

		//per member icon
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_member' ) {

			$member->mapIcon = $member->map_icon;		

		//avatar icons
		} elseif ( $gmw['results_map']['map_icon_usage'] == 'avatar' ) {	

			$member->mapIcon = ( bp_get_user_has_avatar( $member->ID ) ) ? bp_core_fetch_avatar( array( 'item_id' => $member->ID, 'type' => 'thumb', 'width' => 10, 'height' => 10, 'html' => false, 'no_grav' => true ) ) : GMW_PS_URL . '/friends/assets/map-icons/_no_avatar.png';
		}

		//apply member count
		$member->member_count = $gmw['member_count'];
		$gmw['member_count']++;

		if ( $gmw['results_map']['map_icon_usage'] != 'avatar' ) {
			$member->mapIcon = ( empty( $member->mapIcon ) || $member->mapIcon == '_default.png' ) ? 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='. $member->member_count .'|FF776B|000000' : $icons_url.$member->mapIcon;
		}
	}
	
	return $gmw;
}
add_filter( 'gmaps_gmfl_after_members_query', 'gmaps_members_map_icons' );