<?php
global $adb, $table_prefix;

$_SESSION['modules_to_update']['Newsletters'] = Array('location'=>'packages/vte/mandatory/Newsletters.zip','modules'=>Array('Newsletter','Targets'));

set_time_limit(0);

echo "<br/><b>The upgrade process may take several minutes, please be patient.</b><br/>"; flush();

// FUNCTIONS -----------

if (!function_exists('rmdirr')) {
	function rmdirr($dir) {
		if($objs = @glob($dir."/*")) {
			foreach($objs as $obj) {
				@is_dir($obj)? rmdirr($obj) : @unlink($obj);
			}
		}
		@rmdir($dir);
	}
}

if (!function_exists('addColumnToTable')) {
	function addColumnToTable($tablename, $columnname, $type, $extra = '') {
		global $adb;
		// check if already present
		$cols = $adb->getColumnNames($tablename);
		if (in_array($columnname, $cols)) {
			return;
		}
		$col = $columnname.' '.$type.' '.$extra;
		$adb->alterTable($tablename, $col, 'Add_Column');
	}
}

/* -------------- CODE -------------- */


// NEWSLETTER CLEANUP ---------------

// pulizia grafico campagne
if (is_dir('modules/Newsletter')) @rmdirr('modules/Newsletter/src/pChart.1.27d');

// traduzioni
$trans = array(
	'Newsletter' => array(
		'it_it' => array(
			'LBL_PREVIEW_NEWSLETTER' => 'Anteprima Newsletter',
			'LBL_PREVIEW_MAIL_BUTTON' => 'Anteprima Email',
			'LBL_PREVIEW_LINK' => 'Link per anteprima',
		),
		'en_us' => array(
			'LBL_PREVIEW_NEWSLETTER' => 'Preview Newsletter',
			'LBL_PREVIEW_MAIL_BUTTON' => 'Preview Email',
			'LBL_PREVIEW_LINK' => 'Preview Link',
		),
	),
	'APP_STRINGS' => array(
		'it_it' => array(
			'LBL_PREVIEW' => 'Anteprima',
		),
		'en_us' => array(
			'LBL_PREVIEW' => 'Preview',
		),
	),
);

foreach ($trans as $module=>$modlang) {
	foreach ($modlang as $lang=>$translist) {
		foreach ($translist as $label=>$translabel) {
			SDK::setLanguageEntry($module, $lang, $label, $translabel);
		}
	}
}

// codice numerico per tipologia
$schema_table =
'<schema version="0.3">
	<table name="tbl_s_newsletter_status">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="id" type="R" size="19">
			<KEY/>
		</field>
		<field name="name" type="C" size="200"/>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable('tbl_s_newsletter_status')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// now populate it
$list = array(
	0 => 'Unknown',
	// unsubscription
	1 => 'User unsubscription from email',
	// failed
	2 => 'LBL_OWNER_MISSING',
	3 => 'LBL_RECORD_DELETE',
	4 => 'LBL_OWNER_MISSING',
);

$res = $adb->query('select count(*) as m from tbl_s_newsletter_status');
if ($res && $adb->query_result($res, 0, 'm') == 0) {
	foreach ($list as $k => $v) {
		$adb->pquery('insert into tbl_s_newsletter_status (id, name) values (?,?)', array($k, $v));
	}
}

// add id columns values if it not exists
addColumnToTable('tbl_s_newsletter_failed', 'statusid', 'I(11)');
addColumnToTable('tbl_s_newsletter_unsub', 'statusid', 'I(11)');

// check if old columns exists
$hasfailedcol = in_array('note', $adb->getColumnNames('tbl_s_newsletter_failed'));
$hasunsubcol = in_array('type', $adb->getColumnNames('tbl_s_newsletter_unsub'));

// update values
$updok = true;
foreach ($list as $k => $v) {
	if ($hasfailedcol) {
		$res1 = $adb->pquery('update tbl_s_newsletter_failed set statusid = ? where note = ?', array($k, $v));
	} else $res1 = true;
	if ($hasunsubcol) {
		$res2 = $adb->pquery('update tbl_s_newsletter_unsub set statusid = ? where type = ?', array($k, $v));
	} else  $res2 = true;
	if (!$res1 || !$res2) $updok  = false;
}

