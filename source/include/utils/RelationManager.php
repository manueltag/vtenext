<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/*
 * crmv@37004 - get relations between modules
 * crmv@97862 - support for fake relationid for inventory relations
 */

// represent a single relation between 2 modules
class ModuleRelation {

	// relation type
	public static $TYPE_1TO1 = 1;	// TODO
	public static $TYPE_1TON = 2;
	public static $TYPE_NTO1 = 4;
	public static $TYPE_NTON = 8;

	public static $TYPE_ALL = 0x0F;

	// modules
	protected $type;
	protected $module1;
	protected $module2;

	// relation info 1-N, N-1
	public $fieldid;
	public $fieldname;
	public $fieldtable;
	public $fieldcolumn;

	// relation info N-N
	public $relationid;
	public $relationinfo; // crmv@49398
	
	function __construct($module1, $module2, $type) {
		$this->module1 = $module1;
		$this->module2 = $module2;
		$this->type = $type;
	}

	function getFirstModule() {
		return $this->module1;
	}

	function getSecondModule() {
		return $this->module2;
	}

	function getType() {
		return $this->type;
	}

	function getFieldId() {
		return $this->fieldid;
	}
	//crmv@47905
	function getNtoNinfo(){
		global $table_prefix;
		
		if ($this->module1 == 'ProductsBlock') {
			$mod1Inst = new stdClass();
		} else {
			$mod1Inst = CRMEntity::getInstance($this->module1);
		}
		if ($this->module2 == 'ProductsBlock') {
			$mod2Inst = new stdClass();
		} else {
			$mod2Inst = CRMEntity::getInstance($this->module2);
		}
		// module1 -> module2
		$reltab = $mod1Inst->relation_table;
		$relidxCrmid = $mod1Inst->table_index;
		$relidx = $mod1Inst->relation_table_id;
		$relidx2 = $mod1Inst->relation_table_otherid;
		$relmod1 = $mod1Inst->relation_table_module;
		$relmod2 = $mod1Inst->relation_table_othermodule;
		//crmv@43864
		if ($this->module1 == 'Messages') {
			$relidxCrmid = 'messagehash';
		} elseif (isInventoryModule($this->module1) && isProductModule($this->module2)) {
			// inventorymod->products
			$reltab = $table_prefix."_inventoryproductrel";
			$relidx = 'id';
			$relidx2 = 'productid';
			$relmod1 = $relmod2 = '';
		} elseif (isProductModule($this->module1) && isInventoryModule($this->module2)) {
			// products->inventorymod
			$reltab = $table_prefix."_inventoryproductrel";
			$relidx = 'productid';
			$relidx2 = 'id';
			$relmod1 = $relmod2 = '';
		} elseif ($this->module1 == 'PriceBooks' && isProductModule($this->module2)) {
			$reltab = $table_prefix."_pricebookproductrel";
			$relidx = 'pricebookid';
			$relidx2 = 'productid';
			$relmod1 = $relmod2 = '';
		} elseif (isProductModule($this->module1) && $this->module2 == 'PriceBooks') {
			$reltab = $table_prefix."_pricebookproductrel";
			$relidx = 'productid';
			$relidx2 = 'pricebookid';
			$relmod1 = $relmod2 = '';
			// crmv@44187
			// TODO: use this system also for products/pricebooks/inventory
		} elseif ($this->module2 == 'Products' || $this->module2 == 'Documents'){
			$reltab = $mod2Inst->relation_table;
			$relidx = $mod2Inst->relation_table_otherid;
			$relidx2 = $mod2Inst->relation_table_id;
			$relmod1 = $relmod2 = '';
		} else if (!empty($mod1Inst->extra_relation_tables[$this->module2])) {
			$reltab = $mod1Inst->extra_relation_tables[$this->module2]['relation_table'];
			$relidx = $mod1Inst->extra_relation_tables[$this->module2]['relation_table_id'];
			$relidx2 = $mod1Inst->extra_relation_tables[$this->module2]['relation_table_otherid'];
			$relmod1 = $mod1Inst->extra_relation_tables[$this->module2]['relation_table_module'];
			$relmod2 = $mod1Inst->extra_relation_tables[$this->module2]['relation_table_othermodule'];
		}
		// crmv@44187e
		return Array(
			'reltab'=>$reltab,
			'relidxCrmid'=>$relidxCrmid,
			'relidx'=>$relidx,
			'relidx2'=>$relidx2,
			'relmod1'=>$relmod1,
			'relmod2'=>$relmod2,
		);
	}
	//crmv@47905 e
	// returns related ids for this relation, starting with specified module
	// TODO: i permessi!!
	// TODO: fix limit for N-N relations
	// crmv@43611	crmv@51605
	function getRelatedIds($crmid, $start = 0, $limit = 0, $onlycount = false, $use_user_permissions = false) {
		global $adb, $table_prefix, $current_user;

		$ret = array();
		$count = 0;
		
		switch ($this->type) {
			case self::$TYPE_1TON: {
				// fieldname is in secondary module
				$r = self::searchFieldValue($this->fieldid, $crmid, $start, $limit, $use_user_permissions);
				if ($r) {
					$ret = $r;
					++$count;
				}
				break;
			}
			case self::$TYPE_NTO1: {
				// TODO $use_user_permissions
				// fieldname is in primary module
				if (!empty($this->fieldid)) {
					$v = getFieldValue($this->fieldid, $crmid);
					if ($v && getSalesEntityType($v) == $this->module2) { // crmv@44187
						$ret[] = $v;
						++$count;
					}
				}
				break;
			}
			case self::$TYPE_NTON: {
				$ntoninfo = $this->getNtoNinfo(); //crmv@47905
				$mod1Inst = CRMEntity::getInstance($this->module1);
				$mod2Inst = CRMEntity::getInstance($this->module2);
				$reltab = $ntoninfo['reltab'];
				$relidxCrmid = $ntoninfo['relidxCrmid'];
				$relidx = $ntoninfo['relidx'];
				$relidx2 = $ntoninfo['relidx2'];
				$relmod1 = $ntoninfo['relmod1'];
				$relmod2 = $ntoninfo['relmod2'];
				//crmv@43864e
				$query =
					"select ".($onlycount ? 'count('.$table_prefix.'_crmentity2.crmid) as cnt' : $table_prefix.'_crmentity2.crmid')."
					from $reltab r
						inner join {$mod1Inst->table_name} mtab on mtab.$relidxCrmid = r.$relidx
						inner join {$table_prefix}_crmentity crm on crm.crmid = mtab.{$mod1Inst->table_index}
						inner join {$table_prefix}_crmentity {$table_prefix}_crmentity2 on {$table_prefix}_crmentity2.crmid = r.$relidx2";
				$query .= " inner join {$mod2Inst->table_name} on {$mod2Inst->table_name}.{$mod2Inst->table_index} = {$table_prefix}_crmentity2.crmid";
				if ($use_user_permissions) {
					//crmv@26650
					if ($this->module2 == 'Calendar')
						$query .= " inner join {$mod2Inst->table_name} {$mod2Inst->table_name}2 on {$mod2Inst->table_name}2.{$mod2Inst->table_index} = {$table_prefix}_crmentity2.crmid";
					//crmv@26650e
					$query .= $mod2Inst->getNonAdminAccessControlQuery($this->module2, $current_user, '2');
				}
				$query .= " where crm.deleted = 0 and {$table_prefix}_crmentity2.deleted = 0 and mtab.{$mod1Inst->table_index} = ? and {$table_prefix}_crmentity2.setype = ?";
				//crmv@52414
				if ($this->module2 == 'Leads') {
					$query .= " and {$mod2Inst->table_name}.converted = 0";
				}
				//crmv@52414e
				if ($use_user_permissions) {
					$query = $mod2Inst->listQueryNonAdminChange($query, $this->module2, '2');
				}
				$params = array($crmid, $this->module2);
				if ($relmod1) {
					$query .= " and r.$relmod1 = ?";
					$params[] = $this->module1;
				}
				if ($relmod2) {
					$query .= " and r.$relmod2 = ?";
					$params[] = $this->module2;
				}

				if ($reltab && $relidxCrmid && $relidx && $relidx2) {
					if ($limit > 0 && !$onlycount) {
						$res = $adb->limitpQuery($query, $start, $limit, $params);
					} else {
						$res = $adb->pquery($query, $params);
					}
					if ($res && $adb->num_rows($res) > 0) {
						if ($onlycount) {
							$count += intval($adb->query_result_no_html($res, 0, 'cnt'));
						} else {
							while ($row = $adb->FetchByAssoc($res, -1, false)) {
								$ret[] = $row['crmid'];
							}
						}
					}
				}

				// module2 -> module1
				$reltab = $mod2Inst->relation_table;
				$relidxCrmid = $mod2Inst->table_index;
				$relidx = $mod2Inst->relation_table_id;
				$relidx2 = $mod2Inst->relation_table_otherid;
				$relmod1 = $mod2Inst->relation_table_module;
				$relmod2 = $mod2Inst->relation_table_othermodule;
				if ($this->module2 == 'Messages') {
					$relidxCrmid = 'messagehash';
					global $onlyquery;
					$onlyquery = true;
					$mod1Inst = CRMEntity::getInstance($this->module1);
					//crmv@60771
					include_once('vtlib/Vtecrm/Module.php');
					$module1_instance = Vtecrm_Module::getInstance($this->module1);
					$module2_instance = Vtecrm_Module::getInstance($this->module2);
					//crmv@60771e
					//crmv@97260
					require('user_privileges/requireUserPrivileges.php');
					if($profileTabsPermission[$module2_instance->id] == 0){ //se l'utente loggato ha il modulo attivo nel profilo allora procedo
						$mod1Inst->get_messages_list($crmid, $module1_instance->id, $module2_instance->id);
						$query = $_SESSION[strtolower($this->module2)."_listquery"];
						$params = array();
					} else {
						$relidxCrmid = '';
					}
					//crmv@97260e
				} else {
					$query =
						"select ".($onlycount ? 'count('.$table_prefix.'_crmentity.crmid) as cnt' : $table_prefix.'_crmentity.crmid')."
						from $reltab r
							inner join {$mod2Inst->table_name} on {$mod2Inst->table_name}.$relidxCrmid = r.$relidx
							inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$mod2Inst->table_name}.{$mod2Inst->table_index}
							inner join {$table_prefix}_crmentity crm2 on crm2.crmid = r.$relidx2";
					if ($use_user_permissions) {
						$query .= $mod2Inst->getNonAdminAccessControlQuery($this->module2, $current_user);
					}
					$query .= " where {$table_prefix}_crmentity.deleted = 0 and crm2.deleted = 0 and crm2.crmid = ? and {$table_prefix}_crmentity.setype = ?";
					//crmv@52414
					if ($this->module2 == 'Leads') {
						$query .= " and {$mod2Inst->table_name}.converted = 0";
					}
					//crmv@52414e
					if ($use_user_permissions) {
						$query = $mod2Inst->listQueryNonAdminChange($query, $this->module2);
					}
					$params = array($crmid, $this->module2);
					if ($relmod1) {
						$query .= " and r.$relmod1 = ?";
						$params[] = $this->module2;
					}
					if ($relmod2) {
						$query .= " and r.$relmod2 = ?";
						$params[] = $this->module1;
					}
				}
				
				if ($reltab && $relidxCrmid && $relidx && $relidx2) {
					if ($limit > 0 && !$onlycount) {
						$res = $adb->limitpQuery($query, $start, $limit, $params);
					} else {
						$res = $adb->pquery($query, $params);
					}
					if ($res && $adb->num_rows($res) > 0) {
						if ($onlycount) {
							$count += intval($adb->query_result_no_html($res, 0, 'cnt'));
						} else {
							while ($row = $adb->FetchByAssoc($res, -1, false)) {
								$ret[] = $row['crmid'];
							}
						}
					}
				}

				break;
			}
		}

