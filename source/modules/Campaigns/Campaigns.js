/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

function set_return(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);	//crmv@29190
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

//crmv@43611 - Newsletter Wizard
function openNewsletterWizard(module, id) {
	var url = "index.php?module=Campaigns&action=CampaignsAjax&file=NewsletterWizard&from_module="+encodeURIComponent(module)+'&from_record='+id;
	openPopup(url,"NewsletterWizard","width=750,height=602,menubar=no,toolbar=no,location=no,status=no,resizable=no,scrollbars=yes");
}

var nlwRecipientsChanged = false;

function nlwRecordSelect(listid, module, crmid, entityname) {
	var box = jQuery('#nlWizTargetsBox'),
		spanid = 'nlw_item_'+module+'_'+crmid,
		existing = box.find('#'+spanid),
		singlelabel = jQuery('#SLVContainer_'+listid).find('#mod_singlelabel').val(),
		shortname = (entityname.length > 20 ? entityname.substr(0,10) + '...' : entityname),
		ename = singlelabel+': '+shortname;

	if (existing.length > 0) return;

	// create a box
	var span = '<span id="'+spanid+'" class="addrBubble">'
		+'<table cellpadding="3" cellspacing="0" class="small">'
		+'<tr>'
		+	'<td>'+ename+'</td>'
		+	'<td rowspan="2" align="right" valign="top"><div class="ImgBubbleDelete" onClick="nlwRecordRemove(\''+spanid+'\');"><i class="vteicon small">clear</i></div></td>'
		+'</tr>'
		+'</table>'
		+'</span>';

	box.append(span);
	nlwRecipientsChanged = true;
	nlwCountRecipients();
}

function nlwFilterSelect(listid, module, viewid) {
	var box = jQuery('#nlWizTargetsBox'),
		spanid = 'nlw_filter_'+module+'_'+viewid,
		existing = box.find('#'+spanid),
		label = jQuery('#SLVContainer_'+listid).find('#mod_label').val(),
		ename = label+': '+jQuery('#SLVContainer_'+listid+' #viewname').find(":selected").text() + ' ('+alert_arr.LBL_FILTER+')';

	if (existing.length > 0) return;

	// create a box
	var span = '<span id="'+spanid+'" class="addrBubble">'
		+'<table cellpadding="3" cellspacing="0" class="small">'
		+'<tr>'
		+	'<td>'+ename+'</td>'
		+	'<td rowspan="2" align="right" valign="top"><div class="ImgBubbleDelete" onClick="nlwRecordRemove(\''+spanid+'\');"><i class="vteicon small">clear</i></div></td>'
		+'</tr>'
		+'</table>'
		+'</span>';

	box.append(span);
	nlwRecipientsChanged = true;
	nlwCountRecipients();
}


function nlwRecordRemove(spanid) {
	var box = jQuery('#nlWizTargetsBox'),
		span = box.find('#'+spanid);

	span.remove();
	nlwRecipientsChanged = true;
	nlwCountRecipients();
}

// get recipients divided by module and type
function nlwGetRecipients() {
	var box = jQuery('#nlWizTargetsBox'),
	listRecords = box.find('span[id^=nlw_item_]'),
	listFilters = box.find('span[id^=nlw_filter_]'),
	arr = {};

	listRecords.each(function(index, item) {
		var l = item.id.split('_'),
			module = l[2],
			crmid = l[3];
		if (arr[module] === undefined) arr[module] = {};
		if (arr[module]['ids'] === undefined) arr[module]['ids'] = [];
		arr[module]['ids'].push(crmid);
	});

	listFilters.each(function(index, item) {
		var l = item.id.split('_'),
			module = l[2],
			viewid = l[3];
		if (arr[module] === undefined) arr[module] = {};
		if (arr[module]['filters'] === undefined) arr[module]['filters'] = [];
		arr[module]['filters'].push(viewid);
	});

	return arr;
}

function nlwCountRecipients() {
	var ids = nlwGetRecipients(),
		simpletotal = 0,
		total = 0;

	var postParams = {
		'ids' : JSON.stringify(ids),
	};

	for (var mod in ids) {
		simpletotal += (ids[mod]['ids'] ? ids[mod]['ids'].length : 0) + (ids[mod]['filters'] ? ids[mod]['filters'].length : 0);
	}

	if (simpletotal == 0) {
		// no need of ajax request
		jQuery('#nlw_selTargetsCount').html(simpletotal);
		return;
	}

	jQuery('#nlw_selTargetsCount').html('');
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=countrecipients',
		type: 'POST',
		data: postParams,
		complete: function() {
		},
		success: function(data) {
			try {
				var counters = JSON.parse(data);
			} catch (e) {
				// invalid data
			}
			if (counters && counters.count != '') {
				jQuery('#nlw_selTargetsCount').html(counters.count);
			}

		}
	});

}

