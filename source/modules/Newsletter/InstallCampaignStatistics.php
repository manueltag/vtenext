<?php
//crmv@22700
require_once('include/utils/utils.php'); 
require_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Menu.php');

function installCampaignStatistics() {

	global $adb;
	global $table_prefix;
	$campaignsModule = Vtiger_Module::getInstance('Campaigns');

	//Mail Schedulate
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_message_queue',$sequence,'Message Queue',0));

	//Mail Inviate
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_sent_messages',$sequence,'Sent Messages',0));

	//Target che hanno aperto la mail
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_viewed_messages',$sequence,'Viewed Messages',0));

	//Target che hanno cliccato almeno un link della mail
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_tracked_link',$sequence,'Tracked Link',0));

	//Target che si sono disiscritti dalla campagna
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_unsubscriptions',$sequence,'Unsubscriptions',0));

	//Mail non inviate
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_bounced_messages',$sequence,'Bounced Messages',0));
				
	//Suppression list (indirizzi delle mail non inviate + mail dei disiscritti)
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_suppression_list',$sequence,'Suppression list',0));
				
	//Failed Messages (es. record cancellati)
	$relation_id = $adb->getUniqueID($table_prefix.'_relatedlists');
	$max_sequence = 0;
	$result = $adb->query("SELECT max(sequence) as maxsequence FROM ".$table_prefix."_relatedlists WHERE tabid=$campaignsModule->id");
	if($adb->num_rows($result)) $max_sequence = $adb->query_result($result, 0, 'maxsequence');
	$sequence = $max_sequence+1;
	$adb->pquery("INSERT INTO ".$table_prefix."_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence) VALUES(?,?,?,?,?,?,?)",
				array($relation_id,$campaignsModule->id,0,'get_statistics_failed_messages',$sequence,'Failed Messages',0));
}
//crmv@22700e
?>