		return ($onlycount ? $count : $ret);
	}
	
	//crmv@OPER4380
	function getRelatedIdsExtra($crmid, $start = 0, $limit = 0, $onlycount = false) {
		global $adb, $table_prefix,$current_user;
		$ret = array();
		$count = 0;
		include_once('include/Webservices/Extra/WebserviceExtra.php');
		return WebserviceExtra::getRelatedIds($this->module1,$this->module2,$crmid, $start, $limit, $onlycount);
	}	
	//crmv@OPER4380 e

	// crmv@49398
	/**
	 *
	 * Returns an instance of ModuleRelation using the relationid
	 * Supported only N-to-N relations for now
	 */
	static function createFromRelationId($relationId) {
		global $adb, $table_prefix;

		$relation = null;
		$nton_functions = array('get_related_list', 'get_related_list_target', 'get_messages_list', 'get_documents_dependents_list', 'get_attachments', 'get_campaigns_newsletter', 'get_newsletter_emails', 'get_faxes', 'get_sms', 'get_services', 'get_products', 'get_pricebook_products', 'get_product_pricebooks'); // crmv@98500

		if (self::isFakeRelationId($relationId)) {
			$mods = self::getModulesFromFakeRelationId($relationId);
			$relinfo = array(
				'module' => $mods[0],
				'relmodule' => $mods[1],
			);
		} else {
			$res = $adb->pquery(
				"select r.*, t1.name as module, t2.name as relmodule
				from {$table_prefix}_relatedlists r
				inner join {$table_prefix}_tab t1 on t1.tabid = r.tabid
				inner join {$table_prefix}_tab t2 on t2.tabid = r.related_tabid
				where relation_id = ?", array($relationId)
			);
			if (!$res || $adb->num_rows($res) <= 0) return $relation;
			
			$relinfo = $adb->FetchByAssoc($res, -1, false);
		}
		
		// hack for the stupid calendar-contacts relation
		// should be N-N, but of course, there's a stupid field!
		if ($relinfo['module'] == 'Calendar' && $relinfo['relmodule'] == 'Contacts') {
			$nton_functions[] = 'get_contacts';
		} elseif ($relinfo['module'] == 'Contacts' && $relinfo['relmodule'] == 'Calendar') {
			$nton_functions[] = 'get_activities';
		}

		// determine type
		if (in_array($relinfo['name'], $nton_functions)) {
			$type = self::$TYPE_NTON;
		} elseif (isInventoryModule($relinfo['module']) && isProductModule($relinfo['relmodule'])) {
			$type = self::$TYPE_NTON;
		} elseif (isInventoryModule($relinfo['relmodule']) && isProductModule($relinfo['module'])) {
			$type = self::$TYPE_NTON;
		}

		if ($type) {
			$relation = new ModuleRelation($relinfo['module'], $relinfo['relmodule'], $type);
			if ($type == $type = self::$TYPE_NTON) {
				$relation->relationid = $relationId;
				$relation->relationinfo = $relation->getNtoNinfo();
			}
		}

		return $relation;
	}
	// crmv@49398e

	// crmv@96233
	
	/**
	 * Generates a relationid for the inventory relation.
	 * The generated id is not overlapping the real relationids
	 */
	static function generateFakeRelationId($module1, $module2) {
		$relid = null;
		
		$baseRelid = 10000;
		
		if ($module1 == 'ProductsBlock') {
			$tabid1 = 200;
			$tabid2 = getTabid($module2);
			$relid = $baseRelid + 200 * $tabid1 + $tabid2;
		} elseif ($module2 == 'ProductsBlock') {
			$tabid1 = getTabid($module1);
			$tabid2 = 200;
			$relid = $baseRelid + 200 * $tabid1 + $tabid2;
		} elseif ((isInventoryModule($module1) && isProductModule($module2)) ||
			(isInventoryModule($module2) && isProductModule($module1))) {
			$tabid1 = getTabid($module1);
			$tabid2 = getTabid($module2);
			// I assume that the max tabid is < 200
			$relid = $baseRelid + 200 * $tabid1 + $tabid2;
		} else {
			// invalid modules 
		}
		return $relid;
	}
	
	static function isFakeRelationId($relid) {
		return ($relid >= 10000);
	}
	
	static function isFakePBFieldId($fieldid) {
		return ($fieldid == 10001 || $fieldid == 10002);
	}
	
	static function getModulesFromFakeRelationId($relid) {
		$baseRelid = 10000;
		
		if ($relid >= $baseRelid) {
			$relid -= $baseRelid;
			$tabid1 = (int)($relid / 200);
			$tabid2 = (int)($relid % 200);
			$module1 = ($tabid1 == 200 ? 'ProductsBlock' : getTabName($tabid1));
			$module2 = ($tabid2 == 200 ? 'ProductsBlock' : getTabName($tabid2));
			return array($module1, $module2);
		}
		return null;
	}
	
	/**
	 * Returns an array of instances of ModuleRelation using the fieldid (must be an uitype 10)
	 * The relations returned are N-to-1
	 */
	static function createFromFieldId($fieldId) {
		global $adb, $table_prefix;
		
		if (self::isFakePBFieldId($fieldId)) {
			return self::createFromPBFieldId($fieldId);
		}
		
		$relations = array();
		
		// get field info
		$res = $adb->pquery("SELECT t.tabid, t.name, f.uitype FROM {$table_prefix}_tab t INNER JOIN {$table_prefix}_field f ON f.tabid = t.tabid WHERE f.fieldid = ?", array($fieldId));
		if ($res && $adb->num_rows($res) > 0) {
			$fieldInfo = $adb->FetchByAssoc($res, -1, false);
			$module = $fieldInfo['name'];
		} else {
			return $relations;
		}
		
		// TODO: handle the special uitypes
		if ($fieldInfo['uitype'] != 10) return $relations;
		
		$query = "select fmr.fieldid,fmr.relmodule, f.fieldname, f.tablename, f.columnname from {$table_prefix}_fieldmodulerel fmr inner join {$table_prefix}_field f on f.fieldid = fmr.fieldid where fmr.fieldid = ?";
		$params = array($fieldId);

		$res = $adb->pquery($query, $params);
		if ($res) {
			while ($row = $adb->FetchByAssoc($res, -1, false)) {
				$newrel = new ModuleRelation($module, $row['relmodule'], ModuleRelation::$TYPE_NTO1);
				$newrel->fieldid = $row['fieldid'];
				$newrel->fieldname = $row['fieldname'];
				$newrel->fieldtable = $row['tablename'];
				$newrel->fieldcolumn = $row['columnname'];
				$relations[] = $newrel;
			}
		}
		
		return $relations;
	}
	
	static function createFromPBFieldId($fieldId) {
		global $adb, $table_prefix;
		
		$relations = array();
		
		if ($fieldId == 10001) {
			$listmod = getInventoryModules();
			$fieldname = 'id';
		} elseif ($fieldId == 10002) {
			$listmod = getProductModules();
			$fieldname = 'productid';
		}
		foreach ($listmod as $mod) {
			$newrel = new ModuleRelation('ProductsBlock', $mod, ModuleRelation::$TYPE_NTO1);
			$newrel->fieldid = $fieldId;
			$newrel->fieldname = $fieldname;
			$newrel->fieldtable = $table_prefix.'_inventoryproductrel';
			$newrel->fieldcolumn = $fieldname;
			$relations[] = $newrel;
		}
		return $relations;
	}
		
	// crmv@96233e

	function countRelatedIds($crmid) {
		return $this->getRelatedIds($crmid, 0,0, true);
	}
	// crmv@43611e	crmv@51605e

	// search for crmids which field value are $value
	// returns array of ids
	static function searchFieldValue($fieldid, $value, $start = 0, $limit = 0, $use_user_permissions = false) {	//crmv@51605
		global $adb, $table_prefix, $current_user;

		$query =
			"select
				{$table_prefix}_tab.name as modulename,	fieldid, fieldname, tablename, columnname
			from {$table_prefix}_field
				inner join {$table_prefix}_tab on {$table_prefix}_tab.tabid = {$table_prefix}_field.tabid
			where fieldid=? and {$table_prefix}_field.presence in (0,2)";

		if ($limit > 0) {
			$res = $adb->limitpQuery($query, $start, $limit, array($fieldid));
		} else {
			$res = $adb->pquery($query, array($fieldid));
		}

		if ($res && $adb->num_rows($res) > 0) {

			$row = $adb->FetchByAssoc($res, -1, false);
			$focus = CRMEntity::getInstance($row['modulename']);
			if (empty($focus)) return null;

			$indexname = $focus->tab_name_index[$row['tablename']];
			if (empty($indexname)) return null;

			$ret = array();
			if ($row['tablename'] != "{$table_prefix}_crmentity") {
				$join = "inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$row['tablename']}.$indexname";
			} else {
				$join = "";
			}
			
			// crmv@72192
			if ($row['tablename'] != $focus->table_name){
				$tname = $focus->table_name;
				$join .= " inner join {$tname} on {$tname}.{$focus->tab_name_index[$tname]} = {$row['tablename']}.$indexname";
			} else{
				//crmv@51605
				if (in_array($row['modulename'],array('Calendar','Events')) && $row['tablename'] != "{$table_prefix}_activity") {
					$join .= " inner join {$table_prefix}_activity on {$table_prefix}_activity.activityid = {$table_prefix}_crmentity.crmid";
				}
			}
			// crmv@72192e

			// crmv@71354
			$join .= " left join {$table_prefix}_users on {$table_prefix}_crmentity.smownerid = {$table_prefix}_users.id";
			$join .= " left join {$table_prefix}_groups on {$table_prefix}_crmentity.smownerid = {$table_prefix}_groups.groupid";
			// crmv@71354e

			$query2 = "select {$table_prefix}_crmentity.crmid from {$row['tablename']} $join";
			if ($use_user_permissions) {
				$query2 .= $focus->getNonAdminAccessControlQuery($row['modulename'], $current_user);
			}
			$query2 .= " where {$table_prefix}_crmentity.deleted = 0 and {$row['tablename']}.{$row['columnname']} = ? and {$table_prefix}_crmentity.setype = ?";
			if ($use_user_permissions) {
				$query2 = $focus->listQueryNonAdminChange($query2, $row['modulename']);
			}
			if($row['modulename'] == 'Leads') {
				$query2 .= " AND ".$table_prefix."_leaddetails.converted = 0";
			}
			$res2 = $adb->pquery($query2, array($value,$row['modulename'])); // crmv@43765
			//crmv@51605e
			if ($res2 && $adb->num_rows($res2) > 0) {
				while ($row2 = $adb->FetchByAssoc($res2, -1, false)) {
					$ret[] = $row2['crmid'];
				}
				return $ret;
			}
		}
		return null;
	}
	
	/**
	 * Invert the relation
	 */
	public function invert() {
		$m1 = $this->module1;
		$this->module1 = $this->module2;
		$this->module2 = $m1;
		
		if ($this->type == self::$TYPE_NTON) {
			if ($this->relationinfo) {
				$i1 = $this->relationinfo['relidx'];
				$this->relationinfo['relidx'] = $this->relationinfo['relidx2'];
				$this->relationinfo['relidx2'] = $i1;
				$m1 = $this->relationinfo['relmod1'];
				$this->relationinfo['relmod1'] = $this->relationinfo['relmod2'];
				$this->relationinfo['relmod2'] = $m1;
				// TODO: the relation id should be changed too!
			}
		} else {
			if ($this->type == self::$TYPE_NTO1) {
				$this->type = self::$TYPE_1TON;
			} else {
				$this->type = self::$TYPE_NTO1;
			}
		}
	}
	
}

