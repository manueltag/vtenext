/*********************************************************************************

** The contents of this file are subject to the vtiger Crmvillage.biz Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  Crmvillage.biz Open Source
 * The Initial Developer of the Original Code is Crmvillage.biz.
 * Portions created by vtiger are Copyright (C) Crmvillage.biz.
 * All Rights Reserved.
 ********************************************************************************/

//crmv@16703
var no_description;
var conf_sms_srvr_err_msg;
//crmv@16703e

//DS-CR VlMe 27.3.2008 
function Sms(module,oButton)
{
	allids = get_real_selected_ids(module).replace(/;/g,",");
	
	if (allids == "" || allids == ",")
	{
		alert(alert_arr.SELECT);
		return false;
	}
	
	if (allids.substr('0','1')==",")
	   allids = allids.substr('1');

	sendsms(module,allids,oButton);
}
//DS-END

function massSms(module)
{

	var select_options  =  document.getElementsByName('selected_id');
	x = select_options.length;
	var viewid =getviewId();		
	idstring = "";

	xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked)
		{
			idstring = select_options[i].value +";"+idstring
				xx++
		}
	}
	if (xx != 0)
	{
		document.getElementById('selected_ids').value=idstring;
	}
	else
	{
		alert(alert_arr.SELECT);
		return false;
	}
	document.massdelete.action="index.php?module=CustomView&action=SendSmsAction&return_module="+module+"&return_action=index&viewname="+viewid;
}


function set_return_sms(entity_id,sms_id,parentname,smsadd,perm){
	if(perm == 0 || perm == 3)
	{		
			alert(alert_arr.LBL_DONT_HAVE_SMS_PERMISSION);
			return false;
	}
	else
	{
	if(smsadd != '')
	{
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+sms_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+smsadd+'>,';
		window.opener.document.EditView.hidden_toid.value = smsadd+','+window.opener.document.EditView.hidden_toid.value;
		window.close();
	}else
	{
		alert('"'+parentname+alert_arr.DOESNOT_HAVE_AN_SMSID);
		return false;
	}
	}
}	

function rel_Sms(module,oButton,relmod)
{
	var select_options='';
	var allids='';
	var cookie_val=get_cookie(relmod+"_all");
	if(cookie_val != null)
		select_options=cookie_val;
	//Added to remove the semi colen ';' at the end of the string.done to avoid error.
	var x = select_options.split(";");
	var viewid ='';
	var count=x.length
		var idstring = "";
	select_options=select_options.slice(0,(select_options.length-1));

	if (count > 1)
	{
		idstring=select_options.replace(/;/g,':')
			allids=idstring;
	}
	else
	{
		alert(alert_arr.SELECT);
		return false;
	}
	sendsms(relmod,allids,oButton);
}

//crmv@16703
function validate_sendsms(idlist,module)
{
	var j=0;
	var chk_sms = document.SendSms.elements.length;
	var oFsendsms = document.SendSms.elements
	sms_type = new Array();
	for(var i=0 ;i < chk_sms ;i++)
	{
		if(oFsendsms[i].type != 'button')
		{
			if(oFsendsms[i].checked != false)
			{
				sms_type [j++]= oFsendsms[i].value;
			}
		}
	}
	if(sms_type != '')
	{
		var field_lists = sms_type.join(':');
		var url= 'index.php?module=Sms&action=SmsAjax&pmodule='+module+'&file=EditView&sendsms=true&field_lists='+field_lists;	//crmv@27096
		//openPopUp('xComposeSms',this,url,'createsmsWin',820,540,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
		displayComposeSms(module,idlist,field_lists,''); //crmv@fix
		hideFloatingDiv('roleLaySms');
		return true;
	}
	else
	{
		alert(alert_arr.SELECT_SMSID);
	}
}

function sendsms(module,idstrings,oButton)
{
	$('status').show();
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
                       	method: 'post',
                        postBody: "module=Sms&return_module="+module+"&action=SmsAjax&file=SmsSelect&idlist="+idstrings,
                        onComplete: function(response) {
							if(response.responseText == "No Sms Ids")
							{
								alert(alert_arr.NULL_SMSID);
								$('status').hide();
							}
							else if(response.responseText == "Sms Ids not permitted")
							{
								alert(alert_arr.NOTVALID_SMSID);
								$('status').hide();
							}
							else{
								jQuery('#sendsms_cont').html(response.responseText).show();
								showFloatingDiv('roleLaySms', null, {modal:false,center:true});
								$('status').hide();
							}
                        }
		}
	);
}

function displayComposeSms(sourcemodule,idstring,phonefields,other_url) {

	$('status').show();
	VtigerJS_DialogBox.block();
	
	var url = 'module=Sms&action=SmsAjax&ajax=true&file=EditView';			
	url += '&sendsms=true';			
	url += '&pmodule=' + encodeURIComponent(sourcemodule);			
	url += '&idlist=' + encodeURIComponent(idstring);
	url += '&field_lists='+ encodeURIComponent(phonefields);
	url += other_url;
	new Ajax.Request('index.php', {
		queue: {position: 'end', scope: 'command'},
		method: 'post',
		postBody:url,
		onComplete: function(response) {
						
			jQuery('#sendsms_cont').html(response.responseText);
			showFloatingDiv('smssendpopup');
			
			var options = {
				beforeSubmit:	smsValidate,  		// pre-submit callback 
			    success:		SuccessSmsCompose,  // post-submit callback 
		    	dataType:		'json'        		// 'xml', 'script', or 'json' (expected server response type) 
			};
			jQuery('#SendSms').ajaxForm(options);

			$('status').hide();
			VtigerJS_DialogBox.unblock();
		}
	});
}

function smsValidate() {

	$('status').show();

	if (document.SendSms.send_sms.value == 'true')
		ret = sms_validate(document.SendSms,'send');
	else
		ret = sms_validate(document.SendSms,'save');

	if (ret == false) {
		HideSmsCompose();
		return ret;
	}
	
	$('sendsms_cont').style.display='none';
	return true;
}

function HideSmsCompose() {
	$('status').hide();
	VtigerJS_DialogBox.unblock();
}

function SuccessSmsCompose(response) {
	HideSmsCompose();
	$('sendsms_cont').innerHTML='';
}

function sms_validate(oform,mode)
{
	if(trim(mode) == '')
	{
		return false;
	}
	if(oform.description.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		alert(no_description);
		return false;
	}
	if(mode == 'send')
	{
		return server_check(oform);
	}else if(mode == 'save')
	{
		document.SendSms.action.value='Save';
	}else
	{
		return false;
	}
}

function server_check(oform)
{
	res = getFile('index.php?module=Sms&action=SmsAjax&file=Save&ajax=true&server_check=true');
	res = trim(res);
	if(res == 'SUCESS')
	{
		//document.SendSms.send_sms.value='true';
		document.SendSms.action.value='Save';
	}else
	{
		alert(conf_sms_srvr_err_msg);
		return false;
	}
}
//crmv@16703e