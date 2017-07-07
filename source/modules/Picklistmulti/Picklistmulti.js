/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  crmvillage.biz
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by vtiger are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *
 ********************************************************************************/
/**
 * this function is used to update the page with the new module selected
 */
function changeModule(){
	$("status").style.display="inline";
	var module=getObj('pickmodule').value;
	$("status").style.display="inline";
	var result = getFile("index.php?module=Picklistmulti&action=PicklistmultiAjax&file=LoadField&module_name="+encodeURIComponent(module))
	 result = eval('(' + result + ')');
	if (result == null){
		 rm_all_opt('picklist_field');
		 add_opt('picklist_field',"","{$APP.LBL_NONE}");
	}
	else {
		var field_obj = getObj('picklist_field');
		 resetpicklist('picklist_field');
		 for (var key in result){
	    	add_opt('picklist_field',values[key],key);
		 }
		fieldname = field_obj.value;
		fieldmodule = module_obj.value;
		fieldlabel = field_obj.options[field_obj.selectedIndex].text;	
		jQuery("#table_picklist").jqGrid('setGridParam',{editurl:"index.php?module=Picklistmulti&action=PicklistmultiAjax&file=edit&field="+fieldname+"&field_module="+fieldmodule,url:'index.php?module=Picklistmulti&action=PicklistmultiAjax&file=load&field='+fieldname+'&field_module='+fieldmodule}).jqGrid('setCaption',fieldlabel).trigger('reloadGrid');
	}
	$("status").style.display="none";
}
/**
 * this function is used to update the page with the new field selected
 */
function changeField(){
	var module_obj=getObj('pickmodule');
	var field_obj = getObj('picklist_field');
	fieldname = field_obj.value;
	fieldmodule = module_obj.value;
	fieldlabel = field_obj.options[field_obj.selectedIndex].text;
	jQuery("#table_picklist").jqGrid('setGridParam',{editurl:"index.php?module=Picklistmulti&action=PicklistmultiAjax&file=edit&field="+fieldname+"&field_module="+fieldmodule,url:'index.php?module=Picklistmulti&action=PicklistmultiAjax&file=load&field='+fieldname+'&field_module='+fieldmodule}).jqGrid('setCaption',fieldlabel).trigger('reloadGrid');
}