function nlwChangeTargetSel() {
	var selmod = jQuery('#nlw_targetTypeSel').val();

	jQuery('#nlWizStep1').find('div[id^=nlw_targetList]').hide();
	jQuery('#nlw_targetList_'+selmod).show();

}


function InsertIntoTemplate(element) {
    var selectField =  jQuery('#'+element).val(),
    	oEditor = CKEDITOR.instances.nlw_template_body;
	if (selectField != '') {
        oEditor.insertHtml(selectField);
	}
}

var allTplOptions = null;

function modifyMergeFieldSelect(cause, effect) {
    var selected = jQuery(cause).val(),
    	s = allTplOptions[selected],
    	jeffect = jQuery(effect);

    jeffect.empty();
    jeffect.append('<option value="--None--">'+(alert_arr.LBL_NONE)+'</option>');	//crmv@53967
    for (var i = 0; i < s.length; ++i) {
    	jeffect.append('<option value="'+s[i][1]+'">'+s[i][0]+'</option>')
    }
    jQuery('#mergeFieldValue').val('');
}

function nlwTemplateSelect(listid, module, tplid, entityname) {
	jQuery('#nlw_templateid').val(tplid);
	jQuery('#nlw_selTemplate').html(entityname);

	// get template data
	jQuery('#nlw_templatePreviewCont').hide();
	jQuery('#nlw_temlateEditButton').hide();
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=gettemplate',
		type: 'POST',
		data: {templateid : tplid},
		success: function(data) {
			var error = false;
			try {
				var retData = JSON.parse(data);
			} catch (e) {
				error = true;
			}
			if (!error) {
				jQuery('#nlw_templatePreviewSubject').html(retData.subject);
				jQuery('#nlw_templatePreviewBody').html(retData.body);
				jQuery('#nlw_templatePreviewHeader').show();
				jQuery('#nlw_temlateEditButton').show();
				// resize and show
				jQuery('#nlw_templatePreviewCont').css({'height': (jQuery('#nlWizRightPane').height() - 300)+ 'px'}).show();
			}
		}

	});
}

// if templateid is empty -> create
function nlwTemplateEdit(listid, templateid) {
	jQuery('#nlwTopButtons').hide();
	jQuery('#nlw_templateDetails').hide();
	jQuery('#nlw_templateEditCont').show();

	jQuery('#nlw_templateEditId').val(templateid);
	jQuery('#nlw_template_name').val('');
	jQuery('#nlw_template_description').val('');
	jQuery('#nlw_template_subject').val('');
	CKEDITOR.instances.nlw_template_body.setData('');

	if (templateid !== undefined && templateid !== null && templateid !== '') {
		jQuery('#nlw_templateEditlIndicator').show();
		jQuery.ajax({
			url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=gettemplate',
			type: 'POST',
			data: {'templateid': templateid},
			complete: function() {
				jQuery('#nlw_templateEditlIndicator').hide();
			},
			success: function(data) {
				var error = false;
				try {
					var retData = JSON.parse(data);
				} catch (e) {
					error = true;
				}
				if (!error) {
					// resize and show
					jQuery('#nlw_template_name').val(retData.templatename);
					jQuery('#nlw_template_description').val(retData.description);
					jQuery('#nlw_template_subject').val(retData.subject);

					CKEDITOR.instances.nlw_template_body.setData(retData.body);
					CKEDITOR.instances.nlw_template_body.resize('100%', jQuery('#nlWizRightPane').height() - 205);
				}
			}

		});
	}
}

function nlwCancelEditTemplate() {
	jQuery('#nlw_templateDetails').show();
	jQuery('#nlw_templateEditCont').hide();
	jQuery('#nlwTopButtons').show();
}

