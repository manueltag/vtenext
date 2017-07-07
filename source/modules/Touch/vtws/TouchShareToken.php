<?php

$module = $_REQUEST['module'];

if (!$login || empty($userId)) {
	echo 'Login Failed';
} elseif (in_array($module, $touchInst->excluded_modules)) {
	echo "Module not permitted";
} else {

	$wsclass = new TouchShareToken();
	$wsclass->execute($_REQUEST);

}
?>