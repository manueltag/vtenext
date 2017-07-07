<?php
$_SESSION['modules_to_update']['FieldFormulas'] = 'packages/vte/mandatory/FieldFormulas.zip';
$_SESSION['modules_to_update']['Touch'] = 'packages/vte/mandatory/Touch.zip';

@unlink('modules/Emails/gotodownload.php');
@unlink('modules/Emails/old_class.phpmailer.php');
@unlink('modules/Emails/old_class.smtp.php');
@unlink('modules/Emails/GmailBookmarklet.js');
@unlink('modules/Emails/GmailBookmarkletTrigger.js');
@unlink('modules/Settings/ListMailAccount.php');
@unlink('modules/Users/AddMailAccount.php');
@unlink('Smarty/templates/Emails.tpl');
@unlink('Smarty/templates/EmailContents.tpl');
@unlink('Smarty/templates/Webmails.tpl');
?>