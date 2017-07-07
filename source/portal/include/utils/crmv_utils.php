<?php
//crmv@18049
function get_logo($mode){
	include_once('version.php');
	global $enterprise_mode,$enterprise_project;
	$logo_path = 'images/';
	if ($mode == 'favicon')
		$extension = 'ico';
	else		
		$extension = 'png';
	if ($mode == 'project')
		$logo_path.=$enterprise_project.".".$extension;
	else
		$logo_path.=$enterprise_mode."_".$mode.".".$extension;
	return $logo_path;
}
//crmv@18049e
?>