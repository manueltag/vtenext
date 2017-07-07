/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/* crmv@43864	crmv@43050	crmv@43448	crmv@55506 */

function commentsLinkModule(module, crmid, entityname) {
	var parentid = jQuery('#from_crmid').val(),
		parent_win = parent.popup_opener;

	if (parentid > 0) {
		linkModules('ModComments', parentid, module, crmid, {},
			function (data) {
				var uikey = jQuery('#uikey_from').val(),
					commentid = jQuery('#from_crmid').val();

				if (uikey && commentid && parent_win) {
					parent_win.ModCommentsCommon.reloadComment(uikey, null, commentid);
				}
				closePopup();
			}
		);
	} else {
		// I am creating a new comment
		var container = parent_win.jQuery('#editareaModComm');
		container.find('.commentAddLink').hide();
		container.find('#ModCommentsParentId').val(crmid);
		container.find('#ModCommentsNewRelatedLabel').show();
		if (entityname) {
			container.find('#ModCommentsNewRelatedName').html(entityname).show();
		}
		closePopup();
	}
}

function commentsCreateModule(module) {
	/*
	LPOP.create(module, function(mod, recordid) {
		if (recordid > 0) {
			commentsLinkModule(mod, recordid);
			return false;
		}
	});
	*/
	LPOP.create(module, {});
}