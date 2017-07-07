<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@83340 crmv@96155 crmv@98431 crmv@102379 crmv@102334 */

require_once('include/utils/ModuleHomeView.php');
require_once('modules/Reports/Reports.php');
require_once('Smarty_setup.php');

global $adb, $table_prefix, $currentModule, $current_user, $app_strings;

$outputMode = 'json';
$output = null;
$ajxaction = $_REQUEST['ajxaction'];

$MHW = ModuleHomeView::getInstance($currentModule, $current_user->id);

$homeid = intval($_REQUEST['modhomeid']);

if ($ajxaction == 'loadblock') {
	$blockid = intval($_REQUEST['blockid']);
	$blockHtml = $MHW->getBlockContent($homeid, $blockid, array('reload' => true));
	$output = array('success' => true, 'result' => $blockHtml);
	
} elseif ($ajxaction == 'loadblocks') {
	$output = array('success' => true, 'result' => array());
	
	$blockids = array_filter(array_map('intval', explode(':', $_REQUEST['blockids'])));
	foreach ($blockids as $blockid) {
		$blockHtml = $MHW->getBlockContent($homeid, $blockid);
		$output['result'][$blockid] = $blockHtml;
	}
} elseif ($ajxaction == 'savesequence') {
	$blockids = array_filter(array_map('intval', explode(':', $_REQUEST['blockids'])));
	
	$MHW->reorderBlocks($homeid, $blockids);
	
	$output = array('success' => true);

} elseif ($ajxaction == 'removeblock') {
	$blockid = intval($_REQUEST['blockid']);
	$MHW->deleteBlock($homeid, $blockid);
	$output = array('success' => true);

} elseif ($ajxaction == 'confignewblock') {
	
	$type = $_REQUEST['type'];
	
	$tpls = array(
		'Chart' => 'NewChart.tpl',
		'QuickFilter' => 'NewQuickFilter.tpl',
		'Filter' => 'NewFilter.tpl',
		'Wizards' => 'NewWizards.tpl',
		'Processes' => 'NewProcesses.tpl',
	);
	
	$template = $tpls[$type];
	
	$outputMode = 'smarty';
	if ($template) {
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('APP', $app_strings);
		$smarty->assign('MODHOMEID', $homeid);
		
		if ($type == 'Chart') {
			$charts = $MHW->getAvailableCharts($currentModule, $homeid); // crmv@101227
			$smarty->assign('CHARTS', $charts);
		} elseif ($type == 'QuickFilter' || $type == 'Filter') {
			require_once('modules/CustomView/CustomView.php');
			$customView = new CustomView($currentModule);
			$filters = $customView->getCustomViewCombo('', false);
			$smarty->assign('QFILTERS', $filters);
		} elseif ($type == 'Wizards') {
			$WU = WizardUtils::getInstance();
			$wizards = $WU->getWizards(null, true);
			$smarty->assign('WIZARDS', $wizards);
		}
		
		$template = 'ModuleHome/'.$template;
	}

} elseif ($ajxaction == 'addblock') {
	
	$type = $_REQUEST['type'];
	
	$block = array();
	$block['type'] = $type;
	$block['size'] = 1;
	$block['title'] = '';
	
	if ($type == 'Chart') {
		$chartid = intval($_REQUEST['chartid']);
		$block['title'] = getEntityName('Charts', $chartid, true);
		$block['config'] = array('chartid' => $chartid);
		
	} elseif ($type == 'QuickFilter') {
		$cvid = intval($_REQUEST['cvid']);
		$block['config'] = array('cvid' => $cvid);

	} elseif ($type == 'Filter') {
		$cvid = intval($_REQUEST['cvid']);
		$block['size'] = 4;
		$block['config'] = array('cvid' => $cvid);
		
	} elseif ($type == 'Wizards') {
		$block['size'] = 1;
		$block['title'] = getTranslatedString('LBL_WIZARDS');
		$wizids = Zend_Json::decode($_REQUEST['wizards']);
		if (is_array($wizids) && count($wizids) > 0) {
			$wizids = array_filter(array_map('intval', $wizids));
			$block['config'] = array('wizardids' => $wizids);
		}
		
	// crmv@96233
	} elseif ($type == 'Processes') {
		$block['size'] = 1;
	// crmv@96233e

	} else {
		$block = null;
		$output = array('success' => false, 'error' => 'Block type not supported');
	}
	
	if ($block) {
		$blockid = $MHW->insertBlock($homeid, $block);
		if ($blockid > 0) {
			$output = array('success' => true, 'result' => array('blockid' => $blockid));
		} else {
			$output = array('success' => false, 'error' => 'Error while inserting the new block');
		}
	}
	
} elseif ($ajxaction == 'addview') {
	
	$name = $_REQUEST['viewname'];
	$reportid = intval($_REQUEST['reportid']);
	$cvid = intval($_REQUEST['cvid']);
	if (!empty($name)) {
		$homeid = $MHW->insertView(array(
			'name' => $name,
			'reportid' => $reportid,
			'cvid' => $cvid,
		));
		if ($homeid > 0) {
			$output = array('success' => true, 'result' => $homeid);
		} else {
			$output = array('success' => false, 'error' => 'Unable to save the view');
		}
	} else {
		$output = array('success' => false, 'error' => 'No name specified');
	}
	
} elseif ($ajxaction == 'removeview') {
	
	$homeid = $MHW->deleteView($homeid);
	$output = array('success' => true);
	
} elseif ($ajxaction == 'reportlist') {

	$reports = Reports::getInstance();
	$folders = $reports->getFolderList();
	foreach ($folders as $k => $foldinfo) {
		$folderid = $foldinfo['folderid'];
		$list = $reports->sgetRptsforFldr($folderid, $currentModule);
		if (count($list) > 0) {
			$folders[$k]['reports'] = $list[$folderid];
		} else {
			unset($folders[$k]);
		}
	}
	$output = array('success' => true, 'result' => $folders);
	
} elseif ($ajxaction == 'cvlist') {

	$customView = new CustomView($currentModule);
	$customview_html = $customView->getCustomViewCombo('',false);
	$output = array('success' => true, 'result' => $customview_html);
}



if ($outputMode == 'json') {
	echo Zend_Json::encode($output);
	die();
} elseif ($outputMode == 'smarty' && isset($smarty) && !empty($template)) {
	$smarty->display($template);
} elseif ($outputMode == 'raw') {
	echo $output;
	die();
} elseif ($outputMode == 'include') {
	return $output;
}
