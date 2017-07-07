<?php
global $adb, $table_prefix;
$messagesModule = Vtiger_Module::getInstance('Messages');
$mods = array('Assets', 'ProjectMilestone', 'Timecards', 'Invoice', 'Ddt', 'PriceBooks', 'Campaigns', 'ServiceContracts', 'Targets', 'Services', 'Products');
foreach($mods as $lmod) {
	$modInst = Vtiger_Module::getInstance($lmod);
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_relatedlists WHERE tabid = ? AND related_tabid = ?", array($modInst->id, $messagesModule->id));
	if ($result && $adb->num_rows($result) > 0) { /* skip */ } else
	$modInst->setRelatedList($messagesModule, 'Messages', Array('ADD'), 'get_messages_list');
}
?>