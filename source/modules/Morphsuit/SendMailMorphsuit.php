<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@130421 */

$installation_mode = false;
if (empty($_SESSION)) {
	session_start();
}
if ($_SESSION['morph_mode'] == 'installation') {
	$installation_mode = true;
	if (!isset($root_directory)) {
		require_once('../../config.inc.php');
	}
	chdir($root_directory);
	require_once('include/utils/utils.php');
}

global $adb, $table_prefix;

$from_email = $_REQUEST['email_std'];
$to_email = 'welcome@vtenext.org';
$subject = 'VTENEXT CE Registration';
$body = $_REQUEST['key'];
/*
$body = '';
if (!empty($_REQUEST)) {
	foreach ($_REQUEST as $k => $v) {
		$body .= "$k: $v<br>";
	}
}
*/

$mail_error = false;
$server = '';
$res = $adb->pquery("select * from {$table_prefix}_systems where server_type=?", array('email'));
if ($res && $adb->num_rows($res) > 0) {
	$server = $adb->query_result($res,0,'server');
}
if ($server == '') {
	$domains = array(
		$_SERVER['SERVER_NAME'],
		substr($from_email,strpos($from_email,'@')+1),
		substr($to_email,strpos($to_email,'@')+1),
	);
	$focusMessages = CRMEntity::getInstance('Messages');
	$userAccounts = $focusMessages->getUserAccounts();
	if (!empty($userAccounts)) {
		foreach($userAccounts as $account) {
			if (!empty($account['server'])) {
				$domains[] = $account['server'];
			}
			if (!empty($account['domain'])) {
				$domains[] = $account['domain'];
			}
			if (!empty($account['username'])) {
				$domains[] = substr($account['username'],strpos($account['username'],'@')+1);
			}
		}
	}
	$domains = array_filter($domains);
	if(!empty($domains)) {
		$exit = false;
		foreach ($domains as $domain) {
			$mxhosts = array();
			getmxrr($domain, $mxhosts);
			foreach($mxhosts as $mxhost) {
				$servers = array_filter(array($mxhost, gethostbyname($mxhost)));
				foreach ($servers as $server) {
					$_REQUEST['server'] = $server;
					$_REQUEST['server_username'] = '';
					$_REQUEST['server_password'] = '';
					$_REQUEST['smtp_auth'] = '';
					$mail_status = send_mail('Users',$to_email,$from_email,$from_email,$subject,$body);
					if($mail_status != 1) {
						$mail_error = true;
					} else {
						$exit = true;
						break;
					}
				}
				if ($exit) {
					break;
				}
			}
			if ($exit) {
				break;
			}
		}
	}
	(!$exit) ? $res = 'error' : $res = 'success';
} else {
	$mail_status = send_mail('Users',$to_email,$from_email,$from_email,$subject,$body);
	if($mail_status != 1) {
		$mail_error = true;
	}
	($mail_error) ? $res = 'error' : $res = 'success';
}
echo $res; exit;