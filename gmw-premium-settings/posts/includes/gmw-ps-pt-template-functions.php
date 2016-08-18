<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * GMW function - Premium search form taxonomies
 * @param unknown_type $pass
 * @param unknown_type $gmw
 * @param unknown_type $tag
 * @param unknown_type $class
 * @return void|boolean
 */
function gmw_ps_search_form_taxonomies( $pass, $gmw, $tag, $class ) {

	if ( empty( $gmw['search_form']['taxonomies'] ) )
		return;

	if ( empty( $gmw['search_form']['post_types'] ) || count( $gmw['search_form']['post_types'] ) != 1 )
		return;

	$postType = $gmw['search_form']['post_types'][0];

	if ( empty( $gmw['search_form']['taxonomies'][$postType] ) )
		return;

	$orgTag = $tag;
	if ( $orgTag == 'ul' ) {
		echo '<ul>';
		$tag = 'li';
	} elseif ( $orgTag == 'ol') {
		echo '<ol>';
		$tag = 'li';
	}

	$class  	= esc_attr( $class );
	$chosen_set = false;

	//Display taxonomies
	foreach ( $gmw['search_form']['taxonomies'][$postType] as $tax_name => $values ) {

		//no need to display if pre_defined
		if ( empty( $values['style'] ) || $values['style'] == 'pre_defined' )
			continue;
		
		//set some default args
		$gmw_settings	 = gmw_get_options_group( 'gmw_options' );
		$tax_name 		 = esc_attr( $tax_name );	
		$taxonomy 		 = get_taxonomy( $tax_name );
		$values['label'] = ( !empty( $values['label'] ) ) ? esc_attr( $values['label'] ) : $taxonomy->labels->name;
		$cat_icons	  	 = ( $values['style'] == 'checkbox' && isset( $values['cat_icons'] ) ) ? $gmw_settings['pt_category_icons']['set_icons'] : false;
		$hierarchical 	 = ( is_taxonomy_hierarchical( $tax_name ) ) ? true : false;

		//support older versions of the add-on.
		if ( $values['style'] == 'check' ) {
			$values['style'] = 'checkbox';
		} elseif ( $values['style'] == 'drop' ) {
			$values['style'] = 'dropdown';
		}

		$values['style'] = esc_attr( $values['style'] );

		//set taxonomy args
		$args = apply_filters( 'gmw_ps_'.$values['style'].'_taxonomy_args', array(
			    'orderby'           => ( !empty( $values['orderby'] ) ) ? esc_attr( $values['orderby'] ) : 'id',
				'order'             => ( !empty( $values['order'] ) )   ? esc_attr( $values['order']   ) : 'ASC',
				'hide_empty'        => 0,
				'exclude'           => ( !empty( $values['exclude'] ) ) ? esc_attr( $values['exclude'] ) : '',
				'exclude_tree'      => '',
				'include'           => ( !empty( $values['include'] ) ) ? esc_attr( $values['include'] ) : '',
			    'hierarchical'      => $hierarchical, 
			    'child_of'          => 0,
			    'pad_counts'        => 1, 
	
			    //'name'				=> $tax,
			    'selected' 			=> ( !empty( $_GET['tax_'.$tax_name] ) ) ? $_GET['tax_'.$tax_name] : '',
			    'depth'				=> ( $hierarchical ) ? 0 : -1,
			    'cat_icons'			=> ( isset( $values['cat_icons'] ) ) ? array( 
			    		'url'   => $gmw_settings['pt_category_icons']['url'],
			    		'icons' => $cat_icons
			    	) : false,
			    'show_option_all'   => ( isset( $values['label_within'] ) ) ? $values['label'] : __( ' - All - ', 'GMW-PS' ),
			    'show_count'      	=> ( !empty( $values['show_count'] ) ) ? 1 : 0,
			    'style'             => $values['style'],
				'taxonomy'          => $tax_name,
				'sb_no_results_text'=> __( 'No results match', 'GMW_PS' ),
				'sb_multiple_text'  => __( 'Select Some Options', 'GMW-PS' ),
				'sb_placeholder'    => ( isset( $values['label_within'] ) ) ? $values['label'] : __( 'Choose ', 'GMW-PS' ). $taxonomy->labels->name,
				'sb_multiple'       => true,

		), $gmw, $taxonomy, $tax_name, $values );
		
		//set terms_hash args. only args that control the output of the terms should be here.
		$hash_args = array(
				'orderby'         => $args['orderby'],
				'order'           => $args['order'],
				'hide_empty'      => $args['hide_empty'],
				'exclude'         => $args['exclude'],
				'exclude_tree'    => $args['exclude_tree'],
				'include'         => $args['include'],
			    'hierarchical'    => $args['hierarchical'], 
			    'child_of'        => $args['child_of'],
			    'taxonomy'		  => $args['taxonomy']
		);

		//taxonomy output
		$output = '';		
		$output = "<{$tag} id=\"{$tax_name}-tax-wrapper\" class=\"gmw-form-field-wrapper gmw-single-taxonomy-wrapper gmw-{$values['style']}-taxonomy-wrapper gmw-{$values['style']}-{$tax_name}-wrapper {$class}\">";
				
		//checkboxes opening tags
		if ( $values['style'] == 'checkbox' ) {		

			$output .= "<span class=\"gmw-field-label label-{$taxonomy->rewrite['slug']}\">{$values['label']}</span>";
			$output .= '<ul class="gmw-checkbox-level-top">';

		//dropdown 
		} elseif ( $values['style'] == 'dropdown' ) {

			if ( !isset( $values['label_within'] ) ) {
				$output .= "<label class=\"gmw-field-label\" for=\"{$taxonomy->rewrite['slug']}-tax\">{$values['label']}: </label>";
			}

			$output .= "<select name=\"tax_{$tax_name}\" id=\"{$tax_name}-tax\" class=\"gmw-dropdown-{$tax_name} gmw-dropdown-taxonomy\">";

			if ( !empty( $args['show_option_all'] ) ) {
				$output .= '<option value="0" selected="selected">'.esc_attr( $args['show_option_all'] ).'</option>';
			}

		//smartbox
		} else {

			$chosen_set = true;

			if ( !isset( $values['label_within'] ) ) {
				$output .= "<label class=\"gmw-field-label\" for=\"{$taxonomy->rewrite['slug']}-tax\">{$values['label']}: </label>";
			}
			$smartbox_data  = ' data-placeholder="' . esc_attr( $args['sb_placeholder'] ) . '" data-no_results_text="' . esc_attr( $args['sb_no_results_text'] ) . '"' . ( $args['sb_multiple'] ? 'multiple="multiple"' : '' ) . ' data-multiple_text="' . esc_attr( $args['sb_multiple_text'] ) . '"';
			$output .= "<select name=\"tax_{$tax_name}[]\" id=\"{$tax_name}-tax\" class=\"gmw-smartbox-{$tax_name} gmw-smartbox-taxonomy gmw-chosen\" {$smartbox_data}>";
		}
		
		$terms = false;

		//look for cache helper class
		if ( class_exists( 'GEO_my_WP_Cache_Helper' ) ) {

			// Store terms in a transient to help sites with taxonomies
			$terms_hash = 'gmw_tax_' . md5( json_encode( $hash_args	 ) . GEO_my_WP_Cache_Helper::get_transient_version( 'gmw_get_' . $args['taxonomy'] ) );
			$terms 		= get_transient( $terms_hash );

			//if not terms found in transient
			if ( empty( $terms ) ) {
				//get terms
				$terms = get_terms( $tax_name, $hash_args );
				//save terms in transient
				set_transient( $terms_hash, $terms, DAY_IN_SECONDS * 30 );
			}
		} else {
			$terms = get_terms( $tax_name, $hash_args );
		}

		//include GMW_PT_Categories_Walker file
		include_once( GMW_PS_PATH . '/posts/includes/gmw-ps-category-walker-class.php' );

		//new walker
		$walker = new GMW_PT_Categories_Walker;

		//run the category walker
		$output .= $walker->walk( $terms, $args['depth'], $args );
		
		//closing tags
		if ( $values['style'] == 'checkbox' ) {	
			$output .= '</ul>';
		} else {
			$output .= '</select>';
		}
		
		$output .= '</'.$tag.'>';
		
		//modify the taxonomy output
		$output = apply_filters( "gmw_ps_{$values['style']}_taxonomy_{$gmw['ID']}", $output, $gmw, $args );
		$output = apply_filters( "gmw_ps_{$values['style']}_taxonomy", $output, $gmw, $args );
	
		echo $output;
	}

	if ( $orgTag == 'ul' ) {
		echo '</ul>';
	} elseif ( $orgTag == 'ol') {
		echo '</ol>';
	}

	if ( $chosen_set && !wp_script_is( 'chosen', 'enqueued' ) ) {
		
		wp_enqueue_script( 'chosen' );
		wp_enqueue_style( 'chosen' );

		if ( version_compare( GMW_VERSION, '2.6.2', '<' ) ) {
			?>
			<script>
			jQuery(document).ready(function($) {
				$(".gmw-chosen").chosen();
			});
			</script>
			<?php
		}
	}
	//remove_filter( 'gmw_search_form_taxonomies', 'gmw_ps_search_form_taxonomies', 10, 4 );

	return true;
}
add_filter( 'gmw_search_form_taxonomies', 'gmw_ps_search_form_taxonomies', 10, 4 );

