<?php
/**
 * Address Fields
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_address_fields( $address_field, $gmw, $id, $class ) {

	if ( !isset( $gmw['search_form']['address_fields'] ) || $gmw['search_form']['address_fields']['how'] == 'single' )
		return $address_field;

	$id = esc_attr( $gmw['ID'] );

	$address_field = '<div id="gmw-address-fields-wrapper-'.$id.'" class="gmw-address-fields-wrapper gmw-address-fields-wrapper-'.$id.' '.esc_attr( $class ).'">';

	foreach ( $gmw['search_form']['address_fields'] as $key => $field ) {

		if ( $key == 'how' )
			continue;

		//sanitize fields
		array_map( 'esc_attr', $field );

		$address_out   = false;
		$placeholder  = false;
		$am 		   = ( isset( $field['mandatory'] ) ) ? 'mandatory' : '';
		$field_on 	   = ( isset( $field['on'] ) ) ? $field['on'] : false;
		$key 		   = esc_attr( $key );
		$url_px		   = esc_attr( $gmw['url_px'] );

		if ( $field_on == 'default' ) {

			$address_field .=  "<input type='hidden' id='gmw_address_{$key}'  name='{$url_px}address[{$key}]' value='{$field['value']}' />";

		} elseif ( $field_on == 'include' ) {

			$value 		    = ( !empty( $_GET[$url_px.'address'][$key] ) ) ? sanitize_text_field( $_GET[$url_px.'address'][$key] ) : '';
			$address_field .=  "<div id=\"gmw-{$key}-wrapper-{$id}\" class=\"gmw-saf-wrapper gmw-{$key}-wrapper-{$id}\">";

			//create label
			if ( isset( $field['within'] ) ) {
				$placeholder = ( isset( $field['within'] ) ) ? $field['title'] : '';
			} else {
				$address_field .= "<label for='gmw-{$id}-saf-{$key}' class='gmw-field-label'>{$field['title']}</label>";
			} 

			if ( !isset( $field['dropdown'] ) ) {
			
				//input text field				
				$address_field .= "<input type='text' id='gmw-{$id}-saf-{$key}' name='{$url_px}address[{$key}]' class='gmw-saf gmw-saf-{$key} gmw-address {$am}' value='{$value}' size='20' placeholder='{$placeholder}' />";

			} else {

				$dropdown_options = explode(',',$field['drop_values']);
				$address_field   .=  '<select id="gmw-'.$id.'-saf-'.$key.'" name="'.$gmw['url_px'].'address['.$key.']"  class="gmw-'.$key.' gmw-address-field">';

				$selected = ( isset( $_GET[$gmw['url_px'].'address'][$key] ) && $_GET[$gmw['url_px'].'address'][$key] == $va ) ? 'selected="selected"' : '';

				foreach ( $dropdown_options as $option ) :
					$address_field .= "<option value=\"{$option}\" {$selected}>{$va}</option>";
				endforeach;
					
				$address_field .= '</select>';

			}

			$address_field .= '</div>';
		}
	}
	$address_field .= '</div>';

	return $address_field;

}
add_filter( 'gmw_search_form_address_field', 'gmw_ps_address_fields', 10, 4 );

/**
 * GMW function - Display Keywords input
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_get_keywords_field( $gmw ) {

	if ( empty( $gmw['search_form']['keywords_field'] ) || $gmw['search_form']['keywords_field'] == 'dont' ) 
		return;

	$url_px		 = esc_attr( $gmw['url_px'] );
	$value  	 = ( isset( $_GET[$url_px.'keywords'] ) ) ? esc_attr( sanitize_text_field( stripslashes( $_GET[$url_px.'keywords'] ) ) ) : '';
	$label  	 = ( isset( $gmw['search_form']['keywords_title'] ) ) ? esc_attr( $gmw['search_form']['keywords_title'] ) : ''; 
	$placeholder = '';
	$id 		 = esc_attr( $gmw['ID'] );

	$output = '<div class="gmw-form-field-wrapper gmw-keywords-field-wrapper">';

	if ( isset( $gmw['search_form']['keywords_within'] ) ) {
		$placeholder = $label;
	} elseif ( !empty( $label ) ) {
		$output .= "<label for='gmw-keywords-{$id}' class='gmw-field-label'>{$label}</label>";
	}

	$output .= "<input type='text' id='gmw-keywords-{$id}' name='{$url_px}keywords' class='gmw-keywords-field' value='{$value}' placeholder='{$placeholder}' />";
	$output .= '</div>';
	
	return $output;
}

	function gmw_keywords_field( $gmw ) {
		echo gmw_get_keywords_field( $gmw );
	}
	add_action( 'gmw_search_form_before_address', 'gmw_keywords_field' );

/**
 * GMW Premium settings - create slide for radius value
 * @param type $output
 * @param type $gmw
 * @param type $class
 * @param type $btitle
 * @param type $stitle
 * @return string
 */
