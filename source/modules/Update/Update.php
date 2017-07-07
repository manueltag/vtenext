<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is crmvillage.
 * Portions created by crmvillage are Copyright (C) crmvillage.
 * All Rights Reserved.
 *******************************************************************************/
class Update {

	var $server;
	var $username;
	var $password;
	var $from_version;
	var $to_version;
	var $use_script = false;

	static public $logPrefix = ''; // crmv@116306

	function Update($server='',$username='',$password='',$from_version='',$to_version='') {
		if ($server != '') $this->server = $server;
		if ($username != '') $this->username = $username;
		if ($password != '') $this->password = $password;
		if ($from_version != '') $this->from_version = $from_version;
		if ($to_version != '') $this->to_version = $to_version;
	}

 	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {

		require_once('include/utils/utils.php');
		global $adb,$mod_strings,$table_prefix;

 		if($eventType == 'module.postinstall') {
			// Mark the module as Standard module
			$adb->pquery('UPDATE '.$table_prefix.'_tab SET customized=0 WHERE name=?', array($moduleName));

//			$fieldid = $adb->getUniqueID('vtiger_settings_field');
//			$blockid = getSettingsBlockId('LBL_STUDIO');
//			$seq_res = $adb->pquery("SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid = ?", array($blockid));
//			if ($adb->num_rows($seq_res) > 0) {
//				$cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
//				if ($cur_seq != null)	$seq = $cur_seq + 1;
//			}

//			$adb->pquery('INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence)
//				VALUES (?,?,?,?,?,?,?)', array($fieldid, $blockid, 'LBL_ST_MANAGER', 'workflow.gif', 'LBL_ST_MANAGER_DESCRIPTION', 'index.php?module=Update&action=index&parenttab=Settings', $seq));


		} else if($eventType == 'module.disabled') {
		// TODO Handle actions when this module is disabled.
		} else if($eventType == 'module.enabled') {
		// TODO Handle actions when this module is enabled.
		} else if($eventType == 'module.preuninstall') {
		// TODO Handle actions when this module is about to be deleted.
		} else if($eventType == 'module.preupdate') {
		// TODO Handle actions before this module is updated.
		} else if($eventType == 'module.postupdate') {
		// TODO Handle actions after this module is updated.
		}
 	}

