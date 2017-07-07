<?php
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';
$_SESSION['modules_to_update']['Myfiles'] = 'packages/vte/mandatory/Myfiles.zip';

// crmv@73256 - enable advanced caching for Mobile App
require_once('modules/Touch/Touch.php');
$touch = Touch::getInstance();
$touch->setProperty('use_offline_cache', 1);
// crmv@73256e