function search_form_radius_field( $output, $gmw ) {
                
	if ( !isset( $gmw['search_form']['radius_slider']['use'] ) ) 
		return $output;
        
    $initValue  = ( !empty( $_GET[$gmw['url_px'].'distance'] ) ) ? esc_attr( $_GET[$gmw['url_px'].'distance'] ) : esc_attr( $gmw['search_form']['radius_slider']['default_value'] );
    $maxValue   = explode( ",", $gmw['search_form']['radius'] );
    $maxValue   = end( $maxValue );
    $maxValue   = esc_attr( $maxValue );
    $stitle     = ( $gmw['search_form']['units'] == 'imperial' ) ? $gmw['labels']['search_form']['miles'] : $gmw['labels']['search_form']['kilometers'];
    $title      = ( $gmw['search_form']['units']  == 'both' ) 	 ? __( 'Radius: ','GMW-PS' ) : $stitle;
    $id 		= esc_attr( $gmw['ID'] );

    $output  =  '<div class="gmw-radius-slider-wrapper gmw-radius-slider-wrapper-'.$id.'">';
    $output .=      '<label for="gmw-radius-value-field-'.$id.'" class="gmw-field-label">'.esc_attr( $title ).'</label>';
    $output .=      '<input type="text" id="gmw-radius-value-field-'.$id.'" readonly name="'.esc_attr( $gmw['url_px'] ).'distance" class="gmw-radius-value-field gmw-radius-value-field-'.$id.'" style="border:0;padding:0px;margin:0px;background:none;" />';
    $output .=      '<div id="gmw-slider-range-'.$id.'" class="gmw-slider-range gmw-slider-range-'.$id.'"></div>';
    $output .=      "<script>
                       jQuery(function($) {
                           $( '.gmw-slider-range-{$gmw['ID']}' ).slider({
                              range: false,
                               max: '{$maxValue}',
                               values: [{$initValue}],
                               slide: function( event, ui ) {
                                   $( '.gmw-radius-value-field-{$id}' ).val( ui.values[ 0 ] );
                               }
                           });
                           $( '.gmw-radius-value-field-{$id}' ).val( $( '.gmw-slider-range-{$id}' ).slider( 'values', 0 ) );
                       });
                     </script>";
    $output .=  '</div>';

    if ( !wp_script_is( 'jquery-ui-slider', 'enqueued' ) ) {
   		wp_enqueue_script( 'jquery-ui-slider' );
    }

    if ( !wp_style_is( 'ui-comp', 'enqueued' ) ) {
        wp_enqueue_style( 'ui-comp' );
    }
           
    return $output;
}
add_filter( 'gmw_radius_dropdown_output', 'search_form_radius_field', 10, 3 );

/**
 * GMW function - info windows scripts and styles
 */
