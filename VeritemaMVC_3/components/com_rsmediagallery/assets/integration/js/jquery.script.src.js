function rsmg_init_equal_size($, namespace) {	var max = 0;	var current_items = [];	if ($('.rsmg_' + namespace + '_container').length == 0) return;	var old_top = $('.rsmg_' + namespace + '_container').offset().top;	var last	= $('.rsmg_' + namespace + '_container').length - 1;	$('.rsmg_' + namespace + '_container').each(function(index, el) {		var top = $(el).offset().top;		if (old_top != top)		{			for (var i=0; i<current_items.length; i++)				if (max > 0)					current_items[i].css('height', max);						top 	= $(el).offset().top;			old_top = top;			max 	= 0;			current_items.length = 0;		}				max = Math.max(max, $(el).height());		current_items.push($(el));				if (index == last)		{			for (var i=0; i<current_items.length; i++)				if (max > 0)					current_items[i].css('height', max);		}	});}function rsmg_init_hover($, namespace) {	$('ul.rsmg_' + namespace + '_gallery li img').hover(		function () { $(this).stop().animate({ opacity: 0.7 }, "slow"); },		function () { $(this).stop().animate({ opacity: 1.0 }, "slow"); }	);}function rsmg_init_gallery($, namespace) {	rsmg_init_hover($, namespace);	$(document).rsmg_piroBox_ext({		piro_speed: 400,		bg_alpha: 0.5,		piro_scroll: true,		htmlClass: 'default',		selector: 'a[class*="pirobox_gall_' + namespace + '"]'	});	rsmg_init_equal_size($, namespace);}