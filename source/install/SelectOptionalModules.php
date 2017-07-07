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
$configFileUtils = new ConfigFile_Utils($_SESSION['config_file_info']);

if (!$configFileUtils->createConfigFile()) {
	die("<strong class='big'><font color='red'>{$installationStrings['ERR_CANNOT_WRITE_CONFIG_FILE']}</font></strong>");
}

require_once('include/utils/utils.php');  // Required - Especially to create adb instance in global scope.

$mode = $_REQUEST['mode'];
if($mode == 'migration') {
	$prev_file_name = 'SetMigrationConfig.php';
	$file_name = 'MigrationProcess.php';
	$optionalModules = Migration_Utils::getInstallableOptionalModules();
} else {
	$prev_file_name = 'SetInstallationConfig.php';
	$file_name = 'CreateTables.php';
	$optionalModules = Installation_Utils::getInstallableOptionalModules();
}
$selectedOptionalModuleNames = array();

$title = $enterprise_mode. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_OPTIONAL_MODULES'];
$sectionTitle = $installationStrings['LBL_OPTIONAL_MODULES'];

include_once "install/templates/overall/header.php";

?>
							
<div id="config" class="col-xs-12">
	<div id="config-inner" class="col-xs-12">
	
		<div class="spacer-20"></div>
		<strong class="big"><?php echo $installationStrings['MSG_CONFIG_FILE_CREATED']; ?>.</strong>
		<div class="spacer-30"></div>
		
		<div class="col-xs-12 nopadding">
			<table class="table">
				<?php if (count($optionalModules) > 0) {
					foreach ($optionalModules as $option => $modules) {
						if (count($modules) > 0) { ?>															
			    			<tr>
				    			<td colspan="3">
				    				<strong><?php echo $installationStrings['LBL_SELECT_OPTIONAL_MODULES_TO_'.$option]; ?> :</strong>
				    			</td>
			    			</tr>
							<?php foreach ($modules as $moduleName => $moduleDetails) { 
								$moduleDescription = $moduleDetails['description'];
								$moduleSelected = $moduleDetails['selected'];
								$moduleEnabled = $moduleDetails['enabled'];
								if ($moduleSelected == true) $selectedOptionalModuleNames[] = $moduleName;
							?>
							<tr>
								<td>
									<div class="checkbox">
										<label for="<?php echo $moduleName; ?>">
        									<input type="checkbox" id="<?php echo $moduleName; ?>" name="<?php echo $moduleName; ?>" value="<?php echo $moduleName; ?>" 
        									<?php if ($moduleSelected == true) echo "checked"; ?> 
        									<?php if ($moduleEnabled == false || $option == 'update') echo "disabled"; ?>
        									onChange='ModuleSelected("<?php echo $moduleName; ?>");' />&nbsp;
											<b><?php echo $moduleName; ?></b>
										</label>
									</div>
								</td>
								<td class="cell-vcenter"><i><?php echo $moduleDescription; ?></i></td>
							</tr>
							<?php
							}
						}
					} 
				} else {
				?>
				<tr><td>
					<div class="fixedSmallHeight textCenter fontBold">
						<div style="padding-top:50px;width:100%;">
							<span class="genHeaderBig"><?php echo $installationStrings['LBL_NO_OPTIONAL_MODULES_FOUND']; ?> !</span>
						</div>
					</div>
				</td></tr>
			<?php } ?>
			</table>
		</div>
	</div>
</div>
							
<div id="nav-bar" class="col-xs-12 nopadding">
	<div id="nav-bar-inner" class="col-xs-12">	
		<div class="col-xs-6 text-left">
			<form action="install.php" method="post" name="backform" id="backform">
				<input type="hidden" name="file" value="<?php echo $prev_file_name; ?>">
				<button class="crmbutton small edit btn-arrow-left"><?php echo $installationStrings['LBL_BACK']; ?></button>
			</form>
		</div>

		<div class="col-xs-6 text-right">
			<form action="install.php" method="post" name="form" id="form">
				<input type="hidden" value="<?php echo implode(":",$selectedOptionalModuleNames)?>" id='selected_modules' name='selected_modules' />  
				<input type="hidden" name="file" value="<?php echo $file_name; ?>" />
				<input type="hidden" name="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
				<button type="button" class="crmbutton small edit btn-arrow-right" onClick="VtigerJS_DialogBox.progress();submit();"><?php echo $installationStrings['LBL_NEXT']; ?></button>
			</form>
		</div>
	</div>
</div>
							
<script type="text/javascript">
	var selected_modules = '<?php echo implode(":",$selectedOptionalModuleNames)?>';
	
	function ModuleSelected(module){
		var moduleCheckbox = jQuery('#'+module);
		if(moduleCheckbox.prop("checked")){
			if(selected_modules == ''){
				selected_modules = selected_modules+moduleCheckbox.val();
			} else {
				selected_modules = selected_modules+":"+moduleCheckbox.val();
			}
		} else {
			if(selected_modules.indexOf(":"+module+":")>-1){
				selected_modules = selected_modules.replace(":"+module+":",":")
			} else if(selected_modules.indexOf(module+":")>-1){
				selected_modules = selected_modules.replace(module+":","")
			} else if(selected_modules.indexOf(":"+module)>-1){
				selected_modules = selected_modules.replace(":"+module,"")
			} else {
				selected_modules = selected_modules.replace(module,"")
			}
		}
		jQuery('#selected_modules').val(selected_modules);
	}
</script>

<?php include_once "install/templates/overall/footer.php"; ?>
