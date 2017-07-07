/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

/* crmv@98500 */

function trimfValues(value)
{
    var string_array;
    string_array = value.split(":");
    return string_array[4];
}

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	if (trim(form.folderName.value) == "") {
		isError = true;
		errorMessage += "\nFolder Name";
	}
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert(alert_arr.MISSING_FIELDS + errorMessage);
		return false;
	}
	return true;
}

function re_dateValidate(fldval,fldLabel,type) {
	if(re_patternValidate(fldval,fldLabel,"DATE")==false)
		return false;
	dateval=fldval.replace(/^\s+/g, '').replace(/\s+$/g, '')

	var dateelements=splitDateVal(dateval)

	dd=dateelements[0]
	mm=dateelements[1]
	yyyy=dateelements[2]

	if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	if ((mm==2) && (dd>29)) {//checking of no. of days in february month
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
		alert(alert_arr.ENTER_VALID+fldLabel)
		return false
	}

	switch (parseInt(mm)) {
		case 2 :
		case 4 :
		case 6 :
		case 9 :
		case 11 :if (dd>30) {
						alert(alert_arr.ENTER_VALID+fldLabel)
						return false
					}
	}

	var currdate=new Date()
	var chkdate=new Date()

	chkdate.setYear(yyyy)
	chkdate.setMonth(mm-1)
	chkdate.setDate(dd)

	if (type!="OTH") {
		if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
			return false
		} else return true;
	} else return true;
}

//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function re_patternValidate(fldval,fldLabel,type) {
	if (type.toUpperCase()=="DATE") {//DATE validation

		switch (userDateFormat) {
			case "yyyy-mm-dd" :
								var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/
								break;
			case "mm-dd-yyyy" :
			case "dd-mm-yyyy" :
								var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/
		}
	}

	if (type.toUpperCase()=="TIMESECONDS") {//TIME validation
		var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$");
	}
	if (!re.test(fldval)) {
		alert(alert_arr.ENTER_VALID + fldLabel)
		return false
	}
	else return true
}

//added to fix the ticket #5117
function standardFilterDisplay()
{
	if(document.NewReport.stdDateFilterField.options.length <= 0 || (document.NewReport.stdDateFilterField.selectedIndex > -1 && document.NewReport.stdDateFilterField.options[document.NewReport.stdDateFilterField.selectedIndex].value == "Not Accessible"))
	{
		getObj('stdDateFilter').disabled = true;
		getObj('startdate').disabled = true;getObj('enddate').disabled = true;
		getObj('jscal_trigger_date_start').style.visibility="hidden";
		getObj('jscal_trigger_date_end').style.visibility="hidden";
	}
	else
	{
		getObj('stdDateFilter').disabled = false;
		getObj('startdate').disabled = false;
		getObj('enddate').disabled = false;
		getObj('jscal_trigger_date_start').style.visibility="visible";
		getObj('jscal_trigger_date_end').style.visibility="visible";
	}
}

function showReportTab(tabname, tdtab) {
	var tablist = ['trReportCount', 'trReportTotal', 'trReportMain', 'trReportCharts']; // crmv@30014
	var tabtdlist = ['tdTabReportCount', 'tdTabReportTotal', 'tdTabReportMain', 'tdTabReportCharts']; // crmv@30014
	var tdid = tdtab.id;

	for (var i=0; i<tablist.length; ++i) {
		if (tabname == tablist[i]) {
			jQuery('#'+tablist[i]).show();
		} else {
			jQuery('#'+tablist[i]).hide();
		}
	}

	for (var i=0; i<tabtdlist.length; ++i) {
		if (tdid == tabtdlist[i]) {
			jQuery('#'+tabtdlist[i]).removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');
		} else {
			jQuery('#'+tabtdlist[i]).removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
		}
	}
	
	// crmv@96742
	
	// recalc the fixed header, since might be hidden
	var tables = jQuery.fn.dataTable.tables({visible: false, api: true});
	if (tables && tables.fixedHeader) {
		tables.fixedHeader.adjust();
	}
	
	// crmv@82770
	if (tabname == 'trReportCharts' && window.VTECharts) {
		VTECharts.refreshAll();
	}
	// crmv@82770e
}

