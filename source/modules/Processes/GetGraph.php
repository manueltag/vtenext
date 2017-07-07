<?php
include('config.inc.php');
require_once('include/utils/utils.php');

/* 
  Retrieve the graph. This wrapper is necessary, because non-admin users
  don't have access to the Settings directory.
*/

global $app_strings;

$mode = $_REQUEST['mode'];

if ($mode == 'download') {
	require('modules/Settings/ProcessMaker.php');
} else {
	die($app_strings['LBL_PERMISSION']);
}