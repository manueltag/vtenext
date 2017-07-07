<?php
Update::info("vtigerversion.php file was replaced by vteversion.php. So if you have some customisations that includes it replace with the new one.");

@unlink('vtigerversion.php');

// change value of $calculate_response_time in config.inc
$configInc = file_get_contents('config.inc.php');
if (empty($configInc)) {
	Update::info("Unable to get config.inc.php contents, please modify it manually.");
} else {
	// backup it (only if it doesn't exist)
	$newConfigInc = 'config.inc.1505.php';
	if (!file_exists($newConfigInc)) {
		file_put_contents($newConfigInc, $configInc);
	}
	// change value
	$configInc = str_replace("vtigerversion.php","vteversion.php",$configInc);
	if (is_writable('config.inc.php')) {
		file_put_contents('config.inc.php', $configInc);
	} else {
		Update::info("Unable to update config.inc.php, please modify it manually.");
	}
}