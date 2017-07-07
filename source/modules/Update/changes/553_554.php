<?php
global $adb;


SDK::clearSessionValues();

// funzioni utili
function createFields($list) {
	global $adb;

	if (!is_array($list)) return;

	$ret = array();
	foreach ($list as $k=>$arr) {

		$Vtiger_Utils_Log = true;

		$modulo = Vtiger_Module::getInstance($arr['module']);

		if (is_null($modulo)) {
			die('ERROR: Module is empty');
		}

		if (empty($arr['blockid'])) {
			$block = Vtiger_Block::getInstance($arr['block'], $modulo);
			if (is_null($block)) {
				die('ERROR: Block is empty');
			}
		} else {
			$block = Vtiger_Block::getInstance($arr['blockid']);
		}


		$field = @Vtiger_Field::getInstance($arr['name'], $modulo);

		if ($field != NULL) {
			$ret[$k] = $field;
			continue;
		} else {
			$field = new Vtiger_Field();
			$ret[$k] = $field;
		}

		// default values
		$field->name = $arr['name'];
		$field->column = $arr['name'];
		$field->label= $arr['label'];
		$field->columntype = 'C(255)';
		$field->typeofdata = 'V~O';
		$field->uitype = 1;
		$field->readonly = 1;
		$field->displaytype = 1;
		$field->masseditable = 0;
		$field->quickcreate = 1;
		$field->table = $modulo->basetable;

		if (isset($arr['table']) && !empty($arr['table']))
			$field->table = $arr['table'];

		if (isset($arr['column']) && !empty($arr['column']))
			$field->column = $arr['column'];

		if (isset($arr['readonly']) && !empty($arr['readonly']))
			$field->readonly = $arr['readonly'];

		if (isset($arr['presence']) && !empty($arr['presence']))
			$field->presence = $arr['presence'];

		if (isset($arr['columntype']) && !empty($arr['columntype']))
			$field->columntype = $arr['columntype'];

		if (isset($arr['typeofdata']) && !empty($arr['typeofdata']))
			$field->typeofdata = $arr['typeofdata'];

		if (isset($arr['uitype']) && !empty($arr['uitype']))
			$field->uitype = $arr['uitype'];

		if (isset($arr['displaytype']) && !empty($arr['displaytype']))
			$field->displaytype = $arr['displaytype'];

		if (isset($arr['quickcreate']) && !empty($arr['quickcreate']))
			$field->quickcreate = $arr['quickcreate'];

		if (isset($arr['masseditable']) && !empty($arr['masseditable']))
			$field->masseditable = $arr['masseditable'];

		//se picklist aggiungo i valori
		if (isset($arr['picklist']) && !empty($arr['picklist'])){
			$field->setPicklistValues($arr['picklist']);
		}

		$block->addField($field);

		// related modules
		if (isset($arr['relatedModules']) && !empty($arr['relatedModules'])){
			$field->setRelatedModules($arr['relatedModules']);
			if (!empty($arr['relatedModulesAction'])) {
				foreach ($arr['relatedModules'] as $relmod) {
					$relinst = Vtiger_Module::getInstance($relmod);
					$relinst->setRelatedList($modulo, $arr['module'], $arr['relatedModulesAction'][$relmod], 'get_dependents_list');
				}
			}
		}

		// sdk:uitype, we need to change the uitype by hand
		if (isset($arr['sdk_uitype']) && !empty($arr['sdk_uitype'])) {
			$newtype = intval($arr['sdk_uitype']);
			$adb->pquery("update vtiger_field set uitype = ? where columnname = ? and tabid = ?", array($newtype, $arr['name'], $modulo->id));
		}

	}
	return $ret;
}

