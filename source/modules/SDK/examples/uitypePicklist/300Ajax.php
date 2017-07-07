<?php
	require_once('300Utils.php');


	$function = $_REQUEST['function'];
	$ret = array();

	switch ($function) {
		case 'linkedListGetChanges':
			// crmv@30528
			$name = vtlib_purify($_REQUEST['name']);
			$sel = vtlib_purify($_REQUEST['sel']);
			$mod = vtlib_purify($_REQUEST['modname']);

		 	$ret = linkedListGetChanges($name, $sel, $mod);
		 	// crmv@30528e
			break;
		default:
			break;
	}


	die(Zend_Json::encode($ret));
?>