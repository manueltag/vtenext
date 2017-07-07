/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@104180 crmv@112297 crmv@115268 */
 
if (typeof(ConditionTaskScript) == 'undefined') {
	ConditionTaskScript = {
		init: function(processmakerid,elementId,params){
			var context = jQuery('form[shape-id="'+elementId+'"]');
			var moduleName = ConditionTaskScript.getModule(jQuery('[name="moduleName"]',context).val());
			var metaId = ConditionTaskScript.getMetaId(jQuery('[name="moduleName"]',context).val());
			var processmakerId = processmakerid;
			if (typeof(params) == 'undefined') var params = {};
			jQuery('[name="moduleName"]',context).change(function(){
				jQuery('#save_conditions',context).html('');
				moduleName = ConditionTaskScript.getModule(this.value);
				metaId = ConditionTaskScript.getMetaId(this.value);
				jQuery('#group_conditions_add',context).hide();
				if (this.value != '') {
					if (moduleName == 'DynaForm') {
						jQuery('#execution_condition_'+elementId+'_1',context).parent().parent().hide();
						jQuery('#execution_condition_'+elementId+'_3',context).parent().parent().hide();
						GroupConditions.init(jQuery, moduleName, 'save_conditions', context, null, {'otherParams':{'processmakerId':processmakerId,'metaId':metaId}});
					} else {
						jQuery('#execution_condition_'+elementId+'_1',context).parent().parent().show();
						jQuery('#execution_condition_'+elementId+'_3',context).parent().parent().show();
						GroupConditions.init(jQuery, moduleName, 'save_conditions', context, null, params);
					}
				}
			});
			if (moduleName != '') {
				if (jQuery('#conditions',context).html() != '') var conditions = JSON.parse(jQuery('#conditions',context).html()); else var conditions = null;
				if (moduleName == 'DynaForm') {
					jQuery('#execution_condition_'+elementId+'_1',context).parent().parent().hide();
					jQuery('#execution_condition_'+elementId+'_3',context).parent().parent().hide();
					GroupConditions.init(jQuery, moduleName, 'save_conditions', context, conditions, {'otherParams':{'processmakerId':processmakerId,'metaId':metaId}});
				} else {
					jQuery('#execution_condition_'+elementId+'_1',context).parent().parent().show();
					jQuery('#execution_condition_'+elementId+'_3',context).parent().parent().show();
					GroupConditions.init(jQuery, moduleName, 'save_conditions', context, conditions, params);
				}
			}
			//crmv@97575
			selectModuleName = function(value) {
				if (value == 'ON_SUBPROCESS') {
					jQuery('[name="moduleName"]',context).val('');
					jQuery('[name="moduleName"]',context).prop('disabled', 'disabled');
					jQuery('[name="moduleName"]',context).addClass('disabled');
				} else {
					jQuery('[name="moduleName"]',context).prop('disabled', false);
					jQuery('[name="moduleName"]',context).removeClass('disabled');
				}
			}
			jQuery('[name="execution_condition"]',context).change(function(){
				selectModuleName(this.value);
			});
			selectModuleName(jQuery('[name="execution_condition"]:checked',context).val());
			//crmv@97575e
		},
		getModule: function(str){
			if (str.indexOf(':') > -1) {
				var res = str.split(':');
				str = res[1];
			}
			return str;
		},
		getMetaId: function(str){
			if (str.indexOf(':') > -1) {
				var res = str.split(':');
				str = res[0];
			}
			return str;
		}
	}
}

if (typeof(ActionConditionScript) == 'undefined') {
	ActionConditionScript = {
		
		init: function(processmakerid, elementId, metaId, fieldName) {
			var me = this;
			
			var context = jQuery('#actionform'),
				cond = jQuery('#conditions',context).html();
				
			var conditions = (cond != '' ? JSON.parse(cond) : null);
			var oParams = {
				processmakerId: processmakerid,
				metaId: metaId,
				fieldName: fieldName,
				dynaFormConditional: true,
				cycle: true
			}
			
			GroupConditions.init(jQuery, 'TableField', 'save_conditions', context, conditions, {'otherParams':oParams});
		}
	}
}