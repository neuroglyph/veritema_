jQuery(document).ready(function ($) {
	// hide previous button if we're javascript capable
    if ($("#rsmg_prev_page").length > 0)
		$("#rsmg_prev_page").hide();
		
	// has filters
	var hasFilters = $('#rsmg_gallery_filters').length;
	// items gallery
	
    rsmg_init_load_more($);
	
	// check if it's album view
	var albums_v = false;
	if (typeof albumview != 'undefined') {
		albums_v = true;
	}

	
	if (responsive && !hasFilters && !albums_v) {
		$('#rsmg_main').hide();
		$(window).load(function(){
			$('#rsmg_main').show();
			rsmg_init_items($, hasFilters);
		});
	}
	else if(responsive && hasFilters && !albums_v) {
		$(window).load(function(){
			rsmg_init_items($, hasFilters);
		});
	}
	else {
		rsmg_init_items($, hasFilters);
	}
	rsmg_set_tags_filters($);
	
	//album fixes on load
	if (albums_v) {
		rsmg_check_width($);
	}
	//center albums on load
	var albums_center = false;
	if (typeof center_albums!= 'undefined' && center_albums) {
		rsmg_center_albums($);
		albums_center = true;
	}
	
	// activate as need it 'center_albums', 'center_gallery', 'responsive fixes', 'album views fixes' in window resize; 
	if (center_list || responsive) {
		$(window).resize(function () {
			if (center_list) rsmg_align_center($, false);
			if (responsive) rsmg_equal_size_responsive($, false, 0);
			if (albums_v) {
				rsmg_check_width($);
			}
			if (albums_center) {
				rsmg_center_albums($);
			}
			
		});
	}
	
	
    var src = $("#rsmg_image_container img").attr("src");
	// IE needs a random string so it will not cache the image 
    if ($.browser.msie && $.browser.version < 9) {
        src += src.indexOf("?") == -1 ? "?" : "&";
        src += "random=" + Math.floor(Math.random() * 11000)
    }
    $("#rsmg_loader_container").removeAttr("style");
    var preloader = $("<img>").load(function () {
		// if it doesn't fit the page, just make it 100% so the browser will resize it
        if ($("#rsmg_thumb_container > img").outerWidth() > $("#rsmg_image_container").parent().outerWidth())
			$("#rsmg_image_container img").css("width", "100%");
        $("#rsmg_loader_container").remove();
        $("#rsmg_image_container").removeClass("rsmg_hidden_from_view");
        $("#rsmg_image_container img").hide().fadeIn(700);
    }).attr("src", src);
});

function rsmg_check_width($) {
	var titlesHeight = [];
	$('#rsmg_albums li').each(function(){
		if(responsive){
			var album_w = $(this).width();
		}
		else {
			var album_w = $(this).find('.rsmg_album_inner_container').width();
		}
		var album_title = $(this).find('.rsmg_special_title').val();
		album_title = album_title.trim();
	
		var album_image_w_double = album_image_data[album_title] * 2;
		
		var new_album_w = 0;
		if(album_image_w_double <= album_w) {
			new_album_w = album_image_w_double;
			$(this).find('.rsmg_album_inner_container').css('max-width',new_album_w);
		}
		var width_container = (new_album_w==0 ? $(this).width() : new_album_w);
		$(this).find('.rsmg_title').css('max-width',width_container);
		
		titlesHeight.push($(this).find('.rsmg_title').height());
	});	
	
	var maxHeight = Math.max.apply( Math, titlesHeight );
	$('#rsmg_albums li').each(function(){
		$(this).find('.rsmg_title').height(maxHeight);
	});
}

