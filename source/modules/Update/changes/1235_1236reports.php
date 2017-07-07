<?php

// this script can also be executed outside the update process
global $root_directory;
if (empty($root_directory)) {
	// but first you have to manually remove this
	die('Remove die');
	// include necessary files
	require('../../../config.inc.php');
	chdir($root_directory);
	require_once('include/utils/utils.php');
}

require_once('modules/Reports/Reports.php');
require_once('modules/Reports/ReportRun.php');

/* Report migration code */

global $reportsInst, $RRUN;

$reportsInst = Reports::getInstance();
$RRUN = ReportRun::getInstance();

global $currentReportid;

function reportMigLog($text) {
	global $currentReportid;
	echo "[ReportMigration] [$currentReportid] INFO: ".$text."<br>\n";
}

function getReportRefFieldInfo($primodule, $secmodules, $value) {
	global $reportsInst;
	
	$value = preg_replace('/,.*$/', '', $value);
	$value = trim($value, '$');
	
	list($module, $fieldname) = explode('#', $value);
	if ($module == 'Calendar') $module = 'Events';

	$finfo = $reportsInst->getFieldInfoByName($module, $fieldname);
	
	return $finfo;
}

function getReportFieldInfo($primodule, $secmodules, $fieldrow) {
	global $adb, $table_prefix, $reportsInst, $RRUN;

	$pieces = explode(':', $fieldrow);
	$tablename = $pieces[0];
	$column = $pieces[1];
	$label = $pieces[2];
	$fieldname = $pieces[3];
	
	$allmods = array_unique(array_merge(array($primodule), $secmodules));
	
	list($fieldmod, $xx) = explode('_', $label, 2);
	
	if ($primodule == 'Products' && $fieldmod == 'Products' && $fieldname == 'productname' && $secmodules[0] == 'Quotes') {
		$fieldmod = 'ProductsBlock';
		$fieldname = 'id';
		$column = 'id';
	} elseif ($tablename == $table_prefix.'_inventoryproductrel') {
		$fieldmod = 'ProductsBlock';
		//if ($fieldname == 'serviceid') $fieldname = 'productid';
		if ($fieldname == 'serviceid') return false;
	} elseif (preg_match("/Rel{$fieldmod}\$/", $tablename)) {
		$tabid = getTabid($fieldmod);
	} elseif (preg_match("/{$fieldmod}\$/", $tablename) && $fieldname != 'assigned_user_id' && $fieldmod != $primodule && count($secmodules) > 0) {
		$tablename = str_replace($fieldmod, '', $tablename);
		if (preg_match("/Rel\$/", $tablename)) $tablename = str_replace('Rel', '', $tablename);
		
		

		// get tabid from tablename
		$res2 = $adb->limitPquery("SELECT f.tabid FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_tab t ON t.tabid = f.tabid WHERE f.tablename = ? AND t.name IN (".generateQuestionMarks($secmodules).")", 0, 1, array($tablename, $secmodules));
		$tabid = $adb->query_result_no_html($res2, 0, 'tabid');
		if (!$tabid) {
			$tabid = getTabid($fieldmod);
		}
		if ($fieldmod == 'Contacts' && $fieldname == 'account_id') {
			$tabid = getTabid($fieldmod);
		}
		
		$fieldmod = getTabName($tabid);
	} else {
		$tabid = getTabid($fieldmod);
	}
	if (!$tabid && $fieldmod != 'ProductsBlock') {
		reportMigLog("Module for field $fieldname not found (module: $fieldmod, table: $tablename). Field skipped.");
		//preprint($pieces);
		return false;
	}

	$relprodblock = false;
	if ($fieldmod == 'ProductsBlock' && $fieldname == 'productid') {
		/*$fieldname = 'productname';
		$fieldmod = 'Products';
		$relprodblock = true;
		$tabid = getTabid($fieldmod);
		*/
	} elseif ($fieldmod == 'Faq' && $fieldname == 'crmid') {
		$fieldname = 'faq_no';
	} elseif ($fieldmod == 'Contacts') {
		if ($fieldname == 'contact_id') {
			$fieldmod = $primodule;
			$tabid = getTabid($fieldmod);
		}
	} elseif ($fieldmod == 'Accounts') {
		if ($fieldname == 'account_id' && $primodule != 'Products' && strpos($label, 'Member_Of') === false) {
			$fieldmod = $primodule;
			$tabid = getTabid($fieldmod);
		}
	}

	if ($tabid == 9) {
		$tabid = 16; // prioritize the Events module
		$fieldmod = 'Events';
	}
	
	if ($fieldmod == 'ProductsBlock') {
		$finfo = $reportsInst->getPBFieldInfo($fieldname);
		$relprodblock = true;
	} else {
		// crmid field is not supported (it's not a field!)
		if ($fieldname == 'crmid' && $column == 'crmid') return false;
		
		$res2 = $adb->pquery("SELECT fieldid, fieldname, block, presence, displaytype FROM {$table_prefix}_field WHERE tabid = ? AND fieldname = ?", array($tabid, $fieldname));
		$finfo = $adb->fetchByAssoc($res2, -1, false);
		if (!$finfo) {
			// try with the secondary name
			$res2 = $adb->pquery("SELECT fieldid, fieldname, block, presence, displaytype FROM {$table_prefix}_field WHERE tabid = ? AND columnname = ?", array($tabid, $column));
			$finfo = $adb->fetchByAssoc($res2, -1, false);
			if (!$finfo) {
			
				// try with the stupid task/event
				if ($tabid == 9 || $tabid == 16) {
					$tabid = ($tabid == 9 ? 16 : 9);
					$res2 = $adb->pquery("SELECT fieldid, fieldname, block, presence, displaytype FROM {$table_prefix}_field WHERE tabid = ? AND columnname = ?", array($tabid, $column));
					$finfo = $adb->fetchByAssoc($res2, -1, false);
					if ($finfo && $finfo['fieldid'] > 0 && $tabid == 9) {
						$fieldmod = 'Calendar';
					}
				}
				
				if (!$finfo) {
					// otherwise wasn't found
					reportMigLog("Field $fieldname / $column not found (tabid: $tabid, module: $fieldmod). Field skipped.");
					//preprint($pieces);
					return false;
				}
			}
			$fieldname = $column;
		}
	}
	
	if ($finfo['presence'] == 1) {
		reportMigLog("The field $fieldname / $column is not active. Skipped.");
		return false;
	} elseif ($finfo['module'] != 'ProductsBlock' && $finfo['block'] > 0) {
		$res3 = $adb->pquery("SELECT display_status FROM {$table_prefix}_blocks WHERE blockid = ?", array($finfo['block']));
		$binfo = $adb->fetchByAssoc($res3, -1, false);
		if ($binfo['display_status'] == 0) {
			// the block is hidden, don't use the field
			return false;
		}
	}
	
	$finfo['module'] = $fieldmod;
	if ($relprodblock) {
		$finfo['prodblock'] = true;
	}

	return $finfo;
}

