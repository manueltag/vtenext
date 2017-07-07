<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
 
/* crmv@47905bis */

class SDKUtils {
	
	function db2FileLanguages($module) {
	 	$langinfo = vtlib_getToggleLanguageInfo();
		foreach($langinfo as $prefix => $info) {
			$lang = get_lang_strings($module,$prefix);
			$data = "<?php\n\$mod_strings = array(\n";
			foreach($lang as $key => $value){
				$data .= "\t'".addcslashes(html_entity_decode($key, ENT_QUOTES), "'")."'=>'".addcslashes(html_entity_decode($value, ENT_QUOTES), "'")."',\n";
			}
			$data .= ");\n?>";
			$fp = fopen("modules/$module/language/$prefix.lang.php","wb");
			fwrite($fp,$data);
		}
 	}

 	function file2DbLanguages($module) {
 		$langinfo = vtlib_getToggleLanguageInfo();
		foreach($langinfo as $prefix => $info) {
			SDK::file2DbLanguage($module,$prefix);
		}
 	}

 	function file2DbLanguage($module,$language) {
 		unset($mod_strings);
		@include("modules/$module/language/$language.lang.php");
		if (isset($mod_strings)){
			insert_language($module,$language,$mod_strings);
		}
 	}

	/**
 	 * Deletes all the strings for the specified module or language
	 */
 	function deleteLanguage($module='',$language='') {
 		global $adb;
 		if ($module != '' && $language == '') {
 			$adb->pquery('DELETE FROM sdk_language WHERE module = ?',array($module));
 		} elseif ($module == '' && $language != '') {
 			$adb->pquery('DELETE FROM sdk_language WHERE language = ?',array($language));
 		} elseif ($module != '' && $language != '') {
 			$adb->pquery('DELETE FROM sdk_language WHERE module = ? AND language = ?',array($module,$language));
 		}
 		SDK::clearSessionValue('sdk_js_lang');
 		SDK::clearSessionValue('vte_languages');
 	}

 	function getModuleLanguageList() {
 		global $adb,$table_prefix;
 		$sql = "select name from ".$table_prefix."_tab where name <> ?";
		$res = $adb->pquery($sql,Array('Events'));
		while ($row = $adb->fetchByAssoc($res,-1,false)){
			$modules[] = $row[name];
		}
		$modules[] = 'Settings';
		$modules[] = 'CustomView';
		$modules[] = 'Administration';
		$modules[] = 'System';
		$modules[] = 'Picklistmulti';
		$modules[] = 'PickList';
		$modules[] = 'Import';
		$modules[] = 'Help';
		$modules[] = 'com_vtiger_workflow';
		$modules[] = 'Utilities';
		$modules[] = 'Yahoo';
		return $modules;
 	}

 	function importPhpLanguage($language) {
 		$modules = SDK::getModuleLanguageList();
 		foreach ($modules as $module){
 			SDK::file2DbLanguage($module,$language);
 		}
	 	unset($app_strings);
		unset($app_list_strings);
		unset($app_strings);
		@include("include/language/$language.lang.php");
		if (isset($app_strings)){
			insert_language('APP_STRINGS',$language,$app_strings);
		}
		if (isset($app_list_strings)){
			insert_language('APP_LIST_STRINGS',$language,$app_list_strings);
		}
		if (isset($app_currency_strings)){
			insert_language('APP_CURRENCY_STRINGS',$language,$app_currency_strings);
		}
 	}

 	function importJsLanguage($language) {
 		echo '<div style="display:none;"><iframe src="index.php?module=SDK&action=SDKAjax&file=InstallJsLang&language='.$language.'"></iframe></div>';
 	}

	function checkJsLanguage() {
 		global $adb, $current_language;
 		if (isModuleInstalled('SDK')) {
 			$cache = Cache::getInstance('sdk_js_lang');
			$type = $cache->getType();
			if ($type == 'file') {
				$cacheFolder = SDK::getCacheFolder('sdk_js_lang');
	 			$cacheFile = $cacheFolder.$current_language.'.lang.js';
	 			if (file_exists($cacheFile)) {
	 				return;
	 			}
			} elseif ($type == 'session') {
				$tmp = $cache->get();
				if ($tmp !== false && $tmp != 'var alert_arr = {}') {
					return;
				}
			}
	 		$result = $adb->pquery("SELECT * FROM sdk_language WHERE module = ? and language = ?",array('ALERT_ARR',$current_language));
	 		if (!$result || $adb->num_rows($result) == 0) {
	 			require_once('modules/SDK/InstallJsLangs.php');
	 		}
 		}
 	}
 	
