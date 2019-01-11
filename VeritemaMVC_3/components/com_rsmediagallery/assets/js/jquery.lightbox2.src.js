/**
 * Heavily modified for RSMediaGallery! by RSJoomla!
 * jQuery lightBox plugin
 * This jQuery plugin was inspired and based on Lightbox 2 by Lokesh Dhakar (http://www.huddletogether.com/projects/lightbox2/)
 * and adapted to me for use like a plugin from jQuery.
 * @name jquery-lightbox-0.5.js
 * @author Leandro Vieira Pinho - http://leandrovieira.com
 * @version 0.5
 * @date April 11, 2008
 * @category jQuery plugin
 * @copyright (c) 2008 Leandro Vieira Pinho (leandrovieira.com)
 * @license CCAttribution-ShareAlike 2.5 Brazil - http://creativecommons.org/licenses/by-sa/2.5/br/deed.en_US
 * @example Visit http://leandrovieira.com/projects/jquery/lightbox/ for more informations about this jQuery plugin
 */

// Offering a Custom Alias suport - More info: http://docs.jquery.com/Plugins/Authoring#Custom_Alias
(function($) {
	/**
	 * $ is an alias to jQuery object
	 *
	 */
	$.fn.lightBox = function(settings) {
		// Settings to configure the jQuery lightBox plugin how you like
		settings = jQuery.extend({
			overlayOpacity:			0.8,		// (integer) Opacity value to overlay; inform: 0.X. Where X are number from 0 to 9
			// Configuration related to navigation
			fixedNavigation:		false,		// (boolean) Boolean that informs if the navigation (next and prev button) will be fixed or not in the interface.
			// Configuration related to images
			imageCloseText:			'&laquo; back to gallery',
			// Configuration related to container image box
			containerBorderSize:	10,			// (integer) If you adjust the padding in the CSS for the container, #lightbox-container-image-box, you will need to update this value
			containerResizeSpeed:	200,		// (integer) Specify the resize duration of container image. These number are miliseconds. 400 is default.
			// Configuration related to keyboard navigation
			keyToClose:				'c',		// (string) (c = close) Letter to close the jQuery lightBox interface. Beyond this letter, the letter X and the SCAPE key is used to.
			keyToPrev:				'p',		// (string) (p = previous) Letter to show the previous image
			keyToNext:				'n',		// (string) (n = next) Letter to show the next image.
			// Don´t alter these variables in any way
			imageArray:				[],
			activeImage:			0,
			ajaxEndChecks:			0, // use this to end ajax checks (no more results). automatically set to 1 when ajaxFunction() no longer returns results
			ajaxFunction:			function(settings) { return false; },
			onImageLoad:			function(settings) { return false; },
			addImage:				function(settings, href, title, id, group) { if (title.length > 0 && title.charAt(0) == '#' && $(title).length > 0) title = $(title).html(); settings.imageArray.push([href, title, id, group]); }
		},settings);
		// Caching the jQuery object with all elements matched
		var jQueryMatchedObj = this; // This, in this context, refer to jQuery object
		/**
		 * Initializing the plugin calling the start function
		 *
		 * @return boolean false
		 */
		function _initialize() {
			_start(this,jQueryMatchedObj); // This, in this context, refer to object (link) which the user have clicked
			return false; // Avoid the browser following the link
		}
		/**
		 * Start the jQuery lightBox plugin
		 *
		 * @param object objClicked The object (link) whick the user have clicked
		 * @param object jQueryMatchedObj The jQuery object with all elements matched
		 */
		function _start(objClicked,jQueryMatchedObj) {
			// Hime some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
			$('embed, object, select').css({ 'visibility' : 'hidden' });
			// Call the function to create the markup structure; style some elements; assign events in some elements.
			_set_interface();
			// Unset total images in imageArray
			settings.imageArray.length = 0;
			// Unset image active information
			settings.activeImage = 0;
			// Reset ajax flag
			settings.ajaxEndChecks = 0;
			
			// We have an image set? Or just an image? Let´s see it.
			if ( jQueryMatchedObj.length == 1 ) {
				
				href 	= _get_full_image(objClicked);
				title 	= _get_title(objClicked);
				group 	= _get_group(objClicked);
				id		= _get_id(objClicked);
				
				settings.addImage(settings, href, title, id, group);
			} else {
				// Add an Array (as many as we have), with href and title atributes, inside the Array that storage the images references		
				for ( var i = 0; i < jQueryMatchedObj.length; i++ ) {					
					href 	= _get_full_image(jQueryMatchedObj[i]);
					title 	= _get_title(jQueryMatchedObj[i]);
					group 	= _get_group(jQueryMatchedObj[i]);
					id		= _get_id(jQueryMatchedObj[i]);
					
					if (href == _get_full_image(objClicked))
						settings.activeImage = i;
						
					settings.addImage(settings, href, title, id, group);
				}
			}
			
			// Call the function that prepares image exibition
			_set_image_to_view();
		}
		
		function _get_full_image(obj) {
			rel = $(obj).attr('rel');
			
			if (typeof rel != 'undefined' && rel.indexOf('{') > -1 && rel.indexOf('}') > -1)
			{
				eval('var decoded_rel = ' + rel + ';');
				if (typeof decoded_rel == 'object' && decoded_rel.link)
					return decoded_rel.link;
			}
			
			return '';
		}
		
		function _get_title(obj) {
			rel = $(obj).attr('rel');
			
			if (typeof rel != 'undefined' && rel.indexOf('{') > -1 && rel.indexOf('}') > -1)
			{
				eval('var decoded_rel = ' + rel + ';');
				if (typeof decoded_rel == 'object' && decoded_rel.title)
					return decoded_rel.title;
			}
			
			return '';
		}
		
		function _get_group(obj) {
			rel = $(obj).attr('rel');
			
			if (typeof rel != 'undefined' && rel.indexOf('{') > -1 && rel.indexOf('}') > -1)
			{
				eval('var decoded_rel = ' + rel + ';');
				if (typeof decoded_rel == 'object' && decoded_rel.group)
					return decoded_rel.group;
			}
			
			return '';			
		}
		
		function _get_id(obj) {
			rel = $(obj).attr('rel');
			
			if (typeof rel != 'undefined' && rel.indexOf('{') > -1 && rel.indexOf('}') > -1)
			{
				eval('var decoded_rel = ' + rel + ';');
				if (typeof decoded_rel == 'object' && decoded_rel.id)
					return decoded_rel.id;
			}
			
			return '';
		}
		
		/**
		 * Create the jQuery lightBox plugin interface
		 */
		function _set_interface() {
			// Apply the HTML markup into body tag
			$('body').append('<div id="jquery-overlay"></div><div id="jquery-lightbox"><div id="lightbox-container-navigation-box"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a><div id="lightbox-container-image-box"><div id="lightbox-container-image"><div id="lightbox-btnClose"><a href="#" id="lightbox-secNav-btnClose">' + settings.imageCloseText + '</a></div><img id="lightbox-image"><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"></a></div></div></div></div><div id="lightbox-container-image-data-box"><div id="lightbox-container-image-data"><div id="lightbox-image-details"><span id="lightbox-image-details-caption"></span></div></div></div></div>');	
			$('#lightbox-container-image').hover(
				function(e) {
					$('#lightbox-btnClose').fadeIn('fast');
				},
				function(e) {
					$('#lightbox-btnClose').fadeOut('fast');
				}
			);
			// Get page sizes
			var arrPageSizes = ___getPageSize();
			// Style overlay and show it
			$('#jquery-overlay').css({
				opacity:			settings.overlayOpacity,
				width:				arrPageSizes[0],
				height:				arrPageSizes[1]
			}).fadeIn();
			// Get page scroll
			var arrPageScroll = ___getPageScroll();
			// Calculate top and left offset for the jquery-lightbox div object and show it
			$('#jquery-lightbox').css({
				top:	arrPageScroll[1] + (arrPageSizes[3] / 10),
				left:	arrPageScroll[0]
			}).show();
			// Assigning click events in elements to close overlay
			$('#jquery-overlay,#jquery-lightbox').click(function(e) {
				if ($(e.target).parents('#lightbox-container-image-data-box').length == 0)
				_finish();									
			});
			// Assign the _finish function to lightbox-loading-link and lightbox-secNav-btnClose objects
			$('#lightbox-loading-link,#lightbox-btnClose').click(function() {
				_finish();
				return false;
			});
			// If window was resized, calculate the new overlay dimensions
			$(window).resize(_resize_overlay_with_scroll);
			$(window).scroll(_resize_overlay_without_scroll);
		}
		
		function _resize_overlay_with_scroll() {
			_resize_overlay();
			_position_lightbox();
			
			// resize image to fit screen
			var t = new Image();
			t.src = $('#lightbox-image').attr('src');
			
			if ((newsizes = _need_to_resize(t.width,t.height)) !== false)
			{
				_hide_elements();
				_resize_container_image_box(t.width, t.height);
			}
		}
		
		function _resize_overlay_without_scroll() {
			_resize_overlay();
		}
		
		function _resize_overlay() {
			// Get page sizes
			var arrPageSizes = ___getPageSize();
			// Style overlay and show it
			$('#jquery-overlay').css({
				width:		arrPageSizes[0],
				height:		arrPageSizes[1]
			});
		}
		
		function _position_lightbox() {
			// Get page sizes
			var arrPageSizes = ___getPageSize();
			// Get page scroll
			var arrPageScroll = ___getPageScroll();
			// Calculate top and left offset for the jquery-lightbox div object and show it
			$('#jquery-lightbox').css({
				top:	arrPageScroll[1] + (arrPageSizes[3] / 10),
				left:	arrPageScroll[0]
			});
		}
		
		function _hide_elements() {
			// Hide some elements
			$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-container-image-data-box').hide();
			$('#lightbox-btnClose').hide();
		}
		
		function _show_elements() {
			$('#lightbox-container-image-data-box').show();
			//$('#lightbox-container-image-data-box').slideDown('fast');
			$('#lightbox-btnClose').show();
		}
		
		/**
		 * Prepares image exibition; doing a image´s preloader to calculate it´s size
		 *
		 */
		function _set_image_to_view() { // show the loading
			// Show the loading
			$('#lightbox-loading').show();
			_hide_elements();
			
			// Image preload process
			var objImagePreloader = new Image();
			objImagePreloader.onload = function() {
			//alert('aici');
				$('#lightbox-image').attr('src',settings.imageArray[settings.activeImage][0]);
				$('#lightbox-image').attr('rel', settings.imageArray[settings.activeImage][2]);
				// Perfomance an effect in the image container resizing it
				_resize_container_image_box(objImagePreloader.width,objImagePreloader.height);
				
				// trigger our function
				settings.onImageLoad(settings);
				
				//	clear onLoad, IE behaves irratically with animated gifs otherwise
				objImagePreloader.onload=function(){};
			};
			objImagePreloader.src = settings.imageArray[settings.activeImage][0];
		};
		/**
		 * Perfomance an effect in the image container resizing it
		 *
		 * @param integer intImageWidth The image´s width that will be showed
		 * @param integer intImageHeight The image´s height that will be showed
		 */
		function _resize_container_image_box(intImageWidth,intImageHeight) {
			
			if ((newsizes = _need_to_resize(intImageWidth,intImageHeight)) !== false)
			{
				intImageWidth	= newsizes[0];
				intImageHeight 	= newsizes[1];
				
				$('#lightbox-image').css('width', '100%');
			}
			
			// Get the body width and his padding and margin and borders if exists
			var intBodyWidth 	= $('body').width();
			var paddingBody 	= $('body').innerWidth() - intBodyWidth;
			var marginBody 		= $('body').outerWidth(true) - $('body').outerWidth();
			
			intBodyWidth = intBodyWidth + paddingBody + marginBody;
			
			
			// Get current width and height
			var intCurrentWidth = $('#lightbox-container-image-box').width();
			var intCurrentHeight = $('#lightbox-container-image-box').height();
			// Get the width and height of the selected image plus the padding
			var intWidth = (intImageWidth + (settings.containerBorderSize * 2)); // Plus the image´s width and the left and right padding value
			var intHeight = (intImageHeight + (settings.containerBorderSize * 2)); // Plus the image´s height and the left and right padding value
			// Diferences
			var intDiffW = intCurrentWidth - intWidth;
			var intDiffH = intCurrentHeight - intHeight;
			
			// extra width for pushing away the buttons from the picture
			var extra = 100;
			var diff  = intBodyWidth - intWidth;
			
			if (diff < extra) {
				extra = extra - (extra - diff);
			}
			
			$('#lightbox-container-navigation-box').css('width', (intWidth + extra) + 'px');
			// Perfomance the effect
			$('#lightbox-container-image-box').animate({ width: intWidth, height: intHeight },settings.containerResizeSpeed,function() { _show_image(); });
			if ( ( intDiffW == 0 ) && ( intDiffH == 0 ) ) {
				if ( $.browser.msie ) {
					___pause(250);
				} else {
					___pause(100);	
				}
			} 
			$('#lightbox-container-image-data-box').css({ width: intImageWidth });
			
			var offset = $('#jquery-lightbox').offset()
			var arrPageSizes = ___getPageSize();			
			var left = offset.left;
			var top	 = offset.top - (arrPageSizes[3] / 10);
			
			//window.scroll(left, top);
			$(window).scrollTo({'top': top + 'px', 'left': left + 'px'}, 400);
		};
		
		function _need_to_resize(intImageWidth,intImageHeight) {
			// Get window width & resize accordingly
			var windowWidth = $(window).width();
			if (windowWidth <= (intImageWidth + 100))
			{
				var originalImageWidth 	= intImageWidth;
				var originalImageHeight = intImageHeight;
				var imageRatio = originalImageWidth / originalImageHeight;
				
				intImageWidth 	= Math.round(windowWidth * 0.80);
				intImageHeight 	= Math.round(intImageWidth / imageRatio);
				
				return [intImageWidth, intImageHeight];
			}
			
			return false;
		}
		
		function _preload_ajax_images() {
			// if we have defined an ajax call for loading images and the end checks flag isn't set
			if (typeof settings.ajaxFunction == 'function' && !settings.ajaxEndChecks)
			{				
				if ((settings.imageArray.length > 1 && settings.activeImage == ( settings.imageArray.length - 2 )) ||
					(settings.imageArray.length == 1) || 
					(settings.imageArray.length > 1 && settings.activeImage == ( settings.imageArray.length - 1 )))
					{
						if (!settings.ajaxFunction(settings))
						{
							settings.ajaxEndChecks = 1;
						}
					}
			}
		}
		
		/**
		 * Show the prepared image
		 *
		 */
		function _show_image() {
			_preload_ajax_images();
			
			$('#lightbox-loading').hide();
			$('#lightbox-image').fadeIn('fast', function() {
				_show_image_data();
				_set_navigation();
			});
			
			_preload_neighbor_images();
		};
		/**
		 * Show the image information
		 *
		 */
		function _show_image_data() {
			_show_elements();
			$('#lightbox-image-details-caption').hide();
			if ( settings.imageArray[settings.activeImage][1] ) {				
				$('#lightbox-image-details-caption').html(settings.imageArray[settings.activeImage][1]).show();
			}
		}
		/**
		 * Display the button navigations
		 *
		 */
		function _set_navigation() {
			// Instead to define this configuration in CSS file, we define here. And it´s need to IE. Just.
			$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').addClass('lightbox-nav-btnBlank');
			
			// Show the prev button, if not the first image in set
			var currentGroup = settings.imageArray[settings.activeImage][3];
			
			var prevLinkInfo = _get_previous_image(currentGroup);
			var nextLinkInfo = _get_next_image(currentGroup);
			
			if ( prevLinkInfo) {
				$('#lightbox-nav-btnPrev').removeClass('lightbox-nav-btnBlank')
					.unbind()
					.bind('click',function() {
						settings.activeImage = prevLinkInfo[1];
						_set_image_to_view();
						return false;
					})
					.show();
			}
			
			// Show the next button, if not the last image in set
			if ( nextLinkInfo ) {
				$('#lightbox-nav-btnNext').removeClass('lightbox-nav-btnBlank')
					.unbind()
					.bind('click',function() {
						settings.activeImage = nextLinkInfo[1];
						_set_image_to_view();
						return false;
					})
					.show();
			}
			// Enable keyboard navigation
			_enable_keyboard_navigation();
		}
		/**
		 * Enable a support to keyboard navigation
		 *
		 */
		function _enable_keyboard_navigation() {
			$(document).keydown(_keyboard_action);
		}
		/**
		 * Disable the support to keyboard navigation
		 *
		 */
		function _disable_keyboard_navigation() {
			$(document).unbind('keydown', _keyboard_action);
		}
		/**
		 * Perform the keyboard actions
		 *
		 */
		function _keyboard_action(objEvent) {			
			keycode 	= objEvent.keyCode;
			leftArrow 	= 37;
			rightArrow 	= 39;
			escapeKey 	= 27;
			
			// Get the key in lower case form
			key = String.fromCharCode(keycode).toLowerCase();
			// Verify the keys to close the ligthBox
			if ( ( key == settings.keyToClose ) || ( key == 'x' ) || ( keycode == escapeKey ) ) {
				_finish();
			}
			// Verify the key to show the previous image
			if ( ( key == settings.keyToPrev ) || ( keycode == leftArrow ) ) {
				// If we´re not showing the first image, call the previous
				if ( settings.activeImage != 0 ) {
					settings.activeImage = settings.activeImage - 1;
					_set_image_to_view();
					_disable_keyboard_navigation();
				}
			}
			// Verify the key to show the next image
			if ( ( key == settings.keyToNext ) || ( keycode == rightArrow ) ) {
				// If we´re not showing the last image, call the next
				if ( settings.activeImage != ( settings.imageArray.length - 1 ) ) {
					settings.activeImage = settings.activeImage + 1;
					_set_image_to_view();
					_disable_keyboard_navigation();
				}
			}
		}
		/**
		 * Preload prev and next images being showed
		 *
		 */
		function _preload_neighbor_images() {
			var currentGroup = settings.imageArray[settings.activeImage][3];
			
			var prevLinkInfo = _get_previous_image(currentGroup);
			var nextLinkInfo = _get_next_image(currentGroup);
			
			
			if (prevLinkInfo) {
				objNext = new Image();
				objNext.src = prevLinkInfo[0];
			}
			if ( nextLinkInfo) {
				objPrev = new Image();
				objPrev.src = nextLinkInfo[0];
			}
		}
		
		function _get_previous_image(group) {
			var link = false;
			var pos  = 0;
			for (var i=(settings.activeImage-1); i>=0; i--) {
				 if (settings.imageArray[i][3]==group) {
					if (link==false){
						link = settings.imageArray[i][0];
						pos = i;
					}
				 }
			}
			if (link) return [link,pos];
			else return link;
		}
		function _get_next_image(group) {
			var allImages = settings.imageArray.length;
			var link = false;
			var pos  = 0;
			
			for (var i=(settings.activeImage+1); i<allImages; i++) {
				 if (settings.imageArray[i][3]==group) {
					if (link==false) {
						link = settings.imageArray[i][0];
						pos = i;
					}
				 }
			}
			if (link) return [link,pos];
			else return link;
		}
		/**
		 * Remove jQuery lightBox plugin HTML markup
		 *
		 */
		function _finish() {
			$('#jquery-lightbox').remove();
			$('#jquery-overlay').fadeOut(function() { $('#jquery-overlay').remove(); });
			// Show some elements to avoid conflict with overlay in IE. These elements appear above the overlay.
			$('embed, object, select').css({ 'visibility' : 'visible' });
			
			$(window).unbind('resize', _resize_overlay_with_scroll);
			$(window).unbind('scroll', _resize_overlay_without_scroll);
			$(document).unbind('keydown', _keyboard_action);
		}
		/**
		 * getPageSize() by quirksmode.com - removed
		 * why not use jQuery ?
		 * 
		 * @return Array Return an array with page width, height and window width, height
		 */
		function ___getPageSize() {
			var xScroll, yScroll;
			if (window.innerHeight && window.scrollMaxY) {	
				xScroll = window.innerWidth + window.scrollMaxX;
				yScroll = window.innerHeight + window.scrollMaxY;
			} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
				xScroll = document.body.scrollWidth;
				yScroll = document.body.scrollHeight;
			} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
				xScroll = document.body.offsetWidth;
				yScroll = document.body.offsetHeight;
			}
			var windowWidth, windowHeight;
			if (self.innerHeight) {	// all except Explorer
				if(document.documentElement.clientWidth){
					windowWidth = document.documentElement.clientWidth; 
				} else {
					windowWidth = self.innerWidth;
				}
				windowHeight = self.innerHeight;
			} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
				windowWidth = document.documentElement.clientWidth;
				windowHeight = document.documentElement.clientHeight;
			} else if (document.body) { // other Explorers
				windowWidth = document.body.clientWidth;
				windowHeight = document.body.clientHeight;
			}	
			// for small pages with total height less then height of the viewport
			if(yScroll < windowHeight){
				pageHeight = windowHeight;
			} else { 
				pageHeight = yScroll;
			}
			// for small pages with total width less then width of the viewport
			if(xScroll < windowWidth){	
				pageWidth = xScroll;		
			} else {
				pageWidth = windowWidth;
			}
			arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight);
			return arrayPageSize;
			
			pageWidth 	 = $('body').outerWidth();
			pageHeight 	 = $(document).outerHeight();
			windowWidth  = $(window).outerWidth();
			windowHeight = $(window).outerHeight();
			
			return [pageWidth, pageHeight, windowWidth, windowHeight];
		};
		/**
		 * getPageScroll() by quirksmode.com - removed
		 * why not use jQuery ?
		 *
		 * @return Array Return an array with x,y page scroll values.
		 */
		function ___getPageScroll() {
		
			var xScroll = $(document).scrollLeft();
			var yScroll = $(document).scrollTop();
			
			return [xScroll, yScroll];
		};
		 /**
		  * Stop the code execution from a escified time in milisecond
		  *
		  */
		 function ___pause(ms) {
			var date = new Date(); 
			curDate = null;
			do { var curDate = new Date(); }
			while ( curDate - date < ms);
		 };
		// Return the jQuery object for chaining. The unbind method is used to avoid click conflict when the plugin is called more than once
		return this.unbind('click').click(_initialize);
	};
})(jQuery); // Call and execute the function immediately passing the jQuery object

