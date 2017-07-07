/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@3085m crmv@3086m crmv@51865 */

var globaldtlviewspanid = "";
var globaleditareaspanid = "";
var globalbuttonsspanid = "";
var globaltxtboxid = "";
var itsonview=false;
var globaltempvalue = '';	// to retain the old value if we cancel the ajax edit
var globaluitype = '';
var globalfieldname = '';
var globaldivobj = '';
var globaldivobjclass = '';

function showHide(showId, hideId)
{
	show(showId);
	fnhide(hideId);
}

function hndCancel(valuespanid,textareapanid,fieldlabel,checkconfirm)
{
	if (typeof(checkconfirm) == 'undefined') var checkconfirm = false;
	if(globaluitype == '56')
	{
		if (getObj(globaltxtboxid).checked == true)
			var currentvalue = 1;
		else
			var currentvalue = 0;
		if (globaltempvalue != currentvalue) {
			if (checkconfirm == true && confirm(alert_arr.LBL_SAVE_LAST_CHANGES)) {
				var link = jQuery($(globalbuttonsspanid)).find('a').first();
				link.click();
			//crmv@51865bis
			} else if (checkconfirm == true) {
				getObj(globaltxtboxid).focus();
				return false;
			//crmv@51865bis e
			} else {
				getObj(globaltxtboxid).value = globaltempvalue;
				if(globaltempvalue == 1)
					getObj(globaltxtboxid).checked = true;
				else
					getObj(globaltxtboxid).checked = false;
			}
		}
	}
	else if(globaluitype != '53' && globaluitype != '33') {
		if (globaltempvalue != getObj(globaltxtboxid).value) {
			if (checkconfirm == true && confirm(alert_arr.LBL_SAVE_LAST_CHANGES)) {
				var link = jQuery($(globalbuttonsspanid)).find('a').first();
				link.click();
				return false;
			//crmv@51865bis
			} else if (checkconfirm == true) {
				getObj(globaltxtboxid).focus();
				return false;
			//crmv@51865bis e
			} else {
				getObj(globaltxtboxid).value = globaltempvalue;
			}
		}
	}
	if (typeof(globaldivobj) != 'undefined') {
		globaldivobj.className = globaldivobjclass;
	}
	showHide(valuespanid,textareapanid);
	fnhide(globalbuttonsspanid);
	globaltempvalue = '';
	itsonview=false;
	return true;
}

function hndMouseClick(uitype,fieldLabel,fieldname,obj)
{
	if(itsonview)
	{
		if (globalfieldname == fieldname) {
			return;
		} else {
			var res = hndCancel(globaldtlviewspanid,globaleditareaspanid,'',true);
			if (res == false) return false;
		}
	}
	globaluitype = uitype;
	globaldtlviewspanid= "dtlview_"+ fieldLabel;//valuespanid;
	globaleditareaspanid="editarea_"+ fieldLabel;//textareapanid;
	globalbuttonsspanid="buttons_"+ fieldLabel;
	globalfieldlabel = fieldLabel;
	globalfieldname = fieldname;
	globaldivobj = obj;
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
			if(assign_type_U == true)
				globaltxtboxid= 'txtbox_U'+fieldLabel;
			else if(assign_type_G == true)
				globaltxtboxid= 'txtbox_G'+fieldLabel;
		}else
		{
			globaltxtboxid= 'txtbox_U'+fieldLabel;
		}
		removePageSelection();	//crmv@55030
	}else
	{
		globaltxtboxid="txtbox_"+ fieldLabel;//textboxpanid;
	}
	//crmv@54072
	if(globaluitype == 19 || globaluitype == 21 || globaluitype == 208) {
		var height = jQuery(globaldivobj).height();
		var height1 = jQuery("[id='editarea_"+fieldLabel+"']").height();
		if (height1 > height) height = height1;
		jQuery("[id='txtbox_"+fieldLabel+"']").css('height',height);
	}
	//crmv@54072e
	handleEdit();
}

