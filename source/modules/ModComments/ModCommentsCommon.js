/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/
if (typeof(ModCommentsCommon) == 'undefined') {
	ModCommentsCommon = {

		default_number_of_news : 40,
		current_page: 1,	// crmv@80503

		// crmv@43050
		checkComment : function(mode, domkeyid, visibility) {
			var textBoxField = $('txtbox_'+domkeyid);
			if (mode == 'new' || mode == 'addUsers') {
				if (mode == 'new' && (textBoxField.value == '' || textBoxField.value == default_text)) {
					return false;
				}
				// crmv@43448 - removed debug
				if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
					visibility = jQuery('input[name="ModCommentsMethod"]:checked').val() || visibility; // crmv@43448
				}

				if (visibility != 'All' && visibility != 'Users') {
					alert('Scegli a chi scrivere');
					return false;
				} else if (visibility == 'Users') {
					var tmp = getObj('ModCommentsUsers_idlist').value;
					tmp = tmp.replace(/\|/g,"");

					if (tmp == '') {
						alert(alert_arr.SELECT_ATLEAST_ONE_USER);
						return false;
					}
				}
			} else if (mode == 'reply') {
				if (textBoxField.value == '' || textBoxField.value == default_reply_text) {
					return false;
				}
			} else if (mode == 'composeEmail') {
				if (textBoxField.value != '' && textBoxField.value != default_text) {
					if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
						visibility = jQuery('input[name="ModCommentsMethod"]:checked').val();
					}
					if (visibility != 'All' && visibility != 'Users') {
						alert('Scegli a chi scrivere');
						return false;
					} else if (visibility == 'Users') {
						var tmp = getObj('ModCommentsUsers_idlist').value;
						tmp = tmp.replace(/\|/g,"");
						if (tmp == '') {
							alert(alert_arr.SELECT_ATLEAST_ONE_USER);
							return false;
						}
					}
				}
			}
			return true;
		},
		// crmv@43050e
		addComment : function(domkeyid, parentid, visibility, indicator) {
			//crmv@91082
			if(!SessionValidator.check()) {
				SessionValidator.showLogin();
				return false;
			}
			//crmv@91082e
			
			// crmv@43050 - added add users
			var commentid = jQuery('#ModComments_addCommentId').val();
			if (commentid) return ModCommentsCommon.addUsers(domkeyid, indicator, commentid);
			// crmv@43050e

			if (ModCommentsCommon.checkComment('new',domkeyid, visibility) == false) {
				return;
			}
			var textBoxField = $('txtbox_'+domkeyid);
			var contentWrapDOM = $('contentwrap_'+domkeyid);

			// crmv@43448 try to retrieve parent id from the popup
			if (!parentid) {
				parentid = jQuery('#ModCommentsParentId').val();
			}
			// crmv@43448e

			var url = 'module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETADDCOMMENT&parentid='+encodeURIComponent(parentid);
			url += '&comment=' + encodeURIComponent(textBoxField.value);
			url += '&visibility='+visibility;
			if (visibility == 'Users') {
				url += '&users_comm='+encodeURIComponent(getObj('ModCommentsUsers_idlist').value);
			}

			VtigerJS_DialogBox.block();
			if($(indicator)) $(indicator).show();

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if($(indicator)) $(indicator).hide();
					VtigerJS_DialogBox.unblock();

					var responseTextTrimmed = trim(data);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						contentWrapDOM.innerHTML = responseTextTrimmed.substring(10) + contentWrapDOM.innerHTML;
						textBoxField.className = 'detailedViewTextBox detailedViewModCommTextBox';
						textBoxField.value = default_text;
						$('saveButtonRow_'+domkeyid).hide();
						if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
							jQuery('#saveOptionsRow_'+domkeyid).hide();
							jQuery('input[name="ModCommentsMethod"]').removeAttr("checked");
						}
						if (visibility == 'Users') {
							if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
								$('ModCommentsUsers_'+domkeyid).hide();
							} else {
								$('ModCommentsUsers').hide();
							}
							removeAllUsers();
						}
						// crmv@43448
						var container = jQuery('#editareaModComm');
						container.find('.commentAddLink').show();
						container.find('#ModCommentsParentId').val('');
						container.find('#ModCommentsNewRelatedLabel').hide();
						container.find('#ModCommentsNewRelatedName').html('').hide();
						// crmv@43448e
					} else {
						alert(top.alert_arr.OPERATION_DENIED);
					}
				}
			});
		},
		// crmv@43050
		addUsers: function(domkeyid, indicator, commentid) {
			if (ModCommentsCommon.checkComment('addUsers', domkeyid, 'Users') == false) {
				return;
			}

			var contentWrapDOM = jQuery('#tblModCommentsDetailViewBlockCommentWidget_'+commentid),
				url = 'index.php?module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETADDUSERS',
				userids = jQuery('#ModCommentsUsers_idlist').val();

			if (!userids) return;

			url += '&commentid='+encodeURIComponent(commentid)+'&users_comm='+encodeURIComponent(userids);

			VtigerJS_DialogBox.block();
			if($(indicator)) $(indicator).show();

			jQuery.ajax({
				'url': url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if ($(indicator)) $(indicator).hide();
					VtigerJS_DialogBox.unblock();

					var responseTextTrimmed = trim(data);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						contentWrapDOM.replaceWith(responseTextTrimmed.substring(10));

						if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
							jQuery('input[name="ModCommentsMethod"]').removeAttr("checked");
							$('ModCommentsUsers_'+domkeyid).hide();
						} else {
							$('ModCommentsUsers').hide();
						}
						removeAllUsers();
						jQuery('#ModCommentsUsers2').hide(); // crmv@43448

					} else {
						alert(top.alert_arr.OPERATION_DENIED);
					}
				}
			});
		},
		// crmv@43448
		reloadComment: function(domkeyid, indicator, commentid, setunread) {
			var contentWrapDOM = jQuery('#tblModCommentsDetailViewBlockCommentWidget_'+commentid),
				url = 'index.php?module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETGETCOMMENT';

			url += '&commentid='+encodeURIComponent(commentid);
			if (setunread !== undefined && setunread !== null && setunread !== '') {
				url += '&setasunread='+setunread;
			}
			
			//crmv@59626
			if (jQuery('[name="record"]').val() == undefined) {
				url += '&criteria=News';
			}
			if (jQuery('#contentShowFull'+domkeyid+'_'+commentid).length > 0 && jQuery('#contentShowFull'+domkeyid+'_'+commentid).css('display') != 'none') {
				url += '&show_preview=yes';
			}
			//crmv@59626e
			
			VtigerJS_DialogBox.block();
			if ($(indicator)) $(indicator).show();

			jQuery.ajax({
				'url': url,
				type: 'POST',
				dataType: 'html',
				async: false,
				success: function(data){
					if ($(indicator)) $(indicator).hide();
					VtigerJS_DialogBox.unblock();

					var responseTextTrimmed = trim(data);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						contentWrapDOM.replaceWith(responseTextTrimmed.substring(10));

						if (jQuery('input[name="ModCommentsMethod"]').length > 0) {	// new mode
							jQuery('input[name="ModCommentsMethod"]').removeAttr("checked");
							$('ModCommentsUsers_'+domkeyid).hide();
						} else {
							$('ModCommentsUsers').hide();
						}

						if (top.NotificationsCommon) {
							top.NotificationsCommon.showChangesAndStorage('CheckChangesDiv', 'CheckChangesImg', 'ModComments');	//crmv@OPER5904
						}
					} else {
						alert(top.alert_arr.OPERATION_DENIED);
					}
				}
			});
		},
		// crmv@43050e
		setAsUnread: function(domkeyid, commentid, indicator) {
			return this.reloadComment(domkeyid, indicator, commentid, 1);
		},
		setAsRead: function(domkeyid, commentid, indicator) {
			return this.reloadComment(domkeyid, indicator, commentid, 0);
		},
		// crmv@43448e
		//crmv@59626 crmv@98825
		checkAndSetAsRead: function(obj, domkeyid, commentid, indicator) {
			if (jQuery(obj).hasClass('ModCommUnseen')) {	// only if I click in these divs
				return this.setAsRead(domkeyid, commentid, indicator);
			}
		},
		//crmv@59626e crmv@98825e
		reloadContentWithFiltering : function(widget, parentid, criteria, targetdomid, indicator, searchkey) { //crmv@31301
			if($(indicator)) $(indicator).show();

			var url = 'module=ModComments&action=ModCommentsAjax&file=ModCommentsWidgetHandler&ajax=true';
			url += '&widget=' + encodeURIComponent(widget) + '&parentid='+encodeURIComponent(parentid);
			url += '&criteria='+ encodeURIComponent(criteria);
			url += '&searchkey='+ encodeURIComponent(searchkey); //crmv@31301

			if (criteria.indexOf('News')>=0) {
				/* crmv@59626
				if (indicator.indexOf('refresh_')>=0) {
					$(indicator).innerHTML=$('vtbusy_homeinfo').innerHTML;
				}
				jQuery('#'+targetdomid).load(function(){
					NotificationsCommon.removeChanges('ModComments','News',targetdomid);
					if (indicator.indexOf('refresh_')>=0) {
						$(indicator).innerHTML='';
					}
				}); */
				url += '&target_frame='+targetdomid;
				url += '&indicator='+indicator;
				jQuery('#'+targetdomid).attr('src','index.php?'+url);
				return;
			}

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if($(indicator)) $(indicator).hide();
					//crmv@16903
					if($(targetdomid)) {
						$(targetdomid).innerHTML = data;
						if($(targetdomid).style.display!="block")
							showHideStatus('tblModCommentsDetailViewBlockCommentWidget','aidModCommentsDetailViewBlockCommentWidget','themes/softed/images/');
					}
					//crmv@16903e
					//NotificationsCommon.removeChanges('ModComments','DetailView');	//crmv@59626
				}
			});
		},
		//crmv@80503
		// this function must be called from inside the iframe
		appendContentWithFiltering : function(widget, parentid, criteria, targetdomid, indicator, searchkey) { //crmv@31301
			var me = this,
				page = parseInt(criteria.replace(/[^0-9]/g, ''));

			if (top.$ && top.$(indicator)) top.$(indicator).show();

			var url = 'module=ModComments&action=ModCommentsAjax&file=ModCommentsWidgetHandler&ajax=true';
			url += '&widget=' + encodeURIComponent(widget) + '&parentid='+encodeURIComponent(parentid);
			url += '&criteria='+ encodeURIComponent(criteria);
			url += '&searchkey='+ encodeURIComponent(searchkey); //crmv@31301
			
			var uikey = jQuery('#uikey').val();
			var cont = jQuery('#contentwrap_'+uikey);
			
			// get the last child
			if (cont.length > 0) {
				var lastchild = cont.find('input[id^=comment'+uikey+'_lastchild]:last');
				if (lastchild.length > 0) {
					url += "&lastchildid="+parseInt(lastchild.val());
				}
				var lastseen = cont.find('input[id^=comment'+uikey+'_seen]:last');
				if (lastseen.length > 0) {
					url += "&lastseen="+(lastseen.val() == 'true' ? '1' : '0');
				}
			}

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if (top.$ && top.$(indicator)) top.$(indicator).hide();

					if (cont.length > 0) {
						cont.append(data);
						me.current_page = page;
						// update the counter
						var total = parseInt(jQuery('#comments_counter_total_'+uikey).text());
						var newmax = page * me.default_number_of_news;
						jQuery('#comments_counter_to_'+uikey).text(Math.min(total, newmax));
						if (newmax >= total) {
							// hide the "load more" link
							jQuery('#comments_counter_link_'+uikey).hide();
						}
					}
				}
			});
		},
		//crmv@80503e
		addReply : function(domkeyid, parentid, parent_comment, indicator) {
			if (ModCommentsCommon.checkComment('reply',domkeyid) == false) {
				return;
			}
			
			//crmv@91082
			if(!SessionValidator.check()) {
				SessionValidator.showLogin();
				return false;
			}
			//crmv@91082e
			
			var textBoxField = $('txtbox_'+domkeyid);
			var contentWrapDOM = $('contentwrap_'+domkeyid);

			var url = 'module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETADDREPLY&parentid='+encodeURIComponent(parentid);
			url += '&comment=' + encodeURIComponent(textBoxField.value);
			url += '&parent_comment='+encodeURIComponent(parent_comment);

			VtigerJS_DialogBox.block();
			if($(indicator)) $(indicator).show();

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if($(indicator)) $(indicator).hide();
					VtigerJS_DialogBox.unblock();

					var responseTextTrimmed = trim(data);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						//crmv@59626
						/*
						contentWrapDOM.innerHTML += responseTextTrimmed.substring(10);
						textBoxField.className = 'detailedViewTextBox detailedViewModCommTextBox';
						textBoxField.value = default_reply_text;
						$('saveButtonRow_'+domkeyid).hide();
						*/
						ModCommentsCommon.setAsRead(domkeyid, parent_comment, indicator);
						//crmv@59626e
					} else {
						alert(top.alert_arr.OPERATION_DENIED);
					}
				}
			});
		},
		deleteComment : function(domkeyid, id, indicator) {
			var tblDOM = $('tbl'+domkeyid);

			var url = 'module=ModComments&action=ModCommentsAjax&file=DetailViewAjax&ajax=true&ajxaction=WIDGETDELETECOMMENT&id='+encodeURIComponent(id);

			VtigerJS_DialogBox.block();
			if($(indicator)) $(indicator).show();

			jQuery.ajax({
				url: 'index.php?'+url,
				type: 'POST',
				dataType: 'html',
				success: function(data){
					if($(indicator)) $(indicator).hide();
					VtigerJS_DialogBox.unblock();

					var responseTextTrimmed = trim(data);
					if (responseTextTrimmed.substring(0, 10) == ':#:SUCCESS') {
						tblDOM.remove();
					} else {
						alert(top.alert_arr.OPERATION_DENIED);
					}
				}
			});
		},
		//crmv@59626
		showFullContent : function(id, seen, domkeyid, commentid, indicator) {
			/*
			var replyid = id.split('_');
			if (replyid != commentid) {
				jQuery('#contentShowFull'+commentid).show();
			}
			*/
			if (seen == false) ModCommentsCommon.setAsRead(domkeyid, commentid, indicator);

			jQuery('#contentSmall'+id).hide();
			jQuery('#contentFull'+id).show();
			jQuery('#contentShowFull'+id).hide();
		}
		//crmv@59626e
	}
}

