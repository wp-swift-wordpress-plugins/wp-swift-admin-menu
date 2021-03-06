<?php
/*
Plugin Name: 		WP Swift: WordPress Toolbox
Plugin URI:         https://github.com/GarySwift/wp-swift-admin-menu
Description: 		Creates a top level menu item in the admin sidebar along submenu pages for Google Maps, Social Media, Opening Hours, Contact Form etc.
Version:           	1.0.1
Author:            	Gary Swift
Author URI:        	https://github.com/GarySwift
License:            GPL-2.0+
License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain:       	wp-swift-admin-menu
*/
class WP_Swift_Admin_Menu {
	private $menu_slug = 'wp-swift-admin-menu';
	private $page_title = 'Settings';
	private $menu_title = 'WP Swift';//'BrightLight';
	private $capability = 'manage_options';
	
    /*
     * Initializes the plugin.
     */
    public function __construct() {
			
    	
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_javascript') );

		add_action('admin_footer', array($this, 'enqueue_admin_javascript'));

		add_action( 'wp_enqueue_scripts', array($this, 'wp_swift_admin_menu_css_file') );
		add_action( 'admin_enqueue_scripts', array($this, 'wp_swift_admin_menu_css_file_admin_style' ));//wp-swift-admin-menu-css-file-admin-style

    	add_action( 'admin_menu', array($this, 'wp_swift_admin_menu_add_admin_menu') );
    	// register_deactivation_hook( __FILE__, 'wp_swift_admin_menu_plugin_deactivate' );
		add_action( 'admin_init', array($this, 'wp_swift_admin_menu_settings_init') );



    	add_action( 'admin_notices', array($this, 'admin_notice_install_acf') );

    	# Register ACF field groups that will appear on the options pages
		// add_action( 'init', array($this, 'acf_add_local_field_group_google_map') );
		add_action( 'init', array($this, 'acf_add_local_field_group_social_media') );
		add_action( 'init', array($this, 'acf_add_local_field_group_contact_details') );







		# Allow admin remove the "Add Media" button above the WYSIWYG editor for non admins
		add_action( 'admin_head', array($this, 'wp_swift_admin_menu_maybe_remove_add_media_button') );

		# Allow admins to extend the WYSIWYG
		add_action( 'init', array($this, 'wp_swift_admin_menu_extend_wysiwyg') );

				# Allow admins to extend the WYSIWYG
		add_action( 'init', array($this, 'wp_swift_admin_menu_acf_additional_fields') );

						# Allow admins to extend the WYSIWYG
		add_action( 'init', array($this, 'wp_swift_admin_menu_featured_image') );
    }

    /*
     * register_activation_hook
     */
    static function wp_swift_admin_menu_plugin_install() {
    	$menu_slug = 'wp-swift-admin-menu';
        // do not generate any output here
     	if ( get_option( 'wp_swift_admin_menu' ) === false ) {
			// $new_options['page_title'] = $this->page_title;
			// $new_options['menu_title'] = $this->menu_title;
			$new_options['menu_slug'] = $menu_slug;
			add_option( 'wp_swift_admin_menu', $menu_slug );
		} 
		// else {
	 //    	$existing_options = get_option( 'wp_swift_admin_menu' );
	 //    	if ( $existing_options['version'] < 1.1 ) {
		// 		$existing_options['track_outgoing_links'] = false;
		// 		$existing_options['version'] = "1.1";
		// 		update_option( 'wp_swift_admin_menu', $existing_options );
		// 	} 
		// }
    }

    static function wp_swift_admin_menu_plugin_deactivate() {
		// Check if options exist and delete them if present
		if ( get_option( 'wp_swift_admin_menu' ) ) {
			delete_option( 'wp_swift_admin_menu' );
		}
		// if ( get_option( 'wp_swift_admin_menu_settings' ) ) {
		// 	delete_option( 'wp_swift_admin_menu_settings' );
		// }
	}



// Displays a notice if the Advanced Custom Fields plugin is not active.
public function admin_notice_install_acf() {
    if ( isset($_GET["page"]) && $_GET["page"] == "wp-swift-admin-menu"  ) : ?>
	    <?php if (!function_exists( 'acf' )): ?>
	    <div class="error notice">
	        <p><?php _e( 'Please install <b>Advanced Custom Fields Pro</b>. It is required for this plugin to work properly! | <a href="http://www.advancedcustomfields.com/pro/" target="_blank">ACF Pro</a>', 'wp-swift-admin-menu' ); ?></p>
	        <small><i><?php _e( 'Option pages will not show until this is installed', 'wp-swift-admin-menu' ); ?></i></small>
	    </div>
	    <?php endif;    	
   	endif;

}
    /*
     * Add the css file
     */
    public function wp_swift_admin_menu_css_file() {
        // $options = get_option( 'wp_swift_admin_menu_settings' );

        // if (isset($options['wp_swift_admin_menu_checkbox_css'])==false) {
            wp_enqueue_style('wp-swift-admin-menu-style', plugins_url( 'assets/css/wp-swift-admin-menu.css', __FILE__ ) );
        // }

    }