function handleEdit()
{
	if (typeof(globaldivobj) != 'undefined') {
		globaldivobjclass = globaldivobj.className;
		//crmv@57221
		var classes = globaldivobjclass ? globaldivobjclass.split(' ') : [];
		var count = classes.length;
		if (count == 1) {
			globaldivobj.className = 'dvtCellInfoOn';
		} else {
			for (var i=0;i<count;i++) {
				if (classes[i] == 'dvtCellInfo') classes[i] = 'dvtCellInfoOn';
			}
			globaldivobj.className = classes.join(' ');
		}
		//crmv@57221e		
	}
	show(globaleditareaspanid);
	show(globalbuttonsspanid);
	fnhide(globaldtlviewspanid);
	if(globaluitype != 53)
	{
		globaltempvalue = getObj(globaltxtboxid).value;
		if(getObj(globaltxtboxid).type != 'hidden') {
			getObj(globaltxtboxid).focus();
		}
	}
	fnhide('crmspanid');
	itsonview=true;
	return false;
}

// for old SDK fields
function hndMouseOver(uitype,fieldLabel,fieldname)
{
	if(itsonview)
	{
		return;
	}
	var mouseArea="";
	mouseArea="editbutton_"+ fieldLabel;

	show("crmspanid");
	divObj = getObj('crmspanid');
	crmy = findPosY(getObj(mouseArea));
	crmx = findPosX(getObj(mouseArea));
	if(document.all)
	{
		divObj.ondblclick=hndMouseClick(uitype,fieldLabel,fieldname);	//crmv@54072
	}
	else
	{
		divObj.setAttribute('ondblclick',"hndMouseClick('"+uitype+"','"+fieldLabel+"','"+fieldname+"');");	//crmv@54072
	}
	divObj.style.left=(crmx+getObj(mouseArea).offsetWidth -divObj.offsetWidth)+"px";
	divObj.style.top=crmy+"px";
}

//Asha: Function changed to trim both leading and trailing spaces.
function trim(str) {
	var s = str.replace(/\s+$/,'');
	s = s.replace(/^\s+/,'');
	return s;
}

var genUiType = "";
var genFldValue = "";

function dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId)
{
	var dtlView = "dtlview_"+ fieldLabel;
	var editArea = "editarea_"+ fieldLabel;
	var buttons = "buttons_"+ fieldLabel;
	var groupurl = "";

	//crmv@31171
	if(globaluitype == 53)
	{
		if ($(document.DetailView.assigntype).options[$(document.DetailView.assigntype).selectedIndex].value != 'undefined')
		{
			if ($(document.DetailView.assigntype).options[$(document.DetailView.assigntype).selectedIndex].value == 'U') {
				var assign_type_U = true;
				var assign_type_G = false;
			} else {
				var assign_type_U = false;
				var assign_type_G = true;
			}
		} else {
			var assign_type_U = true;
			var assign_type_G = false;
		}
		if(assign_type_U == true)
		{
			var txtBox= 'txtbox_U'+fieldLabel;
		}
		else if(assign_type_G == true)
		{
			var txtBox= 'txtbox_G'+fieldLabel;
			var group_id = document.DetailView.assigned_group_id.value;
			var groupurl = "&assigned_group_id="+group_id+"&assigntype=T"
		}
	}
	//crmv@31171e
	//crmv@8982
	else if(uitype == 15 || uitype == 1015 || uitype == 16 || uitype == 111)
	{
	//crmv@8982e
		var txtBox= "txtbox_"+ fieldLabel;
		var not_access = document.getElementById(txtBox);
		pickval = not_access.options[not_access.selectedIndex].value;
		if(pickval == alert_arr.LBL_NOT_ACCESSIBLE)
		{
			document.getElementById(editArea).style.display='none';
			document.getElementById(dtlView).style.display='block';
			fnhide(buttons);
     		itsonview=false; //to show the edit link again after hiding the editdiv.
			return false;
		}
	}
	else if(globaluitype == 33)
	{
	  var txtBox= "txtbox_"+ fieldLabel;
	  var oMulSelect = $(txtBox);
	  var r = new Array();
	  var notaccess_label = new Array();
	  for (iter=0;iter < oMulSelect.options.length ; iter++)
	  {
      	      if (oMulSelect.options[iter].selected)
		{
			r[r.length] = oMulSelect.options[iter].value;
			notaccess_label[notaccess_label.length] = oMulSelect.options[iter].text;
		}
      	  }
	}else
	{
		var txtBox= "txtbox_"+ fieldLabel;
	}

	var popupTxt= "popuptxt_"+ fieldLabel;
	var hdTxt = "hdtxt_"+ fieldLabel;

	if(formValidate(this.document.DetailView) == false)	//crmv@sdk-18501
	{
		return false;
	}

	$("status").style.display="inline";
	var isAdmin = document.getElementById("hdtxt_IsAdmin").value;

	//overriden the tagValue based on UI Type for checkbox
	if(uitype == '56')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			if(module == "Contacts")
			{
				var obj = getObj("email");
				if((fieldName == "portal") && (obj == null || obj.value == ''))
				{
					tagValue = "0";
					alert(alert_arr.PORTAL_PROVIDE_EMAILID);
					return false;
				}
				else
					tagValue = "1";

			}
			else
				tagValue = "1";
		}else
		{
			tagValue = "0";
		}
	}else	if(uitype == '156')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			tagValue = "on";
		}else
		{
			tagValue = "off";
		}
	}else if(uitype == '33')
	{
		tagValue = r.join(" |##| ");
  	}else if(uitype == '24' || uitype == '21')
	{
		tagValue = document.getElementById(txtBox).value.replace(/<br\s*\/>/g, " ");
	}else
	{
		tagValue = trim(document.getElementById(txtBox).value);
		if(module == "Contacts")
		{
			if(getObj('portal'))
			{
		        var port_obj = getObj('portal').checked;
		        if(fieldName == "email" && tagValue == '' && port_obj == true)
		        {
		                alert(alert_arr.PORTAL_PROVIDE_EMAILID);
		                return false;
		        }
			}
		}
	}
	//crmv@7213
	if ( (globaluitype == '1112' ) && (module= "Accounts") && (fieldName == "external_code") && (tagValue != '') )
	{
		//crmv@19653
		if (AjaxDuplicateValidateEXT_CODE(module,fieldName,tagValue,'ajax')) {
			getObj('mouseArea_'+fieldLabel).onmouseover = '';
			getObj('mouseArea_'+fieldLabel).onmouseout = '';
		}
		else
		//crmv@19653e
			return false;
	}
	var data = "file=DetailViewAjax&module=" + module + "&action=" + module + "Ajax&record=" + crmId+"&recordid=" + crmId ;
	data = data + "&fldName=" + fieldName + "&fieldValue=" + escapeAll(tagValue) + "&ajxaction=DETAILVIEW"+groupurl;
	new Ajax.Request(
		'index.php',
		{queue:
			{position: 'end', scope: 'command'},
			method: 'post',
			postBody: data,
			onComplete: function(response) {
				if(response.responseText.indexOf(":#:FAILURE")>-1) {
					alert(alert_arr.ERROR_WHILE_EDITING);
				} else if(response.responseText.indexOf(":#:SUCCESS")>-1) {
					//For HD & FAQ - comments, we should empty the field value
					if((module == "HelpDesk" || module == "Faq") && fieldName == "comments") {
						getObj(dtlView).innerHTML = "";
						getObj("comments").value = "";
						getObj("comments_div").innerHTML = response.responseText.replace(":#:SUCCESS","");
					}
					if (module == 'Processes' && fieldName.indexOf('vcf_') >- 1) DynaFormScript.loadDetailViewBlocks(crmId);	//crmv@99316
					//crmv@93990
					jQuery.ajax({
						'url': 'index.php?module=Processes&action=ProcessesAjax&file=DetailViewAjax&ajxaction=CHECKDYNAFORMPOPUP&record='+crmId,
						success: function(data) {
							if(data.indexOf(":#:SUCCESS")>-1) DynaFormScript.popup(data.replace(":#:SUCCESS",""));
						}
					});
					//crmv@93990e
					$("status").style.display="none";
				//crmv@103534
				} else if(response.responseText.indexOf("error='RECORDDELETED_")>-1 || response.responseText.indexOf("error='LEADCONVERTED_")>-1 || response.responseText.indexOf("error='RECORDNOTFOUND_")>-1) {
					window.location.reload();
				}
				//crmv@103534e
			}
		}
	);
	tagValue = get_converted_html(tagValue);
	//crmv@7213e
	//crmv@sdk-18509
	sdkUitypeFile = getSDKUitype(uitype);
	if (sdkUitypeFile != '') {
		jQuery.ajax({
			url: sdkUitypeFile,
			dataType: 'text',
			async: false,
			cache: false,
			success: function(data){
				eval(data);
			}
		});
	}
	//crmv@sdk-18509e
	//crmv@7216
	else if(uitype == '1013')
	{
		var temp_fieldname = 'internal_mailer_'+fieldName;
		if($(temp_fieldname))
		{
			var fax_chk_arr = $(temp_fieldname).innerHTML.split("####");
			var fieldId = fax_chk_arr[0];

				var fax_link = "<a href=\"javascript:InternalFax("+crmId+","+fieldId+",'"+fieldName+"','"+module+"','record_id');\">"+tagValue+"&nbsp;</a>";
		}
		getObj(dtlView).innerHTML = fax_link;
	}
	//crmv@7216e
	//crmv@7220
	else if((uitype == '11' || uitype == '1014') && typeof(use_asterisk) != 'undefined' && use_asterisk == true) {
		getObj(dtlView).innerHTML = "<a href=\"javascript:;\" onclick=\"startCall('"+tagValue+"','"+crmId+"')\">"+tagValue+"</a>";
	}
	//crmv@7220e
	else if(uitype == '13' || uitype == '104')
	{
		var temp_fieldname = 'internal_mailer_'+fieldName;
		if($(temp_fieldname))
		{
			var mail_chk_arr = $(temp_fieldname).innerHTML.split("####");
			var fieldId = mail_chk_arr[0];
			var internal_mailer_flag = mail_chk_arr[1];
			if(internal_mailer_flag == 1)
				var email_link = "<a href=\"javascript:InternalMailer("+crmId+","+fieldId+",'"+fieldName+"','"+module+"','record_id');\">"+tagValue+"&nbsp;</a>";
			else
				var email_link = "<a href=\"mailto:"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
		}

		getObj(dtlView).innerHTML = email_link;
		if(fieldName == "email" || fieldName == "email1"){
			var priEmail = getObj("pri_email");
			if(priEmail)
				priEmail.value = tagValue;
		}else{
			var secEmail = getObj("sec_email");
			if(secEmail)
                	        secEmail.value = tagValue;
		}
	}else if(uitype == '17')
	{
		getObj(dtlView).innerHTML = "<a href=\"http://"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
	}else if(uitype == '85')
	{
		getObj(dtlView).innerHTML = "<a href=\"skype://"+ tagValue+"?call\">"+tagValue+"&nbsp;</a>";
	}else if(uitype == '53')
	{
		var hdObj = getObj(hdTxt);
		if(isAdmin == "0")
		{
			getObj(dtlView).innerHTML = hdObj.value;
		}else if(isAdmin == "1" && assign_type_U == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}else if(isAdmin == "1" && assign_type_G == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Settings&action=GroupDetailView&groupId="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}
	}
	else if(uitype == '52' || uitype == '77')
	{
		if(isAdmin == "1")
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text+"&nbsp;</a>";
		else
			getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;
	}
	else if(uitype == '56')
	{
		if(tagValue == '1')
		{
			getObj(dtlView).innerHTML = alert_arr.YES;
		}else
		{
			getObj(dtlView).innerHTML = alert_arr.NO;
		}
		getObj(txtBox).value = tagValue;	//crmv@51865
	}
	else if(uitype == 116)
	{
		getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;
	}
	else if(getObj(popupTxt))
	{
		var popObj = getObj(popupTxt);
		if(uitype == '53')
		{
			var hdObj = getObj(hdTxt);
			if(isAdmin == "0")
			{
				getObj(dtlView).innerHTML = hdObj.value;
			}else if(isAdmin == "1")
			{
				getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";;
			}
		}
		else if(uitype == '56')
		{
			if(tagValue == '1')
			{
				getObj(dtlView).innerHTML = alert_arr.YES;
			}else
			{
				getObj(dtlView).innerHTML = "";
			}

		}
		else
		{
			getObj(dtlView).innerHTML = popObj.value;
		}
	}
	//crmv@8982
	else if(uitype == '111' || uitype == '15' || uitype == '1015' || uitype == '16' )
	{
	//crmv@8982e
		var notaccess = document.getElementById(txtBox);
        tagValue = notaccess.options[notaccess.selectedIndex].text;
		if(tagValue == alert_arr.LBL_NOT_ACCESSIBLE)
			getObj(dtlView).innerHTML = "<font color='red'>"+get_converted_html(tagValue)+"</font>";
		else
			getObj(dtlView).innerHTML = get_converted_html(tagValue);
	}
	else if(uitype == '33')
	{
		/* Wordwrap a long list of multi-select combo box items at the
        * item separator string */
		var DETAILVIEW_WORDWRAP_WIDTH = "70"; // must match value in DetailViewUI.tpl.
		var lineLength = 0;
		for(var i=0; i < notaccess_label.length; i++) {
			lineLength += notaccess_label[i].length + 2; // + 2 for item separator string
			/*if(lineLength > DETAILVIEW_WORDWRAP_WIDTH && i > 0) {
				lineLength = notaccess_label[i].length + 2; // reset.
				notaccess_label[i] = '<br/>&nbsp;' + notaccess_label[i]; // prepend newline.
			}*/
			notaccess_label[i] = get_converted_html(notaccess_label[i]);
			// Prevent a browser splitting multiword items:
			//notaccess_label[i] = notaccess_label[i].replace(/ /g, '&nbsp;');
			notaccess_label[i] = notaccess_label[i].replace(alert_arr.LBL_NOT_ACCESSIBLE,"<font color='red'>"+alert_arr.LBL_NOT_ACCESSIBLE+"</font>"); // for Not accessible label.
		}
		/* Join items with item separator string (which must match string in DetailViewUI.tpl,
		 * EditViewUtils.php and CRMEntity.php)!!
		 */
		getObj(dtlView).innerHTML = notaccess_label.join(", ");
	}
	else if(uitype == '19')
	{
		var desc = tagValue.replace(/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/g, "$1<a href=\"$2\" target=\"_blank\">$2</a>");
		desc = desc.replace(/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/g, "$1<a href=\"http://$2\" target=\"_blank\">$2</a>");
		desc = desc.replace(/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i, "$1<a href=\"javascript:InternalMailer('$2@$3','','','','email_addy');\">$2@$3</a>");	//crmv@27617
		desc = desc.replace(/,\"|\.\"|\)\"|\)\.\"|\.\)\"/, "\"");
		desc = desc.replace(/\n\r/g, "<br>").replace(/\n/g, '<br>'); // crmv@101312
		getObj(dtlView).innerHTML = desc;
	//crmv@16265
	}
	else if(uitype == '199')
	{
		tagValue.replace(/[\n\r]+/g, "<br>&nbsp;");
		i=0;tmp = '';
		while (i<tagValue.toString().length) {
			tmp += '*';
			i++;
		}
		getObj(dtlView).innerHTML = tmp;
	}
	//crmv@16265e
	else
	{
		getObj(dtlView).innerHTML = tagValue.replace(/[\n\r]+/g, "<br>&nbsp;");
	}
	if (typeof(globaldivobj) != 'undefined') {
		globaldivobj.className = globaldivobjclass;
	}
	showHide(dtlView,editArea);  //show,hide
	fnhide(buttons);
	itsonview=false;
}