class RelationManager extends SDKExtendableUniqueClass { // crmv@42024

	// user object to use when getting privileges
	protected $user = null;

	// special uitypes for relations (uitype->array(destination modules))
	protected $uitype_rel = array(
		'51' => array("Accounts"),
		'57' => array("Contacts"),
		'58' => array("Campaigns"),
		'59' => array("Products"),
		// Calendar related_to : dynamically populated
		'66' => array(),
		'68' => array('Accounts','Contacts'),
		'73' => array("Accounts"),
		'75' => array("Vendors"),
		'76' => array("Potentials"),
		'78' => array("Quotes"),
		'81' => array("Vendors"),
		'80' => array("SalesOrder"),
		'206' => array("Reports"),
		// TODO: users
	);

	protected $uitype_relid;
	protected $relations; // cache (by module and relation type)
	protected $disableCache = false;
	
	protected $usePBRelations = false; // if true, the inventory relations (eg: Quotes -> Products) will pass through a fake module, called ProductsBlock

	protected function __construct($user = null) {
		global $current_user;

		$this->user = (empty($user) ? $current_user : $user);
		$this->relations = array();

		// create dynamic uitype modules list
		$calmod = getCalendarRelatedToModules();
		if (count($calmod) > 0)  $this->uitype_rel['66'] = $calmod;

		// get the tabids for special  uitypes
		foreach ($this->uitype_rel as $uitype=>$modlist) {
			$list = array_map('getTabid', $modlist);
			$this->uitype_relid[$uitype] = $list;
		}

	}

