<?php
//crmv@26986
global $current_user;
require_once('modules/SDK/src/Favorites/Utils.php');
echo getHtmlFavoritesList($current_user->id,$_REQUEST['mode']);
exit;
//crmv@26986e
?>