/**
 * Query dropdown and checkboxes for taxonomies/categories
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_query_taxonomies( $tax_args, $gmw ) {

	if ( empty( $gmw['search_form']['taxonomies'] ) )
		return $tax_args;

	if ( empty( $gmw['search_form']['post_types'] ) || count( $gmw['search_form']['post_types'] ) != 1 )
		return $tax_args;

	$postType = $gmw['search_form']['post_types'][0];

	if ( empty( $gmw['search_form']['taxonomies'][$postType] ) )
		return;

	$rr = 0;
	$tax_args = array( 'relation' => 'AND' );

	foreach ( $gmw['search_form']['taxonomies'][$postType] as $tax => $values ) {
			
		$get_tax  = false;
		$style    = ( isset( $values['style'] ) ) ? $values['style'] : 'pre_defined';

		//dropdown taxonomies query
		if ( $values['style'] == 'drop' || $values['style'] == 'dropdown' ) {

			if ( isset( $_GET['tax_'.$tax] ) ) $get_tax = $_GET['tax_'.$tax];

			if ( $get_tax != 0 ) {
				$rr++;
				$tax_args[] = array(
						'taxonomy' 	=> $tax,
						'field' 	=> 'id',
						'terms' 	=> 	array($get_tax)
				);
			}
		} 

		//checkboxes taxonomies queries
		if ( $values['style'] == 'check' || $values['style'] == 'checkbox' || $values['style'] == 'smartbox' ) {

			if( isset( $_GET['tax_'.$tax] ) ) $get_tax = $_GET['tax_'.$tax];

			if ( isset( $get_tax ) && !empty( $get_tax ) ) {
				$rr++;
				$tax_args[] = array(
						'taxonomy'  => $tax,
						'field' 	=> 'id',
						'terms' 	=> $get_tax,
						'operator'  => 'IN'
				);
			}
		}

		//exclude terms
		if ( !empty( $values['exclude'] ) ) {
			$rr++;
			$tax_args[] = array(
					'taxonomy'  => $tax,
					'field' 	=> 'id',
					'terms' 	=> explode(',', $values['exclude']),
					'operator'  => 'NOT IN'
			);
		}

		//include terms
		if ( !empty( $values['include'] ) ) {
			$rr++;
			$tax_args[] = array(
					'taxonomy'  => $tax,
					'field' 	=> 'id',
					'terms' 	=> explode(',', $values['include']),
					'operator'  => 'IN'
			);
		}
	}

	if( $rr == 0 )
		$tax_args = false;

	return $tax_args;
}
remove_filter( 'gmw_pt_tax_query', 'gmw_pt_query_taxonomies', 10, 2 );
add_filter( 'gmw_pt_tax_query',    'gmw_ps_query_taxonomies', 11, 2 );

/**
 * GMW function - Display Custom Fields in search forms
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_display_custom_fields( $gmw ) {

	if ( !isset( $gmw['search_form']['custom_fields'] ) )
		return;

	foreach ( $gmw['search_form']['custom_fields'] as $key => $value ) {

		$value = array_map( 'esc_attr', $value );
		$name  = explode(',', $value['name']);
		$date  = false;
		$key   = esc_attr( $key );

		if ( $value['type'] == "DATE" ) {
			$dateYes = 1; $date = 'gmw_date'; $date_type = $value['date_type'];
		}

		echo '<div id="gmw-cf-'.$key.'" class="gmw-custom-field-wrapper gmw-cf-wrapper-'.$key.'" >';

		if ( $value['compare'] == "BETWEEN" || $value['compare'] == "NOT BETWEEN" ) {
			$value_1 = ( !empty( $_GET['cf_'.$key][0] ) ) ? esc_attr( sanitize_text_field( $_GET['cf_'.$key][0] ) ) : '';
			$value_2 = ( !empty( $_GET['cf_'.$key][1] ) ) ? esc_attr( sanitize_text_field( $_GET['cf_'.$key][1] ) ) : '';
			echo "<label class='gmw-field-label' for='gmw-cf-{$key}-label'>{$name[0]}</label><input class='{$name[0]} {$date} gmw-cf gmw-cf-{$key}' type='text' name='cf_{$key}[]' value='{$value_1}' />";
			echo "<label class='gmw-field-label' for='gmw-cf-{$key}-label'>{$name[1]}</label><input class='{$name[1]} {$date} gmw-cf gmw-cf-{$key}' type='text' name='cf_{$key}[]' value='{$value_2}' />";
		} else {
			$value = ( !empty( $_GET['cf_'.$key] ) ) ? esc_attr( sanitize_text_field( $_GET['cf_'.$key] ) ) : '';
			echo "<label class='gmw-field-label' for='gmw-cf-{$key}-label'>{$name[0]}</label><input class='{$name[0]} {$date} gmw-cf gmw-cf-{$key}' type='text' name='cf_{$key}' value='{$value}' />";
		}

		echo '</div>';		
	}

	//date picker
	if ( isset( $dateYes ) ) {
		?>
        <script type="text/javascript">
        	jQuery(document).ready(function() {
        		jQuery('.gmw_date').datepicker({
                	dateFormat : '<?php echo $date_type; ?>'
                });
       		});	
        </script>
        <?php 
	}

	//enqueue date picker
	if ( !wp_script_is( 'jquery-ui-datepicker', 'enqueued' ) ) {
		wp_enqueue_script('jquery-ui-datepicker');
	}
	if ( !wp_style_is( 'ui-comp', 'enqueued' ) ) {
		wp_enqueue_style( 'ui-comp' );
	}
}
add_action( 'gmw_search_form_before_address', 'gmw_ps_display_custom_fields' );

/**
 * GMW function Convert chosen date by user to date query can read
 * @author Eyal Fitoussi
 */
