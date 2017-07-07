<?php
/* crmv@31780 - TODO: paginazione*/
/* crmv@33097 */

$module = $_REQUEST['module'];

if(!$login || empty($userId)) {
	echo 'Login Failed';

} elseif (in_array($module, $touchInst->excluded_modules)) {
	echo "Module not permitted";

} else {

	$wsclass = new TouchGetRelated();
	$wsclass->execute($_REQUEST);
}
?>
