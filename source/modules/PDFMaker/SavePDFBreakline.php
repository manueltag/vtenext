<?php
global $adb,$table_prefix;
$crmid = $_REQUEST["pid"];

$sql="DELETE FROM ".$table_prefix."_pdfmaker_breakline WHERE crmid=?";
$adb->pquery($sql,array($crmid));

$breaklines = rtrim($_REQUEST["breaklines"],"|");
                                                                                                      
if($breaklines!="")
{               
  $sql="INSERT INTO ".$table_prefix."_pdfmaker_breakline (crmid, productid, sequence, show_header, show_subtotal) VALUES";
  $sql_suf="";
  
  $show_header=0;
  $show_subtotal=0;
  if($_REQUEST["show_header"]=="true")
    $show_header=1;    
  if($_REQUEST["show_subtotal"]=="true")
    $show_subtotal=1;
  
  $products = explode("|",$breaklines);
  for($i=0;$i<count($products);$i++)
  {
    list($productid,$sequence) = explode("_", $products[$i],2);
    $sql_suf.=" (".$crmid.",".$productid.",".$sequence.",".$show_header.",".$show_subtotal."),";  
  }
  
  if($sql_suf!="")
  {
    $sql_suf = rtrim($sql_suf, ",");
    $sql .= $sql_suf;
    $adb->query($sql); 
  }
}
exit;
?>