function rsmg_center_albums($) {
	var offsets = [];
	var img_cont_w = 0;
	var margin_cont = 0;
	
	$('#rsmg_albums li').each(function(){
		offsets.push($(this).find('.rsmg_album_container').offset().top);
		if(img_cont_w == 0 ) {
			if (responsive) {
				img_cont_w = $(this).width();
			}
			else {
				img_cont_w = $(this).find('.rsmg_album_container').width();
			}
		}
		if (margin_cont == 0) {
			if (responsive) {
				margin_cont = parseInt($(this).css('margin-left'));
			}
			else {
				margin_cont = parseInt($(this).css('margin-right'));
			}	
			
		}
		
		if (responsive) {
			var width_album_cont = $(this).find('.rsmg_album_container').outerWidth();
			var width_li_cont = $(this).width();
			
			var innerMargin = (width_li_cont - width_album_cont) / 2;
			$(this).find('.rsmg_album_container').css('margin-left',innerMargin);

		}
	});
	
	var counts 		 = countOccurances(offsets);
	var disp_per_row = 0;
	for(var i=0; i < offsets.length; i++) {
		if (disp_per_row < counts[offsets[i]]) {
			disp_per_row = counts[offsets[i]];
		}
	}
	
	
	var main_w = $('#rsmg_main').width();
	
	
	var img_cont_padding_left = $('#rsmg_albums .rsmg_album_container').css('padding-left');
	img_cont_padding_left = img_cont_padding_left.substr(0,(img_cont_padding_left.length - 2));
	img_cont_padding_left = parseInt(img_cont_padding_left);
	
	var img_cont_padding_right = $('#rsmg_albums .rsmg_album_container').css('padding-right');
	img_cont_padding_right = img_cont_padding_right.substr(0,(img_cont_padding_right.length - 2));
	img_cont_padding_right = parseInt(img_cont_padding_right);
	
	var img_cont_border = 2;
	if (responsive) {
		var img_cont_total_w = img_cont_w;
	}
	else {
		var img_cont_total_w = img_cont_w + img_cont_padding_left + img_cont_padding_right + img_cont_border + margin_cont;
	}
	var max_per_row = Math.floor(main_w / img_cont_total_w);

	
	if (offsets.length >= max_per_row) {
		disp_per_row = max_per_row;
	}
	
	if(disp_per_row == 0) disp_per_row = 1;
	if (disp_per_row == 1) {
		var indent = 0;
	}
	else {
		var indent = margin_cont * disp_per_row;
	}
	if (indent == 0) indent = img_cont_padding_left + img_cont_padding_right + img_cont_border;
	
	if (responsive) {
		var set_margin = (main_w - (((img_cont_w + indent) * (disp_per_row -1)) + img_cont_w)) / 2;
	}
	else {
		var set_margin = (main_w - (img_cont_total_w * disp_per_row)) / 2;
	}
	
	
	if (max_per_row > disp_per_row && !responsive) {
		set_margin = set_margin + margin_cont;
	}
	
	set_margin = (set_margin>0 ? set_margin : 0);
	
	
	$('#rsmg_albums').css('margin-left',set_margin);
}

function inner_center_responsive($, masterContainer, width_li_cont) {
	$('#rsmg_gallery li').each(function(){
		if ($(this).css('display') != 'none' && $(this).css('clear') != 'both') {
			var innerMargin = (width_li_cont - masterContainer) / 2;
			$(this).find('.rsmg_item_container').css('margin-left',innerMargin);
		}
	});
}

