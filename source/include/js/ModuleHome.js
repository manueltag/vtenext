/* crmv@83340 crmv@96155 crmv@98431 crmv@104259 crmv@105193 crmv@128159 */

var ModuleHome = {
	
	dragLibrary: 'Scriptaculous',	// can be Scriptaculous or jQueryUI
	
	blocksCache: {},
	
	reportData: null,
	
	editMode: false,
	
	initialize: function(containerid) {
		var me = this;
		
		if (!document.getElementById(containerid)) {
			// no container, skip
			return;
		}

		if (me.dragLibrary == 'Scriptaculous' && window.Sortable) {
			Sortable.create(
				containerid,
				{
					constraint: false,
					tag: 'div', 
					overlap: 'Horizontal',
					handle: 'headerrow',
					onUpdate: function() {
						me.saveSequence();
					}
				}
			);
		} else if (me.dragLibrary == 'jQueryUI') {
			jQuery('#'+containerid).sortable({
				handle: '.headerrow',
				opacity: 0.75,
				revert: 200,
				update: function() {
					me.saveSequence();
				}
			});
		} else {
			console.log('Unable to initialize the sorting library');
		}
		
		// resize handler
		if (jQuery.throttle) {
			jQuery(window).on('resize', jQuery.throttle(500, me.onWindowResize).bind(me));
		} else {
			console.log('jQuery throttle plugin not found. The resize event won\'t be intercepted for performance reasons');
		}
	},
	
	onWindowResize: function() {
		var me = this;
		for (var blockid in me.blocksCache) {
			me.positionBlock.apply(me, me.blocksCache[blockid]);
		}
	},
	
	enterEditMode: function() {
		var me = this;
		
		if (me.editMode) return;
		
		// hide the buttons
		jQuery('#moduleSettingsTd').hide();
		jQuery('#moduleSettingsResetTd').show();
		jQuery('#add_home_views').show();
		
		var cont = jQuery('#Buttons_List_HomeMod');
		cont.find('span[id^=pencil_]>i').show();
		
		me.editMode = true;
	},
	
	leaveEditMode: function() {
		var me = this;
		
		if (!me.editMode) return;
		
		// show the buttons
		jQuery('#moduleSettingsTd').show();
		jQuery('#moduleSettingsResetTd').hide();
		jQuery('#add_home_views').hide();
		
		var cont = jQuery('#Buttons_List_HomeMod');
		cont.find('span[id^=pencil_]>i').hide();
		
		me.editMode = false;
	},
	
	toggleEditMode: function() {
		var me = this;
		
		if (me.editMode) {
			return me.leaveEditMode();
		} else {
			return me.enterEditMode();
		}
	},
	
	saveSequence: function() {
		var me = this;
		var modhomeid = jQuery('#modhomeid').val();
		var blocks = [];
		
		jQuery('#MainMatrix .modblock').each(function(index, item) {
			var id = parseInt(item.id.replace('modblock_', ''));
			if (id > 0) {
				blocks.push(id);
			}
		});
		
		if (blocks.length == 0) return;
		var params = {
			modhomeid: modhomeid,
			blockids: blocks.join(':'),
		}
		
		me.ajaxRequest('savesequence', params);
	},
	
	changeView: function(modhomeid) {
		var me = this;
		var url = "index.php?module="+gVTModule+'&action=HomeView&modhomeid='+modhomeid;
		if (me.editMode) url += '&editmode=1';
		location.href = url;
	},
	
	addView: function() {
		showFloatingDiv('ModHomeAddView', null, {modal: true});
	},
	
	//crmv@102334
	addListView: function() {
		var me = this;
		
		me.ajaxRequest('cvlist', {}, null, function(data) {
			jQuery('#homecvid').html(data);
			showFloatingDiv('ModHomeAddListView', null, {modal: true});
		});
	},
	//crmv@102334e
	
	addReportView: function() {
		var me = this;
		
		me.ajaxRequest('reportlist', {}, null, function(data) {
			me.reportData = data;
			me.buildReportChooser(data);
			showFloatingDiv('ModHomeAddViewReport', null, {modal: true});
		});
	},
	
	buildReportChooser: function(data) {
		var me = this,
			target = jQuery('#reportChooserFolder');

		// TODO: Do it properly with css/tpl
		var html = '';
		jQuery.each(data, function(folderid, folder) {
			if (folder && folder.reports && folder.reports.length > 0) {
				html += '<div style="float:left;padding:10px;width:140px;height:140px;text-align:center"><div><img src="themes/softed/images/listview_folder.png" border="0" width="96" style="cursor:pointer" onclick="ModuleHome.clickReportFolder(this, '+folderid+');"/></div><div style="text-align:center">'+folder.foldername+' ('+folder.reports.length+')</div></div>';
			}
		});
		
		// clean the id
		jQuery('#chooserReportName').val('');
		jQuery('#chooserReportId').val('');
		jQuery('#reportChooserList').hide();
		
		target.html(html).show();
	},
	
	clickReportFolder: function(self, folderid) {
		var me = this,
			html = '',
			folder = me.reportData[folderid],
			list = folder.reports;

		// TODO: Do it properly with css/tpl
		html += '<div style="margin:8px;font-weight:bold"><img src="themes/'+current_theme+'/images/folderback.png" style="cursor:pointer" align="bottom" onclick="ModuleHome.clickReportBack(this);" alt="'+alert_arr.LBL_BACK+'" title="'+alert_arr.LBL_BACK+'" border="0"/>&nbsp;&nbsp;'+folder.foldername+(folder.description ? '<span style="color:#C0C0C0;font-style:italic"> - '+folder.description+'</span>' : '') + '</div>';
		html += '<table width="95%" style="margin:12px">';
		html += '<tr><td class="lvtCol">'+alert_arr.LBL_REPORT_NAME+'</td><td class="lvtCol">'+alert_arr.LBL_DESCRIPTION+'</td></tr>';
		jQuery.each(list, function(idx, report) {
			html += '<tr><td class="lvtColData"><a href="javascript:;" onclick="ModuleHome.clickReport(this, '+folderid+','+report.reportid+');">'+report.reportname+'</a></td><td class="lvtColData">'+report.description+'</td></tr>';
		});
		html += "</table>";
			
		jQuery('#reportChooserFolder').hide();
		jQuery('#reportChooserList').html(html).show();
	},
	
	clickReportBack: function(self) {
		var me = this;
		
		jQuery('#reportChooserList').hide();
		jQuery('#reportChooserFolder').show();
	},
	
	clickReport: function(self, folderid, reportid) {
		var me = this,
			folder = me.reportData[folderid],
			list = folder.reports;
		
		jQuery.each(list, function(idx, report) {
			if (report.reportid == reportid) {
				var name = jQuery('#homeviewname2').val();
				jQuery('#chooserReportName').val(report.reportname);
				jQuery('#chooserReportId').val(report.reportid);
				if (!name) jQuery('#homeviewname2').val(report.reportname);
				return false;
			}
		});
		
		jQuery('#reportChooserList').hide();
		jQuery('#reportChooserFolder').show();
	},
	
	createView: function() {
		var me = this;
		var name = jQuery('#homeviewname').val();
		
		if (!name) {
			return vtealert(alert_arr.ENTER_VALID+' '+alert_arr.LBL_NAME);
		}
		
		me.ajaxRequest('addview', {viewname: name}, null, function(homeid) {
			if (homeid > 0) {
				var newloc = window.location.href.replace(/&modhomeid=[0-9]*/, '');
				var url = newloc.replace(/&editmode=[01]/, '') + '&modhomeid='+homeid;
				if (me.editMode) url += '&editmode=1';
				window.location.href = url;
			}
		});
	},
	
	//crmv@102334
	createListView: function() {
		var me = this;
		var name = jQuery('#homeviewname3').val(),
			cvid = jQuery('#homecvid').val();
		
		if (!name) {
			return vtealert(alert_arr.ENTER_VALID+' '+alert_arr.LBL_NAME);
		}
		
		me.ajaxRequest('addview', {viewname: name, cvid: cvid}, null, function(homeid) {
			if (homeid > 0) {
				var newloc = window.location.href.replace(/&modhomeid=[0-9]*/, '');
				var url = newloc.replace(/&editmode=[01]/, '') + '&modhomeid='+homeid;
				if (me.editMode) url += '&editmode=1';
				window.location.href = url;
			}
		});
	},
	//crmv@102334e
	
	createReportView: function() {
		var me = this,
			name = jQuery('#homeviewname2').val(),
			reportid = jQuery('#chooserReportId').val();
		
		if (!name) {
			return vtealert(alert_arr.ENTER_VALID+' '+alert_arr.LBL_NAME);
		}
		
		if (!reportid) {
			return vtealert(alert_arr.ENTER_VALID+' Report');
		}
		
		me.ajaxRequest('addview', {viewname: name, reportid: reportid}, null, function(homeid) {
			if (homeid > 0) {
				var newloc = window.location.href.replace(/&modhomeid=[0-9]*/, '');
				var url = newloc.replace(/&editmode=[01]/, '') + '&modhomeid='+homeid;
				if (me.editMode) url += '&editmode=1';
				window.location.href = url;
			}
		});
	},
	
	removeView: function(modhomeid, name, ask) {
		var me = this,
			currentId = jQuery('#modhomeid').val();
		
		if (ask) {
			vteconfirm(alert_arr.ARE_YOU_SURE, function(yes) {
				if (yes) doRemove();
			});
		} else {
			doRemove();
		}
		
		function doRemove() {
			me.ajaxRequest('removeview', {modhomeid: modhomeid}, null, function() {
				if (modhomeid == currentId) {
					var newloc = window.location.href.replace(/&modhomeid=[0-9]*/, '');
					var url = newloc.replace(/&editmode=[01]/, '');
					if (me.editMode) url += '&editmode=1';
					window.location.href = url;
				} else {
					window.location.reload();
				}
			});
		}
	},
	
	showLoader: function(blockid) {
		jQuery('#refresh_'+blockid).html(jQuery('#modhome_loader').html());
		
	},
	
	hideLoader: function(blockid) {
		jQuery('#refresh_'+blockid).html('');
	},
	
	ajaxRequest: function(action, params, options, success, failure) {
		var me = this,
			module = gVTModule;

		// default options
		options = jQuery.extend({}, {
			showBlockLoader: true,
			rawData: false,
		}, options || {});
		
		if (options.showBlockLoader && params.blockid) me.showLoader(params.blockid);
		jQuery.ajax({
			url: 'index.php?module='+module+'&action='+module+'Ajax&file=HomeAjax&ajxaction='+action,
			method: 'GET',
			data: params,
			success: function(res) {
				if (options.showBlockLoader && params.blockid) me.hideLoader(params.blockid);
				if (res) {
					if (options.rawData) {
						if (typeof success == 'function') success(res);
						return;
					}
					try {
						var data = JSON.parse(res);
						if (data.success) {
							if (typeof success == 'function') success(data.result);
						} else {
							console.log('Error in retrieving data from server: '+data.error);
							if (typeof failure == 'function') failure();
						}
					} catch(e) {
						console.log(e);
						console.log('Invalid data returned from server: '+res);
						if (typeof failure == 'function') failure();
					}
				} else {
					console.log('Invalid data returned from server: '+res);
					if (typeof failure == 'function') failure();
				}
			},
			error: function() {
				if (options.showBlockLoader && params.blockid) me.hideLoader(params.blockid);
				console.log('Ajax error');
				if (typeof failure == 'function') failure();
			}
		});
	},
	
	
	loadBlock: function(modhomeid, blockid, callback) {
		var me = this;
		
		var params = {
			modhomeid: modhomeid,
			blockid: blockid,
		};
		
		me.ajaxRequest('loadblock', params, null, function(result) {
			me.processAjaxBlock(modhomeid, blockid, result);
		}, function() {
			console.log('Error loading block #'+blockid);
		});

	},
	
	loadBlocks: function(modhomeid, blockids, callback) {
		var me = this;
		
		var blocklist = blockids.join(':');
		var params = {
			modhomeid: modhomeid,
			blockids: blocklist,
		};
		
		me.ajaxRequest('loadblocks', params, null, function(result) {
			for (blockid in result) {
				if (result.hasOwnProperty(blockid)) {
					me.processAjaxBlock(modhomeid, blockid, result[blockid]);
				}
			}
		}, function() {
			console.log('Error loading blocks');
		});
		
	},
	
	processAjaxBlock: function(modhomeid, blockid, content) {
		var me = this;
		
		jQuery('#blockcont_'+blockid).html(content);
	},
	
	removeBlock: function(modhomeid, blockid) {
		var me = this;
		var module = gVTModule;
		
		vteconfirm(alert_arr.ARE_YOU_SURE, function(yes) {
			if (yes) {
				me.ajaxRequest('removeblock', {modhomeid: modhomeid, blockid: blockid}, null, function(result) {
					jQuery('#modblock_'+blockid).remove();
				});
			}
		});
		
	},
	
	chooseNewBlock: function(modhomeid) {
		jQuery('#newblock_config_div').html('');
		jQuery('#newblock_select').val('');
		jQuery('#newblock_modhomeid').val(modhomeid);
		showFloatingDiv('ChooseNewBlock', null, {modal:true});
	},
	
	// crmv@102379
	addBlock: function(modhomeid, type) {
		var me = this;
		var params = {
			modhomeid: modhomeid,
			type: type,
		}
		
		if (type == 'Chart') {
			params.chartid = jQuery('#select_chart').val();
		}else if (type == 'QuickFilter') {
			params.cvid = jQuery('#select_qfilter').val();
		}else if (type == 'Filter') {
			params.cvid = jQuery('#select_filter').val();
		}else if (type == 'Processes') { // crmv@96233
			// not implemtented
		}else if (type == 'Wizards') {
			var wizids = jQuery('#select_wizards').val();
			if (!wizids || wizids.length == 0) return;
			params.wizards = JSON.stringify(wizids);
		} else {
			vtealert('Non implementato');
			return;
		}
		
		me.showLoader();
		me.ajaxRequest('addblock', params, null, function() {
			hideFloatingDiv('ChooseNewBlock');
			window.location.reload();
		});
	},
	// crmv@102379e
	
	loadNewBlockConfig: function() {
		var me = this,
			modhomeid = jQuery('#newblock_modhomeid').val(),
			type = jQuery('#newblock_select').val();
		
		if (!type) {
			jQuery('#newblock_config_div').html('');
			return;
		}
		
		jQuery('#newblock_config_div').html('');
		me.ajaxRequest('confignewblock', {type: type, modhomeid: modhomeid}, {rawData: true}, function(data) {
			jQuery('#newblock_config_div').html(data);
		});
	
	},
	
	positionBlock: function(blockid, type, size) {
		var me = this,
			layout = parseInt(jQuery('#blockcolumns').val()) || 4,
			spacing = 9,
			scale = 1,
			widgetWidth;

		me.blocksCache[blockid] = [blockid, type, size];

		var columns = Math.max(2, Math.min(layout, 4));
		size = Math.max(1, Math.min(size || 1, columns));

		switch(layout){
			case 2:
				widgetWidth = 49;
				break;
			case 3:
				widgetWidth = 32;
				break;
			case 4:
			default:
				widgetWidth = 24;
				break;
		}

		var mainX = parseInt(jQuery("#MainMatrix").width()); // crmv@97209
		var dx = ((mainX * widgetWidth * size) / 100 + (size-1) * spacing) * scale;

		jQuery('#modblock_'+blockid).width(dx);
	},
	
	clickRecord: function(slvid, module, crmid, entityname) {
		var url = 'index.php?module='+module+'&action=DetailView&record='+crmid;
		window.open(url, '_blank');
	}
	
}
