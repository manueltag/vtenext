<?php
$_SESSION['modules_to_update']['Morphsuit'] = 'packages/vte/mandatory/Morphsuit.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';

require_once('include/utils/Cache.php');

$cache = Cache::getInstance('mIiTtC');
$cache->clear();

?>