function gmw_ps_pt_date_converter( $date, $type ) {

	if ( empty($date) ) {
		$date = date("ymd");
	} else {
		$date = explode("/", $date);

		if ( $type == "mm/dd/yy" || $type == "mm/dd/yyyy" ) {
			$date = $date[2] . $date[0] . $date[1];
		} elseif ( $type == "dd/mm/yy" || $type == "dd/mm/yyyy" ) {
			$date = $date[2] . $date[1] . $date[0];
		} elseif ( $type == "yy/mm/dd" || $type == "yyyy/mm/dd"  ) {
			$date = $date[0] . $date[1] . $date[2];
		}
	}

	return $date;
}

/**
 * GMW funciton - Query custom fields
 * @author Eyal Fitoussi
 */
function gmw_ps_pt_query_custom_fields( $meta_args, $gmw ) {

	if ( empty( $gmw['search_form']['custom_fields'] ) )
		return false;

	$custom_fields = $gmw['search_form']['custom_fields'];
	$rr 		   = 0;
	$get_cf 	   = false;
	$meta_args 	   = array( 'relation' => 'AND' );

	foreach ( $custom_fields as $key => $values ) {

		$get_cf = false;

		// check if field not empty
		$get_cf = ( !empty( $_GET['cf_'.$key] ) ) ? $_GET['cf_'.$key] : false;

		//check if field is array in case of BETWEEN and if so make sure its values are not empty
		if ( is_array( $get_cf ) && !array_filter( $get_cf ) ) $get_cf = false;

		// if we have value for cusrom field
		if ( !empty( $get_cf ) ) {

			// if it is a date field
			if ( $values['type'] == 'DATE' ) {

				if ( is_array( $get_cf ) ) {

					// covert each of the values from date enterd to date query can read
					foreach ( $get_cf as $cf ) {
						$new_cf[] = gmw_ps_pt_date_converter( $cf, $values['date_type'] );
					}
					$get_cf = $new_cf;
				} else {
					$get_cf = gmw_ps_pt_date_converter( $get_cf, $values['date_type'] );
				}
			}

			// create the meta query args
			$rr++;
			$meta_args[] = array(
					'key'     => $key,
					'value'   => $get_cf,
					'type'	  => $values['type'],
					'compare' => $values['compare']
			);

		}
	}

	if ( $rr == 0 ) $meta_args = array();

	return $meta_args;
}
add_filter( 'gmw_pt_meta_query', 'gmw_ps_pt_query_custom_fields', 10, 2 );

