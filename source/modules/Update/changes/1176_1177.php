<?php

global $adb, $table_prefix;

// crmv@93095 - fix Messages ListView

$res = $adb->pquery("select cvid from {$table_prefix}_customview where entitytype = ? and viewname = ?", array('Messages', 'All'));
if ($res && $adb->num_rows($res) > 0) {
	$cvid = intval($adb->query_result_no_html($res, 0, 'cvid'));
	if ($cvid > 0) {
		$newCv = "{$table_prefix}_messages:cleaned_body:cleaned_body:Messages_Body:V";
		$oldCvStart = "{$table_prefix}_crmentity:description:";
		$adb->pquery("update {$table_prefix}_cvcolumnlist SET columnname = ? WHERE cvid = ? AND columnname like '{$oldCvStart}%'", array($newCv, $cvid));
	}
}