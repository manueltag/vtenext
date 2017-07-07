<?php

// crmv@105933
// remove some tools for the module
if ($smarty && is_array($smarty->get_template_vars('CHECK'))) {
	$tool_buttons = $smarty->get_template_vars('CHECK');
	unset($tool_buttons['EditView']);
	unset($tool_buttons['Import']);
	unset($tool_buttons['Merge']);
	unset($tool_buttons['DuplicatesHandling']);
	$smarty->assign('CHECK', $tool_buttons);
}

