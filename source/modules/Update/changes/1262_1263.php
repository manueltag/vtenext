<?php

// crmv@98866

$adb->pquery("UPDATE {$table_prefix}_field SET readonly = ? WHERE fieldname = ? and tabid = ?", array(1, 'menu_view', getTabid('Users')));

SDK::setLanguageEntries('Settings', 'LBL_USER_MANAGEMENT', array('it_it'=>'Permessi utente','en_us'=>'User permissions'));
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_STUDIO', 'Business Process Manager');
SDK::setLanguageEntry('Settings', 'en_us', 'VTLIB_LBL_MODULE_MANAGER', 'Module settings');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_MODULE_MAKER', 'Module creator');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_PICKLIST_EDITOR', 'Picklist editor');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_PICKLIST_EDITOR_MULTI', 'Internationalized picklist');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_EDIT_LINKED_PICKLIST', 'Linked picklist');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_EDIT_UITYPE208', 'Encrypted fields');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_MENU_TABS', 'Top menu settings');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_COLORED_LISTVIEW_EDITOR', 'Listview colors');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_LIST_WORKFLOWS', 'Basic workflows');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_ST_MANAGER', 'Status fields permissions');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_COND_MANAGER', 'Condition based fields');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_DATA_IMPORTER', 'External data import');
SDK::setLanguageEntries('Settings', 'LBL_PROCESS_MAKER', array('it_it'=>'Process manager','en_us'=>'Process manager'));
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_WIZARD_MAKER', 'Wizard creator');
SDK::setLanguageEntry('Settings', 'en_us', 'LBL_COMMUNICATION_TEMPLATES', 'Customer tools');

// New menu layout
$settings = array(
	'LBL_MODULE_MANAGER' => array(
		'LBL_WORKFLOW_LIST',
		'LBL_FIELDFORMULAS'
	),
	'LBL_USER_MANAGEMENT' => array(
		'LBL_USERS',
		'LBL_ROLES',
		'LBL_PROFILES',
		'USERGROUPLIST',
		'LBL_SHARING_ACCESS',
		'LBL_FIELDS_ACCESS',
		'LBL_ADV_RULE',
		'LBL_AUDIT_TRAIL',
		'LBL_LOGIN_HISTORY_DETAILS',
		'LoginProtectionPanel',
	),
	'LBL_STUDIO' => array(
		'LBL_PROCESS_MAKER',
		'LBL_LIST_WORKFLOWS',
		'LBL_WIZARD_MAKER',
		'LBL_MODULE_MAKER',
		'VTLIB_LBL_MODULE_MANAGER',
		'LBL_PICKLIST_EDITOR',
		'LBL_PICKLIST_EDITOR_MULTI',
		'LBL_EDIT_LINKED_PICKLIST',
		'LBL_ST_MANAGER',
		'LBL_COND_MANAGER',
		'LBL_EDIT_UITYPE208',
		'LBL_MENU_TABS',
		'LBL_COLORED_LISTVIEW_EDITOR',
		'LBL_MAIL_SCANNER',
		'LBL_DATA_IMPORTER',
	),
	'LBL_COMMUNICATION_TEMPLATES' => array(
		'EMAILTEMPLATES',
		'Webforms',
		'LBL_CUSTOMER_PORTAL',
	),
	'LBL_OTHER_SETTINGS' => array(
		'LBL_COMPANY_DETAILS',
		'LBL_MAIL_SERVER_SETTINGS',
		'LBL_FAX_SERVER_SETTINGS',
		'LBL_SMS_SERVER_SETTINGS',
		'LBL_SOFTPHONE_SERVER_SETTINGS',
		'LBL_LDAP_SERVER_SETTINGS',
		'LBL_ASSIGN_MODULE_OWNERS',
		'LBL_CURRENCY_SETTINGS',
		'LBL_TAX_SETTINGS',
		'LBL_SYSTEM_INFO',
		'LBL_PROXY_SETTINGS',
		'LBL_ANNOUNCEMENT',
		'LBL_DEFAULT_MODULE_VIEW',
		'INVENTORYTERMSANDCONDITIONS',
		'LBL_CUSTOMIZE_MODENT_NUMBER',
		'LBL_PRIVACY',
	)
);

