<?php
global $adb;
global $currentModule, $current_user;

$reqtype = $_REQUEST['type'];

if ($reqtype == 'picklist') {

	$queryGenerator = QueryGenerator::getInstance($currentModule, $current_user);
	$queryGenerator->initForDefaultCustomView();
	$list_query = $queryGenerator->getQuery();
	$list_query .= " ORDER BY chartname";

	// TODO: limit ??
	$list_result = $adb->query($list_query);

	$outstr = '<select id="selchart_id" onchange="jQuery(\'#stufftitle_id\').val(jQuery(this).find(\'option:selected\').text());">';
	if ($list_result) {
		while ($row = $adb->fetchByAssoc($list_result)) {
			$outstr .= '<option value="'.$row['chartid'].'">'.$row['chartname'].'</option>';
		}
	}
	$outstr .= '</select>';
	echo $outstr;
}

?>