function gmw_ps_register_scripts( $form, $settings ) {
		
	$iwType = ( !isset( $form['info_window']['iw_type'] ) || $form['info_window']['iw_type']  == 'infobox' ) ? 'infobox' :'popup';

	if ( empty( $form['info_window'][$iwType.'_template'] ) )
		$form['info_window'][$iwType.'_template'] = 'default';
	
	$stylesheet = $form['info_window'][$iwType.'_template'];
	$folders	= apply_filters( 'gmaps_iw_stylesheet_folder', array(
			'pt'  => 'posts',
			'fl'  => 'friends'
	));

    if ( empty( $folders[$form['prefix']] ) ) 
        return;
    
	//Load custom info-window stylesheet from child/theme folder
	if ( strpos( $stylesheet, 'custom_' ) !== false ) {

		$stylesheet  = str_replace( 'custom_', '', $stylesheet );
		$style_title = "gmw-{$form['ID']}-{$form['prefix']}-{$iwType}-{$stylesheet}";
		$style_url 	 = get_stylesheet_directory_uri()."/geo-my-wp/{$folders[$form['prefix']]}/info-window-templates/{$iwType}/{$stylesheet}/css/style.css";

	} else {
		$style_title = "gmw-{$form['ID']}-{$form['prefix']}-{$iwType}-{$stylesheet}";
		$style_url 	  = GMW_PS_URL."/{$folders[$form['prefix']]}/templates/{$iwType}/{$stylesheet}/css/style.css";
	}

	if ( !wp_style_is( $style_title, 'enqueued' ) ) {
		wp_enqueue_style( $style_title, $style_url );
	}
}
add_action( 'gmw_form_map_element', 'gmw_ps_register_scripts' , 10, 2 );

/**
 * GMW PS function - Modify address when activity updated
 */
function gmw_fl_activity_address_fields( $activity_address, $address ) {

	//get ML options
	$ml_options = gmw_get_options_group('members_locator');

	if ( empty( $ml_options['activity_update_fields'] ) ) 
		return $activity_address;

	$activity_array = array();
	
	foreach ( $ml_options['activity_update_fields'] as $key => $field ) {
		if ( $key == 'state') {
			$activity_array[] = $address['gmw_state'];
		} elseif ( $key == 'country') {
			$activity_array[] = $address['gmw_country'];
		} else {
			$activity_array[] = $address['gmw_'.$key];
		}
	}

	$activity_address = implode( ' ', $activity_array );

	return $activity_address;

}
add_filter( 'gmw_fl_activity_address_fields','gmw_fl_activity_address_fields', 10, 2 );
add_filter( 'gmw_xf_address_activity_update','gmw_fl_activity_address_fields', 10, 2 );

/**
 * GMW PS function - Modify address fields in location tab of the logged in user
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_location_tab_fields( $fields ) {

	//get ML options
    $ml_options = gmw_get_options_group('members_locator');

    if ( empty( $ml_options['location_tab_fields_loggedin'] ) ) 
    	return $fields;
    
    foreach ( $fields['address_fields'][1] as $key => $field ) {
        if ( !array_key_exists( $field['name'], $ml_options['location_tab_fields_loggedin'] ) ) {
            $fields['address_fields'][1][$key]['type'] = 'hidden';    
        }    
    }
   
    return $fields;
}
add_filter( 'gmw_fl_location_page', 'gmw_fl_location_tab_fields', 10, 3 );

/**
 * Modify the address field of the Location tab for the  displayed
 * @param  string $content single location shortcode
 * @return string          single location shortcode
 */
function gmw_fl_displayed_user_location_tab_content( $content ) {
    
    //get ML options
    $ml_options = gmw_get_options_group('members_locator');

    //hide the address if no address selected
    if ( empty( $ml_options['location_tab_fields_displayed'] ) ) {

    	if ( class_exists( 'GMW_Single_Member_Location' ) ) {
    		$content = '[gmw_single_location item_type="member" elements="map" map_height="300px" map_width="100%" user_map_icon="0"]';
    	} else {
    		$content = '[gmw_member_location address="0" map_height="300px" map_width="100%" no_location="1"]';
    	}

    //if all fields or full address selected show formatted address
    } elseif ( count( $ml_options['location_tab_fields_displayed'] ) == 6 || $ml_options['location_tab_fields_displayed'] == 'address' ) { 
        
        if ( class_exists( 'GMW_Single_Member_Location' ) ) {
    		$content = '[gmw_single_location item_type="member" elements="address,map" map_height="300px" map_width="100%" user_map_icon="0"]';
    	} else {
    		$content = '[gmw_member_location address_fields="address" map_height="300px" map_width="100%" no_location="1"]';
    	}

    } else {

    	$address_fields = implode( ',', array_keys( $ml_options['location_tab_fields_displayed'] ) );

    	if ( class_exists( 'GMW_Single_Member_Location' ) ) {
    		$content = '[gmw_single_location item_type="member" elements="address,map" map_height="300px" address_fields="'.$address_fields.'" map_width="100%" user_map_icon="0"]';
    	} else {
    		$content = '[gmw_member_location address_fields="'. $address_fields.'" map_height="300px" map_width="100%" no_location="1"]';
    	}
    }
    
    return $content;
}
add_filter( 'gmw_fl_user_location_tab_content', 'gmw_fl_displayed_user_location_tab_content', 10, 3 );

