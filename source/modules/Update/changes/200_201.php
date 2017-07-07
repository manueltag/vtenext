<?php
global $adb;
$adb->query("ALTER TABLE vtiger_users CHANGE date_entered date_entered TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL , CHANGE date_modified date_modified TIMESTAMP DEFAULT '0000-00-00 00:00:00' NULL");
$adb->query("UPDATE vtiger_activity_view SET sortorderid='0' WHERE activity_viewid='2'");
$adb->query("UPDATE vtiger_activity_view SET sortorderid='1' WHERE activity_viewid='1'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_campaignrelstatus(
				campaignrelstatusid INT(19) NULL  , 
				campaignrelstatus VARCHAR(256) COLLATE utf8_general_ci NULL  , 
				sortorderid INT(19) NULL  , 
				presence INT(19) NULL  
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_campaignrelstatus_seq(
				id INT(11) NOT NULL  
			) ENGINE=MYISAM DEFAULT CHARSET='utf8'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_licencekeystatus(
				licencekeystatusid INT(19) NOT NULL  AUTO_INCREMENT , 
				licencekeystatus VARCHAR(200) COLLATE utf8_general_ci NOT NULL  , 
				sortorderid INT(19) NOT NULL  DEFAULT '0' , 
				presence INT(1) NOT NULL  DEFAULT '1' , 
				PRIMARY KEY (licencekeystatusid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_notescf(
				notesid INT(19) NOT NULL  DEFAULT '0' , 
				PRIMARY KEY (notesid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_opportunitystage(
				potstageid INT(19) NOT NULL  AUTO_INCREMENT , 
				stage VARCHAR(200) COLLATE utf8_general_ci NOT NULL  , 
				sortorderid INT(19) NOT NULL  DEFAULT '0' , 
				presence INT(1) NOT NULL  DEFAULT '1' , 
				probability DECIMAL(3,2) NULL  DEFAULT '0.00' , 
				PRIMARY KEY (potstageid) , 
				UNIQUE KEY opportunitystage_stage_idx(stage) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("CREATE TABLE IF NOT EXISTS vtiger_relcriteria_grouping(
				groupid INT(11) NOT NULL  , 
				queryid INT(19) NOT NULL  , 
				group_condition VARCHAR(256) COLLATE utf8_general_ci NULL  , 
				condition_expression TEXT COLLATE utf8_general_ci NULL  , 
				PRIMARY KEY (groupid,queryid) 
			) ENGINE=INNODB DEFAULT CHARSET='utf8'");
$adb->query("DROP TABLE IF EXISTS vtiger_workflowrunoncerel");
?>