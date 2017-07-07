<?php

// crmv@98866

SDK::setLanguageEntries('APP_STRINGS', 'LBL_FROM_HOUR', array('it_it' => 'dalle', 'en_us' => 'from'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_TO_HOUR', array('it_it' => 'alle', 'en_us' => 'to'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_OPEN', array('it_it' => 'Apri', 'en_us' => 'Open'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_DISMISS', array('it_it' => 'Ignora', 'en_us' => 'Dismiss'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_OVERDUE', array('it_it' => 'In ritardo di', 'en_us' => 'Overdue by'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_SNOOZE_ALL', array('it_it' => 'Posponi tutto', 'en_us' => 'Snooze all'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_DISMISS_ALL', array('it_it' => 'Tralascia tutto', 'en_us' => 'Dismiss all'));
SDK::setLanguageEntries('APP_STRINGS', 'LBL_APPOINTMENT_REMINDER', array('it_it' => 'Promemoria appuntamenti', 'en_us' => 'Appointment Reminder'));

$calcolor_material = array (
	'31c99e31c99e' => '2bbeb12bbeb1',
	'4cd5da4cd5da' => '58efff58efff',
	'6ce1826ce182' => '94e49794e497',
	'6e65e76e65e7' => '5f7dff5f7dff',
	'6f96e46f96e4' => '697bdc697bdc',
	'79acdf79acdf' => '8b9ae98b9ae9',
	'84c64284c642' => '95cbff95cbff',
	'8a9fba8a9fba' => 'a5bcc8a5bcc8',
	'8bf7a78bf7a7' => '78ffc878ffc8',
	'93930e93930e' => 'b5b429b5b429',
	'99cccc99cccc' => '93e9e193e9e1',
	'99ccff99ccff' => 'a5e8ffa5e8ff',
	'a3c91ea3c91e' => 'c9cf31c9cf31',
	'ace96face96f' => 'c8f494c8f494',
	'b2f0f7b2f0f7' => 'ccffffccffff',
	'b399e6b399e6' => 'cdb4fbcdb4fb',
	'c0c01dc0c01d' => 'dce83adce83a',
	'c0e3e3c0e3e3' => 'ccfffbccfffb',
	'c2d1e1c2d1e1' => 'e2e8ffe2e8ff',
	'cca2cccca2cc' => 'eca9f8eca9f8',
	'cccccccccccc' => 'f7eae6f7eae6',
	'd0a400d0a400' => 'ffb800ffb800',
	'd1c2f0d1c2f0' => 'f0e1fff0e1ff',
	'd3d36dd3d36d' => 'fdff86fdff86',
	'd56bfed56bfe' => 'ff49ffff49ff',
	'd6e1f0d6e1f0' => 'd9d9d9d9d9d9',
	'd9832fd9832f' => 'ff921aff921a',
	'dbc48ddbc48d' => 'ffea93ffea93',
	'e0e0e0e0e0e0' => 'e0e0e0e0e0e0',
	'e17272e17272' => 'ff4d3eff4d3e',
	'e1e123e1e123' => 'ebfd41ebfd41',
	'e29394e29394' => 'ff8484ff8484',
	'e4984de4984d' => 'ffae00ffae00',
	'e6bc13e6bc13' => 'ffdd08ffdd08',
	'e8e0ebe8e0eb' => 'e8e0ebe8e0eb',
	'ecec5cecec5c' => 'ffff65ffff65',
	'f0c2c2f0c2c2' => 'ffd7efffd7ef',
	'f0e8c4f0e8c4' => 'ffffe0ffffe0',
	'f8739ff8739f' => 'ff93c4ff93c4',
	'f8a3a3f8a3a3' => 'ffb1b1ffb1b1',
	'f8d4aff8d4af' => 'ffffccffffcc',
	'fa9efafa9efa' => 'ff93ffff93ff',
	'fab066fab066' => 'ffd258ffd258',
	'fc7777fc7777' => 'ff5f5cff5f5c',
	'fcdc64fcdc64' => 'fff45afff45a',
	'fceaa3fceaa3' => 'ffffb4ffffb4',
	'fcfc82fcfc82' => 'ffff94ffff94',
	'fe4b4bfe4b4b' => 'ff5e5eff5e5e',
	'ff9933ff9933' => 'ffc02bffc02b',
	'ffdedeffdede' => 'ffd7efffd7ef',
	'ffe4feffe4fe' => 'ffe4feffe4fe',
	'fff000fff000' => 'ffff00ffff00',
);

$result = $adb->query("SELECT id, cal_color FROM {$table_prefix}_users");
if (!!$result && $adb->num_rows($result)) {
	while ($row = $adb->fetchByAssoc($result, -1, false)) {
		$userid = $row['id'];
		$cal_color = $row['cal_color'];
		$material_color = $calcolor_material[$cal_color];
		if (!empty($material_color)) {
			$adb->pquery("UPDATE {$table_prefix}_users SET cal_color = ? WHERE id = ?", array($material_color, $userid));
		}
	}
}

foreach ($calcolor_material as $color => $material_color) {
	$adb->pquery("UPDATE tbl_s_cal_color SET color = ? WHERE color = ?", array($material_color, $color));
}

?>