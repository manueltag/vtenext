/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

 function loadCvListTargets(type,id) {
	var element = type+"_cv_list";
	var value = document.getElementById(element).value;        

	var filter = $(element)[$(element).selectedIndex].value	;
	if(filter=='None')return false;
	if(value != '') {
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Targets&action=TargetsAjax&file=LoadList&ajax=true&return_action=DetailView&return_id='+id+'&list_type='+type+'&cvid='+value,
				onComplete: function(response) {
					$("status").style.display="none";
					parent.reloadTurboLift('Targets', id, type);	//crmv@52414
				}
			}
		);
	}
}
//crmv@36539
function return_report_to_rl(id,name,field) {
	jQuery('#'+field).val(id);
	jQuery('#'+field+"_display").val(name);
	disableReferenceField(jQuery('#'+field+'_display')[0]);
}
function popupReport_rl(mode,module,title,field) {
	var url = '';
	if (mode == 'edit') {
		var reportid = jQuery('#'+field).val();
		if (reportid == '') {
			return false;
		}
		var arg = 'index.php?module=Reports&action=ReportsAjax&file=NewReport1&return_module=Targets:'+field+'&skipsecmodule=1&reportmodule='+module+'&reportname='+title+'&record='+reportid;
	} else {
		var arg = 'index.php?module=Reports&action=ReportsAjax&file=NewReport0&return_module=Targets:'+field+'&reportmodule='+module+'&reportname='+title;
	}
	openPopup(arg);
}
 function loadReportListTargets(reportid,targetid,relatedmodule) {
	if(reportid=='')return false;
	if(reportid != '') {
		$("status").style.display="inline";
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody: 'module=Targets&action=TargetsAjax&file=LoadReport&ajax=true&return_action=DetailView&return_id='+targetid+'&reportid='+reportid+'&relatedmodule='+relatedmodule,
				onComplete: function(response) {
					$("status").style.display="none";
					parent.reloadTurboLift('Targets', targetid, relatedmodule);	//crmv@52414
				}
			}
		);
	}
}
//crmv@36539 e