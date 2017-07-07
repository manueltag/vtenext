<?php
global $adb, $table_prefix;

// crmv@82017 crmv@93043 - recalculate js language
global $recalculateJsLanguage;
$recalculateJsLanguage['it_it'] = 'it_it';
// crmv@82017e crmv@93043e

// crmv@78555
$focus = CRMEntity::getInstance('Messages');
if ($focus->fetchBodyInCron == 'yes') {
	if ($adb->isMssql()) {
		$adb->pquery("UPDATE {$table_prefix}_messages
			SET cleaned_body = NULL
			FROM {$table_prefix}_messages
			INNER JOIN {$table_prefix}_crmentity ON crmid = messagesid
			WHERE deleted = 0 AND cleaned_body LIKE ?",
			array('%title= href=%')
		);
	} else {
		$adb->pquery("UPDATE {$table_prefix}_messages
			INNER JOIN {$table_prefix}_crmentity ON crmid = messagesid
			SET cleaned_body = NULL
			WHERE deleted = 0 AND cleaned_body LIKE ?",
			array('%title= href=%')
		);
	}
}
// crmv@78555e