/**
 * Query keywords
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_query_keywords( $clauses, $gmw ) {
	global $wpdb;

	if ( !isset( $gmw['search_form']['keywords_field'] ) || $gmw['search_form']['keywords_field'] == 'dont' || empty( $_GET[$gmw['url_px'].'keywords'] ) ) 
		return $clauses;

	$keyword = $_GET[$gmw['url_px'].'keywords'];
	
	//support for wordpress lower then 4.0
	$like =  method_exists( $wpdb, 'esc_like' ) ? $wpdb->esc_like( trim( $keyword ) ) : like_escape( trim( $keyword ) );
	$like = '%'.$like.'%';
	
	//search title
	$where = $wpdb->prepare( $wpdb->prefix."posts.post_title LIKE %s", $like ); 

	//search content
	if ( $gmw['search_form']['keywords_field'] == 'content' ) {
		$where .= $wpdb->prepare( " OR {$wpdb->prefix}posts.post_content LIKE %s", $like );
	}

	$where .= $wpdb->prepare( " OR {$wpdb->prefix}posts.ID in ( SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_product_attributes' AND  meta_value like %s )", $like );


	$clauses['where'] .= ' AND ('.$where.')';
	
	if($_GET["pweight"] != ''){
		$clauses['where']  .= $wpdb->prepare( " AND {$wpdb->prefix}posts.ID in ( SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_weight' AND  meta_value >= CAST(%s AS UNSIGNED))", $_GET["pweight"] );
	}
	
	return $clauses;
}
add_filter( 'gmw_pt_location_query_clauses', 'gmw_ps_query_keywords', 20, 2 );

/**
 * GMW PT search results function - Per result map
 * @version 1.0
 * @author Eyal Fitoussi
 */
