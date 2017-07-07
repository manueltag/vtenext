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

require_once('install/VerifyDBHealth.php');

$migrationInfo = $_SESSION['migration_info'];
$source_directory = $migrationInfo['source_directory'];
require_once($source_directory.'config.inc.php');
$dbHostName = $dbconfig['db_hostname']; 
$dbName = $dbconfig['db_name'];

$newDbForCopy = $newDbName = $migrationInfo['new_dbname'];
if($dbName == $newDbForCopy) {
	$newDbForCopy = '';
}
$_SESSION['pre_migration'] = true;

$title = $enterprise_mode. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_CONFIRM_SETTINGS'];
$sectionTitle = $installationStrings['LBL_CONFIRM_CONFIG_SETTINGS'];

include_once "install/templates/overall/header.php";

?>

<script type="text/javascript">
	jQuery.noConflict();
	function fixDBHealth(){
		VtigerJS_DialogBox.progress();
		var value = jQuery('#auth_key').attr('value');
		var url = 'install.php?file=VerifyDBHealth.php&ajax=true&updateTableEngine=true&updateEngineForAllTables=true&auth_key='+value;
		jQuery.post(url,function(data,status){
			fnvshNrm('responsePopupContainer');
			jQuery('#responsePopupContainer').show();
			var element = jQuery('#responsePopup');
			if(status == 'success'){
				if(trim(data) == 'TABLE_TYPE_FIXED'){
					element.attr('innerHTML', '<?php echo $installationStrings['MSG_SUCCESSFULLY_FIXED_TABLE_TYPES']; ?>');
					jQuery('#databaseFixMessageDiv').hide();
				} else {
					element.attr('innerHTML', '<?php echo $installationStrings['ERR_FAILED_TO_FIX_TABLE_TYPES']; ?>');
				}
			}else{
				element.attr('innerHTML', '<?php echo $installationStrings['ERR_FAILED_TO_FIX_TABLE_TYPES']; ?>');
			}
			jQuery('#dbMirrorCopy').hide();
			VtigerJS_DialogBox.hideprogress();
			placeAtCenter(document.getElementById('responsePopupContainer'));
		});			
	}
	
	function viewDBReport(){
		var value = jQuery('#auth_key').attr('value');
		var url = 'install.php?file=VerifyDBHealth.php&ajax=true&viewDBReport=true&auth_key='+value;
		window.open(url,'DBHealthCheck', 'width=700px, height=500px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');			
	}
	
	function getDbDump(){
		var value = jQuery('#auth_key').attr('value');
		var url = 'install.php?file=MigrationDbBackup.php&mode=dump&auth_key='+value;
		window.open(url,'DatabaseDump', 'width=800px, height=600px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');
	}
	
	function doDBCopy(){
		var dbName = jQuery('#newDatabaseName').val();
		if (trim(dbName) == '') {
			alert("<?php echo $installationStrings['ERR_SPECIFY_NEW_DATABASE_NAME']; ?>");
			jQuery('#newDatabaseName').focus();
			return false;
		}
		var rootUserName = jQuery('#rootUserName').val();
		if (trim(rootUserName) == '') {
			alert("<?php echo $installationStrings['ERR_SPECIFY_ROOT_USER_NAME']; ?>");
			jQuery('#rootUserName').focus();
			return false;
		}
		VtigerJS_DialogBox.progress();
		var rootPassword = jQuery('#rootPassword').val();			
		var value = jQuery('#auth_key').attr('value');
		var url = 'install.php?file=MigrationDbBackup.php&mode=copy&auth_key='+value;
		url += ('&newDatabaseName='+dbName+'&rootUserName='+rootUserName+'&rootPassword='+rootPassword+'&createDB=true');
		jQuery.post(url,function(data,status){
			fnvshNrm('responsePopupContainer');
			jQuery('#responsePopupContainer').show();
			var element = jQuery('#responsePopup');
			if(status == 'success'){
				if(data != 'true' && data != true){
					element.attr('innerHTML', '<?php echo $installationStrings['ERR_DATABASE_COPY_FAILED']; ?>.');
				}else{
					element.attr('innerHTML', '<?php echo $installationStrings['MSG_DATABASE_COPY_SUCCEDED']; ?>');
				}
			}else{
				element.attr('innerHTML', '<?php echo $installationStrings['ERR_DATABASE_COPY_FAILED']; ?>.');
			}
			jQuery('#dbMirrorCopy').hide();
			VtigerJS_DialogBox.hideprogress();
			placeAtCenter(document.getElementById('responsePopupContainer'));
		});
	}
	
	function showCopyPopup(){
		fnvshNrm('dbMirrorCopy');
		jQuery('#dbMirrorCopy').show();
		placeAtCenter(document.getElementById('dbMirrorCopy'));
	}
	
