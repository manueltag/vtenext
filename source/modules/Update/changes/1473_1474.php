<?php

/* crmv@115378 */

// add column to vteprop table
global $adb;
$adb->addColumnToTable($table_prefix.'_vteprop', 'override_value', 'C(1023) DEFAULT NULL');

// migrate values
require_once('include/utils/PerformancePrefs.php');

PerformancePrefs::migrateConfig();

Update::info("The config.performance.php file is now stored inside the database.");
Update::info("If you had customizations using the raw variable \$PERFORMANCE_CONFIG,");
Update::info("please replace them with the proper methods in the PerformancePrefs class.");