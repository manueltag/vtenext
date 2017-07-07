<?php
/* crmv@74560 */

require('config.inc.php');
require_once('modules/Users/CreateUserPrivilegeFile.php');

$SR = new SharingPrivileges();
$SR->recalcFromCron();