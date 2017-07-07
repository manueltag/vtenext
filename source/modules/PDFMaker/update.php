<?php
global $theme_path,$mod_strings,$app_strings,$table_prefix;
error_reporting(E_ERROR | E_PARSE);
require_once("Smarty_setup.php");
$smarty = new vtigerCRM_Smarty();

$sql = "SELECT version FROM ".$table_prefix."_pdfmaker_releases WHERE updated=0";
$result = $adb->query($sql);
$to126 = "false";
$to127 = "false";
$to128 = "false";
while($row = $adb->fetchByAssoc($result))
{
  switch($row["version"])
  {
    case "1.26":
      $to126 = "true";
      break;
      
    case "1.27":
      $to127 = "true";
      break;
      
    case "1.28":
      $to128 = "true";
      break;  
  }
} 
$smarty->assign("TO126",$to126);
$smarty->assign("TO127",$to127);
$smarty->assign("TO128",$to128);

$theme_path="themes/".$theme_path."/";
$image_path=$theme_path."images/";

$smarty->assign("THEME", $theme_path);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);

if(!isset($_REQUEST["step"]) || $_REQUEST["step"]=="")
{
  $smarty->assign("STEP","0");
  $smarty->assign("CURRENT_STEP","1");
  $smarty->assign("TOTAL_STEPS","2"); 
  $smarty->assign("STEPNAME",$mod_strings["LBL_UPDATE_SELECTION"]);
  
  $controlPermissionsUpdate=true;
  require_once ('modules/PDFMaker/controlPermissions.php');  
  $smarty->assign("LIST_PERMISSIONS",$list_permissions);
  $smarty->assign("P_ERRORS",$p_errors);
  $smarty->display("modules/PDFMaker/update.tpl");
}
elseif($_REQUEST["step"]=="1")
{
  if($_REQUEST["installtype"]=="custom")
  {
    $smarty->assign("STEP","1");
    $smarty->assign("CURRENT_STEP","2");
    $smarty->assign("TOTAL_STEPS","3");
    $smarty->assign("STEPNAME",$mod_strings["LBL_CUSTOMIZATION"]);
    $smarty->display("modules/PDFMaker/update.tpl");
  }
  else
  { 
    $updateFilesToCheck=array();
    if($to126=="true") {      
      array_push($updateFilesToCheck, "index.php","Smarty_setup.php","EditView.php");
      $errTbl = finishUpdate();
    }
    if($to127=="true") 
      array_push($updateFilesToCheck, "index.php","ListViewEntries.tpl","InventoryDetailView.tpl");
    if($to128=="true") 
      array_push($updateFilesToCheck, "index.php");
    $updateFilesToCheck = array_unique($updateFilesToCheck);   
    
    $source_path=getcwd()."/modules/PDFMaker/torewrite";
    $dir_iterator = new RecursiveDirectoryIterator($source_path);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $file) 
    {
      $dest=substr($file, strlen($source_path)+1);
      if(!in_array(basename($file), $updateFilesToCheck))
        continue;   
            
      if($file->isFile())
      { 
        if(!copy($file, getcwd()."/".$dest))
        {
            $error=true;
            $errTbl[]=$mod["LBL_ERROR_COPY_FILE"]." ".$dest.". ".$mod["LBL_CHANGE_PERMISSION"];
        }                   
      }                                                                
    }
    
    if(count($errTbl)>0)
    {
      $smarty->assign("STEP","error");
      $smarty->assign("ERROR_TBL",$errTbl);
      $smarty->display("modules/PDFMaker/update.tpl");
    }
    else
    {
      $smarty->assign("STEP","2");
      $smarty->assign("CURRENT_STEP","2");
      $smarty->assign("TOTAL_STEPS","2");
      $smarty->assign("STEPNAME",$mod_strings["LBL_CUSTOMIZATION"]);
      $smarty->display("modules/PDFMaker/update.tpl");
    }      
  }
}
elseif($_REQUEST["step"]=="2")
{
  if($to126 == "true")
    $errTbl = finishUpdate();
    
  if(count($errTbl)>0)
  {
    $smarty->assign("STEP","error");
    $smarty->assign("ERROR_TBL",$errTbl);
    $smarty->display("modules/PDFMaker/update.tpl");
  }
  else
  {
    $smarty->assign("STEP","2");
    $smarty->assign("CURRENT_STEP","3");
    $smarty->assign("TOTAL_STEPS","3");
    $smarty->assign("STEPNAME",$mod_strings["LBL_CUSTOMIZATION"]);
    $smarty->display("modules/PDFMaker/update.tpl");
  }     
}
elseif($_REQUEST["step"]=="3") {  
  if($to126 == "true")
    $adb->query("UPDATE ".$table_prefix."_pdfmaker_releases SET updated=1 WHERE version='1.26'");
  if($to127 == "true")
    $adb->query("UPDATE ".$table_prefix."_pdfmaker_releases SET updated=1 WHERE version='1.27'");
  if($to128 == "true")
    $adb->query("UPDATE ".$table_prefix."_pdfmaker_releases SET updated=1 WHERE version='1.28'");    
  
  if($to126 == "true")
    echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php?module=Settings&action=OrgSharingDetailView&parenttab=Settings\" />";
  else
    echo "<meta http-equiv=\"refresh\" content=\"0;url=index.php?module=PDFMaker&action=index&parenttab=Tools\" />";
}

  
function finishUpdate()
{
  global $adb, $mod_strings,$table_prefix;
  $tabid = getTabId("PDFMaker");
  $adb->query("INSERT INTO ".$table_prefix."_profile2standardpermissions SELECT profileid, ".$tabid.", 0, 0 FROM ".$table_prefix."_profile");
  $adb->query("INSERT INTO ".$table_prefix."_profile2standardpermissions SELECT profileid, ".$tabid.", 1, 0 FROM ".$table_prefix."_profile");
  $adb->query("INSERT INTO ".$table_prefix."_profile2standardpermissions SELECT profileid, ".$tabid.", 2, 0 FROM ".$table_prefix."_profile");
  $adb->query("INSERT INTO ".$table_prefix."_profile2standardpermissions SELECT profileid, ".$tabid.", 3, 0 FROM ".$table_prefix."_profile");
  $adb->query("INSERT INTO ".$table_prefix."_profile2standardpermissions SELECT profileid, ".$tabid.", 4, 0 FROM ".$table_prefix."_profile");
  
  $errTbl=array();
  $srcZip="http://www.crm4you.sk/PDFMaker/src/mpdf5.zip";
  $trgZip="modules/PDFMaker/mpdf.zip";
  if(copy($srcZip,$trgZip)) {
    require_once('vtlib/thirdparty/dUnzip2.inc.php');
    $unzip = new dUnzip2($trgZip);
    $unzip->unzipAll(getcwd()."/modules/PDFMaker/");
    if($unzip) $unzip->close();
    
    if(!is_dir("include/mpdf")){ //crmv@30066
      $errTbl[]=$mod_strings["UNZIP_ERROR"];        
    }else {
      unlink($trgZip);          
    }    
  }else{
    $errTbl[]=$mod_strings["DOWNLOAD_ERROR"];    
  }
  
  return $errTbl;
}
?>