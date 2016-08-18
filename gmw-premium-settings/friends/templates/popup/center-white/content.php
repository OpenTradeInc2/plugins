<?php 
/**
 * Members Locator "center-white" pop-up window tempalte file . 
 * 
 * The information on this file will be displayed within the markers pop-up window.
 * 
 * The function pass 2 args for you to use:
 * $gmw  - the form being used ( array )
 * $member - the member being displayed ( object )
 * 
 * You could but It is not recomemnded to edit this file directly as your changes will be overridden on the next update of the plugin.
 * Instead you can copy-paste this template ( the "center-white" folder contains this file and the "css" folder ) 
 * into the theme's or child theme's folder of your site and apply your changes from there. 
 * 
 * The template folder will need to be placed under:
 * your-theme's-or-child-theme's-folder/geo-my-wp/friends/info-window-templates/popup/
 * 
 * Once the template folder is in the theme's folder you will be able to choose it when editing the Members Locator form.
 * It will show in the pop-up tempaltes dropdown menu as "Custom: center-white".
 */
?>
<?php do_action( 'gmw_iw_template_before_top_buttons', $member, $gmw ); ?>

<!-- top buttons -->
<div class="top-buttons-wrapper">		

	<?php gmw_window_toggle( $gmw, $member, false, 'gmw-iw-template-holder', 'dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2', 'height', '30px', '100%' ); ?>
	
	<?php if ( isset( $gmw['info_window']['draggable_use'] ) ) { ?>
		<?php echo gmw_get_draggable_handle( $gmw, $member, 'gmw-iw-template-holder', true, 'dashicons-editor-justify', 'window' ); ?>		
	<?php } ?>
	
	<?php echo gmw_get_close_button( $member, $gmw, 'dashicons-no', 'iw' ) ?>	
</div>

<?php do_action( 'gmw_iw_before_template', $member, $gmw ); ?>

<div class="template-content-wrapper">
	
	<?php do_action( 'gmw_iw_template_start', $member, $gmw ); ?>
	
	<!-- avatar -->	
	<?php if ( isset( $gmw['info_window']['avatar'] ) ) { ?>  	
    	<?php do_action( 'gmw_ib_template_before_image', $member, $gmw ); ?>
	
		<div class="user-avatar">
			<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar('type=full'); ?></a>
		</div>
	<?php } ?>	
	
	<?php do_action( 'gmw_iw_template_before_title', $member, $gmw ); ?>
	
	<!-- title -->
	<h3 class="title">
		<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
		<span class="distance"><?php  gmw_distance_to_location( $member, $gmw ); ?></span>
	</h3>
	<span class="last-active"><?php bp_member_last_active(); ?></span>
		
	<!-- address -->						
	<?php if ( isset( $gmw['info_window']['address'] ) ) { ?>
	
		<?php do_action( 'gmw_iw_template_before_address', $member, $gmw ); ?>
	    
	    <div class="address-wrapper">
	    	<span class="dashicons-before dashicons-location address-icon"></span>
	    	<span class="address"><?php echo gmw_location_address( $member, $gmw ); ?></span>
	    </div>    
    <?php } ?>
     	        	
	<!-- Member info -->
	<?php if ( !empty( $gmw['info_window']['profile_fields'] ) || !empty( $gmw['info_window']['profile_fields_date'] ) ) { ?>
	
		<?php do_action( 'gmw_ib_template_before_member_info', $member, $gmw ); ?>
	
	    <div class="member-info">
	    	<h3><?php _e( 'Member information', 'GMW-PS' ); ?></h3>
	    	<?php gmw_iw_xprofile_fields( $gmw ); ?> 
	    </div>
    
    <?php } ?>
   
     <!--  get directions -->   
    <?php if ( isset( $gmw['info_window']['live_directions'] ) ) { ?>   
    
	    <?php do_action( 'gmw_iw_template_before_live_directions', $member, $gmw ); ?>
	    	    
	   	<?php gmw_live_directions( $member, $gmw , '' ) ?>
	   	 
	   	<?php do_action( 'gmw_iw_template_before_directions_panel', $member, $gmw ); ?>
	   	 
	    <?php gmw_live_directions_panel( $member, $gmw ); ?>
	
	<?php } ?>
    
     <!--  get directions link -->   
    <?php if ( isset( $gmw['info_window']['get_directions'] ) ) { ?>    
    	<?php echo gmw_get_directions_link(  $member, $gmw, $gmw['labels']['search_results']['google_map_directions'] ); ?>
	<?php } ?>
	         
    <?php do_action( 'gmw_iw_template_end', $member, $gmw ); ?>
     	    
</div>

<?php do_action( 'gmw_iw_after_template', $member, $gmw ); ?>