// crmv@31209
function getActiveTab() {
	var tablist = ['tdTabReportCount', 'tdTabReportTotal', 'tdTabReportMain', 'tdTabReportCharts']; // crmv@30014

	for (var i=0; i<tablist.length; ++i) {
		if (jQuery('#'+tablist[i]).hasClass('dvtSelectedCell')) return tablist[i].replace('tdTabReport', '');
	}
	return '';
}
// crmv@31209

//crmv@44323
function budgetParams(id) {
	var bpu_check = jQuery('#budgetPerUser').is(':checked') ? 'true' : 'false';
	return '&budgetPeriod='+encodeURIComponent(jQuery('#budgetPeriod').val())+
			'&budgetSubperiod='+encodeURIComponent(jQuery('#budgetSubperiod').val())+
			'&budgetPerUser='+encodeURIComponent(bpu_check);
}

function reloadBudget(clearsp) {
	var url = location.href,
		bpu_check = jQuery('#budgetPerUser').is(':checked') ? 'true' : 'false';
	location.href = url.replace(/&budgetPeriod=.+&?/, '').replace(/&budgetSubperiod=.+&?/, '') +
		'&budgetPeriod=' + jQuery('#budgetPeriod').val() +
		'&budgetSubperiod=' + (clearsp ? '' : jQuery('#budgetSubperiod').val())+
		'&budgetPerUser='+encodeURIComponent(bpu_check);
}
//crmv@44323e

function createrepFolder(oLoc,divid)
{
	$('editfolder_info').innerHTML=' '+ReportLabels.LBL_ADD_NEW_GROUP+' ';
	getObj('fldrsave_mode').value = 'save';
	$('folder_id').value = '';
	$('folder_name').value = '';
	$('folder_desc').value='';
	fnvshobj(oLoc,divid);
}

function DeleteFolder(id)
{
	var title = 'folder'+id;
	var fldr_name = getObj(title).innerHTML;
        if(confirm(ReportLabels.DELETE_FOLDER_CONFIRMATION+fldr_name +"' ?"))
	{
		new Ajax.Request(
			'index.php',
	                {queue: {position: 'end', scope: 'command'},
        	                method: 'post',
                	        postBody: 'action=ReportsAjax&mode=ajax&file=DeleteReportFolder&module=Reports&record='+id,
                        	onComplete: function(response) {
							var item = trim(response.responseText);
							if(item.charAt(0)=='<')
						        getObj('customizedrep').innerHTML = item;
						    else
						    	alert(item);
                        	}
                	}
        	);
	}
	else
	{
		return false;
	}
}

