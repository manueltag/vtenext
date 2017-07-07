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
loadFileJs('include/js/Mail.js');
loadFileJs('include/js/Fax.js');
loadFileJs('include/js/Merge.js');

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
	form.account_name.value = product_name;
	form.account_id.value = product_id;
	disableReferenceField(form.account_name,form.account_id,form.account_id_mass_edit_check);	//crmv@29190
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
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=Accounts&entityid="+entity_id+"&parentid="+recordid;
}

function set_return_address(account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country,bill_pobox,ship_pobox) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.elements["account_id_display"].value = account_name; 
	form.elements["account_id"].value = account_id;
	disableReferenceField(form.elements["account_id_display"],form.elements["account_id"],form.elements["account_id_mass_edit_check"]);	//crmv@29190
	if (enableAdvancedFunction(form)) {	//crmv@29190
		//Ask the user to overwite the address or not - Modified on 06-01-2007
		if(confirm(alert_arr.OVERWRITE_EXISTING_ACCOUNT1+account_name+alert_arr.OVERWRITE_EXISTING_ACCOUNT2))
		{
			if(typeof(form.bill_street) != 'undefined')
				form.bill_street.value = bill_street;
			if(typeof(form.ship_street) != 'undefined')
				form.ship_street.value = ship_street;
			if(typeof(form.bill_city) != 'undefined')
				form.bill_city.value = bill_city;
			if(typeof(form.ship_city) != 'undefined')
				form.ship_city.value = ship_city;
			if(typeof(form.bill_state) != 'undefined')
				form.bill_state.value = bill_state;
			if(typeof(form.ship_state) != 'undefined')
				form.ship_state.value = ship_state;
			if(typeof(form.bill_code) != 'undefined')
				form.bill_code.value = bill_code;
			if(typeof(form.ship_code) != 'undefined')
				form.ship_code.value = ship_code;
			if(typeof(form.bill_country) != 'undefined')
				form.bill_country.value = bill_country;
			if(typeof(form.ship_country) != 'undefined')
				form.ship_country.value = ship_country;
			if(typeof(form.bill_pobox) != 'undefined')
				form.bill_pobox.value = bill_pobox;
			if(typeof(form.ship_pobox) != 'undefined')
				form.ship_pobox.value = ship_pobox;
		}
		//crmv@21048me
	}
}
//crmv@14536
function set_return_account(account_id, account_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.elements["account_name"].value = account_name;
	form.elements["account_id"].value = account_id;
	disableReferenceField(form.elements["account_name"],form.elements["account_id"],form.elements["account_id_mass_edit_check"]);	//crmv@29190
}
//crmv@14536e
//added to populate address
function set_return_contact_address(account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country,bill_pobox,ship_pobox,phone,fax ) { //crmv@65940
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	//crmv@17789
	if(typeof(form.elements["account_id_display"]) != 'undefined')
		form.elements["account_id_display"].value = account_name;
	if(typeof(form.elements["account_id"]) != 'undefined')
		form.elements["account_id"].value = account_id;
	disableReferenceField(form.elements["account_id_display"],form.elements["account_id"],form.elements["account_id_mass_edit_check"]);	//crmv@29190
	if (enableAdvancedFunction(form) && formName == 'EditView') {
		if(confirm(alert_arr.OVERWRITE_EXISTING_ACCOUNT1+account_name+alert_arr.OVERWRITE_EXISTING_ACCOUNT2))
		{
			if(typeof(form.mailingstreet) != 'undefined')
				form.mailingstreet.value = bill_street;
			if(typeof(form.otherstreet) != 'undefined')
				form.otherstreet.value = ship_street;
			if(typeof(form.mailingcity) != 'undefined')
				form.mailingcity.value = bill_city;
			if(typeof(form.othercity) != 'undefined')
				form.othercity.value = ship_city;
			if(typeof(form.mailingstate) != 'undefined')
				form.mailingstate.value = bill_state;
			if(typeof(form.otherstate) != 'undefined')
				form.otherstate.value = ship_state;
			if(typeof(form.mailingzip) != 'undefined')
				form.mailingzip.value = bill_code;
			if(typeof(form.otherzip) != 'undefined')
				form.otherzip.value = ship_code;
			if(typeof(form.mailingcountry) != 'undefined')
				form.mailingcountry.value = bill_country;
			if(typeof(form.othercountry) != 'undefined')
				form.othercountry.value = ship_country;
			if(typeof(form.mailingpobox) != 'undefined')
				form.mailingpobox.value = bill_pobox;
			if(typeof(form.otherpobox) != 'undefined')
				form.otherpobox.value = ship_pobox;
			//crmv@65940
			if(typeof(form.elements["otherphone"]) != 'undefined')
				form.elements["otherphone"].value = phone;
			if(typeof(form.elements["fax"]) != 'undefined')
				form.elements["fax"].value = fax;
			//crmv@65940e
		}
	}
	//crmv@17789e
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
		if(fieldname.indexOf('bill_street') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_street')]))
				mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_street')]).innerHTML+' ';
		}
		if(fieldname.indexOf('bill_pobox') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_pobox')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_pobox')]).innerHTML+' ';
		}
		if(fieldname.indexOf('bill_city') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_city')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_city')]).innerHTML+' ';
		}
		if(fieldname.indexOf('bill_state') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_state')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_state')]).innerHTML+' ';
		}
		if(fieldname.indexOf('bill_country') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_country')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_country')]).innerHTML+' ';
		}
		if(fieldname.indexOf('bill_code') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_code')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('bill_code')]).innerHTML+' ';
		}
	}
	else if (addressType == 'Other')
	{
		if(fieldname.indexOf('ship_street') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_street')]))
				mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_street')]).innerHTML+' ';
		}
		if(fieldname.indexOf('ship_pobox') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_pobox')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_pobox')]).innerHTML+' ';
		}
		if(fieldname.indexOf('ship_city') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_city')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_city')]).innerHTML+' ';
		}
		if(fieldname.indexOf('ship_state') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_state')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_state')]).innerHTML+' ';
		}
		if(fieldname.indexOf('ship_country') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_country')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_country')]).innerHTML+' ';
		}
		if(fieldname.indexOf('ship_code') > -1)
		{
			if(document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_code')]))
				mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('ship_code')]).innerHTML+' ';
		}
	}
	mapParameter = removeHTMLFormatting(mapParameter);
	//crmv@30064
	//openPopup('http://maps.google.com/maps?q='+mapParameter+'&output=embed','goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250','','','','','nospinner');//crmv@21048m //crmv@22065 //crmv@23446
	window.open('http://maps.google.com/maps?q='+mapParameter,'_blank');
	//crmv@30064e
}
//javascript function will open new window to display traffic details for particular url using alexa.com
function getRelatedLink()
{
	var param='';
	param = getObj("website").value;
	openPopup('http://www.alexa.com/data/details/traffic_details?q=&url='+param,'relatedlink','height=400,width=700,resizable=no,titlebar,location,top=250,left=250','','','','','nospinner');//crmv@21048m //crmv@22065
}