$disable_fields = array('LBL_CUSTOM_FIELDS');

$settingsInfo = array();
$tmpData = array();

foreach ($settings as $blockname => $blockinfo) {
	$result = $adb->pquery("SELECT * FROM {$table_prefix}_settings_blocks WHERE label = ?", array($blockname));
	if (!!$result && $adb->num_rows($result)) {
		$block = $adb->fetchByAssoc($result, -1, false);
		$blockdata = &$settingsInfo[$blockname];
		$blockdata['blockid'] = $block['blockid'];
		$blockdata['label'] = $block['label'];
		$blockdata['fields'] = array();
		$tmpData['blocks'][$block['blockid']] = $blockname;

		$tmpData['seq_info'][$blockname] = 0;
		$fieldsequence = &$tmpData['seq_info'][$blockname];

		foreach ($blockinfo as $fieldname) {
			$result = $adb->pquery("SELECT * FROM {$table_prefix}_settings_field WHERE name = ?", array($fieldname));
			if (!!$result && $adb->num_rows($result)) {
				$field = $adb->fetchByAssoc($result, -1, false);

				$fielddata = &$blockdata['fields'][$fieldname];
				$fielddata['fieldid'] = $field['fieldid'];
				$fielddata['blockid'] = $block['blockid'];
				$fielddata['name'] = $field['name'];
				$fielddata['iconpath'] = $field['iconpath'];
				$fielddata['description'] = $field['description'];
				$fielddata['linkto'] = $field['linkto'];
				$fielddata['sequence'] = ++$fieldsequence;
				$fielddata['active'] = $field['active'];

				$tmpData['fields'][] = $fieldname;
			}
		}
	}
}

// Custom blocks
$blocks = $tmpData['blocks'];
$fields = $tmpData['fields'];
$result = $adb->pquery("SELECT * FROM {$table_prefix}_settings_field WHERE name NOT IN (" . generateQuestionMarks($fields) . ")", array($fields));
if (!!$result && $adb->num_rows($result)) {
	while ($row = $adb->fetchByAssoc($result, -1, false)) {
		$name = $row['name'];
		$blockid = $row['blockid'];
		if (array_key_exists($blockid, $blocks)) {
			$blockname = $blocks[$blockid];
		} else {
			$blockname = 'LBL_OTHER_SETTINGS';
		}
		$sequence = &$tmpData['seq_info'][$blockname];
		$settingsInfo[$blockname]['fields'][$name] = array(
			'fieldid' => $row['fieldid'],
			'blockid' => $blockid,
			'name' => $name,
			'iconpath' => $row['iconpath'],
			'description' => $row['description'],
			'linkto' => $row['linkto'],
			'sequence' => ++$sequence,
			'active' => $row['active'],
		);
	}
}

foreach ($settingsInfo as $blockname => $blockinfo) {
	$blockid = $blockinfo['blockid'];
	$fields = $blockinfo['fields'];
	foreach ($fields as $fieldname => $fieldinfo) {
		$params = array(
			'blockid' => $blockid,
			'name' => $fieldinfo['name'],
			'iconpath' => $fieldinfo['iconpath'],
			'description' => $fieldinfo['description'],
			'linkto' => $fieldinfo['linkto'],
			'sequence' => $fieldinfo['sequence'],
			'active' => in_array($fieldname, $disable_fields) ? '1' : $fieldinfo['active'],
		);

		$updcol = array();
		$colparam = array();
		foreach ($params as $col => $val) {
			$updcol[] = "$col = ?";
			$colparam[] = $val;
		}
		$colparam[] = $fieldinfo['fieldid'];

		$adb->pquery("UPDATE {$table_prefix}_settings_field SET " . implode(', ', $updcol) . "  WHERE fieldid = ?", $colparam);
	}
}

?>