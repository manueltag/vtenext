/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@43942 crmv@54707 */

function cancelAreaSearchText(deftext) {
	jQuery('#basic_search_text').val('');
	if (basic_search_submitted) {
		jQuery('#basic_search_query').val(false);
	}
	restoreDefaultText(document.getElementById('basic_search_text'), deftext);
	basic_search_submitted = false;
	jQuery('#basic_search_query').val(true);
}

function goToListView(url,search) {
	if (search && basic_search_submitted) {
		url += '&viewmode=ListView&query=true&searchtype=BasicSearch&search_text='+encodeURIComponent(jQuery('#basic_search_text').val());	//crmv@107077
	} 
	location.href=url;
}

if (typeof(ModuleAreaManager) == 'undefined') {
	ModuleAreaManager = {
		postbody : {
			'module' : 'Area',
			'action' : 'AreaAjax',
			'file' : 'AreaManagerAjax'
		},
		showSettings : function(areaid) {
			var params = {
				'file' : 'SettingsAreas',
				'show_module' : jQuery('#basic_search_area').val()
			};
			LPOP.openPopup('','','',params);
		},
		moveUp : function(el) {
			var elem = document.getElementById(el);
			if(elem.options.selectedIndex>=0){
				for (var i=1;i<elem.options.length;i++){
					if(elem.options[i].selected == true){
						//swap with one up
						var first = elem.options[i-1];
						var second = elem.options[i];
						var temp = new Array();
						
						if (first.disabled) return false;
						
						temp.value = first.value;
						temp.innerHTML = first.innerHTML;
						
						first.value = second.value;
						first.innerHTML = second.innerHTML;
						
						second.value = temp.value;
						second.innerHTML = temp.innerHTML;
						
						first.selected = true;
						second.selected = false;
					}
				}
			}
		},
		moveDown : function(el) {
			var elem = document.getElementById(el);
			if(elem.options.selectedIndex>=0){
				for (var i=elem.options.length-2;i>=0;i--){
					if(elem.options[i].selected == true){
						//swap with one down
						var first = elem.options[i+1];
						var second = elem.options[i];
						var temp = new Array();
						
						temp.value = first.value;
						temp.innerHTML = first.innerHTML;
						
						first.value = second.value;
						first.innerHTML = second.innerHTML;
						
						second.value = temp.value;
						second.innerHTML = temp.innerHTML;
						
						first.selected = true;
						second.selected = false;
					}
				}
			}
		},
		propagateLayout : function() {
			var extraParams = {
				'function' : 'propagateLayout',
			};
			this.ajaxCall(extraParams, function(){
				LPOP.clickLinkModule('', 'AreaTools');
			});
		},
		blockLayout : function(value) {
			if (value) value = 1; else value = 0;
			var extraParams = {
				'function' : 'blockLayout',
				'value' : value,
			};
			this.ajaxCall(extraParams, function(){
				LPOP.clickLinkModule('', 'AreaTools');
			});
		},
		ajaxCall : function(extraParams, callBackFunction) {
			$("status").style.display = "display";
			jQuery.ajax({
				'url': 'index.php?' + jQuery.param(this.postbody),
				'type': 'POST',
				'data': jQuery.param(extraParams),
				success: function(data) {
					$("status").style.display = "none";
					if (callBackFunction) callBackFunction();
				}
			})
		},
	}
}
if (typeof(UnifiedSearchAreasObj) == 'undefined') {
	UnifiedSearchAreasObj = {
		id : 'UnifiedSearchAreas',
		type : '',
		show : function(currObj,type,openOffset,closeOffset) {
			openOffset = openOffset || 8;
			closeOffset = closeOffset || 0;
			UnifiedSearchAreasObj.type = type;
			//crmv@124737
			if (UnifiedSearchAreasObj.type == 'search') {
				jQuery('#UnifiedSearchAreasUnifiedRow').show();
				jQuery('#UnifiedSearchAreasUnifiedRowInput').show();
				if (jQuery('#UnifiedSearchAreasUnifiedRow1_Cont').length > 0) {
					jQuery('#UnifiedSearchAreasUnifiedRow1 td').first().html(jQuery('#UnifiedSearchAreasUnifiedRow1_Cont').html());
					jQuery('#UnifiedSearchAreasUnifiedRow1_Cont').remove();
					jQuery('#UnifiedSearchAreasUnifiedRow1').show();
				}
				var value = jQuery('#unifiedsearchnew_query_string').val();
				jQuery(currObj).html(jQuery('#UnifiedSearchAreas').html());
				jQuery('#UnifiedSearchAreas').remove();
				// crmv@119414
				setTimeout(function() {
					jQuery('#unifiedsearchnew_query_string').val(value)
					jQuery('#unifiedsearchnew_query_string').focus();
					jQuery('#unified_search_icn_canc').show(); 
				}, 100);
				// crmv@119414e
			} else {
				var olayernode = VtigerJS_DialogBox._olayer(true);
				olayernode.style.opacity = '0';
				
				jQuery('#UnifiedSearchAreasUnifiedRow').hide();
				jQuery('#UnifiedSearchAreasUnifiedRow1').hide(); // crmv@119414
				if (get_cookie('crmvWinMaxStatus') == 'open') {
					fnDropDown(currObj,UnifiedSearchAreasObj.id,openOffset);
				} else {
					fnDropDown(currObj,UnifiedSearchAreasObj.id,closeOffset);
				}
				jQuery('#'+UnifiedSearchAreasObj.id).appendTo(document.body);
			}
			/*
			if (UnifiedSearchAreasObj.type == 'search') {
				jQuery('#'+UnifiedSearchAreasObj.id).css('left','');
				jQuery('#'+UnifiedSearchAreasObj.id).css('right','0px');
			}*/
			document.getElementById(UnifiedSearchAreasObj.id).style.zIndex = findZMax()+1;
			//crmv@124737e
			jQuery('#__vtigerjs_dialogbox_olayer__').click(function(){
				UnifiedSearchAreasObj.hide();
			});
		},
		hide : function () {
			fnHideDrop(UnifiedSearchAreasObj.id);
			VtigerJS_DialogBox.unblock();
			jQuery('#__vtigerjs_dialogbox_olayer__').remove();
		},
		//crmv@107077
		openModule : function(index_url,list_url) {
			if (UnifiedSearchAreasObj.type == 'search') {
				UnifiedSearchAreasObj.searchInModule(list_url);
			} else {
				location.href = index_url;
			}
		},
		//crmv@107077e
		openArea : function(url) {
			if (UnifiedSearchAreasObj.type == 'search') {
				UnifiedSearchAreasObj.searchInArea(url);
			} else {
				location.href = url;
			}
		},
		searchInModule : function(url) {
			var search = jQuery('#unifiedsearchnew_query_string').val();
			if (typeof(search) == 'undefined' || search == undefined || search == '') {
				return false;
			}
			url += '&viewmode=ListView&query=true&searchtype=BasicSearch&search_text='+encodeURIComponent(search);	//crmv@107077
			window.open(url,'_blank');
		},
		searchInArea : function(url) {
			var search = jQuery('#unifiedsearchnew_query_string').val();
			if (typeof(search) == 'undefined' || search == undefined || search == '') {
				return false;
			}
			url += '&query=true&search_text='+encodeURIComponent(search);
			window.open(url,'_blank');
		}
	}
}
