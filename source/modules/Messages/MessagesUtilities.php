<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@44037 crmv@50745 */

require_once('modules/Messages/MessagesCore.php');

class MessagesUtilities extends MessagesCore {
	
	protected static $mail;		// Resource
	protected static $protocol;	// Resource protocol
	
	// POP3 Cron configuration
	public $pop3_cron_limit = 20;		// num of messages processed by pop3 configuration
	
	public $filterFields = array();
	protected static $filterFieldsCache = null;
	
	protected static $layout = array();
	
	function __construct() {
		parent::__construct();
		
		// use a cache, usefule when many instances of messages are created
		if (is_null(self::$filterFieldsCache)) {
			self::$filterFieldsCache = array(
				'from'=>array('label'=>getTranslatedString('From','Messages'),'fields'=>array('mfrom','mfrom_n','mfrom_f')),
				'to'=>array('label'=>getTranslatedString('To','Messages'),'fields'=>array('mto','mto_n','mto_f')),
				'cc'=>array('label'=>getTranslatedString('Cc','Messages'),'fields'=>array('mcc','mcc_n','mcc_f')),
				'to_or_cc'=>array('label'=>getTranslatedString('To','Messages').' '.getTranslatedString('LBL_OR').' '.getTranslatedString('Cc','Messages'),'fields'=>array('mto','mto_n','mto_f','mcc','mcc_n','mcc_f')),
				'subject'=>array('label'=>getTranslatedString('Subject','Messages'),'fields'=>array('subject')),
				'body'=>array('label'=>getTranslatedString('Body','Messages'),'fields'=>array('description')),
			);
		}
		$this->filterFields = self::$filterFieldsCache;
	}
	
