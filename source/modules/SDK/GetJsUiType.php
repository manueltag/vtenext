<?php
$info = SDK::getUitypeInfo($_REQUEST['uitype']);
die($info['src_js']);
?>