function gmw_ps_get_per_results_map( $gmw, $post ) {

	if ( !isset( $gmw['results_map']['per_results_map']['use'] ) ) 
		return;
	
	$mapArgs = array(
			'mapId' 	 		=> 'per-post-'.$post->post_count,
			'mapType'			=> 'per-post',
			'mapElement' 		=> 'gmw-map-per-post-'.$post->post_count,
			'hiddenElement' 	=> '#gmw-map-wrapper-per-post-'.$post->post_count,					
			'mapLoaderElement' 	=> 'gmw-map-loader-per-post-'.$post->post_count,
			'locations'			=> array( $post ),
			'zoomLevel'			=> 'auto',
			'mapTypeId'			=> $gmw['results_map']['per_results_map']['map_type'],
			'resizeMapElement'	=> 'gmw-resize-map-trigger-per-post-'.$post->post_count,
			'userPosition'		=> array(
					'lat'		=> $gmw['your_lat'],
					'lng'		=> $gmw['your_lng'],
					'address' 	=> $gmw['org_address'],
					'iwContent' => 'You are here',
					'iwOpen'	=> true
			)
	);

	gmw_new_map_element( $mapArgs );

	//map arguments
	echo gmw_get_results_map( array(
			'ID' 			=> 'per-post-'.$post->post_count,
			'prefix'		=> 'pt-single',
			'addon'			=> 'posts',
			'results_map' 	=> array(
					'map_width'  => $gmw['results_map']['per_results_map']['map_width'],
					'map_height' => $gmw['results_map']['per_results_map']['map_height'],
			)
	) );
}

