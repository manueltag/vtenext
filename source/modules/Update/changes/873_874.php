<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';


/* FIX DISCOUNT FORMAT */

global $current_user;
$oldCurrentUser = $current_user;

$IUtils = InventoryUtils::getInstance();

$columns = array('discount_percent');
$modules = getInventoryModules();
foreach ($modules as $module) {
	$modInstance = CRMEntity::getInstance($module);

	$table = $modInstance->table_name;
	$index = $modInstance->table_index;

	$cond = array();
	foreach ($columns as $c) {
		$cond[] = "$c is not null AND $c != '' AND $c != '0'";
	}

	$res = $adb->query(
		"select $index as id, c.smcreatorid, ".implode(',', $columns)."
		from $table
		inner join {$table_prefix}_crmentity c on c.crmid = $table.$index
		inner join {$table_prefix}_users u on u.id = c.smcreatorid
		where ".implode(' AND ', $cond)."
		order by c.smcreatorid ASC"
		);

	if ($res) {
		$oldUserid = null;
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$userid = $row['smcreatorid'];
			if ($userid !== $oldUserid) {
				$current_user = CRMEntity::getInstance('Users');
				$current_user->retrieveCurrentUserInfoFromFile($userid);

				if (isset($current_user->column_fields['decimal_separator'])) $IUtils->decimalSeparator = $current_user->column_fields['decimal_separator'];
				if (isset($current_user->column_fields['thousands_separator'])) $IUtils->thousandsSeparator = $current_user->column_fields['thousands_separator'];
			}

			$values = array();
			foreach ($columns as $c) {
				$val = $row[$c];
				if (!empty($val)) {
					$val = $IUtils->parseMultiDiscount($val, 1, 0);
					// check array for values > 100, if so, skip: maybe there was something wrong
					if (max($val) <= 100) {
						$val = $IUtils->joinMultiDiscount($val, 0, 0);
						$values[$c] = $val;
					}
				}
			}
			if (count($values) > 0) {
				$updcol = array();
				$params = array();
				foreach ($values as $col=>$val) {
					$updcol[] = "$col = ?";
					$params[] = $val;
				}
				$q = "update $table set ".implode(',', $updcol)." where $index = ?";
				$params[] = $row['id'];
				$adb->pquery($q, $params);
			}


			$oldUserid = $userid;
		}
	}

	$oldUserid = null;

	// now fix also inventoryproductrel
	$res2 = $adb->pquery(
		"select lineitem_id as lineid, i.discount_percent, c.smcreatorid
		from {$table_prefix}_inventoryproductrel i
		inner join $table on $table.$index = i.id
		inner join {$table_prefix}_crmentity c on c.crmid = i.id
		inner join {$table_prefix}_users u on u.id = c.smcreatorid
		where i.relmodule = ? and i.discount_percent is not null and i.discount_percent != '' and i.discount_percent != '0'
		order by c.smcreatorid asc",
		array($module)
		);

	while ($row2 = $adb->FetchByAssoc($res2, -1, false)) {

		$userid = $row['smcreatorid'];
		if ($userid !== $oldUserid) {
			$current_user = CRMEntity::getInstance('Users');
			$current_user->retrieveCurrentUserInfoFromFile($userid);

			if (isset($current_user->column_fields['decimal_separator'])) $IUtils->decimalSeparator = $current_user->column_fields['decimal_separator'];
			if (isset($current_user->column_fields['thousands_separator'])) $IUtils->thousandsSeparator = $current_user->column_fields['thousands_separator'];
		}


		$values = array();
		$val = $row2['discount_percent'];
		if (!empty($val)) {
			$val = $IUtils->parseMultiDiscount($val, 1, 0);

			// check array for values > 100, if so, skip: maybe there was something wrong
			if (max($val) <= 100) {
				$val = $IUtils->joinMultiDiscount($val, 0, 0);
				$values['discount_percent'] = $val;
			}
		}

		if (count($values) > 0) {
			$updcol = array();
			$params = array();
			foreach ($values as $col=>$val) {
				$updcol[] = "$col = ?";
				$params[] = $val;
			}
			$q = "update {$table_prefix}_inventoryproductrel set ".implode(',', $updcol)." where lineitem_id = ?";
			$params[] = $row2['lineid'];
			$adb->pquery($q, $params);
		}

		$oldUserid = $userid;
	}
}

$current_user = $oldCurrentUser;


?>