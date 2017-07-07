/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

loadFileJs('include/js/Inventory.js');

function set_return(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);	//crmv@29190
}

function set_return_specific(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.elements["quote_id_display"].value = product_name;
	form.quote_id.value = product_id;
	disableReferenceField(form.elements["quote_id_display"],form.quote_id,form.quote_id_mass_edit_check);	//crmv@29190
	if (enableAdvancedFunction(form)) { //crmv@29190
		parent.VtigerJS_DialogBox.block();	//crmv@29190
		//crmv@21048m
		form.action.value = 'EditView';
		form.convertmode.value = 'update_quote_val';
		form.submit();
		//crmv@21048me
	}
}

function add_data_to_relatedlist(entity_id,recordid) {
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=Accounts&entityid="+entity_id+"&parentid="+recordid;
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

//crmv@44323
function ReviewQuote(record) {
	document.DetailView.return_module.value='Quotes';
	document.DetailView.return_action.value='DetailView';
	document.DetailView.convertmode.value='reviewquote';
	document.DetailView.module.value='Quotes';
	document.DetailView.action.value='EditView';
	document.DetailView.isDuplicate.value='true';
	//document.DetailView.return_id.value=record;
	document.DetailView.submit();
}
//crmv@44323e