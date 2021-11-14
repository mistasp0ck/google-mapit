<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tonystaffiero.com
 * @since      1.0.0
 *
 * @package    Google_Mapit
 * @subpackage Google_Mapit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Google_Mapit
 * @subpackage Google_Mapit/admin
 * @author     Tony Staffiero <me@tonystaffiero.com>
 */
class Google_Mapit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Options for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $options    Options for this plugin
	 */
	private $options;

	/**
	 * Meta Fields
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $location_meta_fields
	 */
	private $location_meta_fields;

	/**
	 * Meta Fields
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $location_category_meta_fields
	 */
	private $location_category_meta_fields;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		// @todo: add depreciation conditional here
		$prefix = 'gmi_'; 
		$this->prefix = $prefix;


		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// $this->taxonomy = $taxonomy;
		$this->options = get_option( 'google_mapit_settings' );

		add_image_size($prefix . 'custom_icon_small', 40,40,true);
		add_image_size($prefix . 'custom-icon_large', 80,80,true);

		define( 'GML_PAGE_MAIN_SLUG', 'google-maps-locator' );

		// Field Array  
		$this->location_meta_fields = array(  
			array(  
			    'label'=> 'Enter your Location',  
			    'desc'  => '',  
			    'name'    => $prefix . 'location',  
			    'type'  => 'location' 
			),  
	    array(  
	        'label'=> 'Address',  
	        'desc'  => '',  
	        'id' => $prefix . 'street_addy',
	        'name'    => $prefix . 'address',   
	        'type'  => 'text' 
	    ), 
	    array(  
	        'label'=> 'Address 2',  
	        'desc'  => '',  
	        'name'    => $prefix . 'address2',  
	        'type'  => 'text' 
	    ),  
	    array(  
	        'label'=> 'City',  
	        'desc'  => '',  
	        'id' => 'locality', 
	        'name'    => $prefix . 'city',  
	        'type'  => 'text' 
	    ),  
	    array(  
	        'label'=> 'State',  
	        'desc'  => '',  
	        'id' => 'administrative_area_level_1',
	        'name'    => $prefix . 'state',  
	        'type'  => 'text' 
	    ),          
	    array(  
	        'label'=> 'Zip Code',  
	        'desc'  => '',  
	        'id' => 'postal_code', 
	        'name'    => $prefix . 'postal',  
	        'type'  => 'text' 
	    ),  
	    array(  
	        'label'=> 'Country',  
	        'desc'  => '',  
	        'id' => $prefix . 'country', 
	        'name'    => $prefix . 'country',  
	        'type'  => 'text' 
	    ),  
	    array(  
	        'label'=> 'Latitude',  
	        'id'    => 'lat',  
	        'name'    => $prefix . 'lat', 
	        'type'  => 'text' 
	    ),
	    array(  
	        'label'=> 'Longitude',   
	        'id'    => 'lng',  
	        'name'    => $prefix . 'lng',  		        
	        'type'  => 'text' 
	    ), 
	    array(  
	        'label'=> 'Phone',  
	        'desc'  => '',  
	        'id'    => $prefix . 'phone',  
	        'name'    => $prefix . 'phone', 
	        'type'  => 'text' 
	    ),
	    array(  
	        'label'=> 'Link',  
	        'desc'  => 'http://www.example.com',  
	        'id'    => $prefix . 'link',  
	        'name'    => $prefix . 'link', 
	        'type'  => 'text' 
	    )
		 
		); 

		$this->location_category_meta_fields = array (
			array(  
			    'label'=> 'Add custom Icon',  
			    'desc'  => '',  
			    'name'    => 'custom_icon',  
			    'id' => $prefix . 'custom_icon', 
			    'type'  => 'image' 
			) 

		);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Google_Mapit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Google_Mapit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/admin-styles.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		global $post;

		if ( !empty($post->post_type) && 'location' == $post->post_type && ($hook == 'post.php' || $hook == 'post-new.php') ) {
    

			$api_key = get_option('google_mapit_api_key');
			$google_maps_url = 'https://maps.googleapis.com/maps/api/js';

			$google_api_url = add_query_arg( array(
			    'key' => $api_key,
			    'libraries' => 'places',
			    'region' => 'US',
			    'callback' => 'initAutocomplete'
			), $google_maps_url );

			wp_enqueue_script( 'google-api', $google_api_url, '', $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/google-mapit-admin.js', array(), $this->version, true );

			wp_localize_script( 'locator-map', 'googleStorePluginUrl', $vars ); 

		} 
		
		if ($hook == 'edit-tags.php' || $hook == 'term.php') {
			wp_enqueue_script('jquery');
			// This will enqueue the Media Uploader script

			wp_enqueue_media();

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/google-mapit-admin.js', array(), $this->version, true );
		}
		// error_log($hook);
		if ($hook == 'toplevel_page_gmi_general' || $hook == 'location-map_page_gmi_design_options' || $hook == 'location-map_page_gmi_location_options') {
			// This will enqueue the Media Uploader script
			wp_enqueue_media();

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/google-mapit-admin.js', array('wp-color-picker','jquery'), $this->version, true );

		}
	}
	function get_array_val($array, $index = '') {
		(!empty($array[$index])) ? $value = $array[$index] : $value = '';
		
		return $value;
	}

	function google_mapit_add_admin_menu(  ) { 

		add_menu_page( 
			'Google MapIt Settings', 
			'MapIt', 
			'manage_options', 
			'gmi_general', array(&$this, 'google_mapit_options_pages'),
			'dashicons-location-alt'
		);

		add_submenu_page( 
			'gmi_general', 
			'Settings',
			'Settings', 
			'manage_options', 
			'gmi_general', array(&$this, 'google_mapit_options_pages')
		);

		add_submenu_page( 
			// $parent_slug
			'gmi_general', 
			// $page_title
			'Location Options',
			// $menu_title
			'Location Options', 
			// $capability
			'manage_options', 
			// $menu_slug
			'gmi_location_options', 
			// $function
			array(&$this, 'google_mapit_options_pages' )
		);

		add_submenu_page( 
			// $parent_slug
			'gmi_general', 
			// $page_title
			'Design Options',
			// $menu_title
			'Design Options', 
			// $capability
			'manage_options', 
			// $menu_slug
			'gmi_design_options', 
			// $function
			array(&$this, 'google_mapit_options_pages' )
		);

	}

	public function setup_sections() {
	    add_settings_section( 'main_section', 'General Settings', array( $this, 'section_callback' ), 'gmi_general' );

	    add_settings_section( 'info_section', 'Info Window Defaults', array( $this, 'section_callback' ), 'gmi_general' );

	    add_settings_section( 'list_section', 'Location List Defaults', array( $this, 'section_callback' ), 'gmi_general' );

	    add_settings_section( 'location_section', 'Location Settings', array( $this, 'section_callback' ), 'gmi_location_options' );

	    add_settings_section( 'design_section', 'Design Settings', array( $this, 'section_callback' ), 'gmi_design_options' );

	}

	public function google_mapit_setup_fields() { 
		$prefix = 'gmi_';
		$fields = array(
		    array(
		        'uid' => $prefix . '_api_key',
		        'label' => 'Google API Key',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" class="small-link">get API key</a>',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'enable_plugin_api',
		        'label' => 'Enable Locations API?',
		        'section' => 'main_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'api_url',
		        'label' => 'External Locations Url',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'map_height',
		        'label' => 'Default Map Height',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'map_width',
		        'label' => 'Default Map Width',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'full_width',
		        'label' => 'Full Width Map?',
		        'section' => 'main_section',
		        'type' => 'checkbox',
		        'supplemental' => 'This will override the default width',
		        'options' => array(
        			'true' => 'Yes'
        		),
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'zoom',
		        'label' => 'Initial Zoom',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => 'Zoom Range 0 - 21~ <em>0 is zoomed out all the way</em>',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'max_zoom',
		        'label' => 'Maximum Zoom',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => 'Zoom Range 0 - 21~ <em>0 is zoomed out all the way</em>',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'bounds_padding',
		        'label' => 'Location Bounds Padding',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    // @todo: add autocomplete type for this
		    array(
		        'uid' => $prefix . 'default_location',
		        'label' => 'Default Address',
		        'section' => 'main_section',
		        'type' => 'text',
		        'options' => false,
		        'placeholder' => '',
		        // 'helper' => 'Does this help?',
		        'supplemental' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'info_show_directions',
		        'label' => 'Show directions link?',
		        'section' => 'info_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
        		'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'info_show_link',
		        'label' => 'Show external link?',
		        'section' => 'info_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
        		'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'info_link_text',
		        'label' => 'Link Text',
		        'section' => 'info_section',
		        'type' => 'text',
		        'placeholder' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),

		    array(
		        'uid' => $prefix . 'list_show_directions',
		        'label' => 'Show directions link?',
		        'section' => 'list_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
        		'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'list_show_link',
		        'label' => 'Show external link?',
		        'section' => 'list_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
        		'default' => '',
		        'page' => 'gmi_general'
		    ),
		    array(
		        'uid' => $prefix . 'list_link_text',
		        'label' => 'Link Text',
		        'section' => 'list_section',
		        'options' => false,
		        'type' => 'text',
		        'placeholder' => '',
		        'default' => '',
		        'page' => 'gmi_general'
		    ),

		    // design page
		    array(
		        'uid' => $prefix . 'button_color',
		        'label' => 'Button Color',
		        'section' => 'design_section',
		        'type' => 'colorpicker',
		        'options' => false,
		        'default' => '#337ab7',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'button_color_hover',
		        'label' => 'Button Color Hover',
		        'section' => 'design_section',
		        'type' => 'colorpicker',
		        'options' => false,
		        'default' => '#286090',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'button_text_color',
		        'label' => 'Button Text Color',
		        'section' => 'design_section',
		        'type' => 'colorpicker',
		        'options' => false,
		        'default' => '#ffffff',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'link_text_color',
		        'label' => 'View All Text Color',
		        'section' => 'design_section',
		        'type' => 'colorpicker',
		        'options' => false,
		        'default' => '#888888',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'default_icon',
		        'label' => 'Default Icon',
		        'section' => 'design_section',
		        'supplemental' => 'Icon for Location marker',
		        'type' => 'image',
		        'options' => false,
		        'default' => '',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'icon_size',
		        'label' => 'Icon Size',
		        'section' => 'design_section',
		        // 'supplemental' => 'Icon for Location marker',
		        'type' => 'text',
		        'options' => false,
		        'default' => '',
		        'page' => 'gmi_design_options'
		    ),
		    array(
		        'uid' => $prefix . 'map_style',
		        'label' => 'Map Styling ',
		        'section' => 'design_section',
		        'supplemental' => '(import JSON formatted style)',
		        'type' => 'textarea',
		        'options' => false,
		        'default' => '',
		        'page' => 'gmi_design_options'
		    ),		    
		    array(
		        'uid' => $prefix . 'enable_single',
		        'label' => 'Enable Single Page?',
		        'section' => 'location_section',
		        'type' => 'checkbox',
		        'options' => array(
        			'true' => 'Yes'
        		),
        		'default' => '',
		        'page' => 'gmi_location_options'
		    ),
		    array(
		        'uid' => $prefix . 'taxonomies',
		        'post_type' => 'location',
		        'label' => 'Taxonomies to Filter',
		        'section' => 'location_section',
		        'type' => 'taxonomy',
		        'default' => '',
		        'page' => 'gmi_location_options'
		    ),
		    array(
		        'uid' => $prefix . 'post_types',
		        // 'post_type' => 'location',
		        'label' => 'Enable Post Types',
		        'section' => 'location_section',
		        'type' => 'post_type',
		        'default' => '',
		        'page' => 'gmi_location_options',
		        'supplemental' => 'This enables post types to be associated with a location',
		    ),
		    array(
		        'uid' => $prefix . 'post_type_',
		        // 'post_type' => 'location',
		        'label' => 'Filter by ',
		        'section' => 'location_section',
		        'type' => 'post_type_filtering',
		        'default' => '-1',
		        'page' => 'gmi_location_options',
		        'supplemental' => 'Set which associated post types to filter by',
		    ),
		);

		foreach( $fields as $field ){

			if ($field['type'] == 'post_type_filtering') {
				// loop through enabled post types
				$post_types = get_option($prefix . 'post_types');
				if (!empty($post_types)) { 
					$args = array(
					   'public'   => true,
					   '_builtin' => false
					);

					$output = 'names'; // names or objects, note names is the default
					$operator = 'and'; // 'and' or 'or'
					foreach ($post_types as $p_type) {
						$field['post_type'] = $p_type;
						$uid = $field['uid'] . $p_type;
						$label = $field['label'] . $p_type;
						// error_log('*****uid in add_settings_field: ' . $uid);
						// error_log(print_r($field,true));
						add_settings_field( 
							$uid, 
							$label, 
							array( &$this, 'field_callback' ), 
							$field['page'],
							$field['section'], 
							$field 
						);

						register_setting( $field['page'], $uid );
					}
				}
			} else {
				add_settings_field( 
					$field['uid'], 
					$field['label'], 
					array( &$this, 'field_callback' ), 
					$field['page'],
					$field['section'], 
					$field 
				);	

				register_setting( $field['page'], $field['uid'] );
			}



		}
	}

	public function field_callback( $arguments ) {

    $value = get_option( $arguments['uid'] );

    if( ! $value ) {
      $value = $arguments['default'];
    }

    switch( $arguments['type'] ){
      case 'text':
      case 'password':
      case 'number':
        printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
        break;
      case 'textarea':
        printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
        break;
      case 'select':
      case 'multiselect':
        if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
            $attributes = '';
            $options_markup = '';
            foreach( $arguments['options'] as $key => $label ){
                $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
            }
            if( $arguments['type'] === 'multiselect' ){
                $attributes = ' multiple="multiple" ';
            }
            printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
        }
        break;
      case 'radio':
      case 'checkbox':
        if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
            $options_markup = '';
            $iterator = 0;
            foreach( $arguments['options'] as $key => $label ){
                $iterator++;
                $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
            }
            printf( '<fieldset>%s</fieldset>', $options_markup );
        }
        break;
      case 'colorpicker':
    		printf('<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" data-default-color="%5$s" class="colorpicker" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value, $arguments['default']);
    		break;
      case 'image':

    		// See if there's a media id already saved as post meta
    		$img_id = $value;

    		// Get the image src
    		$img_src = wp_get_attachment_image_src( $img_id, 'full' );
    		// For convenience, see if the array is valid
    		$has_img = is_array( $img_src ); ?>
    		  <div>
  		      <div style="width: 100%;clear:both;" class="preview meta-img-container">    
  		      <?php 
  		      $btntext = '';

  		      if ( $has_img ) : 
  		        $btntext = "Change Image"; ?>
  		      <img src="<?php echo $img_src[0] ?>" alt="" class="image-preview" style="max-width:100%" />
  		      <?php else :
  		        $btntext = "Add Image";
  		       ?>
  		        
  		      <?php endif; ?>
  		      </div>
  		      <input type="button" name="upload-btn" class="upload-meta-img button-secondary upload-media" value="<?php echo $btntext; ?>"> <input type="button" name="reset-btn" class="reset-btn button-secondary" value="Reset"> <?php
  		      // A hidden input to set and post the chosen image id
  		      printf('<input name="%1$s" class="meta-img-id" id="%1$s" type="hidden" value="%2$s" />', $arguments['uid'], $value);

	  		break;	
  		case 'taxonomy':
  			$taxonomy_objects = get_object_taxonomies( $arguments['post_type'], 'object' );
  			$i = 0;
  			?>
  			<ul>
  			<?php foreach ($taxonomy_objects as $tax) { 
  				$checked = (in_array($tax->name, $value)) ? 'checked="checked"' : '';
  				?>
  				<li style="margin-bottom: 5px;"> 
  					<?php
  				printf('<input style="margin-right: 5px;" name="%1$s[]" class="meta-img-id" id="%1$s" type="checkbox" value="%2$s" %3$s /> <label>%4$s</label>', $arguments['uid'], $tax->name, $checked, $tax->label, $value);
  				?>
  				</li>
  			<?php
  				$i++;
  			} ?>
  			</ul>
  			<?php

	  		break;
  		case 'post_type' :
	  		$args = array(
	  		   'public'   => true,
	  		   '_builtin' => false
	  		);

	  		$output = 'names'; // names or objects, note names is the default
	  		$operator = 'and'; // 'and' or 'or'

	  		$post_types = get_post_types( $args, $output, $operator ); ?>

	  		<ul>
	  		<?php foreach ( $post_types as $post_type ) { 
	  			if ($post_type == 'location') {
	  				continue;
	  			}
	  			$checked = (in_array($post_type, $value)) ? 'checked="checked"' : '';
	  			?>
	  			<li style="margin-bottom: 5px;">
					<?php
	  			  printf('<input style="margin-right: 5px;" name="%1$s[]" class="meta-img-id" id="%1$s" type="checkbox" value="%2$s" %3$s /> <label>%4$s</label>', $arguments['uid'], $post_type, $checked, $post_type, $value); ?>
	  			</li>  
	  		<?php  
	  		} ?>
	  		</ul>	  		
	<?php
				break;
  		case 'post_type_filtering' :
	  		$posts_array = $this->get_posts_array($arguments['post_type']); 
	  		$i = 0;
	  		// override normal get_option var above
	  		$type_slug = $arguments['post_type'];
	  		$uid = $arguments['uid'] . $type_slug;
        $value = get_option( $uid );

	  		?>
	  		<ul>
	  			<li style="margin-bottom: 5px;">
	  				<?php $checked = (!empty($value) && in_array('-1', $value)) ? 'checked="checked"' : ''; ?>
	  				<?php printf('<input style="margin-right: 5px;" name="%1$s[]" class="meta-img-id" id="all" type="checkbox" value="-1" %3$s /> <label>All</label>', $uid, $key, $checked, $val); ?>
	  			</li>
	  		<?php foreach ($posts_array as $key => $val ) { 
		  			$checked = (!empty($value) && in_array($key, $value)) ? 'checked="checked"' : '';
	  			?>
	  			<li style="margin-bottom: 5px;">
					<?php
	  			  printf('<input style="margin-right: 5px;" name="%1$s[]" class="meta-img-id" id="%2$s" type="checkbox" value="%2$s" %3$s /> <label>%4$s</label>', $uid, $key, $checked, $val); ?>
	  			</li>  
	  		<?php  
	  		} ?>
	  		</ul>	  		
	<?php
				break;
	

    }

    if( !empty($arguments['helper']) ){
      printf( '<span class="helper"> %s</span>', $arguments['helper'] );
    }

    if( !empty($arguments['supplemental']) ){
      printf( '<p class="description">%s</p>', $arguments['supplemental'] );
    }

	}

	public function section_callback( $arguments ) {
	  switch( $arguments['id'] ){
      case 'main_section':
        // echo 'This is the first description here!';
        break;
      case 'design_section':
        // echo 'This one is number two';
        break;
      case 'location_section':
        // echo 'This one is number two';
        break;  
      case 'our_third_section':
        // echo 'Third time is the charm!';
        break;
	  }
	}

	function google_mapit_options_pages( $active_page = '' ) { 

		?>
		<div class="wrap">
			<h2><?php _e( 'Google MapIt Settings', 'sandbox' ); ?></h2>
			<?php settings_errors(); ?>
			
			<?php if( isset( $_GET[ 'page' ] ) ) {
				$active_page = $_GET[ 'page' ];

			} else if( $active_page == 'gmi_design_options' ) {
				$active_page = 'gmi_design_options';
			}	else if( $active_page == 'gmi_location_options' ) {
				$active_page = 'gmi_location_options';
			} else {
				$active_page = 'gmi_general';
			} // end if/else ?>
			<div class="nav-tab-wrapper">
				<a href="?page=gmi_general" class="nav-tab <?php echo $active_page == 'gmi_general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Options', 'sandbox' ); ?></a>
				<a href="?page=gmi_location_options" class="nav-tab <?php echo $active_page == 'gmi_location_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Location Options', 'sandbox' ); ?></a>
				<a href="?page=gmi_design_options" class="nav-tab <?php echo $active_page == 'gmi_design_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Design Options', 'sandbox' ); ?></a>
			</div>
			<form class="google-maps-settings-form" action='options.php' method='post'>
				<?php
				
					if( $active_page == 'gmi_design_options' ) {
						settings_fields( 'gmi_design_options' );
						do_settings_sections( 'gmi_design_options' );
						
					} else if( $active_page == 'gmi_location_options' ) {
						settings_fields( 'gmi_location_options' );
						do_settings_sections( 'gmi_location_options' );
						
					} else {
						settings_fields( 'gmi_general' );
						do_settings_sections( 'gmi_general' );
						
					} // end if/else
					
					submit_button();
				
				?>

			</form>
		</div>	
		<?php

	}

	function get_posts_array($post_type){
	  $args = array(
	      'post_type' => $post_type,
	      'posts_per_page' => -1
	  );

	  $posts = get_posts( $args );
	    if ( $posts ) {
	      $posts_array = array();
	        foreach ( $posts as $post ) {
	          $posts_array[$post->ID] = $post->post_title; 
	        }
	      return $posts_array;

	    } else {

	      return false;

	    } 
	}	

	// Locations Posttype
	public function create_locations_posttype() {
		
		$prefix = $this->prefix;

		if (get_option($prefix . 'enable_api')) {
			$json = true;
		} else {
			$json = false;
		}

		if (get_option($prefix . 'enable_single')) {
			$public = true;
		} else {
			$public = false;
		}

		$labels = array(
			'name'               => _x( 'Locations', 'post type general name', 'metro_health' ),
			'singular_name'      => _x( 'Location', 'post type singular name', 'metro_health' ),
			'menu_name'          => _x( 'Locations', 'admin menu', 'metro_health' ),
			'name_admin_bar'     => _x( 'Location', 'add new on admin bar', 'metro_health' ),
			'add_new'            => _x( 'Add New', 'Location', 'metro_health' ),
			'add_new_item'       => __( 'Add New Location', 'metro_health' ),
			'new_item'           => __( 'New Location', 'metro_health' ),
			'edit_item'          => __( 'Edit Location', 'metro_health' ),
			'view_item'          => __( 'View Location', 'metro_health' ),
			'all_items'          => __( 'All Locations', 'metro_health' ),
			'search_items'       => __( 'Search Locations', 'metro_health' ),
			'parent_item_colon'  => __( 'Parent Locations:', 'metro_health' ),
			'not_found'          => __( 'No Locations found.', 'metro_health' ),
			'not_found_in_trash' => __( 'No Locations found in Trash.', 'metro_health' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'metro_health' ),
			'public'             => $public,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-location', 
			'show_in_menu'       => true,
			'show_in_admin_bar'  => true,
			'show_in_nav_menus'  => true,    
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'locations' ),
			'show_in_rest'       => $json,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title','editor', 'page-attributes', 'custom-fields','meta','thumbnail','excerpt' )
		);

		register_taxonomy(
			'location-category',
			'location',
			array(
			  'label' => __( 'Categories' ),
			  'show_in_rest' => $json,
			  'rewrite' => array( 'slug' => 'location-category' ),
			  'show_admin_column' => true,
			  'hierarchical' => true,
			)
		);

		register_post_type( 'location', $args );
	}

	// Add the Meta Box for our locations
	function google_mapit_add_location_meta_box() {  
	global $post;

	    add_meta_box(  
	        'location_meta_box', // $id  
	        'Additional Information for the Location', // $title  
	        array($this ,'google_mapit_show_location_meta_box'), // $callback  
	        'location', // $page  
	        'normal', // $context  
	        'high'); // $priority  
		}

	// The location Meta Box Callback  
	function google_mapit_show_location_meta_box() {  

	global $post;
	$meta_fields = $this->location_meta_fields; 

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'wpbs_nonce' );
	  
	// Begin the field table and loop
	echo '<table class="form-table">';

	foreach ( $meta_fields as $field ) {
	    // get value of this field if it exists for this post  
	    $meta = get_post_meta($post->ID, $field['name'], true);  
	    // begin a table row with  
	    echo '<tr> 
	            <th><label for="'.$field['id'].'">'.$field['label'].'</label></th> 
	            <td>';  
	            switch($field['type']) {  
	                // text  
	                case 'text':  
	                    echo '<input type="text" class="field" name="'.$field['name'].'" id="'.$field['id'].'" value="'.$meta.'" size="60" /> 
	                        <br /><span class="description">'.$field['desc'].'</span>';  
	                break;

	                // checkbox  
	                case 'checkbox':  
	                    echo '<input type="checkbox" class="checkbox" name="'.$field['name'].'" id="'.$field['id'].'" value="'.$meta.'" size="60" /> 
	                        <br /><span class="description">'.$field['desc'].'</span>';  
	                break;
	                
	                // textarea  
	                case 'textarea':  
	                    echo '<textarea name="'.$field['name'].'" id="'.$field['id'].'" cols="80" rows="4">'.$meta.'</textarea> 
	                        <br /><span class="description">'.$field['desc'].'</span>';  
	                break;  

	                case 'image':
	                ?>
	                <?php // Get WordPress' media upload URL
	                //$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

	                // See if there's a media id already saved as post meta
	                $img_id = get_post_meta( $post->ID, $field['id'], true );

	                // Get the image src
	                $img_src = wp_get_attachment_image_src( $img_id, 'full' );

	                // For convenience, see if the array is valid
	                $has_img = is_array( $img_src ); ?>
	                  <div>
	                      <div style="width: 100%;clear:both;" class="preview meta-img-container">    
	                      <?php 
	                      $btntext = '';

	                      if ( $has_img ) : 
	                        $btntext = "Change Image"; ?>
	                      <img src="<?php echo $img_src[0] ?>" alt="" style="max-width:100%;" />
	                      <?php else :
	                        $btntext = "Add Image";
	                       ?>
	                        
	                      <?php endif; ?>
	                      </div>
	                      <input type="button" name="upload-btn" class="upload-meta-img button-secondary upload-media" value="<?php echo $btntext; ?>">

	                      <!-- A hidden input to set and post the chosen image id -->
	                      <input class="meta-img-id" name="<?php echo $field['id']; ?>" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />

	                  </div>
	                <?php  
	                break;
	            
	            	// location w/ google map
	            	case 'location': ?>
	            	  <style>
	            	    html, body {
	            	      height: 100%;
	            	      margin: 0;
	            	      padding: 0;
	            	    }
	            	    #map {
	            	      height: 100%;
	            	    }
	            	  </style>
	            	  <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
	            	  <style>
	            	    #locationField, #controls {
	            	      position: relative;
	            	      width: 480px;
	            	    }
	            	    #autocomplete {
	            	      position: absolute;
	            	      top: 0px;
	            	      left: 0px;
	            	      width: 99%;
	            	    }
	            	    .label {
	            	      text-align: right;
	            	      font-weight: bold;
	            	      width: 100px;
	            	      color: #303030;
	            	    }
	            	    #address {
	            	      border: 1px solid #000090;
	            	      background-color: #f0f0ff;
	            	      width: 480px;
	            	      padding-right: 2px;
	            	    }
	            	    #address td {
	            	      font-size: 10pt;
	            	    }
	            	    .field {
	            	      width: 99%;
	            	    }
	            	    .slimField {
	            	      width: 80px;
	            	    }
	            	    .wideField {
	            	      width: 200px;
	            	    }
	            	    #locationField {
	            	      height: 20px;
	            	      margin-bottom: 2px;
	            	    }
	            	  </style>
	            	</head>

	            	<body>
	            	  <div id="locationField">
	            	    <input id="autocomplete" name="location" value="<?php echo $meta; ?>" placeholder="Enter your address" onFocus="geolocate()" type="text"></input>
	            	  </div>

	            	  <script>
	            	    // This example displays an address form, using the autocomplete feature
	            	    // of the Google Places API to help users fill in the information.

	            	    // This example requires the Places library. Include the libraries=places
	            	    // parameter when you first load the API. For example:
	            	    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

	            	    var placeSearch, autocomplete;
	            	    var componentForm = {
	            	      // street_number: 'short_name',
	            	      street_addy: 'long_name',
	            	      locality: 'long_name',
	            	      administrative_area_level_1: 'short_name',
	            	      postal_code: 'short_name',
	            	      country: 'short_name',
	            	      lat: 'short_name',
	            	      lng: 'short_name'
	            	    };

	            	    function initAutocomplete() {
	            	      // Create the autocomplete object, restricting the search to geographical
	            	      // location types.
	            	      autocomplete = new google.maps.places.Autocomplete(
	            	          /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
	            	          {types: ['geocode']});

	            	      // When the user selects an address from the dropdown, populate the address
	            	      // fields in the form.
	            	      autocomplete.addListener('place_changed', fillInAddress);
	            	    }

	            	    function fillInAddress() {
	            	      // Get the place details from the autocomplete object.
	            	      var place = autocomplete.getPlace();

	            	      for (var component in componentForm) {
	            	      	// console.log(component);
	            	        document.getElementById(component).value = '';
	            	        document.getElementById(component).disabled = false;
	            	      }

	            	      // Get each component of the address from the place details
	            	      // and fill the corresponding field on the form.
	            	      for (var i = 0; i < place.address_components.length; i++) {
	            	        var addressType = place.address_components[i].types[0];
							if (componentForm[addressType]) {
	            	          var val = place.address_components[i][componentForm[addressType]];
	            	          document.getElementById(addressType).value = val;
	            	        }
	            	      }
	            	      if(componentForm['street_addy']) {
	            	      	var val = place.name;
	            	      	document.getElementById('street_addy').value = val;
	            	      }

	            	      // Add Latitude and Longitude to hidden fields
	            	      if (componentForm['lat']) {
	            	      	var val = place.geometry.location.lat();
	            	      	document.getElementById('lat').value = val;

	            	      }
	            	      if (componentForm['lng']) {
	            	      	var val = place.geometry.location.lng();
	            	      	document.getElementById('lng').value = val;

	            	      }
	            	    }

	            	    // Bias the autocomplete object to the user's geographical location,
	            	    // as supplied by the browser's 'navigator.geolocation' object.
	            	    function geolocate() {
	            	      if (navigator.geolocation) {
	            	        navigator.geolocation.getCurrentPosition(function(position) {
	            	          var geolocation = {
	            	            lat: position.coords.latitude,
	            	            lng: position.coords.longitude
	            	          };
	            	          var circle = new google.maps.Circle({
	            	            center: geolocation,
	            	            radius: position.coords.accuracy
	            	          });
	            	          autocomplete.setBounds(circle.getBounds());
	            	        });
	            	      }
	            	    }
	            	  </script>

