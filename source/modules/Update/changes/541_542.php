<?php
global $adb;
$EmailsInstance = Vtiger_Module::getInstance('Emails');
$adb->pquery('UPDATE vtiger_field SET displaytype = ? WHERE fieldname = ? AND tabid = ?',array(1,'from_email',$EmailsInstance->id));
?>