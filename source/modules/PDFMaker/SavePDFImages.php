<?php
global $adb,$table_prefix;
$crmid = $_REQUEST["pid"];

$sql="DELETE FROM ".$table_prefix."_pdfmaker_images WHERE crmid=?";
$adb->pquery($sql,array($crmid));

$sql="INSERT INTO ".$table_prefix."_pdfmaker_images (crmid, productid, sequence, attachmentid, width, height) VALUES";
$sql_suf="";
foreach($_REQUEST as $key=>$value)
{
  if(strpos($key, "img_")!==false && $value!="no_image")
  {
    list($bin,$productid,$sequence) = explode("_", $key);     
    $width=$_REQUEST["width_".$productid."_".$sequence];
    $height=$_REQUEST["height_".$productid."_".$sequence];
    if(!is_numeric($width) || $width>999)
      $width=0;
    if(!is_numeric($height) || $height>999)
      $height=0;
    
    $sql_suf.=" (".$crmid.",".$productid.",".$sequence.",".$value.",".$width.",".$height."),";
  }  
}
if($sql_suf!="")
{
  $sql_suf = rtrim($sql_suf, ",");
  $sql .= $sql_suf;
  $adb->query($sql); 
}

exit;
?>