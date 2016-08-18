<?php 
/**
 * Posts Locator "center-white" pop-up window tempalte file . 
 * 
 * The information on this file will be displayed within the markers pop-up window.
 * 
 * The function pass 2 args for you to use:
 * $gmw  - the form being used ( array )
 * $post - the post being displayed ( object )
 * 
 * You could but It is not recomemnded to edit this file directly as your changes will be overridden on the next update of the plugin.
 * Instead you can copy-paste this template ( the "center-white" folder contains this file and the "css" folder ) 
 * into the theme's or child theme's folder of your site and apply your changes from there. 
 * 
 * The template folder will need to be placed under:
 * your-theme's-or-child-theme's-folder/geo-my-wp/posts/info-window-templates/popup/
 * 
 * Once the template folder is in the theme's folder you will be able to choose it when editing the Members Locator form.
 * It will show in the pop-up tempaltes dropdown menu as "Custom: center-white".
 */
?>
<?php do_action( 'gmw_iw_template_before_top_buttons', $post, $gmw ); ?>

<!-- top buttons -->
<div class="top-buttons-wrapper">		

	<?php gmw_window_toggle( $gmw, $post, false, 'gmw-iw-template-holder', 'dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2', 'height', '30px', '100%' ); ?>
	
	<?php if ( isset( $gmw['info_window']['draggable_use'] ) ) { ?>
		<?php echo gmw_get_draggable_handle( $gmw, $post, 'gmw-iw-template-holder', true, 'dashicons-editor-justify', 'window' ); ?>		
	<?php } ?>
	
	<?php echo gmw_get_close_button( $post, $gmw, 'dashicons-no', 'iw' ) ?>	
</div>

<?php do_action( 'gmw_iw_before_template', $post, $gmw ); ?>

<div class="template-content-wrapper">
	
	<?php do_action( 'gmw_iw_template_start', $post, $gmw ); ?>
	
	<!-- featured image -->	
	<?php if ( isset( $gmw['info_window']['featured_image'] ) && has_post_thumbnail( $post->ID ) ) { ?>  	
    	
    	<?php do_action( 'gmw_iw_template_before_image', $post, $gmw ); ?>
	
		<div class="featured-image">
			<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
		</div>
		
	<?php } ?>		
	
	<?php do_action( 'gmw_iw_template_before_title', $post, $gmw ); ?>
	
	<h3 class="title">
		<a href="<?php echo get_permalink( $post->ID ); ?>" ><?php echo $post->post_title; ?></a>
		<span class="distance"><?php gmw_distance_to_location( $post, $gmw ); ?></span>
	</h3>
	
	<!-- address -->						
	<?php if ( !empty( $gmw['info_window']['additional_info']['formatted_address'] ) ) { ?>
	
		<?php do_action( 'gmw_iw_template_before_address', $post, $gmw ); ?>
	    
	    <div class="address-wrapper">
	    	<span class="dashicons-before dashicons-location address-icon"></span>
	    	<span class="address"><?php gmw_location_address( $post, $gmw ); ?></span>
	    </div>
    <?php } ?>
     	
    <!--  excerpt -->
    <?php if ( isset( $gmw['info_window']['excerpt']['use'] ) ) { ?>
    	
    	<?php do_action( 'gmw_iw_template_before_excerpt', $post, $gmw ); ?>
		
		<h3><?php _e( 'Information', 'GWW-PS' ); ?></h3>
		<p class="excerpt">
    		<?php gmw_excerpt( $post, $gmw, $post->post_content, $gmw['info_window']['excerpt']['count'], $gmw['info_window']['excerpt']['more'] ); ?>
		</p>
		
    <?php } ?>
    	
    <!-- contact information -->
    <?php if ( !empty( $gmw['info_window']['additional_info'] ) ) { ?>
    
    	<?php do_action( 'gmw_iw_template_before_contact_info', $post, $gmw ); ?>
	   	
	   	<div class="contact-info">
			<h3><?php echo $gmw['labels']['info_window']['contact_info']; ?></h3>
    		<?php gmw_additional_info( $post, $gmw, $gmw['info_window']['additional_info'], $gmw['labels']['info_window'], 'ul' ); ?> 
    	</div>
    <?php } ?>
   
     <!--  get directions -->   
    <?php if ( isset( $gmw['info_window']['live_directions'] ) ) { ?>   
    
	    <?php do_action( 'gmw_iw_template_before_live_directions', $post, $gmw ); ?>
	    	    
	   	<?php gmw_live_directions( $post, $gmw , '' ) ?>
	   	 
	   	<?php do_action( 'gmw_iw_template_before_directions_panel', $post, $gmw ); ?>
	   	 
	    <?php gmw_live_directions_panel( $post, $gmw ); ?>
	
	<?php } ?>
    
     <!--  get directions link -->   
    <?php if ( isset( $gmw['info_window']['get_directions'] ) ) { ?>    
    	<?php echo gmw_get_directions_link(  $post, $gmw, $gmw['labels']['search_results']['google_map_directions'] ); ?>
	<?php } ?>
	         
    <?php do_action( 'gmw_iw_template_end', $post, $gmw ); ?>
     	    
</div>

<?php do_action( 'gmw_iw_after_template', $post, $gmw ); ?>