	public function enablePBRelations() {
		$this->usePBRelations = true;
	}
	
	public function disablePBRelations() {
		$this->usePBRelations = false;
	}
	
	// ----- cache related functions -----
	function disableCache() {
		$this->disableCache = true;
		$this->clearCache();
	}

	function enableCache() {
		$this->disableCache = false;
	}

	function getCachedRelations($module, $type, $relmodules = array(), $excludeModules = array()) {
		$ret = $this->relations[$module][$type];
		// FILTER modules
		if (is_array($ret) && (count($relmodules) > 0 || count($excludeModules) > 0)) {
			foreach ($ret as $k=>$relation) {
				$destmod = $relation->getSecondModule();
				if (!in_array($destmod, $relmodules) || in_array($destmod, $excludeModules)) unset($ret[$k]);
			}
		}
		return $ret;
	}

	function hasCachedRelation($module, $type) {
		return (!$this->disableCache && is_array($this->relations[$module][$type]));
	}

	function initializeCache($module, $type) {
		$this->relations[$module][$type] = array();
	}

	function addRelationToCache($relation) {
		$module = $relation->getFirstModule();
		$type = $relation->getType();
		$this->relations[$module][$type][] = $relation;
	}

	function clearCache() {
		$this->relations = null;
	}
	// ----- end cache related functions -----