function SaveTag(tagfield,crmId,module)
{
	var tagValue = $(tagfield).value;
	tagValue = encodeURIComponent(tagValue);
	$("status").style.display="inline";
	new Ajax.Request(
		'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                       postBody: "file=TagCloud&module=" + module + "&action=" + module + "Ajax&recordid=" + crmId + "&ajxaction=SAVETAG&tagfields=" +tagValue,
                       onComplete: function(response) {
					if(response.responseText.indexOf(":#:FAILURE") > -1)
					{
						alert(alert_arr.VALID_DATA)
					}else{
				        	getObj('tagfields').innerHTML = response.responseText;
						$(tagfield).value = '';
					}
					$("status").style.display="none";
                        }
                }
        );

}
function setSelectValue(fieldLabel)
{
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
			if(assign_type_U == true)
				var selCombo= 'txtbox_U'+fieldLabel;
			else if(assign_type_G == true)
				var selCombo= 'txtbox_G'+fieldLabel;
		}else
		{
			var selCombo= 'txtbox_U'+fieldLabel;
		}
	}else
	{
			var selCombo= 'txtbox_'+fieldLabel;
	}
	var hdTxtBox = 'hdtxt_'+fieldLabel;
	var oHdTxtBox = document.getElementById(hdTxtBox);
	var oSelCombo = document.getElementById(selCombo);

	oHdTxtBox.value = oSelCombo.options[oSelCombo.selectedIndex].text;
}
//crmv@29079

