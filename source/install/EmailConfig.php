<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/
/* crmv@130421 */

include('../include/install/language/en_us.lang.php');

if (!empty($_REQUEST['calculate_account'])) {
	$default_account = array(
		'smtp' => array(
			'Gmail' => array(
				'server'=>'ssl://smtp.gmail.com',
				'server_port'=>'465',
				'server_username'=>'username@gmail.com',
				'server_password'=>'',
				'smtp_auth'=>'checked',
			),
			'Hotmail' => array(
				'server'=>'smtp.live.com',
				'server_port'=>'587',
				'server_username'=>'username@hotmail.com',
				'server_password'=>'',
				'smtp_auth'=>'checked',
			),
			'Yahoo!' => array(
				'server'=>'smtp.mail.yahoo.com',
				'server_port'=>'25',
				'server_username'=>'username@yahoo.com',
				'server_password'=>'',
				'smtp_auth'=>'checked',
				'note'=>'Only the sender or other authorized by yahoo can send emails. (See "553" on http://help.yahoo.com for more information)',
			),
			'Exchange' => array(
				'server'=>'mail.example.com',
				'server_port'=>'25',
				'server_username'=>'username@example.com',
				'server_password'=>'',
				'smtp_auth'=>'checked',
			),
			'Other' => array(
				'server'=>'smtp.example.com',
				'server_port'=>'25',
				'server_username'=>'',
				'smtp_auth'=>'',
			),
		),
		'imap' => array(
			'Gmail' => array(
				'server'=>'imap.gmail.com',
				'server_port'=>'993',
				'ssl_tls'=>'ssl',
			),
			'Yahoo!' => array(
				'server'=>'imap-ssl.mail.yahoo.com',
				'server_port'=>'993',
				'ssl_tls'=>'ssl',
			),
			'Exchange' => array(
				'server'=>'mail.example.com',
				'server_port'=>'993',
				'ssl_tls'=>'tls',
				'domain'=>'example.com',
			),
			'Other' => array(
				'server'=>'imap.example.com',
				'server_port'=>'143',
				'ssl_tls'=>'',
			),
		),
	);
	$default = $default_account[$_REQUEST['account_type']][$_REQUEST['calculate_account']];
	?>
	<?php if ($default['note'] != '') { ?>
		<div class="form-group">
			<span class="helpmessagebox" style="font-style: italic;"><?php echo $default['note']; ?></span>
		</div>
	<?php } ?>
	<div class="form-group">
		<label for="smtp_server"><?php echo $installationStrings['LBL_OUTGOING_MAIL_SERVER']; ?></label>
		<div class="dvtCellInfo">
			<input class="detailedViewTextBox" id="smtp_server" name="server" value="<?php echo $default['server'];?>" type="text">
		</div>
	</div>
	<div class="form-group">
		<label for="smtp_port"><?php echo $installationStrings['LBL_OUTGOING_MAIL_PORT']; ?></label>
		<div class="dvtCellInfo">
			<input class="detailedViewTextBox" id="smtp_port" name="port" value="<?php echo $default['server_port'];?>" type="text">
		</div>
	</div>
	<div class="form-group">
		<label for="smtp_username"><?php echo $installationStrings['LBL_USERNAME']; ?></label>
		<div class="dvtCellInfo">
			<input class="detailedViewTextBox" id="smtp_username" name="server_username" value="<?php echo $default['server_username'];?>" type="text">
		</div>
	</div>
	<div class="form-group">
		<label for="smtp_password"><?php echo $installationStrings['LBL_PASWRD']; ?></label>
		<div class="dvtCellInfo">
			<input type="password" id="smtp_password" name="server_password" value="<?php echo $default['server_password']; ?>" class="detailedViewTextBox small" />
		</div>
	</div>
	<div class="form-group">
		<div>
			<label for="smtp_auth">
				<input type="checkbox" id="smtp_auth" name="smtp_auth" <?php echo $default['smtp_auth']; ?> />&nbsp;
				<b><?php echo $installationStrings['LBL_REQUIRES_AUTHENT']; ?></b>
			</label>
		</div>
	</div>
<?php } ?>