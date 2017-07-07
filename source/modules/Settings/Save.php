<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once("include/database/PearDatabase.php");
global $mod_strings,$enterprise_project;	//crmv@22252
global $table_prefix;
$server=$_REQUEST['server'];
$port=$_REQUEST['port'];
$server_username=$_REQUEST['server_username'];
$server_password=$_REQUEST['server_password'];
$server_type = $_REQUEST['server_type'];
$server_path = vtlib_purify($_REQUEST['server_path']);
$db_update = true;
if($_REQUEST['smtp_auth'] == 'on' || $_REQUEST['smtp_auth'] == 1)
	$smtp_auth = 'true';
else
	$smtp_auth = 'false';

$sql="select * from ".$table_prefix."_systems where server_type = ?";
$id=$adb->query_result($adb->pquery($sql, array($server_type)),0,"id");

if($server_type == 'proxy')
{
	$action = 'ProxyServerConfig&proxy_server_mode=edit';
	if (!$sock =@fsockopen($server, $port, $errno, $errstr, 30))
	{
		$error_str = 'error='.sprintf(getTranslatedString('LBL_UNABLE_TO_CONNECT','Settings'),array($server.':'.$port));
		$db_update = false;
	}else
	{
		$url = "http://www.google.co.in";
		$proxy_cont = '';
		$sock = fsockopen($server, $port);
		if (!$sock)    {return false;}
		fputs($sock, "GET $url HTTP/1.0\r\nHost: $server\r\n");
		fputs($sock, "Proxy-Authorization: Basic " . base64_encode ("$server_username:$server_password") . "\r\n\r\n");
		while(!feof($sock)) {$proxy_cont .= fread($sock,4096);}
		fclose($sock);
		$proxy_cont = substr($proxy_cont, strpos($proxy_cont,"\r\n\r\n")+4);
		
		if(substr_count($proxy_cont, "Cache Access Denied") > 0)
		{
			$error_str = 'error=LBL_PROXY_AUTHENTICATION_REQUIRED';
			$db_update = false;
		}
		else
		{
			$action = 'ProxyServerConfig';
		}
	}
}

if($server_type == 'ftp_backup')
{
	$action = 'BackupServerConfig&bkp_server_mode=edit&server='.$server.'&server_user='.$server_username.'&password='.$server_password;
	if(!function_exists('ftp_connect')){
		$error_str = 'error=FTP support is not enabled.';
		$db_update = false;
	}else
	{
		$conn_id = @ftp_connect($server);
		if(!$conn_id)
		{
			$error_str = 'error='.sprintf(getTranslatedString('LBL_UNABLE_TO_CONNECT','Settings'),array($server));
			$db_update = false;
		}else
		{
			if(!@ftp_login($conn_id, $server_username, $server_password))
			{
				$error_str = 'error=Couldn\'t connect to "'.$server.'" as user "'.$server_username.'"';
				$db_update = false;
			}
			else
			{
				$action = 'BackupServerConfig';
			}
			ftp_close($conn_id);
		}
	}
}

if($server_type == 'local_backup')
{
	$action = 'BackupServerConfig&local_server_mode=edit&server_path="'.$server_path.'"';
	if(!is_dir($server_path)){
		$error_str = 'error1=Folder doesnt Exist or Specified a path which is not a folder';
		$db_update = false;
	}else
	{
		if(!is_writable($server_path))
		{
			$error_str = 'error1=Access Denied to write to "'.$server_path.'"';
			$db_update = false;
		}else
		{
			$action = 'BackupServerConfig';
		}
	}
}

if($server_type == 'asterisk')
{
	$inc_call = $_REQUEST['inc_call'];
	if (!isset($_REQUEST['disable'])){
	$action = 'AsteriskConfig&asterisk_server_mode=edit';
	global $mod_strings, $extension, $ASTERISK_OUTGOING_CONTEXT;
	include_once ("asterisk/phpagi/phpagi-asmanager.php");
	$channel = $extension;
	$context = $ASTERISK_OUTGOING_CONTEXT ;
	$priority = '1';
	$timeout = '';
	$callerid = '';
	$variable = '';
	$account = '';
	$application = '';
	$data = '';
	$as = new AGI_AsteriskManager('',Array('server'=>$server,'port'=>$port,'username'=>$server_username,'secret'=>$server_password));
	$res = $as->connect();
	if (!$res){ 
			$error_str = 'error='.$mod_strings['LBL_ASTERISK_SERVER_CANT_CONNECT'].' "'.$server.'"';
			$db_update = false;
	}
	else {
		$db_update = true;
		$action = 'AsteriskConfig';
	}
	}
	else {
		$db_update = true;
		$action = 'AsteriskConfig';
	}
}