 	/**
	 * this method use always the cache although it is not Cache::$enabled is false
	 */
	function loadJsLanguage() {
		global $current_language;
		$cache = Cache::getInstance('sdk_js_lang');
		$type = $cache->getType();
		if ($type == 'file') {
	 		$cacheFolder = SDK::getCacheFolder('sdk_js_lang');
	 		if (file_exists($cacheFolder.'id.php')) {
	 			include($cacheFolder.'id.php');
	 			$cacheFile = $cacheFolder.$current_language.'_'.$sdk_js_lang_id.'.js';
	 		}
	 		if (!file_exists($cacheFile)) {
	 			if (empty($sdk_js_lang_id)) {
		 			$sdk_js_lang_id = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
					$fp = fopen($cacheFolder.'id.php',"wb");
					fwrite($fp,"<?php \$sdk_js_lang_id = '$sdk_js_lang_id';?>");
					fclose($fp);
					$cacheFile = $cacheFolder.$current_language.'_'.$sdk_js_lang_id.'.js';
	 			}
				$data = "var alert_arr = {\n";
	 			global $adb;
	 			$res = $adb->pquery("SELECT label, trans_label FROM sdk_language WHERE module = ? and language = ?",array('ALERT_ARR',$current_language));
				if ($res && $adb->num_rows($res) > 0) {
					while ($row = $adb->fetchByAssoc($res,-1,false)) {
						$trans_label = $row['trans_label'];
						$trans_label = html_entity_decode($trans_label, ENT_QUOTES);
						if (strpos($trans_label,"\'") === false) {
							$trans_label = addcslashes(html_entity_decode($trans_label, ENT_QUOTES), "'");
						}
						$trans_label = str_replace("\r",'\r',$trans_label);
						$trans_label = str_replace("\n",'\n',$trans_label);
						$data .= "\t".addcslashes(html_entity_decode($row['label'], ENT_QUOTES), "'").":'".$trans_label."',\n";
					}
					$data = substr($data,0,-2)."\n";
				}
				$data .= "}";
				$fp = fopen($cacheFile,"wb");
				fwrite($fp,$data);
				fclose($fp);
	 		}
	 		if (!empty($cacheFile)) {
		 		return $cacheFile;
	 		}
 		} elseif ($type == 'session') {
	 		$data = $cache->get();
	 		if ($data === false) {
	 			global $adb;
	 			$res = $adb->pquery("SELECT label, trans_label FROM sdk_language WHERE module = ? and language = ?",array('ALERT_ARR',$current_language));
				if ($res && $adb->num_rows($res) > 0) {
					while ($row = $adb->fetchByAssoc($res,-1,false)) {
						$trans_label = $row['trans_label'];
						$trans_label = html_entity_decode($trans_label, ENT_QUOTES);
						if (strpos($trans_label,"\'") === false) {
							$trans_label = addcslashes(html_entity_decode($trans_label, ENT_QUOTES), "'");
						}
						$trans_label = str_replace("\r",'\r',$trans_label);
						$trans_label = str_replace("\n",'\n',$trans_label);
						$data .= "\t".addcslashes(html_entity_decode($row['label'], ENT_QUOTES), "'").":'".$trans_label."',\n";
					}
					$data = substr($data,0,-2)."\n";
				}
				if (!empty($data)) $data = "\n".$data;
				$data = "var alert_arr = {".$data."}";
	 			$cache->set($data);
	 		}
	 		return $data;
		}
 	}
	
	function getCacheFolder($cache='') {
 		$focus = new SDK();
 		$cacheFolder = $focus->cacheFolder;
 		if (!empty($cache)) {
 			$cacheFolder .= $cache.'/';
 		}
 		return $cacheFolder;
 	}
 	
 	function getCachedLanguageFilename($language) {
 		if (empty($language)) {
 			global $current_language;
 			$language = $current_language;
 		}
 		return SDK::getCacheFolder('vte_languages')."$language.lang.php";
 	}
 	
 	// crmv@106294
	function getCachedLanguage($module,$language) {
		$cache = Cache::getInstance("SDK/vte_languages/{$language}.lang");
		$langs = $cache->get();
		if (!is_array($langs) || !isset($langs[$module])) return false;
		return $langs[$module];
 	}
 	
	function cacheLanguage($language) {
 		global $adb;
		$arr = array();
		$sql = "select module, label, trans_label from sdk_language where language = ? and module <> ? order by module";
		$params = Array($language,'ALERT_ARR');
		$res = $adb->pquery($sql,$params);
		$data = array();
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByAssoc($res,-1,false)) {
				$module = $row['module'];
				$len = strlen($row['trans_label'])-1;
				if ($row['trans_label'][0] == '{' && $row['trans_label'][$len] == '}') {
					$trans_label = Zend_Json::decode(html_entity_decode($row['trans_label']));
				} elseif ($module == 'APP_STRINGS')
					$trans_label = replace_version_strings(html_entity_decode($row['trans_label']));
				else
					$trans_label = html_entity_decode($row['trans_label']);
					
				if (is_array($trans_label)) {
					foreach($trans_label as $l => $t) {
						$t = html_entity_decode($t, ENT_QUOTES);
						//if (strpos($t,"\'") === false) {
						//	$t = addcslashes(html_entity_decode($t, ENT_QUOTES), "'");
						//}
						$trans_label[$l] = $t;
					}
				} else {
					$trans_label = html_entity_decode($trans_label, ENT_QUOTES);
					//if (strpos($trans_label,"\'") === false) {
					//	$trans_label = addcslashes(html_entity_decode($trans_label, ENT_QUOTES), "'");
					//}
				}
				$label = addcslashes(html_entity_decode($row['label'], ENT_QUOTES), "'");
				$data[$module][$label] = $trans_label;
			}
		}

		$cache = Cache::getInstance("SDK/vte_languages/{$language}.lang");
		$cache->set($data);
 	}
 	// crmv@106294e
 	
}
