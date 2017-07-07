<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

global $php_max_execution_time;
set_time_limit($php_max_execution_time);

session_start();

$auth_key = $_REQUEST['auth_key'];
if($_SESSION['authentication_key'] != $auth_key) {
	die($installationStrings['ERR_NOT_AUTHORIZED_TO_PERFORM_THE_OPERATION']);
}

if(isset($_REQUEST['selected_modules'])) {
	$_SESSION['migration_info']['selected_optional_modules'] = $_REQUEST['selected_modules'];
}

Migration_Utils::copyRequiredFiles($_SESSION['migration_info']['source_directory'], $_SESSION['migration_info']['root_directory']);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $enterprise_mode. ' - ' . $installationStrings['LBL_CONFIG_WIZARD']. ' - ' . $installationStrings['LBL_MIGRATION']?></title>
	<link REL="SHORTCUT ICON" HREF="<?php echo get_logo_install('favicon'); ?>">	<!-- crmv@18123 -->
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script type="text/javascript" src="include/js/jquery.js"></script>	<!-- crmv@26523 -->
	<link href="themes/softed/vte_bootstrap.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/install.css" rel="stylesheet" type="text/css">
	<script type="text/javascript">
	jQuery.noConflict();
	</script>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<?php
if($_REQUEST['migration_start'] != 'true') {
?>	
<body>
	<div id="main-container" class="container">
		<div class="row">
			<div class="col-xs-offset-1 col-xs-10">
		
				<div id="content" class="col-xs-12">
					<div id="content-cont" class="col-xs-12">
						<div id="content-inner-cont" class="col-xs-12">
						
							<div class="col-xs-12 content-padding">	
								<div class="col-xs-12 col-md-6 vcenter text-left">
									<h2 class=""><?php echo "Migration Process" ?></h2>
								</div><!--
								--><div class="col-xs-12 col-md-6 vcenter text-right">
									<a href="http://www.vtecrm.com" target="_blank">
										<img src="include/install/images/vtecrm.png" />
									</a>
								</div>
							</div>
							
							<div id="config" class="col-xs-12">
								<div id="config-inner" class="col-xs-12 content-padding">
									<iframe class='licence' id='triggermigration_iframe' frameborder=0 src='' marginwidth=20 scrolling='auto'>
									</iframe>
								</div>
								
								<div id="nav-bar" class="col-xs-12 nopadding">
									<div id="nav-bar-inner" class="col-xs-12">	
										<div class="col-xs-6 text-left nopadding">
										</div>
										
										<div class="col-xs-6 text-right nopadding">
											<div id='Mig_Close' style='display:none;'>
												<form action="install.php" method="post" name="form" id="form">
													<input type="hidden" name="file" value="MigrationComplete.php" />	
									        <button class="crmbutton small edit btn-arrow-right"><?php echo $installationStrings['LBL_NEXT']; ?></button>
									    	</form>
									    </div>
										</div>
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
	    
	<script type='text/javascript'>
    var auth_key = '<?php echo $auth_key; ?>';
    if(typeof('Event') != 'undefined') {
    	Event.observe(window, 'load', function() {
    		VtigerJS_DialogBox.progress();
    		document.getElementById('triggermigration_iframe').src = 'install.php?file=MigrationProcess.php&migration_start=true&auth_key='+auth_key;
    	});
    }
    function Migration_Complete() {
    	$('Mig_Close').style.display = 'block';
    }
	</script>
<?php 
} else {
	// Start the migration now	
	echo '<body onload="window.parent.VtigerJS_DialogBox.hideprogress();" style="background:white">';
	
	require_once('include/utils/utils.php');
	require_once('include/logging.php');
	$migrationlog = & LoggerManager::getLogger('MIGRATION');	
	
	if($_SESSION['authentication_key']==$_REQUEST['auth_key']) {		
		$completed = Migration_Utils::migrate($_SESSION['migration_info']);
		if ($completed == true) {
			echo "<script type='text/javascript'>window.parent.Migration_Complete();</script>";
		}
	}
}
?>
	</body>
</html>	