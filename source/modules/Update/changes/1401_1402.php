<?php
if (isModuleInstalled('RecycleBin')) {
	$_SESSION['modules_to_update']['RecycleBin'] = 'packages/vte/optional/RecycleBin.zip';
}
@unlink('Smarty/templates/Settings/ProcessMaker/LimitExceeded.tpl');