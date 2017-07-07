<?php
//crmv@28295
global $current_user;
require_once('modules/SDK/src/Todos/Utils.php');
echo getHtmlTodosList($current_user->id,$_REQUEST['mode']);
//crmv@28295e
?>