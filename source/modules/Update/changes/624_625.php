<?php
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

SDK::setLanguageEntries('ALERT_ARR','LBL_SAVING_DRAFT',array(
'it_it'=>'Salvataggio bozza in corso',
'en_us'=>'Saving draft',
'pt_br'=>'Salvando rascunho em curso',
'de_de'=>'Entwurf gespeichert in Arbeit',
));

global $adb;
$indexes[] = Array('table'=>'vte_mailcache_folders','idxname'=>'NewIndex1','idxflds'=>'userid');
$indexes[] = Array('table'=>'vte_mailcache_list','idxname'=>'NewIndex1','idxflds'=>'userid');
$indexes[] = Array('table'=>'vte_mailcache_list','idxname'=>'NewIndex2','idxflds'=>'uid');
$indexes[] = Array('table'=>'vte_mailcache_list','idxname'=>'NewIndex3','idxflds'=>'folder');
$indexes[] = Array('table'=>'vte_mailcache_messages','idxname'=>'NewIndex1','idxflds'=>'userid');
$indexes[] = Array('table'=>'vte_mailcache_messages','idxname'=>'NewIndex2','idxflds'=>'uid');
$indexes[] = Array('table'=>'vte_mailcache_messages','idxname'=>'NewIndex3','idxflds'=>'folder');
foreach ($indexes as $arr){
	$fields = explode(",",$arr['idxflds']);
	$idx_table = $adb->database->MetaIndexes($arr['table']);
	$short_name = $arr['table'];
	if (strpos($short_name,"_")!==false){
		$exp = explode("_",$short_name);
		unset($exp[0]);
		$short_name = implode("_",$exp);
	}
	$short_name=strtolower(substr($short_name,0,20));
	$idx_prefix = "idx_".$short_name."_";
	$progress = Array(0);
	if (is_array($idx_table)){
		$found = false;
		foreach ($idx_table as $name=>$arr2){
			$fields_compare = $arr2['columns'];
			$result_compare = array_udiff($fields, $fields_compare, 'strcasecmp');
			if (empty($result_compare)){
				$found = true;
			}
			$name = strtolower($name);
			$pos_start = strpos($name,$idx_prefix);
			if ($pos_start!==false){
				$pos_start+=strlen($idx_prefix);
				$progress[] = intval(substr($name,$pos_start,strlen($name)));
			}
		}
	}
	if (!$found){
		$index_name = $idx_prefix.str_pad((String)(max($progress)+1), 2, "0",STR_PAD_LEFT);
    	$adb->datadict->ExecuteSQLArray((Array)$adb->datadict->CreateIndexSQL($index_name, $arr['table'], $arr['idxflds']));
	}
}
?>