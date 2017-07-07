<?php
// set default property for detailview_ajax_edit
$vteProp = VTEProperties::getInstance();
$prop = $vteProp->get('performance.detailview_ajax_edit');
if ($prop === null) {
	$prop = $vteProp->set('performance.detailview_ajax_edit', 1);
}