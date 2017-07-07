<?php
//crmv@27618
$mode = $_REQUEST['mode'];
if ($mode == 'spamedit') {
	include('modules/Settings/MailScanner/MailScannerSpamRuleEdit.php');
} elseif ($mode == 'spamsave') {
	include('modules/Settings/MailScanner/MailScannerSpamRuleSave.php');
}
//crmv@27618e
?>