function getReportFieldRelation($primodule, $secmodules, &$finfo) {
	global $adb, $table_prefix;
	
	$newrel = false;
	$fieldmod = $finfo['module'];
	
	// convert calendar to events, since it's the most common
	if ($fieldmod == 'Calendar') $fieldmod = 'Events';

	if ($fieldmod != $primodule) {

		$RM = RelationManager::getInstance();
		$RM->enablePBRelations();
	
		$first = $primodule;
		$second = $fieldmod;
		$parent = $primodule;
		
		if (isProductModule($primodule) && isInventoryModule($fieldmod)) {
			$first = 'ProductsBlock';
			$prevrel = $RM->getRelations($first, null, array($second));
			$prevrel = $prevrel[0];
			if ($prevrel) {
				$parent = $first.'_'.$second.'_fld_'.$prevrel->fieldid;
			}
		} elseif (isInventoryModule($primodule) && isProductModule($fieldmod)) {
			$first = 'ProductsBlock';
			$prevrel = $RM->getRelations($primodule, null, array($first));
			$prevrel = $prevrel[0];
			if ($prevrel) {
				$parent = $primodule.'_'.$first.'_fld_'.$prevrel->fieldid;
			}
		}
	
		$rels = $RM->getRelations($first, null, array($second));
		$rel = $rels[0]; // take the 1st
		if (!$rel) {
			reportMigLog("Relation $first -> $second not found. Field skipped.");
			return false;
		}
		// add the relation
		$type = $rel->getType();
		$relfieldid = $rel->fieldid;
		$relid = $rel->relationid;
		$name = $first.'_'.$second.'_'.($type == ModuleRelation::$TYPE_NTON ? 'rel' : 'fld').'_'.($relfieldid ?: $relid);
		
		$newrel = array(
			'name' => $name,
			'module' => $second,
			'type' => $type,
			'parent' => $parent,
			'fieldid' => $relfieldid,
			'relationid' => $relid,
		);
	}
	
	return $newrel;
}


