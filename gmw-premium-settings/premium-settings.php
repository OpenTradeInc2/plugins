<?php
/*
Plugin Name: GMW Add-on - Premium Settings
Plugin URI: http://www.geomywp.com
Description: Add additional settings to search form shortcodes and results.
Version: 1.6.3
Author URI: http://www.geomywp.com
Requires at least: 4.0
Tested up to: 4.3
GEO my WP: 2.6.1+
Text Domain: GMW-PS
Domain Path: /languages/
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * GMW_Premium_Settings class.
 */
class GMW_Premium_Settings {
 
    /**
     * __construct function.
     */
    public function __construct() { 	
        	
        //load Plugin Text domain
        add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
        
    	//the title of the download in geomywp.com
        define( 'GMW_PS_ITEM_NAME', 'Premium Settings' );
        define( 'GMW_PS_TITLE', __( 'Premium Settings', 'GMW-PS' ) );
        define( 'GMW_PS_LICENSE_NAME', 'premium_settings' );
        define( 'GMW_PS_VERSION', '1.6.3' );
        define( 'GMW_PS_FILE', __FILE__ );
        define( 'GMW_PS_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( GMW_PS_FILE ) ), basename( GMW_PS_FILE ) ) ) );
        define( 'GMW_PS_PATH', untrailingslashit( plugin_dir_path( GMW_PS_FILE ) ) );
    	
        // init add-on
        add_filter( 'gmw_admin_addons_page', array( $this, 'addon_init' ), 14 );
        
        //make sure GEO my WP is activated and compare version, otherwise abort.
        if ( !class_exists( 'GEO_my_WP' ) || version_compare( GMW_VERSION, '2.6.1', '<' ) ) {
            add_action( 'admin_notices', array( $this, 'admin_notice' ) );      
            return;
        }

        //check if addon is activeted via GEO my WP
        if ( !GEO_my_WP::gmw_check_addon( 'premium_settings' ) ) {
            return;
        }

    	//include files
    	include_once( 'includes/gmw-ps-template-functions.php' );
    	
    	//include global maps functions if add-on exists
    	if ( GEO_my_WP::gmw_check_addon( 'global_maps' ) == true ) {
    		include_once( 'includes/gmw-ps-gmaps-functions.php' );
    	}
    	
    	if ( is_admin() ) {
    		include( 'includes/admin/gmw-ps-admin.php' );
    	}
    	
        //registter scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
    	
    	//include friends and posts classes
    	add_action( 'gmw_pt_shortcode_start', array( $this, 'include_posts_locator_functions' ), 10 );
    	add_action( 'gmw_fl_shortcode_start', array( $this, 'include_friends_locator_functions'   ) );
    	
    	//info window ajax
    	add_action( 'wp_ajax_gmw_ps_display_info_window', 		 array( $this, 'info_window_ajax' ) );
    	add_action( 'wp_ajax_nopriv_gmw_ps_display_info_window', array( $this, 'info_window_ajax' ) );
    }
    
    /**
     * Load plugin textdomain
     * @since 1.6
     */
    public function textdomain() {
        load_plugin_textdomain( 'GMW-PS', FALSE, dirname(plugin_basename(__FILE__)).'/languages/' );
    }
    
    /**
     * Load plugin textdomain
     * @since 1.0
     */
    public function admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Premium Settings add-on version 1.6 requires GEO my WP plugin version 2.6.1 or higher.', 'GMW-PS' ); ?></p>
        </div>  
        <?php
    }

    /**
     * Initiate add-on
     *
     * @access public
     * @return $addons
     */
    public function addon_init( $addons ) {
    	
    	$addons[GMW_PS_LICENSE_NAME] = array(
    			'name'    	   => GMW_PS_LICENSE_NAME,
                'item'         => GMW_PS_ITEM_NAME,
                'item_id'      => null,
    			'title'   	   => GMW_PS_TITLE,
    			'version' 	   => GMW_PS_VERSION,		
    			'file' 	  	   => GMW_PS_FILE,
                'basename'     => plugin_basename( GMW_PS_FILE ),
    			'author'  	   => 'Eyal Fitoussi',
    			'desc'    	   => __( "Boost GEO my WP with additional advanced features provided by the add-on.", "GMW-PS" ),
    			'image'   	   => false,
    			'require' 	   => array(),
    			'license' 	   => true,
                'auto_trigger' => true,
                'min_version'  => false,
                'stand_alone'  => false,
                'core'         => false,
                'gmw_version'  => '2.5'
    	);
    	return $addons;
    }

    /**
     * Register scripts
     */
    public function register_scripts() {
    	    	
    	//register get directions script
        if ( !wp_script_is( 'gmw-get-directions', 'registered' ) ) {
      		wp_register_script( 'gmw-get-directions', GMW_PS_URL . '/assets/js/get-directions.min.js', array( 'jquery' ), GMW_PS_VERSION, true );
      	}

        //deregister map.js of gmw core
        wp_deregister_script( 'gmw-map' );
     
        //register map of premium settings
        wp_register_script( 'gmw-map', GMW_PS_URL . '/assets/js/map.min.js', array( 'jquery', 'google-maps', 'gmw-js' ), GMW_PS_VERSION, true );
    }
    
    /**
     * Include posts classes
     * @param type $gmw
     */
    public function include_posts_locator_functions( $gmw ) {	
        include_once( 'posts/includes/gmw-ps-pt-template-functions.php' );
    }

    /**
     * Include friends add-on classes
     * @param type $gmw
     */
    public function include_friends_locator_functions( $gmw ) {
        include_once( 'friends/includes/gmw-ps-fl-template-functions.php' );
    }

    /**
     * Info window ajax function
     */
    function info_window_ajax() {

        $location = $_POST['location_info'];
        $gmw      = $_POST['form'];

        $location = apply_filters( 'gmw_ps_location_before_info_window', $location, $gmw );

        if ( $gmw['addon'] == 'posts' ) {
            include_once( 'posts/includes/gmw-ps-pt-template-functions.php' );
            include_once( 'posts/includes/gmw-ps-pt-info-window-functions.php' );
        } elseif ( $gmw['addon'] == 'friends' ) {
            include_once( 'friends/includes/gmw-ps-fl-template-functions.php' );
            include_once( 'friends/includes/gmw-ps-fl-info-window-functions.php' );
        }
		
        //hook your info window queries here
        do_action( 'gmw_ps_'.$gmw['prefix'].'_info_window_display', $gmw, $location );
        do_action( 'gmw_ps_info_window_display'                   , $gmw, $location );

        die();
    }
}

/**
 *  Premium Settings Instance
 *
 * @since 1.3
 * @return GMW_Premium_Settings Instance
 */
new GMW_Premium_Settings();
?>