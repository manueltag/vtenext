<?php
/* crmv@97862 */

require_once('modules/Reports/Reports.php');


$mode = 'ajax';
$reportid = intval($_REQUEST['reportid']);
$action = $_REQUEST['subaction'];
$raw = null;
$tpl = '';
$json = null;

$reports = Reports::getInstance();

if ($action == 'GetModulesList') {
	$chain = Zend_Json::decode($_REQUEST['chain']);
	$getFields = ($_REQUEST['getfields'] == 1);
	$modules = $reports->getModulesListForChain($reportid, $chain);
	if ($getFields) {
		$ftype = $_REQUEST['fieldstype'];
		if ($ftype == 'total') {
			$fields = $reports->getTotalsFieldsListForChain($reportid, $chain);
		} elseif ($ftype == 'stdfilter') {
			$fields = $reports->getStdFiltersFieldsListForChain($reportid, $chain);
		} elseif ($ftype == 'advfilter') {
			$fields = $reports->getAdvFiltersFieldsListForChain($reportid, $chain);
		} else {
			$fields = $reports->getFieldsListForChain($reportid, $chain);
		}
	}
	$result = array('modules' => $modules, 'fields' => $fields);
	$json = array('success' => true, 'error' => null, 'result' => $result);

} elseif ($action == 'GetFieldsList') {
	$chain = Zend_Json::decode($_REQUEST['chain']);
	$fields = $reports->getFieldsListForChain($reportid, $chain);
	$json = array('success' => true, 'error' => null, 'result' => $fields);

} elseif ($action == 'GetStdFiltersFieldsList') {
	$chain = Zend_Json::decode($_REQUEST['chain']);
	$fields = $reports->getStdFiltersFieldsListForChain($reportid, $chain);
	$json = array('success' => true, 'error' => null, 'result' => $fields);

} elseif ($action == 'GetAdvFiltersFieldsList') {
	$chain = Zend_Json::decode($_REQUEST['chain']);
	$fields = $reports->getAdvFiltersFieldsListForChain($reportid, $chain);
	$json = array('success' => true, 'error' => null, 'result' => $fields);
	
} elseif ($action == 'GetTotalsFieldsList') {
	$chain = Zend_Json::decode($_REQUEST['chain']);
	$fields = $reports->getTotalsFieldsListForChain($reportid, $chain);
	$json = array('success' => true, 'error' => null, 'result' => $fields);

} elseif ($action == 'SaveReport') {
	$success = true;
	$error = '';
	try {
		$config = $reports->prepareForSave($reportid, $_REQUEST);
	} catch (Exception $e) {
		$success = false;
		$error = $e->getMessage();
	}
	if ($success) {
		$newReportid = $reports->saveReport($reportid, $config);
		$result = array(
			'reportid' => $newReportid,
			'folderid' => $config['folderid'],
		);
		$chartinfo = Zend_Json::decode($_REQUEST['chartinfo']);
		if ($newReportid && !empty($chartinfo)) {
			$chartid = $reports->createChart($newReportid, $chartinfo);
			if (!$chartid) {
				// delete the report
				$reports->deleteReport($newReportid);
				$success = false;
				$error = 'Unable to create che chart';
			}
		}
	}	
	$json = array('success' => $success, 'error' => $error, 'result' => $result);
	
} else {
	$json = array('success' => false, 'error' => "Unknown subaction");
}

// output
if (!is_null($raw)) {
	echo $raw;
	exit(); // sorry, I have to do this, some html shit is spitted out at the end of the page
} elseif (!empty($tpl) && isset($smarty)) {
	$smarty->display('modules/Reports/'.$tpl);
} elseif (!empty($json)) {
	echo Zend_Json::encode($json);
	exit(); // idem
} else {
	echo "No data returned";
}
