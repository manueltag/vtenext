<?php
if(!function_exists('folderDetete')){
	function folderDetete($dir) {
		if (is_dir($dir) && ($handle = opendir($dir))) {
			while (false !== ($file = readdir($handle))) {
				if (in_array($file,array('.','..'))) continue;
				elseif(is_file($dir.'/'.$file))	@unlink($dir.'/'.$file);
				elseif (is_dir($dir.'/'.$file)) folderDetete($dir.'/'.$file);
			}
			closedir($handle);
			@rmdir($dir);
		}
	}
}
// copies files and non-empty directories
function rcopy($src, $dst) {
	if (is_dir($src)) {
		@mkdir($dst);
		$files = scandir($src);
		foreach ($files as $file) {
			if ($file != "." && $file != "..") {
				rcopy("$src/$file", "$dst/$file");
			}
		}
	} elseif (file_exists($src)) {
		copy($src, $dst);
	}
}
if(!function_exists('getModuleList201')){
	function getModuleList201() {
		global $adb,$table_prefix;
		$query = "select name from ".$table_prefix."_tab where presence = 0 and name not in ('Emails','Events','Fax') and (isentitytype = 1 or name in ('Home','Dashboard','Rss','Reports','RecycleBin')) order by name";
		return $adb->query($query);
	}
}
?>