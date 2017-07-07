<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
include_once('vtlib/Vtecrm/Module.php');
include_once('vtlib/Vtecrm/Menu.php');
include_once('vtlib/Vtecrm/Event.php');
include_once('vtlib/Vtecrm/Zip.php');

class Vtecrm_PackageExport {
	
		var $_export_tmpdir = 'test/vtlib';
	var $_export_modulexml_filename = null;
	var $_export_modulexml_file = null;

	/**
	 * Constructor
	 */
	function Vtiger_PackageExport() {
		if(is_dir($this->_export_tmpdir) === FALSE) {
			mkdir($this->_export_tmpdir);
		}
	}

	/** Output Handlers */

	/** @access private */
	function openNode($node,$delimiter="\n") {
		$this->__write("<$node>$delimiter");
	}
	/** @access private */
	function closeNode($node,$delimiter="\n") {
		$this->__write("</$node>$delimiter");
	}
	/** @access private */
	function outputNode($value, $node='') {
		if($node != '') $this->openNode($node,'');
		$this->__write($value);
		if($node != '') $this->closeNode($node);
	}
	/** @access private */
	function __write($value) {
		fwrite($this->_export_modulexml_file, $value);
	}

	/**
	 * Set the module.xml file path for this export and 
	 * return its temporary path.
	 * @access private
	 */
	function __getManifestFilePath() {
		if(empty($this->_export_modulexml_filename)) {
			// Set the module xml filename to be written for exporting.
			$this->_export_modulexml_filename = "manifest-".time().".xml";
		}
		return "$this->_export_tmpdir/$this->_export_modulexml_filename";
	}