function rsmg_align_center($, mix) {
	
	var offsets = [];
	var img_cont_w = 0;
	var margin_cont = 0;
	var width_li_cont = 0;
	var masterContainer = 0;
	
	var visited = 0;
	$('#rsmg_gallery li').each(function(){
		if ($(this).css('display') != 'none' && $(this).css('clear') != 'both') {
			offsets.push($(this).find('.rsmg_item_container').offset().top);
			if(img_cont_w == 0 ) {
				if (responsive) {
					img_cont_w = $(this).width();
				}
				else {
					img_cont_w = $(this).find('.rsmg_item_container').width();
				}
			}
			if (margin_cont == 0) {
				if (responsive) {
					margin_cont = parseInt($(this).css('margin-left'));
				}
				else {
					margin_cont = parseInt($(this).css('margin-right'));
				}				
			}
			
			if (responsive) {
				if(visited == 0) {
					width_li_cont = $(this).width();
					masterContainer = $(this).find('.rsmg_item_container').outerWidth();
				}
				visited++;
			}
		}
	});
	
	if (responsive && masterContainer!=0 && width_li_cont!=0) {
		inner_center_responsive($, masterContainer, width_li_cont);
	}
	
	
	var counts 		 = countOccurances(offsets);
	var disp_per_row = 0;
	for(var i=0; i < offsets.length; i++) {
		if (disp_per_row < counts[offsets[i]]) {
			disp_per_row = counts[offsets[i]];
		}
	}
	
	var main_w = $('#rsmg_main').width();
	
	var img_cont_padding_left = $('#rsmg_gallery .rsmg_item_container').css('padding-left');
	img_cont_padding_left = img_cont_padding_left.substr(0,(img_cont_padding_left.length - 2));
	img_cont_padding_left = parseInt(img_cont_padding_left);
	
	var img_cont_padding_right = $('#rsmg_gallery .rsmg_item_container').css('padding-right');
	img_cont_padding_right = img_cont_padding_right.substr(0,(img_cont_padding_right.length - 2));
	img_cont_padding_right = parseInt(img_cont_padding_right);
	
	var img_cont_border = 2;
	if (responsive) {
		var img_cont_total_w = img_cont_w;
	}
	else {
		var img_cont_total_w = img_cont_w + img_cont_padding_left + img_cont_padding_right + img_cont_border + margin_cont;
	}
	
	var max_per_row = Math.floor(main_w / img_cont_total_w);
	
	
	if (offsets.length >= max_per_row) {
		disp_per_row = max_per_row;
	}
	
	if(disp_per_row == 0) disp_per_row = 1;
	
	if (disp_per_row == 1) {
		var indent = 0;
	}
	else {
		var indent = margin_cont;
	}
	
	if (indent == 0) indent = img_cont_padding_left + img_cont_padding_right + img_cont_border;
	if (responsive) {
		var set_margin = (main_w - (((img_cont_w + indent) * (disp_per_row -1)) + img_cont_w)) / 2;
	}
	else {
		var set_margin = (main_w - (img_cont_total_w * disp_per_row)) / 2;
	}
	
	if (max_per_row > disp_per_row && !responsive) {
		set_margin = set_margin + margin_cont;
	}	
	
	set_margin = (set_margin>0 ? set_margin : 0);
	
	if(mix) {
		$('#rsmg_gallery').animate({
			marginLeft: set_margin+"px"}, 500, function() {
				$( this ).after(function() {
						return rsmg_init_same_mix_size($);
					});
				
				});
	} else {
		$('#rsmg_gallery').css('margin-left',set_margin);
	}
}


function countOccurances(vector) {
	var counts = {};

	for(var i = 0; i< vector.length; i++) {
		var num = vector[i];
		counts[num] = counts[num] ? counts[num]+1 : 1;
	}
	return counts;
}

function rsmg_add_lang(id, translation) {
    if (typeof id == "object") {
        for (j in id) rsmg_lang_vars[j] = id[j];
        return true;
    }
    return rsmg_lang_vars[id] = translation;
}

function rsmg_get_lang(id, arg) {
    if (typeof rsmg_lang_vars[id] != "undefined") {
        val = rsmg_lang_vars[id];
        if (arg) {
            if (val.indexOf("%s") > -1) val = val.replace("%s", arg);
            else if (val.indexOf("%d") > -1) val = val.replace("%d", arg)
        }
        return val
    }
    return id;
}

function rsmg_set_tags_filters($) {
	var visibleTags = [];
	
	$('#rsmg_gallery li').each(function(){
		var classes = $(this).attr('class');
		if (classes) {
			classes = classes.split(' ');
			if (classes.length > 0) {
				for (var i=0; i < classes.length; i++) {
					if (classes[i] != 'mix' && classes[i] != 'mix_all') {
						visibleTags.push(classes[i]);
					}
				}
			}
		}
	});
	if (visibleTags.length > 0) {
		var uniqueArray = visibleTags.filter(function(elem, pos) {
			return visibleTags.indexOf(elem) == pos;
		});
		
		var isAnimation = $('#rsmg_gallery_filters').length;
		if (isAnimation > 0) {
			$('#rsmg_gallery_filters li').each(function(){
				var tag = $(this).attr('data-filter');
				if (tag != 'all'){
					if (uniqueArray.indexOf(tag) > -1) {
						$(this).fadeIn(600);
					}
				}
			});
			
		}
	}
}