</script>

<div id="config" class="col-xs-12">
	<div id="config-inner" class="col-xs-12 content-padding">
		<div class="col-xs-12 nopadding">
			<div class="col-xs-12 nopadding">
				<?php if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true) { ?>
				<div class="col-xs-12 nopadding">
					<span class="redColor fontBold"><?php echo $installationStrings['LBL_IMPORTANT']; ?>:</span>
					<?php echo $installationStrings['ERR_TABLES_NOT_INNODB'] .'. '. $installationStrings['MSG_CHANGE_ENGINE_BEFORE_MIGRATION']; ?>.<br/>
					<br />
					<a href="javascript:void(0)" onclick="fixDBHealth();"><?php echo $installationStrings['LBL_FIX_NOW']; ?></a>&nbsp; | &nbsp;<a href="javascript:void(0)" onclick="viewDBReport();"><?php echo $installationStrings['LBL_VIEW_REPORT']; ?></a>
				</div>
				<?php } ?>
				<div class="col-xs-12 col-md-6" style="padding-left:0px">
					<div class="col-xs-12 nopadding">
						<h3><?php echo $installationStrings['LBL_DATABASE_BACKUP']; ?></h3>
						<div class="spacer-20"></div>
						<div class="col-xs-12 nopadding">
							<div class="col-xs-12 col-md-5 nopadding">
								<input type="image" src="include/install/images/dbDump.gif" alt="<?php echo $installationStrings['LBL_DB_DUMP_DOWNLOAD']; ?>" border="0" title="<?php echo $installationStrings['LBL_DB_DUMP_DOWNLOAD']; ?>" onClick="getDbDump();">
							</div>
							<div class="col-xs-12 col-md-7 nopadding">
								<b><?php echo $installationStrings['QUESTION_NOT_TAKEN_BACKUP_YET']; ?></b><br>
								<?php echo $installationStrings['LBL_CLICK_FOR_DUMP_AND_SAVE']; ?>.<br><br>
							</div>
						</div>
						<div class="col-xs-12 nopadding">
							<div class="spacer-20"></div>
							<div><b><?php echo $installationStrings['LBL_NOTE']; ?></b>:<br><?php echo $installationStrings['MSG_PROCESS_TAKES_LONGER_TIME_BASED_ON_DB_SIZE']; ?>.</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-6" style="padding-right:0px">
					<div class="col-xs-12 nopadding">
						<h3><?php echo $installationStrings['LBL_DATABASE_COPY']; ?></h3>
						<div class="spacer-20"></div>
						<div class="col-xs-12 nopadding">
							<div class="col-xs-12 col-md-5 nopadding">
								<input type="image" src="include/install/images/dbCopy.gif" alt="<?php echo $installationStrings['LBL_DB_COPY']; ?>" border="0" title="<?php echo $installationStrings['LBL_DB_COPY']; ?>" onClick="showCopyPopup();">
							</div>
							<div class="col-xs-12 col-md-7 nopadding">
								<b><?php echo $installationStrings['QUESTION_MIGRATING_TO_NEW_DB']; ?>?</b>
								<?php echo $installationStrings['LBL_CLICK_FOR_NEW_DATABASE']; ?>.
							</div>
						</div>
						<div class="col-xs-12 nopadding">
							<div class="spacer-20"></div>
							<div><b><?php echo $installationStrings['LBL_RECOMMENDED']; ?></b>:<br>
								<?php echo $installationStrings['MSG_USE_OTHER_TOOLS_FOR_DB_COPY']; ?>.
							</div>
						</div>
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
				<button class="crmbutton small edit btn-arrow-left"><?php echo $installationStrings['LBL_BACK']; ?></button>
			</form>
		</div>
		<div class="col-xs-6 text-right">
			<form action="install.php" name="migrateform" id="migrateform" method="post">
				<input type="hidden" name="auth_key" id="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
				<input type="hidden" name="file" value="ConfirmMigrationConfig.php" />
				<input type="hidden" name="forceDbCheck" value="true" />											
				<button type="button" class="crmbutton small edit btn-arrow-right" onClick="migrateform.submit();"><?php echo $installationStrings['LBL_NEXT']; ?></button>
			</form>
		</div>
	</div>
