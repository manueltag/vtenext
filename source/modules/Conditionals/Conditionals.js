/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *
 ********************************************************************************/
function load_field_permissions_table(){

	var url = "";
	url += "&chk_module="+escape(getObj('module_name').value);
	
	var data = "file=EditViewAjax&module=Conditionals&action=ConditionalsAjax"+url;
	getObj("field_permissions_table").innerHTML = "loading...";
	
	new Ajax.Request(
			'index.php',
	                {queue: {position: 'end', scope: 'command'},
	                        method: 'post',
	                        postBody: data,
	                        onComplete: function(response) {
	                        
	                        	//alert(response.responseText);
	                	
	                			getObj("field_permissions_table").innerHTML = response.responseText;
	                			getObj("field_permissions_table").style.display = "inline";
	                        }
	                }
	            );
	getObj("field_permissions_table").style.display = "inline";
}
function fnAddProductRow(module,chk_fieldname,chk_criteria_id,chk_field_value){
	getObj('workflow_loading').style.display='block';
	getObj('add_rule').style.display='none';
	rowCnt++;
	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;
	var count = eval(prev);//As the table has two headers, we should reduce the count
	var row = tableName.insertRow(prev);
	row.id = "row"+count;
	row.style.verticalAlign = "top";
	url = 'module=Conditionals&action=ConditionalsAjax&file=GetConditionalRow&conditional_module='+module+'&rowCnt='+count;
	if (chk_fieldname != undefined) url += '&chk_fieldname='+chk_fieldname;
	if (chk_criteria_id != undefined) url += '&chk_criteria_id='+chk_criteria_id;
	if (chk_field_value != undefined) url += '&chk_field_value='+chk_field_value;
	new Ajax.Request(
		'index.php',
        {queue: {position: 'end', scope: 'command'},
	        method: 'post',
	        postBody: url,
	        onComplete: function(response) {
	        	jQuery('#'+row.id).html(response.responseText);	//crmv@17715
	        	getObj('workflow_loading').style.display='none';
	        	getObj('add_rule').style.display='block';
	        }
        }
    );
	return count;
}
function deleteRow(i) {
	rowCnt--;
	document.getElementById("row"+i).style.display = 'none';
	document.getElementById('deleted'+i).value = 1;
}
function verify_data_conditionals(form) {
	// crmv@42024
	var count = jQuery('#proTab tr:visible').length;	//crmv@45813
	getObj('total_conditions').value = count;

	var isError = false;
	var errorMessage = "";
	if (trim(form.workflow_name.value) == "") {
		isError = true;
		errorMessage += "\n"+alert_arr.LBL_FPOFV_RULE_NAME;
		oField_miss = form.workflow_name;
	}
	if (count <= 0) {
		isError = true;
		errorMessage += "\n"+alert_arr.LBL_LEAST_ONE_CONDITION;
		oField_miss = form.workflow_name;
	}
	// crmv@42024e
	if (isError == true) {
		set_fieldfocus(errorMessage,oField_miss);
		return false;
	}
	return true;
}
// crmv@77249
function resetConditions(module, field) {
	jQuery('#proTab').html('');	//crmv@18373
	rowCnt = 0;
	fnAddProductRow(module, field);
	getObj("field_permissions_table").innerHTML = '';
	getObj("field_permissions_table").style.display = 'none';
}
// crmv@77249e
//crmv@17715
function set_fieldfocus(errorMessage,oMiss_field) {
	alert(alert_arr.MISSING_REQUIRED_FIELDS+errorMessage);
	oMiss_field.focus();
}
//crmv@17715e