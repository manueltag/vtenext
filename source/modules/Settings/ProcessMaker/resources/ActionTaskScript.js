/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@96450 crmv@108078 crmv@115268 crmv@118977 */
 
if (typeof(ActionTaskScript) == 'undefined') {
	ActionTaskScript = {
		describe_object_cache:{},
	
		init: function(id){
			var context = jQuery('form[shape-id="'+id+'"]');
			var newTaskPopup = NewTaskPopup(jQuery,context);
			jQuery("#new_task",context).click(function(){
				newTaskPopup.show();
			});
		},
		
		// crmv@102879
		changeActionType: function(self) {
			var value = jQuery(self).val();
			
			jQuery('#new_task_cycle').hide();
			jQuery('#new_task_inserttablerow').hide();
			if (value == 'Cycle') {
				jQuery('#new_task_cycle').show();
			} else if (value == 'InsertTableRow') {
				jQuery('#new_task_inserttablerow').show();
			}
		},
		
		changeCycleActionType: function(self) {
			var value = jQuery(self).val();
			
			if (value == 'InsertTableRow') {
				jQuery('#cycle_inserttablerow').show();
			} else {
				jQuery('#cycle_inserttablerow').hide();
			}
		},
		
		editaction: function(processid,id,action_type,action_id, cycle_field, cycle_action, inserttablerow_field){	// popolare i nuovi campi dal tpl
			var me = this;
			ProcessMakerScript.saveMetadata(processid,id,'Action',function(){
				if (action_type.indexOf('SDK:') == 0) {
					var meta_action = {'action_type':'SDK','function':action_type.substring(4)};
					me.saveaction(processid,id,action_type,action_id,'',meta_action);
				} else {
					var url = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=editaction&id='+processid+'&elementid='+id+'&action_type='+action_type+'&action_id='+action_id;
					if (action_type == 'Cycle') {
						// get cycle field and subaction
						url += '&cycle_field='+encodeURIComponent(cycle_field || jQuery('#table_fields').val());
						url += '&cycle_action='+encodeURIComponent(cycle_action || jQuery('#cycle_action_type').val());
						url += '&inserttablerow_field='+encodeURIComponent(inserttablerow_field || jQuery('#cycle_inserttablerow_table_fields').val());
					} else if (action_type == 'InsertTableRow') {
						url += '&inserttablerow_field='+encodeURIComponent(inserttablerow_field || jQuery('#inserttablerow_table_fields').val());					
					}
					window.location.href = url;
				}
			});
		},
		// crmv@102879e
		
		// crmv@104180
		saveaction: function(processid,id,action_type,action_id,action_title,meta_action){
			var context = top.jQuery('form[shape-id="'+id+'"]');
			if (typeof(meta_action) == 'undefined') var meta_action = {};
			jQuery.each(jQuery('#actionform').serializeArray(), function(){
				meta_action[this.name] = this.value;
			});
			if (typeof(CKEDITOR) != "undefined" && CKEDITOR.instances != undefined) {
				jQuery.each(CKEDITOR.instances,function(fldName,obj){
					var textObj = CKEDITOR.instances[fldName];
					meta_action[obj.element.getAttribute('name')] = textObj.getData();
				});
			}
			if (jQuery('#editForm').length > 0) {
				meta_action['form'] = {};
				jQuery.each(jQuery('#editForm').find('form[name="EditView"]').serializeArray(), function(){
					meta_action['form'][this.name] = this.value;
				});
			}
			
			var object = jQuery('#actionform');
			if (jQuery('#save_conditions',object).length > 0) {
				var conditions = GroupConditions.getJson(jQuery, 'save_conditions', jQuery(object));
				meta_action['conditions'] = conditions;
			}
			
			var postdata = {
				meta_action: meta_action
			};
			
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=saveaction&id='+processid+'&elementid='+id+'&action_id='+action_id,
				'type': 'POST',
				'data': postdata,
				success: function(data) {
					ProcessMakerScript.reloadMetadata(processid,id);
				}
			});
		},
		// crmv@104180e
		
		deleteaction: function(processid,id,action_id){
			ProcessMakerScript.saveMetadata(processid,id,'Action',function(){
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=deleteaction&id='+processid+'&elementid='+id+'&action_id='+action_id,
					'type': 'POST',
					success: function(data) {
						ProcessMakerScript.reloadMetadata(processid,id);
					}
				});
			});
		},
		
		//crmv@106856
		openAdvancedFieldAssignment: function(processid,elementid,actionid,fieldname,form_module,open,reload_session){
			var url = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=open_advanced_field_assignment&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&form_module='+form_module+'&fieldname='+fieldname;
			if (reload_session) url += '&reload_session=yes';
			else url += '&reload_session=no';
			/*
			if (jQuery('[name="record_involved"]').length > 0) {
				var record_involved = jQuery('[name="record_involved"]').val().split(':');
				url += '&form_module='+record_involved[1];
			} else if (jQuery('[name="form_module"]').length > 0) {
				url += '&form_module='+jQuery('[name="form_module"]').val();
			}*/
			if (open == 'popup') {
				openPopup(url);
			} else if (open == 'parent') {
				jQuery.fancybox.showLoading();
				parent.window.location.href = url;
			} else if (open == 'current') {
				jQuery.fancybox.showLoading();
				window.location.href = url;
			}
		},
		saveAdvancedFieldAssignment: function(processid,elementid,actionid,fieldname){
			var me = this;
			me.saveAdvancedFieldAssignmentValues(fieldname, function(){
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=reload_advanced_field_assignment&val=no&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&fieldname='+fieldname,
					'type': 'POST',
					success: function(data) {
						closePopup();
					}
				});
			});
		},
		closeAdvancedFieldAssignment: function(processid,elementid,actionid,fieldname){
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=reload_advanced_field_assignment&val=yes&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&fieldname='+fieldname,
				'type': 'POST',
				success: function(data) {
					closePopup();
				}
			});
		},
		editAdvancedFieldAssignment: function(processid,elementid,actionid,fieldname,form_module,ruleid){
			var me = this;
			me.saveAdvancedFieldAssignmentValues(fieldname, function(){
				openPopup('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=open_advanced_field_assignment_condition&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&form_module='+form_module+'&fieldname='+fieldname+'&ruleid='+ruleid);
			});
		},
		deleteAdvancedFieldAssignment: function(processid,elementid,actionid,fieldname,form_module,ruleid){
			var me = this;
			me.saveAdvancedFieldAssignmentValues(fieldname, function(){
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=delete_advanced_field_assignment&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&fieldname='+fieldname+'&ruleid='+ruleid,
					'type': 'POST',
					success: function(data) {
						me.openAdvancedFieldAssignment(processid,elementid,actionid,fieldname,form_module,'current',false);
					}
				});
			});
		},
		saveAdvancedFieldAssignmentValues: function(fieldname,callback){	// update values in session
			var form = {};
			jQuery.each(jQuery('form[name="EditView"]').serializeArray(), function(){
				form[this.name] = this.value;
			});
			var postdata = {
				form: JSON.stringify(form)
			}
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_advanced_field_assignment_values&fieldname='+fieldname,
				'type': 'POST',
				'data': postdata,
				success: function(data) {
					callback();
				}
			});
		},
		openAdvancedFieldAssignmentCondition: function(processid,elementid,actionid,fieldname,form_module){
			var me = this;
			me.saveAdvancedFieldAssignmentValues(fieldname, function(){
				openPopup('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=open_advanced_field_assignment_condition&processid='+processid+'&elementid='+elementid+'&actionid='+actionid+'&form_module='+form_module+'&fieldname='+fieldname);
			});
		},
		closeAdvancedFieldAssignmentCondition: function(){
			closePopup();
		},
		saveAdvancedFieldAssignmentCondition: function(processid,elementid,actionid,fieldname,form_module,ruleid){
			var me = this;
			var postdata = {
				meta_record: jQuery('[name="moduleName"]').val(),
				conditions: GroupConditions.getJson(jQuery,'save_conditions',jQuery('form[shape-id="'+elementid+'"]'))
			};
			if (postdata.meta_record == '') {
				alert(alert_arr.LBL_PM_SELECT_ENTITY);
				return false;
			}
			if (postdata.conditions == '') {
				alert(alert_arr.LBL_LEAST_ONE_CONDITION);
				return false;
			}
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_advanced_field_assignment_condition&fieldname='+fieldname+'&ruleid='+ruleid,
				'type': 'POST',
				'data': postdata,
				success: function(data) {
					me.openAdvancedFieldAssignment(processid,elementid,actionid,fieldname,form_module,'parent',false);
					me.closeAdvancedFieldAssignmentCondition();
				}
			});
		},
		//crmv@106856e
		
		//crmv@113527
		showSdkParamsInput: function(field, fieldname){
			var value = jQuery(field).val();
			if (value.indexOf('$sdk:') > -1) {
				jQuery('#container_sdk_params_'+fieldname).show();
			} else {
				jQuery('#container_sdk_params_'+fieldname).hide();
				jQuery('#sdk_params_'+fieldname).val('');
			}
		},
		//crmv@113527e
		
		calendarDateOptions: function(value,field) {
			if (value == '' || value == null) {
				jQuery('[name="'+field+'"]').hide();
				jQuery('#jscal_trigger_'+field).hide();
				jQuery('#'+field+'_adv_options').hide();
			} else if (value == 'custom') {
				jQuery('[name="'+field+'"]').show();
				jQuery('#jscal_trigger_'+field).show();
				jQuery('#'+field+'_adv_options').hide();
			} else {
				jQuery('[name="'+field+'"]').hide();
				jQuery('#jscal_trigger_'+field).hide();
				jQuery('#'+field+'_adv_options').show();
			}
		},
		calendarTimeOptions: function(value,field) {
			if (value == '' || value == null) {
				jQuery('#'+field+'_custom').hide();
				jQuery('#'+field+'_adv_options').hide();
			} else if (value == 'custom') {
				jQuery('#'+field+'_custom').show();
				jQuery('#'+field+'_adv_options').hide();
			} else {
				jQuery('#'+field+'_custom').hide();
				jQuery('#'+field+'_adv_options').show();
			}
		},
		
		//crmv@113775
		loadPotentialRelations: function(record) {
			if (record == '') {
				jQuery('#record2_container').html('');
			} else {
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=load_potential_relations&record1='+record+'&id='+jQuery('[name="id"]').val()+'&elementid='+jQuery('[name="elementid"]').val(),
					'type': 'POST',
					success: function(data) {
						jQuery('#record2_container').html(data);
					}
				});
			}
		},
		//crmv@113775e
		
		loadFormEditOptions: function(object,module,params) {
			var i = 0,
				involved_records = params['involved_records'],
				form_data = params['form_data'],
				picklist_values = params['picklist_values'],
				reference_values = params['reference_values'],
				reference_users_values = params['reference_users_values'],
				boolean_values = params['boolean_values'],
				date_values = params['date_values'],
				dynaform_options = params['dynaform_options'],
				elements_actors = params['elements_actors'];
			var vtinst = new VtigerWebservices("webservice.php",undefined,undefined,true);
			vtinst.extendSession(handleError(function(result){
				vtinst.listTypes(handleError(function(accessibleModules) {
					accessibleModulesInfo = accessibleModules;
					if (involved_records == null) return false;	// check if there are involved records
					jQuery.each(involved_records,function(key,involved_record){
						var moduleName = involved_record.module;
						if (moduleName == '' || moduleName == null) {	// check if there are involved records
							i++;
							return;
						}
						getDescribeObjects(vtinst, accessibleModules, moduleName, handleError(function(modules){
							i++;
							if (object.objectName == 'ActionCreateScript') {
								fillSelectBox('task-fieldnames', modules, moduleName, involved_record, null);
							} else {
								fillSelectBox('task-fieldnames', modules, moduleName, involved_record);
							}
							fillSelectBox('task-pickfieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='picklist');});
							fillSelectBox('task-smownerfieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='reference' && e['type']['refersTo'][0]=='Users');});
							fillSelectBox('task-referencefieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='reference' && e['type']['refersTo'][0]!='Users' && e['type']['refersTo'][0]!='Currency');});
							fillSelectBox('task-booleanfieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='boolean');});
							fillSelectBox('task-datefieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='date' || e['type']['name']=='datetime');});
							// last
							if (i == jQuery(involved_records).length) {
								appendDynaformOptions(jQuery('#task-fieldnames'),dynaform_options,'all');
								jQuery('#editForm .editoptions').each(function(){
									jQuery(this).html('<select class="populateFieldGroup"></select><select style="display:none" class="populateField" onchange="'+object.objectName+'.populateField(this)">'+jQuery('#task-fieldnames').html()+'</select>');	//crmv@112299
								});
								// picklist
								appendDynaformOptions(jQuery('#task-pickfieldnames'),dynaform_options,'picklist');
								jQuery.each(picklist_values, function(name,value){
									jQuery('[name="'+name+'"]').append(jQuery('#task-pickfieldnames').html());
									if (value != null) jQuery('[name="'+name+'"]').val(value);
								});
								// owner
								appendDynaformOptions(jQuery('#task-smownerfieldnames'),dynaform_options,'user');
								//crmv@100591
								if (jQuery(elements_actors).length > 0 && !checkSelectBoxDuplicates(jQuery('#task-smownerfieldnames'),alert_arr.LBL_PM_ELEMENTS_ACTORS)) {
									var append = '<optgroup label="'+alert_arr.LBL_PM_ELEMENTS_ACTORS+'">';
									jQuery.each(elements_actors, function(fieldvalue, fieldlabel){
										append += '<option value="'+fieldvalue+'">'+fieldlabel+'</value>';
									});
									append += '</optgroup>';
									jQuery('#task-smownerfieldnames').append(append);
								}
								//crmv@100591e
								jQuery('#other_assigned_user_id').append(jQuery('#task-smownerfieldnames').html());
								if (jQuery('#assigned_user_id_type').val() == 'O' && form_data['assigned_user_id'] != undefined) {
									jQuery('#other_assigned_user_id').val(form_data['assigned_user_id']);
								}
								//crmv@106856
								if (form_data != null && form_data['assigned_user_id'] == 'advanced_field_assignment') {
									jQuery('#advanced_field_assignment_button_assigned_user_id').show();
									jQuery('#other_assigned_user_id').width(jQuery('#other_assigned_user_id').width()-35);
								}
								jQuery('#other_assigned_user_id').change(function(){
									if (jQuery(this).val() == 'advanced_field_assignment') {
										jQuery('#advanced_field_assignment_button_assigned_user_id').show();
										jQuery('#other_assigned_user_id').width(jQuery('#other_assigned_user_id').width()-35);
									} else {
										jQuery('#advanced_field_assignment_button_assigned_user_id').hide();
										jQuery('#other_assigned_user_id').width('100%');
									}
									ActionTaskScript.showSdkParamsInput(this,'assigned_user_id');	//crmv@113527
								});
								if (jQuery('#other_assigned_user_id').length > 0) ActionTaskScript.showSdkParamsInput(jQuery('#other_assigned_user_id'),'assigned_user_id');	//crmv@113527
								//crmv@106856e
								// reference
								appendDynaformOptions(jQuery('#task-referencefieldnames'),dynaform_options,'reference');
								jQuery.each(reference_values, function(name,value){
									jQuery('#other_'+name).append(jQuery('#task-referencefieldnames').html());
									if ((module == 'Calendar' || module == 'Events') && name == 'parent_id') var field_type = 'parent_type'; else var field_type = name+'_type'; 
									if (jQuery('#'+field_type).val() == 'Other' && form_data[name] != undefined) {
										jQuery('#other_'+name).val(form_data[name]);
									}
								});
								// reference users
								jQuery.each(reference_users_values, function(name,value){
									jQuery('[name="'+name+'"]').append(jQuery('#task-smownerfieldnames').html());
									//jQuery('[name="'+name+'"] option[value=""]').remove();	//crmv@105312
									if (value != null) jQuery('[name="'+name+'"]').val(value);
									//crmv@106856
									if (value == 'advanced_field_assignment') {
										jQuery('#advanced_field_assignment_button_'+name).show();
										jQuery('[name="'+name+'"]').width(jQuery('[name="'+name+'"]').width()-35);
									}
									jQuery('[name="'+name+'"]').change(function(){
										if (jQuery(this).val() == 'advanced_field_assignment') {
											jQuery('#advanced_field_assignment_button_'+name).show();
											jQuery('[name="'+name+'"]').width(jQuery('[name="'+name+'"]').width()-35);
										} else {
											jQuery('#advanced_field_assignment_button_'+name).hide();
											jQuery('[name="'+name+'"]').width('100%');
										}
									});
									//crmv@106856e
								});
								// boolean
								appendDynaformOptions(jQuery('#task-booleanfieldnames'),dynaform_options,'boolean');
								jQuery.each(boolean_values, function(name,value){
									jQuery('[name="'+name+'"]').append(jQuery('#task-booleanfieldnames').html());
									if (value != null) jQuery('[name="'+name+'"]').val(value);
								});
								// date
								appendDynaformOptions(jQuery('#task-datefieldnames'),dynaform_options,'date');
								//crmv@120769
								jQuery.each(date_values, function(name,value){
									jQuery('[name="'+name+'_options"]').append(jQuery('#task-datefieldnames').html());
									if (value != null && value != '') {
										try {
											value = jQuery.parseJSON(value);
											jQuery('[name="'+name+'_options"]').val(value['options']);
											jQuery('[name="'+name+'"]').val(value['custom']);
											jQuery('[name="'+name+'_opt_operator"]').val(value['operator']);
											jQuery('[name="'+name+'_opt_num"]').val(value['num']);
											jQuery('[name="'+name+'_opt_unit"]').val(value['unit']);
											if (name == 'time_start' || name == 'time_end')
												ActionTaskScript.calendarTimeOptions(value['options'],name);
											else
												ActionTaskScript.calendarDateOptions(value['options'],name);
										} catch(err) {	// old mode
											jQuery('[name="'+name+'_options"]').val('custom');
											jQuery('[name="'+name+'"]').val(value);
											ActionTaskScript.calendarDateOptions('custom',name);
										}												
									}
								});
								//crmv@120769e
								filterPopulateField();	//crmv@112299
								jQuery.fancybox.hideLoading();
							}
						}));
					});
				}));
			}));
		}
	}
}

if (typeof(ActionCreateScript) == 'undefined') {
	ActionCreateScript = {
		objectName: 'ActionCreateScript',
		loadForm: function(module,processid,id,action_type,action_id,tablerow_mode) {
			var me = ActionCreateScript;
			if (typeof(tablerow_mode) == 'undefined') var tablerow_mode = false;
			if (module == '') {
				jQuery('#editForm').html('');
			} else {
				jQuery.fancybox.showLoading();
				// crmv@102879
				var url = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker/actions/CreateForm&mod='+module+'&id='+processid+'&elementid='+id+'&action_id='+action_id;
				url += '&cycle_field='+encodeURIComponent(jQuery('input[name=cycle_field]').val() || '');
				url += '&cycle_action='+encodeURIComponent(jQuery('input[name=cycle_action]').val() || '');
				if (tablerow_mode) url += '&tablerow_mode=1'; else url += '&tablerow_mode=0';
				// crmv@102879e
				
				jQuery.ajax({
					'url': url,
					'type': 'POST',
					success: function(data) {
						var res = data.split('|&|&|&|');
						if (res[0] != '') var involved_records = JSON.parse(res[0]); else var involved_records = {};
						if (res[1] != '') var form_data = JSON.parse(res[1]); else var form_data = {};
						if (res[2] != '') var picklist_values = JSON.parse(res[2]); else var picklist_values = {};
						if (res[3] != '') var reference_values = JSON.parse(res[3]); else var reference_values = {};
						if (res[4] != '') var reference_users_values = JSON.parse(res[4]); else var reference_users_values = {};
						if (res[5] != '') var boolean_values = JSON.parse(res[5]); else var boolean_values = {};
						if (res[6] != '') var date_values = JSON.parse(res[6]); else var date_values = {};
						if (res[7] != '') var dynaform_options = JSON.parse(res[7]); else var dynaform_options = {};
						if (res[8] != '') var elements_actors = JSON.parse(res[8]); else var elements_actors = {};	//crmv@100591
						try {
							jQuery('#editForm').html(res[9]);
						} catch(err) {
						    console.error(err.message);
						}
						var params = {
							'involved_records':involved_records,
							'form_data':form_data,
							'picklist_values':picklist_values,
							'reference_values':reference_values,
							'reference_users_values':reference_users_values,
							'boolean_values':boolean_values,
							'date_values':date_values,
							'dynaform_options':dynaform_options,
							'elements_actors':elements_actors
						}
						ActionTaskScript.loadFormEditOptions(me,module,params);
					}
				});
			}
		},
		//crmv@106857
		populateField: function(field){
			var tagField = jQuery(field);
			var fieldname = jQuery(field).parent().attr('fieldname');
			var field = jQuery('#editForm [name="'+fieldname+'"]');
			var value = jQuery(tagField).val();
			//crmv@112299
			if (value == 'back') {
				restorePopulateFieldGroup(tagField);
			//crmv@112299e
			} else if (value.indexOf('::') != -1) {
				// show table fields options
				if (jQuery('#actionform [name="cycle_action"]').val() != '') {
					// check if I am in a cycle
					var cycle_field = jQuery('#actionform [name="cycle_field"]').val().replace(':','-');
					if (value.indexOf('$'+cycle_field+'::') == 0 || value.indexOf('$DF'+cycle_field+'::') == 0) {
						jQuery("#tablefields_options_"+fieldname+" .cycle_opt").show();
					} else {
						jQuery("#tablefields_options_"+fieldname+" .cycle_opt").hide();
					}
				}
				jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','300px');
				jQuery("#tablefields_options_"+fieldname+" option:eq(0)").prop('selected', true);
				jQuery("#tablefields_options_"+fieldname).show();
			} else {
				// hide table fields options
				jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','400px');
				jQuery("#tablefields_options_"+fieldname).hide();
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				// end
				if (value != '') insertAtCursor(field.get(0), value);
			}
		},
		changeTableFieldOpt: function(obj, fieldname){
			var me = this,
				value = obj.value;
			if (value == 'seq') {
				jQuery('#tablefields_seq_'+fieldname).show().focus();
				jQuery('#tablefields_seq_btn_'+fieldname).show();
			} else {
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				if (value != '') me.insertTableFieldValue(obj, fieldname, value);
			}
		},
		insertTableFieldValue: function(obj, fieldname, value){
			var tagField = jQuery(obj).parent().parent().find('.editoptions .populateField');
			var parent_value = jQuery(tagField).val();
			var field = jQuery('#editForm [name="'+fieldname+'"]');
			if (value == 'seq') {
				var sequence = parseInt(jQuery('#tablefields_seq_'+fieldname).val());
				if (isNaN(sequence) || sequence <= 0) return false;
				else value += ':'+sequence;
			}
			insertAtCursor(field.get(0), parent_value+':'+value);
		}
		//crmv@106857e
	}
}

if (typeof(ActionUpdateScript) == 'undefined') {
	ActionUpdateScript = {
		objectName: 'ActionUpdateScript',
		loadForm: function(record_involved,processid,id,action_type,action_id) {
			var me = ActionUpdateScript;
			if (record_involved == '') {
				jQuery('#editForm').html('');
			} else {
				jQuery.fancybox.showLoading();
				var tmp = record_involved.split(':'),
					record_involved = tmp[0],
					module = tmp[1],
					url = 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker/actions/UpdateForm&record_involved='+record_involved+'&mod='+module+'&id='+processid+'&elementid='+id+'&action_id='+action_id;
									
				jQuery.ajax({
					'url': url,
					'type': 'POST',
					success: function(data) {
						var res = data.split('|&|&|&|');
						if (res[0] != '') var involved_records = JSON.parse(res[0]); else var involved_records = {};
						if (res[1] != '') var form_data = JSON.parse(res[1]); else var form_data = {};
						if (res[2] != '') var picklist_values = JSON.parse(res[2]); else var picklist_values = {};
						if (res[3] != '') var reference_values = JSON.parse(res[3]); else var reference_values = {};
						if (res[4] != '') var reference_users_values = JSON.parse(res[4]); else var reference_users_values = {};
						if (res[5] != '') var boolean_values = JSON.parse(res[5]); else var boolean_values = {};
						if (res[6] != '') var date_values = JSON.parse(res[6]); else var date_values = {};
						if (res[7] != '') var dynaform_options = JSON.parse(res[7]); else var dynaform_options = {};
						if (res[8] != '') var elements_actors = JSON.parse(res[8]); else var elements_actors = {};	//crmv@100591
						try {
							jQuery('#editForm').html(res[9]);
						} catch(err) {
						    console.error(err.message);
						}
						var params = {
							'involved_records':involved_records,
							'form_data':form_data,
							'picklist_values':picklist_values,
							'reference_values':reference_values,
							'reference_users_values':reference_users_values,
							'boolean_values':boolean_values,
							'date_values':date_values,
							'dynaform_options':dynaform_options,
							'elements_actors':elements_actors
						}
						ActionTaskScript.loadFormEditOptions(me,module,params);
						
						jQuery('#editForm form[name="EditView"] :input').bind('change onchange',function(e){
							var name = jQuery(this).attr('name');
							if (name == 'other_assigned_user_id') name = 'assigned_user_id';
							ActionUpdateScript.setMasseditCheck(name);
						});
						jQuery.each(form_data,function(name,value){
							ActionUpdateScript.setMasseditCheck(name);
						});
					}
				});
			}
		},
		//crmv@106857
		populateField: function(field){
			var tagField = jQuery(field);
			var fieldname = jQuery(field).parent().attr('fieldname');
			var field = jQuery('#editForm [name="'+fieldname+'"]');
			var value = jQuery(tagField).val();
			//crmv@112299
			if (value == 'back') {
				restorePopulateFieldGroup(tagField);
			//crmv@112299e
			} else if (value.indexOf('::') != -1) {
				// show table fields options
				jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','300px');
				jQuery("#tablefields_options_"+fieldname+" option:eq(0)").prop('selected', true);
				jQuery("#tablefields_options_"+fieldname).show();
			} else {
				// hide table fields options
				jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','400px');
				jQuery("#tablefields_options_"+fieldname).hide();
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				// end
				if (value != '') insertAtCursor(field.get(0), value);
				ActionUpdateScript.setMasseditCheck(fieldname);
			}
		},
		setMasseditCheck: function(fieldname){
			jQuery('#editForm form[name="EditView"] #'+fieldname+'_mass_edit_check').prop('checked',true);
		},
		changeTableFieldOpt: function(obj, fieldname){
			var me = this,
				value = obj.value;
			if (value == 'seq') {
				jQuery('#tablefields_seq_'+fieldname).show().focus();
				jQuery('#tablefields_seq_btn_'+fieldname).show();
			} else {
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				if (value != '') me.insertTableFieldValue(obj, fieldname, value);
			}
		},
		insertTableFieldValue: function(obj, fieldname, value){
			var tagField = jQuery(obj).parent().parent().find('.editoptions .populateField');
			var parent_value = jQuery(tagField).val();
			var field = jQuery('#editForm [name="'+fieldname+'"]');
			if (value == 'seq') {
				var sequence = parseInt(jQuery('#tablefields_seq_'+fieldname).val());
				if (isNaN(sequence) || sequence <= 0) return false;
				else value += ':'+sequence;
			}
			insertAtCursor(field.get(0), parent_value+':'+value);
			ActionUpdateScript.setMasseditCheck(fieldname);
		}
		//crmv@106857e
	}
}

if (typeof(ActionEmailScript) == 'undefined') {
	ActionEmailScript = {
		loadForm: function(processid,id,action_type,action_id,involved_records,dynaform_options,elements_actors) {	//crmv@100591
			involved_records = JSON.parse(involved_records);
			dynaform_options = JSON.parse(dynaform_options);
			elements_actors = JSON.parse(elements_actors);	//crmv@100591
			var me = this,
				i = 0,
				vtinst = new VtigerWebservices("webservice.php",undefined,undefined,true)
			last = function(){
				jQuery('#task-fieldnames-busyicon').hide();
				jQuery('#task-subjectfields-busyicon').hide();
				jQuery('#task-emailfields-busyicon').hide();
				jQuery('#task-emailfields_sender-busyicon').hide();
				jQuery('#task-emailfieldscc-busyicon').hide();
				jQuery('#task-emailfieldsbcc-busyicon').hide();
				//time_changes
				jQuery('#task_timefields').unbind('change');
				jQuery('#task_timefields').change(function(){
					var textarea = CKEDITOR.instances.save_content;
					var value = jQuery(this).val();
					textarea.insertHtml(value);
				});
				filterPopulateField();	//crmv@112299
			}
			vtinst.extendSession(handleError(function(result){
				vtinst.listTypes(handleError(function(accessibleModules) {
					accessibleModulesInfo = accessibleModules;
					if (involved_records == null) { last(); return false; }	// check if there are involved records
					jQuery.each(involved_records,function(key,involved_record){
						var moduleName = involved_record.module;
						if (moduleName == '' || moduleName == null) {	// check if there are involved records
							i++;
							return;
						}
						getDescribeObjects(vtinst, accessibleModules, moduleName, handleError(function(modules){
							i++;
						
							fillSelectBox('task-fieldnames', modules, moduleName, involved_record);
							jQuery('#task-fieldnames').prev('.populateFieldGroup').show();
							
							fillSelectBox('task-subjectfields', modules, moduleName, involved_record, function(e){return (e['type']['name']!='file' && e['type']['name']!='text');});
							jQuery('#task-subjectfields').prev('.populateFieldGroup').show();
							
							fillSelectBox('task-emailfields', modules, moduleName, involved_record, function(e){return e['type']['name']=='email';});
							
							if (i == jQuery(involved_records).length) {
								appendDynaformOptions(jQuery('#task-fieldnames'),dynaform_options,'all');
								jQuery('#task-fieldnames').unbind('change');
								jQuery('#task-fieldnames').change(function(){
									me.populateField('append_textarea',CKEDITOR.instances.save_content,this,'content'); //crmv@106857
								});
								
								appendDynaformOptions(jQuery('#task-subjectfields'),dynaform_options,'all');
								jQuery('#task-subjectfields').unbind('change');
								jQuery('#task-subjectfields').change(function(){
									me.populateField('append_input_space',jQuery(jQuery('#save_subject').get()),this,'subject'); //crmv@106857
								});
								
								appendDynaformOptions(jQuery('#task-emailfields'),dynaform_options,'email');
								//crmv@100591
								if (jQuery(elements_actors).length > 0 && !checkSelectBoxDuplicates(jQuery('#task-emailfields'),alert_arr.LBL_PM_ELEMENTS_ACTORS)) {
									var append = '<optgroup label="'+alert_arr.LBL_PM_ELEMENTS_ACTORS+'">';
									jQuery.each(elements_actors, function(fieldvalue, fieldlabel){
										append += '<option value="'+fieldvalue+'">'+fieldlabel+'</value>';
									});
									append += '</optgroup>';
									jQuery('#task-emailfields').append(append);
								}
								//crmv@100591e
								jQuery('#task-emailfields').unbind('change');
								jQuery('#task-emailfields').change(function(){
									me.populateField('append_input_comma',jQuery(jQuery('#save_recepient').get()),this,'recepient'); //crmv@106857
								});
								jQuery('#task-emailfields').show();
								
								jQuery('#task-emailfields_sender').html(jQuery('#task-emailfields').html());
								jQuery('#task-emailfields_sender').unbind('change');
								jQuery('#task-emailfields_sender').change(function(){
									me.populateField('overwrite_input',jQuery(jQuery('#save_sender').get()),this,'sender'); //crmv@106857
								});
								jQuery('#task-emailfields_sender').show();
								
								jQuery('#task-emailfieldscc').html(jQuery('#task-emailfields').html());
								jQuery('#task-emailfieldscc').unbind('change');
								jQuery('#task-emailfieldscc').change(function(){
									me.populateField('append_input_comma',jQuery(jQuery('#save_emailcc').get()),this,'emailcc'); //crmv@106857
								});
								jQuery('#task-emailfieldscc').show();
								
								jQuery('#task-emailfieldsbcc').html(jQuery('#task-emailfields').html());
								jQuery('#task-emailfieldsbcc').unbind('change');
								jQuery('#task-emailfieldsbcc').change(function(){
									me.populateField('append_input_comma',jQuery(jQuery('#save_emailbcc').get()),this,'emailbcc'); //crmv@106857
								});
								jQuery('#task-emailfieldsbcc').show();

								last();
							}
						}));
					});
				}));
			}));
		},
		//crmv@106857
		populateField: function(mode,target,field,fieldname){
			var me = this,
				value = jQuery(field).val();
			//crmv@112299
			if (value == 'back') {
				restorePopulateFieldGroup(field);
			//crmv@112299e
			} else if (value.indexOf('::') != -1) {
				// show table fields options
				if (jQuery('#actionform [name="cycle_action"]').val() != '') {
					// check if I am in a cycle
					var cycle_field = jQuery('#actionform [name="cycle_field"]').val().replace(':','-');
					if (value.indexOf('$'+cycle_field+'::') == 0 || value.indexOf('$DF'+cycle_field+'::') == 0) {
						jQuery("#tablefields_options_"+fieldname+" .cycle_opt").show();
					} else {
						jQuery("#tablefields_options_"+fieldname+" .cycle_opt").hide();
					}
				}
				//jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','300px');
				jQuery("#tablefields_options_"+fieldname+" option:eq(0)").prop('selected', true);
				jQuery("#tablefields_options_"+fieldname).show();
			} else {
				// hide table fields options
				//jQuery(field).parent().parent().find('.editoptions .populateField').css('max-width','400px');
				jQuery("#tablefields_options_"+fieldname).hide();
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				// end
				me.insertAtCursor(mode,target,value);
			}
		},
		changeTableFieldOpt: function(mode,target,fieldname,dropdownid,obj){
			var me = this,
				value = obj.value;
			if (value == 'seq') {
				jQuery('#tablefields_seq_'+fieldname).show().focus();
				jQuery('#tablefields_seq_btn_'+fieldname).show();
			} else {
				jQuery('#tablefields_seq_'+fieldname).hide();
				jQuery('#tablefields_seq_btn_'+fieldname).hide();
				if (value != '') me.insertTableFieldValue(mode,target,fieldname,dropdownid,value);
			}
		},
		insertTableFieldValue: function(mode,target,fieldname,dropdownid,value){
			var me = this,
				parent_value = jQuery('#'+dropdownid).val();
			if (value == 'seq') {
				var sequence = parseInt(jQuery('#tablefields_seq_'+fieldname).val());
				if (isNaN(sequence) || sequence <= 0) return false;
				else value += ':'+sequence;
			}
			me.insertAtCursor(mode,target,parent_value+':'+value);
		},
		insertAtCursor: function(mode,target,value) {
			if (mode == 'append_textarea') {
				target.insertHtml(value);
			} else if (mode == 'append_input_space') {
				target.val(target.val()+' '+value);
			} else if (mode == 'append_input_comma') {
				var oldvalue = target.val().trim();
				target.val((oldvalue ? oldvalue+',' : '')+value);
			} else if (mode == 'overwrite_input') {
				target.val(value);
			}
		}
		//crmv@106857e
	}
}

if (typeof(ProcessHelperScript) == 'undefined') {
	ProcessHelperScript = {
		initPopulateField: function(involved_records,dynaform_options,elements_actors,form_data) {
			involved_records = JSON.parse(involved_records);
			dynaform_options = JSON.parse(dynaform_options);
			elements_actors = JSON.parse(elements_actors);	//crmv@100591
			if (typeof(form_data) != 'undefined') form_data = JSON.parse(form_data); else var form_data = {};
			var vtinst = new VtigerWebservices("webservice.php",undefined,undefined,true)
			var i = 0;
			jQuery.fancybox.showLoading();
			vtinst.extendSession(handleError(function(result){
				vtinst.listTypes(handleError(function(accessibleModules) {
					accessibleModulesInfo = accessibleModules;
					if (involved_records == null) return false;	// check if there are involved records
					jQuery.each(involved_records,function(key,involved_record){
						var moduleName = involved_record.module;
						if (moduleName == '' || moduleName == null) {	// check if there are involved records
							i++;
							return;
						}
						getDescribeObjects(vtinst, accessibleModules, moduleName, handleError(function(modules){
							i++;
							fillSelectBox('task-fieldnames', modules, moduleName, involved_record);
							fillSelectBox('task-smownerfieldnames', modules, moduleName, involved_record, function(e){return (e['type']['name']=='reference' && e['type']['refersTo'][0]=='Users');});
							// last
							if (i == jQuery(involved_records).length) {
								appendDynaformOptions(jQuery('#task-fieldnames'),dynaform_options,'all');
								//crmv@100591
								if (jQuery(elements_actors).length > 0 && !checkSelectBoxDuplicates(jQuery('#task-fieldnames'),alert_arr.LBL_PM_ELEMENTS_ACTORS)) {
									var append = '<optgroup label="'+alert_arr.LBL_PM_ELEMENTS_ACTORS+'">';
									jQuery.each(elements_actors, function(fieldvalue, fieldlabel){
										append += '<option value="'+fieldvalue+'">'+fieldlabel+'</value>';
									});
									append += '</optgroup>';
									jQuery('#task-fieldnames').append(append);
								}
								//crmv@100591e
								//crmv@109685
								jQuery('#editForm .editoptions').each(function(){
									jQuery(this).html('<select class="populateFieldGroup"></select><select style="display:none" class="populateField" onchange="ActionUpdateScript.populateField(this)">'+jQuery('#task-fieldnames').html()+'</select>');	//crmv@112299
								});
								//crmv@109685e
								// owner
								appendDynaformOptions(jQuery('#task-smownerfieldnames'),dynaform_options,'user');
								//crmv@100591
								if (jQuery(elements_actors).length > 0 && !checkSelectBoxDuplicates(jQuery('#task-smownerfieldnames'),alert_arr.LBL_PM_ELEMENTS_ACTORS)) {
									var append = '<optgroup label="'+alert_arr.LBL_PM_ELEMENTS_ACTORS+'">';
									jQuery.each(elements_actors, function(fieldvalue, fieldlabel){
										append += '<option value="'+fieldvalue+'">'+fieldlabel+'</value>';
									});
									append += '</optgroup>';
									jQuery('#task-smownerfieldnames').append(append);
								}
								//crmv@100591e
								jQuery('#other_assigned_user_id').append(jQuery('#task-smownerfieldnames').html());
								if (jQuery('#assigned_user_id_type').val() == 'O' && form_data['assigned_user_id'] != undefined) {
									jQuery('#other_assigned_user_id').val(form_data['assigned_user_id']);
								}
								//crmv@106856
								if (form_data['assigned_user_id'] == 'advanced_field_assignment') {
									jQuery('#advanced_field_assignment_button_assigned_user_id').show();
									jQuery('#other_assigned_user_id').width(jQuery('#other_assigned_user_id').width()-35);
								}
								jQuery('#other_assigned_user_id').change(function(){
									if (jQuery(this).val() == 'advanced_field_assignment') {
										jQuery('#advanced_field_assignment_button_assigned_user_id').show();
										jQuery('#other_assigned_user_id').width(jQuery('#other_assigned_user_id').width()-35);
									} else {
										jQuery('#advanced_field_assignment_button_assigned_user_id').hide();
										jQuery('#other_assigned_user_id').width('100%');
									}
									ActionTaskScript.showSdkParamsInput(this,'assigned_user_id');	//crmv@113527
								});
								if (jQuery('#other_assigned_user_id').length > 0) ActionTaskScript.showSdkParamsInput(jQuery('#other_assigned_user_id'),'assigned_user_id');	//crmv@113527
								//crmv@106856e
								filterPopulateField();	//crmv@112299
								jQuery.fancybox.hideLoading();
							}
						}));
					});
				}));
			}));
		},
		loadPopulateField: function(fieldinfo) {
			// TODO load the correct interface using uitype of the field "fieldprop_default" and populateFieldOptions with the relative field
			jQuery('#fieldprop_default').val(fieldinfo['default']);
			jQuery('#defaultValueContainer').html('<select class="populateFieldGroup"></select><select style="display:none" class="populateField" id="populateFieldOptions" onchange="ProcessHelperScript.populateField()">'+jQuery('#task-fieldnames').html()+'</select>');
			filterPopulateField();	//crmv@112299
		},
		populateField: function(){
			var tagField = jQuery('#populateFieldOptions');
			var field = jQuery('#fieldprop_default');
			var value = jQuery(tagField).val();
			//crmv@112299
			if (value == 'back') {
				restorePopulateFieldGroup(tagField);
			//crmv@112299e
			} else if (value != '') insertAtCursor(field.get(0), value);
		},
		openImportDynaformBlocks: function(){
			var processid = jQuery('.form-helper-shape #processid').val();
			var id = jQuery('.form-helper-shape #elementid').val();
			var mmaker = {};
			jQuery.each(jQuery('#module_maker_form').serializeArray(), function(){
				mmaker[this.name] = this.value;
			});
			openPopup('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=openimportdynaformblocks&id='+processid+'&elementid='+id+'&mmaker='+encodeURI(JSON.stringify(mmaker)));
		},
		checkAllDynaformBlocks: function(elementid,checked){
			jQuery('[id^="import_'+elementid+'"]').prop('checked',checked);
		},
		importDynaformBlocks: function(processmakerid,elementid){
			var dynaformblocks = [];
			jQuery.each(jQuery('[id^="import_"]').serializeArray(), function(){
				dynaformblocks.push(this.value);
			});
			jQuery.ajax({
				'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=importdynaformblocks&id='+processmakerid+'&elementid='+elementid,
				'type': 'POST',
				'data': jQuery.param({'dynaformblocks':dynaformblocks,'mmaker':jQuery('#mmaker').html()}),
				success: function(data) {
					if (data != '') parent.jQuery('#mmaker_div_allblocks').html(data);
					closePopup();
				}
			});
		}
	}
}

//crmv@112299
function filterPopulateField() {
	jQuery('.populateFieldGroup').each(function(){
		var obj = this,
			str = '';
		str += '<option value="">'+alert_arr.LBL_SELECT_OPTION_DOTDOTDOT+'</option>';
		if (jQuery(obj).next('.populateField').length > 0) {
			var populateField = jQuery(obj).next('.populateField');
			populateField.find('optgroup').each(function(){
				str += '<option value="'+this.label+'">'+this.label+'</option>';
			});
			jQuery(obj).html(str).change(function(){
				jQuery(obj).hide();
				populateField.find('optgroup').hide();
				populateField.find('optgroup[label="'+this.value+'"]').show();
				jQuery(populateField).show();
				populateField.val(populateField.find("option:first").val());
			});
		}
	});
}

function restorePopulateFieldGroup(tagField) {
	jQuery(tagField).hide();
	var populateFieldGroup = jQuery(tagField).prev('.populateFieldGroup');
	populateFieldGroup.show();
	populateFieldGroup.val(populateFieldGroup.find("option:first").val());
}
//crmv@112299e

function NewTaskPopup($,context){
	function close(){
		$('#new_task_div',context).css('display', 'none');
	}
	function show(module){
		$('#new_task_div',context).css('display', 'block');
	}
	$('#new_task_div_close',context).click(close);
	$('#new_task_div_cancel',context).click(close);
	return {
		close:close,show:show
	};
}

function insertAtCursor(element, value){
	//http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
	if (document.selection) {
		element.focus();
		var sel = document.selection.createRange();
		sel.text = value;
		element.focus();
	}else if (element.selectionStart || element.selectionStart == '0') {
		var startPos = element.selectionStart;
		var endPos = element.selectionEnd;
		var scrollTop = element.scrollTop;
		element.value = element.value.substring(0, startPos)
			+ value
			+ element.value.substring(endPos,
			element.value.length);
		element.focus();
		element.selectionStart = startPos + value.length;
		element.selectionEnd = startPos + value.length;
		element.scrollTop = scrollTop;
	}	else {
		element.value += value;
		element.focus();
	}
}

function getDescribeObjects(vtinst, accessibleModules, moduleName, callback){
	vtinst.describeObject(moduleName, handleError(function(result){
		var parent = referencify(result);
		var fields = parent['fields'];
		var referenceFields = filter(function(e){return e['type']['name']=='reference';}, fields);
		var referenceFieldModules =
			map(function(e){ return e['type']['refersTo'];},
				referenceFields
			);
		function union(a, b){
			var newfields = filter(function(e){return !contains(a, e);}, b);
			return a.concat(newfields);
		}
		var relatedModules = reduceR(union, referenceFieldModules, []);	//skip duplicate call
		if (relatedModules.length == 0) relatedModules = [moduleName];	//crmv@113775 force module even if there aren't fields in order to prevent error
		if (!(moduleName in ActionTaskScript.describe_object_cache)) ActionTaskScript.describe_object_cache[moduleName] = result;
		
		// Remove modules that is no longer accessible
		relatedModules = diff(accessibleModules, relatedModules);
		
		function executer(parameters){
			var failures = filter(function(e){return e[0]==false;}, parameters);
			if(failures.length!=0){
				var firstFailure = failures[0];
				callback(false, firstFailure[1]);
			}else{
				var moduleDescriptions = map(function(e){return e[1];}, parameters);
				var modules = dict(map(function(e){return [e['name'], referencify(e)];}, moduleDescriptions));
				modules[moduleName] = ActionTaskScript.describe_object_cache[moduleName];	//skip duplicate call
				jQuery.each(modules, function(k,v){
					if (!(k in ActionTaskScript.describe_object_cache)) ActionTaskScript.describe_object_cache[k] = v;
				});
				callback(true, modules);
			}
		}
		var p = parallelExecuter(executer, relatedModules.length);
		jQuery.each(relatedModules, function(i, v){
			if (!(v in ActionTaskScript.describe_object_cache)) {
				//console.log('richiesta',v);
				p(function(callback){
					vtinst.describeObject(v,callback);
				});
			} else {
				//console.log('cache',v);
				p(function(callback){
					callback(true,ActionTaskScript.describe_object_cache[v]);
				});
			}
		});
	}));
}

function checkSelectBoxDuplicates(field, label) {
	var optgroups = jQuery(field).find('optgroup');
	var check_duplicate = function(){
		var check = false;
		jQuery(field).find('optgroup').each(function(){
			if (this.label == label) {
				check = true;
				return true;
			}
		});
		return check;
	}();
	return check_duplicate;
}

function fillSelectBox(id, modules, parentModule, involved_record, filterPred){
	if(filterPred==null){
		filterPred = function(){
			return true;
		};
	}
	var select = jQuery('#'+id);
	if (select.length == 0) select = jQuery('[name="'+id+'"]');
	
	if (checkSelectBoxDuplicates(select, involved_record.label)) return true;
	
	var parent = modules[parentModule];
	var fields = parent['fields'];

	function filteredFields(fields){
		return filter(
			function(e){
				var fieldCheck = !contains(['autogenerated', 'owner', 'multipicklist', 'password'], e.type.name);	//reference
				var predCheck = filterPred(e);
				return fieldCheck && predCheck;
			},
			fields
		);
	}
	var parentFields = map(function(e){return[e['name'],e['label']];}, filteredFields(parent['fields']));

	var referenceFieldTypes = filter(function(e){
			return (e['type']['name']=='reference');
		},parent['fields']
	);
		
	var moduleFieldTypes = {};
	jQuery.each(modules, function(k, v){
			moduleFieldTypes[k] = dict(map(function(e){return [e['name'], e['type']];},filteredFields(v['fields'])));
		}
	);

	function getFieldType(fullFieldName){
		var group = fullFieldName.match(/(\w+) : \((\w+)\) (\w+)/);
		if(group==null){
			var fieldModule = parentModule;
			var fieldName = fullFieldName;
			}else{
				var fieldModule = group[2];
			var fieldName = group[3];
		}
		return moduleFieldTypes[fieldModule][fieldName];
	}

	function fieldReferenceNames(referenceField){
		var name = referenceField['name'];
		var label = referenceField['label'];
		function forModule(parentModule){
			// If module is not accessible return no field information
			if(!contains(accessibleModulesInfo, parentModule)) return [];
			
			return map(function(field){					
				return ['('+name+' : '+'('+parentModule+') '+field['name']+')',label+' : '+'('+modules[parentModule]['label']+') '+field['label']]; //crmv@42329
				},
				filteredFields(modules[parentModule]['fields']));
		}
		return reduceR(concat,map(forModule,referenceField['type']['refersTo']),[]);
	}
	//crmv@36510
	if (id == 'task-emailfields_sender'){
		var accessibleModulesInfo_backup = accessibleModulesInfo;
		accessibleModulesInfo = ['Users'];
	}
	var referenceFields = reduceR(concat,map(fieldReferenceNames,referenceFieldTypes), []);
	if (id == 'task-emailfields_sender'){
		accessibleModulesInfo = accessibleModulesInfo_backup;
	}
	//crmv@36510 e
	var referenceFields = reduceR(concat,map(fieldReferenceNames,referenceFieldTypes), []);
	var fieldLabels = dict(parentFields.concat(referenceFields));
	var optionClass = id+'_option';
	var append = '';
	append += '<optgroup label="'+involved_record.label+'">';
	if (typeof(involved_record.meta_processid) == 'undefined') var rk = involved_record.seq; else rk = involved_record.meta_processid+':'+involved_record.seq;
	if (id == 'task-fieldnames' || id == 'task-referencefieldnames' || id == 'task-subjectfields') {
		append += '<option class="'+optionClass+'" '+ 'value="$'+rk+'-crmid">ID</option>';
	}
	jQuery.each(fieldLabels, function(k, v){
		append += '<option class="'+optionClass+'" '+ 'value="$'+rk+'-'+k+'">' + v + '</option>';
	});
	append += '</optgroup>';
	select.append(append);
}

function appendDynaformOptions(field,options,type) {
	var string = '';
	if (typeof(options[type]) == "object") {
		jQuery.each(options[type], function(grouplabel, fields){
			if (checkSelectBoxDuplicates(field, grouplabel)) return true; // continue
			string += '<optgroup label="'+grouplabel+'">';
			jQuery.each(fields, function(fieldvalue, fieldlabel){
				string += '<option value="'+fieldvalue+'">'+fieldlabel+'</value>';
			});
			string += '</optgroup>';
		});
	}
	if (string != '') jQuery(field).append(string);
}

function id(v){
	return v;
}

function map(fn, list){
	var out = [];
	jQuery.each(list, function(i, v){
		out[out.length]=fn(v);
	});
	return out;
}

function field(name){
	return function(object){
		if(typeof(object) != 'undefined') {
			return object[name];
		}
	};
}

function zip(){
	var out = [];

	var lengths = map(field('length'), arguments);
	var min = reduceR(function(a,b){return a<b?a:b;},lengths,lengths[0]);
	for(var i=0; i<min; i++){
		out[i]=map(field(i), arguments);
	}
	return out;
}

function dict(list){
	var out = {};
	jQuery.each(list, function(i, v){
		out[v[0]] = v[1];
	});
	return out;
}

function filter(pred, list){
	var out = [];
	jQuery.each(list, function(i, v){
		if(pred(v)){
			out[out.length]=v;
		}
	});
	return out;
}

function diff(reflist, list) {
	var out = [];
	jQuery.each(list, function(i, v) {
		if(contains(reflist, v)) {
			out.push(v);
		}
	});
	return out;
}


function reduceR(fn, list, start){
	var acc = start;
	jQuery.each(list, function(i, v){
		acc = fn(acc, v);
	});
	return acc;
}

function contains(list, value){
	var ans = false;
	jQuery.each(list, function(i, v){
		if(v==value){
			ans = true;
			return false;
		}
	});
	return ans;
}

function concat(lista,listb){
	return lista.concat(listb);
}

function errorDialog(message){
	alert(message);
}

function handleError(fn){
	return function(status, result){
		if(status){
			fn(result);
		}else{
			errorDialog('Failure:'+result);
		}
	};
}

function implode(sep, arr){
	var out = "";
	jQuery.each(arr, function(i, v){
		out+=v;
		if(i<arr.length-1){
			out+=sep;
		}
	});
	return out;
}

function mergeObjects(obj1, obj2){
	var res = {};
	for(var k in obj1){
		res[k] = obj1[k];
	}
	for(var k in obj2){
		res[k] = obj2[k];
	}
	return res;
}

function referencify(desc){
  var fields = desc['fields'];
  for(var i=0; i<fields.length; i++){
	var field = fields[i];
	var type = field['type'];
	if(type['name']=='owner'){
	  type['name']='reference';
	  type['refersTo']=['Users'];
	}
  }
  return desc;
}