// report migration
$res = $adb->pquery(
	"SELECT r.*, rm.primarymodule, rm.secondarymodules 
	FROM {$table_prefix}_report r 
	LEFT JOIN {$table_prefix}_reportmodules rm ON r.reportid = rm.reportmodulesid 
	WHERE state != ?",
	array('SDK')
);
if ($res) {
	while ($row = $adb->fetchByAssoc($res, -1, false)) {
		$reportid = $row['reportid'];
		$module = $row['primarymodule'];
		if (empty($module) || !isModuleInstalled($module)) continue;
		
		$currentReportid = $reportid;

		// convert calendar to events, since it's the most common
		if ($module == 'Calendar') $module = 'Events';
		
		$secmodules = explode(':', $row['secondarymodules']);
		foreach ($secmodules as &$smod) {
			if ($smod == 'Calendar') $smod = 'Events';
		}
		unset($smod);

		$isSecInventory = $isSecProducts = false;
		foreach ($secmodules as $smod) {
			if (isInventoryModule($smod)) $isSecInventory = $smod;
			if (isProductModule($smod)) $isSecProducts = $smod;
		}

		if (isProductModule($module) && $isSecInventory) {
			$secmodules[] = 'ProductsBlock';
		} elseif (isInventoryModule($module) && $isSecProducts) {
			$secmodules[] = 'ProductsBlock';
		}

		unset($row['queryid'], $row['category'], $row['secondarymodules'], $row['primarymodule']);
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_selectcolumn WHERE queryid = ? AND columnname != ? ORDER BY columnindex", array($reportid, 'none'));
		$fields = array();
		while($field = $adb->fetchByAssoc($res2,-1,false)) $fields[] = $field;
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_reportdatefilter WHERE datefilterid = ?", array($reportid));
		$datefilters = array();		
		while($date = $adb->fetchByAssoc($res2,-1,false)) $datefilters[] = $date;
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_relcriteria WHERE queryid = ? ORDER BY columnindex", array($reportid));
		$advfilters1 = array();
		while($filter = $adb->fetchByAssoc($res2,-1,false)) $advfilters1[] = $filter;
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_relcriteria_grouping WHERE queryid = ? ORDER BY groupid", array($reportid));
		$advfilters2 = array();
		while($filter = $adb->fetchByAssoc($res2,-1,false)) $advfilters2[] = $filter;
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_reportsortcol WHERE reportid = ? AND columnname != ? ORDER BY sortcolid", array($reportid, 'none'));
		$grouping = array();
		while($group = $adb->fetchByAssoc($res2,-1,false)) $grouping[] = $group;
		
		$res2 = $adb->pquery("SELECT * FROM {$table_prefix}_reportsummary WHERE reportsummaryid = ? ORDER BY summarytype", array($reportid));
		$totals = array();
		while($total = $adb->fetchByAssoc($res2,-1,false)) $totals[] = $total;
		
		// make unique the grouping (sometimes there are duplicte rows)
		$grp2 = array();
		foreach ($grouping as $grp) {
			$grp2[$grp['columnname']] = $grp;
		}
		$grouping = $grp2;
		
		// now convert to the correct structures
		$relations = array();
		$newfields = array();
		$stdfilters = array();
		$newadvfilters = array();
		$totalfields = array();
		$summaryfields = array();
		$groupfields = 0;
		$groupadded = array();
		
		// main module
		$relations[$module] = array('name' => $module, 'module' => $module, 'parent' => null);

		// add the PB relation
		if (isProductModule($module) && $isSecInventory) {
			$pbinfo = array('module' => 'ProductsBlock');
			$pbrel = getReportFieldRelation($module, $secmodules, $pbinfo);
			if ($pbrel) {
				$relations[$pbrel['module']] = $pbrel;
			}
			
			$pbinfo = array('module' => $isSecInventory);
			$pbrel2 = getReportFieldRelation('ProductsBlock', $secmodules, $pbinfo);
			if ($pbrel && $pbrel2) {
				$pbrel2['parent'] = $pbrel['name'];
				$relations[$pbrel2['module']] = $pbrel2;
			}
		}
		
		$fieldsDupCheck = array();
		foreach ($fields as $field) {
			
			$finfo = getReportFieldInfo($module, $secmodules, $field['columnname']);
			if (!$finfo) continue;
			
			// check for duplicates
			$dupKey = $finfo['fieldid'].$finfo['module'];
			if (in_array($dupKey, $fieldsDupCheck)) continue;
			$fieldsDupCheck[] = $dupKey;
			
			$nf = array('fieldid' => $finfo['fieldid'], 'module' => $finfo['module'], 'fieldname' => $finfo['fieldname']);
			
			$fieldrel = getReportFieldRelation($module, $secmodules, $finfo);
			if ($fieldrel) {
				if (empty($relations[$fieldrel['module']])) {
					$relations[$fieldrel['module']] = $fieldrel;
				}
				$nf['relation'] = $fieldrel['name'];
			}
			
			// add the grouping info to the field
			if ($grouping && count($grouping) > 0) {
				$groupfound = false;
				foreach ($grouping as $groupinfo) {
					if ($groupinfo['columnname'] == $field['columnname']) {
						$groupfound = true;
						$nf['group'] = true;
						$nf['sortorder'] = ($groupinfo['sortoroder'] == 'Ascending' ? 'ASC' : 'DESC');
						if ($groupinfo['view_count_lvl'] == 1) $nf['summary'] = true;
						++$groupfields;
						$groupadded[] = $groupinfo['columnname'];
						break;
					}
				}
			}
			
			if ($field['formula']) $nf['formula'] = $field['formula'];
			
			// add the field
			$newfields[] = $nf;
		}
		
		// add missing grouping fields
		if (count($grouping) > $groupfields) {
			foreach ($grouping as $groupinfo) {
			
				if (in_array($groupinfo['columnname'], $groupadded)) continue;
				
				// skip the crmid, pretend it was addedd
				$pieces = explode(':', $groupinfo['columnname']);
				if ($pieces[1] == 'crmid' && $pieces[3] == 'crmid') {
					++$groupfields;
					continue;
				}
			
				$finfo = getReportFieldInfo($module, $secmodules, $groupinfo['columnname']);
				if (!$finfo) continue;
				
				$nf = array('fieldid' => $finfo['fieldid'], 'module' => $finfo['module'], 'fieldname' => $finfo['fieldname']);
				
				$fieldrel = getReportFieldRelation($module, $secmodules, $finfo);
				if ($fieldrel) {
					if (empty($relations[$fieldrel['module']])) {
						$relations[$fieldrel['module']] = $fieldrel;
					}
					$nf['relation'] = $fieldrel['name'];
				}
				
				$nf['group'] = true;
				$nf['sortorder'] = ($groupinfo['sortoroder'] == 'Ascending' ? 'ASC' : 'DESC');
				if ($groupinfo['view_count_lvl'] == 1) $nf['summary'] = true;
				++$groupfields;
				
				$newfields[] = $nf;
			}
		}
		
		
		// stdfilters
		if ($datefilters && count($datefilters) > 0) {
			foreach ($datefilters as $datefilter) {
				// some non accessible field, no idea how could happen
				if ($datefilter['datecolumnname'] == 'Not Accessible') continue;
				
				list($table, $column, $fieldname, $label) = explode(':', $datefilter['datecolumnname'], 4);
				
				$tabid = getTabid($module);
				
				// skip some invalid types
				if ($datefilter['datefilter'] == 'custom' && (empty($datefilter['startdate']) || empty($datefilter['enddate']))) continue;
				
				if ($label === null) {
					// there were only 3 pieces
					$fieldname = $column;
				}
				
				// search the fieldid
				$res2 = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE fieldname = ? and tabid = ?", array($fieldname, $tabid));
				$finfo = $adb->fetchByAssoc($res2, -1, false);
				if (!$finfo) {
				
					// try by column name
					$res2 = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE tablename = ? and columnname = ? and tabid = ?", array($table, $column, $tabid));
					$finfo = $adb->fetchByAssoc($res2, -1, false);
					if (!$finfo) {
					
						// try the secondary module
						if (count($secmodules) > 0) {
							$tabid = getTabid($secmodules[0]);
							$res2 = $adb->pquery("SELECT fieldid FROM {$table_prefix}_field WHERE fieldname = ? and tabid = ?", array($fieldname, $tabid));
							$finfo = $adb->fetchByAssoc($res2, -1, false);
						}
					
						if (!$finfo) {
							reportMigLog("Datefilter field (fieldname: $fieldname, tabid: $tabid) not found, skipped.");
							//preprint($datefilter);
							continue;
						}
					}
					
				}
				$fieldid = $finfo['fieldid'];
				
				$stdfilter = array(
					'fieldid' => $fieldid,
					'type' => 'datefilter',
					'value' => $datefilter['datefilter'],
					'startdate' => ($datefilter['datefilter'] == 'custom' ? $datefilter['startdate'] : null),
					'enddate' => ($datefilter['datefilter'] == 'custom' ? $datefilter['enddate'] : null),
				);
				
				$stdfilters[] = $stdfilter;
			}
		}
		
		// advanced filters
		if ($advfilters1 && $advfilters2 && count($advfilters1) > 0 && count($advfilters2) > 0) {
			
			foreach ($advfilters2 as $group) {
				$advgroup = array();
				
				foreach ($advfilters1 as $filter) {
					if ($group['groupid'] != $filter['groupid']) continue;
				
					$finfo = getReportFieldInfo($module, $secmodules, $filter["columnname"]);
					if (!$finfo) continue;

					$cond = array(
						'fieldid' => $finfo['fieldid'],
						'comparator' => $filter['comparator'],
					);
			
					$fieldrel = getReportFieldRelation($module, $secmodules, $finfo);

					if ($fieldrel) {
						if (empty($relations[$fieldrel['module']])) {
							$relations[$fieldrel['module']] = $fieldrel;
						}	
						$cond['relation'] = $fieldrel['name'];
					}
					
					$reference = false;
					if (substr($filter['value'], 0, 1) == '$' && substr($filter['value'], -1) == '$') {
						$reference = true;
					}
					
					if ($reference) {
						// reference fields
						$finfo = getReportRefFieldInfo($module, $secmodules, $filter['value']);
						if ($finfo) {
							$cond['ref_fieldid'] = $finfo['fieldid'];
							$fieldrel = getReportFieldRelation($module, $secmodules, $finfo);
							if ($fieldrel) {
								if (empty($relations[$fieldrel['module']])) {
									$relations[$fieldrel['module']] = $fieldrel;
								}
								$cond['ref_relation'] = $fieldrel['name'];
							}
						} else {
							reportMigLog("Reference field ({$filter['value']}) not found");
						}
					} else {
						$compvalue = $filter['value'];
					}
					
					$cond['value'] = $compvalue;
					
					if ($reference) {
						$cond['reference'] = true;
					}
					if ($filter['column_condition']) {
						$cond['glue'] = strtolower($filter['column_condition']);
					}
					$advgroup['conditions'][] = $cond;
				}
				
				if ($group['group_condition']) {
					$advgroup['glue'] = strtolower($group['group_condition']);
				}

				$newadvfilters[] = $advgroup;
			}
			
		}
		
		// totals
		if ($totals && count($totals) > 0) {
			foreach ($totals as $total) {
				list($xx, $tablename, $column, $label, $yy) = explode(':', $total['columnname'], 5);
				$aggregator = strtoupper(substr($label, strrpos($label, '_')+1));
				if (!$aggregator || !$column) continue;
				
				if (preg_match("/^{$table_prefix}_inventoryproductrel/", $tablename)) {
					// it's a special field, get the special crmid
					$finfo = $reportsInst->getFieldInfoByName('ProductsBlock', $column);

				} else {
				
					$sql = "SELECT fieldid, presence FROM {$table_prefix}_field WHERE tablename = ? AND columnname = ?";
					if ($tablename == $table_prefix.'_activity') {
						// give priority to Events module (tabid 16)
						$sql .= " AND tabid IN (16,9) ORDER BY tabid DESC";
					}
				
					// search the fieldid
					$res2 = $adb->pquery($sql, array($tablename, $column));
					$finfo = $adb->fetchByAssoc($res2, -1, false);
					
					if ($finfo && $finfo['presence'] == 1) {
						reportMigLog("Total field (tablename: $tablename, column: $column) is disabled, skipped.");
						//preprint($total);
						continue;
					}
				}
				
				if (!$finfo) {
					reportMigLog("Total field (tablename: $tablename, column: $column) not found, skipped.");
					//preprint($total);
					continue;
				}
				$fieldid = $finfo['fieldid'];
				
				if ($total['show_summary'] == 1) {
					if (!$summaryfields[$fieldid] && count($summaryfields) == 0) {
						$summaryfields[$fieldid] = array(
							'fieldid' => $fieldid,
							'aggregators' => array($aggregator),
						);
					} elseif ($summaryfields[$fieldid]) {
						$summaryfields[$fieldid]['aggregators'][] = $aggregator;
						$summaryfields[$fieldid]['aggregators'] = array_unique($summaryfields[$fieldid]['aggregators']);
					}
				}
				
				$totalfield = array(
					'fieldid' => $fieldid,
					'aggregator' => $aggregator,
				);
				$totalfields[] = $totalfield;
			}
		}
		
		// check for missing group fields
		if (count($grouping) > $groupfields) {
			reportMigLog("Some group fields not found.");
			//preprint($grouping);
		}
		
		$config = array_merge($row, array(
			'module' => $module,
			'relations' => array_values($relations),
			'fields' => $newfields,
			'stdfilters' => $stdfilters,
			'advfilters' => $newadvfilters,
			'totals' => $totalfields,
			'summary' => array_values($summaryfields),
		));
		
		//preprint($config);
		
		// DEBUG: execute the report!
		// uncomment to execute the reports on the fly!
		
		/*if (vtlib_isModuleActive($module)) {
		
			global $current_user;
			if (!$current_user) {
				$current_user = CRMEntity::getInstance('Users');
				$current_user->id = 1;
			}
		
			$RR = ReportRun::getInstance();
			$RR->setReportInfo($config);
			$RR->reportid = $reportid;
			$RR->setOutputFormat('NULL', true);
			$RR->setReportTab('MAIN');
		
			echo "<br>\nExecuting report #$reportid: {$config['reportname']}<br>\n";
			$RR->setQueryLimit(0, 20);
			$ret = $RR->generateReport();
			$sql = $RR->getGeneratedQuery();
			//echo "<br>$sql<br>";
			
			flush();
			
			// generate grouping result
			if ($groupfound) {
				echo "<br>\nExecuting grouping query...<br>\n";
				$RR->setQueryLimit(0, 20);
				$RR->setReportTab('COUNT');
				$RR->reuseSubqueries();
				$ret = $RR->generateReport();
				$sql = $RR->getGeneratedQuery();
				//echo "<br>$sql<br>";
			}
			
			// generate total result
			if (count($totalfields) > 0) {
				echo "<br>\nExecuting total query...<br>\n";
				$RR->clearQueryLimit();
				$RR->setReportTab('TOTAL');
				$RR->reuseSubqueries();
				$ret = $RR->generateReport();
				$sql = $RR->getGeneratedQuery();
				//echo "<br>$sql<br>";
			}
		
			flush();
		}*/
		
		// prepare the empty line
		if($reportid != ''){
			$adb->pquery("DELETE FROM {$table_prefix}_reportconfig WHERE reportid = ?", array($reportid));
			$adb->pquery("INSERT INTO {$table_prefix}_reportconfig (reportid, module) VALUES (?,?)", array($reportid, $module));
			$r = $reportsInst->updateReport($reportid, $config);
			if (!$r) {
				reportMigLog("Unable to migrate report #{$reportid}.");
			}
		}

	}
}

// rename old report tables, in case the conversion wasn't perfect
$renames = array(
	$table_prefix.'_reportmodules',
	$table_prefix.'_reportdatefilter',
	$table_prefix.'_reportsortcol',
	$table_prefix.'_reportsummary',
	$table_prefix.'_relcriteria',
	$table_prefix.'_relcriteria_grouping',
	$table_prefix.'_selectcolumn',
);
foreach ($renames as $table) {
	if (Vtiger_Utils::CheckTable($table)) {
		$sql = $adb->datadict->RenameTableSQL($table,$table.'_old');
		if ($sql){
			$adb->datadict->ExecuteSQLArray($sql);
		}
	}
}

echo "<b>Notice</b>: Reports have ben migrated to the new format.<br>\n";
echo "Reports involving Calendar module now handle Events and Tasks separately and by default are converted to show the Events only.<br>\n";
echo "Please review them to ensure their correctness.<br>\n";
