/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 */

if (typeof(ProcessMakerScript) == 'undefined') {
	ProcessMakerScript = {
		formatType: function(type){
			return type.replace('bpmn:','');
		},
		openMetadata: function(processid,id,structure){
			openPopup('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=load_metadata&id='+processid+'&elementid='+id+'&structure='+encodeURI(JSON.stringify(structure)));
		},
		reloadMetadata: function(processid,id){
			//jQuery('#config_'+id).remove();
			//ProcessMakerScript.closeMetadata(id);
			//jQuery('[data-element-id='+id+']').click();
			//window.location.reload(true);
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=load_metadata&id='+processid+'&elementid='+id;
		},
		backToList: function(fieldLabel){
			if (jQuery('[name="pm_active"]').val() == '1') {
				window.location.href = 'index.php?module=Settings&action=ProcessMaker';
			} else {
				vteconfirm(alert_arr.LBL_PM_CHECK_ACTIVE, function(yes) {
					if (yes) {
						jQuery('[name="pm_active"]').prop('checked',true);
						ProcessMakerScript.setActive(fieldLabel,'Settings',56,'','pm_active','1',function(){
							window.location.href = 'index.php?module=Settings&action=ProcessMaker';
						});
					} else {
						window.location.href = 'index.php?module=Settings&action=ProcessMaker';
					}
				});
			}
		},
		saveMetadata: function(processid,id,engineType,callback) {
			var object = jQuery('.form-config-shape[shape-id="'+id+'"]');
			jQuery('#config_'+id+'_Handle .indicatorMetadata').show();
			
			var metadata = {};
			jQuery.each(jQuery(object).serializeArray(), function(){
				metadata[this.name] = this.value;
			});
			
			// Task
			if (jQuery('#save_conditions',object).length > 0) {
				var conditions = GroupConditions.getJson(jQuery, 'save_conditions', jQuery(object));
				metadata['conditions'] = conditions;
			}
			
			// Process Helper
			var helper = {};
			jQuery.each(jQuery('.form-helper-shape[shape-id="'+id+'"]').serializeArray(), function(){
				helper[this.name] = this.value;
			});
			if (helper['active'] == 'on' && helper['related_to'] == '') {
				alert(alert_arr.LBL_PMH_SELECT_RELATED_TO);
				return false;
			}
			//crmv@96450
			var mmaker = {};
			jQuery.each(jQuery('#module_maker_form').serializeArray(), function(){
				mmaker[this.name] = this.value;
			});
			//crmv@96450e
			
			saveFunction = function() {
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=savemetadata&id='+processid+'&elementid='+id,
					'type': 'POST',
					'data': jQuery.param({'vte_metadata':JSON.stringify(metadata),'helper':JSON.stringify(helper),'mmaker':JSON.stringify(mmaker)}),
					success: function(data) {
						jQuery('#config_'+id+'_Handle .indicatorMetadata').hide();
						if (typeof(callback) != 'undefined')
							callback();
						else
							ProcessMakerScript.closeMetadata(id);
					},
					error: function() {}
				});
			}
			validateFunction  = function() {
				if (engineType == 'Condition' && jQuery('#isStartTask',object).val() == '1') {
					if (typeof(jQuery('[name="execution_condition"]:checked',object).val()) == 'undefined') {
						alert(alert_arr.LBL_PM_NO_CHECK_SELECTED);
						return false;
					} else if (jQuery('[name="execution_condition"]:checked',object).val() != 'ON_SUBPROCESS' && jQuery('[name="moduleName"]',object).val() == '') {
						alert(alert_arr.LBL_PM_NO_ENTITY_SELECTED);
						return false;
					}
					saveFunction();
				} else if (engineType == 'Condition') {
					if (jQuery('[name="moduleName"]',object).val() == '') {
						alert(alert_arr.LBL_PM_NO_ENTITY_SELECTED);
						return false;
					}
					if (typeof(jQuery('[name="execution_condition"]:checked',object).val()) == 'undefined') {
						alert(alert_arr.LBL_PM_NO_CHECK_SELECTED);
						return false;
					}
					saveFunction();
				} else if (engineType == 'TimerStart') {
					jQuery.ajax({
						'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=checktimerstart',
						'type': 'POST',
						'data': jQuery.param({'vte_metadata':JSON.stringify(metadata)}),
						'async': false,
						success: function(data) {
							if (data != '') {
								alert(data);
								return false;
							} else saveFunction();
						},
					});
				} else {
					saveFunction();
				}
			}
			validateFunction();
		},
		closeMetadata: function(id) {
			closePopup();
		},
		clearAssignedUserId: function(id) {
			jQuery('.form-helper-shape[shape-id="'+id+'"] #assign_user .dvtCellInfoImgRx img:nth-child(2)').click();
			jQuery('.form-helper-shape[shape-id="'+id+'"] #assign_user #assigned_user_id_display').blur();			
		},
		manageOtherRecords: function(processid) {
			openPopup('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=manage_other_records&id='+processid);
		},
		previewRecurrence: function(id) {
			var object = jQuery('.form-config-shape[shape-id="'+id+'"]');
			//jQuery('#config_'+id+'_Handle .indicatorMetadata').show();
			
			var metadata = {};
			jQuery.each(jQuery(object).serializeArray(), function(){
				metadata[this.name] = this.value;
			});
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=recurrence_preview',
				'type': 'POST',
				'data': jQuery.param({'vte_metadata':JSON.stringify(metadata)}),
				success: function(data) {
					//jQuery('#config_'+id+'_Handle .indicatorMetadata').hide();
					//ProcessMakerScript.closeMetadata(id);
					jQuery('#preview').html(data);
				},
				error: function() {}
			})
		},
		setActive: function(fieldLabel,module,uitype,tableName,fieldName,crmId,callback) {
            dtlViewAjaxSaveActive = function() {
                if (jQuery('[name="pm_active"]').prop('checked')) var value = '1'; else var value = '0';
                jQuery.ajax({
                    'url': 'index.php?module=Settings&action=SettingsAjax&file=DetailViewAjax&ajxaction=DETAILVIEW&record='+jQuery('[name="id"]').val()+'&recordid='+jQuery('[name="id"]').val()+'&fldName=pm_active&fieldValue='+value,
                    'type': 'POST',
                    success: function(data) {
                        if (data == ':#:SUCCESS') {
                            dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId);
                            if (typeof(callback) != 'undefined') callback();
                        } else {
                            alert('Error during save');
                            return false;
                        }
                    }
                });
            }
            if(jQuery('[name="pm_active"]').prop('checked')) {
                jQuery.ajax({
                    url: 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=CheckActiveProcesses',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        if (data && data.success) {
                            jQuery.ajax({
                                'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=checktimerstart&id='+jQuery('[name="id"]').val(),
                                'type': 'POST',
                                success: function(data) {
                                    if (data != '') {
                                        alert(data);
                                        return false;
                                    } else {
                                        dtlViewAjaxSaveActive();
                                    }
                                },
                            });
                        } else {
                            alert(data.message);
                        }
                    },
                });
            } else {
                dtlViewAjaxSaveActive();
            }
        },
		confirmdelete(url) {
			vteconfirm(alert_arr.ARE_YOU_SURE, function(yes) {
				if (yes) {
					location.href = url;
				}
			});
		},
		//crmv@99316
		advancedMetadataSettings: function(processmakerid,elementid,save) {
			if (typeof(save) == 'undefined') save = false;
			if (save) {
				ProcessMakerScript.saveMetadata(processmakerid,elementid,'Action',function(){
					window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=advanced_metadata&id='+processmakerid+'&elementid='+elementid;
				});
			} else {
				window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=advanced_metadata&id='+processmakerid+'&elementid='+elementid;
			}
		},
		closeAdvMetadata: function(processmakerid,elementid) {
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=load_metadata&id='+processmakerid+'&elementid='+elementid;
		},
		editDynaFormConditional: function(processmakerid,elementid,ruleid) {
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=edit_dynaform_conditional&id='+processmakerid+'&elementid='+elementid+'&ruleid='+ruleid;
		},
		deleteDynaFormConditional: function(processmakerid,elementid,ruleid) {
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=delete_dynaform_conditional&id='+processmakerid+'&elementid='+elementid+'&ruleid='+ruleid,
				'type': 'POST',
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
				}
			});
		},
		saveDynaFormConditional: function() {
			var object = jQuery('#DynaformConditionalForm');
			var data = {};
			jQuery.each(jQuery(object).serializeArray(), function(){
				data[this.name] = this.value;
			});
			if (jQuery('#save_conditions',object).length > 0) {
				data['conditions'] = GroupConditions.getJson(jQuery, 'save_conditions', jQuery(object));
			}
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_dynaform_conditional',
				'type': 'POST',
				'data': data,
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(jQuery('#processmakerid').val(),jQuery('#elementid').val());
				},
				error: function() {}
			});
		},
		closeDynaFormConditional: function(processmakerid,elementid,ruleid) {
			ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
		},
		//crmv@99316e
		//crmv@112297
		alertDisableAjaxSave: function() {
			// do nothing
			//alert('Click the button EDIT');
		},
		editConditional: function(processmakerid,elementid,ruleid) {
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=edit_conditional&id='+processmakerid+'&elementid='+elementid+'&ruleid='+ruleid;
		},
		deleteConditional: function(processmakerid,elementid,ruleid) {
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=delete_conditional&id='+processmakerid+'&elementid='+elementid+'&ruleid='+ruleid,
				'type': 'POST',
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
				}
			});
		},
		saveConditional: function() {
			var object = jQuery('#ConditionalForm');
			var data = {};
			jQuery.each(jQuery(object).serializeArray(), function(){
				data[this.name] = this.value;
			});
			if (jQuery('#save_conditions',object).length > 0) {
				data['conditions'] = GroupConditions.getJson(jQuery, 'save_conditions', jQuery(object));
			}
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_conditional',
				'type': 'POST',
				'data': data,
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(jQuery('#processmakerid').val(),jQuery('#elementid').val());
				},
				error: function() {}
			});
		},
		closeConditional: function(processmakerid,elementid,ruleid) {
			ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
		},
		load_field_permissions_table: function(id){
			if (id.indexOf(':') > -1) {
				var tmp = id.split(':');
				var module = tmp[1];
			} else {
				var module = id;
			}
			jQuery('#field_permissions_table').hide();
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=load_field_permissions_table&chk_module='+escape(module),
				'type': 'POST',
				success: function(data) {
					jQuery('#field_permissions_table').html(data);
					jQuery('#field_permissions_table').show();
				},
				error: function() {}
			});
		},
		populateField: function(obj,field) {
			var value = jQuery(obj).val();
			if (value != '') insertAtCursor(jQuery('[id="'+field+'"]').get(0), value);
		},
		//crmv@112297e
		//crmv@100731
		addAdvancedPermission: function(processmakerid,elementid) {
			var record_involved = jQuery('#record_involved').val();
			var resource_type = jQuery('#assigned_user_id_type').val();
			if (resource_type == 'U') {
				var resource = jQuery('#assigned_user_id').val();
			} else if (resource_type == 'T') {
				var resource = jQuery('#assigned_group_id').val();
			} else if (resource_type == 'O') {
				var resource = jQuery('#other_assigned_user_id').val();
			}
			if (record_involved == '') {
				alert(alert_arr.LBL_PM_SELECT_ENTITY);
				return false;
			}
			if (resource == '') {
				alert(alert_arr.LBL_PM_SELECT_RESOURCE);
				return false;
			}
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=add_advanced_permission',
				'type': 'POST',
				'data': {'processmakerid':processmakerid,'elementid':elementid,'record_involved':record_involved,'resource_type':resource_type,'resource':resource,'permission':jQuery('#permission').val()},
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
				},
				error: function() {}
			});
		},
		deleteAdvancedPermission: function(processmakerid,elementid,ruleid) {
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=delete_advanced_permission&id='+processmakerid+'&elementid='+elementid+'&ruleid='+ruleid,
				'type': 'POST',
				success: function(data) {
					ProcessMakerScript.advancedMetadataSettings(processmakerid,elementid);
				}
			});
		},
		//crmv@100731e
		//crmv@100972
		modeler: function(processmakerid) {
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=modeler&id='+processmakerid;
		},
		saveModel: function(processmakerid, xml, values) {
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_model',
				'type': 'POST',
				'data': {'id':processmakerid,'xml':xml,'values':JSON.stringify(values)},
				success: function(processmakerid) {
					ProcessMakerScript.detailProcessMaker(processmakerid);
				},
				error: function() {}
			});
		},
		detailProcessMaker: function(processmakerid) {
			window.location.href = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&parenttab=Settings&mode=detail&id='+processmakerid;
		}
		//crmv@100972e
	}
}