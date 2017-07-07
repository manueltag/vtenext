/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if (typeof(MyNotesSV) == 'undefined') {

	MyNotesSV = {
		
		select: function(listid,module,crmid,entityname) {
	
			jQuery('#ListViewContents [id^="row_"]').removeClass('lvtColDataHoverMessage');
			jQuery('#row_'+crmid).addClass('lvtColDataHoverMessage');
			
			MyNotesSV.detailView(module,crmid);
		},
		detailView: function(module,crmid) {
	
			jQuery('.editviewbutton').hide();
			VtigerJS_DialogBox.progress('DetailViewContents','light');
			
			jQuery.ajax({
				url: 'index.php?module='+module+'&action=DetailView&mode=SimpleView&record='+crmid,
				success: function(data){
					jQuery('#DetailViewContents').html(data);
					
					// crmv@104853
					var bodyH = parseInt(jQuery('body').height());
					var vteMenuH = parseInt(jQuery('#vte_menu').outerHeight(true));
					var buttonsListH = parseInt(jQuery('#Buttons_List').outerHeight(true));
					
					//crmv@54072	crmv@55694
					var height = bodyH-vteMenuH-buttonsListH-220;
					
					var description = jQuery('form[name="DetailView"] [name="description"]');
					description.parent().parent().height(height);
					description.parent().parent().css('overflow-y','auto');
					description.parent().parent().width('100%');
					//crmv@54072e	crmv@55694e
					// crmv@104853e
					
					VtigerJS_DialogBox.hideprogress('DetailViewContents');
					jQuery('.detailviewbutton').show();
				}
			});
		},
		create: function(module) {
		
			jQuery('#ListViewContents [id^="row_"]').removeClass('lvtColDataHoverMessage');
		
			jQuery('.detailviewbutton').hide();
			VtigerJS_DialogBox.progress();
			
			jQuery.ajax({
				url: 'index.php?module='+module+'&action='+module+'Ajax&file=EditView&hide_button_list=1',
				success: function(data){
					jQuery('#DetailViewContents').html(data);
					
					jQuery('form[name="EditView"]').attr('onsubmit','');
					jQuery('form[name="EditView"]').submit(function() {
						jQuery('#saveNoteButton').click();
						return false;
					});
					
					// crmv@104853
					var bodyH = parseInt(jQuery('body').height());
					var vteMenuH = parseInt(jQuery('#vte_menu').outerHeight(true));
					var buttonsListH = parseInt(jQuery('#Buttons_List').outerHeight(true));
					var emptyHeight = bodyH-vteMenuH-buttonsListH-220;
					// crmv@104853e
					
					jQuery('form[name="EditView"] [name="description"]').height(emptyHeight);
					jQuery('form[name="EditView"] [name="description"]').focus();
					
					VtigerJS_DialogBox.hideprogress();
					jQuery('.editviewbutton').show();
				}
			});
		},
		// crmv@97430
		getListId: function() {
			var list = jQuery('#ListViewContents').find('div[id^=SLVContainer_]');
			if (list.length > 0) {
				return parseInt(list.get(0).id.replace('SLVContainer_', ''));
			}
			return null;
		},
		save: function(module) {
			var me = this;
			var form = document.forms['EditView'];
			form.action.value='Save';
			
			var valid = formValidate(form);
			if (!valid) return;
			
			VtigerJS_DialogBox.progress();
			
			jQuery.ajax({
				url: jQuery(form).attr('action'),
				data: jQuery(form).serialize()+'&mode=SimpleView',
				dataType: 'json',
				type: 'POST',
				success: function(data, status, xhr) {
					VtigerJS_DialogBox.hideprogress();
					if (data['success'] != 'true') {
						alert(alert_arr.ERROR);
					} else {
						crmid = data['record'];
						MyNotesSV.detailView(module,crmid);
						
						var listid = me.getListId();
						if (listid > 0) {
							SLV.clear_search(listid);
							SLV.search(listid);
						}
					}
				},
				error: function() {
					VtigerJS_DialogBox.hideprogress();
					alert(alert_arr.ERROR);
				}
			});
		},
		delete: function(module,formname,action,confirmationMsg) {
			var me = this;
			
			if (confirm(confirmationMsg)) {
				
				var form = document.forms[formname];
				form.action.value=action;
			
				VtigerJS_DialogBox.progress();
				
				jQuery.ajax({
					url: jQuery(form).attr('action'),
					data: jQuery(form).serialize()+'&mode=SimpleView',
					type: 'POST',
					complete: function() {
						VtigerJS_DialogBox.hideprogress();
					},
					success: function(data, status, xhr) {
						jQuery('#DetailViewContents').html('');
						jQuery('.editviewbutton').hide();
						jQuery('.detailviewbutton').hide();
						
						var listid = me.getListId();
						if (listid > 0) {
							SLV.clear_search(listid);
							SLV.search(listid);
						}
					},
					error: function() {
						alert(alert_arr.ERROR);
					}
				});
			}
			return false;
		}
		// crmv@97430e
	};
}

if (typeof(MyNotesDVW) == 'undefined') {

	MyNotesDVW = {
	
		load: function(id,parent) {
			$("vtbusy_info").style.display="block";
			//crmv@115268
			if (jQuery('#mynotes_mode').length > 0) var mynotes_mode = jQuery('#mynotes_mode').val(); else var mynotes_mode = 'DetailViewMyNotesWidget';
			window.parent.document.getElementById('frameDetailViewMyNotesWidget').src = 'index.php?module=MyNotes&action=DetailView&mode='+mynotes_mode+'&record='+id+'&parent='+parent;
			//crmv@115268e
		},
		create: function(parent) {
			$("vtbusy_info").style.display="block";
			window.parent.document.getElementById('frameDetailViewMyNotesWidget').src = 'index.php?module=MyNotes&action=MyNotesAjax&file=widgets/create&parent='+parent;
		},
		save: function(parent) {
	
			var form = document.forms['EditView'];
			form.action.value='Save';
			
			var valid = formValidate(form);
			if (!valid) return;
			
			$("vtbusy_info").style.display="block";
			
			jQuery.ajax({
				url: jQuery(form).attr('action'),
				data: jQuery(form).serialize()+'&mode=DetailViewMyNotesWidget&parent='+parent,
				dataType: 'json',
				type: 'POST',
				success: function(data, status, xhr) {
					if (data['success'] != 'true') {
						$("vtbusy_info").style.display="none";
						alert(alert_arr.ERROR);
					} else {
						MyNotesDVW.load(data['record'],data['parent']);
					}
				},
				error: function() {
					$("vtbusy_info").style.display="none";
					alert(alert_arr.ERROR);
				}
			});
		},
		delete: function(id,parent,confirmationMsg) {
			if (confirm(confirmationMsg)) {
				$("vtbusy_info").style.display="block";
				jQuery.ajax({
					url: 'index.php?module=MyNotes&action=MyNotesAjax&action=Delete&record='+id+'&parent='+parent+'&mode=DetailViewMyNotesWidget',
					dataType: 'json',
					complete: function() {
						$("vtbusy_info").style.display="none";
					},
					success: function(data, status, xhr) {
						if (data['success'] == 'true') {
							if (data['record'] == '' || data['record'] == null)
								MyNotesDVW.create(data['parent']);
							else
								MyNotesDVW.load(data['record'],data['parent']);
						}
					},
					error: function() {
						alert(alert_arr.ERROR);
					}
				});
			}
			return false;
		}
	};
}