function nlwSaveTemplate() {
	var templateid = jQuery('#nlw_templateEditId').val();
	
	//crmv@104558
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();
	var hh = today.getHours();
	var mi = today.getMinutes();
	if(dd<10){dd='0'+dd;} 
	if(mm<10){mm='0'+mm;}
	if(hh<10){hh='0'+hh;}
	if(mi<10){mi='0'+mi;}
	var currDate = dd+'/'+mm+'/'+yyyy+' '+hh+':'+mm;
	jQuery('#nlw_template_name').val(alert_arr.LBL_AUTO_TMP_NAME+' '+currDate); 
	//crmv@104558e
	var postData = {
		'templateid': templateid,
		'templatename' : jQuery('#nlw_template_name').val(),
		'description' : jQuery('#nlw_template_description').val(),
		//crmv@104558
		//'subject' : jQuery('#nlw_template_subject').val(),
		'subject' : '',
		//crmv@104558e
		'body' : CKEDITOR.instances.nlw_template_body.getData(),
	};
	if (postData['templatename'] == '') {
		alert(alert_arr.LBL_TEMPLATE_MUST_HAVE_NAME);
		jQuery('#nlw_template_name').focus();
		return;
	}
	//crmv@104558
	/*
	if (postData['subject'] == '') {
		alert(alert_arr.LBL_MUST_TYPE_SUBJECT);
		jQuery('#nlw_template_subject').focus();
		return;
	}
	*/
	//crmv@104558e
	
	//crmv@55961
	if (postData['body'].indexOf('$Newsletter||tracklink#unsubscription$') == -1) {
		if (confirm(alert_arr.LBL_TEMPLATE_MUST_HAVE_UNSUBSCRIPTION_LINK) == false) {
			return;
		}
	}
	if (postData['body'].indexOf('$Newsletter||tracklink#preview$') == -1) {
		if (confirm(alert_arr.LBL_TEMPLATE_MUST_HAVE_PREVIEW_LINK) == false) {
			return;
		}
	}
	//crmv@55961e

	jQuery('#nlw_templateEditlIndicator').show();
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=savetemplate',
		type: 'POST',
		data: postData,
		complete: function() {
			jQuery('#nlw_templateEditlIndicator').hide();
		},
		success: function(data) {
			var error = false;
			try {
				var retData = JSON.parse(data);
			} catch (e) {
				error = true;
			}
			if (!error) {
				var newid = retData.templateid;
				if (!templateid && newid) {
					// reload list - problems with search
					//SLV.load(200, 'EmailTemplates', null, retData.templatename, 1);
				}
				nlwTemplateSelect(200, 'EmailTemplates', newid, retData.templatename);
				nlwCancelEditTemplate();
			}
		}

	});

}

function nlwGetNewsletterFields() {
	var inputs = jQuery('#nlw_RecordFields :input'),
		values = {};
	inputs.each(function(index, item) {
		values[item.name] = jQuery(item).val();
	})
	return values;
}

function nlwValidateStep(step) {
	step = parseInt(step);
	switch (step) {
		case 1:
			var totalCount = jQuery('#nlw_selTargetsCount').html();
			if (totalCount == '' || totalCount == '0') {
				alert(alert_arr.LBL_SELECT_RECIPIENTS);
				return false;
			}
			break;
		case 2:
			var tplid = jQuery('#nlw_templateid').val();
			if (tplid == '') {
				alert(alert_arr.LBL_SELECT_TEMPLATE);
				return false;
			}
			break;
		case 3:
			var mandFields = jQuery('#nlw_RecordFields .mandatoryField'),
				toFill = [];
			mandFields.each(function(index, item) {
				if (jQuery(item).val() == '') {
					var label = jQuery(item).closest('tr').find('.dvtCellLabel').text().replace('*', '');
					toFill.push(label);
				}
			});
			if (toFill.length > 0) {
				alert(alert_arr.LBL_FILL_FIELDS+":\n"+toFill.join("\n"));
				return false;
			}
			break;
		case 4:
			var okemail = jQuery('#nlw_testEmailOk').html();
			if (okemail != 'OK') {
				alert(alert_arr.LBL_SEND_TEST_EMAIL);
				return false;
			}
			break;
	}
	return true;
}

function nlwInitializeStep(step) {
	step = parseInt(step);
	switch (step) {
		case 5:
			var today = new Date(),
				hour = today.getHours(),
				minute = today.getMinutes(),
				day = today.getDate(),
				month = today.getMonth() + 1;
			if (month < 10) month = '0'+month;
			if (day < 10) day = '0'+day;
			if (hour < 10) hour = '0'+hour;
			if (minute < 10) minute = '0'+minute;
			if (jQuery('#nlw_sendDate').val() == '') {
				jQuery('#nlw_sendDate').val(today.getFullYear() + '-' + month + '-' + day);
			}
			if (jQuery('#nlw_sendTime').val() == '') {
				jQuery('#nlw_sendTime').val(hour+':'+minute);
			}
			break;
	}
	return true;
}