//crmv@120738
function showHideStatus(sId, anchorImgId, sImagePath) {
	var $element = jQuery('#' + sId);
	if ($element.length < 1) return;
	oObj = $element.get(0);
	
	if ($element.is(':visible')) {
		hideStatus(oObj, anchorImgId, sImagePath);
	} else {
		showStatus(oObj, anchorImgId, sImagePath);
	}
}
//crmv@120738e

function hideStatus(oObj,anchorImgId,sImagePath) {
	oObj.style.display = 'none';
	jQuery('#'+anchorImgId).css('opacity', 0.5).attr('title', 'Display');
}
function showStatus(oObj,anchorImgId,sImagePath) {
	oObj.style.display = 'block';
	jQuery('#'+anchorImgId).css('opacity', '').attr('title', 'Hide');
}
//crmv@29079e
function setCoOrdinate(elemId){
	oBtnObj = document.getElementById(elemId);
	var tagName = document.getElementById('lstRecordLayout');
	leftpos  = 0;
	toppos = 0;
	aTag = oBtnObj;
	do {
		leftpos  += aTag.offsetLeft;
		toppos += aTag.offsetTop;
	} while(aTag = aTag.offsetParent);
	tagName.style.top= toppos + 20 + 'px';
	tagName.style.left= leftpos - 276 + 'px';
}
function getListOfRecords(obj, sModule, iId, sParentTab) {
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Users&action=getListOfRecords&ajax=true&CurModule='+sModule+'&CurRecordId='+iId+'&CurParentTab='+sParentTab,
			onComplete: function(response) {
				sResponse = response.responseText;
				if (sModule == 'Accounts')
					HideHierarch();
				$("lstRecordLayout").innerHTML = sResponse;
				Lay = 'lstRecordLayout';
				var tagName = document.getElementById(Lay);
				var leftSide = findPosX(obj);
				var topSide = findPosY(obj);
				var maxW = tagName.style.width;
				var widthM = maxW.substring(0,maxW.length-2);
				var getVal = parseInt(leftSide) + parseInt(widthM);
				if(getVal  > document.body.clientWidth ){
					leftSide = parseInt(leftSide) - parseInt(widthM);
					tagName.style.left = leftSide + 230 + 'px';
					tagName.style.top = topSide + 20 + 'px';
				}else{
					tagName.style.left = leftSide + 230 + 'px';
				}
				setCoOrdinate(obj.id);

				tagName.style.display = 'block';
				tagName.style.visibility = "visible";
			}
		}
	);
}
//crmv@99316	crmv@105937
function loadDetailViewBlocks(module,record,view,destination,extraParams,status,dialog,scroll,callback) {
	if (typeof(status) == 'undefined' || status == '') var status = 'status';
	if (typeof(dialog) == 'undefined') var dialog = true;
	if (typeof(scroll) == 'undefined') var scroll = true;
	if (dialog) VtigerJS_DialogBox.block(destination);
	jQuery('#'+status).show();
	var params = {
		'module' : module,
		'action' : module+'Ajax',
		'file' : 'DetailViewBlocks',
		'record' : record,
		'view' : view,
	};
	jQuery.ajax({
		url: 'index.php?'+jQuery.param(params),
		type: 'POST',
		data: ( extraParams && !jQuery.isEmptyObject(extraParams) ? '&' + jQuery.param(extraParams) : '' ),
		success: function(data){
			jQuery('#'+destination).html(data);
			if (scroll && destination == 'DetailViewBlocks') jQuery('body').scrollTop(0);
			if (dialog) VtigerJS_DialogBox.unblock(destination);
            jQuery('#'+status).hide();
            if (typeof callback == 'function') callback();
		}
	});
}
//crmv@99316e	crmv@105937e
//crmv@43864
function reloadTurboLift(module, crmid, relmodule) {
	if (!module) return;
	var buttonId = 'tl_'+module+'_'+relmodule,
		unpinId = 'unPin_'+module+'_'+relmodule,
		tlButton = jQuery('#'+buttonId),
		isClicked = tlButton.hasClass('turboliftEntrySelected'),
		isPinned = (tlButton.is('[class^="turboliftEntry"]') && jQuery('#'+unpinId).is(':visible')), //crmv@62415
		relationID = tlButton.attr('relation_id'); //crmv@62415
	var params = {
		'module' : module,
		'action' : module+'Ajax',
		'file' : 'Turbolift',
		'record' : crmid,
		'ajaxaction' : 'show',
	};
	if (jQuery('#turbolift_back_button').length == 0) {
		params['show_turbolift_back_button'] = 'no';
	}
	if (amIinPopup()) {
		params['inOpenPopup'] = 'yes';
	}
	if (isPinned) {
		//crmv@62415
		if (relationID != ''){
			loadRelatedListBlock('module='+module+'&action='+module+'Ajax&file=DetailViewAjax&ajxaction=LOADRELATEDLIST&order_by=&header='+relmodule+'&record='+crmid+'&relation_id='+relationID, "tbl_"+module+'_'+relmodule, module+'_'+relmodule);			
		}
		else{
			top.location.reload();
			return;
		}
		//crmv@62415 e
	}
	// TODO: show a busy girella
	jQuery.ajax({
		url: 'index.php',
		type: 'POST',
		data: params,
		success: function(data) {
			var cont = jQuery('#turboLiftRelationsContainer');
			cont.html(data);
			if (isClicked) {
				currentRelated = '';
				//crmv@64719
				if (cont.find('#'+buttonId).attr("onclick") == null)
					var btn = cont.find('#'+buttonId).children().first();
				else
					var btn = cont.find('#'+buttonId);
				//crmv@64719e
				btn.click();
			}
		}
	});
}
//crmv@43864e

