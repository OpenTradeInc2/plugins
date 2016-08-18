<?php 
/**
 * Posts Locator "default" pop-up window tempalte file . 
 * 
 * The information on this file will be displayed within the markers pop-up window.
 * 
 * The function pass 2 args for you to use:
 * $gmw  - the form being used ( array )
 * $post - the post being displayed ( object )
 * 
 * You could but It is not recomemnded to edit this file directly as your changes will be overridden on the next update of the plugin.
 * Instead you can copy-paste this template ( the "default" folder contains this file and the "css" folder ) 
 * into the theme's or child theme's folder of your site and apply your changes from there. 
 * 
 * The template folder will need to be placed under:
 * your-theme's-or-child-theme's-folder/geo-my-wp/posts/info-window-templates/popup/
 * 
 * Once the template folder is in the theme's folder you will be able to choose it when editing the Members Locator form.
 * It will show in the pop-up tempaltes dropdown menu as "Custom: default".
 */
?>
<?php do_action( 'gmw_pt_template_before_top_buttons', $post, $gmw ); ?>

<div class="gmw-pt-iw-template-inner">
	
	<div class="gmw-pt-iw-close-button">x</div>
	
	<?php do_action( 'gmw_pt_template_start', $post, $gmw ); ?>
	
	<div class="gmw-pt-iw-title">
		<a href="<?php echo get_permalink( $post->ID ); ?>" ><?php echo $post->post_title; ?></a>
		<span class="gmw-pt-iw-distance-wrapper"><?php gmw_distance_to_location( $post, $gmw ); ?></span>
	</div>
	
	<div class="gmw-pt-iw-featured-image-wrapper">
		<?php echo get_the_post_thumbnail( $post->ID, 'thumb' ); ?>
	</div>
		
	<!--  excerpt -->
    <?php if ( isset( $gmw['info_window']['excerpt']['use'] ) ) { ?>
    
    	<?php do_action( 'gmw_pt_template_before_excerpt', $post, $gmw ); ?>
    	
	    <div class="gmw-pt-iw-excerpt-wrapper">
	    	<?php gmw_excerpt( $post, $gmw, $post->post_content, $gmw['info_window']['excerpt']['count'], $gmw['info_window']['excerpt']['more'] ); ?>
	    </div>
    <?php } ?>
    	
	<!-- contact information -->
    <?php if ( !empty( $gmw['info_window']['additional_info'] ) ) { ?>
    
    	<?php do_action( 'gmw_pt_template_before_contact_info', $post, $gmw ); ?>
	   	
	   	<div class="contact-info">
			<h3><?php echo $gmw['labels']['info_window']['contact_info']; ?></h3>
    		<?php gmw_additional_info( $post, $gmw, $gmw['info_window']['additional_info'], $gmw['labels']['info_window'], 'ul' ); ?>  
    	</div>
    <?php } ?>
        
   	 <!--  get directions link -->   
    <?php if ( isset( $gmw['info_window']['get_directions'] ) ) { ?>    
    	<?php echo gmw_get_directions_link(  $post, $gmw, false ); ?>
	<?php } ?>
 
	<?php do_action( 'gmw_pt_template_end', $post, $gmw ); ?>
     	    
</div>  
 		 
<?php do_action( 'gmw_pt_after_template', $post, $gmw ); ?>   
	
 		    
