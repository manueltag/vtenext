<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

if ($_REQUEST['file'] == 'Accounts') {
	global $currentModule, $app_strings, $mod_strings;
	$focus = CRMEntity::getInstance($currentModule);
	require_once('Smarty_setup.php');
	$smarty = new vtigerCRM_Smarty();
	$smarty->assign('MOD', $mod_strings);
	$smarty->assign('APP', $app_strings);
	$smarty->assign('FOCUS', $focus);
}

$accounts = $focus->getUserAccounts();
if (!empty($accounts)) {
	$account_description = array();
	foreach($accounts as $account) {
		$account_description[$account['id']] = $account['description'];
	}
	$folders = $focus->getAllSpecialFolders('INBOX');
	if (!empty($folders)) {
		$query = "SELECT account, count(*) AS count FROM {$focus->table_name}
				INNER JOIN {$focus->entity_table} ON {$focus->entity_table}.crmid = {$focus->table_name}.messagesid
				WHERE deleted = 0 AND smownerid = ? AND {$focus->table_name}.seen = ? and {$focus->table_name}.mtype = ?";
		$params = array($current_user->id,0,'Webmail');
		$tmp = array();
		foreach($folders as $account => $folder) {
			$tmp[] = "({$focus->table_name}.account = ? AND folder = ?)";
			$params[] = array($account,$folder['INBOX']);
		}
		$query .= ' AND ('.implode('OR',$tmp).')';
		$query .= " GROUP BY account";
		$folder_counts = array('all'=>0);
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$folder_counts[$row['account']] = $row['count'];
				$folder_counts['all'] = ($folder_counts['all']+$row['count']);
			}
		}
		
		$inbox = array();
		$inbox[] = array(
			'account'=>'all',
			'id'=>'INBOX',
			'description'=>getTranslatedString('LBL_Folder_INBOX','Messages'),
			'img'=>'modules/Messages/src/img/folder_inbox.png',
			'count'=>$folder_counts['all'],
		);
		foreach($folders as $accoount => $folder) {
			$inbox[] = array(
				'account'=>$accoount,
				'id'=>$folder['INBOX'],
				'description'=>$account_description[$accoount],
				'img'=>'modules/Messages/src/img/folder_inbox.png',
				'count'=>$folder_counts[$accoount],
			);
		}
		
		$smarty->assign('ACCOUNTS_INBOX', $inbox);
		$smarty->assign('ACCOUNTS', $accounts);
	}
}
$smarty->assign('DIV_DIMENSION', array('Folders'=>'0%','ListViewContents'=>'24%','DetailViewContents'=>'61%','TurboliftContents'=>'15%'));
$smarty->assign('VIEW', 'list');

if ($_REQUEST['file'] == 'Accounts') {
	$smarty->display("modules/Messages/Accounts.tpl");
}
?>