function rsmg_init_load_more($) {
	// do we have a load more button ?
    if ($("#rsmg_load_more").length > 0) {
		// get total images
        var total = $("#rsmg_load_more").attr("rel");
		// get images left
        var left = 0;
		var children = $("ul#rsmg_gallery li .rsmg_item_container").length;
		
        if (total > 0) left = total - children;
        if (left < 0) left = 0;
        $("#rsmg_load_more").attr("rel", left);
        $("#rsmg_load_more").html(rsmg_get_lang("COM_RSMEDIAGALLERY_LOAD_MORE", $("#rsmg_load_more").attr("rel")));
        $("#rsmg_load_more").click(function (e) {
            e.preventDefault();
            e.shiftKey ? rsmg_get_items($, false, {
                limitall: 1,
                limit: $("#rsmg_load_more").attr("rel")
            }) : rsmg_get_items($)
        });
        $(document).keydown(function (e) {
            if (e.shiftKey) $("#rsmg_load_more").html(rsmg_get_lang("COM_RSMEDIAGALLERY_LOAD_ALL", $("#rsmg_load_more").attr("rel")));
        });
        $(document).keyup(function (e) {
            $("#rsmg_load_more").html(rsmg_get_lang("COM_RSMEDIAGALLERY_LOAD_MORE", $("#rsmg_load_more").attr("rel")));
        })
    }
}

function rsmg_get_original_limitstart($) {
    return parseInt($("#rsmg_original_limitstart").val());
}

function rsmg_init_items($, hasFilters) {
	// init hover effect
    $("#rsmg_gallery li img").hover(function () {
        $(this).stop().animate({
            opacity: .7
        }, "slow")
    }, function () {
        $(this).stop().animate({
            opacity: 1
        }, "slow")
    });
	// init lightbox
    if (typeof rsmg_init_lightbox2 == "function") {
		rsmg_init_lightbox2();
	}
	// init equal size if filtering is not enabled
	if(center_list) {
		rsmg_align_center($, false);
	}
	if(!hasFilters) {
		rsmg_init_equal_size($);
	}
	else {
		rsmg_init_same_mix_size($);
	}
	
}

function rsmg_init_equal_size($) {
	
	if (!responsive) {
		var max = 0;
		var current_items = [];
		if ($('.rsmg_item_container').length == 0) return;
		var old_top = $('.rsmg_item_container').offset().top;
		var last	= $('.rsmg_item_container').length - 1;
		$('.rsmg_item_container').each(function(index, el) {
			
			var top = $(el).offset().top;
		
			if (old_top != top)
			{
				for (var i=0; i<current_items.length; i++)
					if (max > 0)
						current_items[i].css('height', max);
				
				top 	= $(el).offset().top;
				old_top = top;
				max 	= 0;
				current_items.length = 0;
			}
			
			max = Math.max(max, $(el).height());
			current_items.push($(el));
			
			if (index == last)
			{
				for (var i=0; i<current_items.length; i++)
					if (max > 0)
						current_items[i].css('height', max);
			}
		});
	}
	else {
		rsmg_equal_size_responsive($, false, 0);
	}
}

// responsive script
 
