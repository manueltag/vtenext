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

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
$cookieurl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']) ?: '/';

session_set_cookie_params(0, $cookieurl, null, $isHttps, true);
session_start();

/** Function to  return a string with backslashes stripped off
 * @param $value -- value:: Type string
 * @returns $value -- value:: Type string array
 */
include_once('include/utils/utils.php');
require_once('Smarty_setup.php');
$smarty = new VTECRM_Smarty();

function stripslashes_checkstrings($value){
	if(is_string($value)){
		return stripslashes($value);
	}
	return $value;
}

if(get_magic_quotes_gpc() == 1){
	$_REQUEST = array_map("stripslashes_checkstrings", $_REQUEST);
	$_POST = array_map("stripslashes_checkstrings", $_POST);
	$_GET = array_map("stripslashes_checkstrings", $_GET);
}

include("include.php");
include("version.php");

if($_REQUEST['param'] == 'forgot_password')
{
	global $client;

	$email = $_REQUEST['email_id'];
	$params = array('email' => "$email");
	$result = $client->call('send_mail_for_password', $params);
	$_REQUEST['mail_send_message'] = $result;
	require_once("supportpage.php");
}
elseif($_REQUEST['logout'] == 'true')
{
	$customerid = $_SESSION['customer_id'];
	$sessionid = $_SESSION['customer_sessionid'];

	$params = Array(Array('id' => "$customerid", 'sessionid'=>"$sessionid", 'flag'=>"logout"));
	$result = $client->call('update_login_details', $params);

	unset($_SESSION['customer_id']);
	unset($_SESSION['customer_name']);
	unset($_SESSION['last_login']);
	unset($_SESSION['support_start_date']);
	unset($_SESSION['support_end_date']);
	unset($_SESSION['__permitted_modules']);
	unset($_SESSION['customer_account_id']);
	
	//crmv@remember_me
	setcookie('VTEPORTALLOGINID', false);
	setcookie('VTEPORTALLOGINHASH', false);
	// remove obsolete cookies
	setcookie('VTEPORTALLOGINID2', false);
	setcookie('VTEPORTALLOGINHASH2', false);
	//crmv@remember_me_e

	session_destroy();
	include("login.php");
}
else
{
	$module = '';
	$action = '';
	$isAjax = ($_REQUEST['ajax'] == 'true');
	
	//crmv@remember_me
	$VTEPORTALLOGINID = $_COOKIE['VTEPORTALLOGINID'];
	$VTEPORTALLOGINHASH = $_COOKIE['VTEPORTALLOGINHASH'];
	
	if (empty($_SESSION['customer_id'])) {
		if (!empty($VTEPORTALLOGINID) && !empty($VTEPORTALLOGINHASH)) {
			
			$params = array('id' => "$VTEPORTALLOGINID", 'user_hash' => $VTEPORTALLOGINHASH, 'step' => 'check');
			$result = $client->call('authenticate_user_cookie', $params, $Server_Path, $Server_Path);

			if($result && $result[0] && $result[1]) {
				$user_name = $result[0];
				$password = $result[1];
					
				$params = array(
					'user_name' => "$user_name",
					'user_password'=>"$password",
					'version' => "$version"
				);
				$result = $client->call('authenticate_user', $params, $Server_Path, $Server_Path);
				
				$_SESSION['customer_id'] = $result[0]['id'];
				$_SESSION['customer_sessionid'] = $result[0]['sessionid'];
				$_SESSION['customer_name'] = $result[0]['user_name'];
				$_SESSION['last_login'] = $result[0]['last_login_time'];
				$_SESSION['support_start_date'] = $result[0]['support_start_date'];
				$_SESSION['support_end_date'] = $result[0]['support_end_date'];
				$customerid = $_SESSION['customer_id'];
				$sessionid = $_SESSION['customer_sessionid'];
				
				$params1 = Array(Array('id' => "$customerid", 'sessionid'=>"$sessionid", 'flag'=>"login"));
				$result2 = $client->call('update_login_details', $params1, $Server_Path, $Server_Path);
				
				$params = array('customerid'=>$customerid);
				$permission = $client->call('get_modules',$params,$Server_path,$Server_path);
				$module = $permission[0];
					
				if($permission == '')
				{
					echo getTranslatedString('LBL_NO_PERMISSION_FOR_ANY_MODULE');
					exit;
				}
					
				// Store the permitted modules in session for re-use
				$_SESSION['__permitted_modules'] = $permission;
				
			} else {
				// wrong cookies, remove them
				setcookie('VTEPORTALLOGINID', false);
				setcookie('VTEPORTALLOGINHASH', false);
			}
		}
	}
	//crmv@remember_me_e

	if($_SESSION['customer_id'] != '')
	{
		$customerid = $_SESSION['customer_id'];
		$sessionid = $_SESSION['customer_sessionid'];

		//crmv@5946
		$block = 'Contacts';
		$params = array('id' => "$customerid", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid",'language'=>getPortalCurrentLanguage());	
		$result = $client->call('get_details', $params, $Server_Path, $Server_Path);

		if($result[0] == '#NOT AUTHORIZED#'){
			echo $result[0];
			die();
		}else{
			if(!empty($result)){
				$firstname = $result[0]['Contacts']['firstname']['fieldvalue'];
				$lastname = $result[0]['Contacts']['lastname']['fieldvalue'];
				$mailingcity = $result[0]['Contacts']['mailingcity']['fieldvalue'];
				$mailingstreet = $result[0]['Contacts']['mailingstreet']['fieldvalue'];
				
				$_SESSION['name_potentials'] = $firstname.'&nbsp;'.$lastname.',&nbsp;'.$mailingcity.'&nbsp;'.$mailingstreet;
			}
		}
		//crmv@5946e
		
		// Set customer account id
		if(isset($_SESSION['customer_account_id'])) {
			$account_id = $_SESSION['customer_account_id']; 
		} else {		
			$params = Array('id'=>$customerid);
			$account_id = $client->call('get_check_account_id', $params, $Server_Path, $Server_Path);
			$_SESSION['customer_account_id'] = $account_id;
		}
		// End
		$is_logged = 1;
		
		// Star HelpDesk
		if($_REQUEST['mode'] == 'saveTicketStars' && $_REQUEST['valutation_support'] != '0.00'){
			global $client;
		
			$field = 'valutation_support';
			$value = $_REQUEST['valutation_support'];
			$ticketid = $_REQUEST['ticketid'];
		
			$customerid = $_SESSION['customer_id'];
			$sessionid = $_SESSION['customer_sessionid'];
		
			$params = Array(Array(
					'id'=>"$customerid",
					'sessionid'=>"$sessionid",
					'ticketid'=>"$ticketid",
					'field'=>"$field",
					'value'=>"$value"
			));
		
			$record_result = $client->call('update_ticket', $params);
		}
		// Star HelpDesk end

		//Added to download attachments
		if($_REQUEST['downloadfile'] == 'true')
		{
			$filename = $_REQUEST['filename'];
			$fileType = $_REQUEST['filetype'];
			//$fileid = $_REQUEST['fileid'];
			$filesize = $_REQUEST['filesize'];

			//Added for enhancement from Rosa Weber

			if($_REQUEST['module'] == 'Invoice' || $_REQUEST['module'] == 'Quotes')
			{
				$id=$_REQUEST['id'];
				$block = $_REQUEST['module'];
				$params = array('id' => "$id", 'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
				$fileContent = $client->call('get_pdf', $params, $Server_Path, $Server_Path);
				$fileType ='application/pdf';
				$fileContent = $fileContent[0];
				$filesize = strlen(base64_decode($fileContent));
				$filename = "$block.pdf";

			}
			else if($_REQUEST['module'] == 'Documents')
			{
				$id=$_REQUEST['id'];
				$folderid = $_REQUEST['folderid'];
				$block = $_REQUEST['module'];
				$params = array('id' => "$id", 'folderid'=> "$folderid",'block'=>"$block", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
				$result = $client->call('get_filecontent_detail', $params, $Server_Path, $Server_Path);
				$fileType=$result[0]['filetype'];
				$filesize=$result[0]['filesize'];
				$filename=html_entity_decode($result[0]['filename']);
				$fileContent=$result[0]['filecontents'];
			}
			else
			{
				$ticketid = $_REQUEST['ticketid'];
				$fileid = $_REQUEST['fileid'];
				//we have to get the content by passing the customerid, fileid and filename
				$customerid = $_SESSION['customer_id'];
				$sessionid = $_SESSION['customer_sessionid'];
				$params = array(Array('id'=>$customerid,'fileid'=>$fileid,'filename'=>$filename,'sessionid'=>$sessionid,'ticketid'=>$ticketid));
				$fileContent = $client->call('get_filecontent', $params, $Server_Path, $Server_Path);
				$fileContent = $fileContent[0];
				$filesize = strlen(base64_decode($fileContent));

			}
			// : End

			//we have to get the content by passing the customerid, fileid and filename
			$customerid = $_SESSION['customer_id'];
			$sessionid = $_SESSION['customer_sessionid'];

			header("Content-type: $fileType");
			header("Content-length: $filesize");
			header("Cache-Control: private");
			header("Content-Disposition: attachment; filename=$filename");
			header("Content-Description: PHP Generated Data");
			echo base64_decode($fileContent);
			exit;
		}
		if($_REQUEST['module'] != '' && $_REQUEST['action'] != '')
		{
			$customerid = $_SESSION['customer_id'];
				
			$permission = array();
			// Look if we have the information already
			if(isset($_SESSION['__permitted_modules'])) {
				$permission = $_SESSION['__permitted_modules'];
			} else {
				// Get the information from server
				$params = array($customerid);
				$permission = $client->call('get_modules',$params,$Server_path,$Server_path);
				// Store for futher re-use
				$_SESSION['__permitted_modules'] = $permission;
			}			
			$isPermitted = false;
			for($i=0;$i<count($permission);$i++){
				if($permission[$i] == $_REQUEST['module']) {
					$isPermitted = true;
					break;
				}
			}
			if($isPermitted == true) {
				$module = $_REQUEST['module']."/";
				$action = $_REQUEST['action'].".php";
			}
		}
		elseif($_REQUEST['action'] != '' && $_REQUEST['module'] == '')
		{
			$action = $_REQUEST['action'].".php";
		}
		elseif($_REQUEST['action'] == '' && $_REQUEST['module'] != '')
		{ // crmv@81291
			$customerid = $_SESSION['customer_id'];
			
			$permission = array();
			// Look if we have the information already
			if(isset($_SESSION['__permitted_modules'])) {
				$permission = $_SESSION['__permitted_modules'];
			} else {
				// Get the information from server
				$params = array($customerid);
				$permission = $client->call('get_modules',$params,$Server_path,$Server_path);
				// Store for futher re-use
				$_SESSION['__permitted_modules'] = $permission;
			}
			$isPermitted = false;
			for($i=0;$i<count($permission);$i++){
				if($permission[$i] == $_REQUEST['module']) {
					$isPermitted = true;
					break;
				}
			}
			if($isPermitted == true) {
				$module = $_REQUEST['module']."/";
				$action = "index.php";
			}
		// crmv@81291e
		}
		elseif($_SESSION['customer_id'] != '')
		{
			$permission = array();
			// Look if we have the information already
			if(isset($_SESSION['__permitted_modules'])) {
				$permission = $_SESSION['__permitted_modules'];
			} else {
				// Get the information from server
				$params = array();
				$permission = $client->call('get_modules',$params,$Server_path,$Server_path);
				// Store for futher re-use
				$_SESSION['__permitted_modules'] = $permission;
			}
			$module = $permission[0];
			$action = "index.php";
		}
	}
	$filename = $module.$action;

	if($is_logged == 1)
	{
		include("HelpDesk/Utils.php");
		global $default_charset, $default_language;
		$default_language = getPortalCurrentLanguage();
		include("language/$default_language.lang.php");
		header('Content-Type: text/html; charset='.$default_charset);

		if(!$isAjax) {
			// header
// 			include("header.html");
			
			$showmodule = array();
			// Look if we have the information already
			if(isset($_SESSION['__permitted_modules'])) {
				$showmodule = $_SESSION['__permitted_modules'];
			} else {
				// Get the information from server
				$params = array();
				$showmodule = $client->call('get_modules',$params,$Server_path,$Server_path);
				// Store for further use.
				$_SESSION['__permitted_modules'] = $showmodule;
			}
			
			$showmodulemenu = array();
			
			for($i=0;$i<count($showmodule);$i++ ) {
				/*crmv@57342 crmv@5946*/
				if(file_exists($showmodule[$i]) && $showmodule[$i] != 'Potentials'){
					/*crmv@57342e crmv@5946e*/
					// Show module tab, only if the module directory exists
					$module_show = getTranslatedString($showmodule[$i]);
					$first_letter = strtoupper(substr($module_show,0,1));
					
					$class_css = 'slidemenu-vte';
					$class_css_label = "slidemenu-vte-label";
					
					$showmodulemenu[$i]['module'] = $showmodule[$i];
					$showmodulemenu[$i]['icon'] = strtolower($showmodule[$i]);
					$showmodulemenu[$i]['first_letter'] = $first_letter;
					if($showmodule[$i] == 'HelpDesk'){
						$class_css = 'slidemenu-vte-more';
						$class_css_label = "slidemenu-vte-label-more";
					}
					$showmodulemenu[$i]['class_css'] = $class_css;
					$showmodulemenu[$i]['class_css_label'] = $class_css_label;
				}	
			}
			
			$smarty->assign('fun',$_REQUEST['fun']);
			$smarty->assign('last_login',$_SESSION['last_login']);
			$smarty->assign('support_start_date',$_SESSION['support_start_date']);
			$smarty->assign('support_end_date',$_SESSION['support_end_date']);
			$smarty->assign('showmodulemenu',$showmodulemenu);
			
			$smarty->assign('customerid',$customerid);
			$smarty->display('header.tpl');
			
			// header end
		}
		if($isPermitted == false || ($module == '' && $action == '')){
			$notmodule= true;
			$smarty->display('NotAuthorized.tpl');
		}
		?>

		<?php
		// Hide non-permitted tabs if not Ajax Request		
		if(!$isAjax) {
			
			echo '<script type="text/javascript">';
		
			// Look if we have the information already
			$tabArray = array();
			if(isset($_SESSION['__permitted_modules'])) {
				$tabArray = $_SESSION['__permitted_modules'];
			} else {
				// Get the information from server
				$params = array();
				$tabArray = $client->call('get_modules',$params,$Server_path,$Server_path);
				// Store for futher re-use
				$_SESSION['__permitted_modules'] = $tabArray;
			}
			$module = $_REQUEST['module'];
			if($module != 'Contacts' && $_REQUEST['action'] != 'Welcome'){
				foreach($tabArray as $key => $tabName) {
					if(file_exists($tabName)) {
						if(strcmp(rtrim($module,"/"),$tabName) == 0) {
			?>
						document.getElementById("<?php echo $tabName;?>").className = "dvtSelectedCell";
			<?php
						}
						else {
			?>
						document.getElementById("<?php echo $tabName;?>").className = "dvtUnSelectedCell";
			<?php
						}
					}
				}
			}
			echo '</script>';
		}
		?>
		
		<?php
		if(is_file($filename)) {
			checkFileAccess($filename);			
			include($filename);
		} else if($_SESSION['customer_id'] != ''){
			$permission = array();
			// Look if we have the information already
			if(isset($_SESSION['__permitted_modules'])) {
				$permission = $_SESSION['__permitted_modules'];
				// Store for further re-use
				$_SESSION['__permitted_modules'] = $permission;
			} else {
				// Get the information from server
				$params = array();
				$permission = $client->call('get_modules',$params,$Server_path,$Server_path);
			}
			$module = $permission[0];
			
			checkFileAccess("$module/index.php");
			include("$module/index.php");
		}
		if(!$isAjax) {
			include("footer.html");
		}
	}
	else {
		header("Location: login.php");
	}

}