	// Account
	function getConfiguredAccounts() {
		global $adb, $table_prefix;
		$accounts = array();
		$result = $adb->pquery("SELECT * FROM {$table_prefix}_systems WHERE server_type = ? order by id",array('email_imap'));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)){
				$accounts[] = array(
					'account'=>$row['id'],
					'label'=>$row['server'],
					'account_type'=>$row['account'],
					'server'=>$row['server'],
					'port'=>$row['server_port'],
					'ssl'=>$row['ssl_tls'],
					'domain'=>$row['domain'],
				);
			}
		}
		return $accounts;
	}
	function getAvailableAccounts() {
		global $adb, $table_prefix;
		//$focusEmails = CRMEntity::getInstance('Emails');
		$accounts = array();
		$accounts[] = array(
			'account'=>'',
			'label'=>getTranslatedString('Select'),
//			'server'=>$focusEmails->default_account['imap']['Other']['server'],
//			'port'=>$focusEmails->default_account['imap']['Other']['server_port'],
//			'ssl'=>$focusEmails->default_account['imap']['Other']['ssl_tls'],
//			'domain'=>$focusEmails->default_account['imap']['Other']['domain'],
		);
		$accounts = array_merge($accounts,$this->getConfiguredAccounts());
		$accounts[] = array(
			'account'=>'Gmail',
			'label'=>'Gmail',
//			'server'=>$focusEmails->default_account['imap']['Gmail']['server'],
//			'port'=>$focusEmails->default_account['imap']['Gmail']['server_port'],
//			'ssl'=>$focusEmails->default_account['imap']['Gmail']['ssl_tls'],
//			'domain'=>$focusEmails->default_account['imap']['Gmail']['domain'],
		);
		$accounts[] = array(
			'account'=>'Yahoo!',
			'label'=>'Yahoo!',
//			'server'=>$focusEmails->default_account['imap']['Yahoo!']['server'],
//			'port'=>$focusEmails->default_account['imap']['Yahoo!']['server_port'],
//			'ssl'=>$focusEmails->default_account['imap']['Yahoo!']['ssl_tls'],
//			'domain'=>$focusEmails->default_account['imap']['Yahoo!']['domain'],
		);
		$accounts[] = array(
			'account'=>'Custom',
			'label'=>getTranslatedString('Custom','Messages'),
//			'server'=>$focusEmails->default_account['imap']['Other']['server'],
//			'port'=>$focusEmails->default_account['imap']['Other']['server_port'],
//			'ssl'=>$focusEmails->default_account['imap']['Other']['ssl_tls'],
//			'domain'=>$focusEmails->default_account['imap']['Other']['domain'],
		);
		return $accounts;
	}

	// crmv@114260
	function getAvailableSmtpAccounts() {
		$accounts = array();
		$accounts[] = array(
			'account'=>'',
			'label'=>'Default',
		);
		$accounts[] = array(
			'account'=>'Gmail',
			'label'=>'Gmail',
		);
		$accounts[] = array(
			'account'=>'Yahoo!',
			'label'=>'Yahoo!',
		);
		$accounts[] = array(
			'account'=>'Custom',
			'label'=>getTranslatedString('Custom','Messages'),
		);
		return $accounts;
	}

	function getUserAccounts($userid='',$id='') {
		if (empty($userid)) {
			global $current_user;
			$userid = $current_user->id;
		}
		global $adb, $table_prefix;
		$accounts = array();
		
		//crmv@55263
		if($adb->isOracle()){
			$join = "LEFT JOIN {$table_prefix}_systems ON cast({$table_prefix}_systems.id as varchar2(50)) = ma.account";
		//crmv@60402
		}elseif($adb->isMssql()){
			$join = "LEFT JOIN {$table_prefix}_systems ON cast({$table_prefix}_systems.id as varchar(50)) = ma.account";
		//crmv@60402e
		} else {
			$join = "LEFT JOIN {$table_prefix}_systems ON {$table_prefix}_systems.id = ma.account";
		}
		//crmv@55263e
		$query = "SELECT
						ma.id,
						ma.account,
						username,
						email,
						password,
						main,
						description,
						signature,
						CASE WHEN ma.account = 'Custom' THEN ma.server ELSE {$table_prefix}_systems.server END AS server,
						CASE WHEN ma.account = 'Custom' THEN ma.port ELSE {$table_prefix}_systems.server_port END AS port,
						CASE WHEN ma.account = 'Custom' THEN ma.ssl_tls ELSE {$table_prefix}_systems.ssl_tls END AS ssl_tls,
						CASE WHEN ma.account = 'Custom' THEN ma.domain ELSE {$table_prefix}_systems.domain END AS domain,
						ma.smtp_account, ma.smtp_server, ma.smtp_port, ma.smtp_username, ma.smtp_password, ma.smtp_auth
					FROM {$table_prefix}_messages_account ma {$join} WHERE userid = ?";

		$params = array($userid);
		if ($id != '') {
			$query .= " AND ma.id = ?";
			$params[] = $id;
		}
		$query .= " ORDER BY ma.description ASC"; // crmv@73256
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			$focusEmails = CRMEntity::getInstance('Emails');
			while($row=$adb->fetchByAssoc($result)){
				require_once('include/utils/encryption.php');
				$de_crypt = new Encryption();
				$row['password'] = $de_crypt->decrypt($row['password']);
				if ($row['smtp_password']) {
					$row['smtp_password'] = $de_crypt->decrypt($row['smtp_password']);
				}
				if (empty($row['description'])) $row['description'] = $row['username'];
				if (in_array($row['account'],array('Gmail','Yahoo!'))) {
					$row['server'] = $focusEmails->default_account['imap'][$row['account']]['server'];
					$row['port'] = $focusEmails->default_account['imap'][$row['account']]['server_port'];
					$row['ssl_tls'] = $focusEmails->default_account['imap'][$row['account']]['ssl_tls'];
					$row['domain'] = $focusEmails->default_account['imap'][$row['account']]['domain'];
				}
				// get the values from the server templates
				if (in_array($row['smtp_account'],array('Gmail','Yahoo!'))) {
					$row['smtp_server'] = $focusEmails->default_account['smtp'][$row['smtp_account']]['server'];
					$row['smtp_port'] = $focusEmails->default_account['smtp'][$row['smtp_account']]['server_port'];
					$row['smtp_auth'] = ($focusEmails->default_account['smtp'][$row['smtp_account']]['smtp_auth'] == 'checked' ? 'true' : 'false');
					$row['smtp_username'] = $row['username'];
					$row['smtp_password'] = $row['password'];
				} elseif (empty($row['smtp_account'])) {
					// nothing configured, set some defaults
					$row['smtp_server'] = $focusEmails->default_account['smtp']['Other']['server'];
					$row['smtp_port'] = $focusEmails->default_account['smtp']['Other']['server_port'];
					$row['smtp_auth'] = ($focusEmails->default_account['smtp']['Other']['smtp_auth'] == 'checked' ? 'true' : 'false');
				}
				
				$accounts[] = $row;
			}
		}
		return $accounts;
	}
	// crmv@114260e

	function getUserAccountsPicklist($account,$jsfunction) {
		require_once('Smarty_setup.php');
		$smarty = new vtigerCRM_Smarty();
		$smarty->assign('ACCOUNTS',$this->getUserAccounts());
		$smarty->assign('SEL_ACCOUNT',$account);
		$smarty->assign('JS_FUNCT',$jsfunction);
		return $smarty->fetch('modules/Messages/Settings/AccountsPicklist.tpl');
	}
	function getMainUserAccount($userid='') {
		if (empty($userid)) {
			global $current_user;
			$userid = $current_user->id;
		}
		global $adb, $table_prefix;
		//crmv@55263
		if($adb->isOracle()){
			$join = "LEFT JOIN {$table_prefix}_systems ON cast({$table_prefix}_systems.id as varchar2(50)) = {$table_prefix}_messages_account.account";
		} else {
			$join = "LEFT JOIN {$table_prefix}_systems ON {$table_prefix}_systems.id = {$table_prefix}_messages_account.account";
		}
		//crmv@55263e
		$query = "SELECT
						{$table_prefix}_messages_account.id,
						{$table_prefix}_messages_account.account,
						username,
						email,
						password,
						main,
						description,
						CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.server ELSE {$table_prefix}_systems.server END AS server,
						CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.port ELSE {$table_prefix}_systems.server_port END AS port,
						CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.ssl_tls ELSE {$table_prefix}_systems.ssl_tls END AS ssl_tls,
						CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.domain ELSE {$table_prefix}_systems.domain END AS domain
					FROM {$table_prefix}_messages_account {$join} WHERE userid = ? and main = ?
					ORDER BY {$table_prefix}_messages_account.account";
		$result = $adb->pquery($query,array($userid,1));
		if (!$result || $adb->num_rows($result) == 0) {
			$query = "SELECT
							{$table_prefix}_messages_account.id,
							{$table_prefix}_messages_account.account,
							username,
							email,
							password,
							main,
							description,
							CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.server ELSE {$table_prefix}_systems.server END AS server,
							CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.port ELSE {$table_prefix}_systems.server_port END AS port,
							CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.ssl_tls ELSE {$table_prefix}_systems.ssl_tls END AS ssl_tls,
							CASE WHEN {$table_prefix}_messages_account.account = 'Custom' THEN {$table_prefix}_messages_account.domain ELSE {$table_prefix}_systems.domain END AS domain
						FROM {$table_prefix}_messages_account {$join} WHERE userid = ?
						ORDER BY {$table_prefix}_messages_account.account";
			$result = $adb->pquery($query,array($userid));
		}
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)){
				require_once('include/utils/encryption.php');
				$de_crypt = new Encryption();
				$row['password'] = $de_crypt->decrypt($row['password']);
				if (empty($row['description'])) $row['description'] = $row['username'];
				return $row;
			}
		}
		return false;
	}
	function saveAccount($id,$account,$username,$email='',$password,$main,$description='',$server='',$port='',$ssl_tls='',$domain='',$signature='') {
		global $current_user, $adb, $table_prefix;
		require_once('include/utils/encryption.php');
		$en_crypt = new Encryption();
		if(!empty($password)) $password = $en_crypt->encrypt($password);
		if (empty($email)) $email = $username;
		if ($account != 'Custom') {
			$server = '';
			$port = '';
			$ssl_tls = '';
			$domain = '';
		}
		if ($id === '') {
			$id = $adb->getUniqueID($table_prefix."_messages_account");
			$adb->pquery("insert into {$table_prefix}_messages_account (id,userid,account,username,email,password,main,description,server,port,ssl_tls,domain,signature) values (?,?,?,?,?,?,?,?,?,?,?,?,?)",
				array($id,$current_user->id,$account,$username,$email,$password,$main,$description,$server,$port,$ssl_tls,$domain,$signature));
			$this->syncAll($current_user->id, $id);	//crmv@51862
		} else {
			//crmv@43764
			if ($password == '') {
				$result = $adb->pquery("select password from {$table_prefix}_messages_account where id = ? and userid = ?",array($id,$current_user->id));
				if ($result && $adb->num_rows($result) > 0) {
					$password = $adb->query_result($result,0,'password');
				}
			}
			//crmv@43764e
			$adb->pquery("update {$table_prefix}_messages_account
				set account = ?, username = ?, email = ?, password = ?, main = ?, description = ?, server = ?, port = ?, ssl_tls = ?, domain = ?, signature = ?
				where id = ? and userid = ?",array($account,$username,$email,$password,$main,$description,$server,$port,$ssl_tls,$domain,$signature,$id,$current_user->id));
		}
		//crmv@57983
		if ($main == 1) {
			$adb->pquery("update {$table_prefix}_messages_account set main = 0 where userid = ? and id <> ?",array($current_user->id,$id));
		}
		//crmv@57983e
		return $id;	//crmv@46468
	}

	// crmv@107655
	function deleteAccount($accountid) {
		global $adb, $table_prefix, $current_user;
		
		// remove the autoload message if it belongs to this account
		if ($_SESSION['autoload_message']) {
			$retrieve = $this->retrieve_entity_info($_SESSION['autoload_message'], 'Messages', false);
			if ($accountid == $this->column_fields['account']) {
				unset($_SESSION['autoload_message']);
			}
		}
		
		// delete the rows
		$adb->pquery("delete from {$table_prefix}_messages_account where userid = ? and id = ?",array($current_user->id,$accountid));
		$adb->pquery("delete from {$table_prefix}_messages_sfolders where userid = ? and accountid = ?",array($current_user->id,$accountid));	//crmv@48159
		$adb->pquery("delete from {$table_prefix}_messages_folders where userid = ? and accountid = ?",array($current_user->id,$accountid));	//crmv@48159
		$adb->pquery("delete from {$table_prefix}_messages_sync_all where userid = ? and accountid = ?",array($current_user->id,$accountid));	//crmv@51862
		$adb->pquery("delete from {$table_prefix}_messages_cron_uid where userid = ? and accountid = ?",array($current_user->id,$accountid));
		$adb->pquery("delete from {$table_prefix}_messages_cron_uidi where userid = ? and accountid = ?",array($current_user->id,$accountid));
		
		// set the main flag again
		$result = $adb->pquery("SELECT MIN(id) AS id FROM {$table_prefix}_messages_account WHERE userid = ?",array($current_user->id));
		if ($result && $adb->num_rows($result) > 0) {
			$id = $adb->query_result_no_html($result,0,'id');
			$adb->pquery("update {$table_prefix}_messages_account set main = 1 where id = ?",array($id));
		}
	}
	
	function canUserDeleteAccount($userid, $accountid) {
		global $current_user, $adb, $table_prefix;
		if (!$userid) $userid = $current_user->id;
		
		// a user can delete an account only if it belongs to him
		$res = $adb->pquery("SELECT id FROM {$table_prefix}_messages_account WHERE id = ? AND userid = ?", array($accountid, $userid));
		return ($res && $adb->num_rows($res) == 1);
	}
	// crmv@107655e

	// crmv@114260
	/**
	 * Set the SMTP config for the specified account
	 */
	public function setAccountSmtp($id, $account, $server, $port, $username, $password, $auth) {
		global $current_user, $adb, $table_prefix;
		require_once('include/utils/encryption.php');
		
		$en_crypt = new Encryption();
		if(!empty($password)) $password = $en_crypt->encrypt($password);
		
		// get other variables from main account
		$res = $adb->pquery("SELECT username, password, ssl_tls FROM {$table_prefix}_messages_account WHERE id = ?", array($id));
		$info = $adb->fetchByAssoc($res, -1, false);

		// for preconfigured accounts, take the settings from the preconfigured templates
		if ($account != 'Custom') {
			$server = '';
			$port = 0;
			$auth = 'true';
			$username = '';
			$password = '';
		} else {
			if ($password == '') {
				$password = $info['password'];
			}
			if ($auth == true) {
				$auth = 'true';
			} elseif ($auth == false) {
				$auth = 'false';
			}
		}
		
		$params = array(
			$account,
			$server,
			$port ?: 0,
			$username,
			$password,
			$auth,
			$id,
		);
		$adb->pquery("UPDATE {$table_prefix}_messages_account SET smtp_account = ?, smtp_server = ?, smtp_port = ?, smtp_username = ?, smtp_password = ?, smtp_auth = ? WHERE id = ?", $params);
	}
	
	/**
	 * Clear the SMTP config for the specified account
	 */
	public function clearAccountSmtp($id) {
		global $adb, $table_prefix;
		
		$adb->pquery("UPDATE {$table_prefix}_messages_account SET smtp_account = '', smtp_server = NULL, smtp_port = 0, smtp_username = NULL, smtp_password = NULL, smtp_auth = NULL WHERE id = ?", array($id));
	}
	
	/**
	 * Check if the account has the smtp server configured
	 */
	public function hasSmtpAccount($id) {
		global $adb, $table_prefix;
		
		$res = $adb->pquery("SELECT smtp_account FROM {$table_prefix}_messages_account WHERE id = ?", array($id));
		$smtpAccount = $adb->query_result_no_html($res, 0, 'smtp_account');
		return !empty($smtpAccount);
	}
	
	/**
	 * Get the smtp configuration for this account (null if the account doesn't have smtp configured)
	 */
	public function getSmtpConfig($id) {
		global $adb, $table_prefix;
		
		$config = null;
		$res = $adb->pquery("SELECT userid FROM {$table_prefix}_messages_account WHERE id = ? and smtp_account IS NOT NULL and smtp_account != ''", array($id));
		if ($res && $adb->num_rows($res) > 0) {
			$userid = $adb->query_result_no_html($res, 0, 'userid');
			$accounts = $this->getUserAccounts($userid, $id);
			if ($accounts[0]) {
				$keys = array('smtp_server', 'smtp_port', 'smtp_username','smtp_password', 'smtp_auth');
				$config = array_intersect_key($accounts[0], array_flip($keys));
			}
		}
		
		return $config;
	}
	// crmv@114260e

	//crmv@51862
	function syncAll($userid, $accountid) {
		global $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_sync_all where userid = ? and accountid = ?",array($userid, $accountid));
		$adb->pquery("insert into {$table_prefix}_messages_sync_all (userid, accountid) values (?,?)",array($userid, $accountid));
	}
	//crmv@51862e
	// Account end
	
	//crmv@46468
	function autoSetSpecialFolders($account) {
		$this->setAccount($account);
		$this->getZendMailStorageImap();
		$folder_list = $this->getFoldersList('');
		$folder_list = array_keys($folder_list);
		$specialFolders = $this->getSpecialFolders();
		foreach($specialFolders as $special_folder => $f) {
			if (in_array($special_folder,$folder_list)) {
				$specialFolders[$special_folder] = $special_folder;
			}
		}
		if (empty($specialFolders['INBOX']) || empty($specialFolders['Sent']) || empty($specialFolders['Drafts']) || empty($specialFolders['Trash'])) {
			return false;
		} else {
			$this->setSpecialFolders($specialFolders,$account);
			return true;
		}
	}
	//crmv@46468e
	
	// Filters
	function getFilters($account) {
		global $current_user, $adb, $table_prefix;
		$filters = array();
		$result = $adb->pquery("select sequence, filter_where, filter_what, filter_folder from {$table_prefix}_messages_filters where userid = ? and accountid = ? order by sequence",array($current_user->id,$account));
		if ($result && $adb->num_rows($result) > 0) {
			$i=0;
			while($row=$adb->fetchByAssoc($result)) {
				$filter = $row;
				$filter['edit'] = true;
				$filter['delete'] = true;
				if ($adb->num_rows($result) == 1) {
					$filter['move_up'] = false;
					$filter['move_down'] = false;
				} elseif ($i == 0) {
					$filter['move_up'] = false;
					$filter['move_down'] = true;
				} elseif ($i == $adb->num_rows($result)-1) {
					$filter['move_up'] = true;
					$filter['move_down'] = false;
				} else {
					$filter['move_up'] = true;
					$filter['move_down'] = true;
				}
				$filters[] = $filter;
				$i++;
			}
		}
		return $filters;
	}
	
	function setFilter($account,$where,$what,$folder,$sequence='') {
		global $current_user, $adb, $table_prefix;
		if ($sequence === '') {
			$result = $adb->pquery("select sequence from {$table_prefix}_messages_filters where userid = ? and accountid = ? order by sequence desc",array($current_user->id,$account));
			if ($result && $adb->num_rows($result) > 0) {
				$sequence = $adb->query_result($result,0,'sequence')+1;
			} else {
				$sequence = 0;
			}
			$adb->pquery("insert into {$table_prefix}_messages_filters (userid,accountid,sequence,filter_where,filter_what,filter_folder) values (?,?,?,?,?,?)",array($current_user->id,$account,$sequence,$where,$what,$folder));
		} else {
			$adb->pquery("update {$table_prefix}_messages_filters set filter_where = ?, filter_what = ?, filter_folder = ? where userid = ? and accountid = ? and sequence = ?",array($where,$what,$folder,$current_user->id,$account,$sequence));
		}
	}
	
	function deleteFilter($account,$sequence) {
		global $current_user, $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_filters where userid = ? and accountid = ? and sequence = ?",array($current_user->id,$account,$sequence));
	}
	
	function moveFilter($account,$sequence,$to) {
		global $current_user, $adb, $table_prefix;
		if ($to == 'up') {
			$result = $adb->pquery("select sequence from {$table_prefix}_messages_filters where userid = ? and accountid = ? and sequence < ? order by sequence desc",array($current_user->id,$account,$sequence));
			if ($result && $adb->num_rows($result) > 0) {
				$sequence1 = $adb->query_result($result,0,'sequence');
			}
		} elseif ($to == 'down') {
			$result = $adb->pquery("select sequence from {$table_prefix}_messages_filters where userid = ? and accountid = ? and sequence > ? order by sequence",array($current_user->id,$account,$sequence));
			if ($result && $adb->num_rows($result) > 0) {
				$sequence1 = $adb->query_result($result,0,'sequence');
			}
		}
		if (!empty($sequence1)) {
			$adb->pquery("update {$table_prefix}_messages_filters set sequence = ? where userid = ? and accountid = ? and sequence = ?",array(0,$current_user->id,$account,$sequence1));
			$adb->pquery("update {$table_prefix}_messages_filters set sequence = ? where userid = ? and accountid = ? and sequence = ?",array($sequence1,$current_user->id,$account,$sequence));
			$adb->pquery("update {$table_prefix}_messages_filters set sequence = ? where userid = ? and accountid = ? and sequence = ?",array($sequence,$current_user->id,$account,0));
		}
	}
	
	function applyFilters($messageId,&$filtered) {
		$specialFolders = $this->getSpecialFolders();
		if ($this->column_fields['folder'] == $specialFolders['INBOX']) {
			$filters = $this->getFilters($this->getAccount());
			foreach($filters as $filter) {
				foreach($this->filterFields[$filter['filter_where']]['fields'] as $fieldname) {
					if (stripos($this->column_fields[$fieldname],$filter['filter_what']) !== false) {
						$folder = $filter['filter_folder'];
						try {
							$this->selectFolder($this->column_fields['folder']);
						} catch (Exception $e) {
							$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
							continue;
						}
						try {
							self::$mail->moveMessage($messageId,$folder);
						} catch(Exception $e) {
							$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
							if ($e->getMessage() == 'cannot copy message, does the folder exist?') {
								continue;
							}
						}
						$filtered[$folder][] = $this->column_fields['xuid'];
						return true;
					}
				}
			}
		}
		return false;
	}
	
	function fetchFiltered($filtered) {
		if (!empty($filtered)) {
			foreach($filtered as $folder => $ids) {
				$this->fetchNews($folder,count($ids));
			}
		}
	}
	
	/* crmv@63113 */
	function scanNowFilters($account, &$errors=array()) {
		global $adb, $table_prefix, $current_user;
		$filtered = array();
		$this->setAccount($account);
		$this->getZendMailStorageImap();
		$specialFolders = $this->getSpecialFolders();
		$filters = $this->getFilters($account);
		$folder_list = $this->getFoldersList('');
		$folder_list = array_keys($folder_list);
		foreach($filters as $filter) {
			if (!in_array($filter['filter_folder'],$folder_list)) {
				$errors['folders_not_found'][] = $filter['filter_folder'];
				continue;
			}
			$query = "SELECT distinct messagesid FROM {$table_prefix}_messages
					INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid
					WHERE deleted = 0 AND account = ? AND smownerid = ? AND folder = ?";
			//crmv@53023
			$where = array();
			$fields = $this->filterFields[$filter['filter_where']]['fields'];
			foreach($fields as $field) {
				$where[] = "$field LIKE '%{$filter['filter_what']}%'";
			}
			$query .= ' AND ('.implode(' OR ',$where).')';
			//crmv@53023e
			$result = $adb->pquery($query,array($account,$current_user->id,$specialFolders['INBOX']));
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					$focus = CRMEntity::getInstance('Messages');
					$focus->id = $row['messagesid'];
					$focus->retrieve_entity_info($row['messagesid'], 'Messages');
					$focus->moveMessage($filter['filter_folder'],true);
					$filtered[$filter['filter_folder']][] = $row['messagesid'].' '.$filter['filter_what'];
				}
			}
		}
		$this->fetchFiltered($filtered);
		return $filtered;
	}
	// Filters end
	
	// Fetch POP3
	function getPop3() {
		global $current_user, $adb, $table_prefix;
		$list = array();
		$result = $adb->pquery("select * from {$table_prefix}_messages_pop3 where userid = ? order by id",array($current_user->id));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$tmp = $row;
				$tmp['edit'] = true;
				$tmp['delete'] = true;
				/*
				require_once('include/utils/encryption.php');
				$de_crypt = new Encryption();
				if(!empty($tmp['password'])) {
					$tmp['password'] = $de_crypt->decrypt($tmp['password']);
				}
				*/
				$list[] = $tmp;
			}
		}
		return $list;
	}
	
	function setPop3($server,$port,$username,$password,$secure,$account,$folder,$lmos,$active,$uidl,$id='') {
		global $current_user, $adb, $table_prefix;
		require_once('include/utils/encryption.php');
		$en_crypt = new Encryption();
		if(!empty($password)) {
			$password = $en_crypt->encrypt($password);
		}
		if ($id === '') {
			$id = $adb->getUniqueID($table_prefix."_messages_pop3");
			$adb->pquery("insert into {$table_prefix}_messages_pop3 (id,userid,server,port,username,password,secure,accountid,folder,lmos,active,uidl) values (?,?,?,?,?,?,?,?,?,?,?,?)",array($id,$current_user->id,$server,$port,$username,$password,$secure,$account,$folder,$lmos,$active,$uidl));
		} else {
			//crmv@43764
			if ($password == '') {
				$result = $adb->pquery("select password from {$table_prefix}_messages_pop3 where id = ? and userid = ?",array($id,$current_user->id));
				if ($result && $adb->num_rows($result) > 0) {
					$password = $adb->query_result($result,0,'password');
				}
			}
			//crmv@43764e
			$adb->pquery("update {$table_prefix}_messages_pop3 set server = ?, port = ?, username = ?, password = ?, secure = ?, accountid = ?, folder = ?, lmos = ?, active = ?, uidl = ? where userid = ? and id = ?",array($server,$port,$username,$password,$secure,$account,$folder,$lmos,$active,$uidl,$current_user->id,$id));
		}
	}
	
	function deletePop3($id) {
		global $current_user, $adb, $table_prefix;
		$result = $adb->pquery("delete from {$table_prefix}_messages_pop3 where userid = ? and id = ?",array($current_user->id,$id));
		if ($result && $adb->getAffectedRowCount($result) > 0) {
			$adb->pquery("delete from {$table_prefix}_messages_pop3_uids where pop3 = ?",array($id));
		}
	}
	
	function getPop3SavedUids($id) {
		global $adb, $table_prefix;
		$uids = array();
		$result = $adb->pquery("select uid from {$table_prefix}_messages_pop3_uids where pop3 = ?",array($id));
		if ($result && $adb->num_rows($result)) {
			while($row=$adb->fetchByAssoc($result)) {
				$uids[] = $row['uid'];
			}
		}
		return $uids;
	}
	
	function getPop3ServerUids($pop3) {
		return $pop3->getUniqueId();
	}
	
	function bin_search($needle, $haystack, $low=0, $high=null) {
		// default last value
		if (is_null($high)) {
			$high = count($haystack) - 1;
		}
		// search
		while ($low <= $high) {
			$mid = floor(($low + $high) / 2);
			$value = $haystack[$mid];
			$value = substr($value,0,strpos($value,'.'));
			if ($value == $needle) {
				// found; return current position
				return $mid;
			}
			if ($value < $needle) {
				// search in higher values
				$low = $mid + 1;
			} else {
				// search in lower values
				$high = $mid - 1;
			}
		}
		// not found, return last minor element
		return $high;
	}
	
	function fetchPop3() {
		global $current_user, $adb, $table_prefix;
		$result = $adb->query("select * from {$table_prefix}_messages_pop3 where active = 1 order by userid, id");
		if ($result && $adb->num_rows($result) > 0) {
			$this->loadZendFramework();
			while($row=$adb->fetchByAssoc($result)) {
				if ($userid != $row['userid'] || $account != $row['accountid']) {
					$userid = $row['userid'];
					$account = $row['accountid'];
					$current_user = CRMEntity::getInstance('Users');
					$current_user->id = $userid;
					$current_user->retrieveCurrentUserInfoFromFile($userid);
					$this->resetMailResource();
					$this->setAccount($account);
					$this->getZendMailStorageImap($userid);
				}
				$folder_root = self::$mail->getFolders();
				$folders_it = new RecursiveIteratorIterator($folder_root,RecursiveIteratorIterator::SELF_FIRST);
				foreach ($folders_it as $f) {
					$folders[] = $f->getGlobalName();
				}
				if (!in_array($row['folder'],$folders)) {	// continue if folder don't exists
					continue;
				}
				$password = $row['password'];
				require_once('include/utils/encryption.php');
				$de_crypt = new Encryption();
				if(!empty($password)) {
					$password = $de_crypt->decrypt($password);
				}
				$pop3 = new Zend\Mail\Storage\Pop3(array(
					'host' => $row['server'],
					'port' => $row['port'],
					'user' => $row['username'],
					'password' => $password,
					'ssl' => $row['secure']
				));				
				$cache_ids = $this->getPop3SavedUids($row['id']);
				$server_ids = $this->getPop3ServerUids($pop3);
				try {
					$lastid = $pop3->getNumberByUniqueId($row['uidl']);
				} catch (Zend\Mail\Storage\Exception\InvalidArgumentException $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					$find_dot = strpos($row['uidl'],'.');
					if ($find_dot !== false) {
						$numeric_uid = substr($row['uidl'],0,$find_dot);
						$lastid = $this->bin_search($numeric_uid,$server_ids);
					}
				}
				if (!empty($lastid)) {
					$pos = array_search($lastid, array_keys($server_ids));
					$server_ids = array_slice($server_ids, $pos+1, null, true);
				}
				$new_ids = array_diff($server_ids,$cache_ids);
				$new_ids = array_reverse($new_ids,true);	//scarico dai più recenti ai più vecchi
				if (!empty($this->pop3_cron_limit)) {
					$new_ids = array_slice($new_ids, 0, $this->pop3_cron_limit, true);
				}
				foreach($new_ids as $id => $uid) {
					$fetch = $this->fetchPop3Message($pop3,$row['id'],$row['folder'],$id,$uid);
					if ($fetch && $row['lmos'] != '1') {
						$pop3->removeMessage($id);
					}
				}
			}
		}
	}
	
	function fetchPop3Message($pop3,$pop3id,$folder,$id,$uid) {
		global $current_user, $adb, $table_prefix;
		$message = $pop3->getMessage($id);
		$message_str = $pop3->getRawHeader($id)."\r\n".$pop3->getRawContent($id)."\r\n";
		$date = date('d-M-Y H:i:s O',strtotime($message->getHeader('Date')->getFieldValue()));
		if (self::$protocol->append($folder,$message_str,null,$date)) {
            $adb->pquery("insert into {$table_prefix}_messages_pop3_uids (pop3,uid) values (?,?)",array($pop3id,$uid));
            return true;
        }
        return false;
	}
	// Fetch POP3 end
	
	function getLayoutSettings($userid='') {
		if (empty(self::$layout)) {
			if (empty($userid)) {
				global $current_user;
				$userid = $current_user->id;
			}
			global $adb, $table_prefix;
			$result = $adb->pquery("select * from {$table_prefix}_messages_layout where userid = ?",array($userid));
			if ($result && $adb->num_rows($result) > 0) {
				self::$layout = $adb->fetch_array($result);
			} elseif ($adb->num_rows($result) == 0) {
				// insert default values
				$adb->pquery("insert into {$table_prefix}_messages_layout (userid,list_descr_preview,thread) values (?,?,?)",array($userid,1,1));
			}
		}
		return self::$layout;
	}
	
	function saveLayoutSettings($params,$userid='') {
		if (empty($userid)) {
			global $current_user;
			$userid = $current_user->id;
		}
		if ($params['list_descr_preview'] == 1 || $params['list_descr_preview'] == 'on') $params['list_descr_preview'] = 1; else $params['list_descr_preview'] = 0;
		if ($params['thread'] == 1 || $params['thread'] == 'on') $params['thread'] = 1; else $params['thread'] = 0;
		global $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_layout where userid = ?",array($userid));
		$adb->pquery("insert into {$table_prefix}_messages_layout (userid,list_descr_preview,thread) values (?,?,?)",array($userid,$params['list_descr_preview'],$params['thread']));
	}
	
	//crmv@90390
	function checkMessage($userid, $accountid, $folder, $uid, $save=false, $print_message=true, $print_data=true) {
		global $current_user, $checkMessage;
		$checkMessage = true;
		$current_user->id = $userid;
		
		$this->setAccount($accountid);
		$this->getZendMailStorageImap($userid);
		$this->selectFolder($folder);
		
		$messageId = self::$mail->getNumberByUniqueId($uid);
		$message = self::$mail->getMessage($messageId);
		if ($print_message) { echo '<b>MESSAGE:</b><br /><pre>'; print_r($message); echo '</pre><br /><br />'; }
		
		/* crmv@57876	crmv@59094
		$memory_usage = memory_get_usage();
		try {
			$message_size = $message->getSize();
		} catch (Zend\Mail\Exception\RuntimeException $e) {
			$message_size = 0;
		}
		if ($message_size > $memory_usage) {
			echo '<b>MEMORIA ESAURITA</b> necessario:'.$message_size.', disponibile '.$memory_usage.'<br />';
		}
		crmv@57876e		crmv@59094e */

		$data = $this->getMessageData($message,$messageId);	//crmv@59094
		if ($print_data) { echo '<b>DATA</b><br /><pre>'; print_r($data); echo '</pre>'; }

		if ($save) {
			$this->saveCache(array($messageId=>$uid));
			$crmid = $this->getSavedMessages();
			echo '<b>SALVATO '.implode(',',$crmid).'</b>';
		}
	}
	//crmv@90390e
	
	//crmv@49480	crmv@58170	crmv@64306
	function imap2DbDate($date) {
		$date = str_replace(',',' ',$date);
		$date = preg_replace('/\s+/',' ',$date);
		$date_arr = explode(' ',$date);
		if (!is_numeric($date_arr[3]) && strpos($date_arr[3],':') !== false) {	// es. Tue Oct 07 14:48:16 CEST 2014
			foreach($date_arr as $k => $v) {
				if (is_numeric($v) && $v >= 1900 && $k > 3) {
					unset($date_arr[$k]);
					array_splice($date_arr, 3, 0, array($v));
					break;
				}
			}
			$date = implode(' ',$date_arr);
		}
		$date = trim(preg_replace('/\(+.*\)*/i','',$date));		// es. Tue, 10 Sep 2013 09:17:50 0200 (GMT+2)
		if (preg_match('/\s{1}[0-9]{4}$/i',$date,$matches)) {	// es. Tue, 10 Sep 2013 09:17:50 0200 : (0200 without +)
			$date = str_replace($matches[0],' +'.trim($matches[0]),$date);
		}
		$strtotime = strtotime($date);
		if (empty($strtotime)) {
			$pos = strrpos($date,' ');
			if ($pos !== false) $date = substr($date,0,$pos);
			$strtotime = strtotime($date);
		}
		($strtotime > time()) ? $date = date('Y-m-d') : $date = date('Y-m-d',$strtotime);
		$time = date('H:i:s',$strtotime);
		return $date.' '.$time;
	}
	//crmv@49480e	crmv@58170e	crmv@64306e
	
	//crmv@90390
	function logException($e,$file,$line,$method) {
		global $checkMessage;
		if ($checkMessage) {
			echo "Exception \"{$e->getMessage()}\" in {$method} ({$file}:{$line})<br>\n";
		}
	}
	//crmv@90390e
}
?>