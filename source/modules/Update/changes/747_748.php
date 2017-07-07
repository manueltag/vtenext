<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['Timecards'] = 'packages/vte/mandatory/Timecards.zip';

$res = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE name = 'Projects'");
if ($res && $adb->num_rows($res)>0) $_SESSION['modules_to_update']['Projects'] = 'packages/vte/optional/Projects.zip';

SDK::setLanguageEntries('Messages', 'LBL_LIST_DESCR_PREVIEW', array('it_it'=>'Mostra anteprima descrizione in lista messaggi','en_us'=>'Show preview description in message list'));

//UITYPE 51
$sql51 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 51";
$res51 = $adb->query($sql51);
if ($res51 && $adb->num_rows($res51) > 0)  {
	while ($row51 = $adb->FetchByAssoc($res51, -1, false)) {
		$src51 = 'modules/SDK/src/ReturnFunct/ReturnAccountAddress.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row51['fieldid']));
		SDK::setPopupReturnFunction($row51['modulename'], $row51['fieldname'], $src51);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Accounts',NULL, NULL)",array($row51[fieldid],$row51[modulename]));
	}
}

//UITYPE 57
$sql57 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 57";
$res57 = $adb->query($sql57);
if ($res57 && $adb->num_rows($res57) > 0)  {
	while ($row57 = $adb->FetchByAssoc($res57, -1, false)) {
		$src57 = 'modules/SDK/src/ReturnFunct/ReturnContactAddress.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row57['fieldid']));
		SDK::setPopupReturnFunction($row57['modulename'], $row57['fieldname'], $src57);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Contacts',NULL, NULL)",array($row57[fieldid],$row57[modulename]));
	}
}

//UITYPE 58
$sql58 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 58";
$res58 = $adb->query($sql58);
if ($res58 && $adb->num_rows($res58) > 0)  {
	while ($row58 = $adb->FetchByAssoc($res58, -1, false)) { 
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row58['fieldid']));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Campaigns',NULL, NULL)",array($row58[fieldid],$row58[modulename]));
	}
}

//UITYPE 59
$sql59 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 59";
$res59 = $adb->query($sql59);
if ($res59 && $adb->num_rows($res59) > 0)  {
	while ($row59 = $adb->FetchByAssoc($res59, -1, false)) {
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row59['fieldid']));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Products',NULL, NULL)",array($row59[fieldid],$row59[modulename]));
	}
}

//UITYPE 68
$sql68 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 68";
$res68 = $adb->query($sql68);
if ($res68 && $adb->num_rows($res68) > 0)  {
	while ($row68 = $adb->FetchByAssoc($res68, -1, false)) {
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row68['fieldid']));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Contacts',NULL, NULL)",array($row68[fieldid],$row68[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Accounts',NULL, NULL)",array($row68[fieldid],$row68[modulename]));
	}
}

//UITYPE 73
$sql73 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 73";
$res73 = $adb->query($sql73);
if ($res73 && $adb->num_rows($res73) > 0)  {
	while ($row73 = $adb->FetchByAssoc($res73, -1, false)) {
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row73['fieldid']));
		if($row73['modulename'] != 'Projects'){
			$src73 = 'modules/SDK/src/ReturnFunct/ReturnAccountAddress.php';
			SDK::setPopupReturnFunction($row73['modulename'], $row73['fieldname'], $src73);
		}
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Accounts',NULL, NULL)",array($row73[fieldid],$row73[modulename]));
	}
}

//UITYPE 75
$sql75 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 75";
$res75 = $adb->query($sql75);
if ($res75 && $adb->num_rows($res75) > 0)  {
	while ($row75 = $adb->FetchByAssoc($res75, -1, false)) {
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row75['fieldid']));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Vendors',NULL, NULL)",array($row75[fieldid],$row75[modulename]));
	}
}

//UITYPE 76
$sql76 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 76";
$res76 = $adb->query($sql76);
if ($res76 && $adb->num_rows($res76) > 0)  {
	while ($row76 = $adb->FetchByAssoc($res76, -1, false)) {
		$src76 = 'modules/SDK/src/ReturnFunct/ReturnPotentialAddress.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row76['fieldid']));
		SDK::setPopupReturnFunction($row76['modulename'], $row76['fieldname'], $src76);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Potentials',NULL, NULL)",array($row76[fieldid],$row76[modulename]));
	}
}