// delete columns
if ($updok) {
	if ($hasfailedcol) {
		$sql = $adb->datadict->DropColumnSQL('tbl_s_newsletter_failed','note');
		$adb->datadict->ExecuteSQLArray($sql);
	}
	if ($hasunsubcol) {
		$sql = $adb->datadict->DropColumnSQL('tbl_s_newsletter_unsub','type');
		$adb->datadict->ExecuteSQLArray($sql);
	}
} else {
	echo "<br/><b>There was an error during update of tables 'tbl_s_newsletter_failed' and 'tbl_s_newsletter_unsub'. Check them manually</b><br/>"; flush();
}

// change column name of tbl_s_newsletter_tl (SLOW!!!)
$sql = $adb->datadict->RenameColumnSQL('tbl_s_newsletter_tl', 'linkid','trackid', 'trackid I(19)');
if ($sql && in_array('linkid', $adb->getColumnNames('tbl_s_newsletter_tl'))) {
	$adb->datadict->ExecuteSQLArray($sql);
}

// tabella tbl_s_newsletter_links
$schema_table =
'<schema version="0.3">
	<table name="tbl_s_newsletter_links">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="linkid" type="R" size="19">
			<KEY/>
		</field>
		<field name="newsletterid" type="I" size="19"/>
		<field name="url" type="C" size="1000"/>
		<field name="forward" type="C" size="1000" />
		<index name="tbl_s_nl_links_nlid">
			<col>newsletterid</col>
		</index>
		<index name="tbl_s_nl_links_url">
			<col>url</col>
		</index>
	</table>
</schema>';
if(!Vtiger_Utils::CheckTable('tbl_s_newsletter_links')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
	// create seq_table
	$linkid = $adb->getUniqueID('tbl_s_newsletter_links');
	// update seq
	$res = $adb->query('select max(trackid) as mlinkid from tbl_s_newsletter_tl');
	if ($res) {
		$m = intval($adb->query_result_no_html($res, 0, 'mlinkid'));
		$adb->pquery('update tbl_s_newsletter_links_seq set id = ?', array($m+1));
	}

	//$adb->query('truncate table tbl_s_newsletter_links');

	// copy links to the new table (slow)
	// don't do a global insert...select -> too slow
	$res = $adb->query("select distinct newsletterid from tbl_s_newsletter_tl");
	if ($res) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$nlid = $row['newsletterid'];
			// CAST is for MS SQL Server
			$res2 = $adb->pquery(
				"insert into tbl_s_newsletter_links (linkid, newsletterid, url, forward)
				select min(trackid) as trackid, newsletterid, url, CAST(forward as CHAR(400)) from tbl_s_newsletter_tl where newsletterid = ? group by url, CAST(forward as CHAR(400))",
				array($nlid)
			);
		}
	}
}

// delete old unclicked links (A LOT!!)
$adb->query('delete from tbl_s_newsletter_tl where clicked = 0');