function loadSummary(label,module,record,destination,relation_id) {
	var related = destination.replace('tbl_','');
	if (jQuery('#'+destination+'_Summary').length == 0) {
		jQuery('<div id="'+destination+'_Summary" style="padding:1px; background-color: #ffffff;"></div>').appendTo(jQuery('#'+destination).parent());
	}
	var extraParams = {
		'show_details_button' : 'true',
		'show_related_buttons' : 'true',
		'DETAILVIEW_AJAX_EDIT' : 'false',
		'relation_id' : relation_id,
		'destination' : destination,
	}
	loadDetailViewBlocks(module,record,'summary',destination+'_Summary',extraParams,'indicator_'+related);
	jQuery('#'+destination).hide();
	jQuery('#'+destination+'_Summary').show();
	jQuery('#dtl_'+related).html(label);
}

function turnToRelatedList(label,real_destination,destination) {
	var related = destination.replace('tbl_','');
	jQuery('#'+real_destination).remove();	//crmv@54375
	jQuery('#'+destination).show();
	jQuery('#dtl_'+related).html(label);
}

//crmv@63001 for mobile devices, trigger the onclick
function hndMobileClick(el) {
	if (el && isMobile()) {
		if (typeof el.ondblclick == 'function') {
			el.ondblclick.call(el);
		}
	}
}
//crmv@63001e
