<?php

$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';

// remove unused files
$deleteFiles = array('modules/Products/MassEditSave.php', 'modules/Services/MassEditSave.php');
foreach ($deleteFiles as $df) {
	if (is_readable($df) && is_writeable($df)) {
		// check for mycrmv tags first
		$content = file_get_contents($df);
		if ($content !== false && strpos($content, 'mycrmv') === false) {
			@unlink($df);
		}
   	}
}
