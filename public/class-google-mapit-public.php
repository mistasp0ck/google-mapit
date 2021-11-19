<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       tonystaffiero.com
 * @since      1.0.0
 *
 * @package    Google_Mapit
 * @subpackage Google_Mapit/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Google_Mapit
 * @subpackage Google_Mapit/public
 * @author     Tony Staffiero <me@tonystaffiero.com>
 */
class Google_Mapit_Public {

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
	 * Attributs for [map] shortcode.
	 *
	 * @since    1.1.1
	 * @access   private
	 * @var      string    $atts    Attributes for [map] shortcode.
	 */
	private $atts;

	/**
	 * Settings Prefix
	 *
	 * @since    1.2
	 * @access   private
	 * @var      string    $atts    Settings Prefix.
	 */
	private $prefix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $prefix) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->atts = [];

		$prefix = 'gmi_'; 
		$this->prefix = $prefix;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/styles.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$prefix = $this->prefix;
		$api_key = get_option($prefix.'api_key');

		$google_maps_url = 'https://maps.googleapis.com/maps/api/js';

		$google_api_url = add_query_arg( array(
			'key' => $api_key,
			'libraries' => 'places',
			'region' => 'US',
		), $google_maps_url );
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

		wp_enqueue_script( 'handlebars', plugin_dir_url( __FILE__ ). 'js/libs/handlebars/handlebars-4.7.7.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'google-api', $google_api_url, '', $this->version, true );

		wp_enqueue_script( 'jquery-storelocator', plugin_dir_url( __FILE__ ) . 'js/plugins/storeLocator/jquery.storelocator.min.js', array( 'jquery','google-api' ), $this->version, true );

		wp_register_script( 'locator-map', plugin_dir_url( __FILE__ ) . 'js/locator-map.js', array('jquery', 'wp-api','google-api'), $this->version, true );



	}

	public function enqueue_footer_scripts() {

	}

	public function google_mapit_add_rest_meta() {
		register_rest_field( 'location', 'location_meta_fields', array(
			'get_callback' => array($this ,'get_post_meta_for_api'),
			'schema' => null,
		)
	);
	}

	/*************************************
	* method: shutdown()
	*
	* This is called whenever the WordPress shutdown action is called.
	*/
	function shutdown() {

		// If we rendered an SLPLUS shortcode...
		//
		if (defined('SLPLUS_SHORTCODE_RENDERED') && SLPLUS_SHORTCODE_RENDERED) {
			// Register Load JavaScript
			//

			wp_enqueue_script( 'locator-map', '', '', $this->version, true );

			// Force our scripts to load for badly behaved themes
			//
			wp_print_footer_scripts();
		}
	}

	public function get_post_meta_for_api( $object ) {
     //get the id of the post object array
		$post_id = $object['id'];

     //return the post meta
		return get_post_meta( $post_id );
	}

	public function google_mapit_shortcode($atts = [], $content = null, $tag = ''){ 
		// Lets get some variables into our script
  	//
		$prefix = $this->prefix;

		$button_color = get_option($prefix . 'button_color');
		$button_color_hover = get_option($prefix . 'button_color_hover');
		$button_text_color = get_option($prefix . 'button_text_color');
		$link_text_color = get_option($prefix . 'link_text_color');
		if (get_option(get_option($prefix . 'default_location'))) {
			$default_location = true;	
			$default_lat = get_option($prefix . 'default_lat');
			$default_lng = get_option($prefix . 'default_lng');
		} else {
			$default_location = false;
		}

		error_log($default_location);

		extract(shortcode_atts(array(	
			'title' => '',
			'search' => 'false',
			'sidebar' => 'false',
			'full_width' => 'false',
			'width' => '',
			'height' => '',
			'posts_per_page' => '-1',
			'categories' => '',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			// @since    1.2
			'ids' => '',
			'zoom' => '',
			'max_zoom' => '',
			'bounds_padding' => '',
			'expanded_height' => '',
		), $atts, 'map'));

		$this->atts = $atts;

		// Init js vars after $atts are loaded
		$vars = array( 'url' => plugin_dir_url( __FILE__ ), 
			'locations' => $this->get_map_locations(), 
			'settings' => $this->get_map_settings(), 
			'img' => wp_get_attachment_image_src( get_option($prefix . 'locator_default_icon'), 'thumbnail'),
			// 'imgCluster' => wp_get_attachment_image_src($this->options['google_mapit_text_cluster_icon'], 'thumbnail'),
			'cat_icons' => $this->get_location_categories(),
			'iconsize' => get_option($prefix . 'locator_icon_size'), 
			'mapstyle' => get_option($prefix . 'locator_map_style'),
			'api_url' => get_option($prefix . 'locator_api_url')
		);

		wp_localize_script( 'locator-map', 'googleStorePluginUrl', $vars );
		// Set our flag for later processing
		// of JavaScript files
		//
		if (!defined('SLPLUS_SHORTCODE_RENDERED')) {
			define('SLPLUS_SHORTCODE_RENDERED',true);
		}

		//defaults
		$optwidth = 'width: 100%;';
		$optheight = 'height: 459px;';

		// @todo add full_width override here google_mapit_full_width
		if ($option = get_option($prefix . 'locator_map_width')) {
			$optwidth = 'width: '.$option.';';
		}

		if ($option = get_option($prefix . 'locator_map_height')) {
			$optheight = 'height: '.$option.';';
		}
		// shortcode dimensions
		if (!empty($width)) {
			//default to px but allow px & % to pass in
			preg_match('/^.*%|^.*px/', $width, $matches);

			if (!empty($matches[0])) {
				$optwidth = 'width: '.$width.';';	
			} else {
				$optwidth = 'width: '.$width.'px;';				
			}

		}

		if (!empty($height)) {
			//default to px but allow px & % to pass in
			preg_match('/^.*%|^.*px/', $height, $matches);

			if (!empty($matches[0])) {
				$optheight = 'height: '.$height.';';	
			} else {
				$optheight = 'height: '.$height.'px;';				
			}		
		}

		if (!empty($expanded_height)) {


			$expanded_height = ' data-expanded-height="'.$expanded_height.'" ';
		}


		$stylesh = ' style="'.$optheight.'"';
		$styles = ' style="'.$optwidth.$optheight.'"';	

		ob_start(); ?>
		<?php 
		$output = '';
		$output .= '<style type="text/css">
		.form-input button,.bh-sl-form-container button {
			background-color: '.$button_color.';
			color: '.$button_text_color.';
		}
		.closed .toggle-sidebar i {
			color: '.$button_color.';
		}
		.form-input button:hover,.form-input button:active,.form-input button:focus, .bh-sl-form-container button:hover,
		.bh-sl-form-container button:hover, .bh-sl-form-container button:active, .bh-sl-form-container button:focus {
			background-color: '.$button_color_hover.';
		}
		.bh-sl-form-container .search-link {
			color: '.$link_text_color.';
		}
		</style>';

		$output .= '<div class="bh-sl-filters-container closed">
		<div class="toggle-filters"><span class="btn-primary btn-sm"><i class="fa fa-sliders-h"></i> Edit Filters <i class="fa fa-chevron-down"></i></span></div>
		<ul class="bh-sl-filter-heading">
		<li class="filter_heading">Filters</li>
		<li><a href="#" class="search-link-sidebar">View All</a></li>
		</ul>';
			// begin loop for $tax 
		if ($taxonomies = get_option( $prefix . 'taxonomies' )) {
			$i = 1;
			foreach ($taxonomies as $tax) {
					# code...
				$output .='<ul id="category-filters-'.$tax.'" class="bh-sl-filters ">

				';

				if ( $post_types = get_option( $prefix . 'post_types' ) ) {
					$post_type_count = count($post_types);
				} else { 	
					$post_type_count = 0;
				}

				if ( $taxonomies = get_option( $prefix . 'taxonomies' ) ) {
					$taxonomy_count = count($taxonomies);
				} else { 	
					$taxonomy_count = 0;
				}

					// if ((count($options['post_types']) + count($options['taxonomies'])) > 1) {
				if (($post_type_count + $taxonomy_count) > 1) {
						// add title with multiple categories
					$tax_obj = get_taxonomy( $tax );

						// get title from tax slug
					$output .='<li class="filter_title">'.$tax_obj->label.'</li>';
				}

				$args = array(
					'type'                     => 'location',		
					'category'                 => $categories,
					'orderby'                  => $orderby,
					'order'                    => $order,
					'hide_empty'               => 1,
					'hierarchical'             => 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
						// Add Loop for this filter for $tax
					'taxonomy'                 => $tax,
					'pad_counts'               => false 

				); 		
				if ($categories != '') {
					$cats = explode(',', $categories);
				} else {
					$cats = array();
				}

				$terms = get_categories( $args );	
					// error_log(print_r($terms,true));
				if ( $terms && !is_wp_error( $terms ) ) :

					foreach ( $terms as $term ) { 
						$custom_cat_icon = get_term_meta($term->term_id, 'custom_icon',true );
						if ($custom_cat_icon != '') {
								// just add categories regardless of taxonomy
							$cat_icon_url = wp_get_attachment_url($custom_cat_icon);
							$cat_icon = '<img src="'.$cat_icon_url.'" width="16" height="16" />';
						} else {
							$cat_icon = '';
						}
						if (in_array($term->slug, $cats) ) {
							$checked = ' checked="checked"';
						} else {
							$checked = ' ';
						}
						$output .='
						<li class="checkbox"><label><input type="checkbox" id="'.$term->slug.'" name="'.$tax.'" value="'.$term->slug.'"'.$checked.'/><label for="'.$term->slug.'"></label>'.$term->name.$cat_icon.'</label></li>';
					} 	

				endif;									
				$output .= '</ul>';
				$i++;
				}//end loop
			}


			// begin loop for linked $post_types 
			if (!empty($post_types) ) {
				$i = 1;
				foreach ($post_types as $post_type) {
					# code...
					$output .='<ul id="category-filters-'.$post_type.'" class="bh-sl-filters ">

					';
					
					if (($post_type_count && $taxonomy_count) > 1) {
						// add title with multiple categories
						$post_type_obj = get_post_type_object( $post_type );

						// get title from tax slug
						$output .='<li class="filter_title">'.$post_type_obj->labels->name.'</li>';
					}

					$enabled_posts = get_post_meta( get_the_ID(), $post_type, true );

					if ($post_type == 'services') {
						$ids = $options['service_filter'];
					} else {
						$ids = '';
					}

					$args = array(
						'post_type' => $post_type,
						'post__in' => $ids,
						'posts_per_page' => -1
					);

					$posts = get_posts( $args );
					if ( $posts ) :
						foreach ( $posts as $post ) {
							// print_r($post);

							if (is_array($enabled_posts) && in_array($post->ID, $enabled_posts) ) {
								$checked = ' checked="checked"';
							} else {
								$checked = ' ';
							}
							$output .='
							<li class="checkbox"><label><input type="checkbox" id="'.$post->ID.'" name="'.$post_type.'" value="'.$post->ID.'"'.$checked.'/><label for="'.$post->ID.'"></label>'.$post->post_title.'</label></li>';
						} 	

					endif;									
					$output .= '</ul>';
					$i++;
				}//end loop
			}
			$output .= '</div>';
			?>	

			<?php if ($full_width == 'true') { ?> 
				<div class="js-force-full-width map-top-container"<?php echo $expanded_height; ?>>
				<?php } else { ?>
					<div class="row map-top-container"<?php echo $styles; ?><?php echo $expanded_height; ?>>
						<div class="col-sm-12">
						<?php } ?> 	
						<?php if($title != '') { ?><h2 class="map-title"><?php echo $title; ?></h2><?php } ?>
						<div class="bh-sl-container">			<div class="map-overlay"></div>
						<?php if ($search == 'true') : ?>
							<div class="bh-sl-form-container">
								<form id="bh-sl-user-location" method="post" action="#">
									<div class="form-input">
										<div class="search-title">Enter your Location</div>
										<button id="bh-sl-submit" type="submit" value="Search" class="fa fa-search"></button>
										<div class="form-input-wrapper">
											<label for="bh-sl-address" class="bh-sl-address"></label>
											<div class="form-input-wrapper-inner">
												<button id="use-location" data-toggle="tooltip" data-placement="left" title="Use my Location"><i class="fa fa-map-marker-alt"></i></button>
												<input type="text" id="bh-sl-address" name="bh-sl-address" placeholder="Enter (city, state) or (zip)" />
											</div>
										</div>			
										<?php echo $output; ?>
									</div>
								</form>	
								<a href="#" class="search-link">View All Locations</a>		
							</div>

						<?php endif; ?>

						<div id="bh-sl-map-container" class="bh-sl-map-container map-container">
							<?php if ($sidebar == 'true' || $search == 'true') : ?>
								<div class="bh-sl-loc-list">
									<button class="toggle-sidebar"><i class="fa"></i></button>
									<div class="results-wrapper">
										<ul class="list">

										</ul>
									</div>  
								</div>
								<div class="bh-sl-pagination-container">
									<ol class="bh-sl-pagination"></ol>
								</div>
							<?php endif; ?>	
							<div id="bh-sl-map" class="bh-sl-map"<?php echo $stylesh; ?>></div>
						</div>
					</div>

					<?php if ($full_width == 'true') { ?> 
					</div>	
				<?php } else { ?>
				</div>			
			</div>	
		<?php } ?> 							 							    

		<?php
		return ob_get_clean();
	}

	function google_mapit_shortcode_init() {
		add_shortcode('map', array($this,'google_mapit_shortcode'));
	}

	function get_map_settings() {
		// get Atts from Shortcode
		$map_atts = $this->atts;
		$prefix = $this->prefix;

		$settings = array();
		// add global stuff here
		if ($option = get_option($prefix . 'default_location')) {
			$settings['default_location'] = true;
		}
		if ($option = get_option($prefix . 'default_lat')) {
			$settings['default_lat'] = $option;
		}

		if ($option = get_option($prefix . 'default_lng')) {
			$settings['default_lng'] = $option;
		}

		if(!empty($map_atts['search'])) {
			$settings['search'] = $map_atts['search'];
		}
		if(!empty($map_atts['sidebar'])) {
			$settings['sidebar'] = $map_atts['sidebar'];
		}

		if (!empty($map_atts['zoom'])) {
			$settings['zoom'] = $map_atts['zoom'];
		} else if ($option = get_option($prefix . 'zoom')){
			$settings['zoom'] = $option;
		} 
		
		if (!empty($map_atts['max_zoom'])) {
			$settings['maxZoom'] = $map_atts['max_zoom'];
		} else if ($option = get_option($prefix . 'max_zoom')){
			$settings['maxZoom'] = $option;
		}

		if (!empty($map_atts['max_zoom'])) {
			$settings['maxZoom'] = $map_atts['max_zoom'];
		} else if ($option = get_option($prefix . 'max_zoom')){
			$settings['maxZoom'] = $option;
		}

		if (!empty($map_atts['bounds_padding'])) {
			$padding =  explode(',', $map_atts['bounds_padding']);
			$settings['bounds_padding'] = $padding;
		} else if ($option = get_option($prefix . 'bounds_padding')){
			$padding =  explode(',', $option);
			$settings['bounds_padding'] = $padding;
		}

		if ( $post_types = get_option( $prefix . 'post_types' ) ) {
			$post_types = implode(',', $post_types);
		} 

		if ( $taxonomies = get_option( $prefix . 'taxonomies' ) ) {
			// error_log($taxonomies);
			$taxonomies = implode(',', $taxonomies);
		}
		if (!empty($post_types) && !empty($taxonomies)) {
			$settings['filters'] = $taxonomies . ',' . $post_types;
		} else {
			$settings['filters'] = $taxonomies;
		}

		//Output JSON
		$settings = json_encode($settings); 
		return $settings;
	}

	function get_location_categories() {

		global $wp_query;

		$prefix = $this->prefix;

		$cat_icons = array();

		if($options = get_option( $prefix . 'taxonomies' )) {
			foreach ($options['taxonomies'] as $tax) {
				# code...
				// error_log($tax);
				$terms = get_terms( $tax);
				
				foreach ($terms as $term) {
					// error_log(print_r($term,true));
					$custom_cat_icon = get_term_meta($term->term_id, 'custom_icon',true );
					if ($custom_cat_icon != '') {
						// just add categories regardless of taxonomy
						$cat_icons[$term->slug] = array(wp_get_attachment_url($custom_cat_icon),22,22);
					}
				}
			}	
		}
		return $cat_icons;
	}

	function get_map_locations() {

		global $wp_query;

		$locations = array();

		$args = array(
			'post_type' => 'location',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'posts_per_page' => -1
		);

		$prefix = $this->prefix;
		// get Atts from Shortcode
		$map_atts = $this->atts;

		if(isset($map_atts['categories'])) {
			$args['location-category'] = $map_atts['categories'];
			$locations['settings']['categories'] = $map_atts['categories'];
		}

		if(isset($map_atts['categories'])) {
			$args['location-category'] = $map_atts['categories'];
			$locations['settings']['categories'] = $map_atts['categories'];
		}
		if(isset($map_atts['orderby'])) {
			$args['orderby'] = $map_atts['orderby'];
			$locations['settings']['orderby'] = $map_atts['orderby'];
		}

		if(isset($map_atts['order'])) {
			$args['order'] = $map_atts['order'];
			$locations['settings']['order'] = $map_atts['order'];
		}

		if ($url = get_option($prefix . 'google_mapit_api_url')) {

			if (get_option($prefix . 'info_show_link')) {
				$locations['settings']['info_link'] = 1;
			} else {
				$locations['settings']['info_link'] = '';
			}

			if ($option = get_option($prefix . 'default_location')) {
				$locations['settings']['default_location'] = '';
			} else {
				$locations['settings']['default_location'] = $option;
			} 

			if ($option = get_option($prefix . 'info_show_directions')) {
				$locations['settings']['info_directions'] = 1;
			} else {
				$locations['settings']['info_directions'] = '';
			}

			if ($option = get_option($prefix . 'list_show_directions')) {
				$locations['settings']['list_directions'] = 1;
			} else {
				$locations['settings']['list_directions'] = '';
			}

			if ($option = get_option($prefix . 'info_link_text')) {
				$locations['settings']['info_link_text'] = '';
			} else {
				$locations['settings']['info_link_text'] = $option;
			} 

			if ($option = get_option($prefix . 'list_show_link')) {
				$locations['settings']['list_link'] = 1;
			} else {
				$locations['settings']['list_link'] = '';
			}

			if ($option = get_option($prefix . 'list_link_text')) {
				$locations['settings']['list_link_text'] = '';
			} else {
				$locations['settings']['list_link_text'] = $option;
			} 

			if (filter_var($url, FILTER_VALIDATE_URL)) {
				// rest call in js will return error if 404
				$locations['settings']['rest'] = true;
				$locations = json_encode($locations); 

			} else {
				// @todo: add error messaging instead of rendering map
				return;
			}

		} else {

			if(isset($map_atts['ids'])) {
				if (preg_match(',', $map_atts['ids'])) {
					explode(',', $map_atts['ids']);
					$args['post__in'] = $map_atts['ids'];
				} else {
					$args['p'] = $map_atts['ids'];
					$location['single'] = true;
				}
			}

			$the_query = new WP_Query( $args );	
			if ( $the_query->have_posts() ) :
				$i = 1;
				$location = array();
				$locations = array();
				if ($location['single'] == true) {
					$locations['single'] == 'true';
				}

				$count = $the_query->post_count;
				$prefix = $this->prefix;

				while ( $the_query->have_posts() ) : $the_query->the_post();

					$location['id'] = $i;
					$location['name'] = get_the_title();
					$address = get_post_meta( get_the_ID(), $prefix . 'address', true );
					// Check if the custom field has a value.
					if ( ! empty( $address ) ) {
						$location['address'] = $address;
					} else {
						$location['address'] = '';
					}

					$address2 = get_post_meta( get_the_ID(), $prefix . 'address2', true );
					// Check if the custom field has a value.
					if ( ! empty( $address2 ) ) {
						$location['address2'] = $address2;
					} else {
						$location['address2'] = '';
					}

					$city = get_post_meta( get_the_ID(), $prefix . 'city', true );
					// Check if the custom field has a value.
					if ( ! empty( $city ) ) {
						$location['city'] = $city;
					} else {
						$location['city'] = '';
					}

					$state = get_post_meta( get_the_ID(), $prefix . 'state', true );
					// Check if the custom field has a value.
					if ( ! empty( $state ) ) {
						$location['state'] = $state;
					} else {
						$location['state'] = '';
					}

					$postal = get_post_meta( get_the_ID(), $prefix . 'postal', true );
					// Check if the custom field has a value.
					if ( ! empty( $postal ) ) {
						$location['postal'] = $postal;
					} else {
						$location['postal'] = '';
					}						

					$latitude = get_post_meta( get_the_ID(), $prefix . 'lat', true );
					// Check if the custom field has a value.
					if ( ! empty( $latitude ) ) {
						$location['lat'] = $latitude;
					} else {
						$location['lat'] = '';
					}

					$longitude = get_post_meta( get_the_ID(), $prefix . 'lng', true );
					// Check if the custom field has a value.
					if ( ! empty( $longitude ) ) {
						$location['lng'] = $longitude;
					} else {
						$location['lng'] = '';
					}

					if (get_option($prefix . 'info_show_link')) {
						$location['info_link'] = 1;
					} else {
						$location['info_link'] = '';
					}

					if (get_option($prefix . 'info_show_directions')) {
						$location['info_directions'] = 1;
					} else {
						$location['info_directions'] = '';
					}

					if (get_option($prefix . 'list_show_directions')) {
						$location['list_directions'] = 1;
					} else {
						$location['list_directions'] = '';
					}

					if ($option = get_option($prefix . 'info_link_text')) {
						$location['info_link_text'] = '';
					} else {
						$location['info_link_text'] = $option;
					} 

					if (get_option($prefix . 'list_show_link')) {
						$location['list_link'] = 1;
					} else {
						$location['list_link'] = '';
					}

					if ($option = get_option($prefix . 'list_link_text')) {
						$location['list_link_text'] = '';
					} else {
						$location['list_link_text'] = $option;
					} 

					$web = get_post_meta( get_the_ID(), $prefix . 'link', true );
					// Check if the custom field has a value.
					if ( ! empty( $web )) {
						$location['web'] = $web;
					} else if (get_option($prefix . 'enable_single')) {
						$location['web'] = get_the_permalink();
					} else {
						$location['web'] = '';
					}
					// @todo: add this feature
					// $image = '';
					// if ( has_post_thumbnail() ) {
					//  $post_thumbnail_id = get_post_thumbnail_id(get_the_ID()); 
					//  $image = wp_get_attachment_image_src( $post_thumbnail_id, 'google_mapit_featured' );
					// }
					// // Check if the custom field has a value.
					// if ( ! empty( $image )) {
					//     $location['image'] = $image[0];
			  //   } else {
					// 	$location['image'] = '';
					// }

					$custom_icon = get_post_meta( get_the_ID(), 'custom_icon', true );

					if (isset($options['post_types']) && $options['post_types'] != '') {
						//@todo: add post type ordering
						array_reverse($options['post_types']);
						foreach ($options['post_types'] as $post_type) {

							$ids = get_post_meta( get_the_ID(), 'type_'.$post_type , true );
							// error_log('POSTS ********** for'.$post_type.': '.print_r($ids,true));
							if ( ! empty( $ids ) ) {

								//Name

								// get posts
								$args = array (
									'post__in' => $ids,
									'post_type' => $post_type,
									'posts_per_page' => -1
								);

								$posts = get_posts($args);
								// add array with id = key and value = title

								if (count($posts > 1)) {
									$posts_array = array();
									foreach ( $posts as $post ) {
									  	//@todo pass both id and title to reduce server requests
										$posts_array[] = $post->ID; 
									}
									$location[$post_type] = implode(', ', $posts_array);			
								} else {
									$location[$post_type] = $posts;
								}

							}
						}
					}
					if ($options['taxonomies'] != '') {
						foreach ($options['taxonomies'] as $tax) {
							$cat_slugs = '';
							$category_name = '';
							# code...
							// error_log('$tax : '. $tax);
							$terms = '';
							$terms = wp_get_post_terms( get_the_ID(), $tax);
							// error_log(print_r($terms,true));
							// Check if the custom field has a value.
							if ( ! empty( $custom_icon ) ) {
								$location['custom_icon'] = $custom_icon;
							} else if (! empty( $custom_cat_icon )) {
								$location['custom_icon'] = $custom_cat_icon;
							} else {
								$location['custom_icon'] = '';
							}
							
							if ( ! empty( $terms ) ) {

								// error_log(print_r($terms,true));
								if (count($terms) > 0) {
									$cat_slugs = array();
									$category_name = array();
									error_log(count($terms));
									foreach($terms as $term) {
										$cat_slugs[] = $term->slug;
										$category_name[] = $term->name;
											// error_log('$term->slug' . $term->slug);
									}	
									$location[$tax] = implode(', ', $cat_slugs);

									if($tax == 'location-type') {
										$location['category'] = implode(', ', $cat_slugs);		
										$location['category_name'] = implode(', ', $category_name);											
									}
									$cat_slugs = '';

								}
								
							} else {
								$location[$tax] = '';								
							}

							// error_log('$location['.$tax.']' . $location[$tax]);
						}
					} else {
						$cats = wp_get_post_terms( get_the_ID(), 'location-type');

						// Check if the custom field has a value.
						if ( ! empty( $custom_icon ) ) {
							$location['custom_icon'] = $custom_icon;
						} else if (! empty( $custom_cat_icon )) {
							$location['custom_icon'] = $custom_cat_icon;
						} else {
							$location['custom_icon'] = '';
						}
						
						if ( ! empty( $cats ) ) {
							if (count($cats->slug > 1)) {
								$categories = array();
								foreach($cats as $cat) {
									$categories[] = $cat->slug;
									$category_name[] = $cat->name;
								}	
								$location['category'] = implode(', ', $categories);		
								$location['category_name'] = $category_name[0];			
							} else {
								$location['category_name'] = $cats->name;
							}

						} 
					}
					
					$location['phone'] = '';
					$location['fax'] = '';
					$location['hours1'] = '';
					$location['hours2'] = '';		

					$phone = get_post_meta( get_the_ID(), $prefix . 'phone', true );
					// Check if the custom field has a value.
					if ( ! empty( $phone )) {
						$location['phone'] = $phone;
					} else {
						$location['phone'] = '';
					}

					$fax = get_post_meta( get_the_ID(), $prefix . 'fax', true );
					// Check if the custom field has a value.
					if ( ! empty( $fax )) {
						$location['fax'] = $fax;
					} else {
						$location['fax'] = '';
					}

					array_push($locations, $location);


					$i++;
				endwhile;
				wp_reset_postdata();
			endif;
			

				// error_log($locations);
			//Output JSON
			$locations = json_encode($locations); 

				error_log($locations);

		}	 
		return $locations;	
	}

}



