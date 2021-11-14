var $ = jQuery.noConflict();
$(document).ready(function() {

	var locations = [];

	(function(window){
		window.htmlentities = {
			/**
			 * Converts a string to its html characters completely.
			 *
			 * @param {String} str String with unescaped HTML characters
			 **/
			encode : function(str) {
				var buf = [];
				
				for (var i=str.length-1;i>=0;i--) {
					buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
				}
				
				return buf.join('');
			},
			/**
			 * Converts an html characterSet into its original character.
			 *
			 * @param {String} str htmlSet entities
			 **/
			decode : function(str) {
				return str.replace(/&#(\d+);/g, function(match, dec) {
					return String.fromCharCode(dec);
				});
			}
		};
	})(window);

	var initMap = function () {
			var $resultsList = $('.bh-sl-loc-list'); 
			var $mapheight = $('#bh-sl-map').height();
			var $catFilters = $('.bh-sl-filters-container');
			var $addressInput = $('#bh-sl-address');
			var $mapForm = $('#bh-sl-user-location');
			var $mapTopContainer = $('.map-top-container');
			var $locButton = $('#use-location');
			var sidebar = '';
			var dim = '';
			var pagination = '';
			var mobile = false;
			var templateUrl = googleStorePluginUrl.url;
			var markerCluster;
			var defaultLoc = googleStorePluginUrl.settings.default_location;
			var fullMapStartBlank;
			var bounds_padding;
			var resultsHeight;
			var filters;

			if (googleStorePluginUrl.mapstyle != '') {
				var mapstyle = JSON.parse(googleStorePluginUrl.mapstyle);		
			} else {
				var	mapstyle = [{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]
			}

			if (googleStorePluginUrl.settings != '' && googleStorePluginUrl.settings != null) {
				var settings = JSON.parse(googleStorePluginUrl.settings);		
			} 

			// Desktop @todo: add auto sizing for icon
			if (window.matchMedia("(min-width: 1024px)").matches) {
				pagination = false;
				dim = 20;
			//Tablet view
			} else if (window.matchMedia("(min-width: 768px)").matches) {
				pagination = false;
				dim = 15;
			//Mobile view
			} else {
				dim = 8;
				pagination = false;
				mobile = true;
				$('.bh-sl-pagination-container').css('display','none');
			}
			if (googleStorePluginUrl.iconsize != '' && googleStorePluginUrl.iconsize != null) {
				dim = googleStorePluginUrl.iconsize;		
			}
			if (googleStorePluginUrl.img != '' && googleStorePluginUrl.img != null) {
				var markerImage = googleStorePluginUrl.img;
				if (markerImage[0]) {
					markerImage = markerImage[0];
				}
			}
			// @todo: split into lat & lng
			if (defaultLoc != '' && defaultLoc != null) {
				defaultLoc = defaultLoc;		
			} else {
				defaultLoc = false;
			}

			if (settings.zoom != '' && settings.zoom != null) {
				zoom = parseInt(settings.zoom);		
			} else {
				zoom = 12;
			}
			if (settings.maxZoom != '' && settings.maxZoom != null) {
				maxZoom = parseInt(settings.maxZoom);		
			} else {
				maxZoom = 18;
			}

			if (settings.bounds_padding != '' && settings.bounds_padding != null) {
				bounds_padding = parseInt(settings.bounds_padding);		
			} else {
				bounds_padding = 18;
			}

			if (googleStorePluginUrl.cat_icons != '' && googleStorePluginUrl.cat_icons != null) {
				var cat_icons = googleStorePluginUrl.cat_icons;
			}

			if (settings.taxonomyFilters != '' && settings.taxonomyFilters != null) {

				taxonomyFilters = parseInt(settings.taxonomyFilters);		
				// category-filters-location-category

				

				// 'taxonomyFilters' : {
				// 	// 'category' : 'category-filters-container1',
				// 	// add available cats from json
				// 	'services' : 'category-filters-services',
				// 	'specialty-center' : 'category-filters-specialty-center',
				// 	'location-type' : 'category-filters-location-type'
				// },
			} else {
				taxonomyFilters = '';
			}

			if (googleStorePluginUrl.imgCluster != '' && googleStorePluginUrl.imgCluster != null) {
				var markerImageCluster = googleStorePluginUrl.imgCluster;
				if (markerImageCluster[0]) {

					// console.log('image cluster active!');
					markerImageCluster = markerImageCluster[0];
					markerCluster = {
						imagePath: markerImageCluster,
						imageExtension: '',
						maxZoom: 11,
						gridSize: 50,
						styles: [
									 {
										   textColor: 'white',
										   fontFamily: '"Roboto", sans-serif',
										   url: markerImageCluster,
										   height: 49,
										   width: 50,
										   textSize: 17,
										 },
										{
										   textColor: 'white',
										   fontFamily: '"Roboto", sans-serif',
										   url: markerImageCluster,
										   height: 49,
										   width: 50,
										   textSize: 17,
										 },
										{
										   textColor: 'white',
										   fontFamily: '"Roboto", sans-serif',
										   url: markerImageCluster,
										   height: 49,
										   width: 50,
										   textSize: 17,
										 }
										]
						
					};
				} else {
					markerCluster = null
				}
			}
			if (googleStorePluginUrl.cat_icons != '' && googleStorePluginUrl.cat_icons != null) {
				var cat_icons = googleStorePluginUrl.cat_icons;
			}

			// build taxonomyFilters setup
			taxonomyFilters = {
				// 'category' : 'category-filters-container1',
				// add available cats from json
				'services' : 'category-filters-services',
				'specialty-center' : 'category-filters-specialty-center',
				'location-type' : 'category-filters-location-type'
			};

			var storeLocator = $('#bh-sl-map').storeLocator({	
				'pagination': pagination,
				'locationsPerPage': 5,
				'storeLimit' : -1,
				'sortBy' : 'name',
				'fullMapStart': true,
				'listColor2' : '#FBFBFB',
				'catMarkers' : cat_icons,
				markerCluster: markerCluster,
				'sessionStorage' : true,
				'geocodeID' : 'use-location',
				'locationList' : 'results-wrapper',
				// 'autoComplete' : true,
				'taxonomyFilters' : taxonomyFilters,
				// exclusiveFiltering: true,
				// zoom issue seems to be with a single location
				'mapSettings' : {
					//effects zoom when searched
					zoom: zoom,
					maxZoom: maxZoom,
					disableDoubleClickZoom: true,
					scrollwheel: false,
					navigationControl: false,
					draggable: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP, styles : mapstyle,
				},	
				'markerImg' :  markerImage,
				'markerDim' : { height: dim, width: dim },
				//Keep this false
				'slideMap' : false,	
				'defaultLoc': true,	
				'openNearest' : false,
				'fitBoundsPadding' : bounds_padding,
				// 'defaultLat': '41.3202726',
				// 'defaultLng' : '-81.6866421',
				'dataType': 'json', 
				'dataRaw' : locations,
				'infowindowTemplatePath'    : templateUrl + '/js/plugins/storeLocator/templates/infowindow-description.html',
				'listTemplatePath'           : templateUrl + '/js/plugins/storeLocator/templates/location-list-description.html',
				callbackSuccess: function(mappingObject, originPoint, data, page){
					formInteractions();
					if (sidebar == 'active' && mobile == true) {
						console.log('map loaded');
					}
				},
				callbackNotify: function (notifyText){
					return false;
				}
			});

			$('[data-toggle="tooltip"]').tooltip();

				function formInteractions(trigger) {
					if(($addressInput.val() !== '' || trigger === 'all') && sidebar !== 'active'){

						if (window.matchMedia("(max-width: 767px)").matches) {
							$('#bh-sl-map').css('display','none');
							$('.bh-sl-loc-list').css('position','relative');
						}
						if ($mapTopContainer.attr('data-expanded-height') && $mapTopContainer.attr('data-expanded-height') !== '') {
							// if initial height change map height
							$mapheight = $mapTopContainer.attr('data-expanded-height');
							// console.log($mapheight);	

								var scrollAdjust = 0;
								var topOffset = $('#bh-sl-map').offset().top + scrollAdjust;

							  if (document.getElementById('wpadminbar')) {
							    var adminOffset = -32;
							  } else {
							    var adminOffset = 0;
							  }
							  $('html, body').animate({
							    scrollTop: topOffset + adminOffset
							  }, 300, function() {
							    $('#bh-sl-map').addClass('complete');
							    $('.map-overlay').css('display','none');
							  });
						}



						$resultsList.css('opacity', '1');
						$resultsList.css('visibility', 'visible');
									
						//Move form and toggle Filter List
						$mapForm.detach();
						$locButton.detach();
						$('.bh-sl-loc-list').prepend($mapForm);
						$(".search-title").css('display','block');
						$('.bh-sl-container').addClass('sidebar');
						if( !$('body').hasClass('single')) {
							$(".bh-sl-map-container").css('padding-left', '376px');
						}

						// height of sidebar
						$resultsList.css('height', $mapheight+'px');
						$('#bh-sl-map').css('height', $mapheight+'px');
						// height of list
						$('.bh-sl-loc-list ul').css('max-height', $mapheight - 165);
						var listheight = $('.bh-sl-loc-list ul').height();
						$('.form-input').prepend($locButton);

						// $catFilters.toggle();
						$('.bh-sl-form-container').toggle();
						$('.bh-sl-pagination-container').toggle();
						resultsHeight = $('.bh-sl-container .list').css('max-height');
						// console.log('resultsHeight ' + resultsHeight);	
						sidebar = 'active';
						$(".toggle-sidebar i").addClass("fa-times");
					}
					if (sidebar === 'active' && trigger === 'sidebar') {
				        $resultsList.addClass("closed");
			        	$(".toggle-sidebar i").addClass("fa-times");
				        $(".toggle-sidebar i").removeClass("fa-map-marker-alt");

				        sidebar = 'inactive';
					} else if (sidebar === 'inactive' && trigger === 'sidebar') {
			        	$resultsList.removeClass("closed");
				        sidebar = 'active';		
				        $(".toggle-sidebar i").removeClass("fa-times");
				        $(".toggle-sidebar i").addClass("fa-map-marker-alt");
					}
				}

				$(".toggle-sidebar").toggle(
					function () {
						$resultsList.addClass("closed");
						$(".toggle-sidebar i").removeClass("fa-times");
						$(".toggle-sidebar i").addClass("fa-map-marker-alt");
						sidebar = 'inactive';	
						if( !$('body').hasClass('single')) {
							$(".bh-sl-map-container").css('padding-left', '0');
						}
					},
					function () {
						$resultsList.removeClass("closed");
						$(".toggle-sidebar i").addClass("fa-times");
						$(".toggle-sidebar i").removeClass("fa-map-marker-alt");
						sidebar = 'active';
						if( !$('body').hasClass('single')) {
							// offset fix for centering selected points
							$(".bh-sl-map-container").css('padding-left', '376px');
						}
				});

			    $("#use-location").click(function () {
			        formInteractions('all');        
			    });

			    $(".toggle-filters").toggle(
			    	function () {
			    		$(this).parent().removeClass('closed');
			    		$(".toggle-filters i").removeClass("fa-chevron-down");
			    		$(".toggle-filters i").addClass("fa-chevron-up");
			    		var filterHeight = $('.bh-sl-filters-container').innerHeight();	
			    		// console.log('filterHeight ' + filterHeight);
			    					
			    		var resultsintHeight = parseInt(resultsHeight, 10);
			    		newHeight = (resultsintHeight - filterHeight + 55);
			    		$('.bh-sl-container .list').css('max-height', newHeight + 'px');
			    		// console.log(newHeight);
			    	},
			    	function () {
			    		$(this).parent().addClass('closed');
			    		$(".toggle-filters i").addClass("fa-chevron-down");
			    		$(".toggle-filters i").removeClass("fa-chevron-up");
			    		$('.bh-sl-container .results-wrapper,.bh-sl-container .list').css('max-height', resultsHeight);                  
			    	});


				$('.search-link').click(function(event){
					event.preventDefault();
					formInteractions('all');
				});

				$('.search-link-sidebar').click(function(event){
						event.preventDefault();
						$catFilters.find('input[type="checkbox"]').each(function(){
							$(this).attr('checked', false);
						}).change();
				});

				// if (window.matchMedia("(min-width: 768px)").matches) {
				  //get the content area's width limit
				  var bodyWidth = $(".js-force-full-width").parent().width();
				  //get the window's width
				  var windowWidth =$(window).width();

				  //set the full width div's width to the body's width, which is always full screen width
				  $(".js-force-full-width").css({"width": $("body").width() + "px"});
				  //set all full width div's children's width to 100%
				  $(".js-force-full-width").children().css({"width":"100%"});

				  //setting margin for aligning full width div to the left
				  //only needed when content area width is smaller than screen width
				  if(windowWidth>bodyWidth){
				    var marginLeft = -(windowWidth - bodyWidth)/2;

				    $(".js-force-full-width").css({"margin-left": marginLeft+"px"});
				  }

				  // handling changing screen size
				  $(window).resize( function(){
				      $(".js-force-full-width").css({"width": $("body").width() + "px"});
				      if(windowWidth>bodyWidth){
				          $(".js-force-full-width").css({"margin-left": (-($(window).width() - $(".js-force-full-width").parent().width())/2)+"px"});
				      } else{
				          $(".js-force-full-width").css({"margin-left": "0px"});
				      }
				  });
		
			}

	var getCategories = function (taxonomy,callback) {

		console.log("Rest Url Enabled");
		$.ajax( {
		    url: googleStorePluginUrl.api_url + 'wp-json/wp/v2/' + taxonomy,
		    method: 'GET',
		} )
		.fail(function( jqXHR, textStatus ) {
		  console.log( "Category Request failed: " + textStatus );
		  // don't load map add php var to prevent map from loading and print message

		})
		// .done( callback );
	}

	var getLocations = function (query, callback) {
		$.ajax( {
		    url: query,
		    method: 'GET'
		} )
		.fail(function( jqXHR, textStatus ) {
		  console.log( "Location Request failed: " + textStatus );
		  $('.bh-sl-form-container').html('<p>Unable to find Map Locations, please check your plugin settings.</p>');
		  $('.map-top-container').addClass('error');
		  // don't load map add php var to prevent map from loading and print message

		})
		// .done( callback );
	}

	var locationObj = JSON.parse(googleStorePluginUrl.locations);


	if (!locationObj.hasOwnProperty('settings')) {	
		// Rest Url Disabled
		// locationObj.pop();
		locations = googleStorePluginUrl.locations;	
		console.log(locations);
		initMap();

	} else {

		var taxonomy = 'location-category';
		var categoryNames = [];
		var cats = $.ajax({
				url: googleStorePluginUrl.api_url + 'wp-json/wp/v2/' + taxonomy,
				method: 'GET',
			}),
			locs = cats.then(function(data) {
			// .then() returns a new promise

			var query = loadCategories(data);

			console.log('return in ajax: ' + query);

			return $.ajax({
					url: query,
					method: 'GET'
				});
			});

			locs.done(function(data) {
				console.log(data);
				loadLocations(data);
			});


		var loadCategories = function(cats) {

			// Rest Url Enabled
			var query = googleStorePluginUrl.api_url + 'wp-json/wp/v2/location';

			var catIds;

			// get category settings from shortcode
			if (locationObj.settings.hasOwnProperty('categories')) {

				var categoryIds = [];
				var categories = locationObj.settings.categories;	
				categories = categories.split(',');
				var catQuery = [];
			} else {
				var categories = '';
			}	
			
			console.log(cats);

			if (cats) {
				var html = '';
				var catIds = [];
				for (var i = 0; i < cats.length; i++) {

					categoryNames[cats[i]['id']] = cats[i]['name'];
					// if category name matches add id to query var

					if (categories !== '') {
						if (categories.includes(cats[i]['name'])) {

							console.log('category found: ' + cats[i]['id']);
							catIds.push(cats[i]['id']);
							// html += '<li>' + cats[i]['name'] + '</li>';
							html += '<li><input type="checkbox" name="category" value=' + cats[i]['name'] + '/><label>' + cats[i]['name'] + '</label></li>';
						}
					} else {
						html += '<li><input type="checkbox" name="category" value=' + cats[i]['name'] + '/><label>' + cats[i]['name'] + '</label></li>';
					}

				}
				// add filter list to shortcode
				$('#category-filters-container1').html(html);

				// if (catIds !== '') {
				if (catIds[0] != null) {	
					console.log('meows found: ' + catIds);
					catIds.join(',');
					var catQuery = '?' + taxonomy + '=' + catIds;
					query = query + catQuery;
				} 

				console.log('return query: ' + query);
			}

			return query;
		}

		var loadLocations = function(data) {
			for (var i = 0; i < data.length; i++) {

				if (data[i].location_meta_fields.hasOwnProperty('address')) {
					var address = data[i].location_meta_fields.address[0];
				} else {
					var address = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('address2')) {
					var address2 = data[i].location_meta_fields.address2[0];
				} else {
					var address2 = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('city')) {
					var city = data[i].location_meta_fields.city[0];
				} else {
					var city = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('state')) {
					var state = data[i].location_meta_fields.state[0];
				} else {
					var state = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('postal')) {
					var postal = data[i].location_meta_fields.postal[0];
				} else {
					var postal = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('latitude')) {
					var lat = data[i].location_meta_fields.latitude[0];
				} else {
					var lat = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('longitude')) {
					var lng = data[i].location_meta_fields.longitude[0];
				} else {
					var lng = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('link')) {
					var web = data[i].location_meta_fields.link[0];
				} else {
					var web = '';
				}

				if (data[i].location_meta_fields.hasOwnProperty('custom_icon')) {
					var custom_icon = data[i].location_meta_fields.custom_icon;
				} else {
					var custom_icon = '';
				}	

				if (data[i].location_meta_fields.hasOwnProperty('phone')) {
					var phone = data[i].location_meta_fields.phone;
				} else {
					var phone = '';
				}

				if (data[i].hasOwnProperty('location-category')) {
					var locCats = data[i]['location-category'];
					
					if (locCats.length === 1) {							
						console.log()
						var category = categoryNames[data[i]['location-category'][0]];
					} else if (locCats.length > 1) {
						var category = [];
						for (var i = 0; i < locCats.length; i++) {
							category.push(categoryNames[locCats[i]]);	
						}
						category.join(',');
					} else {
						var category = '';
					}

					console.log(category);

				} else {
					var category = '';
				}

				// global/shortcode settings
				if (locationObj.settings.hasOwnProperty('info_link')) {
					var info_link = locationObj.settings.info_link;
				} else {
					var info_link = '';
				}

				if (locationObj.settings.hasOwnProperty('info_link_text')) {
					var info_link_text = locationObj.settings.info_link_text;
				} else {
					var info_link_text = '';
				}

				if (locationObj.settings.hasOwnProperty('info_directions')) {
					var info_directions = locationObj.settings.info_directions;
				} else {
					var info_directions = '';
				}

				if (locationObj.settings.hasOwnProperty('list_directions')) {
					var list_directions = locationObj.settings.list_directions;
				} else {
					var list_directions = '';
				}

				if (locationObj.settings.hasOwnProperty('list_link')) {
					var list_link = locationObj.settings.list_link;
				} else {
					var list_link = '';
				}
				
				if (locationObj.settings.hasOwnProperty('list_link_text')) {
					var list_link_text = locationObj.settings.list_link_text;
				} else {
					var list_link_text = '';
				}

				locations.push({
									id: i,
									name: htmlentities.decode(data[i].title.rendered),
									address: address,
									address2: address2,
									city: city,
									state: state,
									postal: postal,
									lat: lat,
									lng: lng,
									web: web,
									// add global settings
									info_link: info_link,
									info_link_text: info_link_text,
									info_directions: info_directions,
									list_directions: list_directions,
									list_link: list_link,
									list_link_text: list_link_text,
									custom_icon: custom_icon,
									category: category,
									phone: phone,
									hours1: '',
									hours2: ''
								});

			} // end for loop
			locations = JSON.stringify(locations,null);
			initMap();

		}; // end getLocations



		// categories="test1,corporate" 



		// var getCategoriesRequest = getCategories(taxonomy, function(cats) {
		// 	var categoryNames = [];
		// 	// Rest Url Enabled
		// 	var query = googleStorePluginUrl.api_url + 'wp-json/wp/v2/location';

		// 	var catIds;

		// 	// get category settings from shortcode
		// 	if (locationObj.settings.hasOwnProperty('categories')) {

		// 		var categoryIds = [];
		// 		var categories = locationObj.settings.categories;	
		// 		categories = categories.split(',');
		// 		var catQuery = [];
		// 	} else {
		// 		var categories = '';
		// 	}	
			
		// 	console.log(categories);

		// 	if (cats) {
		// 		var html = '';

		// 		for (var i = 0; i < cats.length; i++) {

		// 			categoryNames[cats[i]['id']] = cats[i]['name'];
		// 			// if category name matches add id to query var

		// 			if (categories !== '') {
		// 				if (categories.includes(cats[i]['name'])) {
		// 					var catIds = [];
		// 					console.log('category found');
		// 					catIds.push(cats[i]['id']);
		// 					// html += '<li>' + cats[i]['name'] + '</li>';
		// 					html += '<li><input type="checkbox" name="category" value=' + cats[i]['name'] + '/><label>' + cats[i]['name'] + '</label></li>';
		// 				}
		// 			} else {
		// 				html += '<li><input type="checkbox" name="category" value=' + cats[i]['name'] + '/><label>' + cats[i]['name'] + '</label></li>';
		// 			}

		// 		}
		// 		// add filter list to shortcode
		// 		$('#category-filters-container1').html(html);

		// 		if (catIds) {
		// 			catIds.join(',');
		// 			var catQuery = '?' + taxonomy + '=' + catIds;
		// 			query = query + catQuery;
		// 		} 

		// 		console.log(query);
		// 	}


	
		// Use $.when to check if both AJAX calls are successful
		// $.when(getOrder, getCustomerID).then(function(order, customer) {
		//     console.log(order.data);
		//     console.log(customer.data);
		// });

		// })

		// getCategoriesRequest.then(console.log('done')); // end getCategories

		// getLocations( query, function(data) {
		// 	console.log(data);
		// 	for (var i = 0; i < data.length; i++) {

		// 		if (data[i].location_meta_fields.hasOwnProperty('address')) {
		// 			var address = data[i].location_meta_fields.address[0];
		// 		} else {
		// 			var address = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('address2')) {
		// 			var address2 = data[i].location_meta_fields.address2[0];
		// 		} else {
		// 			var address2 = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('city')) {
		// 			var city = data[i].location_meta_fields.city[0];
		// 		} else {
		// 			var city = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('state')) {
		// 			var state = data[i].location_meta_fields.state[0];
		// 		} else {
		// 			var state = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('postal')) {
		// 			var postal = data[i].location_meta_fields.postal[0];
		// 		} else {
		// 			var postal = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('latitude')) {
		// 			var lat = data[i].location_meta_fields.latitude[0];
		// 		} else {
		// 			var lat = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('longitude')) {
		// 			var lng = data[i].location_meta_fields.longitude[0];
		// 		} else {
		// 			var lng = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('link')) {
		// 			var web = data[i].location_meta_fields.link[0];
		// 		} else {
		// 			var web = '';
		// 		}

		// 		if (data[i].location_meta_fields.hasOwnProperty('custom_icon')) {
		// 			var custom_icon = data[i].location_meta_fields.custom_icon;
		// 		} else {
		// 			var custom_icon = '';
		// 		}	

		// 		if (data[i].location_meta_fields.hasOwnProperty('phone')) {
		// 			var phone = data[i].location_meta_fields.phone;
		// 		} else {
		// 			var phone = '';
		// 		}

		// 		if (data[i].hasOwnProperty('location-category')) {
		// 			var locCats = data[i]['location-category'];
					
		// 			if (locCats.length === 1) {							
		// 				var category = categoryNames[data[i]['location-category'][0]];
		// 			} else {
		// 				var category = [];
		// 				for (var i = 0; i < locCats.length; i++) {
		// 					category.push(categoryNames[locCats[i]]);	
		// 				}
		// 				category.join(',');
		// 			}

		// 			console.log(category);

		// 		} else {
		// 			var category = '';
		// 		}

		// 		// global/shortcode settings
		// 		if (locationObj.settings.hasOwnProperty('info_link')) {
		// 			var info_link = locationObj.settings.info_link;
		// 		} else {
		// 			var info_link = '';
		// 		}

		// 		if (locationObj.settings.hasOwnProperty('info_link_text')) {
		// 			var info_link_text = locationObj.settings.info_link_text;
		// 		} else {
		// 			var info_link_text = '';
		// 		}

		// 		if (locationObj.settings.hasOwnProperty('info_directions')) {
		// 			var info_directions = locationObj.settings.info_directions;
		// 		} else {
		// 			var info_directions = '';
		// 		}

		// 		if (locationObj.settings.hasOwnProperty('list_directions')) {
		// 			var list_directions = locationObj.settings.list_directions;
		// 		} else {
		// 			var list_directions = '';
		// 		}

		// 		if (locationObj.settings.hasOwnProperty('list_link')) {
		// 			var list_link = locationObj.settings.list_link;
		// 		} else {
		// 			var list_link = '';
		// 		}
				
		// 		if (locationObj.settings.hasOwnProperty('list_link_text')) {
		// 			var list_link_text = locationObj.settings.list_link_text;
		// 		} else {
		// 			var list_link_text = '';
		// 		}

		// 		locations.push({
		// 							id: i,
		// 							name: htmlentities.decode(data[i].title.rendered),
		// 							address: address,
		// 							address2: address2,
		// 							city: city,
		// 							state: state,
		// 							postal: postal,
		// 							lat: lat,
		// 							lng: lng,
		// 							web: web,
		// 							// add global settings
		// 							info_link: info_link,
		// 							info_link_text: info_link_text,
		// 							info_directions: info_directions,
		// 							list_directions: list_directions,
		// 							list_link: list_link,
		// 							list_link_text: list_link_text,
		// 							custom_icon: custom_icon,
		// 							category: category,
		// 							phone: phone,
		// 							hours1: '',
		// 							hours2: ''
		// 						});

		// 	} // end for loop
		// 	locations = JSON.stringify(locations,null);
		// 	initMap();

		// }); // end getLocations


	}
});