function gmw_ps_per_results_map( $gmw, $post ) {
	echo gmw_ps_get_per_results_map( $gmw, $post );
}
add_action( 'gmw_posts_loop_before_content' , 'gmw_ps_per_results_map', 10, 2 );
add_action( 'gmw_pt_per_result_map', 'gmw_ps_per_results_map', 10, 2 );
		
/**
 * Modify the map element with map 
 * @param  array $mapElements  the original map element
 * @param  array $gmw          the form being displayed
 * @return array               modifyed map element
 */
function gmw_pt_map_elements( $mapElements, $gmw ) {
		
	$settings = get_option( 'gmw_options' );

	if ( !isset( $gmw['results_map']['your_location_icon'] ) )
		$gmw['results_map']['your_location_icon'] = '_default.png';
	
	//set the user location marker
	$mapElements['userPosition']['mapIcon'] = $settings['pt_map_icons']['url']. $gmw['results_map']['your_location_icon'];
	
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
add_filter( "gmw_pt_map_element", 'gmw_pt_map_elements', 10, 2 );

/**
 * GMW function - custom map icons
 * @param $gmw
 * @param $gmw_options
 * @param $gmw_query
 */
function gmw_ps_pt_map_icon( $map_icon, $post, $gmw ) {

	$settings 	 = gmw_get_options_group();
	$pt_settings = $settings['post_types_settings'];
	$icons_url   = $settings['pt_map_icons']['url'];

	if ( empty( $gmw['results_map']['map_icon'] ) ) {
		$gmw['results_map']['map_icon'] = '_default.png';
	}

	if ( Empty( $gmw['results_map']['map_icon_usage'] ) ) {
		$gmw['results_map']['map_icon_usage'] = 'same';
	}
	
	//same global map icon
	if ( empty( $gmw['results_map']['map_icon_usage'] ) || $gmw['results_map']['map_icon_usage'] == 'same' ) {

		$map_icon = $gmw['results_map']['map_icon'];

	//per post map icon
	} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_post' && !empty( $post->map_icon ) ) {

		$map_icon = $post->map_icon;

	//per post type map icons
	} elseif ( $gmw['results_map']['map_icon_usage'] == 'per_post_type' && !empty( $pt_settings['post_types_icons'][$post->post_type] ) ) {

		$map_icon = $pt_settings['post_types_icons'][$post->post_type];

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

			$map_icon = '_default.png';
		}
			
		if ( $map_icon != '_default.png' ) {

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
				$map_icon = '_default.png';
			} else {
				$map_icon = ( !empty( $post_term_ids[0] ) && !empty( $category_icons[$post_term_ids[0]] ) ) ? $category_icons[$post_term_ids[0]]: '_default.png';
			}
		}
		
	//map icon as featured image
	} elseif ( $gmw['results_map']['map_icon_usage'] == 'image' ) {

		if ( has_post_thumbnail( $post->ID ) ) {

			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( 30,30 ) );
			$map_icon = $thumb[0];

		} else {
			$map_icon = GMW_PS_URL.'/posts/assets/map-icons/_no_image.png';
		}
	} else {
		$map_icon = '_default.png';
	}

	if ( $gmw['results_map']['map_icon_usage'] != 'image' ) {
		$map_icon = ( empty( $map_icon ) || $map_icon == '_default.png' ) ? 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='. $post->post_count .'|FF776B|000000' : $icons_url.$map_icon;
	}

	return $map_icon;
}
add_filter( 'gmw_pt_map_icon', 'gmw_ps_pt_map_icon', 10, 3 );