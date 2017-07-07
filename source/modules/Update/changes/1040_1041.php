<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// globals
global $adb, $table_prefix;

// some functions
if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;

		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}

		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

if (!function_exists('getPrimaryKeyName')) {
	function getPrimaryKeyName($tablename) {
		global $adb, $dbconfig;
		$ret = '';
		if ($adb->isMysql()) {
			// for mysql just check if it exists
			$res = $adb->query("SHOW KEYS FROM {$tablename} WHERE Key_name = 'PRIMARY'");
			if ($res && $adb->num_rows($res) > 0) $ret = 'PRIMARY';
		} elseif ($adb->isMssql()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn from INFORMATION_SCHEMA.TABLE_CONSTRAINTS where CONSTRAINT_CATALOG = ? and TABLE_NAME = ? and CONSTRAINT_TYPE = 'PRIMARY KEY'", array($dbconfig['db_name'], $tablename));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		} elseif ($adb->isOracle()) {
			$res = $adb->pquery("SELECT CONSTRAINT_NAME as cn FROM all_constraints cons	WHERE cons.table_name = ? AND cons.constraint_type = 'P'", array(strtoupper($tablename)));
			if ($res) $ret = $adb->query_result_no_html($res, 0, 'cn');
		}
		return $ret;
	}
}

if (!function_exists('dropPrimaryKey')) {
	function dropPrimaryKey($tablename) {
		global $adb;
		if ($adb->isMysql()) {
			$keyname = getPrimaryKeyName($tablename);
			if ($keyname == 'PRIMARY') $adb->query("ALTER TABLE {$tablename} DROP PRIMARY KEY");
		} elseif ($adb->isMssql() || $adb->isOracle()) {
			$keyname = getPrimaryKeyName($tablename);
			$adb->query("ALTER TABLE {$tablename} DROP CONSTRAINT {$keyname}");
		} else {
			echo "Drop Primary key not supported for this database";
		}
	}
}




/* crmv@68357 - support of event invitation and replies with ics files through emails */

// add method column
addColumnToTable($table_prefix.'_messages_attach', 'contentmethod', 'C(63)');

// create table for ical attachments (See RFC 6047 https://tools.ietf.org/html/rfc6047 )
$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_messages_ical">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="messagesid" type="I" size="19">
				<key/>
			</field>
			<field name="sequence" type="I" size="19">
				<key/>
			</field>
			<field name="uuid" type="C" size="127" />
			<field name="method" type="C" size="31" />
			<field name="partecipation" type="I" size="2">
				<DEFAULT value="0"/>
			</field>
			<field name="content" type="XL" />
			<index name="messages_ical_uuid_idx">
				<col>uuid</col>
			</index>
		</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_messages_ical')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// create the field for the calendar

// aggiungo campo
$fields = array(
	'ical_uuid_task'	=> array('module'=>'Calendar', 'block'=>'LBL_TASK_INFORMATION',	'name'=>'ical_uuid',	'label'=>'ICalUUID',		'table'=>"{$table_prefix}_activity", 	'column'=>'ical_uuid',	'columntype'=>'C(127)',	'typeofdata'=>'V~O', 	'uitype'=>1, 'readonly'=>99, 'masseditable'=>0, 'quickcreate'=>1),
	'ical_uuid_event'	=> array('module'=>'Events', 'block'=>'LBL_EVENT_INFORMATION',	'name'=>'ical_uuid',	'label'=>'ICalUUID',		'table'=>"{$table_prefix}_activity", 	'column'=>'ical_uuid',	'columntype'=>'C(127)',	'typeofdata'=>'V~O', 	'uitype'=>1, 'readonly'=>99, 'masseditable'=>0, 'quickcreate'=>1),
);

$fieldRet = Update::create_fields($fields);

// and create index on it
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL('activity_caluuid_idx', "{$table_prefix}_activity", 'ical_uuid'));

require_once('modules/Calendar/iCal/iCalendar_rfc2445.php');

// now calculate the uuids (use db internal functions, to be lightning fast!)
if ($adb->isMysql()) {
	$adb->query("UPDATE {$table_prefix}_activity SET ical_uuid = UUID() WHERE ical_uuid IS NULL OR ical_uuid = ''");
/*
} elseif ($adb->isMssql()) {
	// not tested!!!
	$adb->query("UPDATE {$table_prefix}_activity SET ical_uuid = NEWID() WHERE ical_uuid IS NULL OR ical_uuid = ''");
*/
} else {
	// fallback on slow method
	$res = $adb->query("SELECT activityid FROM {$table_prefix}_activity WHERE ical_uuid IS NULL OR ical_uuid = ''");
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$uuid = rfc2445_guid();
		$adb->pquery("UPDATE {$table_prefix}_activity SET ical_uuid = ? WHERE activityid = ?", array($uuid, $row['activityid']));
	}
}

