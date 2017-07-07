/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if (typeof(ModNotificationsCommon) == 'undefined') {

	ModNotificationsCommon = {

		divId : 'ModNotifications',
		default_number_of_news : 20,

		follow : function(record) {
			$("vtbusy_info").style.display="inline";
			jQuery.ajax({
				url: 'index.php?module=ModNotifications&action=ModNotificationsAjax&file=SetFollowFlag&record='+record,
				success: function(data){
					if (data.indexOf(':#:SUCCESS') > -1) {
						var response = data.split(':#:SUCCESS');
						response = response[1];
						if (response != '') {
							jQuery('#followImg').text(response);
						}
					}
					$("vtbusy_info").style.display="none";
				}
			});
		},
		displayDetailNotificationModuleSettings : function(record) {
			if($('notification_module_settings').style.display != 'none') {
				Effect.Fade('notification_module_settings');
			} else {
				Effect.Appear('notification_module_settings');
			}
		},
		getLastNotifications : function(obj) {
			showFloatingDiv(ModNotificationsCommon.divId, obj);
			ModNotificationsCommon.loadModNotifications(ModNotificationsCommon.default_number_of_news);
		},
		loadModNotifications : function(num,target,indicator) {
			if (target == undefined || target == '') {
				target = ModNotificationsCommon.divId+'_div';
			}
			if (indicator == undefined || indicator == '') {
				indicator = 'indicator'+ModNotificationsCommon.divId;
			}
			ModNotificationsCommon.reloadContentWithFiltering('DetailViewBlockCommentWidget', '', num, target, indicator);
		},
		reloadContentWithFiltering : function(widget, parentid, criteria, targetdomid, indicator) {
			if($(indicator)) $(indicator).show();

			var url = 'module=ModNotifications&action=ModNotificationsAjax&file=ModNotificationsWidgetHandler&ajax=true';
			url += '&widget=' + encodeURIComponent(widget) + '&parentid='+encodeURIComponent(parentid);
			url += '&criteria='+ encodeURIComponent(criteria);

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if($(indicator)) $(indicator).hide();
					if($(targetdomid)) {
						$(targetdomid).innerHTML = data;
						if($(targetdomid).style.display!="block")
							$(targetdomid).show();
					}
					//crmv@30850 crmv@43194 crmv@59626
					jQuery('#'+targetdomid).on('click', '.ModCommUnseen', function(){ // crmv@82419
						var container = jQuery(this).closest('table[id^=tbl]'),
							id = container.find('.dataId').html(),
							imgSeen = container.find('.seenIcon'),
							imgUnseen = container.find('.unseenIcon');
						NotificationsCommon.removeChange('ModNotifications',id);
						container.find('.ModCommUnseen').removeClass('ModCommUnseen');
						imgUnseen.hide();
						imgSeen.show();
					});
					//crmv@30850e crmv@43194e crmv@59626e
				}
			});
		},
		// crmv@43194
		acceptInvitation: function(record, user, answer) {
			if (answer === undefined || answer === null || answer === '') answer = true;
			savePartecipation(record,user,(answer ? 2 : 1));
		},
		declineInvitation: function(record, user) {
			return this.acceptInvitation(record, user, false);
		},
		markAllAsRead: function() {
			NotificationsCommon.removeChange('ModNotifications','all');
			ModNotificationsCommon.loadModNotifications(jQuery('#ModNotificationsDetailViewBlockCommentWidget_max_number_of_news').val());
		},
		markAsRead: function(notificationid, domid, seen) {
			return this.markAsUnread(notificationid, domid, 1);
		},
		markAsUnread: function(notificationid, domid, seen) {
			var rowContainer = jQuery('#tbl'+domid+'_'+notificationid);

			if (seen === undefined || seen === null || seen === '') seen = 0;

			jQuery('#indicatorModNotifications').show();
			jQuery.ajax({
				url: 'index.php?module=ModNotifications&action=ModNotificationsAjax&file=DetailViewAjax&ajxaction=GETNOTIFICATION&seen='+seen+'&record='+notificationid,
				success: function(data){
					if (data.indexOf(':#:SUCCESS') > -1) {
						jQuery('#indicatorModNotifications').hide();
						var response = data.split(':#:SUCCESS'),
							counter = response[0];
						response = response[1];
						if (response != '') {
							rowContainer.html(response);
							NotificationsCommon.drawChangesAndStorage('ModNotificationsCheckChangesDiv', 'ModNotificationsCheckChangesImg', counter, 'ModNotifications');	//crmv@OPER5904
						}
					}
				}
			});

		},
		// crmv@43194e
		followCV : function() {
			var record = jQuery('#viewname').val();
			$("status").style.display="inline";
			jQuery.ajax({
				url: 'index.php?module=ModNotifications&action=ModNotificationsAjax&file=SetFollowFlag&type=customview&record='+record,
				success: function(data){
					if (data.indexOf(':#:SUCCESS') > -1) {
						var response = data.split(':#:SUCCESS');
						response = response[1];
						if (response != '') {
							jQuery('#followImgCV').text(response);
						}
					}
					$("status").style.display="none";
				}
			});
		},
		setFollowImgCV : function(record) {
			$("status").style.display="block";
			jQuery.ajax({
				url: 'index.php?module=ModNotifications&action=ModNotificationsAjax&file=SetFollowFlag&type=customview&record='+record+'&mode=get_image',
				success: function(data){
					if (data.indexOf(':#:SUCCESS') > -1) {
						var response = data.split(':#:SUCCESS');
						response = response[1];
						if (response != '') {
							jQuery('#followImgCV').text(response);
						}
					}
					$("status").style.display="none";
				}
			});
		},
		toggleChangeLog : function (id) {
			var div = 'div_'+id;
			var img = '#img_'+id;

			if(getObj(div).style.display != "block"){
				getObj(div).style.display = "block";
		        jQuery(img).html('keyboard_arrow_down');	//crmv@104566
			}else{
				getObj(div).style.display = "none";
		        jQuery(img).html('keyboard_arrow_right');	//crmv@104566
			}
		}
	}
}