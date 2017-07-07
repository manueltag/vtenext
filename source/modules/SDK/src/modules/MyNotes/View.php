<?php
//crmv@113771
global $sdk_mode;
switch($sdk_mode) {
	case '':
	case 'create':
	case 'edit':
	case 'detail':
		if ($fieldname == 'assigned_user_id') {
			$readonly = 100;
			$success = true;
		}
		break;
}