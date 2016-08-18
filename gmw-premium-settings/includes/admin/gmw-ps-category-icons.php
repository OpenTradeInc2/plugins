<?php

if ( ! defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly

/**
 * GMW_PT_Admin class
 */
class GMW_PS_Category_icons {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
				
		$this->settings  		  = get_option( 'gmw_options' );
		$this->category_icons 	  = get_option( 'gmw_category_icons' );
		$this->category_map_icons = get_option( 'gmw_category_map_icons' );
		$this->tax_array 		  = array();	

		//check if feature enabled and taxonomies exist
		if ( empty( $this->settings['post_types_settings']['per_category_icons']['enabled'] ) || empty( $this->settings['post_types_settings']['per_category_icons']['taxonomies'] ) )
			return;

		if ( !is_array( $this->settings['post_types_settings']['per_category_icons']['taxonomies'] ) )
			return;

		foreach ( $this->settings['post_types_settings']['per_category_icons']['taxonomies'] as $tax ) {

			add_action( 'edited_'.$tax,	array( $this, 'update_category_icons' ) );
			add_action( 'created_'.$tax, array( $this, 'update_category_icons' ) );
			add_action( 'delete_term', array( $this, 'delete_category_icons'  ) );	
		
			add_filter( 'manage_edit-'.$tax.'_columns',  array( $this, 'table_column_name' ) );
			add_filter( 'manage_'.$tax.'_custom_column', array( $this, 'table_column_content' ), 10, 3 );

			add_filter( $tax.'_edit_form_fields', array( $this, 'display_category_icons' ) );
			add_filter( $tax.'_add_form_fields', array( $this, 'display_category_icons'  ) );			 
		}
	}

	/**
	 * Update category icons
	 * @param  int $term_id term ID
	 * @return void
	 */
	function update_category_icons( $term_id ) {

		/*
		 * update category icons
		*/
		$this->category_icons[$term_id] = ( !empty( $_POST['gmw_category_icon'] ) ) ? strip_tags( $_POST['gmw_category_icon'] ) : '';
		update_option( 'gmw_category_icons', $this->category_icons);
	
		/*
		 * update map icons
		*/
		$this->category_map_icons[$term_id] = ( !empty( $_POST['gmw_category_map_icon'] ) ) ? strip_tags( $_POST['gmw_category_map_icon'] ) : '';
		update_option( 'gmw_category_map_icons', $this->category_map_icons );
	}
	
	/**
	 * Delete category icons
	 * @param  int $term_id term ID
	 * @return void 
	 * 
	 */
	function delete_category_icons( $term_id ) {

		/*
		 * Delete category icon
		*/
		unset( $this->category_icons[$term_id] );
		update_option( 'gmw_category_icons', $this->category_icons );
		 
		/*
		 * Delete map icon
		*/
		unset( $this->category_map_icons[$term_id] );
		update_option( 'gmw_category_map_icons', $this->category_map_icons );		
	}

	/**
	 * Table column label
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	function table_column_name( $columns ){
		
		$columns['gmw_category_icon'] = __( 'Category Icon','GMW-PS' );

		//hide map icons column if using same as category icons
		if ( !isset( $this->settings['post_types_settings']['per_category_icons']['same_icons'] ) ) {
			$columns['gmw_category_map_icon'] = __( 'Map Icon','GMW-PS' );
		}
	
		return $columns;
	}
	
	/**
	 * Add content to category table in add/new category page
	 * @param  [type] $content     [description]
	 * @param  [type] $column_name [description]
	 * @param  [type] $term_id     [description]
	 * @return [type]              [description]
	 */
	function table_column_content( $content, $column_name, $term_id ) {

		//display category icon in term row
		if ( $column_name == 'gmw_category_icon' ) {
			if ( !empty( $this->category_icons[$term_id] ) ) {
				$content .=  '<img src="'.esc_url( $this->settings['pt_category_icons']['url'] ). esc_attr( $this->category_icons[$term_id] ).'" />';
			} else {
				$content .= __( 'N/A','GMW-PS' 	);
			}
		}
		
		//hide map icons column if using same as category icons
		if ( !isset( $this->settings['post_types_settings']['per_category_icons']['same_icons'] ) ) {
			if ( $column_name == 'gmw_category_map_icon' ) {	
				
				if ( !empty( $this->category_map_icons[$term_id] ) ) {			
					$content .= '<img src="'.esc_url( $this->settings['pt_map_icons']['url'] ). esc_attr( $this->category_map_icons[$term_id] ).'" />';	
				} else {
					$content .= __( 'N/A','GMW-PS' );
				}	
			}
		}
		return $content;
	}
		
	/**
	 * Display category icons to choose from in category page
	 * @param  [type] $tag [description]
	 * @return [type]      [description]
	 */
	function display_category_icons( $tag ) {
		$category_icons = $this->settings['pt_category_icons']['all_icons'];
		$map_icons 		= $this->settings['pt_map_icons']['all_icons'];	

		?>
		<table class="form-table" style="display: inline-block;margin-bottom: 20px">
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="gmw-category-icon"><?php _e('Category Icon','GMW_PS'); ?></label>
				</th>
			</tr>
			<tr>
				<td>
				<?php 	
				$cic = 1;
				foreach ( $category_icons as $category_icon ) {
					$checked = '';
					
					if ( $cic == 1 || ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && !empty( $this->category_icons[$tag->term_id] ) && $this->category_icons[$tag->term_id] == $category_icon ) ) {
						$checked =  'checked="checked"';
					}

					echo '<span><input type="radio" name="gmw_category_icon" value="'.esc_attr( $category_icon ).'" '.$checked.'/>';
					echo '<img src="'.esc_url( $this->settings['pt_category_icons']['url'].$category_icon ).'" style="max-width:28px;height:auto" /></span>';
					$cic++;
				}					
				?>
			</td>
			</tr>

			
			<?php 
			//hide map icons column if using same as category icons
			if ( !isset( $this->settings['post_types_settings']['per_category_icons']['same_icons'] ) ) { ?>
				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="gmw-category-icon">
							<?php _e('Maps Icon:','GMW_PS'); ?>
						</label>
					</th>
				</tr>
				<tr>
					<td>
					<?php
					$cic = 1;	
					foreach ( $map_icons as $map_icon ) {
						$checked = '';

						if ( $cic == 1 || ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && !empty( $this->category_map_icons[$tag->term_id] ) && $this->category_map_icons[$tag->term_id] == $map_icon ) ) {
							$checked =  'checked="checked"';
						}

						echo '<span><input type="radio" id="gmw-category-icon" name="gmw_category_map_icon" value="'.esc_attr( $map_icon ).'" '.$checked.'/>';
						echo '<img src="'.esc_url( $this->settings['pt_map_icons']['url'].$map_icon ).'" style="max-width:28px;height:auto" /></span>';
						$cic++;
					}		
					?>
					</td>
				</tr>

			<?php } ?>

		</table>
		<?php
	}
}

function gmw_category_page_init() {
	new GMW_PS_Category_icons();
}
add_action( 'admin_init', 'gmw_category_page_init' );
?>