/**
 * GMW FL function - add address fields checkboxes to "Single member" widget
 * @param $id_base
 * @param $number
 * @param $instance
 */
function gmw_fl_single_member_widget_admin_address_fields( $id_base, $number, $instance ) {
	
	$fieldsArray = array( 
			'street' 	=> __( 'Street',  'GMW-PS' ), 
			'city'		=> __( 'City',    'GMW-PS' ), 
			'state'		=> __( 'State',   'GMW-PS' ), 
			'zipcode'	=> __( 'Zipcode', 'GMW-PS' ), 
			'country'	=> __( 'Country', 'GMW-PS' )
	);
	
    $fields  = '<p>';
    $fields .= '<label>'. __( 'Address Fields:', 'GMW-PS' ) .'</label><br />';
    
    foreach ( $fieldsArray as $name => $title ) {
    	$checked = ( isset( $instance['address_fields'] ) && in_array( $name, $instance['address_fields'] ) ) ? 'checked="checked"' : '';  	
    	$fields .= '<input type="checkbox" value="'.esc_attr( $name ).'"  name="widget-'. esc_attr( $id_base ).'['.$number.'][address_fields][]" '.$checked.' width="25" style="float: left;margin-right:10px" />'.esc_attr( $title ). '<br />';
    }
    $fields .= '</p>';
    
    echo $fields;
}
add_action('gmw_fl_single_member_widget_admin_after_map_type','gmw_fl_single_member_widget_admin_address_fields', 10, 3);

/**
 * GMW function - add map icons tab to member location page
 * @return type
 */
function gmw_fl_map_icons_tab() {
    $gmw_options = get_option('gmw_options');
    
    if ( !isset( $gmw_options['members_locator']['per_member_icon'] ) ) 
    	return;
    ?>
    <li id="gmw-yl-map-icons-tab" class="gmw-yl-tab" ><?php _e('Map Icons','GMW-PS'); ?></li>
    <?php
}
add_action( 'gmw_yl_tabs_end', 'gmw_fl_map_icons_tab' );