// add linkurl id + index
addColumnToTable('tbl_s_newsletter_tl', 'linkurlid', 'I(11)');
$sql = $adb->datadict->CreateIndexSQL('tbl_s_nl_linkurlid_idx', 'tbl_s_newsletter_tl', 'linkurlid');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// populate linkurlid
if (in_array('url', $adb->getColumnNames('tbl_s_newsletter_tl'))) {
	$res = $adb->query(
		'select tl.trackid, l.linkid
		from tbl_s_newsletter_tl tl
		inner join tbl_s_newsletter_links l on l.newsletterid = tl.newsletterid and l.url = tl.url');
	if ($res) {
		while ($row = $adb->FetchByAssoc($res, -1, false)) {
			$adb->pquery('update tbl_s_newsletter_tl set linkurlid = ? where trackid = ?', array($row['linkid'], $row['trackid']));
		}
	}
}

// drop columns for urls
if (in_array('url', $adb->getColumnNames('tbl_s_newsletter_tl'))) {
	$sql = $adb->datadict->DropColumnSQL('tbl_s_newsletter_tl','url');
	$adb->datadict->ExecuteSQLArray($sql);
}
if (in_array('forward', $adb->getColumnNames('tbl_s_newsletter_tl'))) {
	$sql = $adb->datadict->DropColumnSQL('tbl_s_newsletter_tl','forward');
	$adb->datadict->ExecuteSQLArray($sql);
}

// add column for json values
addColumnToTable('tbl_s_newsletter_queue', 'fieldvalues', 'X');

// table for sent email templates
$schema_table =
'<schema version="0.3">
	<table name="tbl_s_newsletter_tpl">
		<opt platform="mysql">ENGINE=InnoDB</opt>
		<field name="tplid" type="R" size="19">
			<KEY/>
		</field>
		<field name="newsletterid" type="R" size="19" />
		<field name="datesent" type="T">
			<DEFAULT value="0000-00-00 00:00:00"/>
		</field>
		<field name="templatename" type="C" size="200"/>
		<field name="subject" type="C" size="200" />
		<field name="description" type="X"/>
		<field name="body" type="X"/>
		<field name="fields" type="X"/>
		<index name="tbl_s_nl_tpl_nlid_idx">
			<col>newsletterid</col>
		</index>
		<index name="tbl_s_nl_tpl_date_idx">
			<col>datesent</col>
		</index>
	</table>
</schema>';
if (!Vtiger_Utils::CheckTable('tbl_s_newsletter_tpl')) {
	$schema_obj = new adoSchema($adb->database);
	$schema_obj->ExecuteSchema($schema_obj->ParseSchemaString($schema_table));
}

// add button to see email template
$nlModule = Vtiger_Module::getInstance('Newsletter');
$nlModule->addLink('DETAILVIEWBASIC', 'LBL_PREVIEW_MAIL_BUTTON', "javascript:previewNewsletter(\$RECORD\$);", 'modules/Newsletter/src/preview_mail.png');

// add related for newsletter
$nlmods = array('Accounts', 'Leads', 'Contacts');
foreach ($nlmods as $nlmod) {
	$res = $adb->pquery("select * from {$table_prefix}_relatedlists where name = ? and tabid = ?", array('get_newsletter_emails', getTabid($nlmod)));
	if ($res && $adb->num_rows($res) == 0) {
		$otherModule = Vtiger_Module::getInstance($nlmod);
		$otherModule->setRelatedList($nlModule, 'Newsletter Emails', Array(), 'get_newsletter_emails');
	}
}

// add eventually missing index on crmentity (SLOW!!)
$sql = $adb->datadict->CreateIndexSQL('crmentity_setype_idx', $table_prefix.'_crmentity', 'setype');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// add index for crmv_squirrelmailrel
$sql = $adb->datadict->CreateIndexSQL('crmv_squirrelmailrel_mail_idx', 'crmv_squirrelmailrel', 'mail_id');
if ($sql) @$adb->datadict->ExecuteSQLArray($sql);

// rebuild templates and save them
/* steps -> ABBANDONATO
 * 1. for each sent newsletter
* 2.	get the template (may be obsolete)
* 3. 	get the body of 2 sent newsletter
* 4.	diff the content/subject to find out positions of tags
* 5.	if they are the same as the tags in the template, or the same number (AND delta size < 10%), consider the template valid -> use its tags
* 6.	otherwise consider the template as obsolete and do the following
* 7.		for each email sent (starting from the ones linked to less modified entities), extract the text that replaced the tags, and compare it with each field in the module to fine out the fieldnames
* 				if there are at least 5 emails that have the same fields -> use those fields
* 				else -> failure to retrieve fields -> end
* 8.	now we have the fieldnames
* 9.	for each sent email,
* 			if the entity was modified after the email was sent:
* 				get the body, get the text in tags positions, and use that as fieldvalues
* 			else
	* 				use actual entity field values
* 			save fieldnames/fieldvalues into each newsletter/mail sent
* 10.	end
*
* NEW ALGORITHM
* 1. for each sent newsletter
* 2	check if string differs less than 10%
* 3. 	if so get a random sent template-> use it as template
*/

// get newsletter with at least 1 sent email
$sent_nl = array();
$res = $adb->query(
	"select tbl_s_newsletter_queue.newsletterid
		from {$table_prefix}_newsletter
		inner join tbl_s_newsletter_queue on {$table_prefix}_newsletter.newsletterid = tbl_s_newsletter_queue.newsletterid
	where tbl_s_newsletter_queue.status = 'Sent'
	group by tbl_s_newsletter_queue.newsletterid having count(crmid) > 0"
	);
if ($res) {
	while ($row = $adb->FetchByAssoc($res, -1, false)) $sent_nl[] = $row['newsletterid'];
}


foreach ($sent_nl as $nlid) {
	// get newsletter info
	$res = $adb->pquery("select * from {$table_prefix}_newsletter where newsletterid = ?", array($nlid));
	if ($res) {
		$nlinfo = $adb->FetchByAssoc($res, -1, false);
		// get the template
		if ($nlinfo['templateemailid'] > 0) {
			$res = $adb->pquery("select * from {$table_prefix}_emailtemplates where templateid = ?", array($nlinfo['templateemailid']));
			$tplinfo = $adb->FetchByAssoc($res, -1, false);
			/*if ($tplinfo['subject'] || $tplinfo['body']) {
				$tplDiff['subject'] = findTplTags($tplinfo['subject']);
			$tplDiff['body'] = findTplTags($tplinfo['body']);
			}*/

		}
	} else continue;

	// get 2 newsletter body (2 query because mysql uses the wrong indexe)
	$res = $adb->limitPQuery("select emailid
		from tbl_s_newsletter_queue q
		inner join {$table_prefix}_emaildetails on idlists = ".$adb->sql_concat(array('q.newsletterid', "'@|'", 'q.crmid', "'@|'"))."
		where q.newsletterid = ?",
		0, 30,
		array($nlid)
	);
	$bodies = array();
	$subjects = array();
	while ($row = $adb->FetchByAssoc($res, -1, false)) {
		// get description
		$sentbody = '';
		if ($row['emailid'] > 0) {
			$res2 = $adb->pquery("select description, subject from {$table_prefix}_crmentity c inner join {$table_prefix}_activity a on a.activityid = c.crmid where c.crmid = ? and c.description is not null and c.description != ''", array($row['emailid']));
			if ($res2) {
				$sentbody = $adb->query_result_no_html($res2, 0, 'description');
				$sentsubject = $adb->query_result_no_html($res2, 0, 'subject');
				if (!empty($sentbody) && !empty($sentsubject)) {
					$subjects[] = $sentsubject;
					$bodies[] = $sentbody;
					if (count($subjects) >= 2) break;
				}
			}

		}
	}

	// roughly check for difference:

	$useTemplate = false;

	// save description
	if (count($bodies) >= 1) {
		$threshold = 0.1; // 10%

		// since this function has a limit of 255 chars, use the beginning and the end of the string
		$dist = levenshtein(substr($bodies[0], -200), substr($tplinfo['body'], -200)) +
			levenshtein(substr($bodies[0], 0, 200), substr($tplinfo['body'], 0, 200));
		if ((400-$dist)/400 > $threshold) {
			// use sent email
			$useTemplate = false;
		} else {
			// use template
			$useTemplate = true;
		}

		if ($useTemplate && !empty($tplinfo)) {
			$tplname = $tplinfo['templatename'];
			$subj = $tplinfo['subject'];
			$body = $tplinfo['body'];
			$desc = $tplinfo['description'];
		} else {
			$tplname = 'Rebuilt during update';
			$subj = $subjects[0];
			$body = $bodies[0];
			$desc = '';
		}

		$res = $adb->pquery("select tplid from tbl_s_newsletter_tpl where newsletterid = ?", array($nlid));
		if ($res && $adb->num_rows($res) > 0) {
			// update
			$tplid = $adb->query_result($res, 0, 'tplid');
			$adb->pquery("update tbl_s_newsletter_tpl set templatename = ?, subject = ? where tplid = ?", array($tplname, $subj, $tplid));
			$adb->updateClob('tbl_s_newsletter_tpl','description',"tplid=$tplid",$desc);
			$adb->updateClob('tbl_s_newsletter_tpl','body',"tplid=$tplid",$body);
		} else {
			// insert
			$tplid = $adb->getUniqueId('tbl_s_newsletter_tpl');
			$adb->pquery("insert into tbl_s_newsletter_tpl (tplid, newsletterid, templatename, subject) values (?, ?, ?, ?)", array($tplid, $nlid, $tplname, $subj));
			$adb->updateClob('tbl_s_newsletter_tpl','description',"tplid=$tplid",$desc);
			$adb->updateClob('tbl_s_newsletter_tpl','body',"tplid=$tplid",$body);
		}

	}
}

echo "<br/>Update completed.<br/><b>To migrate saved Emails to the new Messages module, you need to manually execute the script modules/Update/changes/722_723emails.php (might take a long time)</b><br>\n";

?>