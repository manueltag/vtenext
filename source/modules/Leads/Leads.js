/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/
 
/* crmv@19438 crmv@29463 crmv@41880 */

loadFileJs('include/js/Mail.js');
loadFileJs('include/js/Fax.js');
loadFileJs('include/js/Sms.js');
loadFileJs('include/js/Merge.js');

function verifyConvertLead(form) {
	//crmv@sdk-18501	//crmv@sdk-26260
	sdkValidate = SDKValidate(form);
	if (sdkValidate) {
		sdkValidateResponse = eval('('+sdkValidate.responseText+')');
		if (!sdkValidateResponse['status']) {
			return false;
		}
	}
	//crmv@sdk-18501e	//crmv@sdk-26260e
	if (!AjaxDuplicateValidate('Accounts',form)) return false;

	if(! form.createpotential.checked == true){
        if (trim(form.potential_name.value) == ""){
            alert(alert_arr.OPPORTUNITYNAME_CANNOT_BE_EMPTY);
			return false;	
		}
		if(form.closingdate_mandatory != null && form.closingdate_mandatory.value == '*'){
			if (form.closedate.value == ""){
	        	alert(alert_arr.CLOSEDATE_CANNOT_BE_EMPTY);
				return false;	
			}
		}
		if (form.closedate.value != "" ){
			var x = dateValidate('closedate','Potential Close Date','DATE');
			if(!x){
				return false;
			}
		}
		if(form.amount_mandatory.value == '*'){
			if (form.potential_amount.value == ""){
	            alert(alert_arr.AMOUNT_CANNOT_BE_EMPTY);
				return false;					
			}
		}	
		intval= intValidate('potential_amount','Potential Amount');
		if(!intval){
			return false;
		}
		return true;
	}
	else{	
		return true;
	}
}

function togglePotFields(form)
{
	if (form.createpotential.checked == true)
	{
		form.potential_name.disabled = true;
		form.closedate.disabled = true;
		form.potential_amount.disabled = true;
		form.potential_sales_stage.disabled = true;
		
	}
	else
	{
		form.potential_name.disabled = false;
		form.closedate.disabled = false;
		form.potential_amount.disabled = false;
		form.potential_sales_stage.disabled = false;
		form.potential_sales_stage.value="";
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

function set_return_specific(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.lead_name.value = product_name;
	form.lead_id.value = product_id;
	disableReferenceField(form.lead_name,form.lead_id,form.lead_id_mass_edit_check);	//crmv@29190
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

function add_data_to_relatedlist(entity_id,recordid) {
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=leads&entityid="+entity_id+"&parentid="+recordid;
}
//added by rdhital/Raju for emails
function submitform(id){
	document.massdelete.entityid.value=id;
	document.massdelete.submit();
}	

function searchMapLocation(addressType)
{
        var mapParameter = '';
        if (addressType == 'Main')
        {
		if(fieldname.indexOf('lane') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('lane')]))
	                        mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('lane')]).innerHTML+' ';
		}
		if(fieldname.indexOf('pobox') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('pobox')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('pobox')]).innerHTML+' ';
		}
		if(fieldname.indexOf('city') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('city')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('city')]).innerHTML+' ';
		}
		if(fieldname.indexOf('state') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('state')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('state')]).innerHTML+' ';
		}
		if(fieldname.indexOf('country') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('country')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('country')]).innerHTML+' ';
		}
		if(fieldname.indexOf('code') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('code')]))
	                        mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('code')]).innerHTML+' ';
		}
        }
	mapParameter = removeHTMLFormatting(mapParameter);
	//crmv@30064
    //openPopup('http://maps.google.com/maps?q='+mapParameter+'&output=embed','goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250','','','','','nospinner');//crmv@21048m //crmv@22065 //crmv@23446
    window.open('http://maps.google.com/maps?q='+mapParameter,'_blank');
    //crmv@30064e
}

function selectTransferTo(module){
	if(module=='Accounts'){
		if(document.getElementById('transfertoacc').checked){
			$('account_block').style.display="block";
			document.getElementById('select_account').checked="checked";
		}
	}
	if(module=='Contacts'){
		if(document.getElementById('transfertocon').checked){
			$('contact_block').style.display="block";
			document.getElementById('select_contact').checked="checked";
		}
	}
}