$trans = array(
	'Calendar' => array(
		'it_it' => array(
			'LBL_PREVIEW_INVITATION' => 'Anteprima invito',
			'ANSWER_TO_INVITATION' => 'Risposta all\'invito',
			'LBL_INVITATION_YES' => 'Partecipa',
			'LBL_INVITATION_NO' => 'Non partecipa',
			'LBL_INVITATION_ACCEPTED' => 'ha accettato l\'invito al proprio evento.',
			'LBL_INVITATION_DECLINED' => 'ha declinato l\'invito al proprio evento.',
			'LBL_INVITATION_ACCEPTED_SUBJECT' => 'Risposta all\'invito (accettato)',
			'LBL_INVITATION_DECLINED_SUBJECT' => 'Risposta all\'invito (declinato)',
		),
		'en_us' => array(
			'LBL_PREVIEW_INVITATION' => 'Invitation preview',
			'ANSWER_TO_INVITATION' => 'Invitation answer',
			'LBL_INVITATION_YES' => 'Attends',
			'LBL_INVITATION_NO' => 'Doesn\' attend',
			'LBL_INVITATION_ACCEPTED' => 'accepted the invitation to the event.',
			'LBL_INVITATION_DECLINED' => 'declined the invitation to the event.',
			'LBL_INVITATION_ACCEPTED_SUBJECT' => 'Invitation reply (accepted)',
			'LBL_INVITATION_DECLINED_SUBJECT' => 'Invitation reply (declined)',
		),
	),
	'ALERT_ARR' => array(
		'it_it' => array(
			'ANSWER_SENT' => 'La risposta e` stata inviata',
			'CONFIRM_LINKED_EVENT_DELETION' => 'Vuoi anche eliminare l\'evento collegato?',
		),
		'en_us' => array(
			'ANSWER_SENT' => 'The answer has been sent',
			'CONFIRM_LINKED_EVENT_DELETION' => 'Do you also want to delete the linked event?',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_WHEN' => 'Quando',
			'LBL_WHERE' => 'Dove',
			'LBL_ACCEPT_INVITATION' => 'Accetta invito',
			'LBL_ORGANIZER' => 'Organizzatore',
			'LBL_SHOW_PREVIEW' => 'Visualizza anteprima',
		),
		'en_us' => array(
			'LBL_WHEN' => 'When',
			'LBL_WHERE' => 'Where',
			'LBL_ACCEPT_INVITATION' => 'Accept invitation',
			'LBL_ORGANIZER' => 'Organizer',
			'LBL_SHOW_PREVIEW' => 'Show preview',
		),
	),
);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}



/* crmv@67929 - taxes totals in reports */

$IUtils = InventoryUtils::getInstance();

// add columns to modules
$invMods = getInventoryModules();

//add column to product rows
addColumnToTable($table_prefix.'_inventoryproductrel', 'tax_total', 'N(25.3)');

// now create the table with the taxes
$taxfields = '';

$allTaxes = $IUtils->getAllTaxes('all');
foreach ($allTaxes as $tax) {
	$taxfields .= "<field name=\"{$tax['taxname']}\" type=\"N\" size=\"25.3\" />\n";
}
$allShTaxes = $IUtils->getAllTaxes('all', 'sh');
foreach ($allShTaxes as $tax) {
	$taxfields .= "<field name=\"{$tax['taxname']}\" type=\"N\" size=\"25.3\" />\n";
}

$taxfields .= "<field name=\"tax_total\" type=\"N\" size=\"25.3\" />\n";
$taxfields .= "<field name=\"shtax_total\" type=\"N\" size=\"25.3\" />\n";

$schema = 
	'<?xml version="1.0"?>
	<schema version="0.3">
		<table name="'.$table_prefix.'_inventorytotals">
			<opt platform="mysql">ENGINE=InnoDB</opt>
			<field name="id" type="I" size="19">
				<key/>
			</field>'.
			$taxfields.
		'</table>
	</schema>';
if (!Vtiger_Utils::CheckTable($table_prefix.'_inventorytotals')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema));
}

// clean the inventoryshippingrel table from duplicates
$lastid = null;
dropPrimaryKey("{$table_prefix}_inventoryshippingrel");
$res = $adb->query("SELECT * FROM {$table_prefix}_inventoryshippingrel ORDER BY id ASC");
if ($res && $adb->num_rows($res) > 0) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		$id = $row['id'];
		if ($id === $lastid) continue;
		$adb->pquery("DELETE FROM {$table_prefix}_inventoryshippingrel WHERE id = ?", array($id));
		$adb->pquery("INSERT INTO {$table_prefix}_inventoryshippingrel (".implode(',', array_keys($row)).") VALUES (".generateQuestionMarks($row ).")", $row );
		$lastid = $id;
	}
}