function nlwGetCurrentStep() {
	return parseInt(jQuery('#nlWizRightPane div[id^=nlWizStep]:visible').attr('id').replace('nlWizStep', ''));
}

function nlwGotoStep(step) {
	var currStep = nlwGetCurrentStep(),
		cells = jQuery('#nlWizStepTable .nlWizStepCell'),
		totalSteps = cells.length;

	step = parseInt(step);
	if (step <= 0 || step > totalSteps) return false;

	if (step != currStep) {
		var valid = (step < currStep || nlwValidateStep(currStep));
		if (valid) {
			jQuery('#nlWizStep'+currStep).hide();
			jQuery('#nlWizStep'+step).show();
			jQuery(cells[currStep-1]).removeClass('nlWizStepCellSelected');
			var currCircleIndicator = jQuery(cells[currStep-1]).find('.circleIndicator');
			currCircleIndicator.removeClass('circleEnabled');
			
			jQuery(cells[step-1]).addClass('nlWizStepCellSelected');
			
			var prevCircleIndicator = jQuery(cells[step-1]).find('.circleIndicator');
			prevCircleIndicator.addClass('circleEnabled');
			jQuery('#nlw_nextButton')[step == totalSteps ? 'hide' : 'show']();
			jQuery('#nlw_backButton')[step == 1 ? 'hide' : 'show']();
			return nlwInitializeStep(step);
		}
	}
	return false;
}

function nlwGotoPrevStep() {
	nlwGotoStep(nlwGetCurrentStep() - 1);
}

function nlwGotoNextStep() {
	nlwGotoStep(nlwGetCurrentStep() + 1);
}

function nlwSendTestEmail() {
	var address = jQuery('#nlw_testEmailAddress').val();

	jQuery('#nlw_testEmailOk').html('');
	// very basic address validation
	if (!address.match(/[0-9a-z_.-]+@[0-9a-z_.-]+\.[a-z]{1,5}/i)) {
		alert(alert_arr.LBL_INVALID_EMAIL);
		return false;
	}

	var ids = nlwGetRecipients();

	var postData = {
		'newsletterid' : jQuery('#newsletterid').val(),
		'campaignid' : jQuery('#campaignid').val(),
		'templateid' : jQuery('#nlw_templateid').val(),
		'templatename' : jQuery('#nlw_template_name').val(), //crmv@104558
		'subject' : jQuery('#subject').val(), //crmv@104558
		'recipients': JSON.stringify(ids),
		'test_email_address' : address,
		'skiptargets': (nlwRecipientsChanged ? '0' : '1')
	};

	jQuery.extend(postData, nlwGetNewsletterFields());
	jQuery('#nlw_testEmailTable').hide();
	jQuery('#nlw_testEmailIndicator').show();
	jQuery('#nlw_testEmailStatus').html('').hide();
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=saveandtest',
		type: 'POST',
		data: postData,
		complete: function() {
			jQuery('#nlw_testEmailTable').show();
			jQuery('#nlw_testEmailIndicator').hide();
		},
		success: function(data) {
			try {
				var retData = JSON.parse(data);
				jQuery('#nlw_selTemplate').html(retData.templatename);//crmv@104558
			} catch (e) {
				jQuery('#nlw_testEmailOk').html(alert_arr.ERROR);
				return false;
			}
			if (retData.campaignid > 0) {
				jQuery('#campaignid').val(retData.campaignid);
			}
			if (retData.newsletterid > 0) {
				jQuery('#newsletterid').val(retData.newsletterid);
				nlwRecipientsChanged = false;
			}
			if (retData.mail_status == 'ok') {
				jQuery('#nlw_testEmailOk').html('OK');
				jQuery('#nlw_testEmailStatus').html(alert_arr.LBL_TEST_EMAIL_SENT).show();
				jQuery('#nlw_sendTestEmailButton').hide();
				jQuery('#nlw_resendTestEmailButton').show();
			} else {
				jQuery('#nlw_testEmailOk').html(alert_arr.ERROR);
				jQuery('#nlw_testEmailStatus').html(alert_arr.LBL_ERROR_SENDING_TEST_EMAIL).show();
			}
		},
	});
}

