/**
* Name: PiroBox Extended v.1.0
* Date: Gen 2011
* Autor: Diego Valobra (http://www.pirolab.it),(http://www.diegovalobra.com)
* Version: 1.0
* Licence: CC-BY-SA http://creativecommons.org/licenses/by-sa/3/it/
**/

(function($) {
	$.fn.rsmg_piroBox_ext = function(opt) {
		opt = jQuery.extend({
		piro_speed : 700,
		bg_alpha : 0.9,
		piro_scroll : true,
		draggable: false,
		selector: 'a[class*="pirobox"]',
		htmlClass: ''
		}, opt);
	$.fn.rsmg_piroFadeIn = function(speed, callback) {
		$(this).fadeIn(speed, function() {
		if(jQuery.browser.msie)
			$(this).get(0).style.removeAttribute('filter');
		if(callback != undefined)
			callback();
		});
	};
	$.fn.rsmg_piroFadeOut = function(speed, callback) {
		$(this).fadeOut(speed, function() {
		if(jQuery.browser.msie)
			$(this).get(0).style.removeAttribute('filter');
		if(callback != undefined)
			callback();
		});
	};
	var my_gall_obj = $(opt.selector);
	var map = new Object();
		for (var i=0; i<my_gall_obj.length; i++) {
			var it=$(my_gall_obj[i]);
			map['a.'+ it.attr('class').match(/^pirobox_gall\w*/)]=0;
		}
	var gall_settings = new Array();
	for (var key in map) {
		gall_settings.push(key);
	}
	for (var i=0; i<gall_settings.length; i++) {
		$(gall_settings[i]+':first').addClass('rsmg_is_first');
		$(gall_settings[i]+':last').addClass('rsmg_is_last');
	}
	
	var piro_gallery = $(my_gall_obj);
	$('a[class*="pirobox_gall"]').each(function(rev){this.rev = rev+0});
	var struct =(
		'<div class="piro_overlay piro_overlay_' + opt.htmlClass + '"></div>'+
		'<table class="piro_html piro_html_' + opt.htmlClass + '" cellpadding="0" cellspacing="0">'+
		'<tr>'+
		'<td class="h_t_l"></td>'+
		'<td class="h_t_c"></td>'+
		'<td class="h_t_r"></td>'+
		'</tr>'+
		'<tr>'+
		'<td class="h_c_l"></td>'+
		'<td class="h_c_c">'+
		'<div class="piro_loader piro_loader_' + opt.htmlClass + '" title="close"><span></span></div>'+
		'<div class="resize resize_' + opt.htmlClass + '">'+
		'<div class="piro_nav_container piro_nav_container_' + opt.htmlClass + '">'+
		'<a href="#prev" class="piro_prev piro_prev_' + opt.htmlClass + '" title="previous"></a>'+
		'<a href="#next" class="piro_next piro_next_' + opt.htmlClass + '" title="next"></a>'+
		'<div class="piro_close piro_close_' + opt.htmlClass + '" title="close"></div>'+
		'</div>'+
		'<div class="piro_caption piro_caption_' + opt.htmlClass + '"></div>'+
		'<div class="div_reg div_reg_' + opt.htmlClass + '"></div>'+
		'</div>'+
		'</td>'+
		'<td class="h_c_r"></td>'+
		'</tr>'+
		'<tr>'+
		'<td class="h_b_l"></td>'+
		'<td class="h_b_c"></td>'+
		'<td class="h_b_r"></td>'+
		'</tr>'+
		'</table>'
		);
	$('body').append(struct);
	var wrapper = $('.piro_html_' + opt.htmlClass),	
	piro_capt = $('.piro_caption_' + opt.htmlClass),
	piro_bg = $('.piro_overlay_' + opt.htmlClass),
	piro_next = $('.piro_next_' + opt.htmlClass),
	piro_prev = $('.piro_prev_' + opt.htmlClass),
	piro_close = $('.piro_close_' + opt.htmlClass),
	div_reg = $('.div_reg_' + opt.htmlClass),
	piro_loader = $('.piro_loader_' + opt.htmlClass),
	resize = $('.resize_' + opt.htmlClass),
	btn_info = $('.btn_info'),
	nav_container = $('.piro_nav_container_' + opt.htmlClass);
	
	var rz_img =0.95; /*::::: ADAPT IMAGE TO BROWSER WINDOW SIZE :::::*/
	if (opt.draggable)
	{
		if ( $.browser.msie ) {
			wrapper.draggable({ handle:'.h_t_c,.h_b_c,.div_reg_' + opt.htmlClass + ' img'});
		}else{
			wrapper.draggable({ handle:'.h_t_c,.h_b_c,.div_reg_' + opt.htmlClass + ' img',opacity: 0.80});
		}	
	}
	var y = $(window).height();
	var x = $(window).width();
	nav_container.hide();
	wrapper.css({left:  ((x/2)-(250))+ 'px',top: parseInt($(document).scrollTop())+(100)});
	$(wrapper).add(piro_capt).add(piro_bg).hide();
	piro_bg.css({'opacity':opt.bg_alpha});	
	$(piro_prev).add(piro_next).bind('click',function(c) {
		nav_container.hide();
		c.preventDefault();
		piro_next.add(piro_prev).hide();
		var obj_count = parseInt($('a[class*="pirobox_gall"]').filter('.rsmg_gallery_item').attr('rev'));
		var start = $(this).is(piro_prev) ? $('a[class*="pirobox_gall"]').eq(obj_count - 1) : $('a[class*="pirobox_gall"]').eq(obj_count + 1);
		start.click();
	});
	$('html').bind('keyup', function (c) {
		 if(c.keyCode == 27) {
			c.preventDefault();
			if($(piro_close).is(':visible')){close_all();}
		}
	});
	$('html').bind('keyup' ,function(e) {
		 if ($('.rsmg_gallery_item').is('.rsmg_is_first')){
		}else if(e.keyCode == 37){
		e.preventDefault();
			if($(piro_close).is(':visible')){piro_prev.click();}
		 }
	});
	$('html').bind('keyup' ,function(z) {
		if ($('.rsmg_gallery_item').is('.rsmg_is_last')){
		}else if(z.keyCode == 39){
		z.preventDefault();
			if($(piro_close).is(':visible')){piro_next.click();}
		}
	});
	$(window).resize(function(){
		var new_y = $(window).height();
		var new_x = $(window).width();
		var new_h = wrapper.height();
		var new_w = wrapper.width();
		wrapper.css({
			left:  ((new_x/2)-(new_w/2))+ 'px',
			top: parseInt($(document).scrollTop())+(new_y-new_h)/2
			});			  
	});	
	function scrollIt (){
		$(window).scroll(function(){
			var new_y = $(window).height();
			var new_x = $(window).width();
			var new_h = wrapper.height();
			var new_w = wrapper.width();
			wrapper.css({
				left:  ((new_x/2)-(new_w/2))+ 'px',
				top: parseInt($(document).scrollTop())+(new_y-new_h)/2
			});			  
		});
	}
	if(opt.piro_scroll == true){
		scrollIt()
	}
	$(piro_gallery).each(function(){

		var descr = $(this).attr('title');
		$(this).removeAttr('title');
		var params = $(this).attr('rel').split('-');
		var p_link = $(this).attr('href');
		$(this).unbind(); 
		$(this).bind('click', function(e) {
			piro_bg.css({'opacity':opt.bg_alpha});	
			e.preventDefault();
			piro_next.add(piro_prev).hide().css('visibility','hidden');
			$(piro_gallery).filter('.rsmg_gallery_item').removeClass('rsmg_gallery_item');
			$(this).addClass('rsmg_gallery_item');
			open_all();
			if($(this).is('.rsmg_is_first')){
				piro_prev.hide();
				piro_next.show();
			}else{
				piro_next.add(piro_prev).show();
			}
			if($(this).is('.rsmg_is_last')){
				if (!$(this).is('.rsmg_is_first'))
					piro_prev.show();
				piro_next.hide();	
			}
			if($(this).is('.pirobox')){
				piro_next.add(piro_prev).hide();	
			}

		});

	function open_all(){
			wrapper.add(piro_bg).add(div_reg).add(piro_loader).show();
			$('.immagine').remove();
			function animate_html(){
				if(params[1] == 'full' && params[2] == 'full'){
				params[2] = $(window).height()-70;	
				params[1] = $(window).width()-55;
				}
				var y = $(window).height();
				var x = $(window).width();
				piro_close.hide();
				div_reg.add(resize).animate({
					'height':+ (params[2]) +'px',
					'width':+ (params [1])+'px'
					},opt.piro_speed).css('visibility','visible');
					
				wrapper.animate({
					height:+ (params[2])+20 +'px',
					width:+ (params[1]) +20+'px',
					left:  ((x/2)-((params[1])/2+10))+ 'px',
					top: parseInt($(document).scrollTop())+(y-params[2])/2-10
					},opt.piro_speed ,function(){
						piro_next.add(piro_prev).css({'height':'20px','width':'20px'});
						piro_next.add(piro_prev).css('visibility','visible');
						nav_container.show();
						piro_close.show();
				});
			}
			function animate_image (){
						var img = new Image();
						img.onload = function() {
							piro_capt.add(btn_info).hide();	
							var y = $(window).height();
							var x = $(window).width();
							var	imgH = img.height;
							var	imgW = img.width;
							
							//var rz_img =1.203; /*::::: ORIGINAL SIZE :::::*/
							if(imgH+20 > y || imgW+20 > x){
								var _x = (imgW + 20)/x;
								var _y = (imgH + 20)/y;
								if ( _y > _x ){
									imgW = Math.round(img.width* (rz_img/_y));
									imgH = Math.round(img.height* (rz_img/_y));
								}else{
									imgW = Math.round(img.width * (rz_img/_x));
									imgH = Math.round(img.height * (rz_img/_x));
								}
							}else{
								 imgH = img.height;
								 imgW = img.width;
								}

							var y = $(window).height();
							var x = $(window).width();
							$(img).height(imgH).width(imgW).hide();
							
							$(img).fadeOut(300,function(){});
								$('.div_reg_' + opt.htmlClass + ' img').remove();
								$('.div_reg_' + opt.htmlClass + '').html('');
								div_reg.append(img).show();
							$(img).addClass('immagine');
							
							div_reg.add(resize).animate({height:imgH+'px',width:imgW+'px'},opt.piro_speed);
							piro_loader.hide();	
							wrapper.animate({
								height : (imgH+20) + 'px' ,
								width : (imgW+20) + 'px' , 
								left:  ((x/2)-((imgW+20)/2)) + 'px',
								top: parseInt($(document).scrollTop())+(y-imgH)/2-20
								},opt.piro_speed, function(){
									var cap_w = resize.width();
									piro_capt.css({width:cap_w+'px'});
									//piro_loader.hide();
									$(img).fadeIn(300,function(){
									piro_close.add(btn_info).show();
									piro_capt.slideDown(200);								
									piro_next.add(piro_prev).css({'height':'20px','width':'20px'});
									piro_next.add(piro_prev).css('visibility','visible');
									nav_container.show();
									resize.resize(function(){
										NimgW = img.width;//1.50;
										NimgH = img.heigh;//1.50;
										piro_capt.css({width:(NimgW)+'px'});
									});	
								});	
							});	
						}
						
						img.src = p_link;
						piro_loader.click(function(){
						img.src = 'about:blank';
					});
				}

			switch (params[0]) {
				
				case 'iframe':
					div_reg.html('').css('overflow','hidden');
					resize.css('overflow','hidden');
					piro_close.add(btn_info).add(piro_capt).hide();
					animate_html();
					div_reg.rsmg_piroFadeIn(300,function(){
						div_reg.append(
						'<iframe id="my_frame" class="my_frame" src="'+p_link+'" frameborder="0" allowtransparency="true" scrolling="auto" align="top"></iframe>'
						);
						$('.my_frame').css({'height':+ (params[2]) +'px','width':+ (params [1])+'px'});
						piro_loader.hide();
					});
				break;
				
				case 'content':
					div_reg.html('').css('overflow','auto');
					resize.css('overflow','auto');
					$('.my_frame').remove();
					piro_close.add(btn_info).add(piro_capt).hide();
					animate_html()	
					div_reg.rsmg_piroFadeIn(300,function(){
						div_reg.load(p_link);
						piro_loader.hide();
					});
				break;
				
				case 'inline':
					div_reg.html('').css('overflow','auto');
					resize.css('overflow','auto');
					$('.my_frame').remove();
					piro_close.add(btn_info).add(piro_capt).hide();
					animate_html()	
					div_reg.rsmg_piroFadeIn(300,function(){
						$(p_link).clone(true).appendTo(div_reg).rsmg_piroFadeIn(300);
						piro_loader.hide();
					});
				break	
							
				case 'gallery':
					div_reg.css('overflow','hidden');
					resize.css('overflow','hidden');
					$('.my_frame').remove();
					piro_close.add(btn_info).add(piro_capt).hide();
					if (typeof descr != 'undefined')
					{
						if(descr == "")
							piro_capt.html('');
						else {
							piro_capt.html('<p>' + descr + '</p>');
							var piro_close_desc = $('<div id="piro_close_desc">x</div>');
							piro_capt.append(piro_close_desc);
						}
					}
					animate_image();
				break;
				
				case 'single':
					piro_close.add(btn_info).add(piro_capt).hide();
					div_reg.html('').css('overflow','hidden');
					resize.css('overflow','hidden');
					$('.my_frame').remove();
					if (typeof descr != 'undefined')
					{
						if(descr == "")
							piro_capt.html('');
						else {
							piro_capt.html('<p>' + descr + '</p>');
							var piro_close_desc = $('<div id="piro_close_desc">x</div>');
							piro_capt.append(piro_close_desc);
						}
					}
					animate_image();
				break
			} 	
		}
	});
		$('#piro_close_desc').live('click',function(){
		piro_capt.slideToggle(200);
	});
	
	function close_all (){
		if(wrapper.is(':visible')){
			$('.my_frame').remove();
			wrapper.add(div_reg).add(resize).stop();
			var ie_wrapper = wrapper;
			if ( $.browser.msie ) {
			ie_wrapper = div_reg.add(piro_bg);
			$('.div_reg_' + opt.htmlClass + ' img').remove();
			}else{
			ie_wrapper = wrapper.add(piro_bg);
			}
			ie_wrapper.rsmg_piroFadeOut(200,function(){
				div_reg.html('');
				piro_loader.add(piro_capt).add(btn_info).hide();
				nav_container.hide();
				piro_bg.add(wrapper).hide().css('visibility','visible');
			});
			}
		}
		piro_close.add(piro_loader).add(piro_bg).bind('click',function(y){y.preventDefault(); close_all(); });	
	}
})(jQuery);