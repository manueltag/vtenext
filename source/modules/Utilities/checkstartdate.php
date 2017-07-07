<?php
include_once('include/utils/utils.php');
$currtime = date("Y:m:d:H:i:s");
list($y,$m,$d,$h,$min,$sec) = explode(':',$currtime);	//crmv@39176
echo "[{YEAR:'".$y."',MONTH:'".$m."',DAY:'".$d."',HOUR:'".$h."',MINUTE:'".$min."'}]";
die;
?>