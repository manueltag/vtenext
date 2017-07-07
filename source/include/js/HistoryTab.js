/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@104566 crmv@104975 */

if (typeof(HistoryTabScript) == 'undefined') {
	var HistoryTabScript = {
		
		showTab: function(module, record) {
			var me = this;
			if (jQuery('#HistoryTab').length > 0) {
				jQuery('#HistoryTab').show();
				return;
			}
			jQuery('#DetailExtraBlock').append('<div id="HistoryTab" class="detailTabsMainDiv" style="display:none"></div>');
			me.getHistory(module, record);
		},
		
		hideTab: function() {
			jQuery('#HistoryTab').hide();
		},
		
		getHistory: function(module, record) {
			jQuery('#status').show();
			jQuery.ajax({
				'url': 'index.php?module=ChangeLog&action=ChangeLogAjax&file=HistoryTab&pmodule='+module+'&record='+record,
				'type': 'POST',
				success: function(data) {
					jQuery('#status').hide();
					jQuery('#HistoryTab').html(data);
				}
			});
		},
	}
}