	public function wp_swift_admin_menu_css_file_admin_style() {
	        // wp_register_style( 'custom_wp_admin_css', get_template_directory_uri() . '/admin-style.css', false, '1.0.0' );
	        // wp_enqueue_style( 'custom_wp_admin_css' );
		wp_enqueue_style('wp-swift-admin-menu-style', plugins_url( 'assets/css/wp-swift-admin-menu-css-file-admin-style.css', __FILE__ ) );
	}
    /*
     * Add the JavaScript file
     */
    public function enqueue_javascript () {
        // $options = get_option( 'wp_swift_admin_menu_settings' );
        
        // if (isset($options['wp_swift_admin_menu_checkbox_javascript'])==false) {
           wp_enqueue_script( $handle='wp-swift-admin-menu', $src=plugins_url( '/assets/js/wp-swift-admin-menu.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
        // }
    }
    /*
     * Add the JavaScript file
     */
    public function enqueue_admin_javascript () {
        // wp_enqueue_script( $handle='wp-swift-admin-menu', $src=plugins_url( '/assets/js/wp-swift-admin-menu-backend.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
        // wp_enqueue_script( $handle='wp-swift-syntaxhighlighter', $src=plugins_url( '/libraries/brush-php/brush.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
        wp_enqueue_script( $handle='wp-swift-syntax-prettify', 'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js', $deps=null, $ver=null, $in_footer=true );
    }


    public function wp_swift_admin_menu_maybe_remove_add_media_button() {
		if ( !current_user_can( 'manage_options' ) ) {
		    $options = get_option( 'wp_swift_utilities_settings' );
		    $option = 'remove_add_media_button';
			if (isset($options[$option]) && $options[$option]) {
				remove_action( 'media_buttons', 'media_buttons' );
			}  
	    }
    }

    public function wp_swift_admin_menu_extend_wysiwyg() {
	    $options = get_option( 'wp_swift_utilities_settings' );
	    $option = 'extend_wysiwyg';
		if (isset($options[$option]) && $options[$option]) {
			include "_tiny-mce.php";
		}  
    }

    public function wp_swift_admin_menu_featured_image() {
	    $options = get_option( 'wp_swift_utilities_settings' );
	    $option = 'featured_image';
		if (isset($options[$option]) && $options[$option]) {
			include "utilities/_featured-image.php";
		}  
    }

    public function wp_swift_admin_menu_acf_additional_fields() {
	    $options = get_option( 'wp_swift_utilities_settings' );
	    $option = 'acf_additional_fields';
		if (isset($options[$option]) && $options[$option]) {
			require_once 'acf-additional-fields-flex-content/_acf-additional-fields-flex-content.php';
			require_once 'acf-additional-fields-flex-content/_the_acf_content.php';
		} 
	    $option = 'acf_additional_fields_style_and_script';
		if (isset($options[$option]) && $options[$option]) {
			/*
			 * Add the css file
			 */
			wp_enqueue_style('acf-additional-fields-css', plugins_url( 'acf-additional-fields-flex-content/assets/css/_youtube-embed-thumbnail.css', __FILE__ ) );
			/*
			 * Add the js file
			 */
			wp_enqueue_script( $handle='acf-additional-fields-js', $src=plugins_url( 'acf-additional-fields-flex-content/assets/js/_youtube-embed-thumbnail.js', __FILE__ ), $deps=null, $ver=null, $in_footer=true );
		} 		 
    }



    /**
     * A helper fuction that tests if an option is set
     *
     * @param  string  $option     	The text content for shortcode. Not used.
     *
     * @return boolean				If the option is set   
     */
	private function show_sidebar_option($option) {
		$options = get_option( 'wp_swift_admin_menu_settings' );
		if (isset($options[$option]) && $options[$option]) {
			return true;
		}
		return false;
	}



	/*
	 * The ACF field group for 'Contact Details'
	 */	
	public function acf_add_local_field_group_contact_details() {
		include "acf-field-groups/_acf-field-group-contact-page.php";
	}

	/*
	 * The ACF field group for 'Social Media'
	 */	
	public function acf_add_local_field_group_social_media() {
		include "acf-field-groups/_acf-field-group-social-media.php";
	}	

    /*
     * 
     * Create the menu pages that show in the side bar.
     *
     * The top level page is uses the standard WordPress API for showing menus.
     * The submenus use Advanced Custom Fields API to register pages
     */
	public function wp_swift_admin_menu_add_admin_menu() {
		$this->menu_title = "WP Swift";
		$icon = 'assets/images/icon.png';
		$options = get_option( 'wp_swift_admin_menu_settings' );
		if (isset($options['branding_select']) && $options['branding_select']==2) {
			$this->menu_title = "BrightLight";
			$icon = 'assets/images/icon-2.png';
		}
	
		# Create top-level menu item
		add_menu_page( 
			$this->page_title,
		   	$this->menu_title,
		   	$this->capability,
		   	$this->menu_slug, 
		   	array($this, 'wp_swift_admin_menu_options_page_render'), 
		   	plugins_url( $icon, __FILE__ )
		);

    	add_submenu_page($this->menu_slug, $this->page_title, $this->page_title, $this->capability, $this->menu_slug );

		if(function_exists('acf_add_options_page')) { 
			/*
			 * Submenu pages
			 */
	        if ($this->show_sidebar_option('show_sidebar_option_contact_details')) {
	            acf_add_options_sub_page(array(
	                'title' => 'Contact Details',
	                'slug' => 'contact_details',
	                'parent' => $this->menu_slug,
	            ));
	        }
		    if($this->show_sidebar_option('show_sidebar_option_social_media')) {
	            acf_add_options_sub_page(array(
	                'title' => 'Social Media',
	                'slug' => 'social_media',
	                'parent' => $this->menu_slug,
	            ));
	        }
		    // if($this->show_sidebar_option('show_sidebar_options_google_map')) {
	     //        acf_add_options_sub_page(array(
	     //            'title' => 'Google Map',
	     //            'slug' => 'google-map',
	     //            'parent' => $this->menu_slug,
	     //        )); 
	     //    }        
		    // if($this->show_sidebar_option('show_sidebar_options_opening_hours')) {
	     //        acf_add_options_sub_page(array(
	     //            'title' => 'Opening Hours',
	     //            'slug' => 'opening-hours',
	     //            'parent' => $this->menu_slug,
	     //        )); 
	     //    }
	        /*
	         * This is a top level page outside the main menu
	         */
	        $show_sidebar_options_test_page=false;
	    	if($show_sidebar_options_test_page || $this->show_sidebar_option('show_sidebar_options_test_page')) {
		    	$test_args = array(
					'page_title' => 'Test Page - For Developent purposes Only!',
					'menu_title' => 'Test Page',
					'menu_slug' => 'wp-swift-admin-menu-test-page',
					'capability' => $this->capability,
					'icon_url' => 'dashicons-hammer',
				);
				acf_add_options_page($test_args);
	        }
	    }
	}

	/*
	 * Register all of the settings that are used on all the tabs
	 *
	 */
	public function wp_swift_admin_menu_settings_init(  ) { 

		/******************************************************************************
		 *
		 * Register the settings for the 'Menu Options' tab 
		 *
		 ******************************************************************************/	

		register_setting( 'menu_options', 'wp_swift_admin_menu_settings' );

		add_settings_section(
			'wp_swift_admin_menu_menu_options_section', 
			__( 'Configuration Page', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_settings_section_callback'), 
			'menu_options'
		);

		add_settings_field( 
			'show_sidebar_option_contact_details', 
			__( 'Show Contact Page', 'wp-swift-admin-menu' ), 
			array($this, 'show_sidebar_option_contact_details_render'), 
			'menu_options', 
			'wp_swift_admin_menu_menu_options_section',
			array( 'label_for' => 'myprefix_setting-id' ) 
		);

		add_settings_field( 
			'show_sidebar_option_social_media', 
			__( 'Show Social Media', 'wp-swift-admin-menu' ), 
			array($this, 'show_sidebar_option_social_media_render'), 
			'menu_options', 
			'wp_swift_admin_menu_menu_options_section' 
		);

		add_settings_field( 
			'show_sidebar_options_opening_hours', 
			__( 'Settings Opening Hours', 'wp-swift-admin-menu' ), 
			array($this, 'show_sidebar_options_opening_hours_render'), 
			'menu_options', 
			'wp_swift_admin_menu_menu_options_section' 
		);

		add_settings_field( 
			'show_sidebar_options_google_map', 
			__( 'Show Google Map', 'wp-swift-admin-menu' ), 
			array($this, 'show_sidebar_options_google_map_render'), 
			'menu_options', 
			'wp_swift_admin_menu_menu_options_section' 
		);	

		add_settings_field( 
			'branding_select', 
			__( 'Branding', 'wp-swift-admin-menu' ), 
			array($this, 'branding_select_render'), 
			'menu_options', 
			'wp_swift_admin_menu_menu_options_section' 
		);		

		// /******************************************************************************
		//  *
		//  * Register the settings for the 'Google Maps' tab
		//  *
		//  ******************************************************************************/	

		// register_setting( 'google-map', 'wp_swift_google_map_settings' );

		// add_settings_section(
		// 	'wp_swift_admin_menu_google_map_page_section', 
		// 	__( 'Google Map Settings', 'wp-swift-admin-menu' ), 
		// 	array($this, 'wp_swift_admin_menu_google_map_section_callback'), 
		// 	'google-map'
		// );

		// add_settings_field( 
		// 	'show_sidebar_options_google_map_api_key', 
		// 	__( 'Google Map API key', 'wp-swift-admin-menu' ), 
		// 	array($this, 'show_sidebar_options_google_map_api_key_render'), 
		// 	'google-map', 
		// 	'wp_swift_admin_menu_google_map_page_section',
		// 	array( 'label_for' => 'google-map-api-key' )
		// );

		// add_settings_field( 
		// 	'show_sidebar_options_google_map_style', 
		// 	__( 'Google Map Style', 'wp-swift-admin-menu' ), 
		// 	array($this, 'show_sidebar_options_google_map_style_render'), 
		// 	'google-map', 
		// 	'wp_swift_admin_menu_google_map_page_section',
		// 	array( 'label_for' => 'google-map-style' ) 
		// );


/*
 * ********************
 */
// register_setting( 'contact-form', 'wp_swift_contact_form_settings' );

// add_settings_section(
// 	'wp_swift_admin_menu_contact_form_page_section', 
// 	__( 'Contact Settings', 'wp-swift-admin-menu' ), 
// 	array($this, 'wp_swift_admin_menu_contact_form_section_callback'), 
// 	'contact-form'
// );

// add_settings_field( 
// 	'show_sidebar_options_contact_form_api_key', 
// 	__( 'Contact API key', 'wp-swift-admin-menu' ), 
// 	array($this, 'show_sidebar_options_contact_form_api_key_render'), 
// 	'contact-form', 
// 	'wp_swift_admin_menu_contact_form_page_section'
// );
		/******************************************************************************
		 *
		 * Register the settings for the 'Utilities' tab help
		 *
		 ******************************************************************************/		

		register_setting( 'utilities', 'wp_swift_utilities_settings' );

		add_settings_section(
			'wp_swift_admin_menu_utilities_page_section', 
			__( 'Utility Settings', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_utilities_section_callback'), 
			'utilities'
		);
		# Remove the "Add Media" button above the WYSIWYG editor
		add_settings_field( 
			'remove_add_media_button', 
			__( 'Remove Add Media', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_utilities_page_remove_media_upload_render'), 
			'utilities', 
			'wp_swift_admin_menu_utilities_page_section'
		);
		add_settings_field( 
			'extend_wysiwyg', 
			__( 'Extend WYSIWYG', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_utilities_page_extend_wysiwyg_render'), 
			'utilities', 
			'wp_swift_admin_menu_utilities_page_section'
		);		

		add_settings_field( 
			'acf_additional_fields', 
			__( 'ACF Additional Fields', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_utilities_page_acf_additional_fields'), 
			'utilities', 
			'wp_swift_admin_menu_utilities_page_section'
		);	

		add_settings_field( 
			'featured_image', 
			__( 'Featured Image', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_utilities_featured_image'), 
			'utilities', 
			'wp_swift_admin_menu_utilities_page_section'
		);	
		/******************************************************************************
		 *
		 * Register the settings for the 'Help Page' tab
		 *
		 ******************************************************************************/		
		register_setting( 'help-page', 'wp_swift_admin_menu_settings' );

		add_settings_section(
			'wp_swift_admin_menu_help_page_section', 
			__( 'Developer Notes', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_help_section_callback'), 
			'help-page'
		);

		// add_settings_field( 
		// 	'show_help_google_map', 
		// 	__( 'Google Map', 'wp-swift-admin-menu' ), 
		// 	array($this, 'wp_swift_admin_menu_help_page_google_map_render'), 
		// 	'help-page', 
		// 	'wp_swift_admin_menu_help_page_section'
		// );


		add_settings_field( 
			'show_help_contact_page', 
			__( 'Contact Page', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_help_page_contact_render'), 
			'help-page', 
			'wp_swift_admin_menu_help_page_section'
		);

		add_settings_field( 
			'show_help_social_media_page', 
			__( 'Social Media Page', 'wp-swift-admin-menu' ), 
			array($this, 'wp_swift_admin_menu_help_page_social_media_render'), 
			'help-page', 
			'wp_swift_admin_menu_help_page_section'
		);		
	}

	/******************************************************************************
	 *
	 * Render the top level menu page tabs. All other items will be rendered under this
	 *
	 ******************************************************************************/
	public function wp_swift_admin_menu_options_page_render(  ) { 
		include "_tabs.php";
	}

	/******************************************************************************
	 *
	 * Render the description and checkboxes that show on 
	 * the 'Settings' page -> 'Menu Options' tab
	 *
	 ******************************************************************************/

	/*
	 * The description for the 'Menu Options' tab
	 */
	public function wp_swift_admin_menu_settings_section_callback(  ) { 
		echo __( 'Select the options pages you wish to show below', 'wp-swift-admin-menu' );
	}
	/*
	 * Render checkbox that determines if the 'contact page' menu should be shown
	 */
	public function show_sidebar_option_contact_details_render(  ) { 
		$options = get_option( 'wp_swift_admin_menu_settings' );
		?><input type="checkbox" value="1" name="wp_swift_admin_menu_settings[show_sidebar_option_contact_details]" <?php 
			if (isset($options['show_sidebar_option_contact_details'])) {
			 	checked( $options['show_sidebar_option_contact_details'], 1 );
			} 
		?>><?php
	}

	/*
	 * Render checkbox that determines if the 'social_media' menu should be shown
	 */
	public function show_sidebar_option_social_media_render(  ) { 
		$options = get_option( 'wp_swift_admin_menu_settings' );
		?><input type="checkbox" value="1" name="wp_swift_admin_menu_settings[show_sidebar_option_social_media]" <?php 
			if (isset($options['show_sidebar_option_social_media'])) {
				checked( $options['show_sidebar_option_social_media'], 1 );
			}
		?>><?php
	}

	/*
	 * Render checkbox that determines if the 'social_media' menu should be shown
	 */
	public function show_sidebar_options_opening_hours_render(  ) { 
		$options = get_option( 'wp_swift_admin_menu_settings' );
		?><input type="checkbox" value="1" name="wp_swift_admin_menu_settings[show_sidebar_options_opening_hours]" <?php 
			if (isset($options['show_sidebar_options_opening_hours'])) {
				checked( $options['show_sidebar_options_opening_hours'], 1 ); 
			}
		?>><?php
	}

	/*
	 * Render checkbox that determines if the 'google_map' menu should be shown
	 */
	public function show_sidebar_options_google_map_render(  ) { 
		$options = get_option( 'wp_swift_admin_menu_settings' );
		?><input type="checkbox" value="1" id="show-sidebar-options-google-map" name="wp_swift_admin_menu_settings[show_sidebar_options_google_map]" <?php 
			if (isset($options['show_sidebar_options_google_map'])) {
				checked( $options['show_sidebar_options_google_map'], 1 ); 
			}
		?>><?php
	}

/*
	 * Render select box that determines branding used
	 */
	public function branding_select_render(  ) { 

		$options = get_option( 'wp_swift_admin_menu_settings' );
		// if (isset($options['branding_select'])) {
		// 	$options['branding_select']=1;
		// }
		?><select id="branding_select" name="wp_swift_admin_menu_settings[branding_select]">
			<option value='1' <?php selected( $options['branding_select'], 1 ); ?>>WP Swift</option>
			<option value='2' <?php selected( $options['branding_select'], 2 ); ?>>BrightLight</option>
		</select><?php

	}

	# @end Render 'Settings' page -> 'Menu Options' tab






	/******************************************************************************
	 *
	 * Render the description and checkboxes that show on 
	 * the 'Settings' page -> 'Utilities' tab
	 *
	 ******************************************************************************/

	/*
	 * The description for the 'Utilities' tab
	 */
	public function wp_swift_admin_menu_utilities_section_callback(  ) { 
		echo __( 'Select the options below as see fit.', 'wp-swift-admin-menu' );
	}
	/*
	 * Render checkbox that determines if "Add Media" button above the WYSIWYG editor is shown
	 */
	public function wp_swift_admin_menu_utilities_page_remove_media_upload_render(  ) { 
		$options = get_option( 'wp_swift_utilities_settings' );
		?><input type="checkbox" value="1" name="wp_swift_utilities_settings[remove_add_media_button]" <?php 
			if (isset($options['remove_add_media_button'])) {
			 	checked( $options['remove_add_media_button'], 1 );
			} 
		?>><p>Remove the <b>"Add Media"</b> button above the WYSIWYG editor.</p><?php
	}

	/*
	 * Render checkbox that determines if "Add Media" button above the WYSIWYG editor is shown
	 */
	public function wp_swift_admin_menu_utilities_page_extend_wysiwyg_render(  ) { 
		$options = get_option( 'wp_swift_utilities_settings' );
		?><input type="checkbox" value="1" name="wp_swift_utilities_settings[extend_wysiwyg]" <?php 
			if (isset($options['extend_wysiwyg'])) {
			 	checked( $options['extend_wysiwyg'], 1 );
			} 
		?>><p class="desc">Creates a format select dropdown in the second row of the TinyMCE editor for handling <b>Zurb Foundation</b> CSS classes and container components.</p><?php
	}

	/*
	 * Render checkbox that determines if "Add Media" button above the WYSIWYG editor is shown
	 */
	public function wp_swift_admin_menu_utilities_featured_image(  ) { 
		$options = get_option( 'wp_swift_utilities_settings' );
		?><input type="checkbox" value="1" name="wp_swift_utilities_settings[featured_image]" <?php 
			if (isset($options['featured_image'])) {
			 	checked( $options['featured_image'], 1 );
			} 
		?>><p class="desc">This is a helper function for developers does not do anything visible to users.</p><?php
	}
	/*
	 * Render checkbox that determines if "Add Media" button above the WYSIWYG editor is shown
	 */
	public function wp_swift_admin_menu_utilities_page_acf_additional_fields(  ) { 
		$options = get_option( 'wp_swift_utilities_settings' );
		?><input type="checkbox" value="1" name="wp_swift_utilities_settings[acf_additional_fields]" <?php 
			if (isset($options['acf_additional_fields'])) {
			 	checked( $options['acf_additional_fields'], 1 );
			} 
		?>><span class="desc"><b>Use ACF Additional Fields</b></span>
		<hr>
		<input type="checkbox" value="1" name="wp_swift_utilities_settings[acf_additional_fields_style_and_script]" <?php 
			if (isset($options['acf_additional_fields_style_and_script'])) {
			 	checked( $options['acf_additional_fields_style_and_script'], 1 );
			} 
		?>><span class="desc">Load JavaScript and CSS from plugin <small>(Untick if you wish to handle this in the theme)</small></span>
		<br>
		<input type="checkbox" value="1" name="wp_swift_utilities_settings[acf_additional_fields_show_on_post]" <?php 
			if (isset($options['acf_additional_fields_show_on_post'])) {
			 	checked( $options['acf_additional_fields_show_on_post'], 1 );
			} 
		?>><span class="desc">Show on posts</span>
		<br>
		<input type="checkbox" value="1" name="wp_swift_utilities_settings[acf_additional_fields_show_on_page]" <?php 
			if (isset($options['acf_additional_fields_show_on_page'])) {
			 	checked( $options['acf_additional_fields_show_on_page'], 1 );
			} 
		?>><span class="desc">Show on pages</span>
		<br>
		<input type="text" placeholder="testimonal, faq" name="wp_swift_utilities_settings[acf_additional_fields_cpt]" <?php 
			if (isset($options['acf_additional_fields_cpt'])) {
			 	echo ' value="'.$options['acf_additional_fields_cpt'].'"';
			} 
		?>><span class="desc">Additional post types <small>(Comma seperated list)</small></span>
		<br>
		<p>Use ACF Additional Fields to allow users select media such as galleries and video.</p><?php


 
$all_post_types = get_post_types( '', 'names' );

// echo "<pre>"; var_dump($post_types); echo "</pre>";
// echo "<pre>"; var_dump($all_post_types); echo "</pre>";
foreach ( $all_post_types as $post_type ) {

	if (in_array($post_type, $all_post_types)) {
		// echo '<p>' . $post_type . '</p>';
	}
}

$ignore_post_types = array(
	'attachment',
	'revision',
	'nav_menu_item',
	'custom_css',
	'customize_changeset',
	'acf-field-group',
	'acf-field',
);


$args = array(
   'public'   => true,
   '_builtin' => false
);
$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'

$post_types = get_post_types( $args, $output, $operator ); 
// echo "<pre>"; var_dump($post_types); echo "</pre>";

	}

	/******************************************************************************
	 *
	 * Render the description and help content that show on 
	 * the 'Settings' page -> 'Help Page' tab
	 *
	 ******************************************************************************/

	/*
	 * The description for the 'Help Page' tab
	 */
	public function wp_swift_admin_menu_help_section_callback(  ) { 
		echo __( 'These are developer notes that are made to help with the theme development and be a reference page for trouble shooting.', 'wp-swift-admin-menu' );
	}

	/*
	 * Render help content for contact page
	 */
	public function wp_swift_admin_menu_help_page_contact_render () {
		include "help-page-partials/_contact-page.php";
	}

	/*
	 * Render help content for google map page
	 */
	// public function wp_swift_admin_menu_help_page_google_map_render() {
	// 	include "help-page-partials/_google-map.php";
	// }

	public function wp_swift_admin_menu_help_page_social_media_render() {
		include "help-page-partials/_social-media.php";
	}

	# @end Render 'Settings' page -> 'Help Page'
}
$wp_swift_admin_menu = new WP_Swift_Admin_Menu();
register_activation_hook( __FILE__, array( 'WP_Swift_Admin_Menu', 'wp_swift_admin_menu_plugin_install' ) );
register_deactivation_hook( __FILE__, array( 'WP_Swift_Admin_Menu', 'wp_swift_admin_menu_plugin_deactivate' ) );

/*
 * Include the functions that render shortcodes
 */  
include "_shortcodes.php";

/*
 * Allow users put admin bar on bottom (visible in the profile area)
 */
include "utilities/_admin-bar-position.php";
include "get-phone.php";
/*
 * Include the function that will render the google map
 */    
// include "wp-swift-google-map.php";



/*
 * Include the class that will render opening hours
 */   
// if(wp_swift_show_sidebar_option('show_sidebar_options_opening_hours')) { 
// 	include "opening-hours/class-opening-hours.php";
// }
/*
 * Include the class that will render opening hours
 */   
if(wp_swift_show_sidebar_option('show_sidebar_options_google_map')) { 
	include "google-maps/class-google-maps.php";
}
/**
 * A helper fuction that tests if an option is set
 *
 * @param  string  $option     	The text content for shortcode. Not used.
 *
 * @return boolean				If the option is set   
 */
function wp_swift_show_sidebar_option($option) {
	$options = get_option( 'wp_swift_admin_menu_settings' );
	if (isset($options[$option]) && $options[$option]) {
		return true;
	}
	return false;
}

/*
 * This determines the location the menu links
 * They are listed under Settings unless the other plugin 'wp_swift_admin_menu' is activated
 */
function wp_swift_get_parent_slug() {
    if ( get_option( 'wp_swift_admin_menu' ) ) {
        return get_option( 'wp_swift_admin_menu' );
    }
    else {
        return 'options-general.php';
    }
}

function wp_swift_admin_menu_slug() {
	return 'wp-swift-admin-menu';
}