	// return a list of all modules related to the specified module (optionally filtered for destination module)
	// type is a OR between ModuleRelation::$TYPE_*
	function getRelations($module, $type = null, $relmodules = array(), $excludeModules = array()) {
		global $adb, $table_prefix;
		
		if ($this->usePBRelations && $module == 'ProductsBlock') return $this->getPBRelations($module, $type, $relmodules, $excludeModules);

		if (is_null($type)) $type = ModuleRelation::$TYPE_ALL;
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		if (!is_array($excludeModules)) $excludeModules = array($excludeModules);

		$relmodules = array_diff($relmodules, $excludeModules);

		// save cache only if no module filtering
		$saveCache = (!$this->disableCache && (count($relmodules) == 0) && (count($excludeModules) == 0));

		$moduleid = getTabid($module);
		$moduleInst = CRMEntity::getInstance($module);

		$relations = array();

		// N-1
		if ($type & ModuleRelation::$TYPE_NTO1) {
			if ($this->hasCachedRelation($module, ModuleRelation::$TYPE_NTO1)) {
				$relations = array_merge($relations, $this->getCachedRelations($module, ModuleRelation::$TYPE_NTO1, $relmodules, $excludeModules));
			} else {
				if ($saveCache) $this->initializeCache($module, ModuleRelation::$TYPE_NTO1);
				$query = "select fmr.fieldid,fmr.relmodule, f.fieldname, f.tablename, f.columnname from {$table_prefix}_fieldmodulerel fmr inner join {$table_prefix}_field f on f.fieldid = fmr.fieldid where fmr.module = ?"; // crmv@42752
				$params = array($module);

				if (!empty($relmodules)) {
					$query .= " and fmr.relmodule in (".generateQuestionMarks($relmodules).")";
					$params[] = $relmodules;
				}
				if (!empty($excludeModules)) {
					$query .= " and fmr.relmodule not in (".generateQuestionMarks($excludeModules).")";
					$params[] = $excludeModules;
				}
				$res = $adb->pquery($query, $params);
				if ($res) {
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						// crmv@100399 - Events have a field, but it's a NtoN relation
						if ($module == 'Events' && $row['relmodule'] == 'Contacts') {
							continue;
						}
						// crmv@100399e
						$newrel = new ModuleRelation($module, $row['relmodule'], ModuleRelation::$TYPE_NTO1);
						$newrel->fieldid = $row['fieldid'];
						$newrel->fieldname = $row['fieldname'];
						$newrel->fieldtable = $row['tablename'];
						$newrel->fieldcolumn = $row['columnname'];
						$relations[] = $newrel;
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}

				// special uitypes, N-1
				$uitypeRelFiltered = $this->uitype_rel;
				if (!empty($relmodules)) {
					foreach ($uitypeRelFiltered as $ukey=>$kmods) {
						$kmods2 = array_intersect($kmods, $relmodules);
						if (count($kmods2) == 0) {
							unset($uitypeRelFiltered[$ukey]);
						} elseif (count($kmods2) != count($kmods)) {
							$uitypeRelFiltered[$ukey] = $kmods2;
						}
					}
				} elseif (!empty($excludeModules)) {
					foreach ($uitypeRelFiltered as $ukey=>$kmods) {
						$kmods2 = array_diff($kmods, $excludeModules);
						if (count($kmods2) == 0) {
							unset($uitypeRelFiltered[$ukey]);
						} elseif (count($kmods2) != count($kmods)) {
							$uitypeRelFiltered[$ukey] = $kmods2;
						}
					}
				}
				if (count($uitypeRelFiltered) > 0) {
					$uitypeFields = array_keys($uitypeRelFiltered);
					$res = $adb->pquery("select fld.fieldid, fld.uitype, fld.fieldname, fld.tablename, fld.columnname from {$table_prefix}_field fld where fld.tabid = ? and fld.uitype in (".generateQuestionMarks($uitypeFields).")", array($moduleid, $uitypeFields)); //crmv@42752
					if ($res) {
						while ($row = $adb->FetchByAssoc($res, -1, false)) {
							$uitype = $row['uitype'];
							foreach ($uitypeRelFiltered[$uitype] as $relmod) {
								$newrel = new ModuleRelation($module, $relmod, ModuleRelation::$TYPE_NTO1);
								$newrel->fieldid = $row['fieldid'];
								$newrel->fieldname = $row['fieldname'];
								$newrel->fieldtable = $row['tablename'];
								$newrel->fieldcolumn = $row['columnname'];
								$relations[] = $newrel;
								if ($saveCache) $this->addRelationToCache($newrel);
							}
						}
					}
				}
			}
		}

		// 1-N
		if ($type & ModuleRelation::$TYPE_1TON) {
			if ($this->hasCachedRelation($module, ModuleRelation::$TYPE_1TON)) {
				$relations = array_merge($relations, $this->getCachedRelations($module, ModuleRelation::$TYPE_1TON, $relmodules, $excludeModules));
			} else {
				if ($saveCache) $this->initializeCache($module, ModuleRelation::$TYPE_1TON);
				$query = "select fmr.fieldid,fmr.module, f.fieldname, f.tablename, f.columnname from {$table_prefix}_fieldmodulerel fmr inner join {$table_prefix}_field f on f.fieldid = fmr.fieldid where fmr.relmodule = ?"; //crmv@42752
				$params = array($module);
				if (!empty($relmodules)) {
					$query .= " and fmr.module in (".generateQuestionMarks($relmodules).")";
					$params[] = $relmodules;
				}
				if (!empty($excludeModules)) {
					$query .= " and fmr.module not in (".generateQuestionMarks($excludeModules).")";
					$params[] = $excludeModules;
				}
				$res = $adb->pquery($query, $params);
				if ($res) {
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						// crmv@100399 - Events have a field, but it's a NtoN relation
						if ($module == 'Contacts' && $row['module'] == 'Events') {
							continue;
						}
						// crmv@100399e
						$newrel = new ModuleRelation($module, $row['module'], ModuleRelation::$TYPE_1TON);
						$newrel->fieldid = $row['fieldid'];
						$newrel->fieldname = $row['fieldname'];
						$newrel->fieldtable = $row['tablename'];
						$newrel->fieldcolumn = $row['columnname'];
						// crmv@43864 - search for a related
						$relres = $adb->pquery("select relation_id from {$table_prefix}_relatedlists where tabid = ? and related_tabid = ?", array(getTabid($module), getTabid($row['module'])));
						if ($relres && $adb->num_rows($relres) == 1) {
							$newrel->relationid = $adb->query_result_no_html($relres, 0, 'relation_id');
						}
						// crmv@43864e
						$relations[] = $newrel;
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}

				// special uitypes, 1-N
				$uitypeFields = array();
				foreach ($this->uitype_rel as $uitype=>$listmod) {
					if (in_array($module, $listmod)) $uitypeFields[] = $uitype;
				}
				if (count($uitypeFields) > 0) {
					$query = "select fld.fieldid, fld.tabid,fld.fieldname,fld.tablename,fld.columnname, tab.name from {$table_prefix}_field fld inner join {$table_prefix}_tab tab on tab.tabid = fld.tabid where fld.uitype in (".generateQuestionMarks($uitypeFields).")"; //crmv@42752
					$params = array($uitypeFields);
					if (!empty($relmodules)) {
						$query .= " and tab.name in (".generateQuestionMarks($relmodules).")";
						$params[] = $relmodules;
					}
					if (!empty($excludeModules)) {
						$query .= " and tab.name not in (".generateQuestionMarks($excludeModules).")";
						$params[] = $excludeModules;
					}
					$res = $adb->pquery($query, $params);
					if ($res) {
						while ($row = $adb->FetchByAssoc($res, -1, false)) {
							$newrel = new ModuleRelation($module, $row['name'], ModuleRelation::$TYPE_1TON);
							$newrel->fieldid = $row['fieldid'];
							$newrel->fieldname = $row['fieldname'];
							$newrel->fieldtable = $row['tablename'];
							$newrel->fieldcolumn = $row['columnname'];
							$relations[] = $newrel;
							if ($saveCache) $this->addRelationToCache($newrel);
						}
					}
				}
			}
		}

		// N-N - MOLTO BETA!!
		// 1. usare relatedlists e vedere se ci sono 2 related (nelle 2 direzioni)
		// 2. vedere se ci sono relatedlist che usano le funzioni N-N (get_related_list-> per ora cablate!! ARGH)
		// 3. aggiungere relazioni per i moduli con prodotti
		if ($type & ModuleRelation::$TYPE_NTON) {
			if ($this->hasCachedRelation($module, ModuleRelation::$TYPE_NTON)) {
				$relations = array_merge($relations, $this->getCachedRelations($module, ModuleRelation::$TYPE_NTON, $relmodules, $excludeModules));
			} else {
				if ($saveCache) $this->initializeCache($module, ModuleRelation::$TYPE_NTON);

				$found_relid = array();
				$query = "
					SELECT r1.relation_id, r2.relation_id as relation_id2, tab1.name as mod1, tab2.name as mod2
					FROM {$table_prefix}_relatedlists r1
						INNER JOIN {$table_prefix}_relatedlists r2 on r2.tabid = r1.related_tabid and r2.related_tabid = r1.tabid
						INNER JOIN {$table_prefix}_tab tab1 on tab1.tabid = r1.tabid
						INNER JOIN {$table_prefix}_tab tab2 on tab2.tabid = r1.related_tabid
					WHERE r1.tabid = ? and r1.relation_id <> r2.relation_id"; // crmv@43611
				$params = array($moduleid);
				if (!empty($relmodules)) {
					$query .= " and tab2.name in (".generateQuestionMarks($relmodules).")";
					$params[] = $relmodules;
				}
				if (!empty($excludeModules)) {
					$query .= " and tab2.name not in (".generateQuestionMarks($excludeModules).")";
					$params[] = $excludeModules;
				}
				$res = $adb->pquery($query, $params);
				if ($res) {
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						$newrel = new ModuleRelation($module, $row['mod2'], ModuleRelation::$TYPE_NTON);
						$newrel->relationid = $row['relation_id'];
						$newrel->relationinfo = $newrel->getNtoNinfo(); //crmv@47905
						$relations[] = $newrel;
						$found_relid[$row['relation_id']] = $row['relation_id'];
						$found_relid[$row['relation_id2']] = $row['relation_id2'];
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}

				//2.1
				$ntonFunctions = array('get_related_list', 'get_related_list_target', 'get_messages_list', 'get_documents_dependents_list', 'get_attachments', 'get_campaigns_newsletter', 'get_newsletter_emails', 'get_faxes', 'get_sms'); // crmv@38798 crmv@43765
				
				$query = "
					SELECT r.relation_id, tab1.name as mod1, tab2.name as mod2
						FROM {$table_prefix}_relatedlists r
						INNER JOIN {$table_prefix}_tab tab1 on tab1.tabid = r.tabid
						INNER JOIN {$table_prefix}_tab tab2 on tab2.tabid = r.related_tabid
					WHERE r.related_tabid = ? and r.tabid != r.related_tabid and r.name in (".generateQuestionMarks($ntonFunctions).")";
				$params = array($moduleid, $ntonFunctions);
				if (!empty($found_relid)) {
					$query .= " and r.relation_id not in (".generateQuestionMarks($found_relid).")";
					$params[] = $found_relid;
				}
				if (!empty($relmodules)) {
					$query .= " and tab1.name in (".generateQuestionMarks($relmodules).")";	// crmv@43611
					$params[] = $relmodules;
				}
				if (!empty($excludeModules)) {
					$query .= " and tab1.name not in (".generateQuestionMarks($excludeModules).")"; // crmv@43611
					$params[] = $excludeModules;
				}
				$res = $adb->pquery($query, $params);
				if ($res) {
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						$newrel = new ModuleRelation($module, $row['mod1'], ModuleRelation::$TYPE_NTON);
						$newrel->relationid = $row['relation_id'];
						$newrel->relationinfo = $newrel->getNtoNinfo(); // crmv@47905 crmv@54449
						$relations[] = $newrel;
						$found_relid[$row['relation_id']] = $row['relation_id'];
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}

				//2.2
				$query = "
					SELECT r.relation_id, tab1.name as mod1, tab2.name as mod2
						FROM {$table_prefix}_relatedlists r
						INNER JOIN {$table_prefix}_tab tab1 on tab1.tabid = r.tabid
						INNER JOIN {$table_prefix}_tab tab2 on tab2.tabid = r.related_tabid
					WHERE r.tabid = ? and r.tabid != r.related_tabid and r.name in (".generateQuestionMarks($ntonFunctions).")";
				$params = array($moduleid, $ntonFunctions);
				if (!empty($found_relid)) {
					$query .= " and r.relation_id not in (".generateQuestionMarks($found_relid).")";
					$params[] = $found_relid;
				}
				if (!empty($relmodules)) {
					$query .= " and tab2.name in (".generateQuestionMarks($relmodules).")"; // crmv@43611
					$params[] = $relmodules;
				}
				if (!empty($excludeModules)) {
					$query .= " and tab2.name not in (".generateQuestionMarks($excludeModules).")"; // crmv@43611
					$params[] = $excludeModules;
				}
				$res = $adb->pquery($query, $params);
				if ($res) {
					while ($row = $adb->FetchByAssoc($res, -1, false)) {
						$newrel = new ModuleRelation($module, $row['mod2'], ModuleRelation::$TYPE_NTON);
						$newrel->relationid = $row['relation_id'];
						$relations[] = $newrel;
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}
				
				// crmv@100399
				//2.3 Special relation Events - Contacts (there's a field, but it's actually a N-N)
				if ($module == 'Events' && (empty($relmodules) || in_array('Contacts', $relmodules)) && (empty($excludeModules) || !in_array('Contacts', $excludeModules))) {
					$query = "SELECT r.relation_id FROM {$table_prefix}_relatedlists r WHERE r.tabid = ? and r.name = ?";
					$res = $adb->pquery($query, array(9, 'get_contacts'));
					if ($res && $adb->num_rows($res) > 0) {
						$relid = $adb->query_result_no_html($res, 0, 'relation_id');
						$newrel = new ModuleRelation($module, 'Contacts', ModuleRelation::$TYPE_NTON);
						$newrel->relationid = $relid;
						$newrel->relationinfo = $newrel->getNtoNinfo(); // crmv@47905 crmv@54449
						$relations[] = $newrel;
						$found_relid[$relid] = $relid;
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				} elseif ($module == 'Contacts' && (empty($relmodules) || in_array('Events', $relmodules)) && (empty($excludeModules) || !in_array('Events', $excludeModules))) {
					$query = "SELECT r.relation_id FROM {$table_prefix}_relatedlists r WHERE r.tabid = ? and r.name = ?";
					$res = $adb->pquery($query, array(getTabid('Contacts'), 'get_activities'));
					if ($res && $adb->num_rows($res) > 0) {
						$relid = $adb->query_result_no_html($res, 0, 'relation_id');
						$newrel = new ModuleRelation($module, 'Events', ModuleRelation::$TYPE_NTON);
						$newrel->relationid = $relid;
						$newrel->relationinfo = $newrel->getNtoNinfo(); // crmv@47905 crmv@54449
						$relations[] = $newrel;
						$found_relid[$relid] = $relid;
						if ($saveCache) $this->addRelationToCache($newrel);
					}
				}
				// crmv@100399e
				
				//3.1
				if (isInventoryModule($module)) {
					if ($this->usePBRelations) {
						$prodmods = array('ProductsBlock');
						$type = ModuleRelation::$TYPE_1TON;
					} else {
						$prodmods = getProductModules();
						$type = ModuleRelation::$TYPE_NTON;
					}
					// crmv@38798
					foreach ($prodmods as $prodmod) {
						if ((empty($relmodules) || in_array($prodmod, $relmodules)) && !in_array($prodmod, $excludeModules)) {
							$newrel = new ModuleRelation($module, $prodmod, $type);
							if ($type == ModuleRelation::$TYPE_NTON) {
								$newrel->relationinfo = $newrel->getNtoNinfo();
							} else {
								$newrel->fieldid = 10001;
								$newrel->fieldname = 'id';
								$newrel->fieldtable = $table_prefix.'_inventoryproductrel';
								$newrel->fieldcolumn = 'id';
							}
							$relations[] = $newrel;
							if ($saveCache) $this->addRelationToCache($newrel);
						}
					}
					// crmv@38798e
				}

				// 3.2
				if (isProductModule($module)) { // crmv@38798 crmv@64542
					$imods = getInventoryModules();
					if ($this->usePBRelations) $imods = array('ProductsBlock'); // crmv@98894
					if (!empty($relmodules)) $imods = array_intersect($imods, $relmodules);
					$imods = array_diff($imods, $excludeModules);
					if (!empty($imods)) {
						//crmv@3086m
						$relation_ids = array();
						$query = "
							SELECT r.relation_id, tab1.name as mod1, tab2.name as mod2
								FROM {$table_prefix}_relatedlists r
								INNER JOIN {$table_prefix}_tab tab1 on tab1.tabid = r.tabid
								INNER JOIN {$table_prefix}_tab tab2 on tab2.tabid = r.related_tabid
							WHERE r.tabid = ? and tab2.name in (".generateQuestionMarks($imods).")";
						$params = array($moduleid, $imods);
						$res = $adb->pquery($query, $params);
						if ($res) {
							while ($row = $adb->FetchByAssoc($res, -1, false)) {
								$relation_ids[$row['mod2']] = $row['relation_id'];
							}
						}
						//crmv@3086me
						if ($this->usePBRelations) {
							$newrel = new ModuleRelation($module, 'ProductsBlock', ModuleRelation::$TYPE_1TON);
							$newrel->fieldid = 10002;
							$newrel->fieldname = 'productid';
							$newrel->fieldtable = $table_prefix.'_inventoryproductrel';
							$newrel->fieldcolumn = 'productid';
							$relations[] = $newrel;
							if ($saveCache) $this->addRelationToCache($newrel);
						} else {
							foreach ($imods as $imod) {
								$newrel = new ModuleRelation($module, $imod, ModuleRelation::$TYPE_NTON);
								if (!empty($relation_ids[$imod])) $newrel->relationid = $relation_ids[$imod];	//crmv@3086m
								$newrel->relationinfo = $newrel->getNtoNinfo();
								$relations[] = $newrel;
								if ($saveCache) $this->addRelationToCache($newrel);
							}
						}
					}
				}
			}
		}

		return $relations;
	}
	
	/**
	 * Return relations from the special "ProductsBlock" module
	 */
	function getPBRelations($module, $type = null, $relmodules = array(), $excludeModules = array()) {
		global $adb, $table_prefix;
		
		if (is_null($type)) $type = ModuleRelation::$TYPE_ALL;
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		if (!is_array($excludeModules)) $excludeModules = array($excludeModules);

		$relmodules = array_diff($relmodules, $excludeModules);

		// save cache only if no module filtering
		$saveCache = (!$this->disableCache && (count($relmodules) == 0) && (count($excludeModules) == 0));

		$moduleid = 200; // the fake tabid for this module

		$relations = array();

		// N-1
		if ($type & ModuleRelation::$TYPE_NTO1) {
			if ($this->hasCachedRelation($module, ModuleRelation::$TYPE_NTO1)) {
				$relations = array_merge($relations, $this->getCachedRelations($module, ModuleRelation::$TYPE_NTO1, $relmodules, $excludeModules));
			} else {
				if ($saveCache) $this->initializeCache($module, ModuleRelation::$TYPE_NTO1);
				
				$invModules = getInventoryModules();
				if (!empty($relmodules)) $invModules = array_intersect($invModules, $relmodules);
				foreach ($invModules as $imod) {
					$newrel = new ModuleRelation($module, $imod, ModuleRelation::$TYPE_NTO1);
					$newrel->fieldid = 10001;
					$newrel->fieldname = 'id';
					$newrel->fieldtable = $table_prefix.'_inventoryproductrel';
					$newrel->fieldcolumn = 'id';
					$relations[] = $newrel;
					if ($saveCache) $this->addRelationToCache($newrel);
				}
				
				$prodModules = getProductModules();
				if (!empty($relmodules)) $prodModules = array_intersect($prodModules, $relmodules);
				foreach ($prodModules as $prodmod) {
					$newrel = new ModuleRelation($module, $prodmod, ModuleRelation::$TYPE_NTO1);
					$newrel->fieldid = 10002;
					$newrel->fieldname = 'productid';
					$newrel->fieldtable = $table_prefix.'_inventoryproductrel';
					$newrel->fieldcolumn = 'productid';
					$relations[] = $newrel;
					if ($saveCache) $this->addRelationToCache($newrel);
				}

			}
		}


		return $relations;
	}
	
	//crmv@OPER4380
	// return a list of all modules related to the specified module (optionally filtered for destination module)
	// type is a OR between ModuleRelation::$TYPE_*
	function getRelationsExtra($module, $type = null, $relmodules = array(), $excludeModules = array()) {
		global $adb, $table_prefix,$current_user;

		if (is_null($type)) $type = ModuleRelation::$TYPE_ALL;
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		if (!is_array($excludeModules)) $excludeModules = array($excludeModules);
		//crmv@47013
		$result = $adb->query("SELECT tabid, name FROM {$table_prefix}_tab WHERE presence = 1");
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				if (!in_array($row['name'],$excludeModules)) $excludeModules[] = $row['name'];
			}
		}
		//crmv@47013e

		$relmodules = array_diff($relmodules, $excludeModules);

		// save cache only if no module filtering
		$saveCache = (!$this->disableCache && (count($relmodules) == 0) && (count($excludeModules) == 0));
		
		$moduleid = getTabid($module);
		$moduleInst = CRMEntity::getInstance($module);
		// possible extra relations: Transitions
		include_once('include/Webservices/Extra/WebserviceExtra.php');
		$relations = WebserviceExtra::getExtraModulesRelatedTo($module,$this);
		return $relations;
	}
	//crmv@OPER4380 e