<?php
	            	break;
				} //end switch  
	    echo '</td></tr>';  
	} // end foreach  
	echo '</table>'; // end table  
	}  

	// Save the Data  
	function google_mapit_save_location_meta( $post_id ) {  

	  $meta_fields = $this->location_meta_fields;  

	  // verify nonce  
	  if ( !isset( $_POST['wpbs_nonce'] ) || !wp_verify_nonce($_POST['wpbs_nonce'], basename(__FILE__)) )  
	      return $post_id;

	  // check autosave
	  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	      return $post_id;

	  // check permissions
	  if ( 'page' == $_POST['post_type'] ) {
	      if ( !current_user_can( 'edit_page', $post_id ) )
	          return $post_id;
	      } elseif ( !current_user_can( 'edit_post', $post_id ) ) {
	          return $post_id;
	  }

	  // loop through fields and save the data  
	  foreach ( $meta_fields as $field ) {
	      $old = get_post_meta( $post_id, $field['name'], true );
	      $new = $_POST[$field['name']];

	      if ($new && $new != $old) {
	          update_post_meta( $post_id, $field['name'], $new );
	      } elseif ( '' == $new && $old ) {
	          delete_post_meta( $post_id, $field['name'], $old );
	      }
	  } // end foreach
	}

	function location_category_register_meta() {

		$args = array(
		    'sanitize_callback' => '',
		    'auth_callback' => '',
		    'type' => 'string',
		    'description' => 'Custom icon for category',
		    'single' => true,
		    'show_in_rest' => true,
		);

	    register_meta( 'term', 'custom_icon', $args );
	}

	// Add Icon field for the locations category
	function google_mapit_add_location_category_icon( $term_id) {  

	global $wp_query;

    if (is_category()) {
        $term_id = get_query_var('cat');
    } elseif (is_tax()) {
        $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $term_id = $current_term->term_id;
    }
    $term_id = $term_id->term_id;

	$meta_fields = $this->location_category_meta_fields; 

	// Use nonce for verification
	wp_nonce_field( basename( __FILE__ ), 'category_icon_nonce' );

	// Begin the field table and loop
	echo '<table class="form-table">';

	foreach ( $meta_fields as $field ) {
	    // get value of this field if it exists for this post  
	    $meta = get_term_meta($term_id, $field['name'], true);  
	    // begin a table row with  
	    echo '<tr> 
	            <th><label for="'.$field['id'].'">'.$field['label'].'</label></th> 
	            <td>';  
	            switch($field['type']) {  
	                // text  
	                case 'text':  
	                    echo '<input type="text" class="field" name="'.$field['name'].'" id="'.$field['id'].'" value="'.$meta.'" size="60" /> 
	                        <br /><span class="description">'.$field['desc'].'</span>';  
	                break;
	                
	                // textarea  
	                case 'textarea':  
	                    echo '<textarea name="'.$field['name'].'" id="'.$field['id'].'" cols="80" rows="4">'.$meta.'</textarea> 
	                        <br /><span class="description">'.$field['desc'].'</span>';  
	                break;  

	                case 'image':
	                ?>
	                <?php // Get WordPress' media upload URL
	                //$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

	                // See if there's a media id already saved as post meta
	                $img_id = get_term_meta( $term_id, $field['id'], true );

	                // Get the image src
	                $img_src = wp_get_attachment_image_src( $img_id, 'full' );

	                // For convenience, see if the array is valid
	                $has_img = is_array( $img_src ); ?>
	                  <div>
	                      <div style="width: 100%;clear:both;" class="preview meta-img-container">    
	                      <?php 
	                      $btntext = '';

	                      if ( $has_img ) : 
	                        $btntext = "Change Image"; ?>
	                      <img style="width:40px;height:40px;" src="<?php echo $img_src[0] ?>" alt="" style="max-width:100%;" />
	                      <?php else :
	                        $btntext = "Add Image";
	                       ?>
	                        
	                      <?php endif; ?>
	                      </div>
	                      <input type="button" name="upload-btn" class="upload-meta-img button-secondary upload-media" value="<?php echo $btntext; ?>">

	                      <!-- A hidden input to set and post the chosen image id -->
	                      <input class="meta-img-id" name="<?php echo $field['id']; ?>" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />

	                  </div>
	                <?php  
	                break;

				} //end switch  
	    echo '</td></tr>';  
	} // end foreach  
	echo '</table>'; // end table  
	}  

	function google_mapit_save_location_category_icon( $term_id ) {

	    if ( ! isset( $_POST['category_icon_nonce'] ) || ! wp_verify_nonce( $_POST['category_icon_nonce'], basename( __FILE__ ) ) )
	        return;

	    $old_color = get_term_meta($term_id, $prefix . 'custom_icon' );
	    $new_color = isset( $_POST[$prefix . 'custom_icon'] ) ? $_POST[$prefix . 'custom_icon'] : '';

	    if ( $old_color && '' === $new_color )
	        delete_term_meta( $term_id, $prefix . 'custom_icon' );

	    else if ( $old_color !== $new_color )
	        update_term_meta( $term_id, $prefix . 'custom_icon', $new_color );
	}

}
