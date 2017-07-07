<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/Utils/StringTemplate.php');

/**
 * Provides API to handle custom links
 * @package vtlib
 */
class Vtiger_Link {
	var $tabid;
	var $linkid;
	var $linktype;
	var $linklabel;
	var $linkurl;
	var $linkicon;
	var $sequence;
	var $status = false;
	var $condition;	//crmv@37303
	var $size;	//crmv@3085m

	// Ignore module while selection
	const IGNORE_MODULE = -1;

	/**
	 * Constructor
	 */
	function __construct() {
	}

	/**
	 * Initialize this instance.
	 */
	function initialize($valuemap) {
		$this->tabid  = $valuemap['tabid'];
		$this->linkid = $valuemap['linkid'];
		$this->linktype = $valuemap['linktype'];
		$this->linklabel = $valuemap['linklabel'];
		//crmv@94125
		if ($this->linktype == 'HEADERSCRIPT') {
			require_once('include/utils/ResourceVersion.php');
			$this->linkurl = resourcever(decode_html($valuemap['linkurl']));
		} else {
			$this->linkurl = decode_html($valuemap['linkurl']);
		}
		//crmv@94125e
		$this->linkicon = decode_html($valuemap['linkicon']);
		$this->sequence = $valuemap['sequence'];
		$this->status = $valuemap['status'];
		$this->condition = $valuemap['cond'];	//crmv@37303
		$this->size = $valuemap['size'];	//crmv@3085m
	}

	/**
	 * Get module name.
	 */
	function module() {
		if(!empty($this->tabid)) {
			return getTabModuleName($this->tabid);
		}
		return false;
	}

	/**
	 * Get unique id for the insertion
	 */
	static function __getUniqueId() {
		global $adb, $table_prefix;
		return $adb->getUniqueID($table_prefix.'_links');
	}

	/** Cache (Record) the schema changes to improve performance */
	static $__cacheSchemaChanges = Array();

	/**
	 * Initialize the schema (tables)
	 */
	static function __initSchema() {
		global $table_prefix;
		if(empty(self::$__cacheSchemaChanges[$table_prefix.'_links'])) {
			if(!Vtiger_Utils::CheckTable($table_prefix.'_links')) {
				Vtiger_Utils::CreateTable(
				$table_prefix.'_links',
					'linkid I(19) NOTNULL PRIMARY,
					tabid I(19), 
					linktype C(20), 
					linklabel C(30), 
					linkurl C(255), 
					linkicon C(100), 
					sequence I(11), 
					status INT(1) NOTNULL DEFAULT 1
					cond C(200)',	//crmv@37303
				true);
				Vtiger_Utils::CreateIndex('link_tabidtype_idx',$table_prefix.'_links','tabid,linktype');
			}
			self::$__cacheSchemaChanges[$table_prefix.'_links'] = true;
		}
	}

	/**
	 * Add link given module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Label to display
	 * @param String HREF value or URL to use for the link
	 * @param String ICON to use on the display
	 * @param Integer Order or sequence of displaying the link
	 */
	static function addLink($tabid, $type, $label, $url, $iconpath='', $sequence=0, $condition='', $size=1) {	//crmv@37303	//crmv@3085m
		global $adb,$table_prefix;
		self::__initSchema();
		$checkres = $adb->pquery('SELECT linkid FROM '.$table_prefix.'_links WHERE tabid=? AND linktype=? AND linkurl=? AND linkicon=? AND linklabel=?',
		Array($tabid, $type, $url, $iconpath, $label));
		if(!$adb->num_rows($checkres)) {
			$uniqueid = self::__getUniqueId();
			//crmv@37303	//crmv@3085m
			$exists_columns = array_keys($adb->datadict->MetaColumns($table_prefix.'_links'));
			$columns = array('linkid','tabid','linktype','linklabel','linkurl','linkicon','sequence');
			$params = array($uniqueid, $tabid, $type, $label, $url, $iconpath, $sequence);
			if (!empty($condition)) {
				$columns[] = 'cond';
				$params[] = $condition;
			}
			if (in_array(strtoupper('size'),$exists_columns)) {
				$columns[] = 'size';
				$params[] = $size;
			}
			$adb->format_columns($columns);
			$adb->pquery('INSERT INTO '.$table_prefix.'_links ('.implode(',',$columns).') VALUES('.generateQuestionMarks($columns).')',$params);
			//crmv@37303e	//crmv@3085me
			self::log("Adding Link ($type - $label) ... DONE");
		}
	}

	/**
	 * Delete link of the module
	 * @param Integer Module ID
	 * @param String Link Type (like DETAILVIEW). Useful for grouping based on pages.
	 * @param String Display label
	 * @param String URL of link to lookup while deleting
	 */
	static function deleteLink($tabid, $type, $label, $url=false) {
		global $adb,$table_prefix;
		self::__initSchema();
		if($url) {
			$adb->pquery('DELETE FROM '.$table_prefix.'_links WHERE tabid=? AND linktype=? AND linklabel=? AND linkurl=?',
			Array($tabid, $type, $label, $url));
			self::log("Deleting Link ($type - $label - $url) ... DONE");
		} else {
			$adb->pquery('DELETE FROM '.$table_prefix.'_links WHERE tabid=? AND linktype=? AND linklabel=?',
			Array($tabid, $type, $label));
			self::log("Deleting Link ($type - $label) ... DONE");
		}
	}

