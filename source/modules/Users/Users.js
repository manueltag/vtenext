/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

loadFileJs('include/js/Mail.js');
loadFileJs('include/js/Fax.js');
loadFileJs('include/js/Sms.js');

function set_return(user_id, user_name) {
	//crmv@21048m	//crmv@30408
	if(top.jQuery('div#addEventInviteUI').css('display') == 'block'){
		var linkedMod = 'Users';
		var entity_id = user_id;
		var strVal = user_name;
		if (top.jQuery('#addEventInviteUI').contents().find('#' + entity_id + '_' + linkedMod + '_dest').length < 1) {
			strHtlm = '<tr id="' + entity_id + '_' + linkedMod + '_dest' + '" onclick="checkTr(this.id)">' +
			'<td align="center" style="display:none;"><input type="checkbox" value="' + entity_id + '_' + linkedMod + '"></td>' +
			'<td nowrap align="left" class="parent_name" style="width:100%">' + strVal + '</td>' +
			'</tr>';
			top.jQuery('#selectedTable').append(strHtlm);
			jQuery('#parent_id_link_contacts').val(jQuery('#parent_id_link_contacts').val() + ';' + entity_id);
		}
	}
	else{
		//crmv@29190
		var formName = getReturnFormName();
		var form = getReturnForm(formName);
		//crmv@29190e
		//crmv@42247
		if(form.elements["reports_to_id_display"]){
			form.elements["reports_to_id_display"].value = user_name; 
			var name_disabled = form.elements["reports_to_id_display"];
		}
		if(form.elements["reports_to_id"]){
			form.elements["reports_to_id"].value = user_id; 
			var id_disabled = form.elements["reports_to_id"];
		}
		if(form.elements["newresp_display"]){
			form.elements["newresp_display"].value = user_name; 
			var name_disabled = form.elements["newresp_display"];
		}
		if(form.elements["newresp"]){
			form.elements["newresp"].value = user_id; 
			var id_disabled = form.elements["newresp"];
		}
		disableReferenceField(name_disabled,id_disabled);
		//crmv@42247e
	}
	//crmv@21048me	//crmv@30408e
}

//ds@28 workflow
function set_return_specific(user_id, user_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.user_name.value = user_name;
	form.user_id.value = user_id;
	disableReferenceField(form.user_name,form.user_id);	//crmv@29190
}
//ds@28e

//crmv@35153
function getUserName(id) {
	return getFile('index.php?module=Users&action=UsersAjax&file=GetUserName&record='+id);
}
//crmv@35153e