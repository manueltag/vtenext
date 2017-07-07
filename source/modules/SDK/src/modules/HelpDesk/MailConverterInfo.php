<?php
/* crmv@2043m crmv@60095 crmv@87556 */
global $sdk_mode;
if ($sdk_mode == "") {
	if ($fieldname == 'parent_id') {
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '') {
			$value = $_REQUEST['parent_id'];
		}
		if($value != '') {
			$parent_module = getSalesEntityType($value);
			if (!in_array($parent_module,array('Contacts','Accounts','Leads'))) {
				$value = '';
			}
		}
		$col_fields['parent_id'] = $value;
	}
}
$success = true;
?>