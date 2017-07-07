<?php

// crmv@105933
// remove unnecessary tools
$processesModule = Vtecrm_Module::getInstance('Processes');
if ($processesModule) {
	$processesModule->disableTools('Import', 'Merge');
}
// crmv@105933e