<?php
/*
 * INPUT: $sdk_columnnames
 * OUTPUT: $sdk_columnvalues
 */

$sdk_columnvalues = array();
if (is_array($sdk_columnnames) && !empty($sdk_columnnames)) {
	foreach($sdk_columnnames as $sdkfield) {
		switch ($sdk_mode) {
			case 'list':
				$sdk_columnvalues[$sdkfield] = $this->db->query_result($result, $i, $sdkfield);
				break;
			case 'popup':
			case 'related':
				$sdk_columnvalues[$sdkfield] = $adb->query_result($list_result, $i-1, $sdkfield);
				break;
			default:
				break;
		}
	}
}

?>