if($server_type == 'proxy' || $server_type == 'ftp_backup' || $server_type == 'local_backup')
{
	if($db_update)
	{
		if($id=='') {
			$id = $adb->getUniqueID($table_prefix."_systems");
			$params = array($id, $server, $port, $server_username, $server_password, $server_type, $smtp_auth, $server_path);
			$sql="insert into ".$table_prefix."_systems (id,server,server_port,server_username,server_password,server_type,smtp_auth,server_path) values(".generateQuestionMarks($params).")";
		}
		else {
			//crmv@43764
			if ($server_password == '') {
				$result = $adb->pquery("select server_password from {$table_prefix}_systems where id = ?",array($id));
				if ($result && $adb->num_rows($result) > 0) {
					$server_password = $adb->query_result($result,0,'server_password');
				}
			}
			//crmv@43764e
			$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, $server_path, $id);			
			$sql="update ".$table_prefix."_systems set server = ?, server_username = ?, server_password = ?, smtp_auth= ?, server_type = ?, server_port= ?, server_path = ? where id = ?";
		}
		$adb->pquery($sql, $params);
	}
}
if($server_type == 'asterisk')
{
	if($db_update)
	{
		if($id=='') {
			$id = $adb->getUniqueID($table_prefix."_systems");
			$params = array($id, $server, $port, $server_username, $server_password, $server_type, $smtp_auth, $inc_call);
			$sql="insert into ".$table_prefix."_systems (id,server,server_port,server_username,server_password,server_type,smtp_auth,inc_call) values(".generateQuestionMarks($params).")";
			
		}
		else {
			$sql="update ".$table_prefix."_systems set server = ?, server_username = ?, server_password = ?, smtp_auth= ?, server_type = ?, server_port= ?, inc_call = ? where id = ?";
			$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, $inc_call, $id);
		}
		$adb->pquery($sql, $params);
	}
}
//crmv@7216
if($server_type =='fax')
{
	require_once("modules/Fax/fax_.php");
	global $current_user;
	$service_type = $_REQUEST['service_type'];
	$domain = $_REQUEST['adv_domain'];
	$account = $_REQUEST['adv_account'];
	$prefix = $_REQUEST['adv_prefix'];
	$name = $_REQUEST['adv_name'];
	if ($service_type == 'hylafax'){
		//todo test fax
	}	
	elseif ($service_type == 'fax_mail'){
		$to_fax = getUserFaxId('id',$current_user->id);
		$from_fax = $to_fax;
		$subject = 'Test fax about the fax server configuration.';
		$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test fax sent to confirm if a fax is actually being sent through the smtp server that you have configured. </b><br>Feel free to delete this mail.<br><br>Thanks  and  Regards,<br> Team '.$enterprise_project.' <br><br>';	//crmv@22252
		if($to_fax != '')
		{
			$fax_status = send_fax('Users',$to_fax,$current_user->user_name,$from_fax,$subject,$description);
			$fax_status_str = $to_fax."=".$fax_status."&&&";
		}
		else
		{
			$fax_status_str = "'".$to_fax."'=0&&&";
		}
		$error_str = getFaxErrorString($fax_status_str);
		$action = 'FaxConfig';
		if($fax_status != 1){
			$action = 'FaxConfig&faxconfig_mode=edit&server_name='.$_REQUEST['server'].'&server_user='.$_REQUEST['server_username'].'&auth_check='.$_REQUEST['smtp_auth'].'&domain='.$_REQUEST['domain'].'&account='.$_REQUEST['account'].'&prefix='.$_REQUEST['prefix'].'&name='.$_REQUEST['name'];	
		}
	}
	if($db_update)
	{
		if($id=='') {
			$id = $adb->getUniqueID($table_prefix."_systems");
			$params_full = array(
				'id'=>$id, 
				'server'=>$server, 
				'server_port'=>$port, 
				'server_username'=>$server_username, 
				'server_password'=>$server_password, 
				'server_type'=>$server_type, 
				'smtp_auth'=>$smtp_auth,
				'service_type'=>$service_type,
				'domain'=>$domain,
				'account'=>$account);	
			$names = array_keys($params_full);			
			$sql="insert into ".$table_prefix."_systems (".implode(",",$names).") values(".generateQuestionMarks($names).")";
			$params = array_values($params_full);					
		}
		else {
			//crmv@43764
			if ($server_password == '') {
				$result = $adb->pquery("select server_password from {$table_prefix}_systems where id = ?",array($id));
				if ($result && $adb->num_rows($result) > 0) {
					$server_password = $adb->query_result($result,0,'server_password');
				}
			}
			//crmv@43764e
			$sql="update ".$table_prefix."_systems set server = ?, server_username = ?, server_password = ?, smtp_auth= ?, server_type = ?, server_port= ?, 
			service_type = ?, domain = ?, account = ?, prefix = ?, name = ? where id = ?";
			$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, 
			$service_type, $domain, $account, $prefix, $name, $id);
		}
		$adb->pquery($sql, $params);
	}
}
//crmv@7216e
//crmv@7217
if($server_type =='sms')
{
	require_once("modules/Sms/sms_.php");
	$service_type = $_REQUEST['service_type'];
	$domain = $_REQUEST['adv_domain'];
	$account = $_REQUEST['adv_account'];
	$prefix = $_REQUEST['adv_prefix'];
	$name = $_REQUEST['adv_name'];
	if ($service_type == 'sms_mail'){
			$to_sms = getUserSmsId('id',$current_user->id);
		$from_sms = $to_sms;
		$subject = 'Test sms about the sms server configuration.';
		$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test sms sent to confirm if a sms is actually being sent through the smtp server that you have configured. </b><br>Feel free to delete this mail.<br><br>Thanks  and  Regards,<br> Team '.$enterprise_project.' <br><br>';	//crmv@22252
		if($to_sms != '')
		{
			$sms_status = send_sms('Users',$to_sms,$current_user->user_name,$from_sms,$subject,$description);
			$sms_status_str = $to_sms."=".$sms_status."&&&";
		}
		else
		{
			$sms_status_str = "'".$to_sms."'=0&&&";
		}
		$error_str = getSmsErrorString($sms_status_str);
		$action = 'SmsConfig';
		if($sms_status != 1){
			$action = 'SmsConfig&smsconfig_mode=edit&server_name='.$_REQUEST['server'].'&server_user='.$_REQUEST['server_username'].'&auth_check='.$_REQUEST['smtp_auth'].'&domain='.$_REQUEST['domain'].'&account='.$_REQUEST['account'].'&prefix='.$_REQUEST['prefix'].'&name='.$_REQUEST['name'];	
		}
	}	
	if($db_update)
	{
		if($id=='') {
			$id = $adb->getUniqueID($table_prefix."_systems");
			$params_full = array(
				'id'=>$id, 
				'server'=>$server, 
				'server_port'=>$port, 
				'server_username'=>$server_username, 
				'server_password'=>$server_password, 
				'server_type'=>$server_type, 
				'smtp_auth'=>$smtp_auth,
				'service_type'=>$service_type,
				'domain'=>$domain,
				'account'=>$account,
				'prefix'=>$prefix,
				'name'=>$name);	
			$names = array_keys($params_full);			
			$sql="insert into ".$table_prefix."_systems (".implode(",",$names).") values(".generateQuestionMarks($names).")";
			$params = array_values($params_full);
		}
		else {
			//crmv@43764
			if ($server_password == '') {
				$result = $adb->pquery("select server_password from {$table_prefix}_systems where id = ?",array($id));
				if ($result && $adb->num_rows($result) > 0) {
					$server_password = $adb->query_result($result,0,'server_password');
				}
			}
			//crmv@43764e
			$sql="update ".$table_prefix."_systems set server = ?, server_username = ?, server_password = ?, smtp_auth= ?, server_type = ?, server_port= ?, 
			service_type = ?, domain = ?, account = ?, prefix = ?, name = ? where id = ?";
			$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, 
			$service_type, $domain, $account, $prefix, $name, $id);
		}
		$adb->pquery($sql, $params);
	}
}
//crmv@7217e
//Added code to send a test mail to the currently logged in user
if($server_type =='email')
{
	//crmv@16265	//crmv@32079
	//crmv@94084
	$action = 'EmailConfig';
	require_once('include/utils/VTEProperties.php');
	$VTEProperties = VTEProperties::getInstance();
	if ($VTEProperties->getProperty('smtp_editable') == '1') {
		require_once("modules/Emails/mail.php");
		global $current_user;
	
		$to_email = getUserEmailId('id',$current_user->id);
		$from_email = $to_email;
		$subject = 'Test mail about the mail server configuration.';
		$description = 'Dear '.$current_user->user_name.', <br><br><b> This is a test mail sent to confirm if a mail is actually being sent through the smtp server that you have configured. </b><br>Feel free to delete this mail.<br><br>Thanks  and  Regards,<br> Team '.$enterprise_project.' <br><br>';	//crmv@22252
		if($to_email != '') {
			$mail_status = send_mail('Users',$to_email,$current_user->user_name,$from_email,$subject,$description);
			$mail_status_str = $to_email."=".$mail_status."&&&";
		} else {
			$mail_status_str = "'".$to_email."'=0&&&";
		}
		$error_str = getMailErrorString($mail_status_str);
		
		if($mail_status != 1) {
			$action = 'EmailConfig&emailconfig_mode=edit&server_name='.$_REQUEST['server'].'&server_user='.$_REQUEST['server_username'].'&auth_check='.$_REQUEST['smtp_auth'].'&port='.$_REQUEST['port'].'&account_type=smtp&account_smtp='.$_REQUEST['account_smtp'];
		} else {
			if($db_update) {
				$account_smtp = $_REQUEST['account_smtp'];
				if ($account_smtp == '') {
					$sql="delete from {$table_prefix}_systems where server_type=?";
					$adb->pquery($sql, array($server_type));
				} elseif ($id == '') {
					$id = $adb->getUniqueID($table_prefix."_systems");
					$sql="insert into ".$table_prefix."_systems (id,server,server_port,server_username,server_password,server_type,smtp_auth,account) values(?,?,?,?,?,?,?,?)";
					$params = array($id, $server, $port, $server_username, $server_password, $server_type, $smtp_auth, $account_smtp);
					$res = $adb->pquery($sql, $params);
				} else {
					//crmv@43764
					if ($server_password == '') {
						$result = $adb->pquery("select server_password from {$table_prefix}_systems where id = ?",array($id));
						if ($result && $adb->num_rows($result) > 0) {
							$server_password = $adb->query_result($result,0,'server_password');
						}
					}
					//crmv@43764e
					$sql="update ".$table_prefix."_systems set server=?, server_username=?, server_password=?, smtp_auth=?, server_type=?, server_port=?, account=? where id=?";
					$params = array($server, $server_username, $server_password, $smtp_auth, $server_type, $port, $account_smtp, $id);
					$adb->pquery($sql, $params);
				}
			}
		}
	}
	//crmv@94084e
	
	$server_type = 'email_imap';
	//crmv@2963m
	$focusMessages = CRMEntity::getInstance('Messages');
	$old_servers = $focusMessages->getConfiguredAccounts();
	$saved_ids = array();
	$deleted = 0;
	for($i=0;!empty($_REQUEST['account_imap_'.$i]);$i++) {
		if ($_REQUEST['account_imap_deleted_'.$i] == '1') {
			$deleted++;
			continue;
		}
		if (array_key_exists($i,$old_servers)) {
			$id = $old_servers[$i]['account'];
			$sql = "update {$table_prefix}_systems set server=?,server_port=?,account=?,ssl_tls=?,domain=? where server_type=? and id=?";
			$params = array($_REQUEST['server_imap_'.$i], $_REQUEST['port_imap_'.$i], $_REQUEST['account_imap_'.$i], $_REQUEST['ssl_tls_imap_'.$i], $_REQUEST['domain_'.$i], $server_type, $id);
			$adb->pquery($sql, $params);
			if (!empty($old_server[$i]) && $old_server[$i] != $_REQUEST['server_imap_'.$i]) {
				/*
				 * TODO empty cache
				 * code of method emptyCache in rev 1020
				 */
				//$focusMessages = CRMEntity::getInstance('Messages');
				//$focusMessages->emptyCache();
			}
		} else {
			$id = $adb->getUniqueID($table_prefix."_systems");
			$sql = "insert into {$table_prefix}_systems (id,server,server_type,server_port,account,ssl_tls,domain) values (?,?,?,?,?,?,?)";
			$params = array($id, $_REQUEST['server_imap_'.$i], $server_type, $_REQUEST['port_imap_'.$i], $_REQUEST['account_imap_'.$i], $_REQUEST['ssl_tls_imap_'.$i], $_REQUEST['domain_'.$i]);
			$adb->pquery($sql, $params);
		}
		$saved_ids[] = $id;
	}
	if (!empty($saved_ids)) {
		$adb->pquery("delete from {$table_prefix}_systems where server_type=? and id not in (".generateQuestionMarks($saved_ids).")",array($server_type,$saved_ids));
	} elseif (empty($saved_ids) && $i > 0 && $i == $deleted) {
		$adb->pquery("delete from {$table_prefix}_systems where server_type=?",array($server_type));
	}
	//crmv@2963me
	//crmv@16265e	//crmv@32079e
}
//While configuring Proxy settings, the submitted values will be retained when exception is thrown - dina
if($server_type == 'proxy' && $error_str != '') {
	header("Location: index.php?module=Settings&parenttab=Settings&action=$action&server=$server&port=$port&server_username=$server_username&$error_str");
} else {
	header("Location: index.php?module=Settings&parenttab=Settings&action=$action&$error_str");
}
?>