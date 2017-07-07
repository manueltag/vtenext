<?php

/* cmrv@97862 */

require_once('include/QueryGenerator/QueryGenerator.php');


class ProductsBLockMeta {
	
	public function getEntityBaseTable() {
		global $table_prefix;
		return $table_prefix.'_inventoryproductrel';
	}
	
	public function getIdColumn() {
		return 'lineitem_id';
	}
}

/**
 * Class to generate query for the fake ProductsBlock module
 */
class QueryGeneratorPB extends QueryGenerator {
	
	public function __construct($user, &$reportrun, &$reports) {
		$this->module = 'ProductsBlock';
		$this->reportrun = $reportrun;
		$this->reports = $reports;
		
		$this->fields = array();
		$this->fieldAlias = array();
		$this->referenceModuleMetaInfo = array();
		$this->moduleNameFields = array();
		$this->whereFields = array();
		$this->appendSelectFields = array();
		$this->appendRawSelect = array();
		$this->appendWhereClause = '';
		$this->appendFromClause = '';
		
		$meta = new ProductsBLockMeta();
		$this->meta = $meta;
		$this->referenceModuleMetaInfo[$this->module] = $meta;
	}
	
	public function getQuery($onlyFields = false) {
		global $table_prefix;
		
		if(empty($this->query)) {
			$allFields = array_merge($this->whereFields,$this->fields);
			$allFields = array_unique($allFields);
			
			$query = 'SELECT ';
			$query .= $this->getSelectClauseColumnSQL();
			$query .= $this->getFromClause();
			$query .= $this->getWhereClause();
			$query = $this->cleanUpQuery($query); // crmv@49398

			$this->query = $query;
		}
		
		return $this->query;
	}
	
	public function getSelectClauseColumnSQL(){
		$columns = array();
		foreach ($this->fields as $field) {
			$sql = $this->getSQLColumn($field);
			if (!in_array($sql,$columns)){
				$columns[] = $sql;
			}
		}
		if (is_array($this->appendSelectFields) && count($this->appendSelectFields) > 0) {
			foreach ($this->appendSelectFields as $field) {
				$sql = $this->getSQLColumn($field);
				if (!in_array($sql,$columns)){
					$columns[] = $sql;
				}
			}
		}
		if (is_array($this->appendRawSelect) && count($this->appendRawSelect) > 0) {
			foreach ($this->appendRawSelect as $rsel) {
				$columns[] = $rsel;
			}
		}
		$this->columns = implode(',',$columns);
		
		return $this->columns;
	}
	
	public function getFromClause() {
		global $adb,$table_prefix,$current_user,$current_language;  //crmv@74933
		
		if(!empty($this->fromClause)) {
			return $this->fromClause;
		}
		
		$baseTable = $this->meta->getEntityBaseTable();
		$baseTableAlias = $baseTable;
		
		$tableList = array();
		
		// this is not needed now
		/*$allfields = array_merge($this->whereFields, $this->fields);
		$allfields = array_unique($allfields);
		foreach ($allfields as $fieldname) {
			$finfo = $this->reports->getFieldInfoByName($this->module, $fieldname);
			
			if ($finfo['wstype'] == 'reference') {
				$moduleList = $finfo['relmodules'];
				$crmalias = 'crmentityRel'.$finfo['fieldid'];
				$tableList[$crmalias] = array(
					'type' => 'left',
					'table' => $table_prefix.'_crmentity',
					'alias' => $crmalias,
					'condition' => "$crmalias.crmid = $baseTableAlias.{$finfo['columnname']} AND $crmalias.deleted = 0",
				);
				foreach ($moduleList as $relmod) {
					$modmeta = $this->getMeta($relmod);
					$modtable = $modmeta->getEntityBaseTable();
					$tableIndexList = $modmeta->getEntityTableIndexList();
					$modidx = $tableIndexList[$modtable];
					
					$alias = substr(strtolower($relmod).'Rel'.$this->module.$finfo['fieldid'], 0, 29);
					$tableList[$alias] = array(
						'type' => 'left',
						'table' => $modtable,
						'alias' => $alias,
						'condition' => "$alias.$modidx = $crmalias.crmid",
					);
				}
			}
		}*/
		
		$sql = " FROM $baseTable ";
		
		unset($tableList[$baseTable]);
		foreach ($tableList as $joininfo) {
			$join = ($joininfo['type'] == 'left' ? 'LEFT JOIN' : 'INNER JOIN');
			$sql .= " $join {$joininfo['table']} {$joininfo['alias']} ON {$joininfo['condition']}";
		}
		
		// here are all the joins!!
		if ($this->appendFromClause) $sql .= $this->appendFromClause;

		$this->fromClause = $sql;
		
		return $sql;		
	}
	
	public function getWhereClause() {
		// TODO: done in the reportrun for the moment
		return "";
	}
	
	public function getSQLColumn($name,$onlyfields = false) {
		global $table_prefix;
		
		$aliases = $this->fieldAlias[$name];
		$baseTable = $this->meta->getEntityBaseTable();
		$sqlcolumn = $baseTable.'.'.$name;
		
		if (!empty($aliases)) {
			$cols = array();
			foreach ($aliases as $alias) {
				$cols[] = $sqlcolumn . " AS \"$alias\"";
			}
			$sqlcolumn = implode(',', $cols);
		}
		return $sqlcolumn;
	}
}

