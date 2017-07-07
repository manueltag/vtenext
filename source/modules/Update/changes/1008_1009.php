<?php
global $table_prefix,$adb;
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

//crmv@61519 - to enable click2call in Vendors
require_once('vtlib/Vtecrm/Module.php');
$vendors_instance = Vtecrm_Module::getInstance('Vendors');
$adb->pquery("UPDATE {$table_prefix}_field SET uitype=? WHERE fieldname=? AND tabid=18 AND uitype=1",array(11,'phone',$vendors_instance->id));
//crmv@61519e

//crmv@61641
$documents_instance = Vtecrm_Module::getInstance('Documents');
$vendors_instance = Vtecrm_Module::getInstance('Vendors');

$sql = "SELECT relation_id
		FROM
		{$table_prefix}_relatedlists 
		WHERE (tabid = ? 
			AND related_tabid = ?) 
		  OR (tabid = ? 
			AND related_tabid = ?)";
$res = $adb->pquery($sql,array($documents_instance->id,$vendors_instance->id,$vendors_instance->id,$documents_instance->id));
if($res && $adb->num_rows($res) == 0){
		$documents_instance->setRelatedList($vendors_instance, 'Vendors', Array('SELECT','ADD'), 'get_documents_dependents_list');
		$vendors_instance->setRelatedList($documents_instance, 'Documents', Array('SELECT','ADD'), 'get_related_list');
}
//crmv@61641e
?>