<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * GMW_PT_Admin class
 */
class GMW_PS_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->add_ons 	= get_option( 'gmw_addons' );
		$this->settings = get_option( 'gmw_options' );

		add_action( 'admin_init', array( $this, 'glob_functions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

		if ( GEO_my_WP::gmw_check_addon( 'posts' ) && isset( $this->settings['post_types_settings']['post_types'] ) && isset( $this->settings['post_types_settings']['per_category_icons'] ) ) {
			include( 'gmw-ps-category-icons.php' );
		}

		if ( isset( $this->settings['post_types_settings']['per_post_icons'] ) ) {
			include( 'gmw-ps-per-post-map-icon.php' );
		}

		//main settings
		add_action( 'gmw_main_settings_post_types_icons', array( $this, 'main_settings_post_types_icons' ), 1, 4 );
		add_action( 'gmw_main_settings_per_category_icons', array( $this, 'main_settings_per_category_icons' ), 1, 4 );

		add_filter( 'gmw_admin_settings', 			   array( $this, 'settings_init' 				 ), 5 );
		add_filter( 'gmw_posts_form_settings', 		   array( $this, 'pt_form_settings_init' 		 ), 2 );
		add_filter( 'gmw_friends_form_settings', 	   array( $this, 'fl_form_settings_init' 		 ), 2 );
		add_filter( 'gmw_gmaps_posts_form_settings',   array( $this, 'gmaps_posts_form_settings_init' 	) );
		add_filter( 'gmw_gmaps_friends_form_settings', array( $this, 'gmaps_friends_form_settings_init' ) );

		//posts locator form settings
		add_action( 'gmw_posts_form_settings_taxonomies_premium', 	   array( $this, 'taxonomies_premium' 			), 1, 4 );
		add_action( 'gmw_posts_form_settings_address_fields', 		   array( $this, 'form_settings_address_fields' ), 1, 4 );
		add_action( 'gmw_posts_form_settings_keywords', 			   array( $this, 'form_settings_keywords' 		), 1, 4 );
		add_action( 'gmw_posts_form_settings_radius', 				   array( $this, 'form_settings_radius' 		), 1, 4 );
		add_action( 'gmw_posts_form_settings_custom_fields', 		   array( $this, 'form_settings_custom_fields' 	), 1, 4 );
		add_action( 'gmw_posts_form_settings_map_icon_usage', 		   array( $this, 'map_icon_usage' 				), 1, 4 );
		add_action( 'gmw_posts_form_settings_per_results_map', 		   array( $this, 'per_results_map' 				), 1, 4 );
		add_action( 'gmw_posts_form_settings_global_map_icon', 	  	   array( $this, 'global_map_icon' 				), 1, 4 );
		add_action( 'gmw_posts_form_settings_your_location_icon', 	   array( $this, 'your_location_icon' 			), 1, 4 );
		add_action( 'gmw_posts_form_settings_wider_search', 	  	   array( $this, 'wider_search' 				), 1, 4 );
		add_action( 'gmw_posts_form_settings_all_results', 	 	   	   array( $this, 'all_results' 					), 1, 4 );
		add_action( 'gmw_posts_form_settings_posts_info_window_theme', array( $this, 'posts_info_window_theme' 		), 1, 4 );
		//add_action( 'gmw_posts_form_settings_show_excerpt', array( $this, 'show_excerpt' ), 1, 4 );
		 
		//friends locator form settings
		add_action( 'gmw_friends_form_settings_address_fields', 			  array( $this, 'form_settings_address_fields' 	), 2, 4 );
		add_action( 'gmw_friends_form_settings_fl_keyword_search', 	      	  array( $this, 'fl_keyword_search' 			), 2, 4 );
		add_action( 'gmw_friends_form_settings_results_xprofile_fields', 	  array( $this, 'results_xprofile_fields' 	   	), 2, 4 );
		add_action( 'gmw_friends_form_settings_radius', 					  array( $this, 'form_settings_radius' 		   	), 1, 4 );
		add_action( 'gmw_friends_form_settings_fl_map_icon_usage', 			  array( $this, 'fl_map_icon_usage' 		   	), 1, 4 );
		add_action( 'gmw_friends_form_settings_fl_global_map_icon', 		  array( $this, 'fl_global_map_icon' 			), 1, 4 );
		add_action( 'gmw_friends_form_settings_fl_your_location_icon', 		  array( $this, 'fl_your_location_icon' 		), 1, 4 );
		add_action( 'gmw_friends_form_settings_wider_search', 				  array( $this, 'wider_search' 					), 1, 4 );
		add_action( 'gmw_friends_form_settings_all_results', 				  array( $this, 'all_results' 					), 1, 4 );
		add_action( 'gmw_friends_form_settings_bp_members_info_window_theme', array( $this, 'bp_members_info_window_theme' 	), 1, 4 );
		//add_action( 'gmw_posts_form_settings_show_excerpt', array( $this, 'show_excerpt' ), 1, 4 );

		//global maps posts form settings
		add_action( 'gmw_gmaps_posts_form_settings_map_icon_usage', 	 	 array( $this, 'map_icon_usage' 	   ), 1, 4 );
		add_action( 'gmw_gmaps_posts_form_settings_global_map_icon', 		 array( $this, 'global_map_icon' 	   ), 1, 4 );
		add_action( 'gmw_gmaps_posts_form_settings_your_location_icon', 	 array( $this, 'your_location_icon'    ), 1, 4 );

		add_action( 'gmw_gmaps_friends_form_settings_fl_map_icon_usage', 	 array( $this, 'fl_map_icon_usage' 	   ), 1, 4 );
		add_action( 'gmw_gmaps_friends_form_settings_fl_global_map_icon', 	 array( $this, 'fl_global_map_icon'    ), 1, 4 );
		add_action( 'gmw_gmaps_friends_form_settings_fl_your_location_icon', array( $this, 'fl_your_location_icon' ), 1, 4 );
		//
	}

	public function register_scripts() {
		wp_enqueue_script( 'gmw-ps-admin', GMW_PS_URL .'/assets/js/admin.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'gmw-ps-style',  GMW_PS_URL .'/assets/css/style.css' );
	}
	
	public function glob_functions() {
				
		//save pt map icons in settings
		if ( GEO_my_WP::gmw_check_addon( 'posts' ) ) {
			
			//map icons
			if ( is_dir( STYLESHEETPATH.'/geo-my-wp/posts/map-icons/' ) ) {
				$pt_map_icons_path = STYLESHEETPATH.'/geo-my-wp/posts/map-icons/';
				$pt_map_icons_url  = get_stylesheet_directory_uri().'/geo-my-wp/posts/map-icons/';
				$pt_map_icons 	   = glob( $pt_map_icons_path.'*.png', GLOB_NOSORT );	
			}
			
			if ( empty( $pt_map_icons ) ) {
				$pt_map_icons_path = GMW_PS_PATH . '/posts/assets/map-icons/';
				$pt_map_icons_url  = GMW_PS_URL . '/posts/assets/map-icons/';
				$pt_map_icons 	   = glob( $pt_map_icons_path.'*.png', GLOB_NOSORT );	
			}	
	
			foreach ( $pt_map_icons as $key => $file ) {
				$pt_map_icons[$key] = basename( $file );
			}
	
			$this->settings['pt_map_icons']['path'] 	 = $pt_map_icons_path;
			$this->settings['pt_map_icons']['url']  	 = $pt_map_icons_url;
			$this->settings['pt_map_icons']['all_icons'] = $pt_map_icons;
			$this->settings['pt_map_icons']['set_icons'] = get_option( 'gmw_category_map_icons' );
			
			//category icons
			if ( is_dir( STYLESHEETPATH.'/geo-my-wp/posts/category-icons/' ) ) {
				$pt_category_icons_path = STYLESHEETPATH.'/geo-my-wp/posts/category-icons/';
				$pt_category_icons_url  = get_stylesheet_directory_uri().'/geo-my-wp/posts/category-icons/';
				$pt_category_icons 	    = glob( $pt_category_icons_path.'*.png', GLOB_NOSORT );
			}
				
			if ( empty( $pt_category_icons ) ) {
				$pt_category_icons_path = GMW_PS_PATH . '/posts/assets/category-icons/';
				$pt_category_icons_url  = GMW_PS_URL . '/posts/assets/category-icons/';
				$pt_category_icons 	    = glob( $pt_category_icons_path.'*.png', GLOB_NOSORT );
			}
			
			foreach ( $pt_category_icons as $key => $file ) {
				$pt_category_icons[$key] = basename( $file );
			}
		
			$this->settings['pt_category_icons']['path'] 	  = $pt_category_icons_path;
			$this->settings['pt_category_icons']['url']  	  = $pt_category_icons_url;
			$this->settings['pt_category_icons']['all_icons'] = $pt_category_icons;
			$this->settings['pt_category_icons']['set_icons'] = get_option( 'gmw_category_icons' );
		}
		
		//save fl map icons in settings
		if ( GEO_my_WP::gmw_check_addon( 'friends' ) ) {
				
			if ( is_dir( STYLESHEETPATH.'/geo-my-wp/friends/map-icons/' ) ) {
				$fl_map_icons_path = STYLESHEETPATH.'/geo-my-wp/friends/map-icons/';
				$fl_map_icons_url  = get_stylesheet_directory_uri().'/geo-my-wp/friends/map-icons/';
				$fl_map_icons 	   = glob( $fl_map_icons_path.'*.png', GLOB_NOSORT );
			}
				
			if ( empty( $fl_map_icons ) ) {
				$fl_map_icons_path = GMW_PS_PATH . '/friends/assets/map-icons/';
				$fl_map_icons_url  = GMW_PS_URL . '/friends/assets/map-icons/';
				$fl_map_icons 	   = glob( $fl_map_icons_path.'*.png', GLOB_NOSORT );
			}
		
			foreach ( $fl_map_icons as $key => $file ) {
				$fl_map_icons[$key] = basename( $file );
			}
	
			$this->settings['ml_map_icons']['path'] = $fl_map_icons_path;
			$this->settings['ml_map_icons']['url']  = $fl_map_icons_url;
			$this->settings['ml_map_icons']['all_icons'] 	   = $fl_map_icons;
		}	
		update_option( 'gmw_options', $this->settings );
	}
	
	/**
	 * addon settings page function.
	 *
	 * @access public
	 * @return $settings
	 */
	public function settings_init( $settings ) {

		if ( isset( $this->add_ons['posts'] ) && $this->add_ons['posts'] == 'active' ) {

			$settings['post_types_settings'][1][] = array(
					'name'       => 'post_types_icons',
					'std'        => '',
					'label'      => __( 'Post types map icons', 'GMW-PS' ),
					'desc'       => __( 'Assign map icon to each post type.', 'GMW-PS' ),
					'type'       => 'function'
			);
			$settings['post_types_settings'][1][] = array(
					'name'       => 'per_post_icons',
					'std'        => '',
					'label'      => __( 'Per post map icon', 'GMW-PS' ),
					'cb_label'   => __( 'Enable', 'GMW-PS' ),
					'desc'       => __( 'Assign a map icon to each post that you create or update. When this featured is enabled the map icons will be added to the admin "Edit post" page.', 'GMW-PS' ),
					'type'       => 'checkbox',
					'attributes' => array()
			);
			$settings['post_types_settings'][1][] = array(
					'name'       => 'per_category_icons',
					'std'        => '',
					'label'      => __( 'Category icons', 'GMW-PS' ),
					'cb_label'   => __( 'Enable', 'GMW-PS' ),
					'desc'       => __( "This feature does 2 things: <ol><li>Assign categories icon to taxonomies - you can display the categories icon next to a category checkboxes in GEO my WP search form.</li><li>Assign category map icons to taxonomies which you can display as custom map markers.</li></ol> To use the feature check the \"Enable category icons\" checkbox and choose the taxonomies that you would like to assign icons to. Then you can go to a taxonomy page and set the  category icons.", 'GMW-PS' ),
					'type'       => 'function',
					'attributes' => array()
			);
		}

		global $blog_id;

		if ( isset( $this->add_ons['friends'] ) && $this->add_ons['friends'] == 'active' && defined( 'BP_ROOT_BLOG' ) && BP_ROOT_BLOG == $blog_id ) {

			$settings['members_locator'] = array(

					__( 'Members Locator', 'GMW-PS' ),
					array(
							array(
									'name'       => 'location_tab_fields_loggedin',
									'std'        => '',
									'label'      => __( 'Location tab address fields - loggedin user', 'GMW-PS' ),
									'desc'       => __( 'Choose the address fields that you would like to display in the "Location" tab of the logged in user.', 'GMW-PS' ),
									'type'       => 'multicheckbox',
									'attributes' => array(),
									'options'	 => array(
											'street' => 'Street',
											'apt'	 => 'Apt',
											'city'	 => 'City',
											'state'	 => 'State',
											'zipcode'=> 'Zipcode',
											'country'=> 'Country'
									)
							),
							array(
									'name'       => 'location_tab_fields_displayed',
									'std'        => '',
									'label'      => __( 'Location tab address fields -  displayed user', 'GMW-PS' ),
									'desc'       => __( 'Choose the address fields that you would like to display in the "Location" tab when viwing the location of anotehr user.', 'GMW-PS' ),
									'type'       => 'multicheckbox',
									'attributes' => array(),
									'options'	 => array(
											'address'=> 'Full address',
											'street' => 'Street',
											'apt'	 => 'Apt',
											'city'	 => 'City',
											'state'	 => 'State',
											'zipcode'=> 'Zipcode',
											'country'=> 'Country'
									)
							),
							array(
									'name'       => 'activity_update_fields',
									'std'        => '',
									'label'      => __( 'Activity update address fields.', 'GMW-PS' ),
									'desc'       => __( 'Choose the address fields that you would like to displayed in activity update ( after user update his location )', 'GMW-PS' ),
									'type'       => 'multicheckbox',
									'attributes' => array(),
									'options'	 => array(
											'street' => 'Street',
											'apt'	 => 'Apt',
											'city'	 => 'City',
											'state'	 => 'State',
											'zipcode'=> 'Zipcode',
											'country'=> 'Country'
									)
							),
							array(
									'name'       => 'per_member_icon',
									'std'        => '',
									'label'      => __( 'Per members map icon', 'GMW-PS' ),
									'cb_label'   => __( 'Enable', 'GMW-PS' ),
									'desc'       => __( 'This feature will add a "Map icon" tab to the user "location" tab in his profile page. Enable this feature if you would like the members of your site to be able to choose their map icon.', 'GMW-PS' ),
									'type'       => 'checkbox',
									'attributes' => array()
							),

					),
			);

		}

		return $settings;
	}

	public function main_settings_per_category_icons( $gmw_options, $section, $option ) {
		
		$checked = '';
		$display = 'style="display:none;"';

		if ( isset( $gmw_options['post_types_settings']['per_category_icons']['enabled'] ) ) {
			$checked = 'checked="checked"';
			$display = '';
		} 		
		?>
		<p>
			<label>
				<input class="setting-per_category_icons" name="gmw_options[post_types_settings][per_category_icons][enabled]" onchange="jQuery('.per-category-icons-trigger').slideToggle();" type="checkbox" value="1" <?php echo $checked; ?>>
				<?php _e( 'Enable category icons', 'GMW-PS' ); ?>
			</label>
		</p>

		<?php $checked = ( isset( $gmw_options['post_types_settings']['per_category_icons']['same_icons'] ) ) ? 'checked="checked"' : ''; ?>
		<p <?php echo $display; ?> class="per-category-icons-trigger">
			<label>
				<input class="setting-per_category_icons_same_icons" name="gmw_options[post_types_settings][per_category_icons][same_icons]" type="checkbox" value="1" <?php echo $checked; ?>>
				<?php _e( 'Use same icons for category and map icons', 'GMW-PS' ); ?>
			</label>
		</p>
		<?php
		if ( empty( $gmw_options['post_types_settings']['post_types'] ) ) 
			return;
		?>
		<div  id="map-icons-texonomies-wrapper" class="per-category-icons-trigger" <?php echo $display; ?>>
			<label><b><?php _e( 'Taxonomies', 'GMW-PS' ); ?></b></label>
			<br />
			<em style="font-size:11px"><?php _e( 'if you have just changed the Post Types settings above you will need to update this settings page in order to refresh the list of taxonomies below based on the new Post Types settings', 'GMW-PS' ); ?></em>
			<ul>
			<?php 
			foreach ( get_object_taxonomies( $gmw_options['post_types_settings']['post_types'] ) as $taxonomy ) { 

				$checked = ( !empty( $gmw_options['post_types_settings']['per_category_icons']['taxonomies'] ) && in_array( $taxonomy, $gmw_options['post_types_settings']['per_category_icons']['taxonomies'] ) ) ? 'checked="checked"' : ''; 
				?>
				<li>
					<label>
						<input class="setting-per_category_icons_taxonomy" name="gmw_options[post_types_settings][per_category_icons][taxonomies][]" type="checkbox" value="<?php esc_attr_e( $taxonomy ); ?>" <?php echo $checked; ?>> <?php esc_attr_e( $taxonomy ); ?>
					</label>
				</li>
				<?php 
			} 
			?>	
			</ul>
		</div>
		<p>
		<div class="per-category-icons-trigger" <?php echo $display; ?>>
			<label><b><?php _e( 'Taxonomies term orderby', 'GMW-PS' ); ?></b></label><br />
			<select name="gmw_options[post_types_settings][per_category_icons][terms_orderby]">
				<?php $value = ( !empty( $gmw_options['post_types_settings']['per_category_icons']['terms_orderby'] ) ) ? $gmw_options['post_types_settings']['per_category_icons']['terms_orderby'] : ''; ?>
				<option value="name" <?php selected( $value, 'name' ); ?>><?php _e( 'Name', 'GMW-PS'); ?></option>
				<option value="term_id" <?php selected( $value, 'term_id' ); ?>><?php _e( 'Term ID', 'GMW-PS'); ?></option>
				<option value="count" <?php selected( $value, 'count' ); ?>><?php _e( 'Count', 'GMW-PS'); ?></option>
				<option value="slug" <?php selected( $value, 'slug' ); ?>><?php _e( 'Slug', 'GMW-PS'); ?></option>
				<option value="term_group" <?php selected( $value, 'term_group' ); ?>><?php _e( 'Term group', 'GMW-PS'); ?></option>
			</select>
		</div>
		</p>
		<p>
		<div class="per-category-icons-trigger" <?php echo $display; ?>>
			<label><b><?php _e( 'Taxonomies term order', 'GMW-PS' ); ?></b></label><br />
			<select name="gmw_options[post_types_settings][per_category_icons][terms_order]">
				<?php $value = ( !empty( $gmw_options['post_types_settings']['per_category_icons']['terms_order'] ) ) ? $gmw_options['post_types_settings']['per_category_icons']['terms_order'] : ''; ?>
				<option value="ASC"  <?php selected( $value, 'ASC' );  ?>><?php _e( 'ASC', 'GMW-PS'); ?></option>
				<option value="DESC" <?php selected( $value, 'DESC' ); ?>><?php _e( 'DESC', 'GMW-PS'); ?></option>
			</select>
		</div>
		</p>
		<?php
	}

	/**
	 * Per Post types map icons
	 */
	public function main_settings_post_types_icons( $gmw_options, $section, $option ) {
		
		$saved_data = ( isset( $gmw_options[$section]['post_types'] ) ) ? $gmw_options[$section]['post_types'] : array();
		
		foreach ( get_post_types() as $post_icon) { ?>
		
			<table class="widefat">
				<thead>
					<tr>
						<th class="field_order" style="background:#f9f9f9;">
							<i class="fa fa-cog gmw-post-type-icon-list-trigger" onclick="jQuery(this).closest('table').find('.gmw-icons-list').slideToggle();">
								<span class="gmw-icons-list-name"><?php echo get_post_type_object($post_icon)->labels->name; ?></span>
							</i>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="field_order" style="padding:0px;">
							<div class="gmw-icons-list" style="display:none;padding:10px;">
							<?php				
							$map_icons = $this->settings['pt_map_icons']['all_icons'];					
							$icons_url = $this->settings['pt_map_icons']['url'];
							$cic 	   = 1;
							
							foreach ( $map_icons as $map_icon ) {
								$checked = ( ( isset( $gmw_options[$section]['post_types_icons'][$post_icon] ) && $gmw_options[$section]['post_types_icons'][$post_icon] == $map_icon ) || $cic == 1 ) ? 'checked="checked"' : '';
								echo '<span><input type="radio" name="gmw_options['.$section.'][post_types_icons]['.$post_icon.']" value="'.$map_icon.'" '.$checked.' />';
								echo '<img src="'.$icons_url.$map_icon.'" style="max-width:25px"/></span>';
								$cic++;
							}				
							?>
						</div>
						</th>
					</tr>
				</thead>
			</table>
			<br />
		<?php } ?>	
			
		<script>
			jQuery(document).ready(function($) {
				jQuery('.gmw-icons-list-button').click(function(){
					jQuery(this).closest('ul').find('.li').slideToggle();
				});
			});
		</script>	
		<?php 			
	}
	
	/**
	 * Post types form settings
	 * 
	 */
	public function taxonomies_premium( $gmw_forms, $formID, $section, $option ) {

		$posts = get_post_types();  ?>
		
		<div id="taxonomies-wrapper" style="max-width:650px;">
			
			<?php foreach ($posts as $post) : ?>
				
				<?php $taxes = get_object_taxonomies($post); ?>	
	
				<?php $style = ( isset( $gmw_forms[$formID][$section]['post_types']) && ( count( $gmw_forms[$formID][$section]['post_types'] ) == 1) && ( in_array( $post, $gmw_forms[$formID][$section]['post_types'] ) ) ) ? '' : 'style="display: none;"'; ?>
				
				<?php foreach ( $taxes as $key => $tax ) :
					
					if ( empty( $gmw_forms[$formID][$section]['taxonomies'][$post][$tax] ) ) {
						$gmw_forms[$formID][$section]['taxonomies'][$post][$tax]['style'] = 'na';
					}
					$tax_option = $gmw_forms[$formID][$section]['taxonomies'][$post][$tax];

					$cbHide   = ( !isset( $tax_option['style'] ) || ( $tax_option['style'] != 'checkbox' && $tax_option['style'] != 'check' ) ) ? 'display:none' : 'display:block'; 
					$cbpdHide = ( isset( $tax_option['style'] ) && $tax_option['style'] == 'pre_defined' ) ? 'style="display:none"' : '';
					
					$get_tax = get_taxonomy($tax); ?>
					
					<div <?php echo $style; ?>  id="<?php echo $tax; ?>" class="taxonomy-wrapper <?php echo $post; ?>_cat " >

						<table class="gmw-saf-table">
							<tr class="taxes-wrapper">
								<th style="padding:10px;" class="gmw-single-taxonomy">	
									<a href="#" class="gmw-taxonomy-sort-handle fa fa-bars fa-2x" title="Sort taxonomy" onclick="event.preventDefault();"></a>											
									<?php echo $get_tax->labels->singular_name ; ?>
									<a href="#" class="gmw-taxonomy-toggle" title="Edit taxonomy settings" onclick="event.preventDefault();jQuery(this).closest('.taxonomy-wrapper').find('.taxonomy-settings-table-wrapper').slideToggle();"><i class="fa fa-cog"></i><span class="right">Edit</span></a>																							
								</th>
							</tr>
						</table>
						<div class="taxonomy-settings-table-wrapper" style="margin-top: -5px;margin-top: -5px;background: white;padding:0 5px;border:1px solid #d7d7d7;display:none;">
							
							<ul class="gmw-saf-table values-table">
								<li style="margin-bottom:10px">
									<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444"><?php _e( 'Usage', 'GMW-PS'); ?></label>										
									<div id="gmw-st-wrapper" style="background: #f7f7f7; padding: 8px; box-sizing: border-box; border: 1px solid #e5e5e5; display: inline-block; width: 100%">
										<input type="radio" class="gmw-st-btns radio-pd" name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][style]" value="pre_defined"  checked="checked" /><?php _e( 'Pre Defined','GMW-PS' ); ?>	
										<input type="radio" class="gmw-st-btns" name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][style]" value="dropdown" <?php if ( isset( $tax_option['style'] ) && ( $tax_option['style'] == 'dropdown' || $tax_option['style'] == 'drop' ) ) echo  "checked=checked"; ?>  /><?php _e( 'Dropdown','GMW-PS' ); ?>
										<input type="radio" class="gmw-st-btns" name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][style]" value="checkbox" <?php if ( isset( $tax_option['style'] ) && ( $tax_option['style'] == 'checkbox' || $tax_option['style'] == 'check' ) ) echo  "checked=checked"; ?> /><?php _e( 'Checkboxes','GMW-PS' ); ?>
										<input type="radio" class="gmw-st-btns" name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][style]" value="smartbox" <?php if ( isset( $tax_option['style'] ) && $tax_option['style'] == 'smartbox' ) echo  "checked=checked"; ?> /><?php _e( 'Smart-box','GMW-PS' ); ?>			
									</div>							
								</li>
							
								<li class="cb-pd-hidden" <?php echo $cbpdHide; ?>>		

									<?php if ( isset( $this->settings['post_types_settings']['per_category_icons']['enabled'] ) ) { ?>
										<div class="cb-hidden" style="background: #f7f7f7; width:100%;padding: 8px; box-sizing: border-box; border:1px solid #e5e5e5; <?php echo $cbHide; ?>" >									
											<input type="checkbox" name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][cat_icons]" value="1" <?php if ( isset($tax_option['cat_icons']) ) echo 'checked="checked"'; ?> />
											<?php _e( "Enable category icons",'GMW-PS' ); ?>
										</div>
									<?php } ?>
								</li>
						
								<li class="table-within cb-pd-hidden" <?php echo $cbpdHide; ?>>
									
									<div style="width:33%;vertical-align: top;display: inline-block;">
										<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444"><?php _e( 'Taxonomy label', 'GMW-PS'); ?></label>									
										<div style="background: #f7f7f7; padding: 5px; box-sizing: border-box; border:1px solid #e5e5e5; display: inline-block;">	
											<input type="text" style="width:100%; box-sizing: border-box; padding: 5px; margin-bottom:5px;" name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][label]" value="<?php echo ( isset( $tax_option['label'] ) && !empty( $tax_option['label'] ) ) ? $tax_option['label'] : $get_tax->labels->name; ?>" />
											<input type="checkbox"  name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][label_within]" value="1" <?php if ( isset( $tax_option['label_within']) ) echo 'checked="checked"'; ?> /><?php _e('Label within the select box','GMW-PS'); ?>									
											<input type="checkbox" name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][show_count]" value="1" <?php if ( isset( $tax_option['show_count']) ) echo 'checked="checked"'; ?> /><?php _e( 'Show count','GMW-PS'); ?>						
										</div>
									</div>

									<div style="width:33%;vertical-align: top;display: inline-block;">
										<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444"><?php _e( 'Order terms by', 'GMW-PS'); ?></label>
										<?php $selected = ( isset( $tax_option['orderby'] ) ) ? $tax_option['orderby'] : ''; ?>
										<div style="width:100%;background: #f7f7f7; padding: 5px; box-sizing: border-box; border:1px solid #e5e5e5; display: inline-block;">
											<select name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][orderby]" style="width:100%">
												<option value="id" selected="selected"><?php _e( 'ID', 'GMW-PS' ); ?></option>
												<option value="name" <?php if ( $selected == 'name' ) echo 'selected="selected"'; ?>><?php _e( 'Name', 'GMW-PS' ); ?></option>
												<option value="slug" <?php if ( $selected == 'slug' ) echo 'selected="selected"'; ?>><?php _e( 'Slug', 'GMW-PS' ); ?></option>
											</select>
										</div>
									</div>

									<div style="width:33%;vertical-align: top;display: inline-block;float:right">
										<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444"><?php _e( 'Order', 'GMW-PS'); ?></label>
											<div style="text-align:center">
											<?php $selected = ( isset( $tax_option['order'] ) ) ? $tax_option['order'] : ''; ?>
											<div style="width:100%;background: #f7f7f7; padding: 5px; box-sizing: border-box; border:1px solid #e5e5e5; display: inline-block;">
												<select name="gmw_forms[<?php echo $_GET['formID'];?>][<?php echo $section;?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][order]" style="width:100%" >
													<option value="ASC" selected="selected"><?php _e( 'ASC', 'GMW-PS' ); ?></option>
													<option value="DESC" <?php if ( $selected == 'DESC' ) echo 'selected="selected"'; ?>><?php _e( 'DESC', 'GMW-PS' ); ?></option>
												</select>
											</div>
										</div>									
									</div>

								</li>
								<li class="table-within">
									<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444">
										<?php _e( 'Exclude terms ( terms ID comma separated)','GMW-PS' ); ?>
									</label>
									<div style="width:100%;background: #f7f7f7; padding: 5px; box-sizing: border-box; border:1px solid #e5e5e5; display: inline-block;">
										<input type="text" style="width:100%" name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][exclude]" value="<?php if ( isset( $tax_option['exclude'] ) ) echo $tax_option['exclude']; ?>"/>
									</div>
								</li>
								<li class="table-within">
									<label style="padding:8px 10px; background: #C2D7EF; width:100% !important; float: left; box-sizing: border-box; margin-bottom: 1px; font-size:12px; font-weight: normal; color:#444">
										<?php _e( 'Include terms by ID ( terms ID comma separated)','GMW-PS' ); ?>
									</label>
									<div style="width:100%;background: #f7f7f7; padding: 5px; box-sizing: border-box; border:1px solid #e5e5e5; display: inline-block;">
										<input type="text" style="width:100%" name="gmw_forms[<?php echo $_GET['formID']; ?>][<?php echo $section; ?>][taxonomies][<?php echo $post;?>][<?php echo $tax; ?>][include]" value="<?php if ( isset( $tax_option['include'] ) ) echo $tax_option['include']; ?>"/>
									</div>
								</li>	
							</ul>							
						</div>		
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?> 
		</div>
		<?php 
		wp_enqueue_script('jquery-ui-sortable'); 
		?>
		<script>
		jQuery(document).ready(function($) {

			$('.gmw-st-btns').change(function() {

				if ( $(this).attr('checked',true).val() == 'checkbox' ) {
					$(this).closest('.taxonomy-wrapper').find('.cb-hidden').show();
				} else {
					$(this).closest('.taxonomy-wrapper').find('.cb-hidden').hide();
				};

				if ( $(this).attr('checked',true).val() == 'pre_defined' ) {
					$(this).closest('.taxonomy-wrapper').find('.cb-pd-hidden').hide();
				} else {
					$(this).closest('.taxonomy-wrapper').find('.cb-pd-hidden').show();
					if ( $(this).attr('checked',true).val() == 'checkbox' ) {
						$(this).closest('.taxonomy-wrapper').find('.cb-hidden').show();
					} else {
						$(this).closest('.taxonomy-wrapper').find('.cb-hidden').hide();
					};
				};
			});	
			
			$(".post-types-tax").click(function() {
				
				var cCount   = $(this).closest(".posts-checkboxes-wrapper").find(":checkbox:checked").length;
				var scId     = $(this).closest(".posts-checkboxes-wrapper").attr('id');
				var pChecked = $(this).attr('id');
				
				if (cCount == 1  ) {
					var n = $(this).closest(".posts-checkboxes-wrapper").find(":checkbox:checked").attr('id');
					$("#taxonomies-wrapper ." + n + "_cat").show();					
				} else {
					$(".taxonomy-wrapper").hide().find(".radio-pd").attr('checked',true);
				}					
			});

			$("#taxonomies-wrapper").sortable({
				items:'.taxonomy-wrapper',
                opacity: 0.5,
                cursor: 'pointer',
                axis: 'y',
                handle:'.gmw-taxonomy-sort-handle'
		    });
		});
		</script>
		<?php 			
	}
	
	/**
	 * address field form settings
	 *
	 */
	public function form_settings_address_fields( $gmw_forms, $formID, $section, $option ) {
	?>
        <div class="gmw-ssb">
                <div id="gmw-af">
                        <table>
                                <tr>
                                    <td style="background: #C2D7EF;padding:5px 15px;"><?php _e( 'Usage','GMW-PS' ); ?></td>
                                    <td style="border:1px solid #bbb;padding:5px">
                                            <input type="radio" class="gmw-af-buttons" id="gmw-af-single-btn" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields][how]'; ?>" value="single" <?php if ( !isset( $gmw_forms[$formID][$section]['address_fields']['how'] ) || $gmw_forms[$formID][$section]['address_fields']['how'] == 'single' ) echo 'checked="checked"'; ?> /><?php _e( 'Single field','GMW-PS' ); ?>
                                            <span>
                                                    <input type="radio" style="margin-left:5px;" class="gmw-af-buttons" id="gmw-af-multiple-btn" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields][how]'; ?>" value="multiple" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields']['how']) && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple' ) echo 'checked="checked"'; ?>   /><?php _e( 'Multiple fields','GMW-PS' ); ?>
                                            </span>
                                    </td>
                                </tr>
                        </table>
                        <div id="gmw-af-single">

                                <table class="gmw-saf-table">
                                        <tr>
                                            <th>Field Type</th>
                                            <th>Field Actions</th>
                                        </tr>
                                        <tr>
                                            <td style="border:1px solid #bbb;padding:5px">
                                                    <?php echo _e( 'Address','GMW-PS' ); ?>
                                            </td>
                                            <td style="border:1px solid #bbb;padding:5px">
													<p>
                                                    	<label for="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_field][title]'; ?>"><?php echo _e('Field label:','GMW-PS'); ?></label>
                                                    	<input type="text" id="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_field][title]'; ?>" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_field][title]'; ?>" size="40" value="<?php if ( isset( $gmw_forms[$formID][$section]['address_field']['title']) ) echo $gmw_forms[$formID][$section]['address_field']['title']; else echo '' ; ?>" />	      
													</p>
													<p>
                                                    	<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_field][within]'; ?>" <?php echo ( isset( $gmw_forms[$formID][$section]['address_field']['within'])) ? " checked=checked " : ""; ?>>	
                                                    	<?php echo _e('Label as placeholder','GMW-PS'); ?>
													</p>                                   
													<p>
                                                    	<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_field][mandatory]'; ?>" <?php echo (isset( $gmw_forms[$formID][$section]['address_field']['mandatory'])) ? " checked=checked " : ""; ?>>	
                                                    	<label><?php echo _e('Mandatory Field','GMW-PS'); ?></label>
                                                    </p>
                                                    <p>
										                <input type="checkbox" value="1" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][address_field][address_autocomplete]'; ?>" <?php if ( isset( $gmw_forms[$formID][$section]['address_field']['address_autocomplete'] ) ) echo 'checked="checked"'; ?>>	
										                <?php _e( 'Google Places address autocomplete', 'GMW-PS' ); ?>
										            </p>
                                            </td>
                                        </tr>
                                </table>
                        </div>
                        <div id="gmw-af-multiple" style="display:none;">

                                <?php $addressFields = array('street','apt','city', 'state','zipcode','country'); ?>

                                <table class="gmw-saf-table">
                                        <tr>
                                            <th>Field Type</th>
                                            <th style="min-width:115px;">Action</th>
                                            <th>Field Info</th>
                                        </tr>
                                        <?php foreach ($addressFields as $field ) : $sy = false;?>
                                                <tr class="gmw-saf">
                                                        <td>
                                                            <span style="padding:0px 5px;font-size: 12px;text-transform: capitalize;"><?php echo $field; ?></span>
                                                        </td>
                                                        <td style="padding:3px 5px">
                                                            <div style="float:left;padding:4px;">
                                                                    <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][on]'; ?>" value="exclude" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['on'] ) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'exclude' || empty( $gmw_forms[$formID][$section]['address_fields'][$field]['on']) || $gmw_forms[$formID][$section]['address_fields']['how'] == 'single' ) echo " checked=checked "; ?> />	
                                                                    <span style="text-transform:capitalize"><?php echo _e('Exclude','GMW-PS'); ?></span>

                                                                    <br />

                                                                    <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][on]'; ?>" value="include" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['on'] ) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'include' && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple' ) echo " checked=checked "; ?> />	
                                                                    <span style="text-transform:capitalize"><?php echo _e('Include','GMW-PS'); ?></span>

                                                                    <br />

                                                                    <input type="radio" class="gmw-saf-btn" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][on]'; ?>" value="default" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['on'] ) && $gmw_forms[$formID][$section]['address_fields'][$field]['on'] == 'default' && $gmw_forms[$formID][$section]['address_fields']['how'] == 'multiple' ) echo " checked=checked "; ?> />	
                                                                    <span style="text-transform:capitalize"><?php echo _e('Pre-defined Value','GMW-PS'); ?></span>

                                                            </div>
                                                        </td>

                                                        <td  style="min-width: 300px;padding:3px 5px">
                                                                <div class="gmw-saf-settings" style="flaot:left">
                                                                        <?php echo _e( 'Field Title:','GMW-PS' ); ?>
                                                                        <input type="text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][title]'; ?>" size="25" value="<?php echo ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['title'] ) ) ? $gmw_forms[$formID][$section]['address_fields'][$field]['title'] : '' ; ?>" />

                                                                        <br />

                                                                        <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][within]'; ?>" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['within']) ) echo " checked=checked "; ?>>	
                                                                        <label><?php echo _e('Within the address field','GMW-PS'); ?></label>

                                                                        <br />

                                                                        <input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][mandatory]'; ?>" <?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['mandatory']) ) echo " checked=checked "; ?>>	
                                                                        <label><?php echo _e('Mandatory Field','GMW-PS'); ?></label>

                                                                        <?php /* &nbsp;&nbsp; | &nbsp;&nbsp;
                                                                        <input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_fields]['.$field.'][dropdown]'; ?>" <?php if ( isset($option['address_fields'][$field]['dropdown']) ) echo " checked=checked "; ?>>						
                                                                        <?php echo _e('Dropdown menu','GMW-PS'); ?>
                                                                        &nbsp;&nbsp; | &nbsp;&nbsp;
                                                                        <?php echo _e('dropdown Values:','GMW-PS'); ?>
                                                                        <textarea style="vertical-align:top;" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_fields]['.$field.'][drop_values]'; ?>" ><?php if ( isset($option['address_fields'][$field]['drop_values']) ) echo $option['address_fields'][$field]['drop_values']; ?></textarea>
                                                                        */ ?>
                                                                </div>
                                                                <div class="gmw-saf-default" style="flaot:left">
                                                                        <?php echo _e('Default Value:','GMW-PS'); ?>
                                                                        <input type="text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][address_fields]['.$field.'][value]'; ?>" size="15" value="<?php if ( isset( $gmw_forms[$formID][$section]['address_fields'][$field]['value']) ) echo $gmw_forms[$formID][$section]['address_fields'][$field]['value']; ?>" />
                                                                </div>
                                                        </td>
                                                </tr>
                                        <?php endforeach; ?>
                                </table>
                        </div>
                </div>
            </div>

            <?php 
	}
		
	/**
	 * results template form settings
	 *
	 */
	public function form_settings_keywords( $gmw_forms, $formID, $section, $option ) {
	?>
		<div id="gmw-ks">
			<p>
				<input type="radio" class="gmw-ks-buttons hide" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_field]'; ?>" value="dont" <?php echo ( isset( $gmw_forms[$formID][$section]['keywords_field'] ) && $gmw_forms[$formID][$section]['keywords_field'] == 'dont' || empty( $gmw_forms[$formID][$section]['keywords_field'] ) ) ? " checked=checked " : ""; ?>>	
				<label><?php echo _e('Do not Search','GMW-PS'); ?></label>
				
				<input type="radio" class="gmw-ks-buttons show" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_field]'; ?>" value="title" <?php echo ( isset( $gmw_forms[$formID][$section]['keywords_field'] ) && $gmw_forms[$formID][$section]['keywords_field'] == 'title' ) ? " checked=checked " : ""; ?>>	
				<label><?php echo _e('Search post tilte','GMW-PS'); ?></label>
			
				<input type="radio" class="gmw-ks-buttons show" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_field]'; ?>" value="content" <?php echo ( isset( $gmw_forms[$formID][$section]['keywords_field'] ) && $gmw_forms[$formID][$section]['keywords_field'] == 'content'  ) ? " checked=checked " : ""; ?>>	
				<label><?php echo _e('Search post title and post content','GMW-PS'); ?></label>
			</p>
		</div>
		<div id="gmw-ks-title" style="display:none;" >
			<p>
				<label for="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_title]'; ?>"><?php echo _e('Field label: ','GMW-PS'); ?></label>
				<input id="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_title]'; ?>" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_title]'; ?>" size="30" type="text" value="<?php echo ( isset( $gmw_forms[$formID][$section]['keywords_title'] ) && !empty( $gmw_forms[$formID][$section]['keywords_title']) ) ? $gmw_forms[$formID][$section]['keywords_title'] : ''; ?>" />
			</p>	
			<p>
				<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][keywords_within]'; ?>" <?php echo (isset( $gmw_forms[$formID][$section]['keywords_within'] ) ) ? " checked=checked " : ""; ?>>
				<lable><?php echo _e('Label as placeholder','GMW-PS'); ?></lable>
			</p>
		</div>
	<?php 
	}
        
        /**
	 * results template form settings
	 *
	 */
	public function form_settings_radius( $gmw_forms, $formID, $section, $option ) {
	?>		
		<div>
			<p>
				<?php echo _e('Radius Values: ','GMW-PS'); ?>
                <input id="setting-radius" class="regular-text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][radius]'; ?>" size="30" type="text" value="<?php echo ( isset( $gmw_forms[$formID][$section]['radius'] ) && !empty( $gmw_forms[$formID][$section]['radius']) ) ? $gmw_forms[$formID][$section]['radius'] : '5,10,15,25,50,100'; ?>" />
            </p>
            <p>
				<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][radius_slider][use]'; ?>" <?php echo (isset( $gmw_forms[$formID][$section]['radius_slider']['use'] ) ) ? " checked=checked " : ""; ?>>
				<label><?php echo _e('Use field as a Slider?','GMW-PS'); ?></label>
			</p>
            <p>
				<?php echo _e('Slider Default values: ','GMW-PS'); ?>
                <input id="setting-radius" class="regular-text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][radius_slider][default_value]'; ?>" size="5    " type="text" value="<?php echo ( isset( $gmw_forms[$formID][$section]['radius_slider']['default_value'] ) && !empty( $gmw_forms[$formID][$section]['radius_slider']['default_value']) ) ? $gmw_forms[$formID][$section]['radius_slider']['default_value'] : '50'; ?>" />
			</p>
		</div>
	<?php 
	}
	
	/**
	 * auto results settings
	 *
	 */
	public function form_settings_custom_fields( $gmw_forms, $formID, $section, $option ) {
	?>
		<div class="gmw-custom-fields">
	 		<div >
				<div id="taxes-<?php echo $formID; ?>" style=" padding: 8px;">
					<?php 
			        global $wpdb;
			        $keys = $wpdb->get_col( "
			        	SELECT meta_key
			        	FROM $wpdb->postmeta
			        	GROUP BY meta_key
			        	ORDER BY meta_id DESC"
			        	);
			        if ( $keys ) natcasesort($keys);
					?>
					<select class="gmw-custom-fields-select gmw-cf-chosen">
						<?php foreach ($keys as $key) : ?>			
						   	<option value="<?php echo $key; ?>"><?php echo $key; ?></option>										
						<?php endforeach; ?>
					</select>	
					<input type="button" class="button-primary gmw-custom-fields-btn" style="font-size: 11px;line-height: 20px;height: 25px;margin-left: 5px" form_id="<?php echo $formID; ?>" section="<?php echo $section; ?>" value="<?php _e( 'Add custom field', 'GMW-PS');?>" /><br />
					<input type="hidden" class="gmw-cf-type">
					<input type="hidden" class="gmw-cf-single-wrapper">
					<input type="hidden" class="gmw-cf-date-type">
					<table class="gmw-cf-holder" style="min-width: 500px;margin-top:10px;">
						<thead>
							<tr>
								<th>Field name</th>
								<th>Field title</th>
								<th>Field Type</th>
								<th>Compare</th>
								<th>Date Type</th>
								<th></th>
							</tr>
						</thead>

						<?php if ( isset( $gmw_forms[$formID][$section]['custom_fields'] ) || !empty( $gmw_forms[$formID][$section]['custom_fields'] ) ) : ?>
							
								<?php foreach ( $gmw_forms[$formID][$section]['custom_fields'] as $key => $value) : ?>
									
									<?php if ($gmw_forms[$formID][$section]['custom_fields'][$key]['on'] == 1 ) : ?>
								
										<tr class="gmw-cf-single-wrapper">
											<td class="cf-title">
									   			<input type="hidden" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][custom_fields]['.$key.'][on]'; ?>"><?php echo $key; ?>
											</td>
											<td style="min-width: 150px;">
												<input type="text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][custom_fields]['.$key.'][name]'; ?>" value="<?php echo $gmw_forms[$formID][$section]['custom_fields'][$key]['name']; ?>"  size="20" />
											</td>
											<td>
												<select class="gmw-cf-type" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][custom_fields]['.$key.'][type]'; ?>">
													<option value="CHAR"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['type'] == 'CHAR'  ) echo 'selected="selected"'; ?> >CHAR</option>
													<option value="NUMERIC" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['type'] == 'NUMERIC' ) echo 'selected="selected"'; ?> >NUMERIC</option>
													<option value="DATE"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['type'] == 'DATE'  ) echo 'selected="selected"'; ?> >DATE</option>					
												</select>
											</td>
											<td>
												<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][custom_fields]['.$key.'][compare]'; ?>">
													<option value="&#61;"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '='  ) echo 'selected="selected"'; ?> >&#61;</option>
													<option value="&#33;&#61;" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '!=' ) echo 'selected="selected"'; ?> >&#33;&#61;</option>
													<option value="&#62;"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '>'  ) echo 'selected="selected"'; ?> >&#62;</option>
													<option value="&#62;&#61;" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '>=' ) echo 'selected="selected"'; ?> >&#62;&#61;</option>
													<option value="&#60;"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '<'  ) echo 'selected="selected"'; ?> >&#60;</option>
													<option value="&#60;&#61;"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == '<='  ) echo 'selected="selected"'; ?> >&#60;&#61;</option>
													<option value="LIKE"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == 'LIKE'  ) echo 'selected="selected"'; ?> >LIKE</option>
													<option value="NOT LIKE"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == 'NOT LIKE'  ) echo 'selected="selected"'; ?> >NOT LIKE</option>
													<option value="BETWEEN"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == 'BETWEEN'  ) echo 'selected="selected"'; ?> >BETWEEN</option>
													<option value="NOT BETWEEN"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['compare'] == 'NOT BETWEEN'  ) echo 'selected="selected"'; ?> >NOT BETWEEN</option>		
												</select>
											</td>
											<td style="width:110px;">
												<select class="gmw-cf-date-type" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][custom_fields]['.$key.'][date_type]'; ?>" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['type']!= 'DATE' ) echo 'style="display:none;"'; ?>>
													<option value="mm/dd/yy"  <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['date_type'] == 'mm/dd/yy'  ) echo 'selected="selected"'; ?> >MM/DD/YYYY</option>
													<option value="dd/mm/yy" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['date_type'] == 'dd/mm/yy' ) echo 'selected="selected"'; ?> >DD/MM/YYYY</option>
													<option value="yy/mm/dd" <?php if ( $gmw_forms[$formID][$section]['custom_fields'][$key]['date_type'] == 'yy/mm/dd' ) echo 'selected="selected"'; ?> >YYYY/MM/DD</option>				
												</select>							
											</td>
											<td>
												<input type="button" value="Delete" class="button action gmw-custom-fields-remove-btn">
											</td>
										</tr>
																	
									<?php  endif; ?>
								<?php endforeach; ?>
							</tbody>
						<?php endif; ?>						
					</table>
				</div>
			</div>
		</div>
	<?php 

		if ( !wp_script_is( 'chosen', 'enqueued' ) ) {
			
			wp_enqueue_script( 'chosen' );
			wp_enqueue_style( 'chosen' );

			?>
			<script>
			jQuery(document).ready(function($) {
				$(".gmw-cf-chosen").chosen({
				    no_results_text: 'no custom fields found.',
				    width:"300px",
				    placeholder_text: 'Type custom field name...'
				});
			});
			</script>
			<?php 
		}
	}
	
	/**
	 * excerpt 
	 */
	public function per_results_map( $gmw_forms, $formID, $section, $option ) {
	?>
		<div>
			<p>
				<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][per_results_map][use]'; ?>" <?php echo ( isset( $gmw_forms[$formID][$section]['per_results_map']['use'] ) ) ? "checked=checked" : ""; ?>>
				<label><?php echo _e('Yes','GMW-PS'); ?></label>	
			</p>
			<p>
				<label><?php echo _e('Map height:','GMW-PS'); ?></label>
				<input type="text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][per_results_map][map_height]'; ?>" value="<?php echo ( isset( $gmw_forms[$formID][$section]['per_results_map']['map_height'] ) && !empty( $gmw_forms[$formID][$section]['per_results_map']['map_height'] ) ) ? $gmw_forms[$formID][$section]['per_results_map']['map_height'] : '200px'; ?>" size="5"> Pixels or percentage
			</p>
			<p>	
				<label><?php echo _e('Map width:','GMW-PS'); ?></label>
				<input type="text" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][per_results_map][map_width]'; ?>" value="<?php echo ( isset( $gmw_forms[$formID][$section]['per_results_map']['map_width'] ) && !empty( $gmw_forms[$formID][$section]['per_results_map']['map_width'] ) ) ? $gmw_forms[$formID][$section]['per_results_map']['map_width'] : '200px'; ?>" size="5"> Pixels or percentage
			</p>
			<p>
				<?php echo _e('Map Type:','GMW-PS'); ?>			
				<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][per_results_map][map_type]'; ?>">
					<option value="ROADMAP"  selected="selected">ROADMAP</option>
					<option value="SATELLITE" <?php if ( isset( $gmw_forms[$formID][$section]['per_results_map']['map_type'] ) && $gmw_forms[$formID][$section]['per_results_map']['map_type']  == "SATELLITE" ) echo 'selected="selected"'; ?>>SATELLITE</option>
					<option value="HYBRID" <?php if ( isset( $gmw_forms[$formID][$section]['per_results_map']['map_type'] ) && $gmw_forms[$formID][$section]['per_results_map']['map_type']  == "HYBRID" ) echo 'selected="selected"'; ?>>HYBRID</option>
					<option value="TERRAIN" <?php if ( isset( $gmw_forms[$formID][$section]['per_results_map']['map_type'] ) && $gmw_forms[$formID][$section]['per_results_map']['map_type'] == "TERRAIN" ) echo 'selected="selected"'; ?>>TERRAIN</option>
				</select>
			</p>
			<p>
				<input type="checkbox" value="1" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][per_results_map][map_frame]'; ?>" <?php echo (isset( $gmw_forms[$formID][$section]['per_results_map']['map_frame'] ) ) ? " checked=checked " : ""; ?>>
				<label><?php echo _e('Map Frame','GMW-PS'); ?></label>
			</p>
		</div>
	<?php 
	}
	
	public function map_icon_usage( $gmw_forms, $formID, $section, $option ) {
		?>
		<div>
			<p>
				<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="same" <?php if ( (isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'same') || empty( $gmw_forms[$formID][$section]['map_icon_usage']))  echo ' checked="checked"'; ?>>
				<label><?php echo _e('Global','GMW-PS'); ?></label>
				
				<?php if ( isset( $this->settings['post_types_settings']['per_post_icons']) ) : ?>
				
					<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="per_post" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'per_post' ) echo ' checked="checked"'; ?>>
					<lable><?php echo _e('Per Post','GMW-PS'); ?></lable>
				
				<?php endif; ?>
				
				<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="per_post_type" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'per_post_type' ) echo ' checked="checked"'; ?>>
				<label><?php echo _e('Per post type','GMW-PS'); ?></label>
				
				<?php if ( isset( $this->settings['post_types_settings']['per_category_icons']['enabled'] ) ) { ?>
					
					<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="per_category" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage'] ) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'per_category' ) echo ' checked="checked"'; ?>>
					<lable><?php echo _e('Per category','GMW-PS'); ?></lable>
				
				<?php } ?>
				
				<input type="radio" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][map_icon_usage]'; ?>" value="image" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'image' ) echo ' checked="checked"'; ?>>
				<label><?php echo _e( 'Post featured image','GMW-PS' ); ?></label>				
			</p>
		</div>
		<?php 
	}
	
	public function global_map_icon( $gmw_forms, $formID, $section, $option ) {
	?>
	<div class="gmw-ssb">
		<?php
		$map_icons = $this->settings['pt_map_icons']['all_icons'];
		$icons_url = $this->settings['pt_map_icons']['url'];
		$cic 	   = 1;
		
		foreach ( $map_icons as $map_icon ) {
			$checked = ( ( isset( $gmw_forms[$formID][$section]['map_icon'] ) && $gmw_forms[$formID][$section]['map_icon'] == $map_icon ) || $cic == 1 ) ? 'checked="checked"' : '';
			echo '<span><input type="radio" name="gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon]" value="'.$map_icon.'" '.$checked.' />';
			echo '<img src="'.$icons_url.$map_icon.'" style="width:30px;height:auto" /></span>';
			$cic++;
		}
		?>	
	</div>
	<?php
	}
	
	public function your_location_icon( $gmw_forms, $formID, $section, $option ) {
	?>
	<div>
		<?php
		$map_icons = $this->settings['pt_map_icons']['all_icons'];
		$icons_url = $this->settings['pt_map_icons']['url'];
		$cic 	   = 1;
		
		foreach ( $map_icons as $map_icon ) {
			$checked = ( ( isset( $gmw_forms[$formID][$section]['your_location_icon'] ) && $gmw_forms[$formID][$section]['your_location_icon'] == $map_icon ) || $cic == 1 ) ? 'checked="checked"' : '';
			echo '<span><input type="radio" name="gmw_forms[' .$_GET['formID'] .']['.$section.'][your_location_icon]" value="'.$map_icon.'" '.$checked.' />';
			echo '<img src="'.$icons_url.$map_icon.'" style="width:30px;height:auto" /></span>';
			$cic++;
		}
		?>
	</div>
	<?php 
	}
	
	public function wider_search( $gmw_forms, $formID, $section, $option ) {
	?>
		<div>
			<p>
				<input type="checkbox"  value="1"  name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][wider][on]'; ?>" <?php echo ( isset( $gmw_forms[$formID][$section]['wider']['on'] ) ) ? "checked=checked" : ""; ?>>
				<label><?php echo _e('Show','GMW-PS'); ?></label>
			</p>
			<p>
				<?php echo _e('Wider distance value: ','GMW-PS'); ?>
				<input type="text" size="10" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][wider][radius]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['wider']['radius']) ) echo $gmw_forms[$formID][$section]['wider']['radius']; ?>" />
			</p>
			<p>
				<?php echo _e('Title before: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][wider][before]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['wider']['before']) ) echo $gmw_forms[$formID][$section]['wider']['before']; ?>" />
			</p>
			<p>
				<?php echo _e('Link title: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][wider][link_title]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['wider']['link_title']) ) echo $gmw_forms[$formID][$section]['wider']['link_title']; else echo 'click here'; ?>" />
			</p>
			<p>
				<?php echo _e('Title after: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][wider][after]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['wider']['after']) ) echo $gmw_forms[$formID][$section]['wider']['after']; ?>" />
			</p>
		</div>
	<?php
	}
	
	public function all_results( $gmw_forms, $formID, $section, $option ) {
	?>
		<div class="gmw-ssb">
			<p>
				<input type="checkbox"  value="1"  name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][all][on]'; ?>" <?php echo ( isset( $gmw_forms[$formID][$section]['all']['on'] ) ) ? "checked=checked" : ""; ?>>
				<label><?php echo _e('Show','GMW-PS'); ?></label>
			</p>
			<p>
				<?php echo _e('Title before: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][all][before]'; ?>" size="40" value="<?php if ( isset( $gmw_forms[$formID][$section]['all']['before'] ) ) echo $gmw_forms[$formID][$section]['all']['before']; ?>" />
			</p>
			<p>
				<?php echo _e('Link title: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][all][link_title]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['all']['link_title']) ) echo $gmw_forms[$formID][$section]['all']['link_title']; else echo 'click here'; ?>" />
			</p>
			<p>
				<?php echo _e('Title after: ','GMW-PS'); ?>
				<input type="text" size="25" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][all][after]'; ?>" size="40" value="<?php if ( isset($gmw_forms[$formID][$section]['all']['after']) ) echo $gmw_forms[$formID][$section]['all']['after']; ?>" />
			</p>
		</div>
	<?php
	}
	
        /**
	 * results template form settings posts
	 *
	 */
	public function posts_info_window_theme( $gmw_forms, $formID, $section, $option ) {
	?>
	<div id="gmw-ps-infobox-themes-dropdown" class="gmw-ps-themes-dropdown infobox" style="display:none;">
		<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][infobox_template]'; ?>">					
			<?php foreach ( glob( GMW_PS_PATH .'/posts/templates/infobox/*' , GLOB_ONLYDIR ) as $dir ) { ?>
				<?php $dir = basename( $dir ); ?>									
				<?php $selected = ( isset( $gmw_forms[$formID][$section]['infobox_template'] ) && $gmw_forms[$formID][$section]['infobox_template'] == $dir ) ? 'selected="selected"' : ''; ?>
				<option value="<?php echo $dir; ?>" <?php echo $selected; ?>><?php echo $dir; ?></option>
			<?php } ?>
			
			<?php if ( is_dir( STYLESHEETPATH. '/geo-my-wp/posts/info-window-templates/infobox/' ) ) { ?>
				<?php foreach ( glob( STYLESHEETPATH. '/geo-my-wp/posts/info-window-templates/infobox/*' , GLOB_ONLYDIR ) as $dir ) { ?>
					<?php $dir = basename( $dir ); ?>							
					<?php $cThems = 'custom_'.$dir; ?>
					<?php $selected = ( isset( $gmw_forms[$formID][$section]['infobox_template'] ) && $gmw_forms[$formID][$section]['infobox_template'] == $cThems ) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $cThems; ?>" <?php echo $selected; ?>><?php _e( 'Custom Template:' );?> <?php echo $dir; ?></option>					
				<?php } ?>
			<?php } ?>					
		</select>
	</div>
	
	<div id="gmw-ps-popup-themes-dropdown" class="gmw-ps-themes-dropdown popup" style="display:none;">
		<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][popup_template]'; ?>">
			<?php foreach ( glob( GMW_PS_PATH .'/posts/templates/popup/*' , GLOB_ONLYDIR ) as $dir ) { ?>
				<?php $dir = basename( $dir ); ?>			
				<?php $selected = ( isset( $gmw_forms[$formID][$section]['popup_template'] ) && $gmw_forms[$formID][$section]['popup_template'] == $dir ) ? 'selected="selected"' : ''; ?>						
				<option value="<?php echo $dir; ?>" <?php echo $selected; ?>><?php echo $dir; ?></option>
			<?php } ?>
			
			<?php if ( is_dir( STYLESHEETPATH. '/geo-my-wp/posts/info-window-templates/popup' ) ) { ?>
				<?php foreach ( glob( STYLESHEETPATH. '/geo-my-wp/posts/info-window-templates/popup/*' , GLOB_ONLYDIR ) as $dir ) { ?>
					<?php $dir = basename( $dir ); ?>							
					<?php $cThems = 'custom_'.$dir; ?>
					<?php $selected = ( isset( $gmw_forms[$formID][$section]['popup_template'] ) && $gmw_forms[$formID][$section]['popup_template'] == $cThems ) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $cThems; ?>" <?php echo $selected; ?>><?php _e( 'Custom Template:' );?> <?php echo $dir; ?></option>					
				<?php } ?>
			<?php } ?>		
		</select>
	</div>
	
	<script>				
		jQuery(document).ready(function($) {

			function info_window_type_toggler() {

				typeElement = $('.setting-iw_type' );
				checkedType = $('.setting-iw_type:checked').val();

				$('.gmw-ps-themes-dropdown').hide();
				$('.gmw-ps-themes-dropdown.'+ checkedType ).slideToggle();
				
				if ( checkedType == 'normal' ) {

					typeElement.closest('tbody').find('tr').hide();
					typeElement.closest('tr').show();

				} else {
					typeElement.closest('tbody').find('tr').show();
				}
			}

			info_window_type_toggler();

			$('.setting-iw_type').on( 'change', function() {	
				info_window_type_toggler();
			});
		});
	</script>
	<?php 
	}
	
	
	/**
	 * form settings function.
	 *
	 * @access public
	 * @return $settings
	 */
	function pt_form_settings_init($settings) {
				            
         $settings['search_form'][1]['radius'] = array( 
                                'name'       => 'radius',
				'std'        => '',
				'label'      => __( 'Radius / Distance', 'GMW-PS' ),
				'desc'       => __( 'Enter distance values in the input box comma separated if you want to have a select dropdown menu of multiple radius values in the search form. If only one value entered it will be the default value of the search form which will be hidden.', 'GMW-PS' ),
				'type'       => 'function',
				'function'   => 'radius'
                );
               
		$settings['search_form'][1]['form_taxonomies'] = array(
				
				'name'       => 'form_taxonomies',
				'std'        => '',
				'label'      => __( 'Taxonomies', 'GMW-PS' ),
				'desc'       => __( 'Choose taxonomies that you want to display in the search form. you can display any of them using selectbox or checkboxes. you can use the "Exclude" box to enter term IDs (comma separated) of the terms that you want to exclude from the search results. use the "pre defined" checkbox whem you want set a taxonomiy as default (will be hidden) in the search form.', 'GMW-PS' ),
				'type'       => 'function',
				'function'   => 'taxonomies_premium'
		);
		
		$settings['search_form'][1]['address_field'] = array(
				
				'name'       => 'address_field',
				'std'        => '',
				'label'      => __( 'Address field', 'GMW-PS' ),
				'cb_label'   => '',
				'desc'       => __( 'Type the title for the address field of the search form. for example "Enter your address". this title wll be displayed either next to the address input field or within if you check the checkbox for it. You can also choose to have the address field mandatory which will prevent users from submitting the form if no address entered. Otherwise if you allow the field to be empty and user submit a form with no address the plugin will display all results.', 'GMW-PS' ),
				'type'       => 'function',
				'function'   => 'address_fields'
		);

		$settings['search_form'][1]['keywords_field'] = array(
		
				'name'       => 'keywords_field',
				'std'        => '',
				'label'      => __( 'Keywords field', 'GMW-PS' ),
				'desc'       => __( 'Create additional input box to let users search based on keywords. You can let them search based on post title or post title and post content.', 'GMW-PS' ),
				'type'       => 'function',
				'function'   => 'keywords'
		);
	
		$settings['search_form'][1]['custom_fields'] = array(
				'name'       => 'custom_fields',
				'std'        => '',
				'label'      => __( 'Custom fields', 'GMW-PS' ),
				'desc'       => __( 'Create additional input box to let users search based on keywords. You can let them search based on post title or post title and post content.', 'GMW-PS' ),
				'type'       => 'function',
				'function'	 => 'custom_fields'
		);
		
		$settings['results_map'][1]['markers_display'] = array(
				'name'        => 'markers_display',
				'std'         => '',
				'label' 	  => __( 'Markers display', 'GMW-PS'),
				'cb_label'    => __( 'Yes', 'GMW-PS'),
				'desc'        => __( 'Use marker Clusterer to group near locations.', 'GMW-PS' ),
				'type'  	  => 'radio',
				'options'  	  => array(
						'normal'				=> 'Normal',
						'markers_clusterer' 	=> 'Markers clusterer',
						'markers_spiderfier' 	=> 'Markers Spiderfier'
				),
				'attributes'  => array()
		);

		$settings['results_map'][1]['per_results_map'] = array(
				'name'       => 'per_results_map',
				'std'        => '',
				'label'      => __( 'Per results map', 'GMW-PS' ),
				'desc'       => __( 'This featured let you add a single map to each result in the results page with marker shows the location and marker shows the user location. This could be used instead or in addition to the main map.', 'GMW-PS' ),
				'type'       => 'function',
		);
		
		$settings['results_map'][1]['map_icon_usage'] = array(
				'name'       => 'map_icon_usage',
				'std'        => '',
				'label'      => __( 'Map icon usage', 'GMW-PS' ),
				'desc'       => __( 'Choose the map\'s icons that will be used to display results on the map', 'GMW-PS' ),
				'type'       => 'function',
		);		
		$settings['results_map'][1]['global_map_icon'] = array(
				'name'       => 'global_map_icon',
				'std'        => '',
				'label'      => __( 'Default map icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the global icon for google map. All results will use this icon to show its location on the map unless you check the "per post icon" checkbox below. The map icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/main-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);
		
		$settings['results_map'][1]['your_location_icon'] = array(
				'name'       => 'your_location_icon',
				'std'        => '',
				'label'      => __( 'User Location Icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the icon that will show the user location on the map. the "your location" icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/your-location-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);
		
		$settings['results_map'][1]['map_controls'] = array(
				
				'name'        => 'map_controls',
				'std'         => 'true',
				'label'       => __( 'Map controls', 'GMW-PS' ),
				'desc'        => __( 'Which map controls would you like to use', 'GMW-PS' ),
				'type'        => 'multicheckboxvalues',
				'options'     => array(
						'zoomControl' 		=> __( 'Zoom' , 'GMW-PS' ),
						'panControl' 		=> __( 'Pan' , 'GMW-PS' ),
						'scaleControl'   	=> __( 'Scale' , 'GMW-PS' ),
						'mapTypeControl'	=> __( 'Map Type' , 'GMW-PS' ),
						'streetViewControl'	=> __( 'Street View' , 'GMW-PS' ),
						'overviewMapControl'=> __( 'Overview' , 'GMW-PS' ),
						'scrollwheel'		=> __( 'Scrollwheel ( enable map zoom in/out using mouse scroll-wheel )' , 'GMW-PS' ),
						'resizeMapControl'  => __( 'Resize map trigger' , 'GMW-PS' )
				),
		);
					
		$settings['info_window'] = array(
				__( 'Marker window', 'GMW-PS' ),
				array(
						'iw_type'           => array(
								'name'        => 'iw_type',
								'std'         => '',
								'label' 	  => __( 'Marker info-window type', 'GMW-PS'),
								'desc'        => __( 'What type of marker info-window would you like to use? 1) "Infobox" to use the infobox library to generate the info-window within the limits of the map. 2) "Pop-up" to use an HTML pop-up window outside the limit of the map. 3) "Normal" to use the normal info-window generated by GEO m WP.', 'GMW-PS' ),
								'type'  	  => 'radio',
								'options'	  => array(
										'infobox' => 'Infobox',
										'popup'	  => 'Pop-up window',
										'normal'  => 'Normal'
								),
								'attributes'  => array()
						),
						'marker_window_theme'   => array(
								'name'       => 'posts_info_window_theme',
								'std'        => '',
								'label'      => __( 'Info-window template', 'GMW-PS' ),
								'desc'       => __( 'Choose the theme that will display the marker window.', 'GMW-PS'),
								'type'       => 'function'
						),
						'draggable_use'    => array(
						
								'name'        => 'draggable_use',
								'std'         => '',
								'label' 	  => __( 'Draggable marker info-window', 'GMW-PS'),
								'cb_label'    => __( 'Enable', 'GMW-PS'),
								'desc'        => __( "Allow the user to drag the pop-up window across the screen. ( pop-up infoe window only )", 'GMW-PS' ),
								'type'  	  => 'checkbox',
								'attributes'  => array()
						),
						'featured_image'    => array(

								'name'        => 'featured_image',
								'std'         => '',
								'label' 	  => __( 'Featured image', 'GMW-PS'),
								'cb_label'    => __( 'Enable', 'GMW-PS'),
								'desc'        => __( 'Display featured image', 'GMW-PS' ),
								'type'  	  => 'checkbox',
								'attributes'  => array()
						),
						'show_excerpt'      => array(

								'name'       => 'show_excerpt',
								'std'        => '',
								'label'      => __( 'Excerpt', 'GMW-PS' ),
								'desc'       => __( 'This featured will grab the number of words that you choose from the post content and display it. Set the number of words you want to display or leave empty to display the entire content.', 'GMW-PS'),
								'type'		 => 'function'
						),
						'additional_info'   => array(

								'name'        => 'additional_info',
								'std'         => '',
								'label'       => __( 'Additional information', 'GMW-PS' ),
								'desc'        => __( 'Which fields of the additional information would you like to display?', 'GMW-PS' ),
								'type'		  => 'multicheckbox',
								'options'	  => array(
										'phone' 			=> __( 'Phone' , 'GMW-PS' ),
										'fax' 				=> __( 'Fax' , 'GMW-PS' ),
										'email'   			=> __( 'Email' , 'GMW-PS' ),
										'website'			=> __( 'Website' , 'GMW-PS' ),
										'formatted_address'	=> __( 'Address' , 'GMW-PS' ),
								),
						),
						'get_directions'    => array(

								'name'        => 'get_directions',
								'std'         => '',
								'label' 	  => __( 'Get directions link', 'GMW-PS'),
								'cb_label'    => __( 'Yes', 'GMW-PS'),
								'desc'        => __( 'Display get directions link that will open a new window with Google map showting the directions to the location.', 'GMW-PS' ),
								'type'  	  => 'checkbox',
								'attributes'  => array()
						),
						'live_directions'    => array(
								'name'        => 'live_directions',
								'std'         => '',
								'label'       => __( 'Live directions', 'GMW-PS'),
								'cb_label'    => __( 'Enable', 'GMW-PS'),
								'desc'        => __( 'Display directions system inside the info-window. ( available for pop-up info-window only )', 'GMW-PS' ),
								'type'        => 'checkbox',
								'attributes'  => array()
						),
				),

		);
		
		$settings['no_results'] = array(
				__( 'No Results', 'GMW-PS' ),
				array(
						'title'	=> array(
								'name'        => 'title',
								'std'         => __( 'No results found.', 'GMW-PS' ),
								'label'       => __( 'No results title', 'GMW-PS' ),
								'desc'        => __( 'The title that will be displayed when no results found.', 'GMW-PS' ),
								'type'        => 'textarea',
						),
						'wider_search'	=> array(
								'name'       => 'wider_search',
								'std'        => '',
								'label'      => __( 'Wider search link', 'GMW-PS' ),
								'desc'       => __( 'Link that will search within a wider range when no results found.', 'GMW-PS' ),
								'type'       => 'function',
						),
						'all_results'	=> array(
								'name'       => 'all_results',
								'std'        => '',
								'label'      => __( 'All results', 'GMW-PS' ),
								'desc'       => __( 'Create link that will display all results when no results found.', 'GMW-PS' ),
								'type'       => 'function',
						),
				),

		);
		
		return $settings;
	}
		
	public function results_xprofile_fields( $gmw_forms, $formID, $section, $option ) {
		
		global $bp;
		
		if ( bp_is_active ( 'xprofile' ) ) :
			
			if ( function_exists ( 'bp_has_profile' ) ) :
				
				if ( bp_has_profile ( 'hide_empty_fields=0' ) ) :
		
					$dateboxes 	  = array ();
					$dateboxes[0] = '';
		
					while ( bp_profile_groups() ) :
						
						bp_the_profile_group ();
						
						while (bp_profile_fields ()) :
							
							bp_the_profile_field(); 
								
							if ( (bp_get_the_profile_field_type () == 'datebox') )
								$dateboxes[] = bp_get_the_profile_field_id(); 
							
							if ( bp_get_the_profile_field_type () != 'datebox' ) {
								
								$field_id = bp_get_the_profile_field_id(); ?>
								<input type="checkbox" name="<?php echo 'gmw_forms[' .$formID .']['.$section.'][results_profile_fields][]'; ?>" value="<?php echo $field_id; ?>" <?php if ( isset( $gmw_forms[$formID][$section]['results_profile_fields'] ) && in_array( $field_id, $gmw_forms[$formID][$section]['results_profile_fields'] ) ) echo ' checked=checked'; ?>/>
								<label><?php bp_the_profile_field_name(); ?></label>
								<br />
							
							<?php } 
						
						endwhile;
				
					endwhile; ?>
				
					<label><strong style="margin:5px 0px;float:left;width:100%"><?php _e('Choose the "Age Range" Field','GMW-PS'); ?></strong></label><br />
					<select name="<?php echo 'gmw_forms[' .$formID .']['.$section.'][results_profile_fields_date]'; ?>"> 
						<?php foreach ( $dateboxes as $datebox ) {  ?>
							<?php $field = new BP_XProfile_Field( $datebox ); ?>
							<?php $selected = ( $gmw_forms[$formID][$section]['results_profile_fields_date'] == $datebox ) ? 'selected="selected"' : ''; ?>
							<option value="<?php echo $datebox; ?>" <?php echo $selected; ?> ><?php echo $field->name; ?></option>
						<?php } ?>
					</select> 
				<?php 
				endif;
				
			endif;
			 
		endif; 
		
		if ( !bp_is_active ( 'xprofile' ) ) {
			if ( is_multisite() ) 
				$site_url = network_site_url( '/wp-admin/network/admin.php?page=bp-components&updated=true' );
			else 
				$site_url = site_url('/wp-admin/admin.php?page=bp-components&updated=true');
			_e('Your buddypress profile fields are deactivated.  To activate and use them <a href="'.$site_url.'"> click here</a>.','GMW-PS');
		}
	}
	
	public function fl_map_icon_usage( $gmw_forms, $formID, $section, $option ) {
	?>
	<div>
		<p>
			<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="same" <?php if ( (isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'same') || empty( $gmw_forms[$formID][$section]['map_icon_usage']))  echo ' checked="checked"'; ?>>
			<label><?php echo _e('Global','GMW-PS'); ?></label>
			
			<?php //if ( isset( $this->settings['post_types_settings']['per_post_icons']) ) : ?>
			
				<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="per_member" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'per_member' ) echo ' checked="checked"'; ?>>
				<lable><?php echo _e('Per Member','GMW-PS'); ?></lable>
			
			<?php //endif; ?>
			
			<input type="radio" name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon_usage]'; ?>" value="avatar" <?php if ( isset( $gmw_forms[$formID][$section]['map_icon_usage']) && $gmw_forms[$formID][$section]['map_icon_usage'] == 'avatar' ) echo ' checked="checked"'; ?>>
			<lable><?php echo _e('Avatar','GMW-PS'); ?></lable>		
		</p>
	</div>
	<?php 
	}
	
	public function fl_global_map_icon( $gmw_forms, $formID, $section, $option ) {
	?>
		<div class="gmw-ssb">
			<?php
			$map_icons = $this->settings['ml_map_icons']['all_icons'];
			$icons_url = $this->settings['ml_map_icons']['url'];
			$cic 	   = 1;
		
			foreach ( $map_icons as $map_icon ) {
				$checked = ( ( isset( $gmw_forms[$formID][$section]['map_icon'] ) && $gmw_forms[$formID][$section]['map_icon'] == $map_icon ) || $cic == 1 ) ? 'checked="checked"' : '';
				echo '<span><input type="radio" name="gmw_forms[' .$_GET['formID'] .']['.$section.'][map_icon]" value="'.$map_icon.'" '.$checked.' />';
				echo '<img src="'.$icons_url.$map_icon.'" style="width:30px;height:auto" /></span>';
				$cic++;
			}
			?>	
		</div>
	<?php
	}
	
	public function fl_your_location_icon( $gmw_forms, $formID, $section, $option ) {
	?>
	<div>
	<?php
		$map_icons = $this->settings['ml_map_icons']['all_icons'];
		$icons_url = $this->settings['ml_map_icons']['url'];
		$cic 	   = 1;
	
		foreach ( $map_icons as $map_icon ) {
			$checked = ( ( isset( $gmw_forms[$formID][$section]['your_location_icon'] ) && $gmw_forms[$formID][$section]['your_location_icon'] == $map_icon ) || $cic == 1 ) ? 'checked="checked"' : '';
			echo '<span><input type="radio" name="gmw_forms[' .$_GET['formID'] .']['.$section.'][your_location_icon]" value="'.$map_icon.'" '.$checked.' />';
			echo '<img src="'.$icons_url.$map_icon.'" style="width:30px;height:auto" /></span>';
			$cic++;
		}
	?>	
	</div>
	<?php 
	}
        
     /**
	 * results template form settings members
	 *
	 */
	public function bp_members_info_window_theme( $gmw_forms, $formID, $section, $option ) {
	?>
	<div id="gmw-ps-infobox-themes-dropdown" class="gmw-ps-themes-dropdown infobox" style="display:none;">
		<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][infobox_template]'; ?>">					
			<?php foreach ( glob( GMW_PS_PATH .'/friends/templates/infobox/*' , GLOB_ONLYDIR ) as $dir ) { ?>
				<?php $dir = basename( $dir ); ?>
				<?php $selected = ( isset( $gmw_forms[$formID][$section]['infobox_template'] ) && $gmw_forms[$formID][$section]['infobox_template'] == $dir ) ? 'selected="selected"' : ''; ?>								
				<option value="<?php echo $dir; ?>" <?php echo $selected; ?>><?php echo $dir; ?></option>
			<?php } ?>
			
			<?php if ( is_dir( STYLESHEETPATH. '/geo-my-wp/friends/info-window-templates/infobox/' ) ) { ?>
				<?php foreach ( glob( STYLESHEETPATH. '/geo-my-wp/friends/info-window-templates/infobox/*' , GLOB_ONLYDIR ) as $dir ) { ?>
					<?php $dir = basename( $dir ); ?>							
					<?php $cThems = 'custom_'.$dir; ?>
					<?php $selected = ( isset( $gmw_forms[$formID][$section]['infobox_template'] ) && $gmw_forms[$formID][$section]['infobox_template'] == $cThems ) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $cThems; ?>" <?php echo $selected; ?>><?php _e( 'Custom Template:' );?> <?php echo $dir; ?></option>					
				<?php } ?>
			<?php } ?>					
		</select>
	</div>
	
	<div id="gmw-ps-popup-themes-dropdown" class="gmw-ps-themes-dropdown popup" style="display:none;">
		<select name="<?php echo 'gmw_forms[' .$_GET['formID'] .']['.$section.'][popup_template]'; ?>">
			<?php foreach ( glob( GMW_PS_PATH .'/posts/templates/popup/*' , GLOB_ONLYDIR ) as $dir ) { ?>
				<?php $dir = basename( $dir ); ?>
				<?php $selected = ( isset( $gmw_forms[$formID][$section]['popup_template'] ) && $gmw_forms[$formID][$section]['popup_template'] == $dir ) ? 'selected="selected"' : ''; ?>													
				<option value="<?php echo $dir; ?>" <?php echo $selected; ?>><?php echo $dir; ?></option>
			<?php } ?>
			
			<?php if ( is_dir( STYLESHEETPATH. '/geo-my-wp/friends/info-window-templates/popup' ) ) { ?>
				<?php foreach ( glob( STYLESHEETPATH. '/geo-my-wp/friends/info-window-templates/popup/*' , GLOB_ONLYDIR ) as $dir ) { ?>
					<?php $dir = basename( $dir ); ?>							
					<?php $cThems = 'custom_'.$dir; ?>
					<?php $selected = ( isset( $gmw_forms[$formID][$section]['popup_template'] ) && $gmw_forms[$formID][$section]['popup_template'] == $cThems ) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo $cThems; ?>" <?php echo $selected; ?>><?php _e( 'Custom Template:' );?> <?php echo $dir; ?></option>					
				<?php } ?>
			<?php } ?>		
		</select>
	</div>
	<script>
	jQuery(document).ready(function($) {

		function info_window_type_toggler() {

			typeElement = $('.setting-iw_type' );
			checkedType = $('.setting-iw_type:checked').val();

			$('.gmw-ps-themes-dropdown').hide();
			$('.gmw-ps-themes-dropdown.'+ checkedType ).slideToggle();
			
			if ( checkedType == 'normal' ) {

				typeElement.closest('tbody').find('tr').hide();
				typeElement.closest('tr').show();

			} else {
				typeElement.closest('tbody').find('tr').show();
			}
		}

		info_window_type_toggler();

		$('.setting-iw_type').on( 'change', function() {	
			info_window_type_toggler();
		});	
	});
	</script>
	<?php 
	}
	
	/**
	 * results template form settings
	 *
	 */
	public function fl_keyword_search( $gmw_forms, $formID, $section, $option ) {
	?>
		<div id="gmw-ks">
			<p>
				<input type="radio" class="gmw-ks-buttons hide" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_field]'; ?>" value="dont" checked="checked">	
				<label><?php echo _e('Do not Search','GMW-PS'); ?></label>
				
				<input type="radio" class="gmw-ks-buttons show" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_field]'; ?>" value="name" <?php echo ( isset( $gmw_forms[$formID][$section]['keywords_field'] ) && $gmw_forms[$formID][$section]['keywords_field'] == 'name' ) ? " checked=checked " : ""; ?>>	
				<label><?php echo _e( "Search Member's Name",'GMW-PS'); ?></label>
			</p>
		</div>
		<div id="gmw-ks-title" style="display:none;" >
			<p>
				<label for="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_title]'; ?>"><?php echo _e('Field label:','GMW-PS'); ?>
				<input id="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_title]'; ?>" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_title]'; ?>" size="30" type="text" value="<?php echo ( !empty( $gmw_forms[$formID][$section]['keywords_title']) ) ? $gmw_forms[$formID][$section]['keywords_title'] : ''; ?>" />
			</p>	
			<p>
				<input type="checkbox" value="1" name="<?php echo 'gmw_forms['.$_GET['formID'].']['.$section.'][keywords_within]'; ?>" <?php echo ( isset( $gmw_forms[$formID][$section]['keywords_within'] ) ) ? " checked=checked " : ""; ?>>
				<lable><?php echo _e('Label as placeholder','GMW-PS'); ?></lable>
			</p>
		</div>
	<?php 
	}
	
	/**
	 * friends form settings function.
	 *
	 * @access public
	 * @return $settings
	 */
	function fl_form_settings_init( $settings ) {

		$settings['search_form'][1]['radius'] = array(
				'name'       => 'radius',
				'std'        => '',
				'label'      => __( 'Radius / Distance', 'GMW-PS' ),
				'desc'       => __( 'Enter distance values in the input box comma separated if you want to have a select dropdown menu of multiple radius values in the search form. If only one value entered it will be the default value of the search form which will be hidden.', 'GMW-PS' ),
				'type'       => 'function',
				'function'   => 'radius'
		);

		$settings['search_form'][1]['address_field'] = array(

				'name'       => 'address_field',
				'std'        => '',
				'label'      => __( 'Address field', 'GMW-PS' ),
				'cb_label'   => '',
				'desc'       => __( 'Type the title for the address field of the search form. for example "Enter your address". this title wll be displayed either next to the address input field or within if you check the checkbox for it. You can also choose to have the address field mandatory which will prevent users from submitting the form if no address entered. Otherwise if you allow the field to be empty and user submit a form with no address the plugin will display all results.', 'GMW-PS' ),
				'type'       => 'function',
				'function'	 => 'address_fields'
		);
		
		$settings['search_form'][1]['keyword_field'] = array(		
				'name'       => 'keyword_field',
				'std'        => '',
				'label'      => __( 'Keyword field', 'GMW-PS' ),
				'cb_label'   => '',
				'desc'       => __( "Create additional input box to let users search for members by member's name.", 'GMW-PS' ),
				'type'       => 'function',
				'function'	 => 'fl_keyword_search'
		);

		$settings['search_results'][1]['xprofile_fields'] = array(

				'name'       => 'results_xprofile_fields',
				'std'        => '',
				'label'      => __( 'Xprofile fields', 'GMW-PS' ),
				'desc'       => __( 'hoose the profile fields that you want to display in each of the results.', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['search_results'][1]['address_fields'] = array(

				'name'        => 'address_fields',
				'std'         => '',
				'label'       => __( 'Address fields', 'GMW-PS' ),
				'desc'        => __( 'Choose the address fields to display for each member in the results.', 'GMW-PS' ),
				'type'		  => 'multicheckbox',
				'options'	  => array(
						'street' 		=> __( 'Street' , 'GMW-PS' ),
						'apt' 			=> __( 'Apt' , 'GMW-PS' ),
						'city'   		=> __( 'City' , 'GMW-PS' ),
						'state'			=> __( 'State' , 'GMW-PS' ),
						'zipcode'		=> __( 'Zipcode' , 'GMW-PS' ),
						'country'		=> __( 'Country' , 'GMW-PS' ),
				),
		);

		$settings['results_map'][1]['markers_display'] = array(
				'name'        => 'markers_display',
				'std'         => '',
				'label' 	  => __( 'Markers display', 'GMW-PS'),
				'cb_label'    => __( 'Enable', 'GMW-PS'),
				'desc'        => __( 'Use marker Clusterer to group near locations.', 'GMW-PS' ),
				'type'  	  => 'radio',
				'options'  	  => array(
						'none'					=> 'Normal',
						'markers_clusterer' 	=> 'Markers clusterer',
						'markers_spiderfier' 	=> 'Markers Spiderfier'
				),
				'attributes'  => array()
		);

		$settings['results_map'][1]['map_icon_usage'] = array(
				'name'       => 'fl_map_icon_usage',
				'std'        => '',
				'label'      => __( 'Map icons usage', 'GMW-PS' ),
				'desc'       => __( 'Choose the map\'s icons that will be used to display results on the map', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['global_map_icon'] = array(
				'name'       => 'fl_global_map_icon',
				'std'        => '',
				'label'      => __( 'Default map icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the global icon for google map. All results will use this icon to show its location on the map unless you check the "per post icon" checkbox below. The map icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/main-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['your_location_icon'] = array(
				'name'       => 'fl_your_location_icon',
				'std'        => '',
				'label'      => __( 'User location map icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the icon that will show the user location on the map. the "your location" icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/your-location-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['map_controls'] = array(

				'name'        => 'map_controls',
				'std'         => 'true',
				'label'       => __( 'Map controls', 'GMW-PS' ),
				'desc'        => __( 'Which map controls would you like to use', 'GMW-PS' ),
				'type'		  => 'multicheckboxvalues',
				'options'	  => array(
						'zoomControl' 			=> __( 'Zoom' , 'GMW-PS' ),
						'panControl' 			=> __( 'Pan' , 'GMW-PS' ),
						'scaleControl'   		=> __( 'Scale' , 'GMW-PS' ),
						'mapTypeControl'		=> __( 'Map Type' , 'GMW-PS' ),
						'streetViewControl'		=> __( 'Street View' , 'GMW-PS' ),
						'overviewMapControl'	=> __( 'Overview' , 'GMW-PS' ),
						'scrollwheel'			=> __( 'Scroll Wheel' , 'GMW-PS' ),
						'resizeMapControl'  	=> __( 'Resize map trigger' , 'GMW-PS' )
				),
		);
			
		$settings['info_window'] = array(
				__( 'Marker Window', 'GMW-PS' ),
				array(

						'iw_type'       => array(
								'name'        => 'iw_type',
								'std'         => '',
								'label'       => __( 'Marker info-window type', 'GMW-PS'),
								'desc'        => __( 'What type of marker info-window would you like to use? 1) "Infobox" to use the infobox library to generate the info-window within the limits of the map. 2) "Pop-up" to use an HTML pop-up window outside the limit of the map. 3) "Normal" to use the normal info-window generated by GEO m WP.', 'GMW-PS' ),
								'type'        => 'radio',
								'options'     => array(
										'infobox' => 'Infobox',
										'popup'	  => 'Pop-up window',
										'normal'  => 'normal'

								),
								'attributes'  => array()
						),
						'marker_window_theme' => array(

								'name'       => 'bp_members_info_window_theme',
								'std'        => '',
								'label'      => __( 'Marker info-window template', 'GMW-PS' ),
								'cb_label'   => '',
								'desc'       => __( 'Choose the theme for the marker window.', 'GMW-PS'),
								'type'       => 'function'
						),
						'draggable_use'    => array(

								'name'        => 'draggable_use',
								'std'         => '',
								'label' 	  => __( 'Draggable window', 'GMW-PS'),
								'cb_label'    => __( 'Yes', 'GMW-PS'),
								'desc'        => __( "Allow the user to drag the pop-up window across the screen. ( available for pop-up info-window only )", 'GMW-PS' ),
								'type'  	  => 'checkbox',
								'attributes'  => array()
						),
						'avatar'            => array(
								'name'        => 'avatar',
								'std'         => '',
								'label'       => __( 'Avatar', 'GMW-PS' ),
								'desc'        => __( 'Member avatar?', 'GMW-PS' ),
								'type'        => 'checkbox',
								'cb_label'    => __( 'Enable', 'GMW-PS' ),
						),
						'address'           => array(

								'name'        => 'address',
								'std'         => '',
								'label'       => __( 'Address', 'GMW-PS' ),
								'cb_label'    => __( 'Yes', 'GMW-PS' ),
								'desc'        => __( 'Display address', 'GMW-PS' ),
								'type'        => 'checkbox',
								'attributes'  => array()
						),
						'live_directions'    => array(
								'name'        => 'live_directions',
								'std'         => '',
								'label'       => __( 'In Window Directions', 'GMW-PS'),
								'cb_label'    => __( 'Yes', 'GMW-PS'),
								'desc'        => __( 'Display complete directions link within the info-window. ( pop-up infoe window only )', 'GMW-PS' ),
								'type'        => 'checkbox',
								'attributes'  => array()
						),
						'get_directions'    => array(
								'name'        => 'get_directions',
								'std'         => '',
								'label'       => __( 'Get Directions Link', 'GMW-PS'),
								'cb_label'    => __( 'Yes', 'GMW-PS'),
								'desc'        => __( 'Display get directions link that will open a new window with Google map showting the directions to the location.', 'GMW-PS' ),
								'type'        => 'checkbox',
								'attributes'  => array()
						),
						'xprofile_fields' => array(

								'name'       => 'xprofile_fields',
								'std'        => '',
								'label'      => __( 'Xprofile Fields', 'GMW-PS' ),
								'desc'       => __( 'Choose the profile fields that you want to display in each of the results.', 'GMW-PS' ),
								'type'       => 'function',
						)
				),
		);

		$settings['no_results'] = array(
				__( 'No Results', 'GMW-PS' ),
				array(
						'title'         => array(
								'name'        => 'title',
								'std'         => __( 'No members found.', 'GMW-PS' ),
								'label'       => __( 'No results" title', 'GMW-PS' ),
								'desc'        => __( 'The title that will be displayed when no results found.', 'GMW-PS' ),
								'type'		  => 'text',
						),
						'wider_search'  => array(
								'name'       => 'wider_search',
								'std'        => '',
								'label'      => __( 'Wider search link', 'GMW-PS' ),
								'desc'       => __( 'Link that will search within a wider range when no results found.', 'GMW-PS' ),
								'type'       => 'function',
						),
						'all_results'   => array(
								'name'       => 'all_results',
								'std'        => '',
								'label'      => __( 'All Results', 'GMW-PS' ),
								'desc'       => __( 'Create link that will display all results when no results found.', 'GMW-PS' ),
								'type'       => 'function',
						),
				),

		);

		return $settings;
	}

	/**
	 * gmaps form settings function.
	 *
	 * @access public
	 * @return $settings
	 */
	function gmaps_posts_form_settings_init($settings) {

		$settings['results_map'][1]['map_icon_usage'] = array(
				'name'       => 'map_icon_usage',
				'std'        => '',
				'label'      => __( 'Map Icon Usage', 'GMW-PS' ),
				'desc'       => __( 'Choose the map\'s icons that will be used to display results on the map', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['global_map_icon'] = array(
				'name'       => 'global_map_icon',
				'std'        => '',
				'label'      => __( 'Default map icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the global icon for google map. All results will use this icon to show its location on the map unless you check the "per post icon" checkbox below. The map icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/main-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['your_location_icon'] = array(
				'name'       => 'your_location_icon',
				'std'        => '',
				'label'      => __( 'User Location Icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the icon that will show the user location on the map. the "your location" icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/your-location-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		return $settings;
	}

	/**
	 * gmaps form settings function.
	 *
	 * @access public
	 * @return $settings
	 */
	function gmaps_friends_form_settings_init($settings) {

		$settings['results_map'][1]['fl_map_icon_usage'] = array(
				'name'       => 'fl_map_icon_usage',
				'std'        => '',
				'label'      => __( 'Map Icon Usage', 'GMW-PS' ),
				'desc'       => __( 'Choose the map\'s icons that will be used to display results on the map', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['fl_global_map_icon'] = array(
				'name'       => 'fl_global_map_icon',
				'std'        => '',
				'label'      => __( 'Default map icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the global icon for google map. All results will use this icon to show its location on the map unless you check the "per post icon" checkbox below. The map icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/main-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		$settings['results_map'][1]['your_location_icon'] = array(
				'name'       => 'fl_your_location_icon',
				'std'        => '',
				'label'      => __( 'User Location Icon', 'GMW-PS' ),
				'desc'       => __( 'Choose the icon that will show the user location on the map. the "your location" icons can be found in "/wp-content/plugins/geo-my-wp/assets/map-icons/your-location-icons/". you can easily remove or add your own icons to this folder. ', 'GMW-PS' ),
				'type'       => 'function',
		);

		return $settings;
	}
	
}
new GMW_PS_Admin();

?>