// TODO: prenderle invece dal php, ma ce ne sono troppe, quindi uso queste qui
$zonesarray = array(
		"Pacific/Midway" => "(GMT-11:00) Midway Island, Samoa",
		"America/Adak" => "(GMT-10:00) Hawaii-Aleutian",
		"Etc/GMT+10" => "(GMT-10:00) Hawaii",
		"Pacific/Marquesas" => "(GMT-09:30) Marquesas Islands",
		"Pacific/Gambier" => "(GMT-09:00) Gambier Islands",
		"America/Anchorage" => "(GMT-09:00) Alaska",
		"America/Ensenada" => "(GMT-08:00) Tijuana, Baja California",
		"Etc/GMT+8" => "(GMT-08:00) Pitcairn Islands",
		"America/Los_Angeles" => "(GMT-08:00) Pacific Time (US & Canada)",
		"America/Denver" => "(GMT-07:00) Mountain Time (US & Canada)",
		"America/Chihuahua" => "(GMT-07:00) Chihuahua, La Paz, Mazatlan",
		"America/Dawson_Creek" => "(GMT-07:00) Arizona",
		"America/Belize" => "(GMT-06:00) Saskatchewan, Central America",
		"America/Cancun" => "(GMT-06:00) Guadalajara, Mexico City, Monterrey",
		"Chile/EasterIsland" => "(GMT-06:00) Easter Island",
		"America/Chicago" => "(GMT-06:00) Central Time (US & Canada)",
		"America/New_York" => "(GMT-05:00) Eastern Time (US & Canada)",
		"America/Havana" => "(GMT-05:00) Cuba",
		"America/Bogota" => "(GMT-05:00) Bogota, Lima, Quito, Rio Branco",
		"America/Caracas" => "(GMT-04:30) Caracas",
		"America/Santiago" => "(GMT-04:00) Santiago",
		"America/La_Paz" => "(GMT-04:00) La Paz",
		"Atlantic/Stanley" => "(GMT-04:00) Faukland Islands",
		"America/Campo_Grande" => "(GMT-04:00) Brazil",
		"America/Goose_Bay" => "(GMT-04:00) Atlantic Time (Goose Bay)",
		"America/Glace_Bay" => "(GMT-04:00) Atlantic Time (Canada)",
		"America/St_Johns" => "(GMT-03:30) Newfoundland",
		"America/Araguaina" => "(GMT-03:00) UTC-3",
		"America/Montevideo" => "(GMT-03:00) Montevideo",
		"America/Miquelon" => "(GMT-03:00) Miquelon, St. Pierre",
		"America/Godthab" => "(GMT-03:00) Greenland",
		"America/Argentina/Buenos_Aires" => "(GMT-03:00) Buenos Aires",
		"America/Sao_Paulo" => "(GMT-03:00) Brasilia",
		"America/Noronha" => "(GMT-02:00) Mid-Atlantic",
		"Atlantic/Cape_Verde" => "(GMT-01:00) Cape Verde Is.",
		"Atlantic/Azores" => "(GMT-01:00) Azores",
		"Europe/Belfast" => "(GMT) Greenwich Mean Time: Belfast",
		"Europe/Dublin" => "(GMT) Greenwich Mean Time: Dublin",
		"Europe/Lisbon" => "(GMT) Greenwich Mean Time: Lisbon",
		"Europe/London" => "(GMT) Greenwich Mean Time: London",
		"Africa/Abidjan" => "(GMT) Monrovia, Reykjavik",
		"Europe/Rome" => "(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
		"Europe/Belgrade" => "(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
		"Europe/Brussels" => "(GMT+01:00) Brussels, Copenhagen, Madrid, Paris",
		"Africa/Algiers" => "(GMT+01:00) West Central Africa",
		"Africa/Windhoek" => "(GMT+01:00) Windhoek",
		"Asia/Beirut" => "(GMT+02:00) Beirut",
		"Africa/Cairo" => "(GMT+02:00) Cairo",
		"Asia/Gaza" => "(GMT+02:00) Gaza",
		"Africa/Blantyre" => "(GMT+02:00) Harare, Pretoria",
		"Asia/Jerusalem" => "(GMT+02:00) Jerusalem",
		"Europe/Minsk" => "(GMT+02:00) Minsk",
		"Asia/Damascus" => "(GMT+02:00) Syria",
		"Europe/Moscow" => "(GMT+03:00) Moscow, St. Petersburg, Volgograd",
		"Africa/Addis_Ababa" => "(GMT+03:00) Nairobi",
		"Asia/Tehran" => "(GMT+03:30) Tehran",
		"Asia/Dubai" => "(GMT+04:00) Abu Dhabi, Muscat",
		"Asia/Yerevan" => "(GMT+04:00) Yerevan",
		"Asia/Kabul" => "(GMT+04:30) Kabul",
		"Asia/Yekaterinburg" => "(GMT+05:00) Ekaterinburg",
		"Asia/Tashkent" => "(GMT+05:00) Tashkent",
		"Asia/Kolkata" => "(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
		"Asia/Katmandu" => "(GMT+05:45) Kathmandu",
		"Asia/Dhaka" => "(GMT+06:00) Astana, Dhaka",
		"Asia/Novosibirsk" => "(GMT+06:00) Novosibirsk",
		"Asia/Rangoon" => "(GMT+06:30) Yangon (Rangoon)",
		"Asia/Bangkok" => "(GMT+07:00) Bangkok, Hanoi, Jakarta",
		"Asia/Krasnoyarsk" => "(GMT+07:00) Krasnoyarsk",
		"Asia/Hong_Kong" => "(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
		"Asia/Irkutsk" => "(GMT+08:00) Irkutsk, Ulaan Bataar",
		"Australia/Perth" => "(GMT+08:00) Perth",
		"Australia/Eucla" => "(GMT+08:45) Eucla",
		"Asia/Tokyo" => "(GMT+09:00) Osaka, Sapporo, Tokyo",
		"Asia/Seoul" => "(GMT+09:00) Seoul",
		"Asia/Yakutsk" => "(GMT+09:00) Yakutsk",
		"Australia/Adelaide" => "(GMT+09:30) Adelaide",
		"Australia/Darwin" => "(GMT+09:30) Darwin",
		"Australia/Brisbane" => "(GMT+10:00) Brisbane",
		"Australia/Hobart" => "(GMT+10:00) Hobart",
		"Asia/Vladivostok" => "(GMT+10:00) Vladivostok",
		"Australia/Lord_Howe" => "(GMT+10:30) Lord Howe Island",
		"Etc/GMT-11" => "(GMT+11:00) Solomon Is., New Caledonia",
		"Asia/Magadan" => "(GMT+11:00) Magadan",
		"Pacific/Norfolk" => "(GMT+11:30) Norfolk Island",
		"Asia/Anadyr" => "(GMT+12:00) Anadyr, Kamchatka",
		"Pacific/Auckland" => "(GMT+12:00) Auckland, Wellington",
		"Etc/GMT-12" => "(GMT+12:00) Fiji, Kamchatka, Marshall Is.",
		"Pacific/Chatham" => "(GMT+12:45) Chatham Islands",
		"Pacific/Tongatapu" => "(GMT+13:00) Nuku'alofa",
		"Pacific/Kiritimati" => "(GMT+14:00) Kiritimati",
);


$fields = array(
		'user_timezone'	=> array('module'=>'Users', 'block'=>'LBL_USERLOGIN_ROLE',	 'name'=>'user_timezone',	'label'=>'Timezone', 	'table'=>'vtiger_users', 	'columntype'=>'C(255)',	'typeofdata'=>'V~O',	'uitype'=>15,  'readonly'=>1, 'picklist' => array_keys($zonesarray) ),
);

createFields($fields);

// translate it
$res = $adb->query('select prefix from vtiger_language');
if ($res && $adb->num_rows($res) > 0) {
	while ($langrow = $adb->FetchByAssoc($res)) {
		foreach ($zonesarray as $label=>$trans) {
			SDK::setLanguageEntry('Users', $langrow['prefix'], $label, $trans);
		}
	}
}

// imposto il default per gli utenti che non hanno timezone assegnato usando quello del server
global $default_timezone;
if (empty($default_timezone) || !array_key_exists($default_timezone, $zonesarray)) $default_timezone = 'Europe/Rome';
$adb->pquery("update vtiger_users set user_timezone = ? where user_timezone is null or user_timezone = ''", array($default_timezone));


?>