function AddFolder()
{
	if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
                alert(ReportLabels.FOLDERNAME_CANNOT_BE_EMPTY);
                return false;
	}
	else if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length > 20 )
	{
                alert(ReportLabels.FOLDER_NAME_ALLOW_20CHARS);
                return false;
	}
	else if((getObj('folder_name').value).match(/['"<>/\+]/) || (getObj('folder_desc').value).match(/['"<>/\+]/))
    {
            alert(alert_arr.SPECIAL_CHARS+' '+alert_arr.NOT_ALLOWED+alert_arr.NAME_DESC);
            return false;
    }
	/*else if((!CharValidation(getObj('folder_name').value,'namespace')) || (!CharValidation(getObj('folder_desc').value,'namespace')))
	{
			alert(alert_arr.NO_SPECIAL +alert_arr.NAME_DESC);
			return false;
	}*/
	else
	{
		var foldername = encodeURIComponent(getObj('folder_name').value);
		new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&mode=ajax&file=CheckReport&module=Reports&check=folderCheck&folderName='+foldername,
                                onComplete: function(response) {
				var folderid = getObj('folder_id').value;
				var resresult =response.responseText.split("::");
				var mode = getObj('fldrsave_mode').value;
				if(resresult[0] != 0 &&  mode =='save' && resresult[0] != 999)
				{
					alert(ReportLabels.FOLDER_NAME_ALREADY_EXISTS);
					return false;
				}
				else if(((resresult[0] != 1 && resresult[0] != 0) || (resresult[0] == 1 && resresult[0] != 0 && resresult[1] != folderid )) &&  mode =='Edit' && resresult[0] != 999)
					{
                                                alert(ReportLabels.FOLDER_NAME_ALREADY_EXISTS);
                                                return false;
					}
				else if(response.responseText == 999) // 999 check for special chars
					{
                                                alert(ReportLabels.SPECIAL_CHARS_NOT_ALLOWED);
                                                return false;
					}
				else
					{
						fninvsh('orgLay');
						var folderdesc = encodeURIComponent(getObj('folder_desc').value);
						getObj('folder_name').value = '';
						getObj('folder_desc').value = '';
						foldername = foldername.replace(/^\s+/g, '').replace(/\s+$/g, '');
                                                foldername = foldername.replace(/&/gi,'*amp*');
                                                folderdesc = folderdesc.replace(/^\s+/g, '').replace(/\s+$/g, '');
                                                folderdesc = folderdesc.replace(/&/gi,'*amp*');
						if(mode == 'save')
						{
							url ='&savemode=Save&foldername='+foldername+'&folderdesc='+folderdesc;
						}
						else
						{
							var folderid = getObj('folder_id').value;
							url ='&savemode=Edit&foldername='+foldername+'&folderdesc='+folderdesc+'&record='+folderid;
						}
						getObj('fldrsave_mode').value = 'save';
						new Ajax.Request(
				                        'index.php',
				                        {queue: {position: 'end', scope: 'command'},
			                                method: 'post',
			                                postBody: 'action=ReportsAjax&mode=ajax&file=SaveReportFolder&module=Reports'+url,
			                                onComplete: function(response) {
			                                        var item = response.responseText;
                        			                getObj('customizedrep').innerHTML = item;
			                                }
						}

				                );
					}
				}
			}
			);

	}
}


function EditFolder(id,name,desc)
{
	$('editfolder_info').innerHTML= ' '+ReportLabels.LBL_RENAME_FOLDER+' ';
	getObj('folder_name').value = name;
	getObj('folder_desc').value = desc;
	getObj('folder_id').value = id;
	getObj('fldrsave_mode').value = 'Edit';
}

// crmv@30967
function massDeleteReport() {
	var repids = [];
	jQuery('#report_form input[id^=check_report]:checked').each(function(idx, el) {
		repids.push(el.id.replace('check_report_', ''));
	});

	var idstring = '';
	var count = repids.length;
	idstring = repids.join(':');

	if(idstring != '')
	{
                if(confirm(ReportLabels.DELETE_CONFIRMATION+" "+count+alert_arr.RECORDS))
       		{
			new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'action=ReportsAjax&mode=ajax&file=Delete&module=Reports&idlist='+idstring,
                                onComplete: function(response) {
                                        if (response.responseText.match(/ERROR::.*/)) {
                                            window.alert('Access Denied');
                                        } else {
                                            location.reload();
                                        }
                                }
                        }
                );
		}else {
			return false;
		}

	}else {
			alert(ReportLabels.SELECT_ATLEAST_ONE_REPORT);
			return false;
	}
}

function DeleteReport(id) {
    if (confirm(ReportLabels.DELETE_REPORT_CONFIRMATION))
	{
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
			        method: 'post',
			        postBody: 'action=ReportsAjax&file=Delete&module=Reports&record='+id,
			        onComplete: function(response) {
			        	if (response.responseText.match(/ERROR::.*/)) {
			                window.alert('Access Denied');
			            } else {
			                location.reload();
			            }
			        }
			}
		);
	} else {
		return false;
	}
}