	function getRelatedModules($module) {
		$modrel = $this->getRelations($module);

		$ret = array();
		if (is_array($modrel)) {
			foreach ($modrel as $relobj) {
				if(getTabid($relobj->getSecondModule())) //crmv@68610
					$ret[$relobj->getSecondModule()] = true;
			}
			$ret = array_keys($ret);
		}

		return $ret;
	}

	// true if module1 is somehow related to module2
	function isModuleRelated($module1, $module2) {
		return in_array($module1, $this->getRelatedModules($module2));
	}

	// return a list of all related records of a record
	// return: array( id=>array('setype'=>type, 'field'=>fieldid, 'related'=>array(relid,...) ... )
	// TODO: gestire profondità maggiore di 1 (record collegato a x -> collegato a... )
	function getRelatedIds($module, $crmid, $relmodules = array(), $excludeMods = array(), $recursive = false, $groupByModule = false) {
		global $adb, $table_prefix;

		$ret = array();
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		$relmodules = array_filter($relmodules);

		// get relations filtered by module
		$relations = $this->getRelations($module, null, $relmodules, $excludeMods);

		if (is_array($relations)) {
			foreach ($relations as $relation) {
				$ids = $relation->getRelatedIds($crmid);
				if (!empty($ids)) {
					$relation->getSecondModule();
					if ($groupByModule) {
						if (empty($ret[$relation->getSecondModule()])) {
							$ret[$relation->getSecondModule()] = $ids;
						} else {
							$ret[$relation->getSecondModule()] = array_merge($ret[$relation->getSecondModule()], $ids);
						}
						$ret[$relation->getSecondModule()] = array_unique($ret[$relation->getSecondModule()]);
					} else {
						$ret = array_merge($ret, $ids);
					}
				}
			}
		}
		if ($groupByModule) {
			return $ret;
		} else {
			return array_unique($ret);
		}
	}
	