function onModCommTextBoxFocus(obj,domkeyid,mode) {
	var def_text = default_text;
	if (mode == 'reply')
		def_text = default_reply_text;

	if (jQuery('#'+obj).val() == def_text) {
		$(obj).className='detailedViewTextBoxOn detailedViewModCommTextBoxOn';
		jQuery('#'+obj).val('');
		jQuery('#saveButtonRow_'+domkeyid).show();
		if (mode != 'reply' && jQuery('#saveOptionsRow_'+domkeyid).length > 0) {
			jQuery('#saveOptionsRow_'+domkeyid).show();
		}
	}
}

function onModCommTextBoxBlur(obj,domkeyid,mode) {
	var def_text = default_text;
	if (mode == 'reply')
		def_text = default_reply_text;

	if (jQuery('#'+obj).val() == '') {
		$(obj).className='detailedViewTextBox detailedViewModCommTextBoxOn';
		jQuery('#'+obj).val(def_text);
	}
}

function showAllReplies(id) {
	jQuery('#contentwrap_'+id).find('.tbl_ModCommReplies').each(function(){
		this.show();
	});
	jQuery('#showAll'+id).hide();
}

function displayRecipientsInfo(obj,info) {
	var info = eval(decodeURIComponent(info));

	var olayer = document.getElementById('ModCommentsUsers_info');
	if(!olayer) {
		var olayer = document.createElement("div");
		olayer.id = "ModCommentsUsers_info";
		olayer.className = 'small';
		olayer.style.zIndex = findZMax()+1;
		olayer.style.padding = '4px';
		olayer.style.position = "absolute";
		document.body.appendChild(olayer);

		domnode = $('ModCommentsUsers_info');
		Event.observe(domnode, 'mouseover', function() { $('ModCommentsUsers_info').show(); });
		Event.observe(domnode, 'mouseout', function() { $('ModCommentsUsers_info').hide(); });
	} else {
		olayer.innerHTML = '';
	}
	fnvshobj(obj,'ModCommentsUsers_info');
	// crmv@43448 - fix positioning error
	var parentPos = jQuery(obj).offset();
	jQuery(olayer).css({
		'left': parentPos.left,
		'top': parentPos.top,
	});
	// crmv@43448e

	for (item=0; item<info.length; item++) {
		var tmp = info[item];
		var span = '<span id="ModCommentsUsers_info_'+tmp.value+'" class="addrBubble">'
					+'<table cellpadding="3" cellspacing="0" class="small">'
					+'<tr valign="top">'
					+	'<td><img src="'+tmp.img+'" class="userAvatar" /></td>'
					+	'<td>'+tmp.name+'</td>'
					+'</tr>'
					+'</table>'
					+'</span>';
		olayer.innerHTML = olayer.innerHTML+span;
	}
}

