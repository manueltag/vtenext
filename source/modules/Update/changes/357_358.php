<?php
$_SESSION['modules_to_update']['Services'] = 'packages/vte/mandatory/Services.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

//cancello vecchi temi
$old_themes = array('alphagrey','bluelagoon','enterprise','woodspice');
foreach($old_themes as $old_theme) {
	$dir = "themes/$old_theme";
	if (is_dir($dir)) folderDetete($dir);
}
if(!function_exists('folderDetete')){
	function folderDetete($dir) {
		$handle = opendir($dir);
		while (false !== ($file = readdir($handle))) {
			if (in_array($file,array('.','..'))) continue;
			elseif(is_file($dir.'/'.$file))	unlink($dir.'/'.$file);
			elseif (is_dir($dir.'/'.$file)) folderDetete($dir.'/'.$file);
		}
		closedir($handle);
		rmdir($dir);
	}
}

//aumento il numero di elementi nei Più recenti
$file = 'config.inc.php';
$handle_file = fopen($file, "r");
while(!feof($handle_file)) {
	$buffer = fread($handle_file, 552000);
}
$bk_file = 'config.inc.vte3.0.php';
$handle_bk_file = fopen($bk_file, "w");
fputs($handle_bk_file, $buffer);
fclose($handle_bk_file);
$buffer = str_replace('$history_max_viewed = \'5\';','$history_max_viewed = \'10\';',$buffer);
fclose($handle_file);
$handle = fopen($file, "w");
fputs($handle, $buffer);
fclose($handle);
?>