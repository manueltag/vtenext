<?php
$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));
$_SESSION['modules_to_update']['ModComments'] = 'packages/vte/mandatory/ModComments.zip';
$_SESSION['modules_to_update']['ModNotifications'] = 'packages/vte/mandatory/ModNotifications.zip';
$_SESSION['modules_to_update']['PDFMaker'] = 'packages/vte/mandatory/PDFMaker.zip';

@unlink('cron/modules/Messages/MessagesCron.sh');
@unlink('cron/modules/Messages/Pop3Cron.sh');
?>