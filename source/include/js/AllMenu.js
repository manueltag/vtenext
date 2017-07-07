/* crmv@126984 */

var AllMenuObj = {
	
	menu_search_submitted : false,
	
	initialize: function() {
		jQuery('#menu_search_text').keyup(function() {
			AllMenuObj.searchInMenu();
		});
	},
	
	showAllMenu : function(currObj) {
		var olayernode = VtigerJS_DialogBox._olayer(true);
		olayernode.style.opacity = '0';

		fnDropDown(currObj,'OtherModuleList_sub');
		document.getElementById('OtherModuleList_sub').style.zIndex = findZMax()+1;
		//crmv@56399 - adjust div position, 2 px more left
		var actual_div_pos_tmp = jQuery('#OtherModuleList_sub').css('left').match(/(\d*\.?\d*)(.*)/);
		var actual_div_pos = parseFloat(actual_div_pos_tmp[1], 10) || 0;
		var new_div_pos = (actual_div_pos - 2) + actual_div_pos_tmp[2];
		jQuery('#OtherModuleList_sub').css('left',new_div_pos);
		//crmv@56399e
		jQuery('#OtherModuleList_sub').appendTo(document.body);
		
		AllMenuObj.clearMenuSearchText(document.getElementById('menu_search_text'));
		jQuery('#__vtigerjs_dialogbox_olayer__').click(function(){
			AllMenuObj.hideAllMenu();
		});
	},
	
	hideAllMenu : function () {
		fnHideDrop('OtherModuleList_sub');
		VtigerJS_DialogBox.unblock();
		jQuery('#__vtigerjs_dialogbox_olayer__').remove();
	},
	
	searchInMenu : function() {
		AllMenuObj.menu_search_submitted = true;
		jQuery('.highlighted').removeClass('highlighted');
		jQuery('.drop_down_hover').removeClass('drop_down_hover');
		var searchText = jQuery('#menu_search_text').val();
		if (searchText == '') {
			jQuery('#menu_search_icn_canc').hide();
		} else {
			jQuery('#menu_search_icn_canc').show();
		}
		if (searchText != '') {
			jQuery("#OtherModuleList_sub .menu_entry").each(function(i, ele){
				var content = jQuery(ele).text();
				var contentNew = content.replace( new RegExp(searchText, "gi"), "<span class='highlighted'>$&</span>" );
				if (contentNew != content) {
					jQuery(ele).html(contentNew);
				}
			});
			if (jQuery("#OtherModuleList_sub .highlighted").length == 1) {
				var el = jQuery("#OtherModuleList_sub .highlighted");
				el.parent().addClass('drop_down_hover');
				el.removeClass('highlighted');
				jQuery(document).keyup(function(e) {
				    if(e.which == 13) {
						if (jQuery("#OtherModuleList_sub").css('display') == 'block' && el.parent().attr('href') != undefined && el.parent().attr('href') != '') {
							location.href = el.parent().attr('href');
						}
				    }
				});
			}
		}
	},
	
	clearMenuSearchText : function(elem) {
		var jelem = jQuery(elem);
		jelem.focus();
		jelem.val('');
		AllMenuObj.restoreMenuSearchDefaultText(elem);
	},
	
	restoreMenuSearchDefaultText : function(elem) {
		var jelem = jQuery(elem);
		if (jelem.val() == '') {
			jQuery('#menu_search_icn_canc').hide();
			if (AllMenuObj.menu_search_submitted == true) {
				AllMenuObj.searchInMenu();
			} else {
				jelem.val('');
			}
			jelem.focus();
		}
	},
	
	cancelMenuSearchSearchText : function() {
		jQuery('#menu_search_text').val('');
		AllMenuObj.restoreMenuSearchDefaultText(document.getElementById('menu_search_text'));
	},
	
	toggleMenu(show_btn, show_div, hide_btn, hide_div) {
		var me = this;
		jQuery('#'+show_btn).parent().removeClass('dvtUnSelectedCell');
		jQuery('#'+show_btn).parent().addClass('dvtSelectedCell');
		jQuery('#'+show_div).show();
		jQuery('#'+hide_btn).parent().removeClass('dvtSelectedCell');
		jQuery('#'+hide_btn).parent().addClass('dvtUnSelectedCell');
		jQuery('#'+hide_div).hide();
		if (show_btn == 'allmenu_btn_modules') {
			jQuery('#menu_search_text').focus();
			me.setMenuView('modules');
		} else {
			me.setMenuView('areas');
		}
	},
	getMenuView() {
		var moduleMenuView = VTELocalStorage.getItem("moduleMenuView");
		if (moduleMenuView == null) moduleMenuView = 'modules';
		return moduleMenuView;
	},
	setMenuView(view) {
		VTELocalStorage.setItem("moduleMenuView",view);
	}
}