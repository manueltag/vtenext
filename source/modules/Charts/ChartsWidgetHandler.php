<?php
/*  crmv@30014 - charts */
global $currentModule;

$widgetName = vtlib_purify($_REQUEST['widget']);
$criteria = vtlib_purify($_REQUEST['criteria']);

$widgetController = CRMEntity::getInstance($currentModule);
$widgetInstance = $widgetController->getWidget($widgetName);
$widgetInstance->setCriteria($criteria);

echo $widgetInstance->process( array('ID' => vtlib_purify($_REQUEST['parentid'])) );

?>