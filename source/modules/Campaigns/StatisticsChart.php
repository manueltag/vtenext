<?php
/*+*************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@38600 crmv@101506 */

global $serie_vals;

require_once('include/utils/utils.php');
require_once("include/pChart/class/pData.class.php");
require_once("include/pChart/class/pDraw.class.php");
require_once("include/pChart/class/pImage.class.php");

//crmv@25083 - reload related
$focus->get_statistics_message_queue($focus->id, 26, 0, false, true, true);
$focus->get_statistics_sent_messages($focus->id, 26, 0, false, true, true);
$focus->get_statistics_viewed_messages($focus->id, 26, 0, false, true, true);
$focus->get_statistics_tracked_link($focus->id, 26, 0, false, true, true);
$focus->get_statistics_unsubscriptions($focus->id, 26, 0, false, true, true);
$focus->get_statistics_bounced_messages($focus->id, 26, 0, false, true, true);
$focus->get_statistics_suppression_list($focus->id, 26, 0, false, true, true);
$focus->get_statistics_failed_messages($focus->id, 26, 0, false, true, true);
//crmv@25083e

$points = array_keys($related_array);
array_walk($points,'getSerieValues',false);
array_walk($points,'translate','Campaigns');
function translate(&$label, $id, $module) {
	$label = getTranslatedString($label,$module);
}
//se $group_by_crmid = true :
//non ho lo stesso conteggio delle related perchè conto solamente i crmid/email coinvolti (distinct crmid/email per capirci)
function getSerieValues($label,$id,$group_by_crmid) {
	global $adb,$serie_vals;
	if ($_SESSION[strtolower($label).'_listquery'] != '') {
		$result = $adb->query($_SESSION[strtolower($label).'_listquery']);
		if ($result && $adb->num_rows($result)>0) {
			while($row=$adb->fetchByAssoc($result)) {
				if ($label == 'Suppression list') {
					if ($group_by_crmid) {
						$serie_vals[$id][$row['email']] = '';
					} else {
						$serie_vals[$id][] = $row['email'];
					}
				} else {
					//crmv@25083
					$module = $row['setype'];
					if ($group_by_crmid) {
						$serie_vals[$id][$module][$row['crmid']] = '';
					} else {
						$serie_vals[$id][$module][] = $row['crmid'];
					}
					//crmv@25083e
				}
			}
		}
	}
}

$serie = array(
	getTranslatedString('Contacts') => array(
		count($serie_vals[0]['Contacts']),
		count($serie_vals[1]['Contacts']),
		count($serie_vals[2]['Contacts']),
		count($serie_vals[3]['Contacts']),
		count($serie_vals[4]['Contacts']),
		count($serie_vals[5]['Contacts']),
		0,
		count($serie_vals[7]['Contacts']),
	),
	getTranslatedString('Accounts') => array(
		count($serie_vals[0]['Accounts']),
		count($serie_vals[1]['Accounts']),
		count($serie_vals[2]['Accounts']),
		count($serie_vals[3]['Accounts']),
		count($serie_vals[4]['Accounts']),
		count($serie_vals[5]['Accounts']),
		0,
		count($serie_vals[7]['Accounts']),
	),
	getTranslatedString('Leads') => array(
		count($serie_vals[0]['Leads']),
		count($serie_vals[1]['Leads']),
		count($serie_vals[2]['Leads']),
		count($serie_vals[3]['Leads']),
		count($serie_vals[4]['Leads']),
		count($serie_vals[5]['Leads']),
		0,
		count($serie_vals[7]['Leads']),
	),
	getTranslatedString('LBL_ALL') => array(
		count($serie_vals[0]['Contacts'])+count($serie_vals[0]['Accounts'])+count($serie_vals[0]['Leads']),
		count($serie_vals[1]['Contacts'])+count($serie_vals[1]['Accounts'])+count($serie_vals[1]['Leads']),
		count($serie_vals[2]['Contacts'])+count($serie_vals[2]['Accounts'])+count($serie_vals[2]['Leads']),
		count($serie_vals[3]['Contacts'])+count($serie_vals[3]['Accounts'])+count($serie_vals[3]['Leads']),
		count($serie_vals[4]['Contacts'])+count($serie_vals[4]['Accounts'])+count($serie_vals[4]['Leads']),
		count($serie_vals[5]['Contacts'])+count($serie_vals[5]['Accounts'])+count($serie_vals[5]['Leads']),
		count($serie_vals[6]),
		count($serie_vals[7]['Contacts'])+count($serie_vals[7]['Accounts'])+count($serie_vals[7]['Leads']),
	),
);

//crmv e

// Dataset definition
$DataSet = new pData;

$DataSet->AddPoints($points,"Label");
$DataSet->setAbscissa("Label");
$DataSet->setSerieDescription("Label","Label");

foreach($serie as $label => $info) {
	$DataSet->AddPoints($info,$label);
}

// crmv@69743
function chartAxisFormat($v) {
	if (is_float($v)) $v = round($v, 1);
	return $v;
}
$DataSet->setAxisDisplay(0, AXIS_FORMAT_CUSTOM,"chartAxisFormat");
// crmv@69743e


// Initialise the graph
$Test = new pImage(1200, 350, $DataSet);	//crmv@59091
$Test->setFontProperties(array('FontName' => "include/pChart/fonts/MYRIADPRO-REGULAR.OTF", 'FontSize'=>10));
$Test->setGraphArea(260, 30, 1180, 340);	//crmv@59091

$Test->drawScale(array('Mode' => SCALE_MODE_START0, 'GridR'=>50, 'GridG'=>50, 'GridB'=>50, 'CycleBackground'=>true, "Pos"=>SCALE_POS_TOPBOTTOM));	//crmv@59091

// Draw the bar graph
$Test->drawBarChart();

// Finish the graph
$Test->setFontProperties(array('FontName' => "include/pChart/fonts/MYRIADPRO-REGULAR.OTF", 'Fontsize' => 12));
$Test->drawLegend(5, 40, array());	//crmv@59091
if (!is_dir('cache/charts/')) @mkdir('cache/charts');
$Test->Render("cache/charts/StatisticsChart.png");
?>