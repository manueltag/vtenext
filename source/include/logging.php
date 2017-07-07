<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/logging.php,v 1.1 2004/08/17 13:23:37 gjayakrishnan Exp $
 * Description:  Kicks off log4php.
 ********************************************************************************/
 
require_once('config.php');

// crmv@115378
require_once('include/utils/PerformancePrefs.php');
if(PerformancePrefs::getBoolean('LOG4PHP_DEBUG', false)) {
	define('LOG4PHP_DIR', 'log4php.debug');
} else {
	define('LOG4PHP_DIR', 'log4php');
}
// crmv@115378e

define('LOG4PHP_DEFAULT_INIT_OVERRIDE', true);

require_once(LOG4PHP_DIR.'/LoggerManager.php');
require_once(LOG4PHP_DIR.'/LoggerPropertyConfigurator.php');

$config = new LoggerPropertyConfigurator();
$config->configure('log4php.properties');
//crmv@47905 crmv@65455
class vtelog{
	protected static $pagetIDtiming = false; 
 	public static function start($name){
 		if(!PerformancePrefs::getBoolean('APP_LOG_TIMING', false)) {
 			return;
 		}
 		$_SESSION['timelog'][$name]['start'] = microtime(true);
 	}
 	public static function stop($name,$log=true){
  		if(!PerformancePrefs::getBoolean('APP_LOG_TIMING', false)) {
 			return;
 		} 		
 		$_SESSION['timelog'][$name]['end'] = microtime(true);
 		if ($log){
 			self::log($name);
 		}
 	}
 	public static function getpageid(){
  		if(!PerformancePrefs::getBoolean('APP_LOG_TIMING', false)) {
 			return;
 		} 	 		
 		return self::$pagetIDtiming;
 	}
 	public static function log($name,$type='APP',$start=false,$end=false,$params_query=false){
  		if(($type == 'APP' && !PerformancePrefs::getBoolean('APP_LOG_TIMING', false)) || ($type == 'SQL' && !PerformancePrefs::getBoolean('SQL_LOG_TIMING', false))) {
 			return;
 		} 		
 		global $adb;
		$today  = date('Y-m-d H:i:s'); 
		$logtable = 'tbl_s_logtime';
		if (self::$pagetIDtiming === false) {
			self::$pagetIDtiming = $adb->getUniqueID($logtable);
		}
		$params = Array();
		$params['id'] = self::$pagetIDtiming;
		if (PerformancePrefs::getBoolean('BACKTRACE_LOG_TIMING', false)){
			$params['request'] = (php_sapi_name() == 'cli') ? 'CLI' : 'REQ'.':';
			if (isset($_SERVER['REQUEST_METHOD'])) {
				$uri  = $_SERVER['REQUEST_URI'];
				$qmarkIndex = strpos($_SERVER['REQUEST_URI'], '?');
				if ($qmarkIndex !== false) $uri = substr($uri, 0, $qmarkIndex);
				$params['request'] = $uri . '?'. http_build_query($_SERVER['REQUEST_METHOD'] == 'GET'? $_GET:$_POST);
			} else if ($argv) {
				$params['request'] = implode(' ', $argv);
			}
			$params['caller'] = array();
			$callers = debug_backtrace();
			for ($calleridx = 0, $callerscount = count($callers); $calleridx < $callerscount; ++$calleridx) {
				if ($calleridx == 0) {
					continue;
				}
				if ($calleridx < $callerscount) {
					$callerfunc = $callers[$calleridx+1]['function'];
					if (!empty($callerfunc)) $callerfunc = " ($callerfunc) ";
				}
				$params['caller'][] = "CALLER: (" . $callers[$calleridx]['line'] . ') ' . $callers[$calleridx]['file'] . $callerfunc;
			}
			$params['caller'] = implode("\n", $params['caller']);
		}
		$params['type'] = $type;
		switch($type){
			case 'APP':
				$params['content'] = $name;
				$params['start'] = $_SESSION['timelog'][$name]['start'];
				$params['end'] = $_SESSION['timelog'][$name]['end'];
				break;
			case 'SQL':
				$params['content'] = trim($name);
				if (is_array($params_query) && !empty($params_query)) {
					$params['content'] = $adb->convert2Sql($params['content'],$adb->flatten_array($params_query));
				}
				$params['start'] = $start;
				$params['end'] = $end;				
				break;	
		}
		$params['time_elapsed'] = $params['end']-$params['start'];
		$params['loggedon'] = $today;
		$column = array_keys($params);
 		$adb->format_columns($column);
		$logsql = "INSERT INTO {$logtable} (".implode(",",$column).") VALUES (".generateQuestionMarks($params).")";
		$adb->database->Execute($logsql,$params);
 	}	
}
//crmv@47905e crmv@65455e
?>