	/**
	 * Initialize Export
	 * @access private
	 */
	function __initExport($module, $moduleInstance) {
		if($moduleInstance->isentitytype) {
			// We will be including the file, so do a security check.
			Vtiger_Utils::checkFileAccess("modules/$module/$module.php");
		}
		$this->_export_modulexml_file = fopen($this->__getManifestFilePath(), 'w');
		$this->__write("<?xml version='1.0'
\n");
	}

	/**
	 * Post export work.
	 * @access private
	 */
	function __finishExport() {
		if(!empty($this->_export_modulexml_file)) {
			fclose($this->_export_modulexml_file);
			$this->_export_modulexml_file = null;
		}
	}

    /**
	 * Clean up the temporary files created.
	 * @access private
     */
	function __cleanupExport() {
		if(!empty($this->_export_modulexml_filename)) {
			unlink($this->__getManifestFilePath());
		}
	}

	/**
	 * Export Module as a zip file.
	 * @param Vtiger_Module Instance of module
	 * @param Path Output directory path
	 * @param String Zipfilename to use
	 * @param Boolean True for sending the output as download
	 */
	function export($moduleInstance, $todir='', $zipfilename='', $directDownload=false) {

		$module = $moduleInstance->name;

		$this->__initExport($module, $moduleInstance);

		// Call module export function
		$this->export_Module($moduleInstance);

		$this->__finishExport();		

		// Export as Zip
		if($zipfilename == '') $zipfilename = "$module-" . date('YmdHis') . ".zip";
		$zipfilename = "$this->_export_tmpdir/$zipfilename";

		$zip = new Vtiger_Zip($zipfilename);
		// Add manifest file
		$zip->addFile($this->__getManifestFilePath(), "manifest.xml");
		SDK::db2FileLanguages($module);	//crmv@sdk-18430		
		// Copy module directory
		$zip->copyDirectoryFromDisk("modules/$module");
		// Copy templates directory of the module (if any)
		if(is_dir("Smarty/templates/modules/$module"))
			$zip->copyDirectoryFromDisk("Smarty/templates/modules/$module", "templates");
		// Copy cron files of the module (if any)
		if(is_dir("cron/modules/$module"))
			$zip->copyDirectoryFromDisk("cron/modules/$module", "cron");
		SDK::exportPackage($module,$zip);	//crmv@sdk
		
		$zip->save();

		if($directDownload) {
			$zip->forceDownload($zipfilename);
			unlink($zipfilename);
		}
		$this->__cleanupExport();
	}

	/**
	 * Export vtiger dependencies
	 * @access private
	 */
	function export_Dependencies($moduleInstance) {
		global $enterprise_current_version, $adb,$table_prefix;
		$moduleid = $moduleInstance->id;
		
		$sqlresult = $adb->query("SELECT * FROM ".$table_prefix."_tab_info WHERE tabid = $moduleid");
		$vtigerMinVersion = $enterprise_current_version;
		$vtigerMaxVersion = false;
		$noOfPreferences = $adb->num_rows($sqlresult);
		for($i=0; $i<$noOfPreferences; ++$i) {
			$prefName = $adb->query_result($sqlresult,$i,'prefname');
			$prefValue = $adb->query_result($sqlresult,$i,'prefvalue');
			if($prefName == 'vtiger_min_version') {
				$vtigerMinVersion = $prefValue;
			}
			if($prefName == 'vtiger_max_version') {
				$vtigerMaxVersion = $prefValue;
			}
			
		}
		
		$this->openNode('dependencies');
		$this->outputNode($vtigerMinVersion, 'vtiger_version');
		if($vtigerMaxVersion !== false)	$this->outputNode($vtigerMaxVersion, 'vtiger_max_version');
		$this->closeNode('dependencies');
	}

	/**
	 * Export Module Handler
	 * @access private
	 */
	function export_Module($moduleInstance) {
		global $adb,$table_prefix;

		$moduleid = $moduleInstance->id;

		$sqlresult = $adb->query("SELECT * FROM ".$table_prefix."_parenttabrel WHERE tabid = $moduleid");
		$parenttabid = $adb->query_result($sqlresult, 0, 'parenttabid');
		$menu = Vtiger_Menu::getInstance($parenttabid);
		$parent_name = $menu->label;

		$sqlresult = $adb->query("SELECT * FROM ".$table_prefix."_tab WHERE tabid = $moduleid");
		$tabresultrow = $adb->fetch_array($sqlresult);

		$tabname = $tabresultrow['name'];
		$tablabel= $tabresultrow['tablabel'];
		$tabversion = isset($tabresultrow['version'])? $tabresultrow['version'] : false;

		$this->openNode('module');		
		$this->outputNode(date('Y-m-d H:i:s'),'exporttime');
		$this->outputNode($tabname, 'name');
		$this->outputNode($tablabel, 'label');
		$this->outputNode($parent_name, 'parent');

		if(!$moduleInstance->isentitytype) {
			$this->outputNode('extension', 'type');
		}

		if($tabversion) {
			$this->outputNode($tabversion, 'version');
		}

		// Export dependency information
		$this->export_Dependencies($moduleInstance);

		// Export module tables
		$this->export_Tables($moduleInstance);
	
		// Export module blocks
		$this->export_Blocks($moduleInstance);

		// Export module filters
		$this->export_CustomViews($moduleInstance);

		// Export Sharing Access
		$this->export_SharingAccess($moduleInstance);

		// Export Events
		$this->export_Events($moduleInstance);		

		// Export Actions
		$this->export_Actions($moduleInstance);

		// Export Related Lists
		$this->export_RelatedLists($moduleInstance);

		// Export Custom Links
		$this->export_CustomLinks($moduleInstance);
		
		SDK::exportXml($moduleInstance);	//crmv@sdk

		$this->closeNode('module');
	}

	/**
	 * Export module base and related tables
	 * @access private
	 */
	function export_Tables($moduleInstance) {
		global $table_prefix;
		$_exportedTables = Array();

		$modulename = $moduleInstance->name;
		$this->openNode('tables');

		if($moduleInstance->isentitytype) {
			$focus = CRMEntity::getInstance($modulename);
			// Setup required module variables which is need for vtlib API's
			vtlib_setup_modulevars($modulename, $focus);
			$tables = Array ($focus->table_name);
			if(!empty($focus->groupTable)) $tables[] = $focus->groupTable[0];
			if(!empty($focus->customFieldTable)) $tables[] = $focus->customFieldTable[0];
			foreach($tables as $table) {
				//crmv@30456 
				$table_real = $table;
				if(strpos($table, $table_prefix) !== false){
					$table_real = str_replace($table_prefix.'_', 'TABLEPREFIX_', $table);
				}
				$this->openNode('table');
				$this->outputNode($table_real, 'name');
				$this->outputNode('<![CDATA['.Vtiger_Utils::CreateTableSchema($table).']]>', 'sql');
				$this->closeNode('table');
				$_exportedTables[] = $table_real;
				//crmv@30456e
			}
			
		}
		
		// Now export table information recorded in schema file
		if(file_exists("modules/$modulename/schema.xml")) {
			$schema = simplexml_load_file("modules/$modulename/schema.xml");

			if(!empty($schema->tables) && !empty($schema->tables->table)) {
				foreach($schema->tables->table as $tablenode) {
					$table = trim($tablenode->name);
					//crmv@30456
					$table_real = $table;
					if(strpos($table, $table_prefix) !== false){
						$table_real = str_replace($table_prefix.'_', 'TABLEPREFIX_', $table);
					}
					if(!in_array($table_real,$_exportedTables)) {
						$this->openNode('table');
						$this->outputNode($table_real, 'name');
						$this->outputNode('<![CDATA['.Vtiger_Utils::CreateTableSchema($table).']]>', 'sql');
						$this->closeNode('table');
						$_exportedTables[] = $table_real;
					}
					//crmv@30456e
				}
			}
		}
		$this->closeNode('tables');
	}

	/**
	 * Export module blocks with its related fields
	 * @access private
	 */
	function export_Blocks($moduleInstance) {
		global $adb,$table_prefix;
		$sqlresult = $adb->pquery("SELECT * FROM ".$table_prefix."_blocks WHERE tabid = ?", Array($moduleInstance->id));
		$resultrows= $adb->num_rows($sqlresult);

		if(empty($resultrows)) return;

		$this->openNode('blocks');
		for($index = 0; $index < $resultrows; ++$index) {
			$blockid    = $adb->query_result($sqlresult, $index, 'blockid');
			$blocklabel = $adb->query_result($sqlresult, $index, 'blocklabel');
			$this->openNode('block');
			$this->outputNode($blocklabel, 'label');
			// Export fields associated with the block
			$this->export_Fields($moduleInstance, $blockid);
			$this->closeNode('block');
		}
		$this->closeNode('blocks');
	}

	/**
	 * Export fields related to a module block
	 * @access private
	 */
	function export_Fields($moduleInstance, $blockid) {
		global $adb,$table_prefix;
		$fieldresult = $adb->pquery("SELECT * FROM {$table_prefix}_field WHERE tabid=? AND block=?", Array($moduleInstance->id, $blockid));
		$fieldcount = $adb->num_rows($fieldresult);
		if(empty($fieldcount)) return;

		$entityresult = $adb->pquery("SELECT * FROM ".$table_prefix."_entityname WHERE tabid=?", Array($moduleInstance->id));
		$entity_fieldname = $adb->query_result($entityresult, 0, 'fieldname');

		$this->openNode('fields');
		for($index = 0; $index < $fieldcount; ++$index) {
			$this->openNode('field');
			$fieldresultrow = $adb->fetch_row($fieldresult);
			//crmv@30456 
			if(strpos($fieldresultrow['tablename'], $table_prefix) !== false){
				$fieldresultrow['tablename']=str_replace($table_prefix.'_', 'TABLEPREFIX_', $fieldresultrow['tablename']);
			}
			//crmv@30456e
			
			$fieldname = $fieldresultrow['fieldname'];
			$uitype = $fieldresultrow['uitype'];
			$fieldid = $fieldresultrow['fieldid'];
			$this->outputNode($fieldname, 'fieldname');	
			$this->outputNode($uitype,    'uitype');
			$this->outputNode($fieldresultrow['columnname'],'columnname');			
			$this->outputNode($fieldresultrow['tablename'],     'tablename');
			$this->outputNode($fieldresultrow['generatedtype'], 'generatedtype');
			$this->outputNode($fieldresultrow['fieldlabel'],    'fieldlabel');
			$this->outputNode($fieldresultrow['readonly'],      'readonly');
			$this->outputNode($fieldresultrow['presence'],      'presence');
			$this->outputNode($fieldresultrow['selected'],      'selected');
			$this->outputNode($fieldresultrow['sequence'],      'sequence');
			$this->outputNode($fieldresultrow['maximumlength'], 'maximumlength');
			$this->outputNode($fieldresultrow['typeofdata'],    'typeofdata');
			$this->outputNode($fieldresultrow['quickcreate'],   'quickcreate');
			$this->outputNode($fieldresultrow['quickcreatesequence'],   'quickcreatesequence');
			$this->outputNode($fieldresultrow['displaytype'],   'displaytype');
			$this->outputNode($fieldresultrow['info_type'],     'info_type');
			$this->outputNode('<![CDATA['.$fieldresultrow['helpinfo'].']]>', 'helpinfo');
			if(isset($fieldresultrow['masseditable'])) {
				$this->outputNode($fieldresultrow['masseditable'], 'masseditable');
			}

			// Export Entity Identifier Information
			if($fieldname == $entity_fieldname) {
				$this->openNode('entityidentifier');
				$this->outputNode($adb->query_result($entityresult, 0, 'entityidfield'),    'entityidfield');
				$this->outputNode($adb->query_result($entityresult, 0, 'entityidcolumn'), 'entityidcolumn');
				$this->closeNode('entityidentifier');
			}

			// Export picklist values for picklist fields
			if($uitype == '15' || $uitype == '16' || $uitype == '111' || $uitype == '33' || $uitype == '55') {

				if($uitype == '16') {
					$picklistvalues = vtlib_getPicklistValues($fieldname);
				} else {
					$picklistvalues = vtlib_getPicklistValues_AccessibleToAll($fieldname);
				}
				$this->openNode('picklistvalues');
				foreach($picklistvalues as $picklistvalue) {
					$this->outputNode($picklistvalue, 'picklistvalue');
				}
				$this->closeNode('picklistvalues');
			}

			// Export field to module relations
			if($uitype == '10') {
				$relatedmodres = $adb->pquery("SELECT * FROM ".$table_prefix."_fieldmodulerel WHERE fieldid=?", Array($fieldid));
				$relatedmodcount = $adb->num_rows($relatedmodres);
				if($relatedmodcount) {
					$this->openNode('relatedmodules');
					for($relmodidx = 0; $relmodidx < $relatedmodcount; ++$relmodidx) {
						$this->outputNode($adb->query_result($relatedmodres, $relmodidx, 'relmodule'), 'relatedmodule');
					}
					$this->closeNode('relatedmodules');
				}
			}

			$this->closeNode('field');

		}
		$this->closeNode('fields');
	}

	/**
	 * Export Custom views of the module
	 * @access private
	 */
	function export_CustomViews($moduleInstance) {
		global $adb,$table_prefix;

		$customviewres = $adb->pquery("SELECT * FROM ".$table_prefix."_customview WHERE entitytype = ?", Array($moduleInstance->name));
		$customviewcount=$adb->num_rows($customviewres);

		if(empty($customviewcount)) return;

		$this->openNode('customviews');
		for($cvindex = 0; $cvindex < $customviewcount; ++$cvindex) {

			$cvid = $adb->query_result($customviewres, $cvindex, 'cvid');

			$cvcolumnres = $adb->query("SELECT * FROM ".$table_prefix."_cvcolumnlist WHERE cvid=$cvid");
			$cvcolumncount=$adb->num_rows($cvcolumnres);

			$this->openNode('customview');

			$setdefault = $adb->query_result($customviewres, $cvindex, 'setdefault');
			$setdefault = ($setdefault == 1)? 'true' : 'false';

			$setmetrics = $adb->query_result($customviewres, $cvindex, 'setmetrics');
			$setmetrics = ($setmetrics == 1)? 'true' : 'false';

			$this->outputNode($adb->query_result($customviewres, $cvindex, 'viewname'),   'viewname');
			$this->outputNode($setdefault, 'setdefault');
			$this->outputNode($setmetrics, 'setmetrics');

			$this->openNode('fields');
			for($index = 0; $index < $cvcolumncount; ++$index) {
				$cvcolumnindex = $adb->query_result($cvcolumnres, $index, 'columnindex');
				$cvcolumnname = $adb->query_result($cvcolumnres, $index, 'columnname');
				$cvcolumnnames= explode(':', $cvcolumnname);
				$cvfieldname = $cvcolumnnames[2];

				$this->openNode('field');
				$this->outputNode($cvfieldname, 'fieldname');
				$this->outputNode($cvcolumnindex,'columnindex');

				$cvcolumnruleres = $adb->pquery("SELECT * FROM ".$table_prefix."_cvadvfilter WHERE cvid=? AND columnname=?",
					Array($cvid, $cvcolumnname));
				$cvcolumnrulecount = $adb->num_rows($cvcolumnruleres);

				if($cvcolumnrulecount) {
					$this->openNode('rules');
					for($rindex = 0; $rindex < $cvcolumnrulecount; ++$rindex) {
						$cvcolumnruleindex = $adb->query_result($cvcolumnruleres, $rindex, 'columnindex');
						$cvcolumnrulecomp  = $adb->query_result($cvcolumnruleres, $rindex, 'comparator');
						$cvcolumnrulevalue = $adb->query_result($cvcolumnruleres, $rindex, 'value');
						$cvcolumnrulecomp  = Vtiger_Filter::translateComparator($cvcolumnrulecomp, true);

						$this->openNode('rule');
						$this->outputNode($cvcolumnruleindex, 'columnindex');
						$this->outputNode($cvcolumnrulecomp, 'comparator');
						$this->outputNode($cvcolumnrulevalue, 'value');
						$this->closeNode('rule');

					}
					$this->closeNode('rules');
				}

				$this->closeNode('field');
			}
			$this->closeNode('fields');

			$this->closeNode('customview');
		}
		$this->closeNode('customviews');
	}

	/**
	 * Export Sharing Access of the module
	 * @access private
	 */
	function export_SharingAccess($moduleInstance) {
		global $adb,$table_prefix;

		$deforgshare = $adb->pquery("SELECT * FROM ".$table_prefix."_def_org_share WHERE tabid=?", Array($moduleInstance->id));
		$deforgshareCount = $adb->num_rows($deforgshare);

		if(empty($deforgshareCount)) return;

		$this->openNode('sharingaccess');
		if($deforgshareCount) {
			for($index = 0; $index < $deforgshareCount; ++$index) {
				$permission = $adb->query_result($deforgshare, $index, 'permission');
				$permissiontext = '';
				if($permission == '0') $permissiontext = 'public_readonly';
				if($permission == '1') $permissiontext = 'public_readwrite';
				if($permission == '2') $permissiontext = 'public_readwritedelete';
				if($permission == '3') $permissiontext = 'private';

				$this->outputNode($permissiontext, 'default');
			}
		}
		$this->closeNode('sharingaccess');		
	}

	/**
	 * Export Events of the module
	 * @access private
	 */
	function export_Events($moduleInstance) {
		$events = Vtiger_Event::getAll($moduleInstance);
		if(!$events) return;

		$this->openNode('events');
		foreach($events as $event) {
			$this->openNode('event');
			$this->outputNode($event->eventname, 'eventname');
			$this->outputNode('<![CDATA['.$event->classname.']]>', 'classname');
			$this->outputNode('<![CDATA['.$event->filename.']]>', 'filename');
			$this->outputNode('<![CDATA['.$event->condition.']]>', 'condition');
			$this->closeNode('event');
		}
		$this->closeNode('events');
	}

	/**
	 * Export actions (tools) associated with module.
	 * TODO: Need to pickup values based on status for all user (profile)
	 * @access private
	 */
	function export_Actions($moduleInstance) {

		if(!$moduleInstance->isentitytype) return;

		global $adb,$table_prefix;
		$result = $adb->pquery('SELECT distinct(actionname) FROM '.$table_prefix.'_profile2utility, '.$table_prefix.'_actionmapping 
			WHERE '.$table_prefix.'_profile2utility.activityid='.$table_prefix.'_actionmapping.actionid and tabid=?', Array($moduleInstance->id));

		if($adb->num_rows($result)) {
			$this->openNode('actions');
			while($resultrow = $adb->fetch_array($result)) {
				$this->openNode('action');
				$this->outputNode('<![CDATA['. $resultrow['actionname'] .']]>', 'name');
				$this->outputNode('enabled', 'status');
				$this->closeNode('action');
			}
			$this->closeNode('actions');
		}
	}

	/**
	 * Export related lists associated with module.
	 * @access private
	 */
	function export_RelatedLists($moduleInstance) {

		if(!$moduleInstance->isentitytype) return;

		global $adb,$table_prefix;
		$result = $adb->pquery("SELECT * FROM ".$table_prefix."_relatedlists WHERE tabid = ?", Array($moduleInstance->id));
		if($adb->num_rows($result)) {
			$this->openNode('relatedlists');

			for($index = 0; $index < $adb->num_rows($result); ++$index) {
				$row = $adb->fetch_array($result);
				$this->openNode('relatedlist');

				$this->outputNode($row['name'], 'function');
				$this->outputNode($row['label'], 'label');
				$this->outputNode($row['sequence'], 'sequence');
				$this->outputNode($row['presence'], 'presence');

				$action_text = $row['actions'];
				if(!empty($action_text)) {
					$this->openNode('actions');
					$actions = explode(',', $action_text);
					foreach($actions as $action) {
						$this->outputNode($action, 'action');
					}
					$this->closeNode('actions');
				}
				
				$relModuleInstance = Vtiger_Module::getInstance($row['related_tabid']);
				$this->outputNode($relModuleInstance->name, 'relatedmodule');

				$this->closeNode('relatedlist');
			}

			$this->closeNode('relatedlists');
		}
	}

	/**
	 * Export custom links of the module.
	 * @access private
	 */
	function export_CustomLinks($moduleInstance) {
		$customlinks = $moduleInstance->getLinks();
		if(!empty($customlinks)) {
			$this->openNode('customlinks');
			foreach($customlinks as $customlink) {
				$this->openNode('customlink');
				$this->outputNode($customlink->linktype, 'linktype');
				$this->outputNode($customlink->linklabel, 'linklabel');
				$this->outputNode("<![CDATA[$customlink->linkurl]]>", 'linkurl');
				$this->outputNode("<![CDATA[$customlink->linkicon]]>", 'linkicon');
				$this->outputNode($customlink->sequence, 'sequence');
				$this->closeNode('customlink');
			}
			$this->closeNode('customlinks');
		}
	}
	
	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delim=true) {
		Vtiger_Utils::Log($message, $delim);
	}
}

