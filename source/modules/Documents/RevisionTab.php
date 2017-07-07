<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

/* crmv@43147 crmv@53745 crmv@95157 */

require_once('modules/Documents/storage/StorageBackendUtils.php');

$record = intval($_REQUEST['record']);

$SBU = StorageBackendUtils::getInstance();
$revisions = $SBU->getRevisions('Documents', $record);

$html = '<table width="100%" border="0" cellspacing="1" cellpadding="3" class="lvt small">';
if (count($revisions) > 0) {
	$html .= '
	  <tr>
	    <td></td>
	    <td>'.getTranslatedString('LBL_FILE_NAME','Documents').'</td>
	    <td align="center">'.getTranslatedString('Revisionato Da','Documents').'</td>
	    <td align="center">'.getTranslatedString('Data Revisione','Documents').'</td>
	  </tr>';
	foreach ($revisions as $row){
		$name_reduced = $row['name'];
		$maxlen = 20;
		if (strlen($name_reduced) > $maxlen) {
			$name_reduced = substr($name_reduced,0,$maxlen/2-2).'...'.substr($name_reduced,-($maxlen/2-2));
		}
		$url = '<a href="index.php?module=uploads&action=downloadfile&return_module=Documents&fileid='.$row['attachmentid'].'&entityid='.$record.'" title="'.$row['name'].'" >'.$name_reduced.'</a>';
		$html .= '<tr bgcolor="white">';
		$html .= '<td>'.$row['revision'].'</td>';
		$html .= '<td>'.$url.'</td>';
		$html .= '<td align="center">'.(empty($row['user_email']) ? getUserName($row['userid']) : $row['user_email']).'</td>';
		$html .= '<td align="center">'.getDisplayDate(substr($row['revisiondate'], 0, 10)).'</td>';
		$html .= '</tr>';
	}
}else{
	$html = '<tr><td>'.getTranslatedString('NO_REVS','Documents').'</td></tr>';
}
$html .= '</table>';

echo $html;