	function update_changes() {
		require_once('Smarty_setup.php');
		global $adb, $current_language, $table_prefix, $metaLogs; // crmv@49398
 		if ($this->from_version == '' || $this->to_version == '') return false;
 		if($this->use_script && !isFreeVersion()) {
 			$smarty = new vtigerCRM_Smarty;
 			$smarty->assign('CURRENT_LANGUAGE',$current_language);
 			$description = getTranslatedString('LBL_UPDATE_PACK_INVALID','Update');
 			if ($description == 'LBL_UPDATE_PACK_INVALID') {
 				$description = 'This update package is not applicable on your VTE version.<br />Please contact CRMVillage.BIZ or your Partner in order to obtain the correct version.';
 			}
 			$smarty->assign('BODY','<br />'.utf8_decode($description).'<br /><br />');
 			$smarty->display('NoLoginMsg.tpl');
 			exit;
 		}
 		set_time_limit(600);

		// clear cache also before starting
 		if (function_exists('apc_clear_cache')) {
 			@apc_clear_cache();
 		}
		
 		//crmv@18160
 		$_SESSION['skip_recalculate'] = true;
 		$_SESSION['modules_to_install'] = Array();
 		$_SESSION['modules_to_update'] = Array();
 		//crmv@18160 end
 		for ($i_version = $this->from_version; $i_version < $this->to_version; $i_version++) {
 			$change = $i_version.'_'.($i_version+1);
 			if(file_exists("modules/Update/changes/$change.php") && (filesize("modules/Update/changes/$change.php") != 0)) {
 				echo "<br />\n$i_version -> ".($i_version+1)."... ";
 				include("modules/Update/changes/$change.php");
 				echo "DONE";
 			}
 		}
 		//crmv@18160
 		//installo/aggiorno i moduli di VTE...
 		require_once('vtlib/Vtiger/Package.php');
		require_once('vtlib/Vtiger/Language.php');

 		if (is_array($_SESSION['modules_to_install'])){
 			foreach ($_SESSION['modules_to_install'] as $module=>$arr){
	 			$res = $adb->query("SELECT tabid FROM ".$table_prefix."_tab WHERE name = '$module'");
				if ($res && $adb->num_rows($res)>0) {
					unset($_SESSION['modules_to_install'][$module]);
					$_SESSION['modules_to_update'][$module] = $arr;
				}
				else {
	 				$package = new Vtiger_Package();
					$package->import($arr);
				}
 			}
 		}
 		if (is_array($_SESSION['modules_to_update'])){
 			foreach ($_SESSION['modules_to_update'] as $module=>$arr){
 				if (in_array($module,array_keys($_SESSION['modules_to_install'])))
 					continue;
 				if (is_array($arr)){
 					if (is_array($arr[modules])){
						$tmp_dir = "packages/vte/mandatory/tmp";
						mkdir($tmp_dir);
 						foreach ($arr['modules'] as $submodule){
 							$unzip = new Vtiger_Unzip($arr['location']);
							$unzip->unzipAllEx($tmp_dir);
							if($unzip) $unzip->close();
	 						//installo il modulo presente nella cartella temporanea
							$uploadfilename = "packages/vte/mandatory/tmp/$submodule.zip";
							$package = new Vtiger_Package();
							$moduleInstance = Vtiger_Module::getInstance($submodule);
							$package->update($moduleInstance, $uploadfilename);
 						}
 						//cancello la cartella temporanea
						if ($handle = opendir($tmp_dir)) {
							while (false !== ($file = readdir($handle)))
								if(is_file($tmp_dir.'/'.$file))	unlink($tmp_dir.'/'.$file);
							closedir($handle);
							rmdir($tmp_dir);
						}
 					}
 				}
 				else{
 					$package = new Vtiger_Package();
 					$moduleInstance = Vtiger_Module::getInstance($module);
					$package->update($moduleInstance,$arr);
 				}
 			}
 		}
 		unset($_SESSION['modules_to_install']);
 		unset($_SESSION['modules_to_update']);
 		unset($_SESSION['skip_recalculate']);
 		include_once('vtlib/Vtiger/Access.php');
 		include_once('vtlib/Vtiger/ModuleBasic.php');
 		include_once('vtlib/Vtiger/Menu.php');
 		Vtiger_Access::syncSharingAccess();
 		Vtiger_Menu::syncfile();
 		Vtiger_Module::syncfile();
 		//crmv@18160 end

 		//clear cache
 		if (function_exists('apc_clear_cache')) {
 			@apc_clear_cache();
 		}
		$smarty = new vtigerCRM_Smarty;
		@$smarty->clear_all_cache();
		@$smarty->clear_compiled_tpl();
		if (is_dir('smartoptimizer/cache') && is_writable('smartoptimizer/cache')) {
			$files = @glob('smartoptimizer/cache/*', GLOB_NOSORT) ?: array();
			foreach($files as $file) {
				if (is_file($file)) @unlink($file);
			}
		}
		//clear cache end

		if (is_file('install.php')) {
			@unlink('install.php');
		}
		if (is_dir('install')) {
			@folderDetete('install');
		}
		
		// crmv@94125
		// check changed resources
		require_once('include/utils/ResourceVersion.php');
		$RV = ResourceVersion::getInstance();
		$RV->updateResources();
		// crmv@94125e
		
		//crmv@93043
		global $recalculateJsLanguage;
		if (!empty($recalculateJsLanguage)) {
			foreach($recalculateJsLanguage as $lang) {
				$adb->pquery("DELETE FROM sdk_language where language = ? AND module = ?", array($lang, 'ALERT_ARR'));
			}
			SDK::clearSessionValue('sdk_js_lang');
		}
		//crmv@93043e
		
		if ($metaLogs) $metaLogs->log($metaLogs::OPERATION_UPDATED, 0, array('from_revision'=>$this->from_version, 'to_revision'=>$this->to_version)); // crmv@49398
 	}