function showMoveReport(elem) {

	var selcount = jQuery('#report_form input[id^=check_report]:checked').length;
	if (selcount == 0) {
		return window.alert(ReportLabels.SELECT_ATLEAST_ONE_REPORT);
	} else {
		return showFloatingDiv('ReportMove', elem);
	}
}

function MoveReport(id,foldername) {
	var repids = [];
	jQuery('#report_form input[id^=check_report]:checked').each(function(idx, el) {
		repids.push(el.id.replace('check_report_', ''));
	});

	var idstring = repids.join(':');

	if (idstring != '') {
		var destid = jQuery('#select_move_report').val();
		if (destid > 0) {
			new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
					method: 'post',
				    postBody: 'action=ReportsAjax&mode=ajax&file=ChangeFolder&module=Reports&folderid='+destid+'&idlist='+idstring,
				    onComplete: function(response) {
						location.reload();
					}
				}
            );
		}
	}else {
			alert(ReportLabels.SELECT_ATLEAST_ONE_REPORT);
			return false;
	}

}
// crmv@30967e

function CrearEnlace(tipo,id, ajaxfile){
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
		stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].value;

	var stdDateFiltervalue = '';
	if(document.NewReport.stdDateFilter.selectedIndex != -1)
		stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value;

	var ajaxparam = '';
	if (tipo == 'ReportsAjax' && ajaxfile != undefined && ajaxfile != '') ajaxparam = '&file='+ajaxfile;

	return "index.php?module=Reports&action="+tipo+ajaxparam+"&record="+id+"&stdDateFilterField="+stdDateFilterFieldvalue+"&stdDateFilter="+stdDateFiltervalue+"&startdate="+document.NewReport.startdate.value+"&enddate="+document.NewReport.enddate.value+"&folderid="+getObj('folderid').value+getSdkParams(id);	//crmv@sdk-25785
}

/**
 * @deprecated
 * Please use Report.refreshTable
 */
function generateReport(id) {
	return Reports.refreshTables(id);
}

function ReportInfor() {
	// crmv@49622
	if (typeof window.report_info_override != 'undefined' && window.report_info_override !== null) {
		getObj('report_info').innerHTML = window.report_info_override;
		return;
	}
	// crmv@49622e
	var stdDateFilterFieldvalue = '';
	if(document.NewReport.stdDateFilterField.selectedIndex != -1)
		stdDateFilterFieldvalue = document.NewReport.stdDateFilterField.options  [document.NewReport.stdDateFilterField.selectedIndex].text;

	var stdDateFiltervalue = '';
	if(document.NewReport.stdDateFilter.selectedIndex != -1)
		stdDateFiltervalue = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].text;

	var startdatevalue = document.NewReport.startdate.value;
	var enddatevalue = document.NewReport.enddate.value;

	if(startdatevalue != '' && enddatevalue=='')
	{
		var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"   (from  '+startdatevalue+' )';
	}else if(startdatevalue == '' && enddatevalue !='')
	{
		var reportinfr = 'Reporting  "'+stdDateFilterFieldvalue+'"   (  till  '+enddatevalue+')';
	}else if(startdatevalue == '' && enddatevalue =='')
	{
        var reportinfr = ReportLabels.NO_FILTER_SELECTED;
	}else if(startdatevalue != '' && enddatevalue !='')
	{
		var reportinfr = ReportLabels.LBL_REPORTING+' "'+stdDateFilterFieldvalue+'" '+alert_arr.LBL_WITH+' "'+stdDateFiltervalue+'"  ( '+startdatevalue+'  to  '+enddatevalue+' )'; // crmv@29686
	}
	getObj('report_info').innerHTML = reportinfr;
}

/**
 * New interface for reports methods
 * TODO: slowly, move all the functions here
 */
