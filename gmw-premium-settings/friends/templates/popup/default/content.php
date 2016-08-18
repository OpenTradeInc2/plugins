<?php 
/**
 * Members Locator "default" pop-up window tempalte file . 
 * 
 * The information on this file will be displayed within the markers pop-up window.
 * 
 * The function pass 2 args for you to use:
 * $gmw  - the form being used ( array )
 * $member - the member being displayed ( object )
 * 
 * You could but It is not recomemnded to edit this file directly as your changes will be overridden on the next update of the plugin.
 * Instead you can copy-paste this template ( the "default" folder contains this file and the "css" folder ) 
 * into the theme's or child theme's folder of your site and apply your changes from there. 
 * 
 * The template folder will need to be placed under:
 * your-theme's-or-child-theme's-folder/geo-my-wp/friends/info-window-templates/popup/
 * 
 * Once the template folder is in the theme's folder you will be able to choose it when editing the Members Locator form.
 * It will show in the pop-up tempaltes dropdown menu as "Custom: default".
 */
?>
<?php do_action( 'gmw_iw_before_template', $member, $gmw ); ?>
<div class="gmw-fl-iw-template-inner">
	
	<div class="gmw-fl-iw-close-button">x</div>
	
	<?php do_action( 'gmw_iw_template_start', $member, $gmw ); ?>
	
	<div class="gmw-fl-iw-title">
		<a href="<?php echo bp_core_get_user_domain( $member->ID ); ?>" ><?php echo bp_core_get_user_displayname( $member->ID ); ?></a>
		<span class="gmw-fl-iw-distance-wrapper"><?php  gmw_distance_to_location( $member, $gmw ); ?></span>
	</div>

        <?php if ( isset( $gmw['info_window']['avatar'] ) ) { ?>
        
        	<?php do_action( 'gmw_fl_ib_template_before_image', $member, $gmw ); ?>
        	
            <div class="gmw-fl-iw-avatar-wrapper">
                    <a href="<?php echo bp_core_get_user_domain( $member->ID ); ?>" ><?php echo bp_core_fetch_avatar ( array( 'item_id' => $member->ID, 'type' => 'full' ) ); ?></a>
            </div>
        <?php } ?>
        
        <?php if ( isset( $gmw['info_window']['address'] ) ) { ?>
        
        	<?php do_action( 'gmw_iw_template_before_address', $member, $gmw ); ?>
        	
            <div class="gmw-fl-iw-address-wrapper">
                    <span><?php _e( 'Address: ', 'GMW-PS' ); ?></span><?php echo $member->formatted_address;  ?>
            </div>
        <?php } ?>
        
   <?php echo gmw_get_directions_link(  $member, $gmw, false ); ?>
 
</div>  
 		    
