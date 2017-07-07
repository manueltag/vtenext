<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
session_start();
$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";

$cur_dir_path = false;
if (is_file("config.php") && is_file("config.inc.php")) {
	require_once("config.inc.php");	
	$cur_dir_path = true;
	if(!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
		$cur_dir_path = false;
	}
} 

!isset($_SESSION['migration_info']['root_directory']) ? $root_directory = $current_dir : $root_directory = $_SESSION['migration_info']['root_directory'];
!isset($_SESSION['migration_info']['source_directory']) ? $source_directory = "" : $source_directory = $_SESSION['migration_info']['source_directory'];
!isset($_SESSION['migration_info']['user_name']) ? $user_name = "admin" : $user_name = $_SESSION['migration_info']['user_name'];
!isset($_SESSION['migration_info']['user_pwd']) ? $user_pwd = "" : $user_pwd = $_SESSION['migration_info']['user_pwd'];
!isset($_SESSION['migration_info']['new_dbname']) ? $new_dbname = "" : $new_dbname = $_SESSION['migration_info']['new_dbname'];

if(isset($_SESSION['migration_info']['old_version'])) {
	$old_version = $_SESSION['migration_info']['old_version'];
} elseif(isset($_SESSION['VTIGER_DB_VERSION'])) {
	$old_version = $_SESSION['VTIGER_DB_VERSION'];	
} else {
	$old_version = "";
}

include("modules/Migration/versions.php");
$version_sorted = $versions;
uasort($version_sorted,'version_compare');
$version_sorted = array_reverse($version_sorted,true);
$_SESSION['pre_migration'] = false;

$title = $installationStrings['LBL_VTIGER_CRM_5']. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_SYSTEM_CONFIGURATION'];
$sectionTitle = $installationStrings['LBL_SYSTEM_CONFIGURATION'];

include_once "install/templates/overall/header.php";

?>

<script type="text/javascript">
	function verify_data(form) {
		var isError = false;
		var errorMessage = "";
		// Here we decide whether to submit the form.
		if (trim(form.source_directory.value) =='') {
			isError = true;
			errorMessage += "\n <?php echo $installationStrings['LBL_PATH']; ?>";
			form.source_directory.focus();
		}
		if (trim(form.user_name.value) =='') {
			isError = true;
			errorMessage += "\n <?php echo $installationStrings['LBL_USERNAME']; ?>";
			form.user_name.focus();
		}
		if (trim(form.new_dbname.value) =='') {
			isError = true;
			errorMessage += "\n <?php echo $installationStrings['LBL_DATABASE_NAME']; ?>";
			form.new_dbname.focus();
		}
		if(form.old_version.value == ""){
			alert("<?php echo $installationStrings['LBL_SELECT_PREVIOUS_INSTALLATION_VERSION']; ?>");
			form.old_version.focus();
			return false;
		}		
		// Here we decide whether to submit the form.
		if (isError == true) {
			alert("<?php echo $installationStrings['LBL_MISSING_REQUIRED_FIELDS']; ?>:" + errorMessage);
			return false;
		}
		return true;
	}
</script>
		
