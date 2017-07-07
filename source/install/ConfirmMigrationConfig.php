<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
session_start();
		
if (isset($_REQUEST['source_directory'])) {
	$source_directory = $_REQUEST['source_directory'];
	if(!empty($source_directory)){
		$tmp = strlen($source_directory);
		if($source_directory[$tmp-1]!= "/" && $source_directory[$tmp-1]!= "\\"){
			$source_directory .= "/";
		}
		$_SESSION['migration_info']['source_directory'] = $source_directory;
	}
} else {
	$source_directory = $_SESSION['migration_info']['source_directory'];
}

if (isset($_REQUEST['root_directory'])) {
	$_SESSION['migration_info']['root_directory'] = $root_directory = $_REQUEST['root_directory'];
} else {
	$root_directory = $_SESSION['migration_info']['root_directory'];
}
if (isset($_REQUEST['user_name'])) { 
	$_SESSION['migration_info']['user_name'] = $user_name = $_REQUEST['user_name'];
} else {
	$user_name = $_SESSION['migration_info']['user_name'];
}
if (isset($_REQUEST['user_pwd'])) {
	$_SESSION['migration_info']['user_pwd'] = $user_pwd = $_REQUEST['user_pwd'];
} else {
	$user_pwd = $_SESSION['migration_info']['user_pwd'];
}
if (isset($_REQUEST['old_version'])) { 
	$_SESSION['migration_info']['old_version'] = $old_version = $_REQUEST['old_version'];
} else {
	$old_version = $_SESSION['migration_info']['old_version'];
}
if (isset($_REQUEST['new_dbname'])) { 
	$_SESSION['migration_info']['new_dbname'] = $new_dbname = $_REQUEST['new_dbname'];
} else {
	$new_dbname = $_SESSION['migration_info']['new_dbname'];
}

$dbVerifyResult = Migration_Utils::verifyMigrationInfo($_SESSION['migration_info']);
$next = $dbVerifyResult['flag'];
$error_msg = $dbVerifyResult['error_msg'];
$error_msg_info = $dbVerifyResult['error_msg_info'];

$oldDbName = $dbVerifyResult['old_dbname'];
$configFileInfo = $dbVerifyResult['config_info'];

$dbType = $configFileInfo['db_type'];
$dbHostName = $configFileInfo['db_hostname'];
$newDbName = $configFileInfo['db_name'];

if($next == true) {
	$_SESSION['authentication_key'] = md5(microtime());
	$_SESSION['config_file_info'] = $configFileInfo;

	require_once('install/VerifyDBHealth.php');

	if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true || $_SESSION['pre_migration'] != true) {
		header("Location:install.php?file=PreMigrationActions.php");
	} else {
		$innodbEngineCheck = true;
	}
	
	if($oldDbName == $newDbName && empty($_REQUEST['forceDbCheck'])) {
		header("Location:install.php?file=PreMigrationActions.php");
	}
}

include("modules/Migration/versions.php");

$title = $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_CONFIRM_SETTINGS'];
$sectionTitle = $installationStrings['LBL_CONFIRM_CONFIG_SETTINGS'];
$bigTitle = true;

include_once "install/templates/overall/header.php";

?>

<div class="col-xs-12">
	<table class="table borderless">
		<?php if($error_msg) : ?>
		<tr>
			<td align=left class="small" colspan=2 width=50% style="padding-left:10px">
				<div style="background-color:#ff0000;color:#ffffff;padding:5px">
					<b><?php echo $error_msg ?></b>
				</div>
				<?php if($error_msg_info) : ?>
					<p><?php echo $error_msg_info ?><p>
				<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>
	</table>
</div>

<div id="config" class="col-xs-12">
	<div id="config-inner" class="col-xs-12">
		<div class="col-xs-12 nopadding">
			<div class="col-xs-12 nopadding">
			
				<div class="col-xs-12 col-md-6" style="padding-left:0px">
					<div class="col-xs-12 nopadding">
						<h3><?php echo $installationStrings['LBL_DATABASE_CONFIGURATION']; ?></h3>
						<div class="spacer-20"></div>
						
						<table class="table">
							<tr>
								<td nowrap><?php echo $installationStrings['LBL_DATABASE_TYPE']; ?></td>
								<td nowrap align="left"> <font class="dataInput"><i><?php echo $dbType; ?></i></font></td>
							</tr>
							<tr>
								<td nowrap><?php echo $installationStrings['LBL_OLD']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?></td>
									<td nowrap align="left"> <font class="dataInput"><i><?php echo $oldDbName; ?></i></font></td>
								</tr>
							<tr>
								<td nowrap><?php echo $installationStrings['LBL_NEW']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?></td>
								<td nowrap align="left"> <font class="dataInput"><?php echo $newDbName; ?></font></td>
							</tr>
							<tr>
								<td nowrap><?php echo $installationStrings['LBL_INNODB_ENGINE_CHECK']; ?></td>
								<td nowrap align="left">
									<?php if ($innodbEngineCheck == 1) { ?>
									<font class="dataInput"><?php echo $installationStrings['LBL_FIXED']; ?></font>
									<?php } else { ?>
									<font class="dataInput"><span class="redColor"><?php echo $installationStrings['LBL_NOT_FIXED']; ?></span></font></td>
									<?php } ?>
							</tr>
						</table>
					</div>
				</div>
			
				<div class="col-xs-12 col-md-6" style="padding-right:0px">
					<div class="col-xs-12 nopadding">
						<h3><?php echo $installationStrings['LBL_SOURCE_CONFIGURATION']; ?></h3>
						<div class="spacer-20"></div>
						
						<table class="table">
							<tr>
								<td><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_VERSION']; ?></td>
								<td align="left"> <i><?php echo $versions[$old_version]; ?></i></td>
							</tr>
							<tr>
								<td><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_PATH']; ?></td>
								<td align="left"> <i><?php echo $source_directory; ?></i></td>
							</tr>
							<tr>
								<td><?php echo $installationStrings['LBL_NEW_INSTALLATION_PATH']; ?></td>
								<td align="left"> <i><?php echo $root_directory; ?></i></td>
							</tr>
							<tr>
								<td>Admin <?php echo $installationStrings['LBL_USER_NAME']; ?></td>
								<td align="left"> <i><?php echo $user_name; ?></i></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>

<div id="nav-bar" class="col-xs-12 nopadding">
	<div id="nav-bar-inner" class="col-xs-12">
		<div class="col-xs-6 text-left">
			<form action="install.php" method="post" name="form" id="form">
				<input type="hidden" name="file" value="SetMigrationConfig.php">
				<button class="crmbutton small edit btn-arrow-left"><?php echo $installationStrings['LBL_CHANGE']; ?></button>
			</form>
		</div>
		<div class="col-xs-6 text-right">
			<?php if($next) : ?>
			<form action="install.php" method="post" name="form" id="form">
				<input type="hidden" name="mode" value="migration">
				<input type="hidden" name="file" value="SelectOptionalModules.php">
				<button class="crmbutton small edit btn-arrow-right"><?php echo $installationStrings['LBL_NEXT'] ?></button>
			</form>
			<?php endif ?>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
				
<?php include_once "install/templates/overall/footer.php"; ?>