	//crmv@OPER4380
	function getRelatedIdsExtra($module, $crmid, $relmodules = array(), $excludeMods = array(), $recursive = false, $groupByModule = false) {
		global $adb, $table_prefix;
		$ret = array();
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		$relmodules = array_filter($relmodules);

		// get relations filtered by module
		$relations = $this->getRelationsExtra($module, null, $relmodules, $excludeMods);
		if (is_array($relations)) {
			foreach ($relations as $relmodule=>$relation) {
				$ids = $relation->getRelatedIdsExtra($crmid);
				if (!empty($ids)) {
					$relation->getSecondModule();
					if ($groupByModule) {
						if (empty($ret[$relation->getSecondModule()])) {
							$ret[$relation->getSecondModule()] = $ids;
						} else {
							$ret[$relation->getSecondModule()] = array_merge($ret[$relation->getSecondModule()], $ids);
						}
						$ret[$relation->getSecondModule()] = array_unique($ret[$relation->getSecondModule()]);
					} else {
						$ret = array_merge($ret, $ids);
					}
				}
			}
		}
		if ($groupByModule) {
			return $ret;
		} else {
			return array_unique($ret);
		}
	}	
	//crmv@OPER4380 e

	function countRelatedIds($module, $crmid, $relmodules = array(), $excludeMods = array(), $recursive = false) {
		global $adb, $table_prefix;

		$ret = array();
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		$relmodules = array_filter($relmodules);

		// get relations filtered by module
		$relations = $this->getRelations($module, null, $relmodules, $excludeMods);

		if (is_array($relations)) {
			foreach ($relations as $relation) {
				$cnt = $relation->countRelatedIds($crmid);
				$ret[$relation->getSecondModule()] = $cnt;
			}
		}

		return $ret;
	}

