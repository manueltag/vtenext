<?php
/* crmv@42266 */

require_once 'include/utils/CurrencyUtils.php';

$from = substr(trim($_REQUEST['from']), 0, 3);
$to = substr(trim($_REQUEST['to']), 0, 3);

if (empty($from) || empty($to)) die();

// get ratio
$CU = CurrencyUtils::getInstance();
try {
	$ratio = $CU->getExchangeRatio($from, $to);
} catch (Exception $e) {
	// do nothing
}

// output
echo $ratio;
exit;
?>