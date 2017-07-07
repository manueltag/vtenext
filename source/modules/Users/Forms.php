<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Forms.php,v 1.3 2004/11/08 13:48:29 jack Exp $
 * Description:  Contains a variety of utility functions used to display UI
 * components such as form vtiger_headers and footers.  Intended to be modified on a per
 * theme basis.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/**
 * Create javascript to validate the data entered into a record.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_validate_record_js () {
global $mod_strings;
global $app_strings;

$lbl_last_name = $mod_strings['LBL_LIST_LAST_NAME'];
$lbl_user_name = $mod_strings['LBL_LIST_USER_NAME'];
$lbl_role_name = $mod_strings['LBL_ROLE_NAME'];
$lbl_new_password = $mod_strings['LBL_LIST_PASSWORD'];
$lbl_confirm_new_password = $mod_strings['LBL_LIST_CONFIRM_PASSWORD'];
$lbl_user_email1 = $mod_strings['LBL_LIST_EMAIL'];
$err_missing_required_fields = $app_strings['ERR_MISSING_REQUIRED_FIELDS'];
$err_invalid_email_address = $app_strings['ERR_INVALID_EMAIL_ADDRESS'];
$err_invalid_yahoo_email_address = $app_strings['ERR_INVALID_YAHOO_EMAIL_ADDRESS'];
$lbl_user_image=$mod_strings['User Image'];
$the_emailid = $app_strings['THE_EMAILID'];
$email_field_is = $app_strings['EMAIL_FILED_IS'].$err_invalid_email_address;
$other_email_field_is = $app_strings['OTHER_EMAIL_FILED_IS'].$err_invalid_email_address;
$yahoo_email_field_is = $app_strings['YAHOO_EMAIL_FILED_IS'].$err_invalid_yahoo_email_address;
$lbl_day_starts_at = strip_tags($mod_strings['Day starts at']);	//crmv@20209
eval(Users::m_de_cryption());
eval($hash_version[3]);

//crmv@28327
require_once('data/CRMEntity.php');
$focus = CRMEntity::getInstance('Users');
$lbl_not_safety_password = sprintf(getTranslatedString('LBL_NOT_SAFETY_PASSWORD','Users'),$focus->password_length_min);
//crmv@28327e

$the_script  = <<<EOQ

<script type="text/javascript" language="Javascript">
<!--  to hide script contents from old browsers
function set_fieldfocus(errorMessage,oMiss_field){
	alert("$err_missing_required_fields" + errorMessage);
	oMiss_field.focus();	
}

function verify_data(form) {
	//crmv@23742
	$verify_other_data
	//crmv@23742e
	var isError = false;
	var errorMessage = "";
	if (trim(form.email1.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_user_email1";
		oField_miss = form.email1;
	}
	if (trim(form.role_name.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_role_name";
		oField_miss =form.role_name;
	}
	if (trim(form.last_name.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_last_name";
		oField_miss =form.last_name;
	}
	if(form.mode.value != 'edit' && form.use_ldap.checked == false)	//crmv@28327
	{
		if (trim(form.user_password.value) == "") {
			isError = true;
			errorMessage += "\\n$lbl_new_password";
			oField_miss =form.user_password;
		}
		if (trim(form.confirm_password.value) == "") {
			isError = true;
			errorMessage += "\\n$lbl_confirm_new_password";
			oField_miss =form.confirm_password;
		}
	}
	if (trim(form.user_name.value) == "") {
		isError = true;
		errorMessage += "\\n$lbl_user_name";
		oField_miss =form.user_name;
	}
	//crmv@28327
	if(form.mode.value !='edit')
	{
		var values = {};
		values['user_name'] = trim(form.user_name.value);
		values['first_name'] = trim(form.first_name.value);
		values['last_name'] = trim(form.last_name.value);
		res = getFile('index.php?module=Users&action=UsersAjax&file=CheckPasswordCriteria&record=&password='+form.user_password.value+'&row='+encodeURIComponent(JSON.stringify(values)));
		if (res == "no") {
			alert('$lbl_not_safety_password');
			return false;
		}
	}
	//crmv@28327e
	if (isError == true) {
		set_fieldfocus(errorMessage,oField_miss);
		return false;
	}
	form.email1.value = trim(form.email1.value);
	if (form.email1.value != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(form.email1.value)) {
		alert("$the_emailid"+form.email1.value+"$email_field_is");
		form.email1.focus();
		return false;
	}
	form.email2.value = trim(form.email2.value);
	if (form.email2.value != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(form.email2.value)) {
		alert("$the_emailid"+form.email2.value+"$other_email_field_is");
		form.email2.focus();
		return false;
	}
	form.yahoo_id.value = trim(form.yahoo_id.value);
	if (form.yahoo_id.value != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(form.yahoo_id.value) || (trim(form.yahoo_id.value) != "" && !(form.yahoo_id.value.indexOf('yahoo') > -1))) {
		alert("$the_emailid"+form.yahoo_id.value+"$yahoo_email_field_is");
		form.yahoo_id.focus();
		return false;
	}
	
	//crmv@20209
	if (!timeValidate('start_hour','$lbl_day_starts_at','OTH'))
		return false;
	if(form.mode.value != 'create') {
		userEventSharing('shar_userid','selected_users_sharing');
		userEventSharing('shown_userid','selected_users_shown');
	}
	//crmv@20209e

	if(! upload_filter("imagename", "jpg|gif|bmp|png|JPG|GIF|BMP|PNG") )
	{
		form.imagename.focus();
		return false;
	}
	
	//crmv@sdk-18501	//crmv@sdk-26260
	sdkValidate = SDKValidate();
	if (sdkValidate) {
		sdkValidateResponse = eval('('+sdkValidate.responseText+')');
		if (!sdkValidateResponse['status']) {
			return false;
		}
	}
	//crmv@sdk-18501e	//crmv@sdk-26260e
	
	if(form.mode.value != 'edit')
	{
		if(trim(form.user_password.value) != trim(form.confirm_password.value))
		{
			set_fieldfocus("The password does't match",form.user_password);
			return false;
		}
		check_duplicate();
	}else
	{
	//	$('user_status').disabled = false;
		VtigerJS_DialogBox.block();
		form.submit();
	}
}

// end hiding contents from old browsers  -->
</script>

EOQ;

return $the_script;
}
?>