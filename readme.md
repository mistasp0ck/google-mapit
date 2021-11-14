Google MapIt for Wordpress
=================================

This plugin extends the [jQuery Maps Locator Plugin](https://github.com/bjorn2404/jQuery-Store-Locator-Plugin) created by [Bjorn Holine](http://www.bjornblog.com/).  Adds the ability to add interactive google maps via the shortcode `[map]` and add Locations to the map.  Locations can be categorized allowing you to make many different map variations to display.

Shortcode Options
-----------------
Code Example  | Options
------------- | -------------
`[map search="true"]`  | `true,false`
`[map sidebar="true"]`  | `true,false`
`[map width="300"]`  | Any numeric value (px)
`[map height="300"]`  | Any numeric value (px)
`[map full_width="true"]`  | `true,false` **Note:** this will override the map `width`
`[map categories="category-name"]`  | Use the Category Slug
`[map orderby="menu_order"]`  | [WP Codex](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters)
`[map order="ASC"]`  | `ASC`,`DESC`
`[map ids="1,4,28"]`  | Add individual locations based in `ID`
`[map posts_per_page="5"]`  | Limit number of locations (default: `-1` <-no limit)
`[map zoom="12"]`  | Zoom level can only be set when a single location is displayed by the `ids` option.  To manipulate the zoom level with multiple locations, you will need to use `bounds_padding`
`[map bounds_padding="100,100,100,100"]` | [https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds](https://developers.google.com/maps/documentation/javascript/reference/map#Map.fitBounds) uses the padding field for fitBounds

Shortcode defaults
------------------
```		'title' => '',
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
			'expanded_height' => ''  ```


Known Issues
------------

- Default location autofill doesn't work on an unsecure site


Changelog
---------

- 1.2.0: Added multiple features
 - zoom, ids, max_zoom, bounds_padding, expanded height
 - split plugin settings into 3 pages ( General Options, Location Options, and Design Options)

- 1.1.0: Added [Rest Api](https://developer.wordpress.org/rest-api/) functionality for the locations post type
- 1.1.0: Added ability to load locations from an external site using the same plugin