<div id="config" class="col-xs-12">
	<div id="config-inner" class="col-xs-12 content-padding">
		<div class="col-xs-12 nopadding">
		
			<div class="col-xs-12 nopadding">
			
				<form action="install.php" method="post" name="installform" id="form">
					<input type="hidden" name="file" value="ConfirmMigrationConfig.php" />
			
					<div class="col-xs-12 col-md-6" style="padding-left:0px">
						<div class="col-xs-12 nopadding">
							<h3><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_INFORMATION']; ?></h3>
							<div class="spacer-20"></div>
							
							<div class="form-group">
								<label for="source_directory"><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_PATH']; ?> <sup><font color=red>*</font></sup></label>
								<div class="dvtCellInfo">
									<?php
									if($cur_dir_path == true){
										echo $root_directory;
									?>					
									<input  class="small" type="hidden" name="source_directory" id="source_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" /> 
									<?php } else { ?>					
									<input  class="detailedViewTextBox" type="text" name="source_directory" id="source_directory" value="<?php if (isset($source_directory)) echo "$source_directory"; ?>" /> 
									<?php } ?>	
									<input class="dataInput" type="hidden" name="root_directory" id="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" />
								</div>
							</div>
							
							<div class="form-group">
								<label for="old_version"><?php echo $installationStrings['LBL_PREVIOUS_INSTALLATION_VERSION']; ?> <sup><font color=red>*</font></sup></label>
								<div class="dvtCellInfo">
									<select class="detailedViewTextBox" name="old_version" id="old_version">
										<option value="" <?php if($old_version == "") echo "selected"; ?> >--SELECT--</option>
										<?php	
										foreach ($version_sorted as $index => $value) {
											if ($index == $old_version) echo "<option value='$index' selected>$value</option>";
											else echo "<option value='$index'>$value</option>";
										}
										?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="user_name">Admin <?php echo $installationStrings['LBL_USERNAME']; ?> <sup><font color=red>*</font></sup></label>
								<div class="dvtCellInfo">
									<input class="detailedViewTextBox" type="text" name="user_name" id="user_name" value="<?php if (isset($user_name)) echo $user_name; else echo 'admin';?>" />
								</div>
							</div>
							
							<div class="form-group">
								<label for="user_pwd">Admin <?php echo $installationStrings['LBL_PASSWORD']; ?> <sup><font color=red></font></sup></label>
								<div class="dvtCellInfo">
									<input class="detailedViewTextBox" type="password" name="user_pwd" id="user_pwd" value="<?php if (isset($user_pwd)) echo $user_pwd; else echo '';?>" />
								</div>
							</div>
							
							<div class="form-group">
								<label for="new_dbname"><?php echo $installationStrings['LBL_MIGRATION_DATABASE_NAME']; ?> <sup><font color=red>*</font></sup></label>
								<div class="dvtCellInfo">
									<input class="detailedViewTextBox" type="text" name="new_dbname" id="new_dbname" value="<?php if (isset($new_dbname)) echo $new_dbname; else echo '';?>" />
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-xs-12 col-md-6" style="padding-right:0px">
						<div class="col-xs-12 nopadding">
							<h3><?php echo $installationStrings['LBL_IMPORTANT_NOTE']; ?></h3>
							<div class="spacer-20"></div>
							<ul class="nopadding" style="list-style-type:none">
								<li><?php echo $installationStrings['MSG_TAKE_DB_BACKUP']; ?>.</li>
								<li><b><?php echo $installationStrings['QUESTION_MIGRATE_USING_NEW_DB']; ?></b>?<br>
									<ol style="padding:0;padding-left:15px;">
									<li><?php echo $installationStrings['MSG_CREATE_DB_WITH_UTF8_SUPPORT']; ?>.<br>
									<font class='fontBold'><?php echo $installationStrings['LBL_EG']; ?>:</font> CREATE DATABASE <newDatabaseName> DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;</li>
									<li><?php echo $installationStrings['MSG_COPY_DATA_FROM_OLD_DB']; ?>.</li>
									</ol>
								</li>
							</ul>
						</div>
					</div>
				</form>
			</div>
			
		</div>
	</div>
</div>

<div id="nav-bar" class="col-xs-12 nopadding">
	<div id="nav-bar-inner" class="col-xs-12">
		<div class="col-xs-6 text-left">
			<button type="button" class="crmbutton small edit btn-arrow-left" onClick="window.history.back();"><?php echo $installationStrings['LBL_BACK']; ?></button>
		</div>
		<div class="col-xs-6 text-right">
			<button type="button" class="crmbutton small edit btn-arrow-right" onClick="if(verify_data(installform) == true) document.installform.submit();"><?php echo $installationStrings['LBL_NEXT']; ?></button>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>
				
<?php include_once "install/templates/overall/footer.php"; ?>