function rsmg_equal_size_responsive($, mix, called) {
	var offsets = [];
	var item_data = [];
	
	$('#rsmg_gallery li .rsmg_item_container').each(function(){
		var item_id = $(this).parent().attr('id');
		if ($(this).parent().css('display') != 'none' && item_id != 'rsmg_loader_container') {
			offsets.push(Math.round($(this).offset().top));
			items_offsets = {};
			items_offsets.top = Math.round($(this).offset().top); 
			
			items_offsets.bottom = items_offsets.top + $(this).find('.rsmg_image').height();
			
			if ($(this).find('.rsmg_image_details') && $(this).find('.rsmg_image_details').css('display') != 'none') {
				items_offsets.bottom = items_offsets.bottom + $(this).find('.rsmg_image_details').outerHeight(); 
			}
			if ($(this).find('.rsmg_item_rest') && $(this).find('.rsmg_item_rest').css('display') != 'none') {
				items_offsets.bottom = items_offsets.bottom + $(this).find('.rsmg_item_rest').outerHeight(); 
			}
			item_data.push(items_offsets);
			
		}
	});
	var counts 		 = countOccurances(offsets);
	
	for(var i=0; i < item_data.length; i++) {
		item_data[i].row = counts[item_data[i].top];		
	}
	
	
	var new_item_bottoms = {};
	
	for(var i=0; i < item_data.length; i++) {
		if (!new_item_bottoms[item_data[i].top]) {
			new_item_bottoms[item_data[i].top] = rsmg_get_bottoms($, item_data,counts[item_data[i].top], item_data[i].top);
		}
		
	}
	var countItems = 0;
	$('#rsmg_gallery li .rsmg_item_container').each(function(){
		var item_id = $(this).parent().attr('id');
		if ($(this).parent().css('display') != 'none' && item_id != 'rsmg_loader_container') {
			countItems++;
		}
	});
	
	var visited = 0;
	$('#rsmg_gallery li .rsmg_item_container').each(function(){
		var item_id = $(this).parent().attr('id');
		if ($(this).parent().css('display') != 'none' && item_id != 'rsmg_loader_container') {
			
			var current_offset_top = Math.round($(this).offset().top);
			var width_container = $(this).width();
			if (counts[current_offset_top] > 1 ) {
				var biggest_bottom = Math.max.apply(null, new_item_bottoms[current_offset_top]);
				var diff = biggest_bottom - current_offset_top;
				
				if (diff > 0) {
					if (mix){
						visited++;
						
						if (visited == countItems) {
							$(this).animate({height: diff+"px"}, 500, function(){
								rsmg_margin_left_responsive($, mix, called);
							});
						}
						else $(this).animate({height: diff+"px"}, 500);
					}
					else {
						$(this).height(diff);
					}
					$(this).find('.rsmg_image_details').css('max-width',width_container);
				}
			}
			else {
				var new_height = 0;
				if ($(this).find('.rsmg_image') && $(this).find('.rsmg_image').css('display') != 'none') {
					new_height = new_height + $(this).find('.rsmg_image').height();
				}
				if ($(this).find('.rsmg_image_details') && $(this).find('.rsmg_image_details').css('display') != 'none') {
					new_height = new_height + $(this).find('.rsmg_image_details').outerHeight();
				}
				if ($(this).find('.rsmg_item_rest') && $(this).find('.rsmg_item_rest').css('display') != 'none') {
					new_height = new_height + $(this).find('.rsmg_item_rest').outerHeight();
				}
				
				
				if (mix){
					visited++;
					if (visited == countItems) {
						$(this).animate({height: new_height+"px"}, 500, function(){
							rsmg_margin_left_responsive($, mix, called);
						});
					}
					else $(this).animate({height: new_height+"px"}, 500);
				}
				else {
					$(this).height(new_height);
				}
				$(this).find('.rsmg_image_details').css('max-width',width_container);
				
			}
		}
		
	});
	
	if (!mix) rsmg_margin_left_responsive($, mix, called);
	
}

function rsmg_margin_left_responsive($, mix, called) {
	var offsets = [];
	var items = [];
	
	$('#rsmg_gallery li .rsmg_item_container').each(function(){
		var item_id = $(this).parent().attr('id');
		if ($(this).parent().css('display') != 'none' && item_id != 'rsmg_loader_container') {
			offsets.push(Math.round($(this).offset().top));
			items.push($(this).parent());
		}
	});
	var new_offsets = rsmg_break_array($, offsets);
	
	$.each(new_offsets, function(index,val) { 
		var row = index;
		$.each(val, function(k,v) {
			var item = (itemsrow * row) + k;
			if(items[item]) {
				var margin_left = parseInt(items[item].css('margin-left'));
				if (k!=0) {
					if(margin_left == 0) {
						if (mix){
							items[item].css('-webkit-transition','margin-left linear 0.3s');
							items[item].css('-moz-transition','margin-left linear 0.3s');
							items[item].css('-o-transition','margin-left linear 0.3s');
							items[item].css('transition','margin-left linear 0.3s');
						}
						
						items[item].removeClass('margin_left_none');
					}
					
				}
				else {
					if(margin_left != 0) {
						if (mix){
							items[item].css('-webkit-transition','margin-left linear 0.3s');
							items[item].css('-moz-transition','margin-left linear 0.3s');
							items[item].css('-o-transition','margin-left linear 0.3s');
							items[item].css('transition','margin-left linear 0.3s');
						}
						items[item].addClass('margin_left_none');
					}
				}
			}
		});
		
	});
	if (called==0) {
		if (mix) {
			setTimeout( function(){ 
				rsmg_equal_size_responsive($, mix, 1);
			 }, 400 );
		}
		else {
			rsmg_equal_size_responsive($, mix, 1);
		}
	}
}

function rsmg_break_array($, array) {
	var new_arrays = [];
	array = array.sort(function(a,b){return a-b});
	
	var counts = countOccurances(array);
	var slice_val = 0;
	
	var i = 0;
	$.each(counts, function(index,val) {
		new_arrays[i] = array.slice(slice_val, (slice_val + val));
		slice_val = slice_val + val;
		i++;
	});
	return new_arrays;
}