 	function change_field($tablename,$field,$datatype,$precision,$other_params='',$is_primary_key=false) {
 		//per cambiare il tipo di dato di una colonna che contiene valori
 		global $adb;

 		//passo0: elimino evenutuali indici presenti sul campo
 		$idx_table = $adb->database->MetaIndexes($tablename);
 		$idx_to_recreate = array();
 		if (is_array($idx_table)){
 			$found = false;
 			foreach ($idx_table as $name=>$arr){
 				if (in_array($field,$arr['columns'])) {
 					$adb->datadict->ExecuteSQLArray($adb->datadict->DropIndexSQL($name,$tablename));
 					$idx_to_recreate[$name] = $arr;
 				}
 			}
 		}
		//passo1: creo il nuovo campo
		$field_backup = $field."_backup";
		if ($precision != '') $precision = "($precision)";
		$criteria = "$field_backup $datatype"."$precision $other_params";
//		$adb->startTransaction();
		$sql = $adb->datadict->ChangeTableSQL($tablename,$criteria);
  		if ($sql) {
   			$adb->datadict->ExecuteSQLArray($sql);
   			//passo2: copio i valori nel nuovo campo
   			$adb->query("update $tablename set $field_backup = $field");
   			//passo3: cancello il vecchio campo
   			$sql = $adb->datadict->DropColumnSQL($tablename,$field);
			if ($sql) {
    			$adb->datadict->ExecuteSQLArray($sql);
    			//passo4: rinomino il nuovo campo
    			$sql = $adb->datadict->RenameColumnSQL($tablename,$field_backup,$field,$criteria);
	    		if ($sql) {
	     			$adb->datadict->ExecuteSQLArray($sql);
	     			//passo5: se il campo � primary key
//	     			if ($is_primary_key) {
//		     			$sql = $adb->datadict->ChangeTableSQL($tablename,"$field PRIMARY");
//  					$adb->datadict->ExecuteSQLArray($sql);
//	     			}
	    		}
   			}
  		}
  		//passo6: ripristino gli indici eliminati
  		if (!empty($idx_to_recreate)) {
  			foreach ($idx_to_recreate as $name=>$arr){
  				$options = array();
  				if ($arr['unique']) {
  					$options[] = 'unique';
  				}
  				$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($name, $tablename, $arr['columns'], $options));
  			}
  		}
 	}

 	//crmv@44187 crmv@64542
 	static function create_fields($list) {
 		global $adb, $table_prefix;

 		if (!is_array($list)) return;
 		
 		$ret = array();
 		foreach ($list as $k=>$arr) {

 			$modulo = Vtiger_Module::getInstance($arr['module']);
 			
 			if (!$modulo) {
				self::warn("The field {$arr['name']} has been skipped because the module $modulo was not found.");
				continue;
 			}

 			if (empty($arr['blockid'])) {
 				$block = Vtiger_Block::getInstance($arr['block'], $modulo);
 			} else {
 				$block = Vtiger_Block::getInstance($arr['blockid']);
 			}
 			
 			if (!$block) {
				self::warn("The field {$arr['name']} has been skipped because the block {$arr['block']} was not found.");
				continue;
			}

 			$field = @Vtiger_Field::getInstance($arr['name'], $modulo);

 			if ($field != NULL) {
 				$ret[$k] = $field;
 				continue;
 			} else {
 				$field = new Vtiger_Field();
 				$ret[$k] = $field;
 			}

 			// default values
 			$field->name = $arr['name'];
 			$field->column = $arr['name'];
 			$field->label= $arr['label'];
 			$field->columntype = 'C(255)';
 			$field->typeofdata = 'V~O';
 			$field->uitype = 1;
 			$field->readonly = 1;
 			$field->displaytype = 1;
 			$field->masseditable = 0;
 			$field->quickcreate = 2;
 			$field->table = $modulo->basetable;

 			if (isset($arr['table']) && !empty($arr['table']))
 				$field->table = $arr['table'];

 			if (isset($arr['column']) && !empty($arr['column']))
 				$field->column = $arr['column'];

 			if (isset($arr['readonly']) && !empty($arr['readonly']))
 				$field->readonly = $arr['readonly'];

 			if (isset($arr['presence']))
 				$field->presence = $arr['presence'];

 			if (isset($arr['columntype']) && !empty($arr['columntype']))
 				$field->columntype = $arr['columntype'];

 			if (isset($arr['typeofdata']) && !empty($arr['typeofdata']))
 				$field->typeofdata = $arr['typeofdata'];

 			if (isset($arr['uitype']) && !empty($arr['uitype']))
 				$field->uitype = $arr['uitype'];

 			if (isset($arr['displaytype']))
 				$field->displaytype = $arr['displaytype'];

 			if (isset($arr['quickcreate']))
 				$field->quickcreate = $arr['quickcreate'];

 			if (isset($arr['masseditable']))
 				$field->masseditable = $arr['masseditable'];

			if (isset($arr['helpinfo']))
				$field->helpinfo = $arr['helpinfo'];

 			//se picklist aggiungo i valori
 			if (isset($arr['picklist']) && !empty($arr['picklist'])){
 				$field->setPicklistValues($arr['picklist']);
 			}

 			$block->addField($field);

 			// related modules
 			if (isset($arr['relatedModules']) && !empty($arr['relatedModules'])){
 				$field->setRelatedModules($arr['relatedModules']);
 				foreach ($arr['relatedModules'] as $relmod) {
					if (!isset($arr['relatedModulesAction'][$relmod])) {
						$arr['relatedModulesAction'][$relmod] = array("ADD");
					}
 					$relinst = Vtiger_Module::getInstance($relmod);
 					if ($relinst) {	//crmv@83576
 						$relinst->setRelatedList($modulo, $arr['module'], $arr['relatedModulesAction'][$relmod], 'get_dependents_list');
 					}
 				}
 			}

 			// sdk:uitype, we need to change the uitype by hand
 			if (isset($arr['sdk_uitype']) && !empty($arr['sdk_uitype'])) {
 				$newtype = intval($arr['sdk_uitype']);
 				$adb->pquery("update {$table_prefix}_field set uitype = ? where columnname = ? and tabid = ?", array($newtype, $arr['name'], $modulo->id));
 			}

 		}
 		return $ret;
 	}

	// crmv@104975
 	static function create_blocks($blocklist) {
 		global $adb;

 		if (!is_array($blocklist)) return;

 		$ret = array();
 		foreach ($blocklist as $k=>$arr) {
 			$modulo = Vtiger_Module::getInstance($arr['module']);
 			
 			if (!$modulo) {
				self::warn("The block {$arr['label']} has been skipped because the module $modulo was not found.");
				continue;
 			}

 			$block = @Vtiger_Block::getInstance($arr['label'], $modulo);

 			if ($block != NULL) {
 				$ret[$k] = $block;
 				continue;
 			}

			if (!empty($arr['panelid'])) {
				$panel = Vtiger_Panel::getInstance($arr['panelid']);
 			} elseif (!empty($arr['panel'])) {
 				$panel = Vtiger_Panel::getInstance($arr['panel'], $modulo);
 			} else {
				// get the first for the module
				$panel = Vtiger_Panel::getFirstForModule($modulo);
				if (!$panel) {
					// create an empty one
					$panel = new Vtiger_Panel();
					$panel->label = 'LBL_TAB_MAIN';
					$modulo->addPanel($panel);
				}
 			}
 			
 			if (!$panel) {
				self::warn("The block {$arr['label']} has been skipped because the parent panel was not found.");
				continue;
			}

			$block = new Vtiger_Block();
			$ret[$k] = $block;
			
			$block->panel = $panel;
 			$block->label= $arr['label'];

 			if (isset($arr['sequence']) && !empty($arr['sequence']))
 				$block->sequence = $arr['sequence'];

 			if (isset($arr['showtitle']))
 				$block->showtitle = $arr['showtitle'];

 			if (isset($arr['visible']))
 				$block->visible = $arr['visible'];

 			if (isset($arr['increateview']))
 				$block->increateview = $arr['increateview'];

 			if (isset($arr['ineditview']))
 				$block->ineditview = $arr['ineditview'];

 			if (isset($arr['indetailview']))
 				$block->indetailview = $arr['indetailview'];

 			$modulo->addBlock($block);
 		}
 		return $ret;
 	}

 	static function create_panels($panelslist) {

 		if (!is_array($panelslist)) return;

 		$ret = array();
 		foreach ($panelslist as $k=>$arr) {
 			$modulo = Vtecrm_Module::getInstance($arr['module']);
 			
 			if (!$modulo) {
				self::warn("The panel {$arr['label']} has been skipped because the module $modulo was not found.");
				continue;
 			}

 			$panel = @Vtecrm_Panel::getInstance($arr['label'], $modulo);

 			if ($panel != NULL) {
 				$ret[$k] = $panel;
 				continue;
 			} else {
 				$panel = new Vtecrm_Panel();
 				$ret[$k] = $panel;
 			}

 			$panel->label= $arr['label'];

 			if (isset($arr['sequence']) && !empty($arr['sequence']))
 				$panel->sequence = $arr['sequence'];

 			if (isset($arr['visible']))
 				$panel->visible = $arr['visible'];

 			$modulo->addPanel($panel);
 		}
 		return $ret;
 	}
	// crmv@104975e

 	static function create_filters($filterlist) {
 		global $adb;

 		if (!is_array($filterlist)) return;

 		$ret = array();
 		foreach ($filterlist as $k=>$arr) {

 			$modulo = Vtiger_Module::getInstance($arr['module']);
 			
 			if (!$modulo) {
				self::warn("The filter {$arr['name']} has been skipped because the module $modulo was not found.");
				continue;
 			}

 			$filter = @Vtiger_Filter::getInstance($arr['name'], $modulo);

 			if ($filter != NULL) {
 				$ret[$k] = $filter;
 				continue;
 			} else {
 				$filter = new Vtiger_Filter();
 				$ret[$k] = $filter;
 			}

 			$filter->name = $arr['name'];
 			$filter->isdefault = false;

 			if (isset($arr['isdefault']) && !empty($arr['isdefault']))
 				$filter->isdefault = $arr['isdefault'];

 			$modulo->addFilter($filter);

 			if (isset($arr['fields']) && is_array($arr['fields'])) {
 				$seq = 1;
 				foreach ($arr['fields'] as $fieldname) {
 					$field = Vtiger_Field::getInstance($fieldname, $modulo);
 					if ($field) {
						$filter->addField($field, $seq++);
					} else {
						self::warn("Unable to find the field $fieldname for the filter");
					}
 				}
 			}

 			if (isset($arr['stdrule']) && is_array($arr['stdrule'])) {
 				$rule = $arr['stdrule'];
 				$field = Vtiger_Field::getInstance($rule['fieldname'], $modulo);
 				if ($field) {
					$filter->addStandardRule($field, $rule['duration'], $rule['datestart'], $rule['dateend'], intval($rule['onlymonth']));
				} else {
					self::warn("Unable to find the field {$rule['fieldname']} for the standard rule");
				}
 			}


 			if (isset($arr['rules']) && is_array($arr['rules'])) {
 				$seq = 1;
 				foreach ($arr['rules'] as $rule) {
 					$field = Vtiger_Field::getInstance($rule['fieldname'], $modulo);
 					if ($field) {
						$filter->addRule($field, $rule['comparator'], $rule['value'], $seq++);
					} else {
						self::warn("Unable to find the field {$rule['fieldname']} for the rule");
					}
 				}
 			}
 			
 		}
 		return $ret;
 	}
 	
 	// crmv@116306
 	static function info($message, $delim = true) {
		return self::log('[INFO] '.$message, $delim);
	}
 	
 	static function warn($message, $delim = true) {
		return self::log('[WARNING] '.$message, $delim);
	}
	
 	static function log($message, $delimit = true) {
		echo self::$logPrefix . $message;
		if($delimit) {
			if (php_sapi_name() == 'cli') echo "\n"; else echo "<BR>\n";
		}
	}
 	//crmv@44187e crmv@64542e crmv@116306e

}
