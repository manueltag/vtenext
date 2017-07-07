<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
//crmv@35153
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
	global $currentModule, $mod_strings, $app_strings;
	$currentModule = 'Morphsuit';
	$current_language = 'en_us';
	$path = '../../';
	$mod_strings = return_module_language($current_language, $currentModule);
	$app_strings = return_application_language($current_language);
}
//crmv@35153e

global $mod_strings,$currentModule;
require_once("modules/Emails/mail.php");
$mail = new PHPMailer();

$type = $_REQUEST['type'];
if ($type == 'SendMorphsuit') {
	$subject = $mod_strings['LBL_MORPHSUIT_ACTIVATION'].' VTE';
	//crmv@35153
	if (!empty($_REQUEST['vte_user_info'])) {
		$vte_user_info = Zend_Json::decode($_REQUEST['vte_user_info']);
		$from_email = $vte_user_info['email'];
		$from_name = $vte_user_info['name'];
	}
	//crmv@35153e
	require_once('modules/Morphsuit/Morphsuit.php');
	$focusMorphsuit = new Morphsuit();
	$to_email = $focusMorphsuit->vteActivationMail;
	$contents = $_REQUEST['chiave'];
} elseif ($type = 'ErrorFreeKey') {
	$subject = $mod_strings['LBL_ERROR_VTE_FREE'];
	$from_email = $from_name = $_REQUEST['email'];
	$to_email = 'errors@crmvillage.biz';
	$contents = '<b>$_REQUEST</b><pre>'.print_r($_REQUEST,true).'</pre><b>$_SESSION</b><pre>'.print_r($_SESSION,true).'</pre><b>$_SERVER</b><pre>'.print_r($_SERVER,true).'</pre>';
}

setMailerProperties($mail,$subject,$contents,$from_email,$from_name,trim($to_email,","),'','',$currentModule,'');
$mail->SMTPAuth = true;
(stripos($to_email,'@vtecrm.com') !== false) ? $mail->Host = 'mail.vtecrm.com' : $mail->Host = 'mail.crmvillage.biz';
$mail->Username = 'betakey@vtecrm.com';
$mail->Password = 'pupazz0';
$mail_status = MailSend($mail);
if($mail_status != 1)
	echo $mail_status;
die;
?>