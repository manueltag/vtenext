/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

loadFileJs('include/js/Merge.js');

function verify_data(form) {
	if(! form.createpotential.checked == true)
	{
		if (form.potential_name.value == "")
		{
			alert(alert_arr.OPPORTUNITYNAME_CANNOT_BE_EMPTY);
			return false;	
		}
		if (form.closedate.value == "")
		{
			alert(alert_arr.CLOSEDATE_CANNOT_BE_EMPTY);
			return false;	
		}
		return dateValidate('closedate','Potential Close Date','GECD');
	}
	return true;
}

function togglePotFields(form)
{
	if (form.createpotential.checked == true)
	{
		form.potential_name.disabled = true;
		form.closedate.disabled = true;
		
	}
	else
	{
		form.potential_name.disabled = false;
		form.closedate.disabled = false;
	}	

}

function toggleAssignType(currType)
{
	if (currType=="U")
	{
		getObj("assign_user").style.display="block"
		getObj("assign_team").style.display="none"
	}
	else
	{
		getObj("assign_user").style.display="none"
		getObj("assign_team").style.display="block"
	}
}

function set_return(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);	//crmv@29190
}

function set_return_todo(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	if (formName != 'QcEditView') {
		formName = 'createTodo';
	}
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);	//crmv@29190
}

//crmv@56233
function doNotImportAnymore(module,record,view) {
	$('status').show();
	var mode = 'spam';
	if (view == 'MassListView') {
		mode = 'mass_spam';
		get_real_selected_ids(module);
	}
	jQuery.ajax({
		url: 'index.php?module=HelpDesk&action=HelpDeskAjax&file=DoNotImportAnymore&mode='+mode+'&record='+record,
		type: 'POST',
		success: function(data) {
			if (data.indexOf("ERROR::") > -1) {
				var str = data.split("ERROR::");
				alert(str[1]);
			} else {
				if (view == 'ListView' || view == 'MassListView') window.location.reload();
				else if (view == 'DetailView') window.location.href='index.php?module=HelpDesk&action=ListView';
			}
			$('status').hide();
		}
	});
}
//crmv@56233e