function nlwShowPreview() {
	var ids = nlwGetRecipients();

	var postData = {
		'newsletterid' : jQuery('#newsletterid').val(),
		'campaignid' : jQuery('#campaignid').val(),
		'templateid' : jQuery('#nlw_templateid').val(),
		'recipients': JSON.stringify(ids),
		'skiptargets': (nlwRecipientsChanged ? '0' : '1')
	};

	jQuery.extend(postData, nlwGetNewsletterFields());

	jQuery('#nlw_previewIndicator').show();
	jQuery('#nlw_previewButton').hide();
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=saveonly',
		type: 'POST',
		data: postData,
		complete: function() {
			jQuery('#nlw_previewIndicator').hide();
			jQuery('#nlw_previewButton').show();
		},
		success: function(data) {
			try {
				var retData = JSON.parse(data);
			} catch (e) {
				return false;
			}
			if (retData.campaignid > 0) {
				jQuery('#campaignid').val(retData.campaignid);
			}
			if (retData.newsletterid > 0) {
				jQuery('#newsletterid').val(retData.newsletterid);
				nlwRecipientsChanged = false;
			}
			if (retData.mail_status == 'ok') {
				previewNewsletter(retData.newsletterid);
			} else {
				//alert(alert_arr.ERROR)
			}
		},
	});
}

function nlwSaveAll() {
	var postData = {
		'newsletterid' : jQuery('#newsletterid').val(),
		'sendnow' : (jQuery('#nlw_radioSendNow').is(':checked') ? '1' : '0'),
		'scheduled_date' : jQuery('#nlw_sendDate').val(),
		'scheduled_time' : jQuery('#nlw_sendTime').val(),
	};

	jQuery('#nlw_newsletterSaved').html('');
	jQuery('#nlw_newsletterIndicator').show();
	jQuery('#nlw_newsletterTimes').hide();
	jQuery('#nlw_newsletterStatus').hide();
	jQuery('#nlw_closeButtonDiv').hide();
	jQuery.ajax({
		url: 'index.php?module=Campaigns&action=CampaignsAjax&file=NLWAjax&ajaxaction=saveandsend',
		type: 'POST',
		data: postData,
		complete: function() {
			jQuery('#nlw_newsletterIndicator').hide();

		},
		success: function(data) {
			var error = false;
			try {
				var retData = JSON.parse(data);
				if (retData.success != '1') error = true;
			} catch (e) {
				error = true;
			}

			if (error) {
				jQuery('#nlw_newsletterTimes').show();
				jQuery('#nlw_newsletterStatus').html(alert_arr.LBL_ERROR_SAVING).show();
				jQuery('#nlw_newsletterSaved').html(alert_arr.ERROR);
			} else {
				jQuery('#nlw_newsletterStatus').html(alert_arr.LBL_NEWSLETTER_SCHEDULED).show();
				jQuery('#nlw_closeButtonDiv').show();
				jQuery('#nlw_newsletterSaved').html('OK');
			}
		},
		error: function() {
			jQuery('#nlw_newsletterTimes').show();
			jQuery('#nlw_newsletterSaved').html(alert_arr.ERROR);
		}
	});
}
//crmv@43611e

//crmv@101503
function export_statistics(module,record,title,nr_rows){
	if(nr_rows > 0){
		document.location.href = "index.php?module=Campaigns&action=CampaignsAjax&file=CreateXL&currmodule="+module+"&record="+record+"&title="+title;
	}else{
		alert(alert_arr.ERR_TARGET_XLS);
	}
}

function create_target(module,record,title,targetname,obj,nr_rows){
	if(nr_rows > 0){
		fnvshobj(obj,'ModTarget');
		target=getObj('ModTarget');
		placeAtCenter(target);
		jQuery('#targetname').val(targetname);
		jQuery('#title').val(title);
		jQuery('#campaignid').val(record);
	}else{
		alert(alert_arr.ERR_TARGET);
	}
}

function saveTarget(){
	jQuery('#status').show();
	VtigerJS_DialogBox.block();
	var targetname = jQuery('#targetname').val();
	var title = jQuery('#title').val();
	var campaignid = jQuery('#campaignid').val();
	
	document.location.href = "index.php?module=Campaigns&action=CampaignsAjax&file=CreateTarget&targetname="+targetname+"&title="+title+"&campaignid="+campaignid;
}
//crmv@101503e