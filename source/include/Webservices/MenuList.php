<?php
// crmv@42707

function vtws_getmenulist() {
	global $current_user;

	$menulist = array();

	$menu_module_list = getMenuModuleList();

	$menulist = array(
		'visible' => $menu_module_list[0],
		'others' => $menu_module_list[1],
	);

	return $menulist;
}


?>