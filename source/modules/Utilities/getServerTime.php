<?php
/* crmv@62394 */

$format = $_REQUEST['oformat'];

$ts = time();

if ($format == 'raw') {
	echo $ts;
} elseif ($format == 'json') {
	echo Zend_Json::encode(array('success' => true, 'timestamp'=>$ts));
}