function rsmg_get_bottoms($, item_data , row, diff_top) {
	var heights = [];
	for(var i=0; i< item_data.length; i++) {
		if(item_data[i].row == row && item_data[i].top == diff_top) {
			heights.push(item_data[i].bottom);
		}
	}
	return heights;
}

// end responsive sript

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function rsmg_init_same_mix_size($){
	if (responsive) {
		rsmg_equal_size_responsive($, true, 0);
	}
	else {
		var offsets = [];
		$('#rsmg_gallery li').each(function(){
			if ($(this).css('display') == 'block' || $(this).css('display') == 'list-item') {
				offsets.push($(this).find('.rsmg_item_container').offset().top);
			}
		});
		var uniqueOffsets = offsets.filter(function(elem, pos) {
			return offsets.indexOf(elem) == pos;
		});
		for (var i=0; i<uniqueOffsets.length; i++) {
			var offsetsImagesHeights = [];
			
			$('#rsmg_gallery li').each(function(){
				if ($(this).css('display') == 'block' || $(this).css('display') == 'list-item') {
					if (uniqueOffsets[i]==$(this).find('.rsmg_item_container').offset().top) {
						var rmsgTitle = $(this).find('.rsmg_item_container').find('.rsmg_title').outerHeight();
						var rmsgDescription = $(this).find('.rsmg_item_container').find('.rsmg_item_description').height();
						var imgHeight = $(this).find('.rsmg_item_container').find('a > img').height();
						var contHeight = imgHeight + rmsgTitle + rmsgDescription + 22;
						offsetsImagesHeights.push(contHeight);
					}
				}
			});
			
			var newContainerHeight = Math.max.apply(null, offsetsImagesHeights);
			
			$('#rsmg_gallery li').each(function(){
				if ($(this).css('display') == 'block' || $(this).css('display') == 'list-item') {
					if (uniqueOffsets[i]==$(this).find('.rsmg_item_container').offset().top) {
						$(this).find('.rsmg_item_container').animate({height: newContainerHeight+"px"}, 500);
					}
				}
			});	
		}
	}
}

function rsmg_get_items_filter($, more, albumView) {
	var children = $("ul#rsmg_gallery li .rsmg_item_container").length;
    if (albumView) {
		var data = {
			limitstart: children + rsmg_get_original_limitstart($),
			Itemid: $("#rsmg_itemid").val(),
			tag: $('#selectedTag').val()
		};
	}
	else {
		var data = {
			limitstart: children + rsmg_get_original_limitstart($),
			Itemid: $("#rsmg_itemid").val()
		};
	}
    if (more)
		for (var k in more)
			data[k] = more[k];
    return data;
}

function rsmg_min_max_occourences($){
	var offsets = [];
	$('#rsmg_gallery li .rsmg_item_container').each(function(){
		var item_id = $(this).parent().attr('id');
		if ($(this).css('display') != 'none' && item_id != 'rsmg_loader_container') {
			offsets.push($(this).offset().top);
		}
	});
	
	var counts = countOccurances(offsets);
	var counts_array = [];
	
	for(var i=0; i<offsets.length; i++) {
		counts_array.push(counts[offsets[i]]);
	}
	
	var min = Math.min.apply(null, counts_array);
    var max = Math.max.apply(null, counts_array);
	
	var data = { min:min, max:max };
	return data;
	
}