var Reports = {
	
	initialize: function() {

		var filter = getObj('stdDateFilter').options[document.NewReport.stdDateFilter.selectedIndex].value;
		if (filter != "custom") {
			showDateRange( filter );
		}
		
		// If current user has no access to date fields, we should disable selection
		// Fix for: #4670
		standardFilterDisplay();
		ReportInfor();	
	},
	
	getReportid: function() {
		return document.NewReport.record.value;
	},
	
	openPopup: function(url) {
		openPopup(url, "ReportWindow","width=790px,height=630px,scrollbars=yes", 'auto'); //crmv@21048m
	},
	
	createNew: function(folderid, duplicateFrom, formodule) {
		var arg ='index.php?module=Reports&action=ReportsAjax&file=EditReport';
		if (folderid) arg += '&folder='+folderid;
		if (duplicateFrom) arg += '&duplicate='+duplicateFrom;
		if (formodule) arg += '&formodule='+formodule;
		this.openPopup(arg);
	},
	
	editReport: function(reportid) {
		if (!reportid) return;
		var arg ='index.php?module=Reports&action=ReportsAjax&file=EditReport&record='+reportid;
		this.openPopup(arg);
	},
	
	showExportOptions: function(reportid, type) {
		var me = this;
		
		var types = ['print', 'pdf', 'xls'];

		var hasSummary = parseInt(jQuery('#reportHasSummary').val());
		var hasTotals = parseInt(jQuery('#reportHasTotals').val());

		if (!hasSummary && !hasTotals) {
			// call directly export function
			return me.startExport(reportid, type);
		}

		showFloatingDiv('ReportExport');

		for (var i=0; i<types.length; ++i) {
			if (types[i] == type) {
				jQuery('#report_export_button_'+types[i]).show();
				jQuery('#report_choose_button_'+types[i]).show();
			} else {
				jQuery('#report_export_button_'+types[i]).hide();
				jQuery('#report_choose_button_'+types[i]).hide();
			}
		}
	},
	
	startExport: function(reportid, type) {
		var extraopts = jQuery('#export_report_list').serialize();
		if (extraopts == '') {
			return window.alert(ReportLabels.LBL_CHOOSE_EMPTY);
		} else {
			extraopts = '&'+extraopts;
		}

		switch (type) {
			case 'pdf': 
				document.location.href = CrearEnlace('CreatePDF',reportid) + extraopts; 
				break;
			case 'xls': 
				document.location.href = CrearEnlace('CreateXL',reportid) + extraopts;
				break;
			case 'print':
				window.open(CrearEnlace('ReportsAjax',reportid,'PrintReport') + extraopts, ReportLabels.LBL_Print_REPORT,"width=800,height=650,resizable=1,scrollbars=1,left=100");
				break;
		}

		hideFloatingDiv('ReportExport');
	},
	
	validateStdFilter: function() {
		var me = this;
		
		var stdDateFilterFieldvalue = jQuery('#stdDateFilterField').val();
		var stdDateFiltervalue = jQuery('#stdDateFilter').val();
		var startdatevalue = document.NewReport.startdate.value;
		var enddatevalue = document.NewReport.enddate.value;
		
		if ((startdatevalue != '') || (enddatevalue != '')) {

			if(!dateValidate("startdate","Start Date","D"))
				return false

			if(!dateValidate("enddate","End Date","D"))
				return false

			if(!dateComparison("startdate",'Start Date',"enddate",'End Date','LE'))
				return false;
		}
		
		return true;
	},
	
	refreshTables: function(reportid) {
		var me = this;
		
		if (!me.validateStdFilter()) return false;
		
		$("status").style.display = "inline";
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?action=ReportsAjax&file=SaveAndRun&mode=ajax&module=Reports&record='+reportid+"&folderid="+getObj('folderid').value,
			data: me.getExtraAjaxParams(),
			success: function(response) {
				$("status").style.display = "none";
				jQuery('#Generate').html(response);
				// Performance Optimization: To update record count of the report result
				//var __reportrun_directoutput_recordcount_scriptnode = $('__reportrun_directoutput_recordcount_script');
				//if(__reportrun_directoutput_recordcount_scriptnode) { eval(__reportrun_directoutput_recordcount_scriptnode.innerHTML); }
				// END
				setTimeout("ReportInfor()",1);
			},
			error: function() {
				$("status").style.display = "none";
			}
		});

	},
	
	getExtraAjaxParams: function() {
		var me = this,
			reportid = me.getReportid();
			
		var stdDateFilterFieldvalue = jQuery('#stdDateFilterField').val();
		var stdDateFiltervalue = jQuery('#stdDateFilter').val();
		var startdatevalue = jQuery('#jscal_field_date_start').val();
		var enddatevalue = jQuery('#jscal_field_date_end').val();
		
		var params = {
			stdDateFilterField: stdDateFilterFieldvalue,
			stdDateFilter: stdDateFiltervalue,
			startdate: startdatevalue,
			enddate: enddatevalue,
			tab: getActiveTab(),
		}
		
		var sdkparams = getSdkParams(reportid); //crmv@sdk-25785 crmv@31209
		if (sdkparams) {
			// transform to object
			sdkparams = sdkparams.replace(/^&/, '');
			var sdkobj = JSON.parse('{"' + decodeURI(sdkparams).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
			jQuery.extend(params, sdkobj || {});
		}
		return params;
	}
	
}

