<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

// crmv@81126
// aggiungo campo per id ricorrente
$fields = array(
	'recurr_idx_task'	=> array('module'=>'Calendar', 'block'=>'LBL_TASK_INFORMATION',	'name'=>'recurr_idx',	'label'=>'RecurrentIdx',		'table'=>"{$table_prefix}_activity", 	'column'=>'recurr_idx',	'columntype'=>'I(11) DEFAULT 0',	'typeofdata'=>'I~O', 	'uitype'=>7, 'readonly'=>99, 'masseditable'=>0, 'quickcreate'=>1),
	'recurr_idx_event'	=> array('module'=>'Events', 'block'=>'LBL_EVENT_INFORMATION',	'name'=>'recurr_idx',	'label'=>'RecurrentIdx',		'table'=>"{$table_prefix}_activity", 	'column'=>'recurr_idx',	'columntype'=>'I(11) DEFAULT 0',	'typeofdata'=>'I~O', 	'uitype'=>7, 'readonly'=>99, 'masseditable'=>0, 'quickcreate'=>1),
);
$fieldRet = Update::create_fields($fields);

// add column in ical table
$adb->addColumnToTable($table_prefix.'_messages_ical', 'recurring_idx', 'I(11) DEFAULT 0');
// crmv@81126e