	/**
	 * Delete all links related to module
	 * @param Integer Module ID.
	 */
	static function deleteAll($tabid) {
		global $adb,$table_prefix;
		self::__initSchema();
		$adb->pquery('DELETE FROM '.$table_prefix.'_links WHERE tabid=?', Array($tabid));
		self::log("Deleting Links ... DONE");
	}

	/**
	 * Get all the links related to module
	 * @param Integer Module ID.
	 */
	static function getAll($tabid) {
		return self::getAllByType($tabid);
	}

	/**
	 * Get all the link related to module based on type
	 * @param Integer Module ID
	 * @param mixed String or List of types to select
	 * @param Map Key-Value pair to use for formating the link url
	 */
	static function getAllByType($tabid, $type=false, $parameters=false) {
		global $adb, $current_user,$table_prefix;
		self::__initSchema();

		$multitype = false;

		if($type) {
			$columnSize = 'size';
			$adb->format_columns($columnSize);
			$order_by = 'ORDER BY '.$columnSize.', sequence';	//crmv@3085m
			// Multiple link type selection?
			if(is_array($type)) {
				$multitype = true;
				if($tabid === self::IGNORE_MODULE) {
					$sql = 'SELECT * FROM '.$table_prefix.'_links WHERE linktype IN ('.
					Vtiger_Utils::implodestr('?', count($type), ',') .') ';
					$params = $type;
					$permittedTabIdList = getPermittedModuleIdList();
					if(count($permittedTabIdList) > 0 && $current_user->is_admin !== 'on') {
						//crmv@sdk
						if (isModuleInstalled('SDK')) {
							$permittedTabIdList[] = getTabid('SDK');
						}
						//crmv@sdk e
						$sql .= ' and tabid IN ('.
						Vtiger_Utils::implodestr('?', count($permittedTabIdList), ',').')';
						$params[] = $permittedTabIdList;
					}
					$result = $adb->pquery($sql, Array($adb->flatten_array($params)));
				} else {
					$result = $adb->pquery('SELECT * FROM '.$table_prefix.'_links WHERE tabid=? AND linktype IN ('.
					Vtiger_Utils::implodestr('?', count($type), ',') .') '.$order_by,
					Array($tabid, $adb->flatten_array($type)));
				}
			} else {
				// Single link type selection
				if($tabid === self::IGNORE_MODULE) {
					$result = $adb->pquery('SELECT * FROM '.$table_prefix.'_links WHERE linktype=? '.$order_by, Array($type));
				} else {
					$result = $adb->pquery('SELECT * FROM '.$table_prefix.'_links WHERE tabid=? AND linktype=? '.$order_by, Array($tabid, $type));
				}
			}
		} else {
			$result = $adb->pquery('SELECT * FROM '.$table_prefix.'_links WHERE tabid=?', Array($tabid));
		}

		$strtemplate = new Vtiger_StringTemplate();
		if($parameters) {
			foreach($parameters as $key=>$value) $strtemplate->assign($key, $value);
		}

		$instances = Array();
		if($multitype) {
			foreach($type as $t) $instances[$t] = Array();
		}

		$class = get_called_class() ?: get_class();
		while($row = $adb->fetch_array($result)){
			$instance = new $class();
			$instance->initialize($row);
			//crmv@29984
			if ($instance->linktype == 'DETAILVIEWWIDGET'){
				//disabilito i widget di moduli disabilitati
				global $site_URL;
				//in nome del modulo lo prendo dal linkurl..
				parse_str(parse_url($site_URL."index.php?".$instance->linkurl,PHP_URL_QUERY),$params);
				$module = $params['module'];
				if ($module != '' && !vtlib_isModuleActive($module)){
					continue;
				}
			}
			//crmv@29984e
			//crmv@3085m
			if ($instance->linktype == 'DETAILVIEWBASIC'){
				if (strpos($instance->linkurl,'javascript:') !== false) {
					$instance->linkurl = str_replace('javascript:','',$instance->linkurl);
				} else {
					$instance->linkurl = "location.href='{$instance->linkurl}';";
				}
			}
			//crmv@3085me
			if($parameters) {
				$instance->linkurl = $strtemplate->merge($instance->linkurl);
				$instance->linkicon= $strtemplate->merge($instance->linkicon);
			}
			//crmv@37303
			$check = true;
			if (!empty($instance->condition)) {
				$cond = explode(':',$instance->condition);
				if (count($cond) == 2) {
					require_once($cond[1]);
					$check = $cond[0]($instance);
					if (!$check) {
						continue;
					}
				}
			}
			//crmv@37303e
			
			// crmv@119414
			global $theme;
			$TU = ThemeUtils::getInstance($theme);
			$config = $TU->getAll();
			$menuPosition = $config['primary_menu_position'];
			if ($instance->linktype === 'DETAILVIEWWIDGET' && $menuPosition === 'left') {
				$instance->size = 2;
			}
			// crmv@119414e
			
			if($multitype) {
				$instances[$instance->linktype][] = $instance;
			} else {
				$instances[] = $instance;
			}
		}
		return $instances;
	}

	/**
	 * Helper function to log messages
	 * @param String Message to log
	 * @param Boolean true appends linebreak, false to avoid it
	 * @access private
	 */
	static function log($message, $delimit=true) {
		Vtiger_Utils::Log($message, $delimit);
	}
}
