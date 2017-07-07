<?php
$iframe = SDK::getHomeIframe($_REQUEST['stuffid']);
die(Zend_Json::encode($iframe));
?>