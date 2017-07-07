<?php

function checkGeoButton(&$instance) {
	global $current_user;

	$tabid = getTabid('Geolocalization');
	if (!$tabid) return false;

	require('user_privileges/requireUserPrivileges.php');
	$permitted = ($profileTabsPermission[$tabid] == 0);

	if (vtlib_isModuleActive('Geolocalization') && $permitted) {
		return true;
	} else {
		return false;
	}

}