function rsmg_get_items($, clear, more, successFunction, overrideAsync) {
	// set layout 
	var albumView = $('#rsmg_load_more').hasClass('rsmg_album_layout');
	
	// parent container
    var parent = $("#rsmg_gallery");
	
	// clear contents
    if (clear == true)
		parent.empty();
		
    if (overrideAsync === false)
		overrideAsync = false;
    else
		overrideAsync = true;
	
	if (albumView) {
		urlPost = rsmg_get_root() + "/index.php?option=com_rsmediagallery&task=getItemsAlbum&format=raw";
	}
	else {
		urlPost = rsmg_get_root() + "/index.php?option=com_rsmediagallery&task=getitems&format=raw";
	}
    $.ajax({
        type: "POST",
        url: urlPost,
        data: rsmg_get_items_filter($, more, albumView),
        async: overrideAsync,
        beforeSend: function () {
			// li container
            var li = $("<li>", {
                id: "rsmg_loader_container"
            });
			
			if (responsive) {
				li.addClass('span'+Math.floor(12/itemsrow));
				offset_data = rsmg_min_max_occourences($);
				if (offset_data.min == offset_data.max) {
					li.addClass('margin_left_none');
				}
			}
			
			// ajax loader
            var loader = $("<div>", {
                "class": "rsmg_item_container"
            });
			
			if (responsive) {
				loader.width('100%');
			}
			
			// add loader image
            li.append(loader);
			
			// append the loader as the last item in the list
            parent.append(li);
			
			// hide load more
            $("#rsmg_load_more").fadeOut(500)
        },
        success: function (data) {
           $("#rsmg_loader_container").remove();
			
            if (typeof data == "object" && data.items && data.total) {
				// check if animation is enabled
				var animation = (data.animation ? data.animation : false);
				
                var k = parent.find('.rsmg_item_container').length;
				
				var loaded_images = [];
                $(data.items).each(function (index, item) {
					loaded_images.push(item.thumb);
					// li container
                    var li = $("<li>");
					
					
					if (responsive) {
						li.addClass('span'+Math.floor(12/itemsrow));
						offset_data = rsmg_min_max_occourences($);
						if (offset_data.min == offset_data.max) {
							li.addClass('margin_left_none');
						}
					}
					
					if (animation!=0) {
						li.addClass('mix '+item.niceTags+' mix_all');
						li.css({'display':'inline-block','opacity':'1'});
					}
					
					// div container
                    var div = $("<div>", {
                        "class": "rsmg_item_container"
                    });
					
					if (responsive) {
						var div_image = $("<div>", {
							"class": "rsmg_image"
						});
					}
					// thumbnail
					// thumbnail link
                    var a_thumb = $("<a>", {
                        href: item.href,
                        "class": "rsmg_lightbox",
                        rel: "{'link': '" + item.full + "', 'title': '#rsmg_item_" + k + "', 'id': '" + item.id + "', 'group' : 'all'}"
                    });
                    if (item.open_in_new_page)
						a_thumb.attr("target", "_blank");
					// thumbnail image
                    var img_thumb = $("<img>", {
                        src: item.thumb,
                        alt: item.title
                    });
					if (!responsive) {
						img_thumb.attr({
							'width': item.thumb_width,
							'height': item.thumb_height
						});
						img_thumb.css({
							'width': item.thumb_width,
							'height': item.thumb_height
						});
					}
                    a_thumb.append(img_thumb);
					
					// title in listing ?
                    var title = "";
					
					if(responsive) {
						var div_details =  "";
						if (item.show_title_list == 1) {
							div_details = $("<div>", {
							"class": "rsmg_image_details"
							});
						}
					}	
					
                    if (item.show_title_list == 1) {
                        title = $("<a>", {
                            href: item.href,
                            "class": "rsmg_title"
                        }).html(item.title);
                        if (item.open_in_new_page)
							title.attr("target", "_blank")
                    }
					
					// description in listing ?
                    var description = "";
                    if (item.show_description_list == 1) description = $("<span>", {
                        "class": "rsmg_item_description"
                    }).html(item.description);
					
					// details
					
                    var details_container = $("<div>", {
                        id: "rsmg_item_" + k
                    }).css("display", "none");
					
					if(responsive) {
						details_container.addClass('rsmg_item_rest');
					}
					
					// show title in details ?
                    if (item.show_title_detail == 1)
						details_container.append($("<h2>", {"class": "rsmg_title"}).html(item.title));
					
					// show description in details ?
                    if (item.show_description_detail == 1)
						details_container.append(item.full_description);
					
					// download original link
                    var download_original = "";
                    if (item.download_original == 1) {
                        download_original = $("<div>", {
                            "class": "rsmg_download rsmg_toolbox"
                        });
                        a_download_original = $("<a>", {
                            href: item.download
                        }).html(rsmg_get_lang("COM_RSMEDIAGALLERY_DOWNLOAD"));
                        download_original.append(a_download_original)
                    }
					
					// show views
                    var hits = "";
                    if (item.show_hits == 1) hits = $("<div>", {
                        "class": "rsmg_views rsmg_toolbox"
                    }).html(rsmg_get_lang(item.hits == 1 ? "COM_RSMEDIAGALLERY_HIT" : "COM_RSMEDIAGALLERY_HITS", item.hits));
					
					// show created date
                    var created = "";
                    if (item.show_created == 1) created = $("<div>", {
                        "class": "rsmg_calendar rsmg_toolbox"
                    }).html(rsmg_get_lang("COM_RSMEDIAGALLERY_CREATED", item.created));
					
					// show modified date
                    var modified = "";
                    if (item.show_modified == 1) modified = $("<div>", {
                        "class": "rsmg_calendar rsmg_toolbox"
                    }).html(rsmg_get_lang("COM_RSMEDIAGALLERY_MODIFIED", item.modified));
					
					// show a list of tags
                    var tags = "";
                    if (item.show_tags == 1) tags = $("<p>", {
                        "class": "rsmg_tags"
                    }).html(rsmg_get_lang("COM_RSMEDIAGALLERY_TAGS") + ": <strong>" + item.tags + "</strong>");
					
					// clear spans
                    var clear1 = $("<span>", {
                        "class": "rsmg_clear"
                    });
                    var clear2 = $("<span>", {
                        "class": "rsmg_clear"
                    });
					
					// append all details
                    details_container.append(clear1, download_original, hits, created, modified, clear2, tags);
					
					if (responsive) {
					
						// add details to item
						div.append(div_image.append(a_thumb));
						if(div_details!="") div.append(div_details.append(title));
						if(description!="") div.append(description);
						div.append(details_container);
						li.append(div);
						
					}
					else {
						// add details to item
						li.append(div.append(a_thumb, title, description, details_container));
					
					}
					// append item to parent
						parent.append(li);
					
                    k++;
                });
				
				// init items (+ lightbox if available)
                rsmg_init_items_loaded($, loaded_images, responsive);
				
				if (animation!=0) {
					// remix all gallery
					$('#rsmg_gallery').mixitup('remix','all');
					rsmg_set_tags_filters($)
				}
				
				// show load more
				var original_limitstart = rsmg_get_original_limitstart($);
                if (data.total > $("ul#rsmg_gallery").children().length + original_limitstart) {
                    var left = data.total - ($("ul#rsmg_gallery").children().length + original_limitstart);
                    $("#rsmg_load_more").html(rsmg_get_lang("COM_RSMEDIAGALLERY_LOAD_MORE", left));
                    $("#rsmg_load_more").attr("rel", left);
                    $("#rsmg_load_more").fadeIn(500);
                }
				else
					$("#rsmg_load_more").attr("rel", 0);
					
                if (typeof successFunction == "function") {
                    successFunction(data);
                }
            }
        }
    })
}