//UITYPE 78
$sql78 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 78";
$res78 = $adb->query($sql78);
if ($res78 && $adb->num_rows($res78) > 0)  {
	while ($row78 = $adb->FetchByAssoc($res78, -1, false)) {
		$src78 = 'modules/SDK/src/ReturnFunct/ReturnProductLines.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row78['fieldid']));
		SDK::setPopupReturnFunction($row78['modulename'], $row78['fieldname'], $src78);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Quotes',NULL, NULL)",array($row78[fieldid],$row78[modulename]));
	}
}

//UITYPE 80
$sql80 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 80";
$res80 = $adb->query($sql80);
if ($res80 && $adb->num_rows($res80) > 0)  {
	while ($row80 = $adb->FetchByAssoc($res80, -1, false)) {
		$src80 = 'modules/SDK/src/ReturnFunct/ReturnProductLines.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row80['fieldid']));
		SDK::setPopupReturnFunction($row80['modulename'], $row80['fieldname'], $src80);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'SalesOrder',NULL, NULL)",array($row80[fieldid],$row80[modulename]));
	}
}

//UITYPE 81
$sql81 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 81";
$res81 = $adb->query($sql81);
if ($res81 && $adb->num_rows($res81) > 0)  {
	while ($row81 = $adb->FetchByAssoc($res81, -1, false)) {
		$src81 = 'modules/SDK/src/ReturnFunct/ReturnVendorAddress.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row81['fieldid']));
		SDK::setPopupReturnFunction($row81['modulename'], $row81['fieldname'], $src81);
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Vendors',NULL, NULL)",array($row81[fieldid],$row81[modulename]));
	}
}

//UITYPE 101
$sql101 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 101";
$res101 = $adb->query($sql101);
if ($res101 && $adb->num_rows($res101) > 0)  {
	while ($row101 = $adb->FetchByAssoc($res101, -1, false)) {
		$src101 = 'modules/SDK/src/ReturnFunct/ReturnUserLastname.php';
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row101['fieldid']));
		SDK::setPopupReturnFunction($row101['modulename'], $row101['fieldname'], $src101);
		$mod_pu = $row101[modulename];
		$field_pu = $row101[fieldname];
		SDK::setPopupQuery('field', $mod_pu, $field_pu, 'modules/SDK/src/PopupQuery/ExcludeCurrentUser.php');
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Users',NULL, NULL)",array($row101[fieldid],$row101[modulename]));
	}
}

//UITYPE 66
$sql66 = "SELECT ".$table_prefix."_field.fieldid,
				 ".$table_prefix."_field.fieldname,
				 ".$table_prefix."_tab.name AS modulename,
				 ".$table_prefix."_field.uitype FROM ".$table_prefix."_ws_fieldtype
			INNER JOIN ".$table_prefix."_field ON ".$table_prefix."_field.uitype = ".$table_prefix."_ws_fieldtype.uitype
			INNER JOIN ".$table_prefix."_tab ON ".$table_prefix."_tab.tabid = ".$table_prefix."_field.tabid
			WHERE ".$table_prefix."_ws_fieldtype.fieldtype = 'reference' AND ".$table_prefix."_ws_fieldtype.uitype = 66";
$res66 = $adb->query($sql66);
if ($res66 && $adb->num_rows($res66) > 0)  {
	while ($row66 = $adb->FetchByAssoc($res66, -1, false)) {
		$adb->pquery("UPDATE ".$table_prefix."_field SET uitype=10 WHERE fieldid= ?",array($row66['fieldid']));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Accounts',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Campaigns',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'HelpDesk',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Invoice',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Leads',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Potentials',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'ProjectMilestone',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'ProjectPlan',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'ProjectTask',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'PurchaseOrder',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Quotes',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'SalesOrder',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
		$adb->pquery("INSERT ".$table_prefix."_fieldmodulerel (fieldid,module,relmodule,status,sequence) VALUES (?,?,'Visitreport',NULL, NULL)",array($row66[fieldid],$row66[modulename]));
	}
}

$uitypes = array(50,51,57,58,73,75,76,78,80,81,101,59,66,68);
$adb->pquery("delete from {$table_prefix}_ws_referencetype where fieldtypeid in (select fieldtypeid from {$table_prefix}_ws_fieldtype where fieldtype = ? and uitype in (".generateQuestionMarks($uitypes)."))",array('reference',$uitypes));
$adb->pquery("delete from {$table_prefix}_ws_fieldtype where fieldtype = ? and uitype in (".generateQuestionMarks($uitypes).")",array('reference',$uitypes));
?>