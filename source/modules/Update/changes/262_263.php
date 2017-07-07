<?php
global $adb;
$moduloCampaigns = Vtiger_Module::getInstance('Campaigns');
$moduloCalendar = Vtiger_Module::getInstance('Calendar');
$adb->query("insert into vtiger_relatedlists values(".$adb->getUniqueID('vtiger_relatedlists').",".$moduloCampaigns->id.",".$moduloCalendar->id.",'get_history',5,'Activity History',0,'add')");
$idxflds = 'recordid,status,date_start,time_start';
$sqlarray = $adb->datadict->CreateIndexSQL('popup_index', 'vtiger_act_reminder_popup', $idxflds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$fields = Array(
crmv_bankdetails=>Array(uitype=>1,label=>'Bank Details',typeofdata=>'V~O'),
crmv_vat_registration_number=>Array(uitype=>1,label=>'VAT Registration Number',typeofdata=>'PIVA~O'),
crmv_social_security_number=>Array(uitype=>1,label=>'Social Security number',typeofdata=>'CF~O'),
external_code=>Array(uitype=>1,label=>'External Code',typeofdata=>'V~O'),
);
foreach ($fields as $field=>$arr){
	$presence = $adb->query_result($adb->pquery("select count(fieldid) as presence from vtiger_field where columnname = ? and tabid = ?",Array($field,6)),0,'presence');
	$seq = $adb->query_result($adb->pquery("select max(sequence) as seq from vtiger_field where tabid = ? and block = ?",Array(6,9)),0,'seq');
	$seq++;
	if ($presence == 0){
		$params = Array(
			tabid=>6,
			fieldid=>$adb->getUniqueID("vtiger_field"),
			columnname=>$field,
			tablename=>'vtiger_account',
			generatedtype=>1,
			uitype=>$arr[uitype],
			fieldname=>$field,
			fieldlabel=>$arr[label],
			readonly=>1,
			presence=>2,
			selected=>0,
			maximumlength=>100,
			sequence=>$seq,
			block=>9,
			displaytype=>1,
			typeofdata=>$arr[typeofdata],
			quickcreate=>1,
			quickcreatesequence=>NULL,
			info_type=>'BAS',
			masseditable=>0,
			helpinfo=>'',
		);
		$adb->pquery("insert into vtiger_field (".implode(",",array_keys($params)).") values (".generateQuestionMarks($params).")",$params);
	}
}
//add module update
$result = $adb->query("SELECT MAX(tabid) AS max_seq FROM vtiger_tab");
$tabid = $adb->query_result($result, 0, 'max_seq');
$tabid++;
$result = $adb->query("SELECT MAX(tabsequence) AS max_tabseq FROM vtiger_tab");
$sequence = $adb->query_result($result, 0, 'max_tabseq');
$sequence++;
$params = Array(
tabid=>$tabid,
name=>'Update',
presence=>1,
tabsequence=>$sequence,
tablabel=>'Update',
modifiedby=>NULL,
modifiedtime=>NULL,
customized=>0,
ownedby=>1,
version=>'1.0',
isentitytype=>0
);
$adb->pquery("INSERT INTO vtiger_tab (".implode(",",array_keys($params)).") VALUES (".generateQuestionMarks($params).")",$params);

//crmv@18160
$_SESSION['modules_to_update']['Conditionals'] = 'packages/vte/mandatory/Conditionals.zip';
$_SESSION['modules_to_update']['Transitions'] = 'packages/vte/mandatory/Transitions.zip';
//crmv@18160 end
?>