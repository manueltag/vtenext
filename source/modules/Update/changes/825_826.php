<?php

global $adb, $table_prefix;

// ACCOUNTS		///////////////////////////////////////////////////////////////
$array_fields = array('fax','rating','industry','account_type','crmv_vat_registration_number','crmv_social_security_number','emailoptout','bill_street','ship_street','bill_city','ship_city','bill_state','ship_state','bill_code','ship_code','bill_country','ship_country','bill_pobox','ship_pobox' );

foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 6 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 6 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}

// LEADS	///////////////////////////////////////////////////////////////
$array_fields = array('mobile','fax','designation','leadsource','website','industry','rating','lane','code','city','country','state','pobox');

foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 7 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 7 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}

// CONTACTS		///////////////////////////////////////////////////////////////

$array_fields = array('mobile','homephone','leadsource','otherphone','birthday','emailoptout','mailingstreet','otherstreet','othercity','mailingzip','mailingcountry','mailingpobox','otherpobox','description');

foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 4 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 4 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}

//POTENTIALS	///////////////////////////////////////////////////////////////

$array_fields = array('potential_no','opportunity_type','leadsource','probability','description');
foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 2 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 2 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}

//PRODUCTS	///////////////////////////////////////////////////////////////
$array_fields = array('product_no','productcode','productcategory','serial_no','imagename','taxclass','commissionrate','description');
foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 14 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 14 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}


//HELPDESK	///////////////////////////////////////////////////////////////
$array_fields = array('ticket_no','description','ticketcategories');
foreach($array_fields as $field){
	$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = 13 and fieldname = '$field' ");
}

//UPDATE QUICKCREATESEQUENCE
$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
			FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
			WHERE f.tabid = 13 AND quickcreate IN (0,2)
			ORDER BY b.sequence,block, f.sequence ";
$ress = $adb->query($query);
$sequence = 1;
while($row = $adb->fetchByAssoc($ress)){
	$params = array($sequence,$row['tabid'],$row['fieldid']);
	$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
	$sequence++;
}

// SERVICES	///////////////////////////////////////////////////////////////
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'Services' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	$array_fields = array('service_no','commissionrate','taxclass','qty_per_unit','servicecategory','description');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}


// SERVICECONTRACTS	///////////////////////////////////////////////////////////////
$array_fields = array('planned_duration','actual_duration','contract_no','contract_type','contract_priority','progress','residual_units');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'ServiceContracts' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}

// ASSETS	///////////////////////////////////////////////////////////////
$array_fields = array('asset_no','description');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'Assets' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}

// VISITREPORT	///////////////////////////////////////////////////////////////
$array_fields = array('scopovisit','visitnote','visitdate','description');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'Visitreport' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}


// PROJECTPLAN	///////////////////////////////////////////////////////////////
$array_fields = array('projectpriority','projecttype','project_no','progress','description');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'ProjectPlan' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}

     
// PROJECTTASK	///////////////////////////////////////////////////////////////
$array_fields = array('projecttask_no','projecttaskpriority','projecttasktype','projecttaskhours','projecttaskprogress','description');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'ProjectTask' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}

// PROJECTMILESTONE	///////////////////////////////////////////////////////////////
$array_fields = array('projectmilestone_no','projectmilestype','description');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'ProjectMilestone' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}

//TIMECARDS
$array_fields = array('sortorder','newresp','timecardtype','description','worktime');
$tabid = $adb->query("SELECT tabid FROM {$table_prefix}_tab WHERE NAME = 'Timecards' ");
if ($adb->num_rows($tabid) > 0){
	$tabid = $adb->query_result($tabid, 0, 'tabid');
	foreach($array_fields as $field){
		$adb->query("UPDATE {$table_prefix}_field SET quickcreate = 2 WHERE tabid = '$tabid' and fieldname = '$field' ");
	}   

	//UPDATE QUICKCREATESEQUENCE
	$query = "SELECT f.tabid, fieldid, fieldname, quickcreatesequence 
				FROM {$table_prefix}_field f INNER JOIN {$table_prefix}_blocks b ON f.block = b.blockid
				WHERE f.tabid = '$tabid' AND quickcreate IN (0,2)
				ORDER BY b.sequence,block, f.sequence ";
	$ress = $adb->query($query);
	$sequence = 1;
	while($row = $adb->fetchByAssoc($ress)){
		$params = array($sequence,$row['tabid'],$row['fieldid']);
		$adb->pquery("UPDATE {$table_prefix}_field SET quickcreatesequence = ? WHERE tabid = ? AND fieldid = ? ", $params);
		$sequence++;
	}
}


?>