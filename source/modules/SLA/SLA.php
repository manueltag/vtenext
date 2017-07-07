<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.biz.
 * Portions created by crmvillage.biz are Copyright (C) crmvillage.biz.
 * All Rights Reserved.
 *******************************************************************************/
 
class SLA {
 	
	function get_config(){
		include('modules/SLA/SLA.config.php');
		return $sla_config;
	}
	
 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
 	function vtlib_handler($moduleName, $eventType) {
 					
		require_once('include/utils/utils.php');			
		global $adb;
		global $table_prefix;
		
 		if($eventType == 'module.postinstall') {	
 			/*	TODO: settings
			$fieldid = $adb->getUniqueID('vtiger_settings_field');
			$blockid = getSettingsBlockId('LBL_MODULE_MANAGER');
			
			$seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field");
			$seq = 1;
			if ($adb->num_rows($seq_res) > 0) {
				$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
				if ($cur_seq != null)	$seq = $cur_seq + 1;
			}
			
			$adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence) 
				VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_SLA', 'modules/SLA/resources/SLA.png', 'LBL_SLA_DESCRIPTION', 'index.php?module=SLA&action=index&parenttab=Settings', $seq));
			*/
			$tabid = getTabid('SLA');
			if(isset($tabid) && $tabid!='') {
				$adb->pquery('DELETE FROM '.$table_prefix.'_profile2tab WHERE tabid = ?', array($tabid));
			}
			
			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));
			//installazione dei campi nei moduli specificati nel file di configurazione
			$Vtiger_Utils_Log = true;
			require_once('vtlib/Vtecrm/Utils.php');
			include_once('vtlib/Vtecrm/Menu.php');
			include_once('vtlib/Vtecrm/Module.php');
			require 'include/events/include.inc';
			include_once('modules/SLA/SLA.php');
			$sla_config_global = SLA::get_config();
			$lang = Array(
				'it_it',
				'en_us',
			);
			$template = Array(
				'blocks_to_create'=>Array(
					'LBL_SLA'=>Array(
						'label'=>'LBL_SLA',
						'langs'=>Array('it_it'=>'Temipistiche SLA','en_us'=>'SLA timings'),
					),
				),
				'fields_to_hide'=>Array(
				),
				'fields_to_modify'=>Array(
				),
				'fields_to_create'=>Array(
					'time_elapsed'=>Array(
						'label'=>'Time Elapsed',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo trascorso','en_us'=>'Time Elapsed'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
						'typeofdata'=>'I~O',
					),
					'time_remaining'=>Array(
						'label'=>'Time remaining',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo rimanente','en_us'=>'Time remaining'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
						'typeofdata'=>'I~O',
					),
					'start_sla'=>Array(
						'label'=>'SLA start date',
						'uitype'=>1021,
						'langs'=>Array('it_it'=>'Data partenza SLA','en_us'=>'SLA start date'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),
					'end_sla'=>Array(
						'label'=>'SLA end date',
						'uitype'=>1021,
						'langs'=>Array('it_it'=>'Data fine SLA','en_us'=>'SLA end date'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),
					'time_refresh'=>Array(
						'label'=>'Update Time',
						'uitype'=>1021,
						'langs'=>Array('it_it'=>'Orario di aggiornamento','en_us'=>'Update Time'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),
					'sla_time'=>Array(
						'label'=>'SLA Estimated Time',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo SLA previsto','en_us'=>'SLA Estimated Time'),
						'block'=>'LBL_SLA',
						'readonly'=>1,
					),
					'due_date'=>Array(
						'label'=>'Due Date',
						'uitype'=>5,
						'generatedtype'=>2,
						'langs'=>Array('it_it'=>'Data di chiusura','en_us'=>'Due Date'),
						'block'=>'LBL_SLA',
					),
					'due_time'=>Array(
						'label'=>'Due Time',
						'uitype'=>1,
						'langs'=>Array('it_it'=>'Ora chiusura (hh:mm)','en_us'=>'Due time (hh:mm)'),
						'block'=>'LBL_SLA',
						'columntype'=>'C(5)',
						'typeofdata'=>'T~O',
					),
					'time_change_status'=>Array(
						'label'=>'Time Last Status Change',
						'uitype'=>1021,
						'langs'=>Array('it_it'=>'Data ultimo cambio di stato','en_us'=>'Time Last Status Change'),
						'block'=>'LBL_SLA',
						'typeofdata'=>'V~O',
						'readonly'=>100,
					),
					'time_elapsed_change_status'=>Array(
						'label'=>'Time Elapsed Last Status Change',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo trascorso da ultimo cambio di stato','en_us'=>'Time Elapsed Last Status Change'),
						'block'=>'LBL_SLA',
						'readonly'=>100,		
					),
					'reset_sla'=>Array(
						'label'=>'Reset SLA',
						'uitype'=>56,
						'langs'=>Array('it_it'=>'Resetta SLA','en_us'=>'Reset SLA'),
						'block'=>'LBL_SLA',
					),
					'ended_sla'=>Array(
						'label'=>'End SLA',
						'uitype'=>56,
						'langs'=>Array('it_it'=>'Fine SLA','en_us'=>'End SLA'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),
					'time_elapsed_idle'=>Array(
						'label'=>'Idle Time Elapsed',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo trascorso in idle','en_us'=>'Idle Time Elapsed'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),		
					'time_elapsed_out_sla'=>Array(
						'label'=>'Out SLA Time Elapsed',
						'uitype'=>1020,
						'langs'=>Array('it_it'=>'Tempo trascorso fuori SLA','en_us'=>'Out SLA Time Elapsed'),
						'block'=>'LBL_SLA',
						'readonly'=>99,
					),		
				),
			);
			if (count($sla_config_global)<=0){
				echo'impossibile installare i campi per la gestione sla! configurazione assente!';
				exit;
			}	
			foreach (array_keys($sla_config_global) as $mod){
				$modules[$mod] = $template;
			}
			foreach ($modules as $module=>$arr_master){
				$moduleInstance = Vtiger_Module::getInstance($module);
				//creazione blocchi
				if ($arr_master['blocks_to_create']){
					foreach ($arr_master['blocks_to_create'] as $blockname=>$arr){
						$block = new Vtiger_Block();
						$block->label = $arr['label'];
						if ($arr['sequence'])
							$block->sequence = $arr['sequence'];
						if ($arr['showtitle'])
							$block->showtitle = $arr['showtitle'];
						if ($arr['visible'])
							$block->visible = $arr['visible'];
						if ($arr['increateview'])
							$block->increateview = $arr['increateview'];
						if ($arr['ineditview'])
							$block->ineditview = $arr['ineditview'];
						if ($arr['indetailview'])
							$block->indetailview = $arr['indetailview'];
						$block->save($moduleInstance);
						foreach ($lang as $lang_str){
							$file = "modules/$module/language/$lang_str.lang.php";
							include($file);
							$old = "/\);\s*.?>\s*/";
							$new = "'".$arr['label']."' => '".$arr['langs'][$lang_str]."',\n);\n?>";
							$contents = preg_replace($old,$new,file_get_contents($file));
							file_put_contents($file,$contents);
						}			
					}
				}
				//creazione campi
				if ($arr_master['fields_to_create']){
					foreach ($arr_master['fields_to_create'] as $fieldname=>$arr){
						$field = new Vtiger_Field();
						$field->name = $fieldname;
						if ($arr['columnn'])
							$field->columnn = $arr['columnn'];
						if ($arr['table'])
							$field->table = $arr['table'];
						else
							$field->table = $moduleInstance->basetable;
						if ($arr['label'])
							$field->label = $arr['label'];
						if (!$arr['block']){
							$q = "SELECT blockid FROM ".$table_prefix."_blocks where tabid  = (select tabid from ".$table_prefix."_tab where name = ?)  order by sequence asc";
							$res = $adb->limitpQuery($q,0,1,Array($module));
							if ($res && $adb->num_rows($res) == 1){
								$arr['block'] = $adb->query_result($res,0,'blockid');
							}
						}
						$block_instance = Vtiger_Block::getInstance($arr['block'],$moduleInstance);
						if ($arr['readonly'])
							$field->readonly = $arr['readonly'];
						else	
							$field->readonly = 1;
						if (!$arr['uitype'])
							$arr['uitype'] = 1;
						switch($arr['uitype']){
							case 9:
								$field->columntype = 'C(3)';
								$field->typeofdata = 'N~O';
								break;
							case 7:
							case 71:
								$field->columntype = 'N(20,2)';
								$field->typeofdata = 'N~O';			
								break;
							case 5:
								$field->columntype = 'D';
								$field->typeofdata = 'D~O';			
								break;
							case 15:
								$field->columntype = 'C(255)';
								$field->typeofdata = 'V~O';			
								break;
							case 70:
								$field->columntype = 'T';
								$field->typeofdata = 'T~O';			
								break;
							case 1020:
								$field->columntype = 'N(20,0)';
								$field->typeofdata = 'N~O';			
								break;
							case 1021:
								$field->columntype = 'DT';
								$field->typeofdata = 'V~O';			
								break;
							default:
								$field->columntype = 'C(255)';
								$field->typeofdata = 'V~O';	
								break;	
						}
						if ($arr['generatedtype'])
							$field->generatedtype = $arr['generatedtype'];
						if ($arr['columntype'])
							$field->columntype = $arr['columntype'];
						if ($arr['typeofdata'])
							$field->typeofdata = $arr['typeofdata'];
						$field->uitype = $arr['uitype'];
						if ($arr['masseditable'])
							$field->masseditable = $arr['masseditable'];
						else
							$field->masseditable = 0;	
						if ($arr['quickcreate']){
							$field->quickcreate = $arr['quickcreate'];
							$q = "select max(quickcreatesequence)+1 as seq from ".$table_prefix."_field where tabid  = (select tabid from ".$table_prefix."_tab where name = ?) and block = ?";
							$res = $adb->pquery($q,Array($module,$arr['block']));
							if ($res && $adb->num_rows($res)==1)
								$field->quicksequence = $adb->query_result($res,0,'seq');
						}	
						else
							$field->quickcreate = 1;	//crmv@22583
								
						//se picklist aggiungo i valori
						if ($arr['picklistvalues']){
							$field->setPicklistValues( $arr['picklistvalues'] );
						}
						if ($arr['helpinfo']){
							$field->helpinfo = $arr['helpinfo'];
						}
						$block_instance->addField($field);
						foreach ($lang as $lang_str){
							$file = "modules/$module/language/$lang_str.lang.php";
							include($file);
							$old = "/\);\s*.?>\s*/";
							$new = "'".$arr['label']."' => '".$arr['langs'][$lang_str]."',\n);\n?>";
							$contents = preg_replace($old,$new,file_get_contents($file));
							file_put_contents($file,$contents);
						}
					}
				}
				//nascondere campi
				if ($arr_master['fields_to_hide']){
					$q = "update ".$table_prefix."_def_org_field set visible = 1
					where tabid  = (select tabid from ".$table_prefix."_tab where name = ?)
					and fieldid in (select fieldid from ".$table_prefix."_field where tabid = ".$table_prefix."_def_org_field.tabid 
					and fieldname in (".generateQuestionMarks($arr_master['fields_to_hide'])."))";
					$params = Array($module,$arr_master['fields_to_hide']);
					$adb->pquery($q,$params);
					$q = "update ".$table_prefix."_field set presence = 1
					where tabid  = (select tabid from ".$table_prefix."_tab where name = ?)
					and fieldname in (".generateQuestionMarks($arr_master['fields_to_hide']).")";
					$params = Array($module,$arr_master['fields_to_hide']);
					$adb->pquery($q,$params);
				}
				//rinominazione campi
				if ($arr_master['fields_to_rename']){
					foreach ($arr_master['fields_to_rename'] as $fieldname=>$arr){
						$q = "update ".$table_prefix."_field set fieldlabel=? where fieldname = ? and tabid in (select tabid from ".$table_prefix."_tab where name = ?)";
						$params = Array($arr['new'],$fieldname,$module);
						$adb->pquery($q,$params);
					}
					foreach ($lang as $lang_str){
						$file = "modules/$module/language/$lang_str.lang.php";
						include($file);
						$old_trad = $mod_strings[$arr['old']];
						$old = "/'".$arr['old']."'\s*=>\s*'".$old_trad."'\s*,/";
						$new = "'".$arr['new']."' => '".$arr['langs'][$lang_str]."',";
						$contents = preg_replace($old,$new,file_get_contents($file));
						file_put_contents($file,$contents);	
					}
				}
				//modifica campi
				if ($arr_master['fields_to_modify']){
					foreach ($arr_master['fields_to_modify'] as $field=>$arr){
						if (!$arr)
							continue;
						$q = "update ".$table_prefix."_field set ";
						$tot = count($arr);
						$cnt = 1;
						$params = Array();	
						foreach ($arr as $column=>$value){		
							$q.= $column." = ?";
							$params[] = $value;
							if ($cnt < $tot)
								$q.=",";
						}
						$q.=" where fieldname = ? and tabid in (select tabid from ".$table_prefix."_tab where name = ?)";
						$params[] = $field;
						$params[] = $module;
						$adb->convert2Sql($q,$adb->flatten_array($params));
						$adb->pquery($q,$params);
					}
					
				}
			}
			SDK::file2DbLanguages('SLA');
			// gets all the SLA languages
			$slalangs = array_keys(vtlib_getToggleLanguageInfo());
			$langstrings = array();
			if (!empty($slalangs)) {
				foreach ($slalangs as $lang) {
					$langstrings[$lang] = get_lang_strings('SLA', $lang);
				}
			}
			// register them for modules associated with SLA
			$slacfg = SLA::get_config();
			if (is_array($slacfg) && !empty($langstrings)) {
				$slamod = array_keys($slacfg);
				foreach ($slamod as $mod) {
					foreach ($langstrings as $lang=>$mod_strings) {
						if (is_array($mod_strings)) {
							foreach ($mod_strings as $key => $value) {
								SDK::setLanguageEntry($mod, $lang, $key, $value);
							}
						}
					}
				}
			}

			// crmv@47611
			if (Vtiger_Utils::CheckTable($table_prefix.'_cronjobs')) {
				require_once('include/utils/CronUtils.php');
				$CU = CronUtils::getInstance();

				$cj = new CronJob();
				$cj->name = 'SLA';
				$cj->active = 0;
				$cj->singleRun = false;
				$cj->fileName = 'cron/modules/SLA/SLA.service.php';
				$cj->timeout = 300;		// 5min timeout
				$cj->repeat = 300;		// run every 5 min
				$CU->insertCronJob($cj);
			}
			// crmv@47611e
			
		} else if($eventType == 'module.disabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerInActive('SLAHandler');
			
		} else if($eventType == 'module.enabled') {
			$em = new VTEventsManager($adb);
			$em->setHandlerActive('SLAHandler');

		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
			$tmp_dir = 'packages/vte/mandatory/tmp1';
			mkdir($tmp_dir);
			$unzip = new Vtiger_Unzip("packages/vte/mandatory/$moduleName.zip");
			$unzip->unzipAllEx($tmp_dir);
			if($unzip) $unzip->close();
			copy("$tmp_dir/cron/$moduleName.service.php","cron/modules/$moduleName/$moduleName.service.php");
			if ($handle = opendir($tmp_dir)) {
				folderDetete($tmp_dir);
			}
		}
 	}	
}
?>
