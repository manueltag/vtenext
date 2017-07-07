<?php
/* validazione delle credenziali di login */
require_once('include/utils/utils.php');
require_once('include/Webservices/Login.php');

global $login, $userId;
$login = false;
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$user = CRMEntity::getInstance('Users');
if (empty($user)) $user = new Users(); // fallback if Users not SDK-able
$userId = $user->retrieve_user_id($username);
$accessKey = vtws_getUserAccessKey($userId);
if (strcmp($accessKey,$password) === 0) {
	$login = true;
}
?>