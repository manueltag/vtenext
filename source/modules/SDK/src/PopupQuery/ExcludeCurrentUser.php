<?php
/* crmv@42247 */
global $table_prefix, $current_user;
$query .= " and {$table_prefix}_users.id <> ".$current_user->id.' ';
?>