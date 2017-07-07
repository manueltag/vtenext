{*********************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************}
{* crmv@29190 crmv@82419 crmv@82831 *}
{assign var="AUTOCOMPLETE_MODULE" value=$MODULE_NAME}
{if $AUTOCOMPLETE_MODULE eq ''}
	{assign var="AUTOCOMPLETE_MODULE" value=$MODULE}
{/if}
<script type="text/javascript">
	var autocomplete_module = '{$AUTOCOMPLETE_MODULE}';	//crmv@108227
	var autocomplete_include_script;
	{literal}
	function reloadAutocomplete(id,display,popup_params,sdk_popup_hidden_elements) {
		initAutocomplete(id,display,encodeURIComponent(popup_params),sdk_popup_hidden_elements);
	}
	function initAutocomplete(id,display,params,sdk_popup_hidden_elements) {

		if (jQuery.type(id) == 'string') {
			var id_str = id;
			var id_obj = jQuery('#'+id);
		} else if (jQuery.type(id) == 'object') {
			var id_str = jQuery(id).attr('name');
			var id_obj = jQuery(id);
		}
		if (jQuery.type(display) == 'string') {
			var display_str = display;
			var display_obj = jQuery('#'+display);
		} else if (jQuery.type(display) == 'object') {
			var display_str = jQuery(display).attr('name');
			var display_obj = jQuery(display);
		}
		//crmv@92272 crmv@108227
		if (autocomplete_module == 'Calendar' && id_str == 'parent_id') var reference_field_type = 'parent_type'; else var reference_field_type = id_str+'_type';
		if (jQuery('#'+reference_field_type).val() == 'Other') {
			id_obj.parent('div').hide();
			jQuery('#div_other_'+id_str).show();
		} else {
			jQuery('#div_other_'+id_str).hide();
			id_obj.parent('div').show();
		}
		//crmv@92272e crmv@108227e
		
		var empty_str = '{/literal}{"LBL_SEARCH_STRING"|getTranslatedString}{literal}';
		
		display_obj
			.focus(function(){
				var term = this.value;
				if ( term.length == 0 || this.value == empty_str) {
					this.value = '';
				}
			})
			.blur(function(){
				var term = this.value;
				if ( term.length == 0 ) {
					this.value = empty_str;
				}
			})
			.autocomplete({
				source: function( request, response ) {
					// crmv@91082
					if (!SessionValidator.check()){
						SessionValidator.showLogin();
						return false;
					}
					// crmv@91082e
					jQuery.getJSON( "index.php?module=SDK&action=SDKAjax&file=src/Reference/Autocomplete", {
						term: request.term,
						field: id_str,
						params: params
					}, function(data) {
						var url = "index.php?"+decodeURIComponent(params);
						if (sdk_popup_hidden_elements != '') {
        					for (var label in sdk_popup_hidden_elements) {  
								url += "&"+label+"="+eval(sdk_popup_hidden_elements[label]);
							} 
						}
						jQuery.getJSON(url, {
							autocomplete: 'yes',
							autocomplete_select: data[0],
							autocomplete_where: data[1] //crmv@42329
						}, response );
					});
				},
				open: function() {
					if (typeof window.findZMax == 'function') {
						var zmax = findZMax();
						jQuery(this).autocomplete('widget').css('z-index', zmax+2);
					}
					return false;
				},
				search: function() {
					// custom minLength
					var term = this.value;
					if ( term.length < 3 ) {
						return false;
					}
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui, ret_funct ) {
					if (ui.item.return_function_file != '') {
						autocomplete_include_script = 'yes';
						jQuery.getScript(ui.item.return_function_file, function(data){
							eval(data);
							eval(ui.item.return_function);
							jQuery.getScript('modules/{/literal}{$AUTOCOMPLETE_MODULE}/{$AUTOCOMPLETE_MODULE}{literal}.js', function(data){eval(data);});
							autocomplete_include_script = 'no';
						});
					}
					return false;
				}
			}
		);
	}
	//crmv@31171
	function initAutocompleteUG(type,id,display,values,label,form) {
		var empty_str = '{/literal}{"LBL_SEARCH_STRING"|getTranslatedString}{literal}';
		var values = eval("("+values+")");
		var source = new Array();
		var curr_form = form;
		//crmv@36944
		if (values == null){
			return;
		}
		//crmv@36944 e		
		jQuery.each(values, function(index,obj) {
			jQuery.each(obj, function(user) {
				var tmp = {'id':index,'label':user};
				source.push(tmp) 
			});
		});
		source.sort(function(a,b){
			if (a.label < b.label){
				return -1;
			}
			if (a.label > b.label){
				return 1;
			}
			return 0;
		});
		jQuery('#'+display)
			.focus(function(){
				var term = this.value;
				if ( term.length == 0 || this.value == empty_str) {
					this.value = '';
				}
			})
			.blur(function(){
				var term = this.value;
				if ( term.length == 0 ) {
					this.value = empty_str;
				}
			})
			.autocomplete({
				minLength: 0,
				source: source,
				// crmv@105046
				open: function() {
					if (typeof window.findZMax == 'function') {
						var zmax = findZMax();
						jQuery(this).autocomplete('widget').css('z-index', zmax+2);
					}
					return false;
				},
				// crmv@105046e
				select: function( event, ui ) {
					if (curr_form != undefined) {
						var form = curr_form;
					} else {
						var formName = getReturnFormName();
						var form = getReturnForm(formName);
					}
					form.elements[id].value = ui.item.id;
					form.elements[display].value = ui.item.label;
					if (label != undefined) {
						form.elements['hdtxt_'+label].value = ui.item.label;
					}
					//crmv@34104
					var mass_edit_check = id+'_mass_edit_check';
					if (id == 'assigned_group_id') {
						mass_edit_check = 'assigned_user_id_mass_edit_check';
					}
					//crmv@34104e
					disableReferenceField(form.elements[display],form.elements[id],form.elements[mass_edit_check]);
					return false;
				}
			}
		);
	}
	function toggleAutocompleteList(display) {
		if ( jQuery("#"+display).autocomplete( "widget" ).is( ":visible" ) ) {
			jQuery("#"+display).autocomplete( "close" );
			return;
		}
		//jQuery( this ).blur();	//crmv@44794
		jQuery("#"+display).autocomplete("search","");
	}
	function closeAutocompleteList(display) {
		jQuery("#"+display).autocomplete( "close" );
	}
	//crmv@31171e
	function enableReferenceField(field) {
		//crmv@34627
		if (field.name == 'report_display') {
			var module = document.forms['EditView'].module.value;
			if (module != undefined && module != 'undefined' && module == 'CustomView') {
				reloadColumns(document.forms['EditView'].cvmodule.value,document.forms['EditView'].report.value);
			}
		}
		//crmv@34627e
		field.readOnly = false;
		if (jQuery(field).parent('div').length > 0) {
			var div = jQuery(field).parent('div');
			div.attr('class','dvtCellInfoOn');
			div.focusin(function(){
				div.attr('class','dvtCellInfoOn');
			}).focusout(function(){
				div.attr('class','dvtCellInfo');
			});
		}
		jQuery(field).focus();
	}
	function disableReferenceField(field,realfield,checkbox) {	//crmv@32341
		//crmv@34627
		if (field.name == 'report_display') {
			var module = document.forms['EditView'].module.value;
			if (module != undefined && module != 'undefined' && module == 'CustomView') {
				reloadColumns(document.forms['EditView'].cvmodule.value,document.forms['EditView'].report.value);
			}
		}
		//crmv@34627e
		jQuery(field).attr('readonly','readonly');
		if (jQuery(field).parent('div').length > 0) {
			var div = jQuery(field).parent('div');
			div.attr('class','dvtCellInfoOff');
			div.focusin(function(){
				div.attr('class','dvtCellInfoOff');
			}).focusout(function(){
				div.attr('class','dvtCellInfoOff');
			});
		}
		jQuery(field).blur();
		if(checkbox && checkbox != undefined && checkbox != 'undefined') checkbox.checked = true;	//crmv@32341
	}
	function resetReferenceField(field) {
		field.readOnly = false;
		if (jQuery(field).parent('div').length > 0) {
			var div = jQuery(field).parent('div');
			div.attr('class','dvtCellInfo');
			div.focusin(function(){
				div.attr('class','dvtCellInfoOn');
			}).focusout(function(){
				div.attr('class','dvtCellInfo');
			});
		}
		field.value = '{/literal}{"LBL_SEARCH_STRING"|getTranslatedString}{literal}';
	}
	{/literal}
</script>
{* crmv@29190e *}