function rsmg_init_items_loaded($, loaded_images, responsive) {
	var hasFilters = $('#rsmg_gallery_filters').length;
	var nr_images = loaded_images.length;
	var loaded 	  = 0;
	
	if (!responsive) var images = $('#rsmg_gallery li .rsmg_item_container img');
	else var images = $('#rsmg_gallery li .rsmg_item_container .rsmg_image img');
	
	images.each(function(){
		
		var src = $(this).attr('src');
		if (inArray(src, loaded_images)){
			$(this).load(function(){
				loaded++;
				if(loaded == nr_images) {
					rsmg_init_items($, hasFilters);
				}
			});
		}
	});
	
}

function rsmg_hit_item(settings) {
	cid = parseInt(jQuery('#lightbox-image').attr('rel'));
	if (!isNaN(cid) && cid > 0 && rsmg_hit.indexOf(cid) == -1)
	{
		// add it to the stacks
		rsmg_hit.push(cid);
		rsmg_to_hit.push(cid);
		
		if (rsmg_hit_timer)
			clearTimeout(rsmg_hit_timer);
		
		rsmg_hit_timer = setTimeout(function() {
			// hit it
			jQuery.ajax({
				type: "POST",
				url: rsmg_get_root() + "/index.php",
				data: {
					'option': 'com_rsmediagallery',
					'task': 'hititem',
					'cid': rsmg_to_hit
				}
			});
			
			rsmg_to_hit.length = 0;
		}, 2500);
	}
}


var rsmg_lang_vars = {};
var rsmg_hit 	   = [];
var rsmg_to_hit	   = [];
var rsmg_hit_timer = false;