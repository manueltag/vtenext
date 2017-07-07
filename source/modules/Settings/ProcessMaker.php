<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@92272 crmv@97566 crmv@115268 crmv@128159 */

require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
require_once('Smarty_setup.php');

global $mod_strings, $app_strings, $theme;

$PMUtils = ProcessMakerUtils::getInstance();
$mode = $_REQUEST['mode'];
$sub_template = '';

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");
$smarty->assign("MODE", $mode);

switch($mode) {
	//crmv@100972
	case 'create':
		if (!empty($_REQUEST['err'])) {
			$smarty->assign("DATA", array('name'=>$_REQUEST['name'],'description'=>$_REQUEST['description']));
			$smarty->assign("ERROR", $_REQUEST['err']);
		}
		$sub_template = 'Settings/ProcessMaker/Create.tpl';
		break;
	case 'import':
		$err = '';
		$check = $PMUtils->checkUploadBPMN($err);
		if ($check) {
			//header("Location: index.php?module=Settings&action=ProcessMaker&parenttab=Settings&mode=modeler&id=$id");
			$PMUtils->readUploadedBPMN($smarty);
			$buttons = '
			<div class="morphsuitlink" style="float:left; height:34px; font-size:14px; padding-top:7px; padding-left:10px">
				'.$mod_strings['LBL_SETTINGS'].'</a> &gt; '.$mod_strings['LBL_PROCESS_MAKER'].'
			</div>
			<div style="float:right; padding-right:5px;"><img id="logo" src="'.get_logo('header').'" alt="{$APP.LBL_BROWSER_TITLE}" title="'.$app_strings['LBL_BROWSER_TITLE'].'" border=0 style="padding:4px 0px; height:30px"></div>
			<div style="float:right; padding-right:5px">
				<input type="button" style="background-color:white" id="save-button" class="crmbutton small edit" value="'.$app_strings['LBL_SAVE_LABEL'].'" title="'.$app_strings['LBL_SAVE_LABEL'].'"></input>
				<input type="button" style="background-color:white" onclick="window.location.href=\'index.php?module=Settings&action=ProcessMaker\'" class="crmbutton small edit" value="'.$mod_strings['LBL_CANCEL_BUTTON'].'" title="'.$mod_strings['LBL_CANCEL_BUTTON'].'"></input>
			</div>';
			$smarty->assign("BUTTON_LIST", $buttons);
			$smarty->display('Settings/ProcessMaker/Modeler.tpl');
			exit;
		} else {
			header("Location: index.php?module=Settings&action=ProcessMaker&parenttab=Settings&mode=create&name={$_REQUEST['name']}&description={$_REQUEST['description']}&err={$err}");
			exit;
		}
		break;
	case 'detail':
		global $default_charset;
		$id = vtlib_purify($_REQUEST['id']);
		$data = $PMUtils->retrieve($id);
		$smarty->assign("DATA", $data);
		$smarty->assign("TABLE_NAME", $PMUtils->table_name);
		$smarty->assign("default_charset", $default_charset);
		
		include_once('vtlib/Vtecrm/Link.php');
		$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('HEADERSCRIPT'));
		$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
		
		$buttons = '
		<div class="morphsuitlink" style="float:left; height:34px; font-size:14px; padding-top:7px; padding-left:10px">
			'.$mod_strings['LBL_SETTINGS'].'</a> &gt; '.$mod_strings['LBL_PROCESS_MAKER'].'
		</div>
		<div style="float:right; padding-right:5px;"><img id="logo" src="'.get_logo('header').'" alt="{$APP.LBL_BROWSER_TITLE}" title="'.$app_strings['LBL_BROWSER_TITLE'].'" border=0 style="padding:4px 0px; height:30px"></div>
		<div style="float:right; padding-right:5px">
			<div id="status" style="display:none;"><i class="dataloader" data-loader="circle"></i></div>
			<input type="button" style="background-color:white" onclick="ProcessMakerScript.modeler('.$id.')" class="crmbutton small edit" value="'.$app_strings['LBL_EDIT'].' '.$mod_strings['LBL_PM_MODELER'].'" title="'.$app_strings['LBL_EDIT'].' '.$mod_strings['LBL_PM_MODELER'].'">
			<input type="button" style="background-color:white" onclick="ProcessMakerScript.backToList(\''.$app_strings['Active'].'\')" class="crmbutton small edit" value="'.$app_strings['LBL_BACK'].'" title="'.$app_strings['LBL_BACK'].'">
		</div>';
		//		{* TODO <input type="button" onclick="ProcessMakerScript.manageOtherRecords({$DATA.id})" class="crmbutton small edit" value='{$MOD.LBL_PROCESS_MAKER_MANAGE_OTHER_RECORD}' title='{$MOD.LBL_PROCESS_MAKER_MANAGE_OTHER_RECORD}'> *}
		$smarty->assign("BUTTON_LIST", $buttons);
		
		$smarty->display('Settings/ProcessMaker/Detail.tpl');
		exit;
		break;
	case 'modeler':
		$id = vtlib_purify($_REQUEST['id']);
		$smarty->assign("PROCESSMAKERID", $id);
		$buttons = '
		<div class="morphsuitlink" style="float:left; height:34px; font-size:14px; padding-top:7px; padding-left:10px">
			'.$mod_strings['LBL_SETTINGS'].'</a> &gt; '.$mod_strings['LBL_PROCESS_MAKER'].'
		</div>
		<div style="float:right; padding-right:5px;"><img id="logo" src="'.get_logo('header').'" alt="{$APP.LBL_BROWSER_TITLE}" title="'.$app_strings['LBL_BROWSER_TITLE'].'" border=0 style="padding:4px 0px; height:30px"></div>
		<div style="float:right; padding-right:5px">
			<input type="button" style="background-color:white" id="save-button" class="crmbutton small edit" value="'.$app_strings['LBL_SAVE_LABEL'].'" title="'.$app_strings['LBL_SAVE_LABEL'].'"></input>
			<input type="button" style="background-color:white" onclick="history.back()" class="crmbutton small edit" value="'.$mod_strings['LBL_CANCEL_BUTTON'].'" title="'.$mod_strings['LBL_CANCEL_BUTTON'].'"></input>
		</div>';
		$smarty->assign("BUTTON_LIST", $buttons);
		$smarty->display('Settings/ProcessMaker/Modeler.tpl');
		exit;
		break;
	case 'save_model':
		global $default_charset, $current_user;
		$id = vtlib_purify($_REQUEST['id']);
		$xml = $_REQUEST['xml'];
		if (empty($id)) {	// new
			$values = Zend_Json::decode($_REQUEST['values']);
			$name = $values['name'];
			$description = $values['description'];
			$vte_metadata = null;
			$structure = null;
			$helper = null;
			$metarec = null;
			$dynameta = null;
			$file = $values['file'];
			if (stripos($file,'<vtebpmn>') !== false) {
				$xmlObj = new SimpleXMLElement($file);
				//$xml = base64_decode($xmlObj->bpmn);
				$vte_metadata = base64_decode($xmlObj->vte_metadata);
				$structure = base64_decode($xmlObj->structure);
				$helper = base64_decode($xmlObj->helper);
				if (!empty($xmlObj->metarec)) $metarec = Zend_Json::decode(base64_decode($xmlObj->metarec));
				if (!empty($xmlObj->dynameta)) $dynameta = Zend_Json::decode(base64_decode($xmlObj->dynameta));
			}
			$id = $PMUtils->create($name,$description,$xml,$vte_metadata,$structure,$helper,$metarec,$dynameta);
		} else {	// update model
			//crmv@101057 versioning
			$result = $adb->pquery("select * from {$PMUtils->table_name} where id = ?", array($id));
			$version = $adb->query_result($result,0,'xml_version');
			$xml_old = $adb->query_result_no_html($result,0,'xml');
			$vte_metadata = $adb->query_result_no_html($result,0,'vte_metadata');
			$structure = $adb->query_result_no_html($result,0,'structure');
			$helper = $adb->query_result_no_html($result,0,'helper');
			$adb->pquery("insert into {$table_prefix}_processmaker_versions(processmakerid,xml_version,userid,date_version,vte_metadata,structure,helper) values(?,?,?,?,?,?,?)",
				array($id,$version,$current_user->id,date('Y-m-d H:i:s'),$vte_metadata,$structure,$helper));
			$adb->updateClob("{$table_prefix}_processmaker_versions",'xml',"processmakerid=$id and xml_version=$version",$xml_old);
			// save new
			$adb->pquery("update {$PMUtils->table_name} set xml=?, structure=null, xml_version=? where id=?", array($xml, $version+1, $id));
			//crmv@101057e
		}
		echo $id;
		exit;
		break;
	//crmv@100972e
	case 'download':
		function array_to_xml( $data, &$xml_data ) {
		    foreach( $data as $key => $value ) {
		        if(is_array($value)) {
			        if (is_numeric($key)) $key = 'arrayitem'.$key; //dealing with <0/>..<n/> issues
		            $subnode = $xml_data->addChild($key);
		            array_to_xml($value, $subnode);
		        } else {
		        	//$xml_data->addChild("$key",htmlspecialchars("$value"));
		            $xml_data->addChild($key,$value);
		        }
		     }
		}
		$id = vtlib_purify($_REQUEST['id']);
		$format = vtlib_purify($_REQUEST['format']);
		$data = $PMUtils->retrieve($id);
		if ($format == 'bpmn') {
			$filename = $data['name'].'.bpmn';
			$fileContent = $data['xml'];
		} elseif ($format == 'vtebpmn') {
			$filename = $data['name'].'.vtebpmn';
			$xml = new SimpleXMLElement('<vtebpmn/>');
			$xml->addChild('name',$data['name']);
			$xml->addChild('description',$data['description']);
			$xml->addChild('bpmn',base64_encode($data['xml']));
			$PMUtils->unsetSubProcesses($id,$data['vte_metadata']);	//crmv@97575 unset subprocesses
			$xml->addChild('vte_metadata',base64_encode($data['vte_metadata']));
			$xml->addChild('structure',base64_encode($data['structure']));
			$xml->addChild('helper',base64_encode($data['helper']));
			
			$metarec = array();
			$result = $adb->pquery("select * from {$table_prefix}_processmaker_metarec where processid = ?", array($id));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$metarec[] = $row;
				}
			}
			$xml->addChild('metarec',base64_encode(Zend_Json::encode($metarec)));
			
			$dynameta = array();
			$result = $adb->pquery("select * from {$table_prefix}_process_dynaform_meta where processid = ?", array($id));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$dynameta[] = $row;
				}
			}
			$xml->addChild('dynameta',base64_encode(Zend_Json::encode($dynameta)));
			
			$fileContent = $xml->asXML();
		}
		$fileType = 'application/octet-stream';
		function_exists('mb_strlen') ? $filesize = mb_strlen($fileContent, '8bit') : $filesize = strlen($fileContent);
		
		header("Content-type: $fileType");
		header("Content-length: $filesize");
		header("Cache-Control: private");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Content-Description: PHP Generated Data");
		echo $fileContent; exit;
		break;
	case 'load_metadata':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = $_REQUEST['elementid'];
		$req_structure = Zend_Json::decode($_REQUEST['structure']);
		
		$data = $PMUtils->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		$vte_metadata_arr = $vte_metadata[$elementid];
		$helper = Zend_Json::decode($data['helper']);
		$helper_arr = $helper[$elementid];
		if (empty($helper_arr)) $helper_arr['active'] = 'on';	// default helper active
		$structure = Zend_Json::decode($data['structure']);
		
		if (!isset($req_structure['text']) || !isset($req_structure['type'])) {
			$req_structure = $structure['shapes'][$elementid];
		}
		$type = $PMUtils->formatType($req_structure['type']);
		$subType = $PMUtils->formatType($req_structure['subType']);
		$type_map = $PMUtils->getMetadataTypes($type,$req_structure);
		$type_tpl = $type_map['tpl'];
		if (empty($type_tpl)) {
			$error = $type;
			if (!empty($subType)) $error .= "($subType)";
			$error .= ' not implemented';
			die($error);
		}
		$engineType = $PMUtils->getEngineType($req_structure);
		$title = $PMUtils->getElementTitle($req_structure);
		$smarty->assign("PAGE_TITLE", $title);
		$smarty->assign("PAGE_RIGHT_TITLE", $elementid);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$smarty->assign("ID", $elementid);
		$smarty->assign("PROCESSID", $id);
		$buttons['save'] = '<input type="button" onclick="ProcessMakerScript.saveMetadata(\''.$id.'\',\''.$elementid.'\',\''.$engineType.'\')" class="crmbutton small save" value="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'" title="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'">';
		$buttons['cancel'] = '<input type="button" onclick="ProcessMakerScript.closeMetadata(\''.$elementid.'\');" class="crmbutton small cancel" value="'.$mod_strings['LBL_CANCEL_BUTTON'].'" title="'.$mod_strings['LBL_CANCEL_BUTTON'].'">';
		//crmv@99316
		if ($PMUtils->todoFunctions && $engineType == 'Action') {
			$buttons['advanced'] = '<input type="button" onclick="ProcessMakerScript.advancedMetadataSettings(\''.$id.'\',\''.$elementid.'\',true);" class="crmbutton small" value="'.$mod_strings['LBL_PM_ADVANCED_ACTIONS'].'..." title="'.$mod_strings['LBL_PM_ADVANCED_ACTIONS'].'...">';
		}
		//crmv@99316e
		
		if (isset($type_map['php'])) include($type_map['php']);

		$smarty->assign("METADATA", $vte_metadata_arr);
		$smarty->assign("HELPER", $helper_arr);
		$smarty->assign('ADVANCED_FIELD_ASSIGNMENT',$PMUtils->todoFunctions);	//crmv@106856
		
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					'.implode('',$buttons).'					
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		
		//crmv@96450 retrieve dynaform
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
		$smarty->assign("STEPVARS", $helper_arr['dynaform']);
		$smarty->assign("NEWFIELDS", $MMSteps->getNewFields());
		$smarty->assign("NEWTABLEFIELDCOLUMNS", $MMSteps->getNewTableFieldColumns()); // crmv@102879
		$smarty->assign("PROCESSMAKERMODE", true);
		$smarty->assign("USETABLEFIELDS", $PMUtils->todoFunctions); // crmv@102879
		//crmv@96450e
		
		include_once('vtlib/Vtecrm/Link.php');
		$COMMONHDRLINKS = Vtiger_Link::getAllByType(Vtiger_Link::IGNORE_MODULE, Array('HEADERSCRIPT'));
		$smarty->assign('HEADERSCRIPTS', $COMMONHDRLINKS['HEADERSCRIPT']);
		$smarty->assign('HEAD_INCLUDE',"icons,jquery,jquery_plugins,jquery_ui,fancybox,prototype,jscalendar,sdk_headers");
		
		$smarty->display($type_tpl);
		exit;
		break;
	case 'savemetadata':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		//crmv@96450
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
		$dynaform = Zend_Json::decode($_REQUEST['mmaker']);
		$MMSteps->preprocessStepVars('ajax', 2, 0, $dynaform);
		$dynaform = $MMSteps->extractStepVars($dynaform);
		//crmv@96450e
		$PMUtils->saveMetadata($id,$elementid,vtlib_purify($_REQUEST['vte_metadata']),vtlib_purify($_REQUEST['helper']),$dynaform);	//crmv@96450
		echo 'SUCCESS'; exit;
		break;
	case 'save_structure':
		$id = vtlib_purify($_REQUEST['id']);
		$PMUtils->saveStructure($id,vtlib_purify($_REQUEST['structure']));
		echo 'SUCCESS'; exit;
		break;
	case 'editaction':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$action_type = $_REQUEST['action_type'];
		$action_id = $_REQUEST['action_id'];
		// crmv@102879
		$action_options = array();
		// at the moment, only the cycle has options
		if ($action_type == 'Cycle') {
			$action_options['cycle_field'] = vtlib_purify($_REQUEST['cycle_field']);
			$action_options['cycle_action'] = vtlib_purify($_REQUEST['cycle_action']);
		}
		$PMUtils->actionEdit($id,$elementid,$action_type,$action_id, $action_options);
		// crmv@102879e
		exit;
		break;
	case 'saveaction':
		$result = $PMUtils->actionSave($_REQUEST);
		die($result);
		break;
	case 'deleteaction':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$action_id = $_REQUEST['action_id'];
		$result = $PMUtils->actionDelete($id,$elementid,$action_id);
		die($result);
		break;
	case 'manage_other_records':
		require_once('modules/Settings/ProcessMaker/ProcessMakerPopup.php');
		$popup = ProcessMakerPopup::getInstance();
		
		$smarty->assign("PAGE_TITLE", $mod_strings['LBL_PROCESS_MAKER_MANAGE_OTHER_RECORD']);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$smarty->assign('LINK_MODULES', $popup->getModules());
		
		// TODO extraInputs per gestire funzione ritorno list, togliere checkbox e tasto Aggiungi oppure usarli meglio
		// TODO blocco sotto stile wizard
		
		$smarty->display('Settings/ProcessMaker/ManageOtherRecords.tpl');
		exit;
		break;
	case 'recurrence_preview':
		$vte_metadata = Zend_Json::decode($_REQUEST['vte_metadata']);
		$preview = $PMUtils->previewTimerStart($vte_metadata);
		$smarty->assign("PREVIEWS", $preview);
		$smarty->display('Settings/ProcessMaker/Metadata/TimerStartPreviewRecurrences.tpl');
		exit;
		break;
	case 'checktimerstart':
		if (isset($_REQUEST['vte_metadata'])) {
			$vte_metadata = Zend_Json::decode($_REQUEST['vte_metadata']);
		} elseif (!empty($_REQUEST['id'])) {
			$processmakerid = vtlib_purify($_REQUEST['id']);
			$startElementid = '';
			$isTimerProcess = $PMUtils->isTimerProcess($processmakerid,$startElementid);
			if ($isTimerProcess) {
				$data = $PMUtils->retrieve($processmakerid);
				$vte_metadata = Zend_Json::decode($data['vte_metadata']);
				$vte_metadata = $vte_metadata[$startElementid];
			} else {
				exit;
			}
		}
		$date_start = getValidDBInsertDateValue($vte_metadata['date_start']).' '.$vte_metadata['starthr'].':'.$vte_metadata['startmin'];
		($vte_metadata['date_end_mass_edit_check'] == 'on') ? $date_end = getValidDBInsertDateValue($vte_metadata['date_end']).' '.$vte_metadata['endhr'].':'.$vte_metadata['endmin'] : $date_end = false;
		if (strtotime($date_start) < time()) {
			echo getTranslatedString('LBL_PM_CHECK_TIMER_START_DATE','Settings');
		} elseif (!empty($date_end) && strtotime($date_start) > strtotime($date_end)) {
			echo getTranslatedString('LBL_PM_CHECK_TIMER_START_GREATER_THAN_END','Settings');
		}
		exit;
		break;
	//crmv@96450 retrieve dynaform
	case 'openimportdynaformblocks':
		$processmakerid = vtlib_purify($_REQUEST['id']);
		$elementid = $_REQUEST['elementid'];
		$mmaker = $_REQUEST['mmaker'];

		$data = $PMUtils->retrieve($processmakerid);
		$helper = Zend_Json::decode($data['helper']);
		$structure = Zend_Json::decode($data['structure']);
		
		require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
		$MMUtils = new ModuleMakerUtils();
		$MMSteps = new ProcessModuleMakerSteps($MMUtils);
		$titles = array();
		$stepvars = array();
		if (!empty($helper)) {
			unset($helper[$elementid]);
			foreach($helper as $dyna_elementid => $h) {
				if (!empty($h['dynaform']['mmaker_blocks'])) {
					$titles[$dyna_elementid] = $PMUtils->getElementTitle($structure['shapes'][$dyna_elementid]);
					$stepvars[$dyna_elementid] = $h['dynaform'];
				}
			}
		}
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					<input type="button" onclick="ProcessHelperScript.importDynaformBlocks(\''.$processmakerid.'\',\''.$elementid.'\')" class="crmbutton small save" value="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'" title="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'">
					<input type="button" onclick="closePopup();" class="crmbutton small cancel" value="'.$mod_strings['LBL_CANCEL_BUTTON'].'" title="'.$mod_strings['LBL_CANCEL_BUTTON'].'">
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		$smarty->assign("PAGE_TITLE", $mod_strings['LBL_PM_IMPORT_BLOCKS_TITLE']);
		$smarty->assign("PAGE_RIGHT_TITLE", $elementid);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$smarty->assign("TITLES", $titles);
		$smarty->assign("STEPVARS_ARR", $stepvars);
		$smarty->assign("PROCESSMAKERMODE", true);
		$smarty->assign("MMAKER", $mmaker);
		
		$smarty->display('Settings/ProcessMaker/Metadata/ImportDynaformBlocks.tpl');
		exit;
		break;
	case 'importdynaformblocks':
		$processmakerid = vtlib_purify($_REQUEST['id']);
		$elementid = $_REQUEST['elementid'];
		$dynaformblocks = $_REQUEST['dynaformblocks'];
		if (!empty($dynaformblocks)) {
			$data = $PMUtils->retrieve($processmakerid);
			$helper = Zend_Json::decode($data['helper']);
			
			require_once('modules/Settings/ModuleMaker/ModuleMakerUtils.php');
			$MMUtils = new ModuleMakerUtils();
			$MMSteps = new ProcessModuleMakerSteps($MMUtils);
			$dynaform = Zend_Json::decode($_REQUEST['mmaker']);
			$MMSteps->preprocessStepVars('ajax', 2, 0, $dynaform);
			$final_dynaform = $MMSteps->extractStepVars($dynaform);

			if (empty($final_dynaform['mmaker_lastfieldid'])) $final_dynaform['mmaker_lastfieldid'] = 0;
			foreach($dynaformblocks as $dynaformblock) {
				$dynaform_elementid = substr($dynaformblock,0,strrpos($dynaformblock,'_'));
				$blockno = substr($dynaformblock,strrpos($dynaformblock,'_')+1);
				$dynaform = $helper[$dynaform_elementid]['dynaform'];
				if (!empty($dynaform['mmaker_blocks'][$blockno])) {
					if (!empty($dynaform['mmaker_blocks'][$blockno]['fields'])) {
						foreach($dynaform['mmaker_blocks'][$blockno]['fields'] as &$field) {
							$final_dynaform['mmaker_lastfieldid']++;
							$field['fieldname'] = 'vcf_'.$final_dynaform['mmaker_lastfieldid'];
						}
					}
					$final_dynaform['mmaker_blocks'][] = $dynaform['mmaker_blocks'][$blockno];
				}
			}
			
			$smarty->assign("STEPVARS", $final_dynaform);
			$smarty->assign("NEWFIELDS", $MMSteps->getNewFields());
			$smarty->assign("PROCESSMAKERMODE", true);
			$smarty->display('Settings/ModuleMaker/Step2Fields.tpl');
		}
		exit;
		break;
	//crmv@96450e
	//crmv@99316 crmv@112297
	case 'advanced_metadata':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = $_REQUEST['elementid'];
		
		$data = $PMUtils->retrieve($id);
		$structure = Zend_Json::decode($data['structure']);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		$vte_metadata_arr = $vte_metadata[$elementid];

		$title = $PMUtils->getElementTitle($structure['shapes'][$elementid]);
		$smarty->assign("PAGE_TITLE", $title.': '.$mod_strings['LBL_PM_ADVANCED_ACTIONS']);
		$smarty->assign("PAGE_RIGHT_TITLE", $elementid);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$smarty->assign("ID", $elementid);
		$smarty->assign("PROCESSID", $id);
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					<input type="button" onclick="ProcessMakerScript.closeAdvMetadata(\''.$id.'\',\''.$elementid.'\');" class="crmbutton small" value="'.$app_strings['LBL_BACK'].'" title="'.$app_strings['LBL_BACK'].'">
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		$smarty->assign("METADATA", $vte_metadata_arr);
		
		//crmv@100731
		$smarty->assign("ADV_RECORD_INVOLVED", $PMUtils->getRecordsInvolvedOptions($id, ''));
		$_REQUEST['enable_editoptions'] = 'yes';
		//$smarty->assign("ADV_OTHER_ASSIGNED_TO", $PMUtils->getOwnerFieldOptions($id, '', false, true));
		$smarty->assign('ADV_ASSIGNEDTO', getOutputHtml(53,'assigned_user_id','LBL_ASSIGNED_TO',100,array(),1,'Settings','',1,'I~M'));
		$adv_permissions_list = $vte_metadata_arr['advanced_permissions'];
		if (!empty($adv_permissions_list)) {
			foreach($adv_permissions_list as &$ap) {
				$ap['record_involved_display'] = $PMUtils->getRecordsInvolvedLabel($id,substr($ap['record_involved'],0,strpos($ap['record_involved'],':')));
				$ap['resource_display'] = $PMUtils->getTranslatedProcessResource($id,$ap['resource']);
				if ($ap['permission'] == 'rw')
					$ap['permission_display'] = getTranslatedString('Read/Write','Settings');
				elseif ($ap['permission'] == 'ro')
					$ap['permission_display'] = getTranslatedString('Read Only ','Settings');
			}
		}
		$smarty->assign('ADV_PERMISSIONS_LIST',$adv_permissions_list);
		
		$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerFieldActions());
		
		$involvedRecords = $PMUtils->getRecordsInvolved($id,true);
		$smarty->assign('JSON_INVOLVED_RECORDS',Zend_Json::encode($involvedRecords));
		
		require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
		$processDynaFormObj = ProcessDynaForm::getInstance();
		$dynaFormOptions = $processDynaFormObj->getFieldsOptions($id,true);
		$smarty->assign('JSON_DYNAFORM_OPTIONS',Zend_Json::encode($dynaFormOptions));
		//crmv@100731e
		
		//crmv@100591
		$elementsActors = $PMUtils->getElementsActors($id);
		$smarty->assign('JSON_ELEMENTS_ACTORS',Zend_Json::encode($elementsActors));
		//crmv@100591e
		
		$smarty->display('Settings/ProcessMaker/Metadata/Advanced.tpl');
		exit;
		break;
	case 'edit_dynaform_conditional':
	case 'edit_conditional':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = $_REQUEST['elementid'];
		$ruleid = $_REQUEST['ruleid'];
		(empty($ruleid)) ? $mmode = '' : $mmode = 'edit';
		$smarty->assign("MMODE", $mmode);
		$smarty->assign("ID", $elementid);
		$smarty->assign("PROCESSID", $id);
		$smarty->assign("RULEID", $ruleid);
		
		$data = $PMUtils->retrieve($id);
		$structure = Zend_Json::decode($data['structure']);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		$vte_metadata_arr = $vte_metadata[$elementid];
		
		if ($mode == 'edit_dynaform_conditional') {
			$conditionals = $vte_metadata_arr['dfconditionals'];
			$save_function = 'saveDynaFormConditional';
			$close_function = 'closeDynaFormConditional';
			$smarty->assign("SAVE_MODE", 'save_dynaform_conditional');
		} else {
			$conditionals = $vte_metadata_arr['conditionals'];
			$save_function = 'saveConditional';
			$close_function = 'closeConditional';
			$smarty->assign("SAVE_MODE", 'save_conditional');
		}

		$title = $PMUtils->getElementTitle($structure['shapes'][$elementid]);
		($mmode == '') ? $title .= ' > '.getTranslatedString('LBL_CREATE_NEW_CONDITIONAL','Conditionals') : $title .= ' > '.getTranslatedString('LBL_EDIT');
		$smarty->assign("PAGE_TITLE", $title);
		$smarty->assign("PAGE_RIGHT_TITLE", $elementid);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					<input type="button" onclick="ProcessMakerScript.'.$save_function.'(\''.$id.'\',\''.$elementid.'\',\''.$ruleid.'\')" class="crmbutton small save" value="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'" title="'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'">
					<input type="button" onclick="ProcessMakerScript.'.$close_function.'(\''.$id.'\',\''.$elementid.'\',\''.$ruleid.'\');" class="crmbutton small cancel" value="'.$mod_strings['LBL_CANCEL_BUTTON'].'" title="'.$mod_strings['LBL_CANCEL_BUTTON'].'">
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		
		if ($mmode == 'edit') {
			$smarty->assign("TITLE", $conditionals[$ruleid]['title']);
			$smarty->assign("RULES", $conditionals[$ruleid]['rules']);
			$smarty->assign("CONDITIONS", Zend_Json::encode($conditionals[$ruleid]['conditions']));
			$role_grp_check = $conditionals[$ruleid]['role_grp_check'];
			$fpofv_saved = $conditionals[$ruleid]['fpofv'];
		}

		$roleDetails=getAllRoleDetails();
		unset($roleDetails['H1']);
		$grpDetails=getAllGroupName();
		$role_grp_check_picklist = '<select id="role_grp_check" name="role_grp_check" class="detailedViewTextBox">';
		($role_grp_check == 'ALL') ? $selected = "selected" : $selected = "";
		$role_grp_check_picklist .= '<option value="ALL" '.$selected.'>'.getTranslatedString('NO_CONDITIONS','Conditionals').'</option>';
		foreach($roleDetails as $roleid=>$rolename) {
			('roles::'.$roleid == $role_grp_check) ? $selected = "selected" : $selected = "";
			$role_grp_check_picklist .='<option value="roles::'.$roleid.'" '.$selected.'>'.getTranslatedString('LBL_ROLES','Conditionals').'::'.$rolename[0].'</option>';
		}
		foreach($roleDetails as $roleid=>$rolename) {
			('rs::'.$roleid == $role_grp_check) ? $selected = "selected" : $selected = "";
			$role_grp_check_picklist .='<option value="rs::'.$roleid.'" '.$selected.'>'.getTranslatedString('LBL_ROLES_SUBORDINATES','Conditionals').'::'.$rolename[0].'</option>';
		}
		foreach($grpDetails as $groupid=>$groupname) {
			('groups::'.$groupid == $role_grp_check) ? $selected = "selected" : $selected = "";		
			$role_grp_check_picklist .='<option value="groups::'.$groupid.'" '.$selected.'>'.getTranslatedString('LBL_GROUP','Conditionals').'::'.$groupname.'</option>';
		}
		$role_grp_check_picklist .= '</select>';
		$smarty->assign("ROLE_GRP_CHECK_PICKLIST",$role_grp_check_picklist);
		
		if ($mode == 'edit_dynaform_conditional') {
			$result = $adb->pquery("select id from {$table_prefix}_process_dynaform_meta where processid = ? and elementid = ?", array($id,$elementid));
			if ($result && $adb->num_rows($result) > 0) {
				$smarty->assign("METAID", $adb->query_result($result,0,'id'));
			
				require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
				$processDynaFormObj = ProcessDynaForm::getInstance();
				$blocks = $processDynaFormObj->getStructure($id, $elementid);
				$fpofv_value_options = array();
				if (!empty($blocks)) {
					foreach($blocks as $block) {
						foreach($block['fields'] as $field) {
							//crmv@106857
							if ($field['uitype'] == 220) {
								$fieldname = $field['fieldname'];
								$label = $field['label'];
								$fpofv_value_options['$'.$fieldname] = $label;
								$fpofv[] = array(
									'FpofvBlockLabel'=>$block['label'],
									'TaskField'=>$fieldname,
									'TaskFieldLabel'=>$label,
									'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
									'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
									'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
									'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
									'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
									'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
									'HideFpovValue'=>true,
									'HideFpovMandatoryPermission'=>true,
								);
								if (!empty($field['columns'])) {
									$columns = Zend_Json::decode($field['columns']);
									foreach($columns as $column) {
										$fieldname = $field['fieldname'].'::'.$column['fieldname'];
										$label = $field['label'].': '.$column['label'];
										$fpofv_value_options['$'.$fieldname] = $label;
										$fpofv[] = array(
											'FpofvBlockLabel'=>$block['label'],
											'TaskField'=>$fieldname,
											'TaskFieldLabel'=>$label,
											'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
											'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
											'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
											'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
											'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
											'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
										);
									}
								}
							} else {
								$fieldname = $field['fieldname'];
								$label = $field['label'];
								$fpofv_value_options['$'.$fieldname] = $label;
								$fpofv[] = array(
									'FpofvBlockLabel'=>$block['label'],
									'TaskField'=>$fieldname,
									'TaskFieldLabel'=>$label,
									'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
									'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
									'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
									'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
									'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
									'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
								);
							}
							//crmv@106857e
						}
					}
					$smarty->assign("FPOFV_PIECE_DATA", $fpofv);
					$smarty->assign("FPOFV_VALUE_OPTIONS", $fpofv_value_options);
					$smarty->assign('SDK_CUSTOM_FUNCTIONS', SDK::getFormattedProcessMakerFieldActions());
				}
			} else {
				$buttons = '
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" style="padding:5px"></td>
					 	<td align="right" style="padding: 5px;" nowrap>
							<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
							<input type="button" onclick="ProcessMakerScript.'.$close_function.'(\''.$id.'\',\''.$elementid.'\',\''.$ruleid.'\');" class="crmbutton small" value="'.$app_strings['LBL_BACK'].'" title="'.$app_strings['LBL_BACKN'].'">
						</td>
					 </tr>
					 </table>';
				$smarty->assign("BUTTON_LIST", $buttons);
				$smarty->assign("ERROR", getTranslatedString('LBL_NONE_DYNAFORM_CONDITIONAL','Settings'));
			}
			$smarty->display('Settings/ProcessMaker/Metadata/DynaformConditional.tpl');
		} else {
			if (empty($ruleid)) {
				$smarty->assign('FIELD_PERMISSIONS_DISPLAY','none');
			} else {
				require_once('include/utils/ModLightUtils.php');
				$MLUtils = ModLightUtils::getInstance();
				
				$smarty->assign('FIELD_PERMISSIONS_DISPLAY','block');
				$moduleName = $conditionals[$ruleid]['moduleName'];
				list($metaid,$module) = explode(':',$moduleName);

				$conditionals_obj = CRMEntity::getInstance('Conditionals');
				$FpofvData = $conditionals_obj->wui_getFpofvData('',$module);
				$fpofv = array();
				foreach($FpofvData as $tmp) {
					if ($tmp['uitype'] == 220) {
						$fieldname = $tmp['FpofvChkFieldName'];
						$label = $tmp['FpofvChkFieldLabel'];
						$fpofv_value_options['$'.$fieldname] = $label;
						$fpofv[] = array(
							'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$module),
							'TaskField'=>$fieldname,
							'TaskFieldLabel'=>getTranslatedString($label,$module),
							'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
							'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
							'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
							'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
							'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
							'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
							'HideFpovValue'=>$tmp['HideFpovValue'],
							'HideFpovManaged'=>$tmp['HideFpovManaged'],
							'HideFpovReadPermission'=>$tmp['HideFpovReadPermission'],
							'HideFpovWritePermission'=>$tmp['HideFpovWritePermission'],
							'HideFpovMandatoryPermission'=>$tmp['HideFpovMandatoryPermission'],
						);
						$columns = $MLUtils->getColumns($module, $tmp['FpofvChkFieldName']);
						if (!empty($columns)) {
							foreach($columns as $column) {
								$fieldname = $tmp['FpofvChkFieldName'].'::'.$column['fieldname'];
								$label = $tmp['FpofvChkFieldLabel'].': '.$column['label'];
								$fpofv_value_options['$'.$fieldname] = $label;
								$fpofv[] = array(
									'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$module),
									'TaskField'=>$fieldname,
									'TaskFieldLabel'=>getTranslatedString($label,$module),
									'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
									'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
									'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
									'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
									'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
									'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
								);
							}
						}
					} else {
						$fieldname = $tmp['FpofvChkFieldName'];
						$label = $tmp['FpofvChkFieldLabel'];
						$fpofv_value_options['$'.$fieldname] = $label;
						$fpofv[] = array(
							'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$module),
							'TaskField'=>$fieldname,
							'TaskFieldLabel'=>getTranslatedString($label,$module),
							'FpovValueActive'=>$fpofv_saved[$fieldname]['FpovValueActive'],
							'FpovValueStr'=>$fpofv_saved[$fieldname]['FpovValueStr'],
							'FpovManaged'=>$fpofv_saved[$fieldname]['FpovManaged'],
							'FpovReadPermission'=>$fpofv_saved[$fieldname]['FpovReadPermission'],
							'FpovWritePermission'=>$fpofv_saved[$fieldname]['FpovWritePermission'],
							'FpovMandatoryPermission'=>$fpofv_saved[$fieldname]['FpovMandatoryPermission'],
							'HideFpovValue'=>$tmp['HideFpovValue'],
							'HideFpovManaged'=>$tmp['HideFpovManaged'],
							'HideFpovReadPermission'=>$tmp['HideFpovReadPermission'],
							'HideFpovWritePermission'=>$tmp['HideFpovWritePermission'],
							'HideFpovMandatoryPermission'=>$tmp['HideFpovMandatoryPermission'],
						);
					}
				}
				$smarty->assign("FPOFV_PIECE_DATA", $fpofv);
				$smarty->assign("FPOFV_VALUE_OPTIONS", $fpofv_value_options);
				$smarty->assign('SDK_CUSTOM_FUNCTIONS', SDK::getFormattedProcessMakerFieldActions());
			}
			$modules = $PMUtils->getRecordsInvolvedOptions($id, $moduleName);
			$smarty->assign("moduleNames", $modules);
	
			$smarty->display('Settings/ProcessMaker/Metadata/Conditional.tpl');
		}
		exit;
		break;
	case 'load_field_permissions_table':
		require_once('include/utils/ModLightUtils.php');
		$MLUtils = ModLightUtils::getInstance();
		
		$chk_module = $_REQUEST['chk_module'];
		$conditionals_obj = CRMEntity::getInstance('Conditionals');
		$fpofv = array();
		$fpofv_value_options = array();
		if (!empty($chk_module)) {
			$FpofvData = $conditionals_obj->wui_getFpofvData('',$chk_module);
			foreach($FpofvData as $tmp) {
				if ($tmp['uitype'] == 220) {
					$fieldname = $tmp['FpofvChkFieldName'];
					$label = $tmp['FpofvChkFieldLabel'];
					$fpofv_value_options['$'.$fieldname] = $label;
					$fpofv[] = array(
						'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$chk_module),
						'TaskField'=>$fieldname,
						'TaskFieldLabel'=>getTranslatedString($label,$chk_module),
						'HideFpovValue'=>$tmp['HideFpovValue'],
						'HideFpovManaged'=>$tmp['HideFpovManaged'],
						'HideFpovReadPermission'=>$tmp['HideFpovReadPermission'],
						'HideFpovWritePermission'=>$tmp['HideFpovWritePermission'],
						'HideFpovMandatoryPermission'=>$tmp['HideFpovMandatoryPermission'],
					);					
					$columns = $MLUtils->getColumns($chk_module, $tmp['FpofvChkFieldName']);
					if (!empty($columns)) {
						foreach($columns as $column) {
							$fieldname = $tmp['FpofvChkFieldName'].'::'.$column['fieldname'];
							$label = $tmp['FpofvChkFieldLabel'].': '.$column['label'];
							$fpofv_value_options['$'.$fieldname] = $label;
							$fpofv[] = array(
								'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$chk_module),
								'TaskField'=>$fieldname,
								'TaskFieldLabel'=>getTranslatedString($label,$chk_module),
							);
						}
					}
				} else {
					$fieldname = $tmp['FpofvChkFieldName'];
					$label = $tmp['FpofvChkFieldLabel'];
					$fpofv_value_options['$'.$fieldname] = $label;
					$fpofv[] = array(
						'FpofvBlockLabel'=>getTranslatedString($tmp['FpofvBlockLabel'],$chk_module),
						'TaskField'=>$fieldname,
						'TaskFieldLabel'=>getTranslatedString($label,$chk_module),
						'HideFpovValue'=>$tmp['HideFpovValue'],
						'HideFpovManaged'=>$tmp['HideFpovManaged'],
						'HideFpovReadPermission'=>$tmp['HideFpovReadPermission'],
						'HideFpovWritePermission'=>$tmp['HideFpovWritePermission'],
						'HideFpovMandatoryPermission'=>$tmp['HideFpovMandatoryPermission'],
					);
				}
			}
		}
		$smarty->assign("FPOFV_PIECE_DATA", $fpofv);
		$smarty->assign("FPOFV_VALUE_OPTIONS", $fpofv_value_options);
		$smarty->assign('SDK_CUSTOM_FUNCTIONS', SDK::getFormattedProcessMakerFieldActions());
		$smarty->display('Settings/ProcessMaker/Metadata/ConditionalFieldTable.tpl');
		exit;
		break;
	case 'save_dynaform_conditional':
	case 'save_conditional':
		$id = vtlib_purify($_REQUEST['processmakerid']);
		$elementid = $_REQUEST['elementid'];
		$ruleid = $_REQUEST['ruleid'];
		$metaid = $_REQUEST['metaid'];
		$conditions = Zend_Json::decode($_REQUEST['conditions']);
		($mode == 'save_dynaform_conditional') ? $item = 'dfconditionals' : $item = 'conditionals';
		
		$fpofv = array();
		foreach($_REQUEST as $k => $v) {
			$perms = array('FpovValueActive','FpovValueStr','FpovManaged','FpovReadPermission','FpovWritePermission','FpovMandatoryPermission');
			foreach($perms as $perm) {
				if (strpos($k,$perm) !== false) {
					list($tmp,$fieldname) = explode($perm,$k);
					if (!empty($fieldname)) {
						$fpofv[$fieldname][$perm] = $v;
					}
				}
			}
		}
		foreach($fpofv as $fieldname => $info) {
			if ($info['FpovValueActive'] != '1') unset($fpofv[$fieldname]['FpovValueStr']);
			if (empty($fpofv[$fieldname])) unset($fpofv[$fieldname]);
		}
		$conditionals = array(
			'title'=>$_REQUEST['title'],
			'role_grp_check'=>$_REQUEST['role_grp_check'],
			'conditions'=>$conditions,
			'fpofv'=>$fpofv,
		);
		if ($mode == 'save_conditional') {
			$conditionals['moduleName'] = $_REQUEST['moduleName'];
		}
		
		$data = $PMUtils->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		if (empty($ruleid)) {
			$ruleid = 1;
			if (!empty($vte_metadata[$elementid][$item])) {
				end($vte_metadata[$elementid][$item]);
				$ruleid = key($vte_metadata[$elementid][$item])+1;
			}
		}
		$vte_metadata[$elementid][$item][$ruleid] = $conditionals;
		$PMUtils->saveMetadata($id,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		exit;
		break;
	case 'delete_dynaform_conditional':
	case 'delete_conditional':
		($mode == 'delete_dynaform_conditional') ? $item = 'dfconditionals' : $item = 'conditionals';
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$ruleid = vtlib_purify($_REQUEST['ruleid']);
		
		$data = $PMUtils->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		unset($vte_metadata[$elementid][$item][$ruleid]);
		$PMUtils->saveMetadata($id,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		exit;
		break;
	//crmv@99316e crmv@112297e
	//crmv@100731
	case 'add_advanced_permission':
		$id = vtlib_purify($_REQUEST['processmakerid']);
		$elementid = $_REQUEST['elementid'];
		$data = $PMUtils->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		$vte_metadata[$elementid]['advanced_permissions'][] = array(
			'record_involved'=>$_REQUEST['record_involved'],
			'resource_type'=>$_REQUEST['resource_type'],
			'resource'=>$_REQUEST['resource'],
			'permission'=>$_REQUEST['permission'],
		);
		$PMUtils->saveMetadata($id,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		exit;
		break;
	case 'delete_advanced_permission':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$ruleid = vtlib_purify($_REQUEST['ruleid']);
		
		$data = $PMUtils->retrieve($id);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		unset($vte_metadata[$elementid]['advanced_permissions'][$ruleid]);
		$PMUtils->saveMetadata($id,$elementid,Zend_Json::encode($vte_metadata[$elementid]));
		exit;
		break;
	//crmv@100731e
	case 'CheckActiveProcesses':
		global $mod_strings;
		$ckeckProcesses = $PMUtils->checkActiveProcesses();
		$success = $ckeckProcesses;
		if (!$success) {
			$limit = $PMUtils->limit_processes;
			$message = sprintf($mod_strings['LBL_PM_LIMIT_EXCEEDED'], $limit);
		}
		echo Zend_Json::encode(array('success' => $success, 'message' => $message));
		exit;
	//crmv@106856
	case 'open_advanced_field_assignment':
		$processmakerid = vtlib_purify($_REQUEST['processid']);
		$elementid = $_REQUEST['elementid'];
		$actionid = $_REQUEST['actionid'];
		$fieldname = $_REQUEST['fieldname'];
		$form_module = $_REQUEST['form_module'];
		
		$smarty->assign("PROCESSID", $processmakerid);
		$smarty->assign("ELEMENTID", $elementid);
		$smarty->assign("ACTIONID", $actionid);
		$smarty->assign("FIELDNAME", $fieldname);
		$smarty->assign("FORM_MODULE", $form_module);
		
		$smarty->assign("PAGE_TITLE", $mod_strings['LBL_PM_ADVANCED_FIELD_ASSIGNMENT']);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					<input type="button" onclick="ActionTaskScript.saveAdvancedFieldAssignment(\''.$processmakerid.'\',\''.$elementid.'\',\''.$actionid.'\',\''.$fieldname.'\');" class="crmbutton small save" value="'.$app_strings['LBL_SAVE_LABEL'].'" title="'.$app_strings['LBL_SAVE_LABEL'].'">
					<input type="button" onclick="ActionTaskScript.closeAdvancedFieldAssignment(\''.$processmakerid.'\',\''.$elementid.'\',\''.$actionid.'\',\''.$fieldname.'\');" class="crmbutton small delete" value="'.$app_strings['LBL_CANCEL_BUTTON_LABEL'].'" title="'.$app_strings['LBL_CANCEL_BUTTON_LABEL'].'">
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		
		$data = $PMUtils->retrieve($processmakerid);
		$vte_metadata = Zend_Json::decode($data['vte_metadata']);
		$helper = Zend_Json::decode($data['helper']);
		$formModuleInstance = Vtecrm_Module::getInstance($form_module);
		// TODO gestire per tutti i campi
		$result = $adb->pquery("SELECT uitype FROM {$table_prefix}_field WHERE tabid = ? and fieldname = ?", array($formModuleInstance->id,$fieldname));
		if ($result && $adb->num_rows($result) > 0) {
			$uitype = $adb->query_result($result,0,'uitype');
			if ($uitype == 51) $uitype = 52;
		}
		
		$reload_session = $_REQUEST['reload_session'];
		$tmp_reload_session = $PMUtils->getReloadAdvancedFieldAssignment($fieldname);
		if ($reload_session == 'yes' && !empty($tmp_reload_session)) $reload_session = $tmp_reload_session;
		$PMUtils->unsetReloadAdvancedFieldAssignment($fieldname);
		if ($reload_session == 'yes') {
			if ($form_module == 'Processes') {
				$rules = $helper[$elementid]['advanced_field_assignment'][$fieldname];
			} else {
				$rules = $vte_metadata[$elementid]['actions'][$actionid]['advanced_field_assignment'][$fieldname];
			}
			$PMUtils->setAdvancedFieldAssignment($fieldname,$rules);
		} else {
			$rules = $PMUtils->getAdvancedFieldAssignment($fieldname);
		}
		$PMUtils->addConditionTranslations($rules, $processmakerid);

		global $noof_group_rows, $current_user;
		$_REQUEST['enable_editoptions'] = 'yes';
		get_group_options();
		if (!empty($rules)) {
			foreach($rules as $i => &$rule) {
				$assigned_user_id = $rule['value'];
				$assigntype = $rule['assigntype'];
				$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
				if ($noof_group_rows!=0) $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
				$rule['users_combo'] = $users_combo;
				$rule['groups_combo'] = $groups_combo;
				$rule['value'] = $assigned_user_id;
				$rule['assigntype'] = $assigntype;
				$rule['uitype'] = $uitype;
				$_REQUEST['sdk_params_'.'assigned_user_id'.$i] = $rule['sdk_params'];
			}
		}
		$smarty->assign("RULES", $rules);
		
		$smarty->display('Settings/ProcessMaker/Metadata/AdvancedFieldAssignment.tpl');
		exit;
	case 'open_advanced_field_assignment_condition':
		$ruleid = vtlib_purify($_REQUEST['ruleid']);
		$processid = vtlib_purify($_REQUEST['processid']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$actionid = vtlib_purify($_REQUEST['actionid']);
		$fieldname = vtlib_purify($_REQUEST['fieldname']);
		$form_module = vtlib_purify($_REQUEST['form_module']);
		$smarty->assign("PROCESSID", $processid);
		$smarty->assign("ELEMENTID", $elementid);
		$smarty->assign("ACTIONID", $actionid);
		$smarty->assign("FIELDNAME", $fieldname);
		
		$current_entity = '';
		if (isset($ruleid)) {
			$rules = $PMUtils->getAdvancedFieldAssignment($fieldname);
			$current_entity = $rules[$ruleid]['meta_record'];
			$smarty->assign("CONDITIONS", Zend_Json::encode($rules[$ruleid]['conditions']));
		}
		$modules = $PMUtils->getRecordsInvolvedOptions($processid, $current_entity);
		//crmv@96450
		require_once('modules/Settings/ProcessMaker/ProcessDynaForm.php');
		$processDynaFormObj = ProcessDynaForm::getInstance();
		$dynaforms = $processDynaFormObj->getOptions($processid, $current_entity);
		if (!empty($dynaforms)) $modules = array_merge($modules,$dynaforms);
		//crmv@96450e
		$smarty->assign("moduleNames", $modules);

		$smarty->assign("PAGE_TITLE", $mod_strings['LBL_PM_ADVANCED_FIELD_ASSIGNMENT'].': '.$mod_strings['LBL_NEW_CONDITION_BUTTON_LABEL']);
		$smarty->assign("HEADER_Z_INDEX", 1);
		$buttons = '
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="100%" style="padding:5px"></td>
			 	<td align="right" style="padding: 5px;" nowrap>
					<span class="indicatorMetadata" style="display:none;"><i class="dataloader" data-loader="circle" style="vertical-align:middle;"></i></span>&nbsp;
					<input type="button" onclick="ActionTaskScript.saveAdvancedFieldAssignmentCondition(\''.$processid.'\',\''.$elementid.'\',\''.$actionid.'\',\''.$fieldname.'\',\''.$form_module.'\',\''.$ruleid.'\');" class="crmbutton small save" value="'.$app_strings['LBL_SAVE_LABEL'].'" title="'.$app_strings['LBL_SAVE_LABEL'].'">
					<input type="button" onclick="ActionTaskScript.closeAdvancedFieldAssignmentCondition();" class="crmbutton small delete" value="'.$app_strings['LBL_CANCEL_BUTTON_LABEL'].'" title="'.$app_strings['LBL_CANCEL_BUTTON_LABEL'].'">
				</td>
			 </tr>
			 </table>';
		$smarty->assign("BUTTON_LIST", $buttons);
		
		$smarty->assign('SDK_CUSTOM_FUNCTIONS',SDK::getFormattedProcessMakerTaskConditions());
		
		$smarty->display('Settings/ProcessMaker/Metadata/AdvancedFieldAssignmentCondition.tpl');
		exit;
	case 'save_advanced_field_assignment_condition':
		$ruleid = vtlib_purify($_REQUEST['ruleid']);
		$fieldname = $_REQUEST['fieldname'];
		$meta_record = $_REQUEST['meta_record'];
		$conditions = $_REQUEST['conditions'];
		$PMUtils->saveAdvancedFieldAssignment($fieldname,'condition',array($ruleid,$meta_record,$conditions));
		exit;
	case 'save_advanced_field_assignment_values':
		$fieldname = $_REQUEST['fieldname'];
		$PMUtils->saveAdvancedFieldAssignment($fieldname,'values',array($_REQUEST['form']));
		exit;
	/*
	case 'save_advanced_field_assignment':
		$fieldname = $_REQUEST['fieldname'];
		$PMUtils->saveAdvancedFieldAssignment($fieldname,'db',array($_REQUEST['processid'],$_REQUEST['elementid'],$_REQUEST['actionid']));
		exit;
	*/
	case 'delete_advanced_field_assignment':
		$PMUtils->removeAdvancedFieldAssignment($_REQUEST['processid'],$_REQUEST['elementid'],$_REQUEST['actionid'],$_REQUEST['fieldname'],$_REQUEST['ruleid']);
		exit;
	case 'reload_advanced_field_assignment':
		$PMUtils->setReloadAdvancedFieldAssignment($_REQUEST['fieldname'],$_REQUEST['val']);
		exit;
	//crmv@106856e
	//crmv@113775
	case 'load_potential_relations':
		$id = vtlib_purify($_REQUEST['id']);
		$elementid = vtlib_purify($_REQUEST['elementid']);
		$record1 = vtlib_purify($_REQUEST['record1']);
		list($metaid1,$module1) = explode(':',$record1);
		
		$relationManager = RelationManager::getInstance();
		$recordsInvolved = $PMUtils->getRecordsInvolved($id);
		$values = array(''=>array(getTranslatedString('LBL_PLEASE_SELECT'),''));
		foreach($recordsInvolved as $i => $r) {
			if ($r['seq'] != $metaid && $relationManager->isModuleRelated($module1, $r['module'])) {
				$key = $r['seq'].':'.$r['module'];
				($selected_value == $key) ? $selected = 'selected' : $selected = '';
				$values[$key] = array($r['label'], $selected);
			}
		}
		$smarty->assign("RECORDPICK", $values);
		$smarty->assign("ENTITY", '2');
		$smarty->display('Settings/ProcessMaker/actions/RelateRecord.tpl');
		exit;	
	//crmv@113775e
	default:
		if ($mode == 'delete') {
			$id = vtlib_purify($_REQUEST['id']);
			$PMUtils->delete($id);
			$smarty->assign("MODE", '');
		/*
		} elseif ($mode == 'save') {
			$id = vtlib_purify($_REQUEST['id']);
			$PMUtils->edit($id,$_REQUEST);
		*/
		}
		$limit_exceeded = $PMUtils->limitProcessesExceeded();
		if ($limit_exceeded !== false) {
			global $adb, $table_prefix;
			$result = $adb->limitpQuery("select id from {$table_prefix}_processmaker where active = ?",0,($limit_exceeded-$PMUtils->limit_processes),array(1));
			if ($result && $adb->num_rows($result) > 0) {
				$ids = array();
				while($row=$adb->fetchByAssoc($result)) {
					$ids[] = $row['id'];
				}
				$adb->pquery("update {$table_prefix}_processmaker set active = ? where id in (".generateQuestionMarks($ids).")",array(0,$ids));
			}
		}
		// load list
		$smarty->assign("HEADER", $PMUtils->getHeaderList());
		$smarty->assign("LIST", $PMUtils->getList());
		$sub_template = 'Settings/ProcessMaker/List.tpl';
		break;
}

$smarty->assign("SUB_TEMPLATE", $sub_template);
$smarty->display('Settings/ProcessMaker/ProcessMaker.tpl');