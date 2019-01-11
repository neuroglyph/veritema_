/**
 * Abivia Super Table Plugin.
 *
 * @package AbiviaSuperTable
 * @copyright (C) 2011 by Abivia Inc. All rights reserved.
 * @license GNU/GPL
 * @link http://www.abivia.net/
 */

window.addEvent('domready', function() {
	var tables = $$('.supertable');
	var highlight = new Array();
	tables.each(function(table, tableInd) {
		var columns = table.getElements('.supertable-col');
		columns.each(function(col, colInd) {
			if (col.hasClass('supertable-active')) {
				highlight[tableInd] = colInd;
			}
            if (!col.hasClass('supertable-col-rowhead')) {
                col.addEvents({
                    'mouseenter': function() {
                        columns.removeClass('supertable-active');
                        this.addClass('supertable-active');
                    }
                });
            }
		});
		var headcolumns = table.getElements('.supertable-col-rowhead');
		headcolumns.each(function(col, colInd) {
			col.addEvents({
				'mouseleaave': function() {
					columns.removeClass('supertable-active');
					table.getElements('.supertable-col')[highlight[tableInd]].addClass('supertable-active');
				}
			});
		});
		if ($chk(highlight[tableInd])) {
			table.addEvent('mouseleave', function() {
				table.getElements('.supertable-col').removeClass('supertable-active');
				table.getElements('.supertable-col')[highlight[tableInd]].addClass('supertable-active');
			});
		}
	});
});
