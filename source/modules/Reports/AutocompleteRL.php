<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@31775 crmv@98500 */

require_once('modules/Reports/Reports.php');
$module = $_REQUEST['cvmodule'];
$field = $_REQUEST['field'];
if ($module != '') {
	$reports = Reports::getInstance();
	$report_list = $reports->sgetRptsforFldr(false,$module);
	$autocomplete_return = array();
	foreach($report_list as $folder) {
		foreach($folder as $report) {
			$reportname = getTranslatedString($report['reportname'],'Reports');
			if (stripos($reportname,$_REQUEST['autocomplete_where']) !== false || $_REQUEST['autocomplete_where'] == 'ALL') {
				$autocomplete_return[] = array(
					'id'=>$report['reportid'],
					'label'=>$reportname,
					'return_function'=>"return_report_to_rl(".$report['reportid'].",'".$reportname."','".$field."')",
					'return_function_file'=>'modules/Targets/Targets.js',
				);
			}
		}
	}
	die(Zend_Json::encode($autocomplete_return));
}
