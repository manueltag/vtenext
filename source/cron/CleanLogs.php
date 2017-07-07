<?php
//crmv@27164
$files = scandir('logs');
foreach($files as $file) {
	if (strpos($file,'.pid') !== false) {
		unlink('logs/'.$file);
	}
}
//crmv@27164e
?>