var ReportTable = {
	
	reportid: null,
	folderid: null,
	
	lengthMenu: [20, 50, 100, 200],
	baseUrl: 'index.php?module=Reports&action=ReportsAjax&file=SaveAndRun&mode=ajax&format=json',
	
	table: null,
	summaryTable: null,
	
	initialize: function(reportid, folderid, params, options) {
		var me = this;
		
		me.reportid = reportid;
		me.folderid = folderid;
		
		options = options || {};
		
		me.initMainTable(params, options);
		me.initSummaryTable();
	},
	
	initMainTable: function(params, options) {
		var me = this;
		
		var pageSize = params.pageSize;
		var totalRecords = params.totalRecords;
		var columns = params.columns;

		if (me.lengthMenu.indexOf(pageSize) == -1) me.lengthMenu.push(pageSize);
		me.lengthMenu.sort(function(a,b) {return a - b;});
		
		// prepare the columns
		jQuery.each(columns, function(idx, col) {
			col.name = col.column;
			col.data = col.column+'.value';
		});
		
		// Setup - add a text input to each footer cell
		jQuery('#tableContentMain thead th').each(function(idx) {
			var title = jQuery(this).text();
			if (columns[idx].searchable === true) {
				// crmv@118320
				var data = 'data-uitype="'+columns[idx].uitype+'" data-wstype="'+columns[idx].wstype+'"';
				jQuery(this).append('<br><div class="dvtCellInfo"><input class="detailedViewTextBox" type="text" placeholder="'+alert_arr.LBL_SEARCH+' '+title+'" '+data+' /></div>');
				// crmv@118320e
			}
		});

		me.table = jQuery('#tableContentMain').DataTable({
			
			// paging
			paging: true,
			lengthMenu: me.lengthMenu,
			pageLength: pageSize,
			
			// ordering
			ordering: true,
			orderMulti: false,	// disabled for the moment
			
			// searching
			searching: true,
			search: {
				caseInsensitive: true,
				smart: false,	// disabled for the moment
			},
			
			fixedHeader: {
				headerOffset: jQuery('#vte_menu').outerHeight(),
			},
			
			// server side processing
			deferLoading: totalRecords,
			processing: true,
			serverSide: true,
			ajax: {
				url: me.baseUrl + '&folderid='+me.folderid+'&record='+me.reportid,
				data: me.appendAjaxData,
				type: 'POST',
				// crmv@106004
				beforeSend: function(){
					//crmv@91082
					if(!SessionValidator.check()) {
						SessionValidator.showLogin();
						jQuery('#modalProcessingDiv').hide();
						jQuery('#status').hide();
						return false;
					}
					//crmv@91082e
				}
				//crmv@106004e
			},
			
			// column definitions
			columns: columns,
			
			// internationalization
			language: {
				url: "include/js/dataTables/i18n/"+(window.current_language || "en_us")+".lang.json"
			},
			
			// apply some transformations after ajax calls
			fnRowCallback: function(tr, aData, iRow, iDisplayIndexFull) {
				for (colname in aData) {
					var cdata = aData[colname];
					if (cdata && cdata.class) {
						var cell = this.api().cell(iRow, colname+':name');
						var node = cell.node();
						// apply the class
						jQuery(node).addClass(cdata.class);
					}
				}
			},

		});
		
		// wait for the table to be initialized
		me.table.on('init.dt', function() {
		
			// handle the generic search on return key, not keypress
			jQuery('.dataTables_filter input').off();
			jQuery('.dataTables_filter input').on('keyup', function(e) {
				if(e.keyCode == 13) {
					me.table.search(this.value).draw();
				}
			});
			
		}).on('preXhr.dt', function() {
			var zindex = jQuery(this).zIndex();
			jQuery('#modalProcessingDiv').css('z-index', zindex+1).show();
			jQuery('#tableContentMain_processing').css('z-index', zindex+2);
			jQuery('#status').show();
		}).on('xhr.dt', function() {
			jQuery('#modalProcessingDiv').hide();
			jQuery('#status').hide();
		});
		
		// Apply the search per column
		me.table.columns().every(function (idx) {
			var that = this,
				header = this.header();
				
			// prevent propagation
			jQuery('input', header).on('click focus', function (event) {
				return false;
			});
 
			// use keypress, since the th has a listener on it and fires the redraw
			jQuery('input', header).on('keypress', function (event) {
				// crmv@118320
				var uitype = parseInt(jQuery(this).data('uitype'));
				var wstype = jQuery(this).data('wstype');
				if (event.type == 'keypress' && event.keyCode == 13 && that.search() !== this.value) {
					if (me.validateColSearch(this.value, uitype, wstype)) {
						var searchvalue = me.alterColSearchValue(this.value, uitype, wstype);
						that.search(searchvalue).draw();
					}
					return false;
				}
				// crmv@118320e
			});
			
		});
	},
	
	// crmv@118320
	validateColSearch: function(value, uitype, wstype) {
	
		if (uitype == 7 || uitype == 9 || uitype == 71 || uitype == 72) {
			// validate as user number
			if (!validateUserNumber(value)) {
				alert(alert_arr.LBL_ENTER_VALID_NO);
				return false;
			}
		}
		
		return true;
	},
	
	alterColSearchValue: function(value, uitype, wstype) {
	
		if (uitype == 7 || uitype == 9 || uitype == 71 || uitype == 72) {
			// convert a user number to a standard float number,but keep it as a string!
			if (thousands_separator != '') value = value.replace(thousands_separator, '');
			if (decimal_separator != '') value = value.replace(decimal_separator, '.');
		}
		
		return value;
	},
	// crmv@118320e
	
	initSummaryTable: function(params, options) {
		var me = this;
		
		me.summaryTable = jQuery('#tableContentCount').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			processing: false,
			fixedHeader: {
				headerOffset: jQuery('#vte_menu').outerHeight(),
			},
		});
	},
	
	appendAjaxData: function(data) {
		var me = ReportTable;
		
		// alter the data sent to server appending more informations
		var params = Reports.getExtraAjaxParams();
		if (params) {
			jQuery.extend(data, params);
		}
		
	},
	
	/*refreshAll: function(callback) {
		
	},
	
	refreshTable: function(tab, callback) {
		
	}*/
}