function getModCommentsNews(obj) {
	
	//crmv@91082
	if(!SessionValidator.check()) {
		SessionValidator.showLogin();
		return false;
	}
	//crmv@91082e
	
	showFloatingDiv('ModCommentsNews', null, {modal:false, center:true, removeOnMaskClick:false}); // crmv@103908
	
	// fix the positioning!
	var el = jQuery('#ModCommentsNews').get(0);
	if (el) placeAtCenter(el, true);
	
	loadModCommentsNews(ModCommentsCommon.default_number_of_news);
	jQuery('#modcomments_search_text').val('');
	jQuery('#modcomments_search_text').blur();
}

function loadModCommentsNews(num,target,indicator,searchkey) { //crmv@31301
	if (target == undefined || target == '') {
		target = 'ModCommentsNews_iframe';
	}
	if (indicator == undefined || indicator == '') {
		indicator = 'indicatorModCommentsNews';
	}
	//crmv@31301
	if (searchkey == undefined || searchkey == '') {
		searchkey = '';
	}
	//crmv@31301e
	ModCommentsCommon.reloadContentWithFiltering('DetailViewBlockCommentWidget', '', 'Last'+num+'News', target, indicator, searchkey); //crmv@31301
}

//crmv@80503
function loadModCommentsPage(num,target,indicator,searchkey) {
	if (target == undefined || target == '') {
		target = 'ModCommentsNews_iframe';
	}
	if (indicator == undefined || indicator == '') {
		indicator = 'indicatorModCommentsNews';
	}
	//crmv@31301
	if (searchkey == undefined || searchkey == '') {
		searchkey = '';
	}
	//crmv@31301e
	
	
	var cpage = ModCommentsCommon.current_page,
		rowsPerPage = ModCommentsCommon.default_number_of_news;
		//page = Math.ceil(num/rowsPerPage);
	
	ModCommentsCommon.appendContentWithFiltering('DetailViewBlockCommentWidget', '', 'Page'+(cpage+1)+'News', target, indicator, searchkey); //crmv@31301
}
//crmv@80503e

function clearTextModComments(elem, prefix) {
	var jelem = jQuery(elem);
	var rest = jQuery.data(elem, 'restored');
	if (rest == undefined || rest == true) {
		jelem.val('');
		jQuery('#'+prefix+'_icn_canc').show();
		jQuery.data(elem, 'restored', false);
		jQuery('#'+prefix+'_text').focus();
	}
}

function restoreDefaultTextModComments(elem, deftext, prefix) {
	var jelem = jQuery(elem);
	if (jelem.val() == '') {
		jelem.val(deftext);
		jQuery('#'+prefix+'_icn_canc').hide();
		jQuery.data(elem, 'restored', true);
	}
}

function cancelSearchTextModComments(deftext, prefix, target, indicator) {
	jQuery('#'+prefix+'_text').val('');
	jQuery('#'+prefix+'_icn_canc').hide();
	restoreDefaultTextModComments(document.getElementById(prefix+'_text'), deftext);
	loadModCommentsNews(eval(jQuery('#'+target).contents().find('#max_number_of_news').val()),target,indicator);
}

function launchModCommentsSearch(e,prefix) {
	if (e.keyCode == 13) {
        jQuery('#'+prefix+'_icn_go').click();
    }
}
