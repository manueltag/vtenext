<?php
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['ChangeLog'] = 'packages/vte/mandatory/ChangeLog.zip';

SDK::setLanguageEntries('Calendar', 'Will expire', array('it_it'=>'Scade','en_us'=>'Will expire','pt_br'=>'Vencerei'));
SDK::setLanguageEntries('Calendar', 'Expired', array('it_it'=>'E\' scaduto','en_us'=>'Expired','pt_br'=>'Venceu'));
SDK::setLanguageEntry('Settings', 'it_it', 'LBL_SUBJECT', 'Oggetto');
SDK::setLanguageEntry('Emails', 'en_us', 'LBL_SUBJECT', 'Subject');

global $adb, $current_user;
$result = $adb->pquery('SELECT uitype FROM vtiger_ws_fieldtype WHERE fieldtype IN (?,?)',array('picklist','multipicklist'));
$uitypes = array();
if ($result && $adb->num_rows($result) > 0) {
	while($row=$adb->fetchByAssoc($result)) {
		$uitypes[] = $row['uitype'];
	}
	$fl = array('tablename',"':'",'columnname',"':'",'fieldname',"':'");
	$result = $adb->pquery('SELECT '.$adb->sql_concat($fl).' as field, tabid FROM vtiger_field WHERE uitype IN ('.generateQuestionMarks($uitypes).')',$uitypes);
	if ($result && $adb->num_rows($result) > 0) {
		$values = array();
		while($row=$adb->fetchByAssoc($result)) {
			$field = $row['field'];
			$moduleInstance = Vtiger_Module::getInstance($row['tabid']);
			$res = $adb->pquery("SELECT * FROM vtiger_cvadvfilter WHERE comparator IN (?,?) AND columnname like '".formatForSqlLike($field,2)."'",array('e','n'));
			if ($res && $adb->num_rows($res) > 0) {
				$comparator_mapping = array('e'=>'is','n'=>'isn');
				while($rr=$adb->fetchByAssoc($res)) {
					if ($rr['value'] != '') {
						$qgen_obj = QueryGenerator::getInstance($moduleInstance->name,$current_user);
						$moduleFieldList = $qgen_obj->getModuleFields();
						$fieldname = explode(":",$rr['columnname']);
						$fieldname = $moduleFieldList[$fieldname[2]];
						$value_trans = $qgen_obj->getReverseTranslate($rr['value'],$comparator_mapping[$rr['value']],$fieldname);
						if ($value_trans != $rr['value']) {
							$values[$rr['columnname']][$rr['value']] = $value_trans;
						}
					}
				}
			}
		}
		//echo '<pre>';print_r($values);echo '</pre>';
		if (!empty($values)) {
			foreach ($values as $columnname => $fieldvalues) {
				foreach ($fieldvalues as $fieldvalue => $new_fieldvalue) {
					$adb->pquery('update vtiger_cvadvfilter set value = ? where columnname = ? and value = ?',array($new_fieldvalue,$columnname,$fieldvalue));
				}
			}
		}
	}
}
?>