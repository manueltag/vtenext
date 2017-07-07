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
$migrationInfo = $_SESSION['migration_info'];
$root_directory = $migrationInfo['root_directory'];
$source_directory = $migrationInfo['source_directory'];
session_destroy();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $enterprise_mode. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_FINISH']?></title>
	<link REL="SHORTCUT ICON" HREF="<?php echo get_logo_install('favicon'); ?>">	<!-- crmv@18123 -->
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/vte_bootstrap.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/install.css" rel="stylesheet" type="text/css">
</head>

<body>

<div id="main-container" class="container">
		<div class="row">
			<div class="col-xs-offset-1 col-xs-10">
				
				<div id="content" class="col-xs-12">
					<div id="content-cont" class="col-xs-12">
						<div id="content-inner-cont" class="col-xs-12">
						
							<div class="col-xs-12 content-padding">	
								<div class="col-xs-12 col-md-6 vcenter text-left">
									<h2 class=""><?php echo $installationStrings['LBL_CONFIG_COMPLETED']; ?></h2>
								</div><!--
								--><div class="col-xs-12 col-md-6 vcenter text-right">
									<a href="http://www.vtecrm.com" target="_blank">
										<img src="include/install/images/vtecrm.png" />
									</a>
								</div>
							</div>
							
							<div id="config" class="col-xs-12">
								<div id="config-inner" class="col-xs-12 content-padding">
									<div class="col-xs-12 nopadding">
									
										<?php
											$renameResult = Common_Install_Wizard_Utils::renameInstallationFiles();
											$renamefile = $renameResult['renamefile'];
											$ins_file_renamed = $renameResult['install_file_renamed'];
											$ins_dir_renamed = $renameResult['install_directory_renamed'];
											
											$_SESSION['VTIGER_DB_VERSION'] = $vtiger_current_version;
										?>
										
										<table class="table borderless">
											<tr>
												<td align=center class=small>
													<b><?php echo $installationStrings['LBL_MIGRATION_FINISHED']; ?>. vtigercrm-<?php echo $vtiger_current_version. ' ' .$installationStrings['LBL_ALL_SET_TO_GO']; ?></b>
													<div style="width:100%;padding:10px;" align=left>
														<ul>
															<?php if($ins_file_renamed==true){ ?>
															<li><?php echo $installationStrings['LBL_INSTALL_PHP_FILE_RENAMED']. ' ' .$renamefile;?>install.php.txt.</li>
															<?php } else { ?>						
															<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_INSTALL_PHP_FILE']; ?>.</font></li>
															<?php } ?>
															
															<?php /*if($mig_file_renamed==true){ ?>
															<li><?php echo $installationStrings['LBL_MIGRATE_PHP_FILE_RENAMED']. ' ' .$renamefile;?>migrate.php.txt.</li>
															<?php } else { ?>						
															<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_MIGRATE_PHP_FILE']; ?>.</font></li>
															<?php } */ ?>
															<?php if($ins_dir_renamed==true){ ?>
															<li><?php echo $installationStrings['LBL_INSTALL_DIRECTORY_RENAMED']. ' ' .$renamefile;?>install.</li> 
															<?php } else { ?>						
															<li><font color='red'><?php echo $installationStrings['WARNING_RENAME_INSTALL_DIRECTORY']; ?>.</font></li>
															<?php } ?>
														</ul>
														<br>
														<ul>
															<li><?php echo $installationStrings['LBL_OLD_VERSION_IS_AT'] . $source_directory;?>.
															<li><?php echo $installationStrings['LBL_CURRENT_SOURCE_PATH_IS'] . $root_directory;?>.
															<li><?php echo $installationStrings['LBL_LOGIN_USING_ADMIN']; ?>.</li>
															<li><?php echo $installationStrings['LBL_SET_OUTGOING_EMAIL_SERVER']; ?></li>						
															<li><?php echo $installationStrings['LBL_RENAME_HTACCESS_FILE']; ?>. <a href="javascript:void(0);" onclick="showhidediv();"><?php echo $installationStrings['LBL_MORE_INFORMATION']; ?></a>
												   				<div id='htaccess_div' style="display:none"><br><br>
													   				<?php echo $installationStrings['MSG_HTACCESS_DETAILS']; ?>
												  			 	</div>
												  			</li>
														</ul>
														<br>
														<ul><b>
															<li><font color='#0000FF'><?php echo $installationStrings['LBL_YOU_ARE_IMPORTANT']; ?></font></li>
															<li><?php echo $installationStrings['LBL_PRIDE_BEING_ASSOCIATED']; ?></li>
															<li><?php echo $installationStrings['LBL_TALK_TO_US_AT_FORUMS']; ?></li>
															<li><?php echo $installationStrings['LBL_DISCUSS_WITH_US_AT_BLOGS']; ?></li>
															<li><?php echo $installationStrings['LBL_WE_AIM_TO_BE_BEST']. '. ' .$installationStrings['LBL_SPACE_FOR_YOU']; ?></li>
														</b></ul>
													</div>
												</td>
											</tr>
										</table>

									</div>
								</div>
							</div>
							
							<div id="nav-bar" class="col-xs-12 nopadding">
								<div id="nav-bar-inner" class="col-xs-12">	
									<div class="col-xs-12 text-center">
										<form action="index.php" method="post" name="form" id="form">
											<input type="hidden" name="default_user_name" value="admin">
									 		<button class="crmbutton small edit"><?php echo $installationStrings['LBL_FINISH']; ?></button>
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
</body>
</html>