/*
* javascript function to populate fieldvalue in account editview
* @param id1 :: div tag ID
* @param id2 :: div tag ID
*/
function populateData(id1,id2)
{
	document.EditView.description.value = document.getElementById('summary').innerHTML;
	document.EditView.employees.value = getObj('emp').value;
	document.EditView.website.value = getObj('site').value;
	document.EditView.phone.value = getObj('Phone').value;
	document.EditView.fax.value = getObj('Fax').value;
	document.EditView.bill_street.value = getObj('address').value;
	
	showhide(id1,id2);
}
/*
* javascript function to show/hide the div tag
* @param argg1 :: div tag ID
* @param argg2 :: div tag ID
*/
function showhide(argg1,argg2)
{
	var x=document.getElementById(argg1).style;
	var y=document.getElementById(argg2).style;
	if (y.display=="none")
	{
		y.display="block"
		x.display="none"
	}
}

// JavaScript Document

if (document.all) var browser_ie=true
else if (document.layers) var browser_nn4=true
else if (document.layers || (!document.all && document.getElementById)) var browser_nn6=true

function getObj(n,d) {
  var p,i,x;
  if(!d)d=document;
  if((p=n.indexOf("?"))>0&&parent.frames.length) {d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}//crmv@21048m
  if(!(x=d[n])&&d.all)x=d.all[n];
  for(i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++)  x=getObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n);
  return x;
}


