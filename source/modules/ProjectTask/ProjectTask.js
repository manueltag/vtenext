/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ************************************************************************************/

function set_return(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check); //crmv@29190
}

//crmv@104562
function toggleWorkingDays(field) {
	if (field.checked) {
		jQuery('#working_days').attr('readonly', true);
		jQuery('#working_days').parent('div').attr('class','dvtCellInfoOff');
	} else {
		jQuery('#working_days').attr('readonly', false);
		jQuery('#working_days').parent('div').attr('class','dvtCellInfo');
		jQuery('#working_days').focus();
	}
}
//crmv@104562e