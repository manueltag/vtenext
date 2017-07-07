<?php
//crmv@27589
include_once('../../config.inc.php');
chdir($root_directory);

// crmv@91979
require_once('include/MaintenanceMode.php');
if (MaintenanceMode::check()) {
	MaintenanceMode::display();
	die();
}
// crmv@91979e

// crmv@105588

require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserAuthtoken.php');
require_once('include/Zend/Json.php');
global $adb, $site_URL, $default_language, $default_charset, $table_prefix, $default_theme, $theme;
$current_language = $default_language;
$theme = $default_theme;
header('Content-Type: text/html; charset='. $default_charset);
$smarty = new vtigerCRM_Smarty;
$smarty->assign('PATH','../../');
$user_auth_token_type = 'password_recovery';
$user_auth_seconds_to_expire = 60*60*24;	//1 day in seconds

if ($_REQUEST['action'] == 'change_password') {
	$user_id = validateUserAuthtokenKey($user_auth_token_type,$_POST['key']);
	if ($user_id !== false) {
		$current_user = CRMEntity::getInstance('Users');
		$current_user->id = $user_id;
		$current_user->retrieve_entity_info($current_user->id,'Users');

		//crmv@28327
		if (!$current_user->checkPasswordCriteria($_REQUEST['confirm_new_password'],$current_user->column_fields)) {
			$description = '
			<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
			<tr><td colspan="2">'.sprintf(getTranslatedString('LBL_NOT_SAFETY_PASSWORD','Users'),$current_user->password_length_min).'</td></tr>
			<tr height="25px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="right">
				<input type="button" class="crmbutton small cancel" value="'.getTranslatedString('LBL_BACK','APP_STRINGS').'" onclick="history.back();" />
				<input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" />
			</td></tr>
			</table>
			';
		//crmv@35153
		} elseif ($current_user->id == 1 && isFreeVersion()) {
			$result = $adb->query("SELECT hash_version FROM ".$table_prefix."_version");
		    $_SESSION['vtiger_hash_version'] = Users::m_encryption(Users::de_cryption($adb->query_result_no_html($result, 0, 'hash_version')));
			$focusMorphsuit = CRMEntity::getInstance("Morphsuit");
			$description = '
			<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
			<tr><td colspan="2" id="change_password_message"></td></tr>
			<tr height="25px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="right"><input id="change_password_button" type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
			</table>
			<script>
			var url = "'.$focusMorphsuit->vteFreeServer.'";
			var params = {
				"method" : "updateSiteCredentials",
				"username" : "'.$focusMorphsuit->morph_par($current_user->column_fields['user_name']).'",
				"password" : "'.$focusMorphsuit->morph_par($_POST['new_password']).'"
			};
			jQuery.ajax({
				url : url,
				type: "POST",
				data: params,
				async: false,
				complete  : function(res, status) {
					if (status != "success") {
						var message = "Connection with VTECRM Network failed ("+status+")";
					} else if (res.responseText == true) {
						var message = "'.getTranslatedString('LBL_RECOVERY_PASSWORD_SAVED','Users').'";
						jQuery("#change_password_button").show();
					}
					jQuery("#change_password_message").html(message);
				}
			});
			</script>
			';
			emptyUserAuthtokenKey($user_auth_token_type,$user_id);
		//crmv@35153e
		} else {
			$current_user->change_password('oldpwd', $_POST['confirm_new_password'], true, true);
			emptyUserAuthtokenKey($user_auth_token_type,$user_id);
			$description = '
			<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
			<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_PASSWORD_SAVED','Users').'</td></tr>
			<tr height="25px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
			</table>
			';
		}
		//crmv@28327e
	} else {
		$description = '
		<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
		<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_SESSION_EXPIRED','Users').'</td></tr>
		<tr height="25px"><td colspan="2"></td></tr>
		<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
		</table>
		';
	}
} elseif ($_REQUEST['action'] == 'recover') {
	$user_id = validateUserAuthtokenKey($user_auth_token_type,$_REQUEST['key']);
	if ($user_id !== false) {
		$current_user = CRMEntity::getInstance('Users');
		$current_user->id = $user_id;
		$current_user->retrieve_entity_info($current_user->id, 'Users');

		$login_link = "<a href='$site_URL'>" . getTranslatedString('LBL_HERE', 'Calendar') . "</a>";
		$description = '
		<form action="Recover.php" onsubmit="VtigerJS_DialogBox.block();" name="ChangePassword" method="POST">
		<input type="hidden" name="action" value="change_password">
		<input type="hidden" name="key" value="'.$_REQUEST['key'].'">
		
		<table class="table borderless">
		<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_SYSTEM1','Users').' <b>'.$current_user->column_fields['user_name'].'</b> '.getTranslatedString('LBL_RECOVERY_SYSTEM2','Users').' '.$login_link.' '.getTranslatedString('LBL_RECOVERY_SYSTEM3','Users').'</td></tr>
		<tr>
			<td>
				<div style="width:50%" class="text-left">
						<label for="new_password">'.getTranslatedString('LBL_NEW_PASSWORD','Users').'</label>
						<div class="dvtCellInfo">
								<input class="detailedViewTextBox" type="password" id="new_password" name="new_password">
						</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:50%" class="text-left">
					<label for="confirm_new_password">'.getTranslatedString('LBL_CONFIRM_PASSWORD','Users').'</label>
					<div class="dvtCellInfo">
							<input class="detailedViewTextBox" type="password" id="confirm_new_password" name="confirm_new_password">
					</div>
				</div>
			</td>
		</tr>
		<tr height="45px"><td colspan="2"></td></tr>
		<tr height="25px"><td colspan="2" align="center">
			<input type="submit" class="crmbutton small save" value="'.getTranslatedString('LBL_SAVE_BUTTON_LABEL','APP_STRINGS').'" onclick="return set_password(this.form);" />
		</td></tr>
		</table>
		</form>
		<script>
		function set_password(form) {
			if (trim(form.new_password.value) == "") {
				alert("'.getTranslatedString('ERR_ENTER_NEW_PASSWORD','Users').'");
				return false;
			}
			if (trim(form.confirm_new_password.value) == "") {
				alert("'.getTranslatedString('ERR_ENTER_CONFIRMATION_PASSWORD','Users').'");
				return false;
			}
			if (trim(form.new_password.value) == trim(form.confirm_new_password.value)) {
				form.submit();
				return true;
			}
			else {
				alert("'.getTranslatedString('ERR_REENTER_PASSWORDS','Users').'");
				return false;
			}
		}
		</script>';
	} else {
		$description = '
		<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
		<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_SESSION_EXPIRED','Users').'</td></tr>
		<tr height="25px"><td colspan="2"></td></tr>
		<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
		</table>
		';
	}
} elseif ($_REQUEST['action'] == 'send') {
	if (isset($_POST['user_name']) && $_POST['user_name'] != '') {
		$current_user = CRMEntity::getInstance('Users');
		$current_user->id = $current_user->retrieve_user_id($_POST['user_name']);
		$current_user->retrieve_entity_info($current_user->id, 'Users');
		$current_language = $current_user->column_fields['default_language'];
		if ($current_user->column_fields['email1'] != '') {
			require_once('modules/Emails/mail.php');
			global $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID;
			if (empty($HELPDESK_SUPPORT_EMAIL_ID) || $HELPDESK_SUPPORT_EMAIL_ID == 'admin@vte123abc987.com') {
				$result = $adb->query("select email1 from {$table_prefix}_users where id = 1");
				$HELPDESK_SUPPORT_EMAIL_ID = $adb->query_result($result,0,'email1');
			}
			$subject = getTranslatedString('LBL_RECOVER_EMAIL_SUBJECT','Users');
			$key = getUserAuthtokenKey($user_auth_token_type,$current_user->id,$user_auth_seconds_to_expire);
			$mail_error = true;
			if ($key !== false) {
				$mail_error = false;
				$link = "<a href='$site_URL/modules/Users/Recover.php?action=recover&key=$key'>".getTranslatedString('LBL_HERE','APP_STRINGS')."</a>";
				$body = getTranslatedString('Dear','HelpDesk').' '.$_POST['user_name'].',<br><br>';
				$body .= getTranslatedString('LBL_RECOVER_EMAIL_BODY1','Users').' '.$link.' '.getTranslatedString('LBL_RECOVER_EMAIL_BODY2','Users');
				$body .= '<br><br>'.getTranslatedString("LBL_REGARDS",'HelpDesk').',<br>'.getTranslatedString("LBL_TEAM",'HelpDesk');

				$server = '';
				$res = $adb->pquery("select * from {$table_prefix}_systems where server_type=?", array('email'));
				if ($res && $adb->num_rows($res) > 0) {
					$server = $adb->query_result($res,0,'server');
				}
				if ($server == '') {
					$domains = array(
						$_SERVER['SERVER_NAME'],
						substr($current_user->column_fields['email1'],strpos($current_user->column_fields['email1'],'@')+1)
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
									$mail_status = send_mail('Users',$current_user->column_fields['email1'],$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$body);
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
				} else {
					$mail_status = send_mail('Users',$current_user->column_fields['email1'],$HELPDESK_SUPPORT_NAME,$HELPDESK_SUPPORT_EMAIL_ID,$subject,$body);
					if($mail_status != 1) {
						$mail_error = true;
					}
				}
			}
			if ($mail_error) {
				$description = '
				<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
				<tr><td colspan="2">'.getTranslatedString('LBL_RECOVER_MAIL_ERROR','Users').'</td></tr>
				<tr height="25px"><td colspan="2"></td></tr>
				<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
				</table>
				';
			} else {
				$description = '
				<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
				<tr><td colspan="2">'.getTranslatedString('LBL_RECOVER_MAIL_SENT','Users').'</td></tr>
				<tr height="25px"><td colspan="2"></td></tr>
				<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
				</table>
				';
			}
		}
	}
// crmv@43592
} elseif ($_REQUEST['action'] == 'change_old_pwd') {
	$user_id = validateUserAuthtokenKey($user_auth_token_type,$_REQUEST['key']);
	if ($user_id > 0) {
		$current_user = CRMEntity::getInstance('Users');
		$current_user->id = $user_id;
		$description = '
		<form action="Recover.php" onsubmit="VtigerJS_DialogBox.block();" name="ChangeOldPassword" method="POST">
			<input type="hidden" name="action" value="change_old_pwd_send">
			<input type="hidden" name="key" value="'.$_REQUEST['key'].'">
			<table class="table borderless">
			<tr><td colspan="2">'.sprintf(getTranslatedString('LBL_PASSWORD_TO_BE_CHANGED','Users'), $current_user->time_to_change_password).'<br>'.getTranslatedString('LBL_USE_FIELDS_TO_CHANGE_PWD', 'Users').'.</td></tr>
			<tr>
				<td>
					<div style="width:50%" class="text-left">
						<label for="old_password">'.getTranslatedString('LBL_OLD_PASSWORD','Users').'</label>
						<div class="dvtCellInfo">
								<input class="detailedViewTextBox" type="password" id="old_password" name="old_password">
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="width:50%" class="text-left">
						<label for="new_password">'.getTranslatedString('LBL_NEW_PASSWORD','Users').'</label>
						<div class="dvtCellInfo">
								<input class="detailedViewTextBox" type="password" id="new_password" name="new_password">
						</div>
					</div>
				</td>	
			</tr>
			<tr>
				<td>
					<div style="width:50%" class="text-left">
						<label for="confirm_new_password">'.getTranslatedString('LBL_CONFIRM_PASSWORD','Users').'</label>
						<div class="dvtCellInfo">
								<input class="detailedViewTextBox" type="password" id="confirm_new_password" name="confirm_new_password">
						</div>
					</div>
				</td>
			</tr>
			<tr style="height:45px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="center">
				<input type="submit" class="crmbutton small save" value="'.getTranslatedString('LBL_SAVE_BUTTON_LABEL','APP_STRINGS').'" onclick="return set_password(this.form);" />
			</td></tr>
			</table>
		</form>
		<script type="text/javascript">
			function set_password(form) {
				if (trim(form.old_password.value) == "") {
					alert("'.getTranslatedString('ERR_ENTER_OLD_PASSWORD','Users').'");
					return false;
				}
				if (trim(form.new_password.value) == "") {
					alert("'.getTranslatedString('ERR_ENTER_NEW_PASSWORD','Users').'");
					return false;
				}
				if (trim(form.confirm_new_password.value) == "") {
					alert("'.getTranslatedString('ERR_ENTER_CONFIRMATION_PASSWORD','Users').'");
					return false;
				}
				if (trim(form.new_password.value) == trim(form.confirm_new_password.value)) {
					form.submit();
					return true;
				} else {
					alert("'.getTranslatedString('ERR_REENTER_PASSWORDS','Users').'");
					return false;
				}
			}
		</script>';
	} else {
		$description = '
		<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
			<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_SESSION_EXPIRED','Users').'</td></tr>
			<tr height="25px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
		</table>';
	}
} elseif ($_REQUEST['action'] == 'change_old_pwd_send') {
	$user_id = validateUserAuthtokenKey($user_auth_token_type,$_POST['key']);
	if ($user_id > 0) {
		$current_user = CRMEntity::getInstance('Users');
		$current_user->id = $user_id;
		$current_user->retrieve_entity_info($current_user->id, 'Users');

		// first check the old password
		if (!$current_user->doLogin($_POST['old_password'])) {
			$description = getTranslatedString('ERR_PASSWORD_INCORRECT_OLD', 'Users');
			$description .= '<br><a href="javascript:history.back()">'.getTranslatedString('LBL_BACK').'</a>';
		} elseif (!$current_user->checkPasswordCriteria($_POST['new_password'],$current_user->column_fields)) {
			$description = sprintf(getTranslatedString('LBL_NOT_SAFETY_PASSWORD','Users'),$current_user->password_length_min);
			$description .= '<br><a href="javascript:history.back()">'.getTranslatedString('LBL_BACK').'</a>';
		} else {
			$r = $current_user->change_password($_POST['old_password'], $_POST['new_password']);
			if ($r === false) {
				$description = 'Unknown error while updating password';
				$description .= '<br><a href="javascript:history.back()">'.getTranslatedString('LBL_BACK').'</a>';
			} else {
				// authenticate and redirect
				$description = '
				<p>'.getTranslatedString('LBL_PASSWORD_CHANGED', 'Users').'</p>
				<p>'.getTranslatedString('LBL_WAIT_FOR_LOGIN', 'Users').'</p>
				<form method="POST" action="'.$site_URL.'/index.php" name="autoLoginForm">
					<input type="hidden" name="module" value="Users">
					<input type="hidden" name="action" value="Authenticate">
					<input type="hidden" name="return_module" value="Users">
					<input type="hidden" name="return_action" value="Login">
					<input type="hidden" name="user_name" value="'.$current_user->column_fields['user_name'].'">
					<input type="hidden" name="user_password" value="'.$_POST['new_password'].'">
					<input type="submit" value="Login">
				</form>
				<script type="text/javascript">
					setTimeout(function() {
						document.autoLoginForm.submit();
					}, 2000);
				</script>';
			}
		}

	} else {
		$description = '
		<table border="0" cellpadding="5" cellspacing="0" width="100%" align="center" class="small">
			<tr><td colspan="2">'.getTranslatedString('LBL_RECOVERY_SESSION_EXPIRED','Users').'</td></tr>
			<tr height="25px"><td colspan="2"></td></tr>
			<tr height="25px"><td colspan="2" align="right"><input type="button" class="crmbutton small edit" value="'.getTranslatedString('LBL_SIGN_IN','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" /></td></tr>
		</table>';
	}
// crmv@43592e
} else {
	$description = '<form action="Recover.php" onSubmit="if(checkRecoverForm()){ VtigerJS_DialogBox.block(); } else { return false; }" method="POST">
		<input type="hidden" name="action" value="send">
		<table class="table borderless">
			<tr><td colspan="2">'.getTranslatedString('LBL_RECOVER_INTRO','Users').'</td></tr>
			<tr>
				<td align="left">
					<div style="width:50%" class="text-left">
						<label for="user_name">'.getTranslatedString('LBL_USER_NAME','Users').'</label>
						<div class="dvtCellInfo">
							<input class="detailedViewTextBox" type="text" id="user_name" name="user_name" value="">		
						</div>
					</div>
				</td>
			</tr>
			<tr style="height:45px"><td colspan="2"></td></tr>
			<tr height="25px">
				<td colspan="2" align="center">
					<input type="button" class="crmbutton small cancel" value="'.getTranslatedString('LBL_BACK','APP_STRINGS').'" onclick="location.href=\''.$site_URL.'\'" />
					<input type="submit" class="crmbutton small save" value="'.getTranslatedString('LBL_SEND','APP_STRINGS').'" />
				</td>
			</tr>
		</table>
	</form>
	<script>
		function checkRecoverForm() {
			if (!emptyCheck("user_name","'.getTranslatedString('LBL_USER_NAME','Users').'",getObj("user_name").type))
				return false;
			return true;
		}
	</script>';
}

$smarty->assign('THEME', $default_theme);
$smarty->assign('CURRENT_LANGUAGE', $current_language);
$smarty->assign('TITLE', getTranslatedString('LBL_RECOVER_EMAIL_SUBJECT', 'Users'));
$smarty->assign('BODY', $description);
$smarty->display('Recover.tpl');
//crmv@27589e

?>
