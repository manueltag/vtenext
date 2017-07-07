<?php
//crmv@27096
$module = vtlib_purify($_REQUEST['selected_module']);
$ids = vtlib_purify($_REQUEST['selected_ids']);
if ($module != '' && $ids != '') {
	saveListViewCheck($module,$ids);
}
exit;
//crmv@27096e
?>