</div>
</div>
</div>
</div>
<div id="footer" class="col-xs-12 content-padding">
	<div id="footer-inner" class="col-xs-12 content-padding text-center">
		<div class="spacer-50"></div>
	</div>
</div>
</div>
</div>
</div>
<div id="dbMirrorCopy" class="posLayPopup container" style="display:none;">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12 nopadding">
				<div class="spacer-20"></div>
				<div class="col-xs-12 col-md-1 vcenter text-left" style="padding:5px">
					<div class="floatRightTiny">
						<a href="javascript: void(0);" onClick="fninvsh('dbMirrorCopy');">
						<i class="vteicon">highlight_off</i>
						</a>
					</div>
				</div>
				<!-- 
					-->
				<div class="col-xs-12 col-md-11 text-center vcenter">
					<b><?php echo $installationStrings['LBL_COPY_OLD_DB_TO_NEW_DB'] ?></b>
				</div>
			</div>
			<div id="dbMirrorCopy-inner" class="col-xs-12">
				<div class="spacer-20"></div>
				<div class="form-group">
					<label for="newDatabaseName"><?php echo $installationStrings['LBL_NEW']. ' ' .$installationStrings['LBL_DATABASE_NAME']; ?> <sup><font class="redColor">*</font></sup></label>
					<div class="dvtCellInfo">
						<input type='text' class="detailedViewTextBox" name='newDatabaseName' id='newDatabaseName' value='<?php echo $newDbForCopy ?>'>
						<br><?php echo $installationStrings['LBL_IF_DATABASE_EXISTS_WILL_RECREATE'] ?>.
					</div>
				</div>
				<div class="form-group">
					<label for="rootUserName">Root <?php echo $installationStrings['LBL_USER_NAME'] ?> <sup><font class="redColor">*</font></sup></label>
					<div class="dvtCellInfo">
						<input type='text' class="detailedViewTextBox" name='rootUserName' id='rootUserName' value=''>
						<br><?php echo $installationStrings['LBL_SHOULD_BE_PRIVILEGED_USER'] ?>.
					</div>
				</div>
				<div class="form-group">
					<label for="rootPassword">Root <?php echo $installationStrings['LBL_PASSWORD'] ?></label>
					<div class="dvtCellInfo">
						<input type='password' class="detailedViewTextBox" name='rootPassword' id='rootPassword' value=''>
					</div>
				</div>
			</div>
			<div class="col-xs-12 text-center">
				<button type="button" class="crmbutton small edit" onclick='doDBCopy();'>Copy Now</button>
			</div>
			<div class="col-xs-12 text-center">
				<div class="spacer-20"></div>
				<div class="helpmessagebox"><span class='redColor fontBold'><?php echo $installationStrings['LBL_NOTE']; ?>:</span> <?php echo $installationStrings['MSG_PROCESS_TAKES_LONGER_TIME_BASED_ON_DB_SIZE']; ?>.</div>
				<div class="spacer-20"></div>
			</div>
		</div>
	</div>
</div>
<div id="responsePopupContainer" class="posLayPopup container" style="display:none;">
	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12 nopadding">
				<div class="col-xs-12 col-md-1 vcenter text-left" style="padding:5px">
					<div class="floatRightTiny">
						<a href="javascript: void(0);" onClick="fninvsh('responsePopupContainer');">
						<i class="vteicon">highlight_off</i>
						</a>
					</div>
				</div>
				<!-- 
					-->
				<div class="col-xs-12 col-md-11 text-center vcenter">
				</div>
			</div>
			<div class="col-xs-12">
				<div id="responsePopup" style="word-wrap:break-word;font-weight:bold">&nbsp;</div>
				<div class="spacer-20"></div>
			</div>
		</div>
	</div>
</div>
</body>
</html>