<?php
global $current_user;
if (!is_admin($current_user)) die('Unauthorized'); // crmv@37463
$startUpdateTime = microtime();

require_once("modules/$currentModule/$currentModule.php");
$focus = new $currentModule($_REQUEST['server'],$_REQUEST['server_username'],$_REQUEST['server_password'],$_REQUEST['current_version']);
$type_update = $_REQUEST['type_update'];
($type_update == 'specific_version') ? $focus->to_version = $_REQUEST['specificied_version'] : $focus->to_version = $_REQUEST['max_version'];

// TODO connesione e aggiornamento svn
//echo "server: $focus->server<br />user name: $focus->username<br />password: ******<br />from_version: $focus->from_version<br />to_version: $focus->to_version<br />";

$focus->update_changes();

$endUpdateTime = microtime();
list($usec, $sec) = explode(" ", $endUpdateTime);
$endUpdateTime = ((float)$usec + (float)$sec);
list($usec, $sec) = explode(" ", $startUpdateTime);
$startUpdateTime = ((float)$usec + (float)$sec);
$deltaTime = ($endUpdateTime - $startUpdateTime)/60;
$deltaTime = round($deltaTime,2);
?>
<br />
<form action="index.php" method="post" name="form" id="form">
 	<b>Update terminato</b> in <?php echo $deltaTime; ?> minuti <input type="submit" class="crmbutton small save" value="Prosegui" title="Prosegui" />
</form>