/**
 * GMW FL function - display map's icon in location tab
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_per_member_icons_display( $member_location ) {
    
    $gmw_options = get_option('gmw_options');
    if ( !isset( $gmw_options['members_locator']['per_member_icon'] ) ) return;
    
    ?>
    <div id="gmw-yl-map-icons-tab-wrapper" class="gmw-yl-tab-wrapper map-icons" style="display:none;">
        <table>
            <tbody>
                <tr>
                    <td style="padding:10px">
                    <?php
                    $map_icons = glob(STYLESHEETPATH. '/geo-my-wp/friends/map-icons/*.png');
                    if ( !empty($map_icons) ) {
                    	$display_icon = get_stylesheet_directory_uri() . '/geo-my-wp/friends/map-icons/';
                    	$cic = 1;
                    	foreach ($map_icons as $map_icon) {
                    		if  ( substr( basename($map_icon), 0, 1) != '_') {
                    			echo '<span><input type="radio" name="gmw_map_icon" value="'.esc_attr( basename( $map_icon ) ).'"'; if ( ( isset( $member_location->map_icon ) && $member_location->map_icon == basename($map_icon) ) || $cic == 1 ) echo 'checked="checked"'; echo ' />
                    			<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
                    			$cic++;
                    		}
                    	}
                    } else {
                    	$map_icons = glob(GMW_PS_PATH . '/friends/assets/map-icons/*.png');
                    	$display_icon = GMW_PS_URL . '/friends/assets/map-icons/';
                    	$cic = 1;
                    	foreach ($map_icons as $map_icon) {
                    		if  ( substr( basename($map_icon), 0, 1) != '_') {
                    			echo '<span><input type="radio" name="gmw_map_icon" value="'.esc_attr( basename( $map_icon ) ).'"'; if ( ( isset( $member_location->map_icon ) && $member_location->map_icon == basename($map_icon) ) || $cic == 1 ) echo 'checked="checked"'; echo ' />
                    			<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
                    			$cic++;
                    		}
                    	}
                    }
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php 
}
add_action( 'gmw_yl_after_tabs_wrapper', 'gmw_fl_per_member_icons_display', 10 );

/**
 * GMW FL function - display map's icon per member in Buddypress's profile page
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_fl_per_member_icons_display_profile() {
    global $bp, $wpdb;
    $addons      = get_option( 'gmw_addons' );
    $gmw_options = get_option( 'gmw_options' );
    
    if ( !isset( $gmw_options['members_locator']['per_member_icon'] ) || !isset( $addons['xprofile_fields'] ) || $addons['xprofile_fields'] != 'active' ) 
    	return;

    if ( bp_is_user_profile_edit()  ) {

            //$member_icon[0] = '_default';	
        $member_icon = $wpdb->get_col( $wpdb->prepare("SELECT `map_icon` FROM wppl_friends_locator WHERE member_id = %s", $bp->loggedin_user->id), 0 ); 
        $member_icon = ( isset( $member_icon[0] ) && !empty( $member_icon[0] ) ) ? $member_icon[0] : '_default.png';
        ?>
        <div class="edit-profile-icons-wrapper">
            <label><?php _e( "Choose your map icon", "GMW-PS"); ?></label>
            <?php 
                $map_icons = glob(STYLESHEETPATH. '/geo-my-wp/friends/map-icons/*.png');
                
                if (  !empty( $map_icons ) ) {
                	$display_icon = get_stylesheet_directory_uri() . '/geo-my-wp/friends/map-icons/';
                	$cic = 1;
                	foreach ( $map_icons as $map_icon ) {
                		if  ( substr( basename($map_icon), 0, 1) != '_') {
                			echo '<span><input type="radio" name="gmw_map_icon" value="'.basename($map_icon).'"'; if ( ( isset( $member_icon ) && $member_icon == basename($map_icon) ) || $cic == 1 ) echo 'checked="checked"'; echo ' />
                			<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
                			$cic++;
                		}
                	}
                } else {
                	$map_icons = glob(GMW_PS_PATH . '/friends/assets/map-icons/*.png');
                	$display_icon = GMW_PS_URL . '/friends/assets/map-icons/';
                	$cic = 1;
                	foreach ($map_icons as $map_icon) {
                		if  ( substr( basename($map_icon), 0, 1) != '_') {
                			echo '<span><input type="radio" name="gmw_map_icon" value="'.basename($map_icon).'"'; if ( ( isset( $member_icon ) && $member_icon == basename($map_icon) ) || $cic == 1 ) echo 'checked="checked"'; echo ' />
                			<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
                			$cic++;
                		}
                	}
                }
            ?>
        </div>
    <?php  }
}
add_action('bp_after_profile_field_content', 'gmw_fl_per_member_icons_display_profile', 10, 2); 

/**
 * GMW function - display no results message and links
 * @param $no_results
 * @param $gmw
 *
 * This function filter the no results message when displayed dynamically. It might be 
 * removed in the future in favor of the new no results tempalte file.
 * 
 */