	//crmv@43864 crmv@3086m	crmv@57221 crmv@104568
	function getTurboliftRelations($focus, $module, $crmid, $relmodules = array(), $excludeMods = array(), $recursive = false, $panelid = null) {
		global $adb, $table_prefix;

		$relations = $pins = array();
		if (!is_array($relmodules)) $relmodules = array($relmodules);
		$relmodules = array_filter($relmodules);

		$CRMVUtils = CRMVUtils::getInstance();
		 //TODO:per relationid
		$pinRelatedLists = $CRMVUtils->getPinRelatedLists(getTabid($module));
		$pinRelationIds = $CRMVUtils->getPinRelationIds(getTabid($module)); //crmv@62415
		$oldStyle = $CRMVUtils->getConfigurationLayout('old_style');
		$tbRelationsOrder = $CRMVUtils->getConfigurationLayout('tb_relations_order');
		
		$allTabRelated = $tabRelated = array();
		if ($panelid > 0) {
			$tab = Vtecrm_Panel::getInstance($panelid);
			if ($tab) {
				$tabrels = $tab->getRelatedLists();
				// relation id as key
				if (is_array($tabrels)) {
					foreach ($tabrels as $rel) {
						$tabRelated[$rel['id']] = $rel;
					}
				}
			}
		}
		
		// get all fixed related lists
		$res = $adb->pquery(
			"SELECT pr.relation_id FROM {$table_prefix}_panel2rlist pr
			INNER JOIN {$table_prefix}_panels p ON p.panelid = pr.panelid
			WHERE p.tabid = ?",
			array(getTabid($module))
		);
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$allTabRelated[] = (int)$row['relation_id'];
			}
		}

		$sdkInfo = SDK::getTurboliftCountInfo();	//crmv@51605

		// get related lists
		$related_array = getRelatedLists($module,$focus);
		if (!empty($related_array)) {
			$related_array_info = array();
			$sequence = 0;
			foreach ($related_array as $header => $info) {
				if (!empty($excludeMods) && in_array($info['related_tabname'], $excludeMods)) continue; // crmv@109871
				$relinfo = array(
					'sequence'=>$sequence,
					'header'=>$header,
					'related_module'=>$info['related_tabname'],
					'relationId'=>$info['relationId'],
					'actions'=>$info['actions'],
					'name'=>$info['name'],
				);
				if (in_array($info['relationId'], $allTabRelated)) {
					$relinfo['fixed'] = true;
				}
				if (array_key_exists($info['relationId'], $tabRelated)) {
					$relinfo['sequence'] = $tabRelated[$info['relationId']]['sequence'];
				} else {
					// make sure the ones added by the user are after
					$relinfo['sequence'] += 100;
				}
				$related_array_info[$info['relationId']] = $relinfo;
				if (file_exists('themes/images/modulesimg/'.$info['related_tabname'].'.png')) {
					$related_array_info[$info['relationId']]['img'] = 'themes/images/modulesimg/'.$info['related_tabname'].'.png';
				}
				//crmv@51605
				if (!empty($sdkInfo[$info['relationId']])) {
					// crmv@115634 - do not exclude used mods, allow repetitions! 
					if (PerformancePrefs::getBoolean('RELATED_LIST_COUNT')) { // crmv@115378
						$related_array_info[$info['relationId']]['count'] = SDK::getTurboliftCount($info['relationId'], $crmid);
					}
				}
				//crmv@51605e
				if ($oldStyle) {
					$related_array_info[$info['relationId']]['buttons'] = $this->getRelatedListButtons($info, $module, $crmid);
				}
				$sequence++;
			}
		}
		
		if (PerformancePrefs::getBoolean('RELATED_LIST_COUNT')) { // crmv@115378
			$relations1N = $this->getRelations($module, ModuleRelation::$TYPE_1TON, $relmodules, $excludeMods);
			$relationsNN = $this->getRelations($module, ModuleRelation::$TYPE_NTON, $relmodules, $excludeMods);
			$rl = array();
			if (is_array($relations1N)) {
				foreach ($relations1N as $relation) {
					$relationid = (empty($relation->relationid) ? 'fld_'.$relation->fieldid : $relation->relationid);
					$ids = $relation->getRelatedIds($crmid, null, null, null, true);	//crmv@51605
					if (!empty($ids)) {
						$rl[$relationid] = array('secmod'=>$relation->getSecondModule(), 'ids'=>$ids);
					}
				}
			}
			if (is_array($relationsNN)) {
				foreach ($relationsNN as $relation) {
					$relationid = $relation->relationid;
					$ids = $relation->getRelatedIds($crmid, null, null, null, true);	//crmv@51605
					if (!empty($ids)) {
						if (empty($rl[$relationid])) {
							$rl[$relationid] = array('secmod'=>$relation->getSecondModule(), 'ids'=>$ids);
						} else {
							$rl[$relationid]['ids'] = array_merge($rl[$relationid]['ids'], $ids);
						}
					}
				}
			}
			$force_related = array('ChangeLog','Fax','Sms');
			foreach ($rl as $relationid => $relinfo) {
				$mod = $relinfo['secmod'];
				$ids = array_unique($relinfo['ids']);
				// TODO: services??
				if (isInventoryModule($module) && in_array($mod,array('Products'))) {
					continue;
				}
				$pin = false;
				$display = 'block';
				if (!empty($pinRelatedLists[$mod])) {
					$pins[$pinRelatedLists[$mod]] = $related_array[$pinRelatedLists[$mod]];
					$pins[$pinRelatedLists[$mod]] = array_merge($pins[$pinRelatedLists[$mod]],$related_array_info[$relationid]);
					$pin = true;
					$display = 'none';
				} elseif (array_key_exists($relationid, $tabRelated)) {
					$pins[$mod] = $related_array[$mod];
					$pins[$mod] = array_merge($pins[$mod],$related_array_info[$relationid]);
					$pin = true;
					$display = 'none';
				}
				$tmp = array(
					'type'=>'other',
					'display'=>$display,
					'module'=>$mod,
					'count'=>count($ids),
					'sequence'=>$related_array_info[$relationid]['sequence']
				);
				//crmv@62415
				if (in_array($relationid,$pinRelationIds) || array_key_exists($relationid, $tabRelated)){
					$tmp['pinned'] = true;
				}
				else{
					$tmp['pinned'] = false;
				}
				//crmv@62415 e				
				if (!empty($related_array_info[$relationid])) {
					$tmp = array_merge($tmp,$related_array_info[$relationid]);
					unset($related_array_info[$relationid]);
				}
				if (empty($tmp['relationId'])) continue;
				$relations[] = $tmp;
			}
			//crmv@62415
			//FORCE ODLSTYLE FOR PINS
			foreach ($pinRelatedLists as $mod => $prl) {
				if (!isset($pins[$pinRelatedLists[$mod]]) && array_key_exists($mod,$related_array)) {	//crmv@72900
					$relationid = $related_array[$pinRelatedLists[$mod]]['relationId'];
					$pins[$pinRelatedLists[$mod]] = $related_array[$pinRelatedLists[$mod]];
					$pins[$pinRelatedLists[$mod]] = array_merge($pins[$pinRelatedLists[$mod]],$related_array_info[$relationid]);
				}
			}
			foreach ($tabRelated as $relid => $pinrel) {
				$pinmod = $pinrel['module'];
				if (!isset($pins[$pinmod]) && array_key_exists($mod,$related_array)) {	//crmv@72900
					$pins[$pinmod] = $related_array[$pinmod] ?: array();
					$pins[$pinmod] = array_merge($pins[$pinmod],$related_array_info[$relid]);
				}
			}
			//crmv@62415 e
		}
		if (!empty($related_array_info)) {
			foreach($related_array_info as $relationid => $info) {
				$mod = $info['related_module'];
				if ($module == 'Calendar' && in_array($mod,array('','Contacts'))) {	// remove related list of Users and Contacts
					continue;
				}
				$tmp = array(
					'type'=>'other',
					'display'=>'none',
					'module'=>$mod
				);
				//crmv@62415
				if (in_array($relationid,$pinRelationIds) || array_key_exists($relationid, $tabRelated)){
					$tmp['pinned'] = true;
				}
				else{
					$tmp['pinned'] = false;
				}
				//crmv@62415 e				
				$tmp = array_merge($tmp,$related_array_info[$relationid]);
				$relations[] = $tmp;
			}
		}
		//crmv@51605
		if (!function_exists('array_sort_by_count')) {
			function array_sort_by_count(&$arr, $col, $col2 = null, $dir = SORT_ASC) {
			    $sort_col = array();
			    $others = array();
			    foreach ($arr as $key=> $row) {
			    	if (empty($row[$col])) {
			    		$others[] = $row;
			    		unset($arr[$key]);
			    	} else {
			    		$sort_col[$key] = $row[$col];
			    	}
			    }
			    array_multisort($sort_col, $dir, $arr);
			    if ($col2) {
					array_sort_by_count($arr, $col2, null, SORT_ASC);
					array_sort_by_count($others, $col2, null, SORT_ASC);
				}
			    $arr = array_merge($arr, $others);
			}
		}
		if (!function_exists('array_sort_by_sequence')) {
			function array_sort_by_sequence(&$arr, $col, $dir = SORT_ASC) {
			    $sort_col = array();
			    foreach ($arr as $key=> $row) {
			    	$sort_col[$key] = $row[$col];
			    }
			    array_multisort($sort_col, $dir, $arr);
			}
		}
		if ($tbRelationsOrder == 'num_of_records') {
			array_sort_by_count($relations, 'count', SORT_DESC);
			array_sort_by_count($pins, 'count', 'sequence', SORT_DESC); //crmv@62415
		} else {
			array_sort_by_sequence($relations, 'sequence');
			array_sort_by_sequence($pins, 'sequence'); //crmv@62415
		}
		//crmv@51605e
		return array('turbolift'=>$relations,'pin'=>$pins);
	}
	//crmv@43864e crmv@3086me crmv@57221 crmv@104568e
	
	//crmv@57221
	function getRelatedListButtons($relation, $module, $crmid) {
		global $onlyquery, $onlybutton, $currentModule;
		$onlyquery = true;
		$onlybutton = true;

		$focus = CRMEntity::getInstance($module);
		$method = $relation['name'];
		$return = $focus->$method($crmid, getTabid($module), $relation['related_tabid'], $relation['actions']);
		$custom_button = str_replace('&nbsp;','',$return['CUSTOM_BUTTON']);
		
		$onlyquery = false;
		$onlybutton = false;
		$currentModule = $module;
		
		return $custom_button;
	}
	//crmv@57221e

	//crmv@44609
	function relate($module1, $record1, $module2, $record2) {
		if (!is_array($record2)) $record2 = array($record2);
		if (empty($record2)) return false;
		
		if ($this->isModuleRelated($module1, $module2)) {
			$moduleRelations = $this->getRelations($module1, null, $module2);
			if (!empty($moduleRelations)) {
				foreach($moduleRelations as $moduleRelation) {
					$moduleRelationType = $moduleRelation->getType();
					if ($moduleRelationType == ModuleRelation::$TYPE_1TO1) {
						// TODO
					} elseif ($moduleRelationType == ModuleRelation::$TYPE_1TON) {
						foreach ($record2 as $r) {
							$focus = CRMEntity::getInstance($module2);
							$focus->retrieve_entity_info_no_html($r, $module2);
							$focus->mode = 'edit';
							$focus->id = $r;
							$focus->column_fields[$moduleRelation->fieldname] = $record1;
							$focus->save($module2);
						}
					} elseif ($moduleRelationType == ModuleRelation::$TYPE_NTO1) {
						$focus = CRMEntity::getInstance($module1);
						$focus->retrieve_entity_info_no_html($record1, $module1);
						$focus->mode = 'edit';
						$focus->id = $record1;
						$focus->column_fields[$moduleRelation->fieldname] = $record2[0];
						$focus->save($module1);
					} elseif ($moduleRelationType == ModuleRelation::$TYPE_NTON) {
						$focus = CRMEntity::getInstance($module1);
						$focus->save_related_module($module1, $record1, $module2, $record2);
					}
					break;
				}
			}
		}
	}
	//crmv@44609e
}
?>