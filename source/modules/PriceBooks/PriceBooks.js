/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

prod_array = new Array();
function addtopricebook()
{
	x = document.addToPB.selected_id.length;
	prod_array = new Array(x);
	idstring = "";

	if ( x == undefined)
	{
		if (document.addToPB.selected_id.checked)
		{
			yy = document.addToPB.selected_id.value+"_listprice";
			document.addToPB.idlist.value=document.addToPB.selected_id.value;
		
			var elem = document.addToPB.elements;
			var ele_len =elem.length;
			var i=0,j=0;
	
			for(i=0; i<ele_len; i++)
			{	
				if(elem[i].name == yy)
				{
					if (elem[i].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) 
					{
						alert(alert_arr.LISTPRICE_CANNOT_BE_EMPTY);
			               		return false;	
					}
					else if(isNaN(elem[i].value))
					{
						alert(alert_arr.INVALID_LIST_PRICE);
						return false;	
					}
	
				}
				
			}		
		}
		else 
		{
			alert(alert_arr.SELECT);
			return false;
		}
	}
	else
	{
		xx = 0;
		for(i = 0; i < x ; i++)
		{
			if(document.addToPB.selected_id[i].checked)
			{
				idstring = document.addToPB.selected_id[i].value +";"+idstring;
				prod_array[xx] = document.addToPB.selected_id[i].value;

				xx++;	
			}
		}
		if (xx != 0)
		{
			document.addToPB.idlist.value=idstring;
			var elem = document.addToPB.elements;
			var ele_len =elem.length;
			var i=0,j=0;
			for(i=0; i<ele_len; i++)
			{	
				for(j=0; j < xx; j++)
				{		
					var xy= prod_array[j]+"_listprice";
					if(elem[i].name == xy)
					{
						if (elem[i].value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0) 
						{
		
							alert(alert_arr.LISTPRICE_CANNOT_BE_EMPTY);
			                		return false;	
						}
						else if(isNaN(elem[i].value) || elem[i].value < 0)
						{
							alert(alert_arr.INVALID_LIST_PRICE);
			                		return false;	
							
						}
					}	
				}
							
			}
		}
		else
		{
			alert(alert_arr.SELECT);
			return false;
		}
	}
	document.addToPB.action="index.php?module=Products&action=addPbProductRelToDB&return_module=Products&return_action=AddProductsToPriceBook&parenttab="+parenttab;
}

function updateListPrice(unitprice,fieldname,oSelect)
{
	if(oSelect.checked == true)
	{
		document.getElementById(fieldname).style.visibility = 'visible';
		document.getElementById(fieldname).value = unitprice;
	}else
	{
		document.getElementById(fieldname).style.visibility = 'hidden';
	}
}

function check4null(form)
{
	var isError = false;
	var errorMessage = "";
	if (trim(form.productname.value) =='') 
	{
		isError = true;
		errorMessage += "\n Product Name";
		form.productname.focus();
	}
	if (isError == true) 
	{
		alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
		return false;
	}
	return true;
}

function set_return_specific(vendor_id, vendor_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.vendor_name.value = vendor_name;
	form.vendor_id.value = vendor_id;
	disableReferenceField(form.vendor_name,form.vendor_id,form.vendor_id_mass_edit_check);	//crmv@29190
}

function set_return_inventory_pb(listprice, fldname) 
{
	//crmv@21048m
    parent.document.EditView.elements[fldname].value = listprice;
	parent.document.EditView.elements[fldname].focus();
	//crmv@21048m e
	disableReferenceField(parent.document.EditView.elements[fldname]);	//crmv@29190
	
	//crmv@69922
	var field = document.getElementById(fldname) || parent.document.getElementById(fldname);
	var onChangeFunction = field.onchange;
	if (typeof onChangeFunction === "function") {
		onChangeFunction();
	}
	//crmv@69922e
}

function deletePriceBookProductRel(id,pbid)
{
	$("status").style.display="inline";
	new Ajax.Request(
	'index.php',
	{queue: {position: 'end', scope: 'command'},
		method: 'post',
		postBody: 'module=Products&action=ProductsAjax&file=DeletePriceBookProductRel&ajax=true&return_action=CallRelatedList&return_module=PriceBooks&record='+id+'&pricebook_id='+pbid+'&return_id='+pbid,
		onComplete: function(response) {
			$("status").style.display="none";
			$("RLContents").update(response.responseText);
		}
	});
}

function verify_data(fieldLabel)
{
	if (typeof(fieldLabel) == 'undefined') fieldLabel = 'List Price';
	var returnValue = true;
	var list_price = $('list_price');
	if(list_price.value != '' && list_price.value != 0)
	{
		intval= intValidate('list_price',fieldLabel);
		if(!intval)
		{
			returnValue =  false;
		}
	       
	}
	else
	{
		if(list_price.value == '')
		{
			alert(alert_arr.LISTPRICE_CANNOT_BE_EMPTY);
			returnValue = false;
		}
	}
	return returnValue;
}