(function( $ ){
	
	var $scrollTo = $.scrollTo = function( target, duration, settings ){
		$(window).scrollTo( target, duration, settings );
	};

	$scrollTo.defaults = {
		axis:'xy',
		duration: parseFloat($.fn.jquery) >= 1.3 ? 0 : 1
	};

	// Returns the element that needs to be animated to scroll the window.
	// Kept for backwards compatibility (specially for localScroll & serialScroll)
	$scrollTo.window = function( scope ){
		return $(window)._scrollable();
	};

	// Hack, hack, hack :)
	// Returns the real elements to scroll (supports window/iframes, documents and regular nodes)
	$.fn._scrollable = function(){
		return this.map(function(){
			var elem = this,
				isWin = !elem.nodeName || $.inArray( elem.nodeName.toLowerCase(), ['iframe','#document','html','body'] ) != -1;

				if( !isWin )
					return elem;

			var doc = (elem.contentWindow || elem).document || elem.ownerDocument || elem;
			
			return $.browser.safari || doc.compatMode == 'BackCompat' ?
				doc.body : 
				doc.documentElement;
		});
	};

	$.fn.scrollTo = function( target, duration, settings ){
		if( typeof duration == 'object' ){
			settings = duration;
			duration = 0;
		}
		if( typeof settings == 'function' )
			settings = { onAfter:settings };
			
		if( target == 'max' )
			target = 9e9;
			
		settings = $.extend( {}, $scrollTo.defaults, settings );
		// Speed is still recognized for backwards compatibility
		duration = duration || settings.speed || settings.duration;
		// Make sure the settings are given right
		settings.queue = settings.queue && settings.axis.length > 1;
		
		if( settings.queue )
			// Let's keep the overall duration
			duration /= 2;
		settings.offset = both( settings.offset );
		settings.over = both( settings.over );

		return this._scrollable().each(function(){
			var elem = this,
				$elem = $(elem),
				targ = target, toff, attr = {},
				win = $elem.is('html,body');

			switch( typeof targ ){
				// A number will pass the regex
				case 'number':
				case 'string':
					if( /^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(targ) ){
						targ = both( targ );
						// We are done
						break;
					}
					// Relative selector, no break!
					targ = $(targ,this);
				case 'object':
					// DOMElement / jQuery
					if( targ.is || targ.style )
						// Get the real position of the target 
						toff = (targ = $(targ)).offset();
			}
			$.each( settings.axis.split(''), function( i, axis ){
				var Pos	= axis == 'x' ? 'Left' : 'Top',
					pos = Pos.toLowerCase(),
					key = 'scroll' + Pos,
					old = elem[key],
					max = $scrollTo.max(elem, axis);

				if( toff ){// jQuery / DOMElement
					attr[key] = toff[pos] + ( win ? 0 : old - $elem.offset()[pos] );

					// If it's a dom element, reduce the margin
					if( settings.margin ){
						attr[key] -= parseInt(targ.css('margin'+Pos)) || 0;
						attr[key] -= parseInt(targ.css('border'+Pos+'Width')) || 0;
					}
					
					attr[key] += settings.offset[pos] || 0;
					
					if( settings.over[pos] )
						// Scroll to a fraction of its width/height
						attr[key] += targ[axis=='x'?'width':'height']() * settings.over[pos];
				}else{ 
					var val = targ[pos];
					// Handle percentage values
					attr[key] = val.slice && val.slice(-1) == '%' ? 
						parseFloat(val) / 100 * max
						: val;
				}

				// Number or 'number'
				if( /^\d+$/.test(attr[key]) )
					// Check the limits
					attr[key] = attr[key] <= 0 ? 0 : Math.min( attr[key], max );

				// Queueing axes
				if( !i && settings.queue ){
					// Don't waste time animating, if there's no need.
					if( old != attr[key] )
						// Intermediate animation
						animate( settings.onAfterFirst );
					// Don't animate this axis again in the next iteration.
					delete attr[key];
				}
			});

			animate( settings.onAfter );			

			function animate( callback ){
				$elem.animate( attr, duration, settings.easing, callback && function(){
					callback.call(this, target, settings);
				});
			};

		}).end();
	};
	
	// Max scrolling position, works on quirks mode
	// It only fails (not too badly) on IE, quirks mode.
	$scrollTo.max = function( elem, axis ){
		var Dim = axis == 'x' ? 'Width' : 'Height',
			scroll = 'scroll'+Dim;
		
		if( !$(elem).is('html,body') )
			return elem[scroll] - $(elem)[Dim.toLowerCase()]();
		
		var size = 'client' + Dim,
			html = elem.ownerDocument.documentElement,
			body = elem.ownerDocument.body;

		return Math.max( html[scroll], body[scroll] ) 
			 - Math.min( html[size]  , body[size]   );
			
	};

	function both( val ){
		return typeof val == 'object' ? val : { top:val, left:val };
	};

})( jQuery );