function verifyConvertLeadData(form) {
	var convertForm=document.ConvertLead;
	var no_ele=convertForm.length;
	
	//crmv@sdk-18501	//crmv@sdk-26260
	sdkValidate = SDKValidate(form);
	if (sdkValidate) {
		sdkValidateResponse = eval('('+sdkValidate.responseText+')');
		if (!sdkValidateResponse['status']) {
			return false;
		}
	}
	//crmv@sdk-18501e	//crmv@sdk-26260e
	if (!AjaxDuplicateValidate('Accounts',form)) return false;
	
	if((form.select_account!=null)&&(form.select_contact!=null)){
		if(!(form.select_account.checked || form.select_contact.checked)){
			alert(alert_arr["ERR_SELECT_EITHER"]);
			return false;
		}
	}
	else if(form.select_account!=null){
		if(!form.select_account.checked){
			alert(alert_arr["ERR_SELECT_ACCOUNT"]);
			return false;
		}
	}
	else if(form.select_contact!=null){
		if(!form.select_contact.checked){
			alert(alert_arr["ERR_SELECT_CONTACT"]);
			return false;
		}
	}

	if(form.select_account!=null && form.select_account.checked){
		for(i=0;i<no_ele;i++){
			if((convertForm[i].getAttribute('module')=='Accounts') && (convertForm[i].getAttribute('record')=='true')){
				if(convertForm[i].value==''){
					alert(alert_arr["ERR_MANDATORY_FIELD_VALUE"])
					return false;
				}
			}
		}
	}
	if(form.select_potential!=null && form.select_potential.checked){
		for(i=0;i<no_ele;i++){
			if((convertForm[i].getAttribute('module')=='Potentials') && (convertForm[i].getAttribute('record')=='true')){
				if(convertForm[i].value==''){
					alert(alert_arr["ERR_MANDATORY_FIELD_VALUE"])
					return false;
				}
			}
		}
		if(form.jscal_field_closedate!=null && form.jscal_field_closedate.value!=''){
			if(!dateValidate('closingdate',alert_arr['LBL_CLOSE_DATE'],'date')){
				return false;
			}
		}
		if(form.amount.value!=null && isNaN(form.amount.value)){
			alert(alert_arr["ERR_POTENTIAL_AMOUNT"]);
			return false;
		}
	}
	if(form.select_contact!=null && form.select_contact.checked){
		for(i=0;i<no_ele;i++){
			if((convertForm[i].getAttribute('module')=='Contacts') && (convertForm[i].getAttribute('record')=='true')){
				if(convertForm[i].value==''){
					alert(alert_arr["ERR_MANDATORY_FIELD_VALUE"])
					return false;
				}
			}
		}
		var emailpattern=/^[a-zA-Z0-9]+([!"#$%&'()*+,./:;<=>?@\^_`{|}~-]?[a-zA-Z0-9])*@[a-zA-Z0-9]+([\_\-\.]?[a-zA-Z0-9]+)*\.([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)?$/;
		if(form.email.value!=''){
			if(!patternValidate('email',alert_arr['LBL_EMAIL'],'email')){
				return false;
			}
		}
	}

	if(document.getElementById('transfertoacc').checked && !form.select_account.checked){
		alert(alert_arr["ERR_TRANSFER_TO_ACC"]);
		return false;
	}
	if(document.getElementById('transfertocon').checked && !form.select_contact.checked){
		alert(alert_arr["ERR_TRANSFER_TO_CON"]);
		return false;
	}
	return true;
}

function callConvertLeadDiv(id) {
	$('status').show();
    new Ajax.Request(
        'index.php',
        {queue: {position: 'end', scope: 'command'},
            method: 'post',
            postBody: 'module=Leads&action=LeadsAjax&file=ConvertLead&record='+id,
            onComplete: function(response) {
            	jQuery("#convertleaddiv").css('zIndex',findZMax()+1);
                $("convertleaddiv").innerHTML=response.responseText;
				eval($("conv_leadcal").innerHTML);
				eval($("drag_conv_leadcal").innerHTML);
				$('status').hide();
            }
        }
    );
}