/*********************************************************************************

** The contents of this file are subject to the vtiger Crmvillage.biz Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  Crmvillage.biz Open Source
* The Initial Developer of the Original Code is Crmvillage.biz.
* Portions created by vtiger are Copyright (C) Crmvillage.biz.
* All Rights Reserved.
********************************************************************************/

function Fax(module,oButton) {
	
	var allids = get_real_selected_ids(module).replace(/;/g,",");
	
	if (allids == "" || allids == ",") {
		alert(alert_arr.SELECT);
		return false;
	}
	
	if (allids.substr('0','1')==",") {
		allids = allids.substr('1');
	}

	sendfax(module,allids,oButton);
}

function set_return_fax(entity_id,fax_id,parentname,faxadd,perm){
	if(perm == 0 || perm == 3)
	{		
			alert(alert_arr.LBL_DONT_HAVE_FAX_PERMISSION);
			return false;
	}
	else
	{
	if(faxadd != '')
	{
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+fax_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+faxadd+'>,';
		window.opener.document.EditView.hidden_toid.value = faxadd+','+window.opener.document.EditView.hidden_toid.value;
		window.close();
	}else
	{
		alert('"'+parentname+alert_arr.DOESNOT_HAVE_AN_FAXID);
		return false;
	}
	}
}	


function validate_sendfax(idlist,module) {
	var j=0;
	var chk_fax = document.SendFax.elements.length;
	var oFsendfax = document.SendFax.elements
	var fax_type = new Array();
	
	for(var i=0 ;i < chk_fax ;i++)
	{
		if(oFsendfax[i].type != 'button')
		{
			if(oFsendfax[i].checked != false)
			{
				fax_type [j++]= oFsendfax[i].value;
			}
		}
	}
	
	if(fax_type != '')
	{
		var field_lists = fax_type.join(':');
		var url= 'index.php?module=Fax&action=FaxAjax&pmodule='+module+'&file=EditView&sendfax=true&field_lists='+field_lists+'&idlist='+idlist;	//crmv@27096 crmv@55198
		openPopUp('xComposeFax',this,url,'createfaxWin',820,389,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
		fninvsh('roleLayFax');
		return true;
	}
	else
	{
		alert(alert_arr.SELECT_FAXID);
	}
}

function sendfax(module,idstrings,oButton) {
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: "module=Fax&return_module="+module+"&action=FaxAjax&file=faxSelect&idlist="+idstrings,
			onComplete: function(response) {
				if(response.responseText == "Fax Ids not permitted" || response.responseText == "No Fax Ids") {
					var url= 'index.php?module=Fax&action=FaxAjax&pmodule='+module+'&file=EditView&sendfax=true';
					openPopUp('xComposeFax',this,url,'createfaxWin',820,389,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
				} else {
					jQuery('#sendfax_cont').html(response.responseText);
					showFloatingDiv('roleLayFax', null, {modal:false,center:true});
				}	
			}
		}
	);
}

function rel_Fax(module,oButton,relmod){
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
	sendfax(relmod,allids,oButton);
}