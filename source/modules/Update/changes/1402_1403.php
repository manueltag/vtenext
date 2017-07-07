<?php

global $adb, $table_prefix;
if (file_exists('hash_version.txt')) {
	$hash_version = file_get_contents('hash_version.txt');
	$adb->updateClob($table_prefix.'_version','hash_version','id=1',$hash_version);
	@unlink('hash_version.txt');
}
$cache = Cache::getInstance('vteCacheHV');
$cache->clear();