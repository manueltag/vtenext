<?php
global $table_prefix;
require_once("modules/Update/Update.php");
Update::change_field($table_prefix.'_modcomments','parent_comments','I','19');
?>