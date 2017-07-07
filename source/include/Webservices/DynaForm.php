<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@96450 crmv@104180 */

require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');

function dynaform_describe($processmakerid, $metaId, $options, $user){
	global $current_user;
	$current_user = vtws_preserveGlobal('current_user',$user);
	$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');

	$fields = array();
	$processDynaFormObj = ProcessDynaForm::getInstance();
	$blocks = $processDynaFormObj->getStructure($processmakerid, false, $metaId);
	if (!empty($blocks)) {
		foreach($blocks as $block) {
			foreach($block['fields'] as $field) {
				$newfield = array(
					"name"=>$field['fieldname'],	//TODO add $DFX-
					"label"=>$field['label'],
					"mandatory"=>($field['mandatory'] == 1)?true:false,
					"type"=>$processDynaFormObj->getFieldTypeDetails($field),
					"nullable"=>true,
					"editable"=>($field['mandatory'] == 1)?true:false,
					"fieldid"=>$field['fieldname'],
					"uitype"=>$field['uitype'],
					"blockid"=>$block['blocklabel'],
					"sequence"=>false
				);
				if ($newfield['type']['name'] == 'table') {
					// add the columns
					$newfield['columns'] = Zend_Json::decode($field['columns']) ?: array();
					foreach ($newfield['columns'] as &$col) {
						$col['name'] = $col['fieldname'];
						//$col['mandatory'] = ($col['mandatory'] == 1)?true:false;
						$col['nullable'] = true;
						$col['editable'] = ($col['mandatory'] == 1)?true:false;
						$col['type'] = $processDynaFormObj->getFieldTypeDetails($col);
						$col['fieldid'] = $col['fieldname'];
						$col['blockid'] = $field['fieldname'];
						$col['sequence'] = false;
					}
				}
				$fields[] = $newfield;
			}
		}
	}
	$return = array(
		"label"=>(isset($app_strings['DynaForm'])) ? $app_strings['DynaForm'] : 'DynaForm',
		"name"=>'DynaForm',
		"createable"=>false,
		"updateable"=>false,
		"deleteable"=>false,
		"retrieveable"=>false,
		"fields"=>$fields,
		"idPrefix"=>false,
		'isEntity'=>false,
		'labelFields'=>false
	);
	return $return;
}