function gmw_ps_no_results( $no_results, $gmw ) {

	$no_results = false;

	$no_results =  '<div class="gmw-no-results-wrapper gmw-'.esc_attr( $gmw['prefix'] ).'-no-results-wrapper">';
	$no_results .= '<p>';
	$no_results .= ( !empty( $gmw['no_results']['title'] ) ) ? esc_attr( $gmw['no_results']['title'] ) : esc_attr( $gmw['labels']['search_results'][$gmw['prefix'].'_no_results'] );
	$no_results .= '</p>';

	if ( isset( $gmw['no_results']['wider']['on']) || isset( $gmw['no_results']['all']['on'] ) ) {

		$no_results .= '<div id="gmw-no-results-links-wrapper"><p>';

		if ( isset( $gmw['no_results']['wider']['on'] ) ) {
			$url = add_query_arg( 
					array( 
						'gmw_distance' => $gmw['no_results']['wider']['radius'], 
						'gmw_lat' 	   => $gmw['your_lat'], 
						'gmw_lng' 	   => $gmw['your_lng'] 
					));
			$no_results .= '<span id="gmw-wider">'. esc_attr( $gmw['no_results']['wider']['before'] ).' <a href="'. esc_url( $url ).'" onclick="document.gmw_form.submit();">'. esc_attr( $gmw['no_results']['wider']['link_title'] ) .'</a> '. esc_attr( $gmw['no_results']['wider']['after'] ).'</span>';
		}

		if ( isset( $gmw['no_results']['all']['on'] ) ) {
			$no_results .= '<span id="gmw-all-results">'.esc_attr( $gmw['no_results']['all']['before'] ).' <a href="'.esc_url ( add_query_arg('gmw_address', array('value' => '') ) ). '" onclick="document.gmw_form.submit();">'.esc_attr( $gmw['no_results']['all']['link_title'] ).' </a> '. esc_attr( $gmw['no_results']['all']['after'] ).' </span>';
		}
			
		$no_results .= '</p></div>';
	}

	$no_results .= '</div>';

	return $no_results;
}
add_filter( 'gmw_pt_no_results_message', 'gmw_ps_no_results' , 10 ,2 );
add_filter( 'gmw_fl_no_results_message', 'gmw_ps_no_results' , 10 ,2 );

/**
 * Modify the no results message when using the no results template file
 * @param  string $message no result message
 * @param  array  $gmw     the form being used
 * @return string          modified no results message
 */
function gmw_ps_no_results_template_message( $message, $gmw ) {

	if ( !empty( $gmw['no_results']['title'] ) ) {
		$message = $gmw['no_results']['title'];
	}
	return $message;
}
add_filter( "gmw_no_results_template_message", 'gmw_ps_no_results_template_message', 10, 2 );


function gmw_ps_no_results_links( $gmw ) {

	if ( !isset( $gmw['no_results']['wider']['on']) && !isset( $gmw['no_results']['all']['on'] ) )
		return;

	$output = '<div id="gmw-no-results-links-wrapper"><p>';

	if ( isset( $gmw['no_results']['wider']['on'] ) ) {
		$url = add_query_arg( 
				array( 
					'gmw_distance' => $gmw['no_results']['wider']['radius'], 
					'gmw_lat' 	   => $gmw['your_lat'], 
					'gmw_lng' 	   => $gmw['your_lng'] 
				));
		$output .= '<span id="gmw-wider">'.esc_attr( $gmw['no_results']['wider']['before'] ).' <a href="'. esc_url( $url ).'" onclick="document.gmw_form.submit();">'.esc_attr( $gmw['no_results']['wider']['link_title'] ).'</a> '. esc_attr( $gmw['no_results']['wider']['after'] ).'</span>';
	}

	if ( isset( $gmw['no_results']['all']['on'] ) ) {
		$output .= '<span id="gmw-all-results">'.esc_attr( $gmw['no_results']['all']['before'] ).' <a href="'.esc_url ( add_query_arg('gmw_address', array('value' => '') ) ). '" onclick="document.gmw_form.submit();">'.$gmw['no_results']['all']['link_title'].' </a> '. $gmw['no_results']['all']['after'].' </span>';
	}

	$output .= '</p></div>';

	echo $output;
}
add_action( 'gmw_no_results_template_end', 'gmw_ps_no_results_links', 10 );