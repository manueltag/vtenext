<?php
include_once('include/utils/utils.php');
//HTML Option
$site_name = $browsername;
$site_title = getTranslatedString('customerportal');
//Display Option
$wrapper_align = "center";
//Set Banner
$banner_image = "banner.jpg";
$banner_code = "<a class='banner' href='http://www.vtecrm.com' target='_blank'><img src='templates/images/$banner_image' /></a>";
//Set Subtitles
function getSubString($substr, $language = null) {
	return getTranslatedString('LBL_'.strtoupper($substr).'_DESC');
}
?>