// create the primary key
$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->DropIndexSQL('inventoryishippingrel_id_idx', "{$table_prefix}_inventoryshippingrel"));
$adb->query("ALTER TABLE {$table_prefix}_inventoryshippingrel ADD PRIMARY KEY (id)");

// simple function
if (!function_exists('arrayPluck')) {
	function arrayPluck(&$array, $keyname) {
		return array_map(create_function('$v', 'return $v["'.$keyname.'"];'), $array);
	}
}

// now fill it! WARNING: might be a very looong operation
foreach ($invMods as $module) {
	$focus = CRMEntity::getInstance($module);
	$res = $adb->query(
		"SELECT {$focus->table_name}.*, isr.* FROM {$focus->table_name} 
		INNER JOIN {$table_prefix}_crmentity ON crmid = {$focus->table_name}.{$focus->table_index}
		LEFT JOIN {$table_prefix}_inventoryshippingrel isr ON isr.id = {$focus->table_name}.{$focus->table_index}
		WHERE {$table_prefix}_crmentity.deleted = 0 and isr.id"
	);
	if ($res && $adb->num_rows($res) > 0) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$id = $row[$focus->table_index];
			$taxtype = $row['taxtype'];

			$recTaxes = array();
			$allTaxes = array();
			
			// now get the products
			$res2 = $adb->pquery("SELECT * from {$table_prefix}_inventoryproductrel WHERE id = ?", array($id));
			if ($res2 && $adb->num_rows($res2) > 0) {
				while ($row2 = $adb->FetchByAssoc($res2, -1, false)) {
					unset($row2['tax_total']);
					// set up the taxes array
					foreach ($row2 as $k => $v) {
						if (substr($k, 0, 3) == 'tax') {
							$row2['taxes'][$k] = $v;
						}
					}
					$rowTots = $IUtils->calcProductTotals($row2);
					if (is_array($rowTots['taxes'])) {
						if ($taxtype == 'group') {
							// no update, the taxes are for the group
							$recTaxes = $row2['taxes'];
							break;
						}
						$prodTax = array_combine(array_keys($row2['taxes']), arrayPluck($rowTots['taxes'], 'amount'));
						$allTaxes['tax_total'] += array_sum($prodTax);
						// do the update for the row
						$adb->pquery("UPDATE {$table_prefix}_inventoryproductrel SET tax_total = ? WHERE lineitem_id = ?", array(array_sum($prodTax), $row2['lineitem_id']));
						// calculate the totals
						foreach ($prodTax as $name => $tax) {
							$allTaxes[$name] += floatval($tax);
						}
						
					}
				}
			}
			
			// calculate the totals for the record
			$totalinfo = array(
				'nettotal' => floatval($row['subtotal']),
				's_h_amount' => floatval($row['s_h_amount']),
				'discount_percent' => $row['discount_percent'],
				'discount_amount' => $row['discount_amount'],
				'adjustment' => floatval($row['adjustment']),
				'taxes' => array(),
				'shtaxes' => array(),
			);

			// record taxes
			if ($taxtype == 'group' && count($recTaxes) > 0) {
				foreach ($recTaxes as $k => $v) {
					if (substr($k, 0, 3) == 'tax') {
						$totalinfo['taxes'][$k] = $v;
					}
				}
			}
			// S&H Taxes
			foreach ($row as $k => $v) {
				if (substr($k, 0, 5) == 'shtax') {
					$totalinfo['shtaxes'][$k] = $v;
				}
			}

			// do the calculation
			$totalPrices = $IUtils->calcInventoryTotals($totalinfo);
			
			// sum the taxes
			if ($taxtype == 'group' && is_array($totalPrices['taxes'])) {
				$totTax = array_combine(array_keys($totalinfo['taxes']), arrayPluck($totalPrices['taxes'], 'amount'));
				// calculate the totals
				foreach ($totTax as $name => $tax) {
					$allTaxes[$name] += floatval($tax);
				}
				$allTaxes['tax_total'] = array_sum($totTax);
			}
			
			// sum the S&H taxes
			if (is_array($totalPrices['shtaxes'])) {
				$totTax = array_combine(array_keys($totalinfo['shtaxes']), arrayPluck($totalPrices['shtaxes'], 'amount'));
				// calculate the totals
				foreach ($totTax as $name => $tax) {
					$allTaxes[$name] += floatval($tax);
				}
				$allTaxes['shtax_total'] = array_sum($totTax);
			}
			
			// save the totals
			$adb->pquery("DELETE FROM {$table_prefix}_inventorytotals WHERE id = ?", array($id));
			$columns = array_keys($allTaxes);
			$adb->pquery("INSERT INTO {$table_prefix}_inventorytotals (id, ".implode(',', $columns).") VALUES (?, ".generateQuestionMarks($allTaxes).")", array($id, $allTaxes));
		}
	}
}
