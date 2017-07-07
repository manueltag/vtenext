<?php
global $current_user;
include_once('modules/Calendar/calendarLayout.php');
//crmv@36555
$cal_class = CRMEntity::getInstance('Calendar');
echo crmvGetUserAssignedToHTML($cal_class->getShownUserId($current_user->id,true),"events",true);
//crmv@36555 e
?>