function findPosX(obj) {
        var curleft = 0;
        if (document.getElementById || document.all) {
                while (obj.offsetParent) { curleft += obj.offsetLeft; obj = obj.offsetParent;}
        }
        else if (document.layers) { curleft += obj.x; }
        return curleft;
}

function findPosY(obj) {
        var curtop = 0;
        if (document.getElementById || document.all) {
                while (obj.offsetParent) { curtop += obj.offsetTop; obj = obj.offsetParent; }
        }
        else if (document.layers) {curtop += obj.y;}
        return curtop;
}

ScrollEffect = function(){ };
ScrollEffect.lengthcount=202;
ScrollEffect.closelimit=0;
ScrollEffect.limit=0;


function just(){
        ig=getObj("company");
        if(ScrollEffect.lengthcount > ScrollEffect.closelimit ){closet();return;}
        ig.style.display="block";
        ig.style.height=ScrollEffect.lengthcount+'px';
        ScrollEffect.lengthcount=ScrollEffect.lengthcount+10;
        if(ScrollEffect.lengthcount < ScrollEffect.limit){setTimeout("just()",25);}
        else{ getObj("innerLayer").style.display="block";return;}
}

function closet(){
        ig=getObj("company");
        getObj("innerLayer").style.display="none";
        ScrollEffect.lengthcount=ScrollEffect.lengthcount-10;
        ig.style.height=ScrollEffect.lengthcount+'px';
        if(ScrollEffect.lengthcount<20){ig.style.display="none";return;}
        else{setTimeout("closet()", 25);}
}


function fnDown(obj){
        var tagName = document.getElementById(obj);
        document.EditView.description.value = document.getElementById('summary').innerHTML;
        document.EditView.employees.value = getObj('emp').value;
        document.EditView.website.value = getObj('site').value;
        document.EditView.phone.value = getObj('Phone').value;
        document.EditView.fax.value = getObj('Fax').value;
        document.EditView.bill_street.value = getObj('address').value;
        if(tagName.style.display == 'none')
                tagName.style.display = 'block';
        else
                tagName.style.display = 'none';
}

//When changing the Account Address Information  it should also change the related contact address.
function checkAddress(form,id)
{
		var url='';
		if(typeof(form.bill_street) != 'undefined')
     			url +="&bill_street="+form.bill_street.value;
		if(typeof(form.ship_street) != 'undefined')
     			url +="&ship_street="+form.ship_street.value;
		if(typeof(form.bill_city) != 'undefined')
    			url +="&bill_city="+form.bill_city.value;
		if(typeof(form.ship_city) != 'undefined')
     			url +="&ship_city="+form.ship_city.value;
		if(typeof(form.bill_state) != 'undefined')
     			url +="&bill_state="+form.bill_state.value;
		if(typeof(form.ship_state) != 'undefined')
     			url +="&ship_state="+form.ship_state.value;
		if(typeof(form.bill_code) != 'undefined')
    			url +="&bill_code="+ form.bill_code.value;
		if(typeof(form.ship_code) != 'undefined')
			url +="&ship_code="+ form.ship_code.value;
		if(typeof(form.bill_country) != 'undefined')
			url +="&bill_country="+form.bill_country.value;
		if(typeof(form.ship_country) != 'undefined')
			url +="&ship_country="+form.ship_country.value;
		if(typeof(form.bill_pobox) != 'undefined')
			url +="&bill_pobox="+ form.bill_pobox.value;
		if(typeof(form.ship_pobox) != 'undefined')
			url +="&ship_pobox="+ form.ship_pobox.value;

      		url +="&record="+id;		
	
		$("status").style.display="inline";
                        new Ajax.Request(
                              'index.php',
                                {queue: {position: 'end', scope: 'command'},
                                        method: 'post',
                                        postBody:"module=Accounts&action=AccountsAjax&ajax=true&file=AddressChange"+url,
                                        onComplete: function(response) {
						if(response.responseText  == 'address_change')
                                        	{
                                            		if(confirm(alert_arr.WANT_TO_CHANGE_CONTACT_ADDR) == true)
								{
									form.address_change.value='yes';
									form.submit();	
								}
								else
								{	
                                    form.submit();
								}
						}
						else
						{
							form.submit();	
						}
                      			}
			}
                        );

}
//Changing account address info - ENDS