function rsmg_init_system_equal_size(e){if(!responsive_system){var t=0;var n=[];if(e(".rsmg_system_container").length==0)return;var r=e(".rsmg_system_container").offset().top;var i=e(".rsmg_system_container").length-1;e(".rsmg_system_container").each(function(s,o){var u=e(o).offset().top;if(r!=u){for(var a=0;a<n.length;a++)if(t>0)n[a].css("height",t);u=e(o).offset().top;r=u;t=0;n.length=0}t=Math.max(t,e(o).height());n.push(e(o));if(s==i){for(var a=0;a<n.length;a++)if(t>0)n[a].css("height",t)}});if(center_list_system){rsmg_align_center_system(e,false)}}else{rsmg_equal_size_responsive_system(e,false)}}function rsmg_equal_size_responsive_system(e,t){e(".rsmg_gallery").each(function(){var n=[];var r=[];var i=[];e(this).find(".rsmg_system_gallery li .rsmg_system_container").each(function(){var t=e(this).parent().attr("id");if(e(this).parent().css("display")!="none"){n.push(Math.round(e(this).offset().top));i.push(e(this).parent());items_offsets={};items_offsets.top=Math.round(e(this).offset().top);items_offsets.bottom=Math.round(e(this).offset().top);items_offsets.bottom=items_offsets.bottom+e(this).find("img").height();r.push(items_offsets)}});var s=countOccurances(n);for(var o=0;o<r.length;o++){r[o].row=s[r[o].top]}var u=rsmg_break_array(e,n);e.each(u,function(n,r){var s=n;e.each(r,function(e,n){var r=itemsrow_system*s+e;if(i[r]){var o=parseInt(i[r].css("margin-left"));if(e!=0){if(o==0){if(t){i[r].css("-webkit-transition","margin-left linear 0.3s");i[r].css("-moz-transition","margin-left linear 0.3s");i[r].css("-o-transition","margin-left linear 0.3s");i[r].css("transition","margin-left linear 0.3s")}i[r].removeClass("margin_left_none")}}else{if(o!=0){if(t){i[r].css("-webkit-transition","margin-left linear 0.3s");i[r].css("-moz-transition","margin-left linear 0.3s");i[r].css("-o-transition","margin-left linear 0.3s");i[r].css("transition","margin-left linear 0.3s")}i[r].addClass("margin_left_none")}}}})});var a={};for(var o=0;o<r.length;o++){if(!a[r[o].top]){a[r[o].top]=rsmg_get_bottoms(e,r,s[r[o].top],r[o].top)}}e(this).find(".rsmg_system_gallery li .rsmg_system_container").each(function(){var n=e(this).parent().attr("id");if(e(this).parent().css("display")!="none"){var r=Math.round(e(this).offset().top);if(s[r]>1){var i=Math.max.apply(null,a[r]);var o=i-r;if(o>0){if(t){e(this).animate({height:o+"px"},500)}else{e(this).height(o)}}}else{var u=0;u=e(this).find("img").height();if(t){e(this).animate({height:u+"px"},500)}else{e(this).height(u)}}}});if(center_list_system){rsmg_align_center_system(e,t)}})}function countOccurances(e){var t={};for(var n=0;n<e.length;n++){var r=e[n];t[r]=t[r]?t[r]+1:1}return t}function rsmg_get_bottoms(e,t,n,r){var i=[];for(var s=0;s<t.length;s++){if(t[s].row==n&&t[s].top==r){i.push(t[s].bottom)}}return i}function rsmg_break_array(e,t){var n=[];t=t.sort();var r=countOccurances(t);var i=0;var s=0;e.each(r,function(e,r){n[s]=t.slice(i,i+r);i=i+r;s++});return n}function rsmg_set_tags_filters_system(e){var t=[];e(".rsmg_system_gallery li").each(function(){var n=e(this).attr("class");if(n){n=n.split(" ");if(n.length>0){for(var r=0;r<n.length;r++){if(n[r]!="mix"&&n[r]!="mix_all"){t.push(n[r])}}}}});if(t.length>0){var n=t.filter(function(e,n){return t.indexOf(e)==n});var r=e(".rsmg_system_gallery_filters").length;if(r>0){e(".rsmg_system_gallery_filters li").each(function(){var t=e(this).attr("data-filter");if(t!="all"){if(n.indexOf(t)>-1){e(this).fadeIn(600)}}})}}}function rsmg_init_same_mix_size_system(e){var t=[];e("ul.rsmg_system_gallery li").each(function(){if(e(this).css("display")=="block"){t.push(e(this).find(".rsmg_system_container").offset().top)}});var n=t.filter(function(e,n){return t.indexOf(e)==n});for(var r=0;r<n.length;r++){var i=[];e("ul.rsmg_system_gallery li").each(function(){if(e(this).css("display")=="block"){if(n[r]==e(this).find(".rsmg_system_container").offset().top){var t=e(this).find(".rsmg_system_container").find("a > img").height();i.push(t)}}});var s=Math.max.apply(null,i);e("ul.rsmg_system_gallery li").each(function(){if(e(this).css("display")=="block"){if(n[r]==e(this).find(".rsmg_system_container").offset().top){e(this).find(".rsmg_system_container").animate({height:s+"px"},500)}}})}if(center_list_system){rsmg_align_center_system(e,true)}}function rsmg_align_center_system(e,t){e(".rsmg_gallery").each(function(){var n=[];var r=0;var i=0;e(this).find(".rsmg_system_gallery li").each(function(){if(e(this).css("display")!="none"){n.push(e(this).find(".rsmg_system_container").offset().top);if(r==0){if(responsive_system){r=e(this).width()}else{r=e(this).find(".rsmg_system_container").width()}}if(i==0){if(responsive_system){i=parseInt(e(this).css("margin-left"))}else{i=parseInt(e(this).css("margin-right"))}}if(responsive_system){var t=e(this).width();var s=e(this).find(".rsmg_system_container");var o=e(s).outerWidth();var u=(t-o)/2;e(s).css("margin-left",u)}}});var s=countOccurances(n);var o=0;for(var u=0;u<n.length;u++){if(o<s[n[u]]){o=s[n[u]]}}var a=e(".rsmg_system_gallery").parent().width();var f=e(".rsmg_system_container").css("padding-left");f=f.substr(0,f.length-2);f=parseInt(f);var l=e(".rsmg_system_container").css("padding-right");l=l.substr(0,l.length-2);l=parseInt(l);var c=2;if(responsive_system){var h=r}else{var h=r+f+l+c}var p=Math.floor(a/h);if(n.length>=p){o=p}if(o==0)o=1;if(o==1){var d=0}else{var d=i*o}if(d==0)d=f+l+c;if(responsive_system){var v=(a-((r+d)*(o-1)+r))/2}else{var v=(a-(r+d)*o)/2}if(p>o){v=v+i}v=v>0?v:0;if(t){e(this).find(".rsmg_system_gallery").animate({marginLeft:v+"px"},500)}else{e(this).find(".rsmg_system_gallery").css("margin-left",v)}})}jQuery(document).ready(function(e){if(responsive_system){e(".rsmg_gallery").hide();e(window).load(function(){e(".rsmg_gallery").show();rsmg_init_system_equal_size(jQuery)})}e("ul.rsmg_system_gallery li img").hover(function(){e(this).stop().animate({opacity:.7},"slow")},function(){e(this).stop().animate({opacity:1},"slow")});e(document).piroBox_system({piro_speed:700,bg_alpha:.5,piro_scroll:true,htmlClass:"system",selector:'a[class*="pirobox_gall_system"]'});if(!responsive_system)rsmg_init_system_equal_size(jQuery);rsmg_set_tags_filters_system(jQuery);if(responsive_system||center_list_system){e(window).resize(function(){if(responsive_system)rsmg_equal_size_responsive_system(e,false);if(center_list_system&&!responsive_system)rsmg_align_center_system(e,false)})}})