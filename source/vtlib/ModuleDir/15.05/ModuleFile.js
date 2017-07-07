/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

//crmv@29190 crmv@69568
function set_return(product_id, product_name) {
	var formName = getReturnFormName();
	var form = (formName ? getReturnForm(formName) : null);
	if (form) {
		form.parent_name.value = product_name;
		form.parent_id.value = product_id;
		disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);
	}
}
//crmv@29190e crmv@69568e
