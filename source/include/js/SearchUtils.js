/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

// crmv@120738

var SearchUtils = SearchUtils || {
	
	clearText: function(elem, canc) {
		var jelem = jQuery(elem);
		var jcanc = jQuery(canc);
		var rest = jQuery.data(elem, 'restored');
		if (rest == undefined || rest == true) {
			jelem.val('');
			jcanc.show();
			jQuery.data(elem, 'restored', false);
		}
	},
	
	restoreDefaultText: function(elem, deftext, canc) {
		var jelem = jQuery(elem);
		var jcanc = jQuery(canc);
		if (jelem.val() == '') {
			jcanc.hide();
			jQuery.data(elem, 'restored', true);
			jelem.val(deftext);
		}
	},
	
	cancelSearchText: function(elem, deftext) {
		var jelem = jQuery(elem);
		jelem.val('');
		restoreDefaultText(jelem.get(0), deftext);
	},
	
};