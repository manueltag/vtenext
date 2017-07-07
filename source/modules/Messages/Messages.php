<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@48159 (diff rev 860-863) */
/* crmv@49395 (diff rev 889-893) */
/* crmv@51862 (diff rev 909-910) */

require_once('modules/Messages/MessagesRelationManager.php');
require_once('modules/Messages/src/Squirrelmail.php');
require_once('modules/Messages/Utils.php');	//crmv@49432
require_once('modules/Calendar/iCal/includeAll.php'); // crmv@68357

class Messages extends MessagesRelationManager {

	protected $folder;			// Selected folder
	private $saved_messages;
	protected $account;
	var $folderSeparator = '/';
	var $defaultSpecialFolders = array(
		'INBOX'=>'',
		'Drafts'=>'',
		'Sent'=>'',
		'Spam'=>'',
		//'Junk'=>'',
		'Trash'=>'',
	);
	var $fakeFolders = array('Shared','Flagged','Links','Outgoing');
	var $resetMailResource = false;
	var $other_contenttypes_attachment = array(
		'application/pgp-signature',
		'application/octet-stream',
		'application/pdf',
		//crmv@53651
		'message/delivery-status',
		'text/rfc822-headers',
		//crmv@53651e
		'text/calendar',
		'message/rfc822', //crmv@62340
	);
	var $ical_methods = array('REQUEST', 'REPLY'); // crmv@68357 : attachments with this method and contenttype text/calendar are invitations or replies
	var $list_max_entries_first_page = 50;	//crmv@46154
	var $list_max_entries_per_page = 10;	// navigation, fetch and append

	// IMAP Cron configuration
	var $messages_by_schedule = 20;			// max number of messages processed by cron Messages.service.php (0 = no limit)
	var $messages_by_schedule_inbox = 20;	// max number of messages processed by cron Inbox.service.php (0 = no limit)
	var $interval_schedulation = '15 days';	// '' = all (no temporary interval)
	var $max_message_cron_uid_attempts = 3;	//crmv@55450
	var $update_duplicates = false;			// crmv@57585 : if true in saveCache if I try to save a message already downloaded I update its informations. if false skip saving.
	
	var $fetchBodyInCron = 'yes';	// crmv@59094 : (string) yes | no | no_disposition_notification_to
	var $relatedEditButton = false;	//crmv@61173
	//crmv@62414
	var $view_image_supported_extensions = array('png','bmp','gif','jpeg','jpg','tiff','tif');	//crmv@91321 
	var $viewerJS_supported_extensions = array('pdf','odt','ods','ots','ott','otp'); 
	var $action_view_JSfunction_array = array(
		'eml'=>'ViewEML',
		'pdf'=>'ViewDocument',
		'odt'=>'ViewDocument',
		'ods'=>'ViewDocument',
		'ots'=>'ViewDocument',
		'ott'=>'ViewDocument',
		'otp'=>'ViewDocument',
		'png'=>'ViewImage',
		'bmp'=>'ViewImage',
		'gif'=>'ViewImage',
		'jpeg'=>'ViewImage',
		'jpg'=>'ViewImage',
		//crmv@91321
		'tiff'=>'ViewImage',
		'tif'=>'ViewImage',
		//crmv@91321e
	);
	//crmv@62414e
	//crmv@76756
	var $IMAPDebug = false;
	var $IMAPLogMaxSize = 5242880;	// 5MB per logfile (more or less)
	var $IMAPLogDir = 'logs/imap/';
	//crmv@76756e
	var $inline_image_supported_extensions = array('jpeg','jpg','gif','png','apng','svg','bmp','ico','tiff','tif');	//crmv@80250 crmv@91321
	var $inline_image_convertible_extensions = array('tiff','tif');	//crmv@91321
	var $view_related_messages_recipients = false;	//crmv@86301

	//crmv@87055
	var $search_intervals = array(
		array('','-2 months'),
		array('-2 months','-6 months'),
		array('-6 months','-1 year'),
		array('-1 year','-2 years'),
		array('-2 years',''),
	);
	//crmv@87055e

	function __construct() {
		parent::__construct();
	}

	function loadZendFramework() {
		global $root_directory;
		set_include_path(get_include_path() . PATH_SEPARATOR . $root_directory.'include');
		require_once 'Zend2/Loader/StandardAutoloader.php';
		$loader = new Zend\Loader\StandardAutoloader(array(
		    'autoregister_zf' => true,
		    'namespaces' => array(
		        'Mail' => 'Zend2/Mail',
		        'Mime' => 'Zend2/Mime',
		    ),
		    'fallback_autoloader' => false,
		));
		// Register with spl_autoload:
		$loader->register();
	}

	function getZendMailProtocolImap($userid='') {
		if (empty(self::$protocol)) {
			if (!empty($userid)) {
				$user = CRMEntity::getInstance('Users');
				$user->retrieve_entity_info($userid,'Users');
			} else {
				global $current_user;
				$user = $current_user;
			}
			$accountid = $this->getAccount();
			$account = $this->getUserAccounts($user->id,$accountid);
			$account = $account[0];
			$server = $account['server'];
			$port = (!empty($account['port']) ? $account['port'] : null);
			$ssl_tls = (!empty($account['ssl_tls']) ? $account['ssl_tls'] : false);
			if (empty($server)) {
				throw new Exception('ERR_IMAP_SERVER_EMPTY');
			}
			$username = $account['username'];
			$password = $account['password'];
			try {
				$protocol = new Zend\Mail\Protocol\Imap($server,$port,$ssl_tls);
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				throw new Exception('ERR_IMAP_CONNECTION_FAILED');
			}
			$protocol->crmMessage = $this;	//crmv@76756
			$login = $protocol->login($username,$password);
			if ($login === false) {
				if (empty($username) || empty($password)) {
					throw new Exception('ERR_IMAP_CREDENTIALS_EMPTY');
				} else {
					throw new Exception('ERR_IMAP_LOGIN_FAILED');
				}
			}
			self::$protocol = $protocol;
		}
		return self::$protocol; // crmv@38592
	}

	function getZendMailStorageImap($userid='') {
		global $current_user, $current_folder;
		if (empty(self::$mail)) {
			$this->loadZendFramework();
			try {
				$this->getZendMailProtocolImap($userid);
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				// if error in cron skip
				if (isset($_REQUEST['app_key']) && $_REQUEST['service'] == 'Messages') {
					throw new Exception('ERR_IMAP_CRON');
				}
				// show Shared folder even if no mail server configured
				if ($_REQUEST['action'] == 'DetailView' && !empty($_REQUEST['record']) && in_array($current_folder,$this->fakeFolders)) {
					return false;
				}
				$this->manageConnectionError($e,$userid);
			}
			$mail = new Zend\Mail\Storage\Imap(self::$protocol);
			self::$mail = $mail;
			return true;
		}
	}

	function manageConnectionError($e,$userid='') {
		if (!empty($userid)) {
			$user = CRMEntity::getInstance('Users');
			$user->retrieve_entity_info($userid,'Users');
		} else {
			global $current_user;
			$user = $current_user;
		}
		global $adb, $table_prefix, $theme;
		$title = getTranslatedString($e->getMessage(),'Messages');
		if ($title != $e->getMessage()) {
			$descr = getTranslatedString($e->getMessage().'_DESCR','Messages');
		}
		//if (in_array($e->getMessage(),array('ERR_IMAP_SERVER_EMPTY','ERR_IMAP_CONNECTION_FAILED'))) {}
		//if (in_array($e->getMessage(),array('ERR_IMAP_CREDENTIALS_EMPTY','ERR_IMAP_LOGIN_FAILED'))) {}
		$link = 'index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=Accounts';
		$accounts = $this->getUserAccounts();
		$account = $this->getAccount();
		if ($e->getMessage() == 'ERR_IMAP_SERVER_EMPTY' && empty($accounts)) {
			$link = 'index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=EditAccount';
		} elseif ($account !== '') {
			$link = 'index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=EditAccount&id='.$account;
		}
		$descr = sprintf($descr,'<a href="javascript:;" onClick="top.openPopup(\''.$link.'\',\'\',\'\',\'auto\',720,500,\'top\');">'.getTranslatedString('LBL_HERE').'</a>');	//crmv@46468 crmv@114260
		require_once('Smarty_setup.php');
		$smarty = new vtigerCRM_Smarty;
		$smarty->assign('THEME', $theme);
		$smarty->assign('TITLE', $title);
		$smarty->assign('DESCR', $descr);
		$smarty->display('Error.tpl');
		exit;
	}

	function getMailResource() {
		return self::$mail;
	}

	function resetMailResource() {
		if (!empty(self::$mail)) {
			self::$mail->__destruct();
			self::$mail = '';
		}
		if (!empty(self::$protocol)) {
			self::$protocol->__destruct();
			self::$protocol = '';
		}
		$this->__construct();
	}

	function setAccount($id) {
		if ($this->account !== $id) {
			$this->account = $id;
			$this->resetMailResource();
		}
	}

	function getAccount() {
		if (!is_numeric($this->account) && $this->account == '') {
			$this->account = $this->column_fields['account'];
		}
		if (!is_numeric($this->account) && $this->account == '') {
			$this->account = '-1';
		}
		return $this->account;
	}
	
	//crmv@76756
	function logIMAP($mode, $str) {
		if (!$this->IMAPDebug) return false;
		global $root_directory;
		
		$now = date('Y-m-d H:i:s');
		
		$dir = $root_directory.$this->IMAPLogDir;
		if (!is_dir($dir)) {
			mkdir($dir, 0755);
		}
		// find a free name
		$logfile = false;
		for ($i=1; $i<1000; ++$i) {
			$logfile = $dir.str_pad(strval($i), 2, '0', STR_PAD_LEFT).'.log';
			if (!file_exists($logfile) || filesize($logfile) < $this->IMAPLogMaxSize) break;
		}
		
		if (stripos($str,' LOGIN ') !== false) {
			$tmp = explode('"',$str);
			$tmp[3] = 'password';
			$str = implode('"',$tmp);
		}
		
		if ($logfile) {
			@file_put_contents($logfile, "[$now] [ACCOUNT:{$this->getAccount()}] {$mode}: {$str}\n", FILE_APPEND);
			if (!file_exists($logfile)) @chmod($logfile, 0777);
		}
	}
	//crmv@76756e

	function setSpecialFolders($specialFolders,$accountid) {
		global $current_user, $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_sfolders where userid = ? and accountid = ?",array($current_user->id,$accountid));
		foreach($specialFolders as $special => $folder) {
			$adb->pquery("insert into {$table_prefix}_messages_sfolders (userid, accountid, special, folder) values (?,?,?,?)",array($current_user->id,$accountid,$special,$folder));
		}
	}

	function getSpecialFolders($dieOnError=true) {
		global $adb, $table_prefix;
		$specialFolders = $this->defaultSpecialFolders;
		$accountid = $this->getAccount();
		$result = $adb->pquery("select special, folder from {$table_prefix}_messages_sfolders where accountid = ?",array($accountid));	//crmv@44788
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$specialFolders[$row['special']] = $row['folder'];
			}
		}
		if ($dieOnError) {
			if (!in_array($_REQUEST['file'],array('Settings/index','MessagePopup'))
				&& (empty($specialFolders['INBOX']) || empty($specialFolders['Sent']) || empty($specialFolders['Drafts']) || empty($specialFolders['Trash']))
			) {
				$accounts = $this->getUserAccounts();	// check after set account
				if (!empty($accounts)) {
					// if error in cron skip
					if (isset($_REQUEST['app_key']) && $_REQUEST['service'] == 'Messages') {
						throw new Exception('ERR_IMAP_CRON');
					} else {
						global $theme;
						$link = 'index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=Folders';
						if ($accountid !== '') {
							$link = 'index.php?module=Messages&action=MessagesAjax&file=Settings/index&operation=Folders&account='.$accountid;
						}
						require_once('Smarty_setup.php');
						$smarty = new vtigerCRM_Smarty;
						$smarty->assign('THEME', $theme);
						$smarty->assign('TITLE', getTranslatedString('LBL_ERROR_SPECIALFOLDERS_TITLE','Messages'));
						$descr = getTranslatedString('LBL_ERROR_SPECIALFOLDERS_DESCR','Messages');
						$descr .= '<br />- '.getTranslatedString('LBL_Folder_INBOX','Messages');
						$descr .= '<br />- '.getTranslatedString('LBL_Folder_Drafts','Messages');
						$descr .= '<br />- '.getTranslatedString('LBL_Folder_Sent','Messages');
						$descr .= '<br />- '.getTranslatedString('LBL_Folder_Trash','Messages');
						$smarty->assign('DESCR', sprintf($descr,'<a href="javascript:;" onClick="openPopup(\''.$link.'\',\'\',\'\',\'auto\',720,500,\'top\');">'.getTranslatedString('LBL_HERE').'</a>'));	//crmv@46468 crmv@114260
						$smarty->display('Error.tpl');
						exit;
					}
				}
			}
		}
		return $specialFolders;
	}

	function getAllSpecialFolders($special='',$userid='') {
		global $adb, $table_prefix;
		if (empty($userid)) {
			global $current_user;
			$userid = $current_user->id;
		}
		$specialFolders = array();
		$query = "select accountid, special, folder from {$table_prefix}_messages_sfolders where userid = ?";
		$params = array($userid);
		if (!empty($special)) {
			$query .= " and special = ?";
			$params[] = $special;
		}
		$query .= " order by accountid";
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$specialFolders[$row['accountid']][$row['special']] = $row['folder'];
			}
		}
		return $specialFolders;
	}

	/*
	 * Find differences from imap and vte (new messages, changes in flags and messages to delete)
	 * - change of flag is made immediately beacouse is only an update in table _messages
	 * - actions of fetching and deleting of messages are recorded in a queue and it will be processed by another cron
	 * 
	 * Parameter $skip_inbox:
	 * - true : sync all folders except inbox ones
	 * - false : sync all folders
	 */
	function syncUids($skip_inbox=true,$userid=null,$sync_account=null) {
		global $adb, $table_prefix, $current_user;
		$query = "SELECT {$table_prefix}_users.id FROM {$table_prefix}_users WHERE {$table_prefix}_users.status = ?";
		$params = array('Active');
		if (!empty($userid)) {
			$query .= " and {$table_prefix}_users.id = ?";
			$params[] = $userid;
		}
		$result = $adb->pquery($query,$params);
		
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$tmp_current_user_id = $current_user->id;
				$current_user->id = $user = $row['id'];
				if ($skip_inbox) $specialFolders = $this->getAllSpecialFolders('INBOX',$user);
				$accounts = $this->getUserAccounts($user);
				foreach($accounts as $account) {
					$accountid = $account['id'];
					if (!empty($sync_account) && $sync_account != $accountid) continue;
					try {
						$this->setAccount($accountid);
						$this->getZendMailStorageImap($user);
						
						// check special folders, do not download message for accounts not totally configured
						$specialFolders = $this->getSpecialFolders(false);
						if (empty($specialFolders['INBOX']) || empty($specialFolders['Sent']) || empty($specialFolders['Drafts']) || empty($specialFolders['Trash'])) continue;
						$special_folders_list = array_keys($specialFolders);	//crmv@56609
						
						//crmv@56609 get folder list
						$tmp2 = array();
						$tmp1 = array();
						$folders = self::$mail->getFolders();
						foreach ($folders as $folder) {
							$foldername = $folder->getGlobalName();
							$in_array = array_search(preg_replace('#'.$this->folderSeparator.'.*#','',$foldername),$special_folders_list);
							if ($in_array !== false) {
								$tmp1[$in_array.$foldername] = $folder;
							} else {
								$tmp2[$foldername] = $folder;
							}
							$tmp[$foldername] = $folder;
						}
						ksort($tmp1);
						$tmp = array_merge($tmp1, $tmp2);
						//crmv@56609e
						
						$folder_root = new Zend\Mail\Storage\Folder('/','/',false,$tmp);						
						$folders_it = new RecursiveIteratorIterator($folder_root,RecursiveIteratorIterator::SELF_FIRST);
						foreach ($folders_it as $folder) {
							if (!$folder->isSelectable()) continue;
							$foldername = $folder->getGlobalName();
							if ($skip_inbox && $foldername == $specialFolders['INBOX']) continue;
							
							//crmv@51946
							try {
								$this->selectFolder($foldername);
							} catch (Exception $e) {	// reset connection
								$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
								$this->resetMailResource();
								$this->getZendMailStorageImap($user);
								//crmv@59095
								try {
									$this->selectFolder($foldername);
								} catch (Exception $e) {
									$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
									continue;
								}
								//crmv@59095e
							}
							//crmv@51946e
							
							$server_ids = array();			// populated in checkFlagsChanges
							$server_ids_dates = array();	// populated in checkFlagsChanges
							$cache_ids = array();			// populated in checkFlagsChanges

							//Update flags of cached mail messages - start
							$flag_changed = $this->checkFlagsChanges($user,$server_ids,$server_ids_dates,$cache_ids);
							//end

							//merge cache_ids (ids saved in vte) with the ids in _messages_cron_uid
							$query = "select uid from {$table_prefix}_messages_cron_uid where action = ? and userid = ? and accountid = ? and folder = ?";
							$params = array('fetch',$user,$accountid,$foldername);
							if (!empty($this->interval_schedulation)) {
								$dateCol = 'date';
								$adb->format_columns($dateCol);
								$query .= " AND $dateCol >= ?";
								$params[] = date('Y-m-d',strtotime("-{$this->interval_schedulation}"));
							}
							$result1 = $adb->pquery($query,$params);
							if ($result1 && $adb->num_rows($result1) > 0) {
								while($row1=$adb->fetchByAssoc($result1)) {
									$cache_ids[] = $row1['uid'];
								}
							}

							//Save new mail messages and delete - start
							$delete_ids = array_diff($cache_ids,$server_ids);
							if (!empty($delete_ids)) {
								$this->populateSyncUidsQueue('noinbox','delete',$user,$accountid,$foldername,$delete_ids,$server_ids_dates);
							}

							$new_ids = array_diff($server_ids,$cache_ids);
							$new_ids = array_reverse($new_ids,true);	//scarico dai piu recenti ai piu vecchi
							if (!empty($new_ids)) {
								$this->populateSyncUidsQueue('noinbox','fetch',$user,$accountid,$foldername,$new_ids,$server_ids_dates);
							}
							//end
						}
					} catch (Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						//echo "ERROR {$e->getMessage()} user:$user account:$accountid\n";
						continue;
					}
				}
				$current_user->id = $tmp_current_user_id;
			}
		}
	}
	
	function syncUidsInbox($userid=null,$account=null) {
		global $adb, $table_prefix, $current_user;
		$query = "SELECT {$table_prefix}_users.id FROM {$table_prefix}_users WHERE {$table_prefix}_users.status = ?";
		$params = array('Active');
		if (!empty($userid)) {
			$query .= " and {$table_prefix}_users.id = ?";
			$params[] = $userid;
		}
		$result = $adb->pquery($query,$params);
		
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$tmp_current_user_id = $current_user->id;
				$current_user->id = $user = $row['id'];
				$allSpecialFolders = $this->getAllSpecialFolders('INBOX',$user);
				foreach($allSpecialFolders as $accountid => $folders) {
					try {
						if (!empty($account) && $account != $accountid) continue;
						
						$foldername = $folders['INBOX'];
						
						$this->setAccount($accountid);
						$this->getZendMailStorageImap($user);
						$this->selectFolder($foldername);
						
						// check special folders, do not download message for accounts not totally configured
						$specialFolders = $this->getSpecialFolders(false);
						if (empty($specialFolders['INBOX']) || empty($specialFolders['Sent']) || empty($specialFolders['Drafts']) || empty($specialFolders['Trash'])) continue;
						
						$server_ids = array();			// populated in checkFlagsChanges
						$server_ids_dates = array();	// populated in checkFlagsChanges
						$cache_ids = array();			// populated in checkFlagsChanges
	
						//Update flags of cached mail messages - start
						$flag_changed = $this->checkFlagsChanges($user,$server_ids,$server_ids_dates,$cache_ids);
						//end
	
						//merge cache_ids (ids saved in vte) with the ids in _messages_cron_uidi
						$query = "select uid from {$table_prefix}_messages_cron_uidi where action = ? and userid = ? and accountid = ? and folder = ?";
						$params = array('fetch',$user,$accountid,$foldername);
						if (!empty($this->interval_schedulation)) {
							$dateCol = 'date';
							$adb->format_columns($dateCol);
							$query .= " AND $dateCol >= ?";
							$params[] = date('Y-m-d',strtotime("-{$this->interval_schedulation}"));
						}
						$result1 = $adb->pquery($query,$params);
						if ($result1 && $adb->num_rows($result1) > 0) {
							while($row1=$adb->fetchByAssoc($result1)) {
								$cache_ids[] = $row1['uid'];
							}
						}
						
						//Save new mail messages and delete - start
						$delete_ids = array_diff($cache_ids,$server_ids);
						if (!empty($delete_ids)) {
							$this->populateSyncUidsQueue('inbox','delete',$user,$accountid,$foldername,$delete_ids,$server_ids_dates);
						}
	
						$new_ids = array_diff($server_ids,$cache_ids);
						$new_ids = array_reverse($new_ids,true);	//scarico dai piu recenti ai piu vecchi
						if (!empty($new_ids)) {
							$this->populateSyncUidsQueue('inbox','fetch',$user,$accountid,$foldername,$new_ids,$server_ids_dates);
						}
						//end
					} catch (Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						//echo "ERROR {$e->getMessage()} user:$user account:$accountid\n";
						continue;
					}
				}
				$current_user->id = $tmp_current_user_id;
			}
		}
	}
	
	function syncUidsAll() {
		global $adb, $table_prefix;
		$adb->query("delete from {$table_prefix}_messages_sync_all where inbox = 1 and other = 1");
		$result = $adb->limitQuery("select * from {$table_prefix}_messages_sync_all where inbox = 0 or other = 0",0,1);
		if ($result && $adb->num_rows($result) > 0) {
			$this->interval_schedulation = '';
			$userid = $adb->query_result($result,0,'userid');
			$accountid = $adb->query_result($result,0,'accountid');
			if ($adb->query_result($result,0,'inbox') == 0) {
				$adb->pquery("update {$table_prefix}_messages_sync_all set inbox = 1 where accountid = ?",array($accountid));
				$this->syncUidsInbox($userid, $accountid);
			} else {
				$adb->pquery("update {$table_prefix}_messages_sync_all set other = 1 where accountid = ?",array($accountid));
				$this->syncUids(true, $userid, $accountid);
			}
		}
	}
	
	function populateSyncUidsQueue($mode,$action,$userid,$accountid,$folder,$uids,$server_ids_dates) {
		global $adb, $table_prefix;
		($mode == 'inbox') ? $table = "{$table_prefix}_messages_cron_uidi" : $table = "{$table_prefix}_messages_cron_uid";
		$uids = array_filter($uids);
		if (!empty($uids)) {
			foreach($uids as $uid) {
				($action == 'delete' || empty($server_ids_dates[$uid])) ? $date = date('Y-m-d H:i:s') : $date = $server_ids_dates[$uid];
				$values = array(
					'sequence'=>$adb->getUniqueID($table),
					'userid'=>$userid,
					'accountid'=>$accountid,
					'folder'=>$folder,
					'uid'=>$uid,
					'date'=>$date,
					'action'=>$action,
					'cdate'=>date('Y-m-d H:i:s'),
				);
				if ($adb->isMysql()) {
					$adb->pquery("insert ignore into {$table} (".implode(',',array_keys($values)).") values (".generateQuestionMarks($values).")",array($values));
				} else {
					$columns = array_keys($values);
					$adb->format_columns($columns);
					$result = $adb->pquery("select * from {$table} where userid = ? and accountid = ? and folder = ? and uid = ?",array($userid,$accountid,$folder,$uid));
					if (!$result || $adb->num_rows($result) == 0) {
						$adb->pquery("insert into {$table} (".implode(',',$columns).") values (".generateQuestionMarks($values).")",array($values));
					}
				}
			}
		}
	}
	
	function cleanSyncUidsQueue($userid,$accountid,$folder,$uids='',$crmids='') {
		global $adb, $table_prefix;
		$err_uids = $this->getErrUids();	//crmv@50124
		$this->err_uids = array();	//crmv@53430
		if (!empty($uids)) $uids = array_filter($uids);
		if (!empty($crmids)) $crmids = array_filter($crmids);
		if (!empty($err_uids)) $err_uids = array_filter($err_uids);
		
		if (!empty($uids)) {
			$uids = array_map('intval',$uids);
			if (!empty($uids)) {
				$adb->pquery("delete from {$table_prefix}_messages_cron_uid where userid = ? and accountid = ? and folder = ? and uid in (".generateQuestionMarks($uids).")",array($userid,$accountid,$folder,$uids));
				$adb->pquery("delete from {$table_prefix}_messages_cron_uidi where userid = ? and accountid = ? and folder = ? and uid in (".generateQuestionMarks($uids).")",array($userid,$accountid,$folder,$uids));
			}
		}
		if (!empty($crmids)) {
			$crmids = array_map('intval',$crmids);
			$adb->pquery("delete from {$table_prefix}_messages_cron_uid where userid = ? and accountid = ? and folder = ? and uid in (
					select xuid from {$table_prefix}_messages
					where messagesid in (".generateQuestionMarks($crmids)."))",
				array($userid,$accountid,$folder,$crmids)
			);
			$adb->pquery("delete from {$table_prefix}_messages_cron_uidi where userid = ? and accountid = ? and folder = ? and uid in (
					select xuid from {$table_prefix}_messages
					where messagesid in (".generateQuestionMarks($crmids)."))",
				array($userid,$accountid,$folder,$crmids)
			);
		}
		//crmv@50124
		if (!empty($err_uids)) {
			$err_uids = array_map('intval',$err_uids);
			$adb->pquery("update {$table_prefix}_messages_cron_uid set status = 1 where userid = ? and accountid = ? and folder = ? and uid in (".generateQuestionMarks($err_uids).")",array($userid,$accountid,$folder,$err_uids));
			$adb->pquery("update {$table_prefix}_messages_cron_uidi set status = 1 where userid = ? and accountid = ? and folder = ? and uid in (".generateQuestionMarks($err_uids).")",array($userid,$accountid,$folder,$err_uids));
		}
		//crmv@50124e
	}
	
	function checkSyncUidsErrors() {
		global $adb, $table_prefix, $current_user;
		$error = false;
		$tables = array("{$table_prefix}_messages_cron_uid"=>'Messages',"{$table_prefix}_messages_cron_uidi"=>'MessagesInbox');
		foreach($tables as $table => $cron) {
			$resultCron = $adb->pquery("select lastrun from {$table_prefix}_cronjobs where cronname = ?",array($cron));
			if ($resultCron && $adb->num_rows($resultCron) > 0) {
				$lastrun = $adb->query_result($resultCron,0,'lastrun');
				if (empty($lastrun)) continue;
				$result = $adb->pquery("select * from {$table} where userid = ? and action = ? and status = ? and cdate < ? and attempts = ?",array($current_user->id,'fetch',2,$lastrun,$this->max_message_cron_uid_attempts));	//crmv@55450
				if ($result && $adb->num_rows($result) > 0) {
					$error = true;
					break;
				}
			}
		}
		return $error;
	}
	
	function checkSendQueueErrors() {
		global $adb, $table_prefix, $current_user;
		$error = false;
		$resultCron = $adb->pquery("select lastrun from {$table_prefix}_cronjobs where cronname = ?",array('MessagesSend'));
		if ($resultCron && $adb->num_rows($resultCron) > 0) {
			$lastrun = $adb->query_result($resultCron,0,'lastrun');
			//crmv@98338
			if (!empty($lastrun)){
				$dateCol = 'date';
				$adb->format_columns($dateCol);
				$result = $adb->pquery("select * from {$table_prefix}_emails_send_queue where userid = ? and method = ? and status = ? and s_send = ? and $dateCol < ?",array($current_user->id,'send',2,0,$lastrun));
				if ($result && $adb->num_rows($result) > 0) {
					$error = true;
				}
			}
			//crmv@98338e
		}
		return $error;
	}
	
	function cronSync($user_start='',$user_end='') {

		// first of all propagate to server pending changes
		$this->propagateToImap();

		// process uids in queue
		$i = 1;
		while($this->processSyncUidsQueue('noinbox',$user_start,$user_end)) {
			if ($this->messages_by_schedule > 0 && $i == $this->messages_by_schedule) break;
			$i++;
		}
	}
	
	function cronSyncInbox($user_start='',$user_end='') {
		
		// first of all propagate to server pending changes
		$this->propagateToImap('fast');

		// process uids in queue only for inbox folders
		$i = 1;
		while($this->processSyncUidsQueue('inbox',$user_start,$user_end)) {
			if ($this->messages_by_schedule_inbox > 0 && $i == $this->messages_by_schedule_inbox) break;
			$i++;
		}
	}
	
	/*
	 * Process sync queue in order to fetch or delete messages in vte
	 * 
	 * Parameter $mode:
	 * - 'inbox' : only process uids of inbox folders
	 * - 'noinbox' : process all except uids of inbox folders
	 * - any other value : process all
	 */
	function processSyncUidsQueue($mode='',$user_start='',$user_end='') {
		global $adb, $table_prefix, $current_user;
		($mode == 'inbox') ? $table = "{$table_prefix}_messages_cron_uidi" : $table = "{$table_prefix}_messages_cron_uid";
		$where = '';
		if ($user_start != '') {
			$where = " where userid >= $user_start";
			if ($user_end != '') {
				$where .= " and userid <= $user_end";
			}
		}
		(empty($where)) ? $where .= ' where ' : $where .= ' and ';
		$where .= "(status = 0 OR (status = 2 AND attempts < {$this->max_message_cron_uid_attempts}))";	//crmv@55450 : not already processed or attempts < max attempts
		$dateCol = 'date';
		$adb->format_columns($dateCol);
		$query = "select * from {$table} {$where} order by $dateCol desc";
		$result = $adb->limitQuery($query,0,1);
		if ($result && $adb->num_rows($result) > 0) {
			$sequence = $adb->query_result($result,0,'sequence');
			$current_user->id = $userid = $adb->query_result($result,0,'userid');
			$accountid = $adb->query_result($result,0,'accountid');
			$folder = $adb->query_result_no_html($result,0,'folder');
			$uid = $adb->query_result($result,0,'uid');
			$action = $adb->query_result($result,0,'action');
			//echo "$user_start-$user_end: $action u:$userid a:$accountid f:$folder uid:$uid\n";
			
			$this->setAccount($accountid);
			try {
				$this->getZendMailStorageImap($userid);
			} catch (Exception $e) {	// problems with connection (ex. account not configured)
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$this->cleanSyncUidsQueue($userid,$accountid,$folder,array($uid));
				return true;
			}
			try {
				$this->selectFolder($folder);
			} catch (Exception $e) {	// problems with reading of folder (es. folder deleted)
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$this->cleanSyncUidsQueue($userid,$accountid,$folder,array($uid));
				return true;
			}
			
			// set as processing, so if there are problems or timeout with this message, next run skip this
			$adb->pquery("update {$table} set status = ?, cdate = ?, attempts = attempts+1 where sequence = ?",array(2,date('Y-m-d H:i:s'),$sequence));	//crmv@55450

			if ($action == 'delete') {
				// crmv@64325
				$setypeCond = '';
				if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
					$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
				}
				$result1 = $adb->pquery("select messagesid from {$table_prefix}_messages
						inner join {$table_prefix}_crmentity on {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid
						where deleted = 0 $setypeCond AND smownerid = ? and {$table_prefix}_messages.account = ? and {$table_prefix}_messages.folder = ? and {$table_prefix}_messages.xuid = ?",
					array($userid,$accountid,$folder,$uid)
				);
				// crmv@64325e
				if ($result1 && $adb->num_rows($result1) > 0) {
					$crmid = $adb->query_result($result1,0,'messagesid');
					if (!empty($crmid)) {
						$this->deleteCache(array($crmid=>$uid));
					}
				}
				$this->cleanSyncUidsQueue($userid,$accountid,$folder,array($uid));
			} elseif ($action == 'fetch') {
				try {
					$messageId = self::$mail->getNumberByUniqueId($uid);
					$this->saveCache(array($messageId=>$uid));
				} catch(Exception $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					if ($e->getMessage() == 'unique id not found') {
						$this->cleanSyncUidsQueue($userid,$accountid,$folder,array($uid));
					}
					//crmv@50124 if error status remain 2 -- $this->setSkippedUids($uid);
				}
				$this->cleanSyncUidsQueue($userid,$accountid,$folder,$this->getSkippedUids(),$this->getSavedMessages());
				$this->skipped_uids = array();		//crmv@53430
				$this->saved_messages = array();	//crmv@53430
			}
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Sync immediately server mail folder with vte cache instead of syncUids/syncUidsInbox and then processSyncUidsQueue that made it in two steps.
	 * 
	 * Parameters
	 * - $num_news : if empty (null,false,0,'') download all $news_ids
	 * - $only_news : download only new messages from the previous time
	 */
	function syncFolder($folder, $num_news=0, $only_news=false) {
		global $current_user, $adb, $table_prefix;

		$this->getZendMailStorageImap();
		$this->selectFolder($folder);

		$server_ids = array();			// populated in checkFlagsChanges
		$server_ids_dates = array();	// populated in checkFlagsChanges
		$cache_ids = array();			// populated in checkFlagsChanges

		//Update flags of cached mail messages - start
		$flag_changed = $this->checkFlagsChanges($current_user->id,$server_ids,$server_ids_dates,$cache_ids);
		//end

		// if no messages cached disable $only_news
		if (empty($cache_ids)) {
			$only_news = false;
		}

		//Save new mail messages and delete - start
		$delete_ids = array_diff($cache_ids,$server_ids);
		$this->deleteCache($delete_ids);
		$this->cleanSyncUidsQueue($current_user->id,$this->account,$folder,$delete_ids);

		$new_ids = array_diff($server_ids,$cache_ids);
		$new_ids = array_reverse($new_ids,true);	//scarico dai piu recenti ai piu vecchi
		if ($only_news) {
			$tmp = array();
			// crmv@64325
			$setypeCond = '';
			if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
				$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
			}
			$result = $adb->limitpQuery("SELECT xuid FROM {$this->table_name}
										INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
										WHERE deleted = 0 $setypeCond AND mtype = ? AND smownerid = ? AND account = ? AND folder = ?
										ORDER BY mdate DESC",0,1,
										array('Webmail',$current_user->id,$this->account,$folder));
			// crmv@64325e
			if ($result && $adb->num_rows($result) > 0) {
				$last_uid = $adb->query_result($result,0,'xuid');
				$last_messageid = array_search($last_uid,$server_ids);
				if (empty($last_messageid)) {
					$last_messageid = self::$mail->getNumberByUniqueId($last_uid);
				}
				foreach($new_ids as $messageid => $uid) {
					if ($messageid > $last_messageid) {
						$tmp[$messageid] = $uid;
					}
				}
			}
			$new_ids = $tmp;
		}
		if (!empty($num_news) && !empty($new_ids)) {
			$new_ids = array_slice($new_ids, 0, $num_news, true);
		}

		$this->saveCache($new_ids);
		$this->cleanSyncUidsQueue($current_user->id,$this->account,$folder,$this->getSkippedUids(),$this->getSavedMessages());
		$this->skipped_uids = array();		//crmv@53430
		$this->saved_messages = array();	//crmv@53430
		//end

		return array(
			'delete_ids'=>$delete_ids,
			'new_ids'=>$new_ids,
			'flag_changed'=>$flag_changed
		);
	}
	
	function addToPropagationCron($operation, $params, $max_attempts=3) {
		global $adb, $table_prefix;
		if (is_array($params)) {
			$params = Zend_Json::encode($params);
		}
		$adb->pquery("insert into {$table_prefix}_messages_prop2imap (sequence,operation,params,status,attempts,max_attempts) values (?,?,?,?,?,?)",array(
			$adb->getUniqueID($table_prefix.'_messages_prop2imap'), $operation, $params, 0, 0, $max_attempts
		));
	}
	
	function propagateToImap($mode='full') {
		global $adb, $table_prefix;
		// skip running propagations and attempts exceeded
		$query = "select * from {$table_prefix}_messages_prop2imap where status <> ? and attempts < max_attempts";
		$params = array(2);
		if ($mode == 'fast') {	// skip slow operations
			$query .= " and operation <> ?";
			$params[] = 'empty';
		}
		$query .= " order by sequence";
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result,-1,false)) {
				$adb->pquery("update {$table_prefix}_messages_prop2imap set status = ?, attempts = ? where sequence = ?",array(2,$row['attempts']+1,$row['sequence']));
				try {
					$params = Zend_Json::decode($row['params']);
					switch ($row['operation']) {
						case 'flag':
							$this->propagateSetFlag($params['id'],$params['flag'],$params['value']);
							break;
						case 'flag_folder':
							$this->propagateFlagFolder($params['userid'],$params['account'],$params['folder'],$params['flag'],$params['value']);
							break;
						case 'move':
							$this->propagateMoveMessage($params['userid'],$params['account'],$params['folder'],$params['uid'],$params['new_folder'],$params['skip_fetch']);
							break;
						case 'move_mass':
							$this->propagateMassMoveMessage($params['userid'],$params['account'],$params['folder'],$params['uid'],$params['new_folder']);
							break;
						case 'trash':
							$this->propagateTrash($params['userid'],$params['account'],$params['folder'],$params['uid'],$params['fetch']);
							break;
						case 'empty':
							$this->propagateEmpty($params['userid'],$params['account'],$params['folder']);
							break;
					}
					$adb->pquery("delete from {$table_prefix}_messages_prop2imap where sequence = ?",array($row['sequence']));
				} catch (Exception $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					$adb->pquery("update {$table_prefix}_messages_prop2imap set status = ?, error = ? where sequence = ?",array(1,$e->getMessage(),$row['sequence']));
				}
			}
			// crmv@102274
			// This is necessary because the propagateSetFlag (and probably others too) creates a new instance of Messages and use a different account.
			// The problem is that the self::$mail and self::$protocol are static, therefore are shared between all the instances.
			// When the fetch method set the account with setAccount, the self::$protocol is not cleared, since $this->account wasn't changed
			// (another instance was used) and the old $protocol is still used, even if connected with a different user, causing wrong messages
			// to be retrieved.
			$this->setAccount('');
			// crmv@102274e
		}
	}
	
	function syncFolders($userid='', $account='',$skip_empty=false) { //crmv@49843
		global $adb, $table_prefix;
		$query = "select userid, id from {$table_prefix}_messages_account";
		$params = array();
		if (!empty($userid) && !empty($account)) {
			$query .= " where userid = ? and id = ?";
			$params[] = $userid;
			$params[] = $account;
		}
		$query .= " order by userid, id";
		$res = $adb->pquery($query,$params);
		if ($res && $adb->num_rows($res) > 0) {
			while($r=$adb->fetchByAssoc($res)) {
				
				$account = $r['id'];
				$userid = $r['userid'];

				$this->setAccount($account);
				try {
					$this->getZendMailStorageImap($userid);
				} catch (Exception $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					continue;
				}
		
				$specialFolders = $this->getSpecialFolders(false);
				if (empty($specialFolders['INBOX']) && $skip_empty)	continue;	//crmv@49843
				$special_folders_list = array_keys($specialFolders);
		
				//Order folder list with $special_folders_list
				$tmp1 = array();
				$tmp2 = array();
				$depths = array();
				$folders = self::$mail->getFolders();
				foreach ($folders as $folder) {
					$foldername = $folder->getGlobalName();
					$in_array = array_search(preg_replace('#'.$this->folderSeparator.'.*#','',$foldername),$special_folders_list);
					if ($in_array !== false) {
						$tmp1[$in_array.$foldername] = $folder;
					} else {
						$tmp2[$foldername] = $folder;
					}
					if (strpos($foldername,$this->folderSeparator) !== false) {
						$depths[$foldername] = substr_count($foldername,$this->folderSeparator);
					}
				}
				ksort($tmp1);
				$folders = array_merge($tmp1, $tmp2);
				$folder_root = new Zend\Mail\Storage\Folder('/','/',false,$folders);
				//end
		
				$folders_it = new RecursiveIteratorIterator($folder_root,RecursiveIteratorIterator::SELF_FIRST);
				$folders = array();
				
				$folders[] = array(
					'localname'=>$folder_root->getLocalName(),
					'globalname'=>$folder_root->getGlobalName(),
					'depth'=>0,
					'selectable'=>0,
					'count'=>0,
				);
		
				// folder of Shared mail
				$related_ids = $this->getRelatedModComments(false,$userid);
				$folders[] = array(
					'localname'=>'Shared',
					'globalname'=>'Shared',
					'depth'=>0,
					'selectable'=>1,
					'count'=>count($related_ids),
				);
				// end
				
				// crmv@64325
				$setypeCond = '';
				if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
					$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
				}
	
				// folder of Links
				$count = 0;
				$result = $adb->pquery("SELECT count(*) AS count FROM {$this->table_name}
										INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
										WHERE deleted = 0 $setypeCond AND smownerid = ? and {$this->table_name}.mtype = ?",
										array($userid,'Link'));
				if ($result && $adb->num_rows($result) > 0) {
					$count = $adb->query_result($result,0,'count');
				}
				$folders[] = array(
					'localname'=>'Links',
					'globalname'=>'Links',
					'depth'=>0,
					'selectable'=>1,
					'count'=>$count,
				);
				// end
		
				// folder of Flagged mail
				$count = 0;
				//crmv@79192
				$result = $adb->pquery("SELECT count(distinct messagehash) AS count FROM {$this->table_name}
										INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
										WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.account = ? AND {$this->table_name}.flagged = ? and {$this->table_name}.mtype = ?",
										array($userid,$account,1,'Webmail'));
				//crmv@79192e
				if ($result && $adb->num_rows($result) > 0) {
					$count = $adb->query_result($result,0,'count');
				}
				$folders[] = array(
					'localname'=>'Flagged',
					'globalname'=>'Flagged',
					'depth'=>0,
					'selectable'=>1,
					'count'=>$count,
				);
				// end
				
				//crmv@85634 - performance fix
				$folder_counts = array();
				$result_folder = $adb->pquery("SELECT folder FROM {$this->table_name} WHERE account = ? GROUP BY folder", array($account));
				if ($result_folder && $adb->num_rows($result_folder) > 0) {
					while($row_folder=$adb->fetchByAssoc($result_folder, -1, false)) {
						$result = $adb->pquery("SELECT count(*) AS count FROM {$this->table_name}
							INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
							WHERE deleted = 0 AND smownerid = ? AND {$this->table_name}.account = ? AND {$this->table_name}.folder = ? AND {$this->table_name}.mtype = ? AND {$this->table_name}.seen = ?",
							array($userid,$account,$row_folder['folder'],'Webmail',0)
						);
						if ($result && $adb->num_rows($result) > 0) {
							while($row=$adb->fetchByAssoc($result, -1, false)) {
								$folder_counts[$row_folder['folder']] = $row['count'];
							}
						}
					}
				}
				//crmv@85634e
					
				foreach ($folders_it as $folder) {
					$localName = $folder->getLocalName();
					$globalName = $folder->getGlobalName();
		
					$depth1 = $depths[$globalName];
					$depth2 = $folders_it->getDepth();
					$depth = max($depth1,$depth2);
					if (empty($depth)) $depth = 0;
					
					$folders[] = array(
						'localname'=>$localName,
						'globalname'=>$globalName,
						'depth'=>$depth,
						'selectable'=>$folder->isSelectable(),
						'count'=>$folder_counts[$globalName],	//crmv@51191
					);
				}
				// crmv@64325e
				
				$adb->pquery("delete from {$table_prefix}_messages_folders where userid = ? and accountid = ?",array($userid,$account));

				$sequence = 0;
				foreach($folders as $folder) {
					(empty($folder['selectable'])) ? $selectable = 0 : $selectable = 1;
					(empty($folder['count'])) ? $folder['count'] = 0 : $folder['count']; //crmv@60402
					if ($adb->isMysql()) {
						$adb->pquery("insert ignore into {$table_prefix}_messages_folders (userid,accountid,globalname,localname,depth,selectable,count,sequence) values (?,?,?,?,?,?,?,?)",array(
							$userid,$account,$folder['globalname'],$folder['localname'],$folder['depth'],$selectable,$folder['count'],$sequence
						));
					} else {
						$result = $adb->pquery("select * from {$table_prefix}_messages_folders where userid = ? and accountid = ? and globalname = ?",array($userid,$account,$folder['globalname']));
						if (!$result || $adb->num_rows($result) == 0) {
							$adb->pquery("insert into {$table_prefix}_messages_folders (userid,accountid,globalname,localname,depth,selectable,count,sequence) values (?,?,?,?,?,?,?,?)",array(
								$userid,$account,$folder['globalname'],$folder['localname'],$folder['depth'],$selectable,$folder['count'],$sequence
							));
						}
					}
					$sequence++;
				}
			}
		}
	}
	
	function reloadCacheFolderCount($userid,$accountid,$folder) {
		global $adb, $table_prefix;
		// crmv@64325	crmv@79192
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		($folder == 'Flagged') ? $select_count = 'count(distinct messagehash)' : $select_count = 'count(*)';			
		$query = "SELECT $select_count AS count FROM {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
				WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.account = ? and {$this->table_name}.mtype = ?";
		// crmv@64325e	crmv@79192e
		$params = array($userid,$accountid,'Webmail');
		if ($folder == 'Flagged') {
			//crmv@79192
			$query .= " AND {$this->table_name}.flagged = ?";
			$params[] = 1;
			//crmv@79192e
		} else {
			$query .= " AND {$this->table_name}.seen = ? and {$this->table_name}.folder = ?";
			$params[] = 0;
			$params[] = $folder;
		}
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			$count = $adb->query_result($result,0,'count');
			$adb->pquery("update {$table_prefix}_messages_folders set count = ? where userid = ? and accountid = ? and globalname = ?",array($count,$userid,$accountid,$folder));
		}
	}

	/*
	 * Fetch new messages from folder, if $num empty -> fetch only the most recent
	 */
	/* crmv@53430 crmv@54904 */
	function fetchNews($folder, $num=1) {
		global $current_user;
		$this->selectFolder($folder);
		$cache_ids = $this->getSavedUids($current_user->id);
		$server_ids = $this->getServerUids();
		$new_ids = array_diff($server_ids,$cache_ids);
		$new_ids = array_slice(array_reverse($new_ids,true),0,$num,true);
		$this->saveCache($new_ids);
		$savedMessages = $this->getSavedMessages();
		$this->cleanSyncUidsQueue($current_user->id,$this->account,$folder,$this->getSkippedUids(),$savedMessages);
		$this->skipped_uids = array();
		$this->saved_messages = array();
		return $savedMessages;
	}

	function fetch($account, $folder, $only_news = false) {
		
		global $current_user, $adb, $table_prefix;

		//crmv@48471
		if (empty($folder) || $account == '' || in_array($folder,$this->fakeFolders)) {	// || $account == 'all' //crmv@62140
			return '';
		}
		/* crmv@62140
		$specialFolders = $this->getAllSpecialFolders('INBOX');
		if (empty($specialFolders[$account]) || $folder == $specialFolders[$account]['INBOX']) {
			return '';
		}
		crmv@62140e */

		// crmv@96019
		if (PerformancePrefs::get('MESSAGES_UPDATE_ICON_PERFORM_IMAP_ACTIONS', '') == 'disable') {
			return 'RELOAD';
		}
		// crmv@96019e
		
		// first of all propagate to server pending changes
		$this->propagateToImap();	//TODO parameterize by user
		
		//crmv@48471e

		if ($account == 'all') {
			$accounts = array();
			$tmp = $this->getUserAccounts();
			foreach($tmp as $t) {
				$accounts[] = $t['id'];
			}
		} else {
			$accounts = array($account);
		}
		$reload = false;
		foreach ($accounts as $account) {
			$this->setAccount($account);
			$sync_result = $this->syncFolder($folder, $this->list_max_entries_per_page, $only_news);
			$delete_ids = $sync_result['delete_ids'];
			$new_ids = $sync_result['new_ids'];
			$flag_changed = $sync_result['flag_changed'];
			if (!empty($delete_ids) || !empty($new_ids) || $flag_changed) {
				$reload = true;
			}
		}
		if ($reload) return 'RELOAD';
		return '';
	}

	function selectFolder($folder) {
		$this->folder = $folder;
		self::$mail->selectFolder($folder);
	}

	function getServerUids() {
		return self::$mail->getUniqueId();
	}

	function getSavedUids($userid) {
		if (empty($this->folder) || $this->account === '') {
			return false;
		}
		global $adb, $table_prefix;
		$external_codes = array();
		
		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		$result = $adb->pquery("SELECT crmid, xuid FROM {$this->table_name}
								INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
								WHERE {$this->entity_table}.deleted = 0 $setypeCond AND {$this->table_name}.mtype = ? AND {$this->entity_table}.smownerid = ? AND {$this->table_name}.account = ? AND {$this->table_name}.folder = ?",array('Webmail',$userid,$this->account,$this->folder));
		// crmv@64325e
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$external_codes[$row['crmid']] = $row['xuid'];
			}
		}
		return $external_codes;
	}
	
	function checkFlagsChanges($userid,&$server_ids,&$server_ids_dates,&$cache_ids) {
		if (empty($this->folder) || $this->account === '') {
			return;
		}

		// crmv@96019
		if (PerformancePrefs::get('MESSAGES_UPDATE_ICON_PERFORM_IMAP_ACTIONS', '') == 'fast_sync') {
			if ($_REQUEST['file'] == 'Fetch') {
				$interval_imap_fast_sync = PerformancePrefs::get('INTERVAL_IMAP_FAST_SYNC', false);
				if (!empty($interval_imap_fast_sync)) {
					$this->interval_schedulation = $interval_imap_fast_sync;
				}
			}
		}
		// crmv@96019e

		if (!empty($this->interval_schedulation)) {
			$date = date('j-M-Y',strtotime("-{$this->interval_schedulation}"));
			$messageids = self::$protocol->search(array('SINCE "'.$date.'"'));
			/* Lotus do not support SEARCH SINCE... crmv@52514 crmv@57585 */
			if ($messageids !== false) {
				$tmp_server_ids = self::$protocol->fetch(array('UID','INTERNALDATE'),$messageids);
				$searchSinceSupported = true;
			} else {
				$tmp_server_ids = self::$protocol->fetch(array('UID','INTERNALDATE'),1,INF);
				$searchSinceSupported = false;
				$limitTime = strtotime("-{$this->interval_schedulation}");
			}
			/* crmv@52514e crmv@57585e */
		} else {
			$tmp_server_ids = self::$protocol->fetch(array('UID','INTERNALDATE'),1,INF);	/* all */
		}
		$server_ids = array();
		foreach($tmp_server_ids as $messageid => $val) {
			//crmv@57585
			$save = false;
			if (!empty($this->interval_schedulation) && !$searchSinceSupported) {
				(strtotime($val['INTERNALDATE']) >= $limitTime) ? $save = true : $save = false;
			} else {
				$save = true;
			}
			if ($save) {
				$server_ids[$messageid] = $val['UID'];
				$server_ids_dates[$val['UID']] = date('Y-m-d H:i:s',strtotime($val['INTERNALDATE']));			
			}
			//crmv@57585e
		}

		$found_changes = false;
		global $adb, $table_prefix;
		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		//crmv@58931
		$query = "SELECT xuid, seen, answered, flagged, forwarded, messagesid as crmid FROM {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
				WHERE {$this->entity_table}.deleted = 0 $setypeCond AND {$this->entity_table}.smownerid = ? AND {$this->table_name}.mtype = ? AND {$this->table_name}.account = ? AND {$this->table_name}.folder = ?";
		$params = array($userid,'Webmail',$this->account,$this->folder);
		//crmv@58931e crmv@64325e
		if (!empty($this->interval_schedulation)) {
			$query .= " AND {$this->table_name}.mdate >= ?";
			$params[] = date('Y-m-d',strtotime("-{$this->interval_schedulation}"));
		}
		$result = $adb->pquery($query,$params);		
		if (!$result || $adb->num_rows($result)== 0) {
			return;
		} else {
			$cache_flags = array();
			while($row=$adb->fetchByAssoc($result)) {
				$tmp = array();
				if ($row['seen'] == '1') {
					$tmp[] = Zend\Mail\Storage::FLAG_SEEN;
				}
				if ($row['answered'] == '1') {
					$tmp[] = Zend\Mail\Storage::FLAG_ANSWERED;
				}
				if ($row['flagged'] == '1') {
					$tmp[] = Zend\Mail\Storage::FLAG_FLAGGED;
				}
				if ($row['forwarded'] == '1') {
					//$tmp[] = '$Forwarded';
					$tmp[] = 'Forwarded';
				}
				$cache_flags[$row['xuid']] = $tmp;
				$cache_ids[$row['crmid']] = $row['xuid'];
			}
		}

		$cache_uids = array_keys($cache_flags);
		$server_message_ids = array_flip($server_ids);
		$cache_list = array_intersect($server_ids,$cache_uids);
		//$cache_list = array_slice($cache_list,-1000,1000,true);	//crmv@42701	//crmv@54310

		$managed_flags = array(Zend\Mail\Storage::FLAG_SEEN,Zend\Mail\Storage::FLAG_ANSWERED,Zend\Mail\Storage::FLAG_FLAGGED,'$Forwarded','Forwarded');
		$server_flags = array();

		//crmv@54310	crmv@70424
		if (count($cache_list) <= 2000) {
			$server_flags_tmp = self::$protocol->fetch(array('UID','FLAGS'),array_keys($cache_list));	// read only messages already cached
		} else {	// read all messages
			//$server_flags_tmp = self::$protocol->fetch(array('UID','FLAGS'),1,INF);
			$server_flags_tmp = array();
			$tmp_cache_list = array_chunk($cache_list, 2000, true);
			foreach($tmp_cache_list as $tmp1) {
				$tmp2 = self::$protocol->fetch(array('UID','FLAGS'),array_keys($tmp1));
				foreach($tmp2 as $tmp3) $server_flags_tmp[] = $tmp3;
			}
		}
		//crmv@54310e	crmv@70424e

		foreach ($server_flags_tmp as $i => $info) {
			$uid = $info['UID'];
			//crmv@49432
			$flags = array_map('format_flags', $info['FLAGS']);
			$server_flags[$uid] = array_intersect($flags,$managed_flags);
			//crmv@49432e
			if (in_array('Forwarded',$server_flags[$uid]) && in_array('$Forwarded',$server_flags[$uid])) {
				$server_flags[$uid] = array_diff($server_flags[$uid],array('$Forwarded'));
			}
			if (empty($server_flags[$uid])) $server_flags[$uid] = array();
			if (empty($cache_flags[$uid])) $cache_flags[$uid] = array();
			$inter = array_intersect($server_flags[$uid],$cache_flags[$uid]);
			if (count($inter) != count($server_flags[$uid]) || count($inter) != count($cache_flags[$uid])) { 
				//$inters[$uid] = array('server'=>$server_flags[$uid],'cache'=>$cache_flags[$uid],'inter'=>$inter);
				$this->updateCacheFlags($userid,$uid,$server_flags[$uid]);	//crmv@42701
				$found_changes = true;
			}
		}
		return $found_changes;
	}

	function updateCacheFlags($userid,$uid,$flags) {	//crmv@42701
		global $adb, $table_prefix;

		$sql_update = array(
			'seen = ?' => 0,
			'answered = ?' => 0,
			'flagged = ?' => 0,
			'forwarded = ?' => 0,
		);
		foreach($flags as $flag) {
			switch ($flag) {
				case Zend\Mail\Storage::FLAG_SEEN :
					$sql_update['seen = ?'] = 1;
					break;
				case Zend\Mail\Storage::FLAG_ANSWERED :
					$sql_update['answered = ?'] = 1;
					break;
				case Zend\Mail\Storage::FLAG_FLAGGED :
					$sql_update['flagged = ?'] = 1;
					break;
				case 'Forwarded':
				case '$Forwarded':
					$sql_update['forwarded = ?'] = 1;
					break;
			}
		}
		//crmv@42701	crmv@63611	crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		if ($adb->isMssql()) {
			$query = "UPDATE {$this->table_name}
			SET ".implode(',',array_keys($sql_update))."
			FROM {$this->table_name}
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$this->table_name}.messagesid
			WHERE xuid = ? AND deleted = 0 $setypeCond AND smownerid = ? AND account = ? AND folder = ?";
		} else {
			$query = "UPDATE {$this->table_name}
			INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$this->table_name}.messagesid
			SET ".implode(',',array_keys($sql_update))."
			WHERE xuid = ? AND deleted = 0 $setypeCond AND smownerid = ? AND account = ? AND folder = ?";
		}
		$adb->pquery($query,array($sql_update,$uid,$userid,$this->account,$this->folder));
		//crmv@42701e	crmv@63611e	crmv@64325e
	}

	function getCacheFlags() {
		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT seen, answered, flagged, forwarded FROM {$this->table_name} WHERE {$this->table_name}.messagesid = ?",array($this->id));
		$flags = array();
		if ($result && $adb->num_rows($result) > 0) {
			if ($adb->query_result($result,0,'seen') == '1') {
				$flags[Zend\Mail\Storage::FLAG_SEEN] = Zend\Mail\Storage::FLAG_SEEN;
			}
			if ($adb->query_result($result,0,'answered') == '1') {
				$flags[Zend\Mail\Storage::FLAG_ANSWERED] = Zend\Mail\Storage::FLAG_ANSWERED;
			}
			if ($adb->query_result($result,0,'flagged') == '1') {
				$flags[Zend\Mail\Storage::FLAG_FLAGGED] = Zend\Mail\Storage::FLAG_FLAGGED;
			}
			if ($adb->query_result($result,0,'forwarded') == '1') {
				$flags['$Forwarded'] = '$Forwarded';
				$flags['Forwarded'] = 'Forwarded';
			}
		}
		return $flags;
	}

	function getAddressListString($header_obj,$param) {
		if (get_class($header_obj) != 'ArrayIterator') {
			$header_obj = array($header_obj);
		}
		if ($param == 'full') {
			$return = array();
			foreach ($header_obj as $i) {
				$return[] = $i->toString();
			}
			return implode(', ',$return);
		} else {
			$return = array();
			foreach ($header_obj as $i) {
				$addresslist = $i->getAddressList();
				foreach($addresslist as $address_obj) {
					if ($param == 'email') {
						$return[] = $address_obj->getEmail();
					} elseif ($param == 'name') {
						$return[] = $address_obj->getName();
					}
				}
			}
			if ($param == 'email') {
				//return last element of array -> fix this cases '"Lastname, firstname.lastname@domain.com' -> 'firstname.lastname@domain.com'
				return $return[sizeof($return)-1];
			} else {
				return implode(', ',array_filter($return));
			}
		}
	}

	function getMessageHeader($message) {
		$headerkeys_addr_type = array('From','To','ReplyTo','Cc','Bcc');
		$headerkeys = array('From','To','ReplyTo','Cc','Bcc','Date','Subject','Sender','Messageid','Xmailer','In-Reply-To','References','Thread-Index','X-Rcpt-To','X-MDRcpt-To','X-MDArrival-Date','Content-Class','Delivery-Date'); // crmv@64178	crmv@84628 crmv@86123
		$return = array();
		$squirrelmail = new Squirrelmail($this,true);
		foreach($headerkeys as $headerkey) {
			try {
				$isset = isset($message->{strtolower($headerkey)});
			} catch(Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$isset = false;
			}
			if ($isset) {
				if (in_array($headerkey,$headerkeys_addr_type)) {
					try {
						$headerobj = $message->getHeader($headerkey);
					} catch(Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						continue;
					}
					$full = str_replace("$headerkey: ",'',$this->getAddressListString($headerobj,'full'));
					if ($headerkey == 'ReplyTo') {
						$full = str_replace('Reply-To: ','',$full);
					}
					$full = $squirrelmail->decodeHeader($full,true,false);

					$name = $this->getAddressListString($headerobj,'name');
					$name = $squirrelmail->decodeHeader($name,true,false);

					$email = $this->getAddressListString($headerobj,'email');

					$return[$headerkey] = array(
						'email'=>strval($email),
						'name'=>strval($name),
						'full'=>strval($full),
					);
				} else {
					//crmv@49548
					try {
						$value = $message->{strtolower($headerkey)};
					} catch(Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						$headers_arr = $message->getHeaders()->toArray();
						$value = $headers_arr[$headerkey];
					}
					//crmv@49548e
					$return[$headerkey] = strval($squirrelmail->decodeHeader($value,true,false));
				}
			}
		}
		return $return;
	}

	/* crmv@59492 crmv@59094 */
	function getMessageData($message,$id,$include_attach_content=false) {
		global $default_charset;

		$data = array();
		$data['header'] = $this->getMessageHeader($message);
		$data['flags'] = $this->getMessageFlags($message);

		try {
			$dispositionNotificationTo = $message->getHeaderField('Disposition-Notification-To');
		} catch (Exception $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			$dispositionNotificationTo = '';
		}
		if ($this->fetchBodyInCron == 'yes' || ($this->fetchBodyInCron == 'no_disposition_notification_to' && empty($dispositionNotificationTo))) {
			$content = $this->getMessageContentParts($message,$id,$include_attach_content);
			if ($content === false)
				return false;
			elseif (!empty($content))
				$data = array_merge($data, $content);
		}
		if (empty($data['flags']['seen'])) {
			$this->restoreSeenFlag($message,$id);
		}
		if (!empty($data['text/plain'])) {
			$data['text/plain'] = implode("\n\n",$data['text/plain']);
		}
		if (!empty($data['text/html'])) {
			$data['text/html'] = implode('<br><br>',$data['text/html']);
		}
		// crmv@68357
		if (!empty($data['text/calendar'])) {
			$data['text/calendar'] = $this->parseAndSplitIcal($data['text/calendar']);
		}
		// crmv@68357e

		// crmv@64178
		// fix missing headers for some servers (For example: MailDaemon)
		if (empty($data['header']['Messageid'])) {
			// generate a fake messageid
			$uniq_id = md5(strval($uid) . '_' . $data['header']['Subject']);
			$mid = sprintf('<%s@%s>', $uniq_id, 'localhost');
			$data['header']['Messageid'] = $mid;
		}
		// crmv@86123
		if (!empty($data['header']['Date']) && strpos($data['header']['Date'],"\n") !== false) {
			$data['header']['Date'] = substr($data['header']['Date'],0,strpos($data['header']['Date'],"\n"));
		}
		if (empty($data['header']['Date']) && !empty($data['header']['X-MDArrival-Date'])) {
			$data['header']['Date'] = $data['header']['X-MDArrival-Date'];
		}
		if (empty($data['header']['Date']) && !empty($data['header']['Delivery-Date'])) {
			$data['header']['Date'] = $data['header']['Delivery-Date'];
		}
		// crmv@86123e
		if (empty($data['header']['To']['full']) && !empty($data['header']['X-Rcpt-To'])) {
			$data['header']['To']['full'] = $data['header']['X-Rcpt-To'];
		}
		if (empty($data['header']['To']['full']) && !empty($data['header']['X-MDRcpt-To'])) {
			$data['header']['To']['full'] = $data['header']['X-MDRcpt-To'];
		}
		// crmv@64178e

		return $data;
	}

	// crmv@68357
	// parse several ics/ical inline parts and split them in order to have one event/todo per item
	public function parseAndSplitIcal($icals) {

		$list = array();
		if (!is_array($icals)) $icals = array($icals);
		
		foreach ($icals as $icalTxt) {
			$pieces = array();
			//$config = array( "unique_id" => "VTECRM");
			$vcalendar = new VTEvcalendar();
			$r = $vcalendar->parse($icalTxt);
			if ($r === false) continue;
			
			// add the prodid, since it's not read properly
			if (preg_match('/^PRODID:(.*)$/m', $icalTxt, $matches)) {
				$vcalendar->prodid = $matches[1];
			}
			// now parse events and todos, other components are not supported
			while ($piece = $vcalendar->getComponent("vevent")) {
				$pieces[] = $piece;
			}
			while ($piece = $vcalendar->getComponent("vtodo")) {
				$pieces[] = $piece;
			}
			
			if (count($pieces) == 0) {
				continue; // unknown components
			} elseif (count($pieces) == 1) {
				// only 1, output as it was
				$list[] = trim($icalTxt);
			} else {
				// more than 1, must split
				$tzone = $vcalendar->getComponent("vtimezone");
				foreach ($pieces as $piece) {
					$newcal = new VTEvcalendar();
					if ($tzone) $newcal->addComponent($tzone);
					if ($vcalendar->version) $newcal->setVersion($vcalendar->version);
					if ($vcalendar->prodid) $newcal->prodid = $vcalendar->prodid;
					if ($vcalendar->method) $newcal->setMethod($vcalendar->method);
					if ($vcalendar->calscale) $newcal->setCalscale($vcalendar->calscale);
					$newcal->addComponent($piece);
					$out = $newcal->createCalendar();
					if ($out !== false) $list[] = trim($out);
				}
				
			}
		}

		return $list;
	}
	// crmv@68357e
	
	/* crmv@59094 */
	function getMessageContentParts($message,$id,$include_attach_content=false) {
		
		$data = array();
		
		$isMultipart = false;
		try {
			if ($message->isMultipart()) {
				$isMultipart = true;
			}
		} catch (Exception $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
		}

		if (!$isMultipart) {
			try {
				$contentobj = $message->getHeader('Content-Type');
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			}
			if (get_class($contentobj) == 'Zend\Mail\Header\ContentType') {
				$contenttype = strtolower($contentobj->getType());
				$parameters = $contentobj->getParameters();	//crmv@90966
			} elseif (get_class($contentobj) == 'ArrayIterator') {
				foreach ($contentobj as $contenttmp) {
					$contenttype = strtolower($contenttmp->getType());
					$parameters = $contenttmp->getParameters();	//crmv@90966
					break;
				}
			} else {
				$contenttype = 'text/plain';
				$parameters = '';	//crmv@90966
			}
			if (!in_array($contenttype,array('text/plain','text/html', 'text/calendar')) && strpos($contenttype,'text/') !== false) $contenttype = 'text/plain';	//crmv@59605 crmv@68357
			try {
				$charset = $message->getHeaderField('Content-Type', 'charset');
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$charset = '';
			}
			$encoding = '';
			try {
				$isset = (isset($message->contentTransferEncoding) && !empty($message->contentTransferEncoding));
			} catch(Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$isset = false;
			}
			if ($isset) {
				$encoding = $message->contentTransferEncoding;
			}
			$content = $message->__toString($message->getContent());	// <- this slow down!!!!!
			//crmv@90966
			if (!in_array($contenttype,array('text/plain','text/html', 'text/calendar'))) {
				$otherContent = array('content'=>$content);
				$otherContent['parameters'] = $parameters;
				$otherContent['parameters']['contenttype'] = $contenttype;
				if (isset($message->contentdisposition)) {
					$otherContent['parameters']['contentdisposition'] = $message->getHeaderField('contentdisposition');
				} else {
					$otherContent['parameters']['contentdisposition'] = 'attachment';
				}
				if (!empty($charset)) {
					$otherContent['parameters']['charset'] = $charset;
				}
				if (!empty($encoding)) {
					$otherContent['parameters']['encoding'] = $encoding;
				}
				$otherContent['parameters']['size'] = $message->getSize();	//crmv@65328
				try {
					$contentid = $message->getHeader('Content-ID');
					//crmv@58436
					$contentidClass = get_class($contentid);
					if ($contentidClass !== false && $contentidClass == 'ArrayIterator') {
						foreach($contentid as $c) {
							try {
								$contentid = $c->getFieldValue();
								break;
							} catch (Exception $e) {}
						}
					} else {
						$contentid = $contentid->getFieldValue();
					}
					//crmv@58436e
					$contentid = ltrim($contentid,'<');
					$contentid = rtrim($contentid,'>');
					$otherContent['parameters']['content_id'] = $contentid;
				} catch (Exception $e) {}
				//crmv@45179	crmv@43245	crmv@53651
				if (empty($otherContent['parameters']['name'])) {
					$filename = 'Unknown';
					try {
						$filename_tmp = $message->getHeader('Content-Disposition')->getFieldValue();
						$pos = strpos($filename_tmp,'filename=');
						if (!empty($filename_tmp) && $pos !== false) {
							$r = preg_match('/filename="([^"]+)"/', $filename_tmp, $matches);
							if (!empty($matches[1])) $filename = $matches[1];
						}
					} catch (Exception $e) {}
					if ($filename == 'Unknown' && $contenttype == 'message/delivery-status') {
						$filename = 'details.txt';
					}
					if ($filename == 'Unknown' && $contenttype == 'text/rfc822-headers') {
						$filename = 'message.txt';
					}
					$otherContent['parameters']['name'] = $filename;
				}
				//crmv@45179e	crmv@43245e	crmv@53651e
				if (!$include_attach_content) unset($otherContent['content']);	//crmv@59492
				$data['other'][] = $otherContent;
			} else {
			$content = $this->decodePart($content,$encoding,$charset);
			$data[$contenttype][] = $content;
			}
			//crmv@90966e
		} else {
			try {
				foreach (new RecursiveIteratorIterator($message) as $part) {	// <- this slow down!!!!!
					/*
					echo $id.'<br /><pre>';
					print_r($part);
					echo '</pre><br /><br />';
					*/
					try {
						$contentobj = $part->getHeader('Content-Type');
					} catch (Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					}
					if (get_class($contentobj) == 'Zend\Mail\Header\ContentType') {
						$contenttype = strtolower($contentobj->getType());
						$parameters = $contentobj->getParameters();
					} elseif (get_class($contentobj) == 'ArrayIterator') {
						foreach ($contentobj as $contenttmp) {
							$contenttype = strtolower($contenttmp->getType());
							$parameters = $contenttmp->getParameters();
							break;
						}
					} else {
						$contenttype = 'text/plain';
						$parameters = '';
					}
					try {
						$charset = $part->getHeaderField('Content-Type', 'charset');
					} catch (Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						$charset = '';
					}
					$encoding = '';
					try {
						$isset = (isset($part->contentTransferEncoding) && !empty($part->contentTransferEncoding));
					} catch(Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						$isset = false;
					}
					if ($isset) {
						$encoding = $part->contentTransferEncoding;
					}
					$content = $part->__toString($part->getContent());

					// text/html content
					$txtContent = $this->decodePart($content,$encoding,$charset);

					// attachment content
					$otherContent = array('content'=>$content);
					$otherContent['parameters'] = $parameters;
					$otherContent['parameters']['contenttype'] = $contenttype;
					if (isset($part->contentdisposition)) {
						$otherContent['parameters']['contentdisposition'] = $part->getHeaderField('contentdisposition');
					}
					if (!empty($charset)) {
						$otherContent['parameters']['charset'] = $charset;
					}
					if (!empty($encoding)) {
						$otherContent['parameters']['encoding'] = $encoding;
					}
					$otherContent['parameters']['size'] = $part->getSize();	//crmv@65328
					try {
						$contentid = $part->getHeader('Content-ID');
						//crmv@58436
						$contentidClass = get_class($contentid);
						if ($contentidClass !== false && $contentidClass == 'ArrayIterator') {
							foreach($contentid as $c) {
								try {
									$contentid = $c->getFieldValue();
									break;
								} catch (Exception $e) {
									$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
								}
							}
						} else {
							$contentid = $contentid->getFieldValue();
						}
						//crmv@58436e
						$contentid = ltrim($contentid,'<');
						$contentid = rtrim($contentid,'>');
						$otherContent['parameters']['content_id'] = $contentid;
					} catch (Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					}

					// check if is text/html part or attachment
					$isText = false;
					if (in_array($contenttype,array('text/plain','text/html', 'text/calendar'))) { // crmv@68357
						$isText = true;
					}
					//crmv@46629
					if (in_array($contenttype,array('text/plain','text/html', 'text/calendar')) && $otherContent['parameters']['contentdisposition'] == 'inline') { // crmv@68357
						$isText = true;
					//crmv@46629e
					} elseif (!in_array($contenttype,array('text/plain','text/html', 'text/calendar')) || !empty($otherContent['parameters']['name']) || !empty($otherContent['parameters']['contentdisposition'])) { // crmv@68357
						if ($isText && $contenttype == 'text/calendar') $data[$contenttype][] = $txtContent; // crmv@68357, split it as an attachment + ical
						$isText = false;
						//crmv@45179	crmv@43245	crmv@53651
						if (empty($otherContent['parameters']['name'])) {
							$filename = 'Unknown';
							try {
								$filename_tmp = $part->getHeader('Content-Disposition')->getFieldValue();
								$pos = strpos($filename_tmp,'filename=');
								if (!empty($filename_tmp) && $pos !== false) {
									$r = preg_match('/filename="([^"]+)"/', $filename_tmp, $matches);
									if (!empty($matches[1])) $filename = $matches[1];
								}
							} catch (Exception $e) {
								$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
							}
							if ($filename == 'Unknown' && $contenttype == 'message/delivery-status') {
								$filename = 'details.txt';
							}
							if ($filename == 'Unknown' && $contenttype == 'text/rfc822-headers') {
								$filename = 'message.txt';
							}
							//crmv@90697
							if ($filename == 'Unknown' && stripos($contenttype,'image/') === 0) {
								$extension = substr($contenttype,6);
								if(in_array(strtolower($extension),$this->inline_image_supported_extensions)){
									$filename .= '.'.$extension;
								}
							}
							if ($filename == 'Unknown' && empty($otherContent['parameters']['contentdisposition'])) {
								$otherContent['parameters']['contentdisposition'] = 'attachment';
							}
							//crmv@90697e
							$otherContent['parameters']['name'] = $filename;
						}
						//crmv@45179e	crmv@43245e	crmv@53651e
					}
					if ($isText) {
						$data[$contenttype][] = $txtContent;
					} else {
						if (!$include_attach_content) unset($otherContent['content']);	//crmv@59492
						$data['other'][] = $otherContent;
					}
				}
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				if ($e->getMessage() == 'Not a valid Mime Message: End Missing') {
					return false;
				}
			}
		}
		return $data;
	}

	function getMessageFlags($message) {
		$flags = array(
			'seen' => '',
			'answered' => '',
			'flagged' => '',
			'forwarded' => '',
			'draft' => '',	//crmv@84628
		);
		if ($message->hasFlag(Zend\Mail\Storage::FLAG_SEEN)) {
			$flags['seen'] = 'on';
		}
		if ($message->hasFlag(Zend\Mail\Storage::FLAG_ANSWERED)) {
			$flags['answered'] = 'on';
		}
		if ($message->hasFlag(Zend\Mail\Storage::FLAG_FLAGGED)) {
			$flags['flagged'] = 'on';
		}
		if ($message->hasFlag('Forwarded') || $message->hasFlag('$Forwarded')) {
			$flags['forwarded'] = 'on';
		}
		//crmv@84628
		if ($message->hasFlag(Zend\Mail\Storage::FLAG_DRAFT)) {
			$flags['draft'] = 'on';
		}
		//crmv@84628e
		return $flags;
	}

	function restoreSeenFlag($message,$id) {
		$flags = $message->getFlags();
		//crmv@49432
		unset($flags[array_search(Zend\Mail\Storage::FLAG_SEEN, $flags)]);
		unset($flags[array_search(Zend\Mail\Storage::FLAG_RECENT, $flags)]);	// error to set recent flag
		//crmv@49432e
		if (!empty(self::$mail)) //crmv@90941
		try {
			self::$mail->setFlags($id, $flags);
		} catch (Zend\Mail\Storage\Exception\RuntimeException $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
		}
	}

	function decodePart($content,$encoding='',$charset='') {
		global $default_charset;
		if (isset($encoding)) {
			switch (strtolower($encoding)) {	//crmv@46629
				case 'base64':
					$content = base64_decode($content);
					break;
				case 'quoted-printable':
					$content = quoted_printable_decode($content);
					break;
			}
		}
		//crmv@54247 crmv@80351
		if (function_exists('mb_detect_encoding')) {
			// add here new encodings to check, pay attention to the order!
			if (strtolower(substr($charset, 0, 4)) == 'iso-') {
				// use the provided charset as the fallback during detection,
				// since there is no way to tell from the different ISO charsets
				$encorder = 'ASCII,UTF-8,'.strtoupper($charset);
			} else {
				// otherwise do it as usual
				$encorder = 'ASCII,UTF-8,ISO-8859-1';
			}
			$detect_charset = mb_detect_encoding($content, $encorder);
			if (!empty($detect_charset)) $charset = $detect_charset;
		}
		//crmv@54247e crmv@80351e
		//crmv@90390
		$content_encoded = correctEncoding($content, $default_charset, $charset);
		if ($content_encoded !== false) $content = $content_encoded;
		//crmv@90390e
		return $content;
	}
	
	function decodeAttachment($content,$encoding='',$charset='') {
		global $default_charset;
		if (isset($encoding)) {
			switch (strtolower($encoding)) {	//crmv@46629
				case 'base64':
					$content = base64_decode($content);
					break;
				case 'quoted-printable':
					$content = quoted_printable_decode($content);
					break;
			}
		}
		return $content;
	}
	
	function propagateEmpty($userid,$account,$folder) {
		global $adb, $table_prefix, $current_user;
		$tmp_current_user_id = $current_user->id;
		$current_user->id = $userid;
						
		$focus = CRMEntity::getInstance('Messages');
		$focus->setAccount($account);
		$focus->getZendMailStorageImap($userid);
		
		$focus->emptyFolder($folder);
		$focus->syncFolder($folder);
		$focus->reloadCacheFolderCount($userid,$account,$folder);
		
		$current_user->id = $tmp_current_user_id;
	}

	/* crmv@56636 */
	function emptyFolder($folder) {
		
		// delete messages in folder
		self::$mail->selectFolder($folder);
		$uids = $this->getServerUids();
		if (!empty($uids)) {
			foreach($uids as $messageId => $uid) {
				//$messageId = self::$mail->getNumberByUniqueId($uid);
				try {
					self::$mail->removeMessage($messageId);
				} catch (Zend\Mail\Storage\Exception\RuntimeException $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				}
			}
		}
		
		// delete messages in subfolders
		$subfolders = array();
		try {
			$folders = self::$mail->getFolders($folder);
			if ($folders->getGlobalName() == $this->folderSeparator) {	// check to have the correct tree
				$folders_it = new RecursiveIteratorIterator($folders,RecursiveIteratorIterator::CHILD_FIRST);
				foreach ($folders_it as $tmp_folders) {
					if ($tmp_folders->getGlobalName() == $folder) {
						$folders = $tmp_folders;
						break;
					}
				}
			}
			$specialFolders = $this->getSpecialFolders();
			$folders_it = new RecursiveIteratorIterator($folders,RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($folders_it as $localName => $leave_folder) {
				if ($leave_folder == $folder || in_array($leave_folder,$specialFolders)) {	// check to not delete Trash folder or other special folders
					continue;
				}
				$leave_folder = htmlspecialchars($leave_folder);
				self::$mail->selectFolder($leave_folder);
				$uids = $this->getServerUids();
				if (!empty($uids)) {
					foreach($uids as $messageId => $uid) {
						//$messageId = self::$mail->getNumberByUniqueId($uid);
						self::$mail->removeMessage($messageId);
					}
				}
				$subfolders[] = $leave_folder;
			}
		} catch (Zend\Mail\Exception\InvalidArgumentException $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
		}
		
		// delete subfolders
		if (!empty($subfolders)) {
			try {
				self::$mail->selectFolder($folder);
				foreach ($subfolders as $leave_folder) {
					self::$mail->removeFolder($leave_folder);
				}
			} catch (Zend\Mail\Exception\InvalidArgumentException $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			}
		}
	}

	function flagFolder($account,$folder,$flag) {
		global $adb, $table_prefix, $current_user;
		
		if ($flag == 'seen') {
			$field = 'seen';
			$value = 1;
		} elseif ($flag == 'unseen') {
			$field = 'seen';
			$value = 0;
		}
		
		$this->addToPropagationCron('flag_folder', array('userid'=>$current_user->id,'account'=>$account,'folder'=>$folder,'flag'=>$field,'value'=>$value));
		
		//crmv@63611
		if ($adb->isMssql()) {
			$adb->pquery("UPDATE {$this->table_name}
				SET $field = ?
				FROM {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
				WHERE {$this->entity_table}.deleted = 0 AND {$this->table_name}.mtype = ? AND {$this->entity_table}.smownerid = ? AND {$this->table_name}.account = ? AND {$this->table_name}.folder = ? AND $field <> ? ",array($value,'Webmail',$current_user->id,$account,$folder,$value)); //crmv@57797
		} else {
			$adb->pquery("UPDATE {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
				SET $field = ?
				WHERE {$this->entity_table}.deleted = 0 AND {$this->table_name}.mtype = ? AND {$this->entity_table}.smownerid = ? AND {$this->table_name}.account = ? AND {$this->table_name}.folder = ? AND $field <> ? ",array($value,'Webmail',$current_user->id,$account,$folder,$value)); //crmv@57797
		}
		//crmv@63611e

		if ($field == 'seen') {
			$this->reloadCacheFolderCount($current_user->id,$account,$folder);
		}
	}
	
	function propagateFlagFolder($userid,$account,$folder,$flag,$value) {
		$focus = CRMEntity::getInstance('Messages');

		$focus->setAccount($account);
		$focus->getZendMailStorageImap($userid);
		self::$mail->selectFolder($folder);

		$managed_flags = array(Zend\Mail\Storage::FLAG_SEEN,Zend\Mail\Storage::FLAG_ANSWERED,Zend\Mail\Storage::FLAG_FLAGGED,'$Forwarded','Forwarded');
		$server_flags = array();
		$server_flags_tmp = self::$protocol->fetch(array('UID','FLAGS'),1,INF);
		foreach ($server_flags_tmp as $i => $info) {
			$uid = array_shift($info);
			//crmv@49432
			$flags = array_map('format_flags', $info['FLAGS']);
			$oldflags = $flags = array_intersect($flags,$managed_flags);
			//crmv@49432e
			if ($flag == 'seen') {
				if ($value == 0) {
					unset($flags[array_search(Zend\Mail\Storage::FLAG_SEEN,$flags)]);
				} elseif ($value == 1 && !in_array(Zend\Mail\Storage::FLAG_SEEN,$flags)) {
					$flags[] = Zend\Mail\Storage::FLAG_SEEN;
				}
			}
			$server_flags[$uid] = $flags;
			try {
				self::$mail->setFlags($i, $flags);
			} catch (Zend\Mail\Storage\Exception\RuntimeException $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			}
		}
	}

	function folderMove($account,$folder,$move_in) {
		$this->setAccount($account);
		$specialFolders = $this->getSpecialFolders();
		if (in_array($folder,$specialFolders)) {
			return false;
		}
		$this->getZendMailStorageImap();
		$folder_tree = explode($this->folderSeparator,$folder);
		$move_in .= $this->folderSeparator.$folder_tree[count($folder_tree)-1];	//crmv@47411
		try {
			self::$mail->renameFolder($folder,$move_in);
			return true;
		} catch (Exception $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			/*
			try {
				// new folder already exixsts
				$folder = self::$mail->getFolders();
				foreach($folder_tree as $f) {
					$folder = $folder->__get($f);
				}
			} catch (Exception $e) {
				// old folder do not exists
			}
			*/
			return false;
		}
	}

	function folderCreate($account,$folder,$current_folder) {
		$this->setAccount($account);
		$this->getZendMailStorageImap();
		try {
			//crmv@91187
			global $default_charset;
			if (function_exists('mb_convert_encoding')) {
				$folder = mb_convert_encoding($folder, "UTF7-IMAP",$default_charset);
			}
			//crmv@91187e
			self::$mail->createFolder($folder,$current_folder,$this->folderSeparator);	//crmv@47411
		} catch (Exception $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			return false;
		}
	}

	function getFoldersList($mode='list',$current_folder='',$move_mode='') {
		global $adb, $table_prefix, $current_user;
		
		$folders = array();
		$query = "select * from {$table_prefix}_messages_folders where userid = ? and accountid = ?";
		$params = array($current_user->id,$this->getAccount());
		if ($mode != 'list') {
			$query .= " and localname not in (?,?,?)";
			$params[] = 'Shared';
			$params[] = 'Links';
			$params[] = 'Flagged';
		}
		if ($move_mode != 'folders') {
			$query .= " and localname <> ?";
			$params[] = '/';
		}
		$query .= " order by sequence";
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			//crmv@61520
			while($row=$adb->fetchByAssoc($result, -1, false)) {
				$localName = htmlentities(str_replace("\x00", '', imap_utf7_decode($row['localname'])), ENT_NOQUOTES, 'ISO-8859-1');
				$globalName = htmlentities(str_replace("\x00", '', imap_utf7_decode($row['globalname'])), ENT_NOQUOTES, 'ISO-8859-1');
				switch ($localName) {
					case '/':
						$folders[($globalName)] = array(
							'label'=>'<span style="padding-right:10px;color:#464646">'.($localName).'</span>'.getTranslatedString('LBL_ROOT','Messages'),
							//crmv@61520 e
							'selectable'=>true,
							'depth'=>0
						);
						break;
					case 'Shared':
					case 'Flagged':
					case 'Links':
						if ($row['count'] > 0) {
							$arr = array(
								'label'=>getTranslatedString('LBL_Folder_'.$localName,'Messages'),
								'selectable'=>$row['selectable'],
								'depth'=>$row['depth'],
								'count'=>$row['count']
							);
							if ($localName == 'Links') {
								$arr['img'] = 'modules/Messages/src/img/folder.png';
							} else {
								$arr['img'] = 'modules/Messages/src/img/folder_'.strtolower($localName).'.png';
							}
							$folders[$globalName] = $arr;
						}
						break;
					default:
						$specialFolders = $this->getSpecialFolders();
						$aliasSpecialFolder = array_search($globalName,$specialFolders);
						if (!empty($aliasSpecialFolder)) {
							$label = $aliasSpecialFolder;
						} else {
							$label = ($localName); //crmv@61520
						}
						$label_trans = getTranslatedString('LBL_Folder_'.$label,'Messages');
						if ($label_trans != 'LBL_Folder_'.$label) {
							$label = $label_trans;
						}
						if (!empty($aliasSpecialFolder)) {
							$img_str = strtolower($aliasSpecialFolder);
						} else {
							$img_str = strtolower($localName);
						}
						$img = 'modules/Messages/src/img/folder.png';	//default image
						if (file_exists('modules/Messages/src/img/folder_'.$img_str.'.png')) {
							$img = 'modules/Messages/src/img/folder_'.$img_str.'.png';
						}
						$arr = array(
							'label'=>$label,
							'img'=>$img,
							'selectable'=>$row['selectable'],
							'depth'=>$row['depth'],
							'count'=>$row['count'],
							'bg_notification_color'=>'#2c80c8'
						);
						if ($mode == 'move' && !empty($current_folder) && $globalName == $current_folder) {
							$arr['selectable'] = false;
						}
						$folders[$globalName] = $arr;
						break;
				}
			}
		/*
		} else {
			// force sync
			$this->syncFolders($current_user->id,$this->getAccount());
			$folders = $this->getFoldersList($mode,$current_folder,$move_mode);
		*/
		}
		return $folders;
	}

	function getStrUnreadMessageCount($folder='') {
		$string = '';
		$count = $this->getUnreadMessageCount($folder);
		if (intval($count) > 0) {
			$string = ' ('.$count.')';
		}
		return $string;
	}

	function getUnreadMessageCount($folder='') {
		if (empty($folder)) {
			global $current_folder;
			$folder = $current_folder;
		}
		$account = $this->getAccount();
		if (!empty($folder)) {
			global $adb, $table_prefix, $current_user;
			// crmv@64325
			$setypeCond = '';
			if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
				$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
			}
			$query = "SELECT count(*) AS count FROM {$this->table_name}
					INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
					WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.seen = ? and {$this->table_name}.mtype = ?";
			// crmv@64325e
			$params = array($current_user->id,0,'Webmail');
			if ($account == 'all') {
				$folders = $this->getAllSpecialFolders('INBOX');
				$tmp = array();
				foreach($folders as $account => $folder) {
					$tmp[] = "({$this->table_name}.account = ? AND {$this->table_name}.folder = ?)";
					$params[] = array($account,$folder['INBOX']);
				}
				$query .= ' AND ('.implode(' OR ',$tmp).')';
			// crmv@42537
			} elseif ($folder == 'any') {
				$query .= ' AND account = ?';
				$params[] = array($account);
			// crmv@42537e
			} else {
				$query .= " and account = ? and folder = ?";
				$params[] = array($account,$folder);
			}
			$result = $adb->pquery($query,$params);
			if ($result && $adb->num_rows($result) > 0) {
				return $adb->query_result($result,0,'count');
			}
		} else {
			return false;
		}
	}

	// crmv@63349
	public function getRelatedModComments($return_query=false, $userid='') {
		if (PerformancePrefs::getBoolean('USE_TEMP_TABLES', true)) {
			return $this->getRelatedModComments_tmp($return_query, $userid);
		} else {
			return $this->getRelatedModComments_notmp($return_query, $userid);
		}
	}

	public function getRelatedModComments_notmp($return_query=false, $userid='') {
		global $adb, $table_prefix, $current_user;

		if (empty($userid)) {
			global $current_user;
			$user = $current_user;
		} else {
			$user = CRMEntity::getInstance('Users');
			$user->retrieveCurrentUserInfoFromFile($userid);
		}

		$query = "SELECT messagesid AS \"id\" FROM {$table_prefix}_modcomments_msgrel WHERE userid = ?";
		$params = array($user->id);
		if ($return_query) {
			return $adb->convert2Sql($query,$adb->flatten_array($params));
		}
		$result = $adb->pquery($query,$params);
		$tmp = array();
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result, -1, false)) {
				$tmp[] = $row['id'];
			}
		}	
		return $tmp;
	}
	
	public function isMessageRelatedModComments($messageId) {
		global $adb, $table_prefix, $current_user;
		
		$cnt = 0;
		$result = $adb->pquery("SELECT COUNT(*) AS cnt FROM {$table_prefix}_modcomments_msgrel WHERE userid = ? AND messagesid = ?", array($current_user->id, $messageId));
		if ($result && $adb->num_rows($result) > 0) {
			$cnt = intval($adb->query_result_no_html($result, 0, 'cnt'));
		}	
		return ($cnt > 0);
	}
	
	public function countRelatedModComments() {
		global $adb, $table_prefix, $current_user;
		
		$cnt = 0;
		$result = $adb->pquery("SELECT COUNT(*) AS cnt FROM {$table_prefix}_modcomments_msgrel WHERE userid = ?", array($current_user->id));
		if ($result && $adb->num_rows($result) > 0) {
			$cnt = intval($adb->query_result_no_html($result, 0, 'cnt'));
		}	
		return $cnt;
	}
	
	public function regenCommentsMsgRelTable($userid, $messagesid = 0) {
		global $adb, $table_prefix;
		
		if (empty($userid)) {
			global $current_user;
			$user = $current_user;
		} else {
			$user = CRMEntity::getInstance('Users');
			$user->retrieveCurrentUserInfoFromFile($userid);
		}
		
		// clean
		$this->cleanCommentsMsgRelTable($userid, $messagesid);

		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$this->entity_table}.setype = 'ModComments' AND relatedEntity.setype = 'Messages'";
		}
		
		//crmv@58931 crmv@60402
		$params = Array();
		if($adb->isMssql() || $adb->isOracle()){
			$col_arr = array('user');
			$adb->format_columns($col_arr);
			$userCol = $col_arr[0];
		} else {
			$userCol = 'user';
		}	
		
		$idCol = 'ID';	// leave uppercase, or oracle will have problems
		$adb->format_columns($idCol);
		
		$msgidSql = '';
		if ($messagesid > 0) {
			$msgidSql = "AND {$table_prefix}_messages.messagesid = $messagesid";
		}
		
		if ($user->column_fields['receive_public_talks'] == '1') {
			$query1 = "SELECT {$table_prefix}_modcomments.related_to AS \"ID\", {$table_prefix}_modcomments.modcommentsid
			FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			WHERE {$this->entity_table}.deleted = 0 $msgidSql AND relatedEntity.deleted = 0 $setypeCond and visibility_comm = ? AND {$this->entity_table}.smownerid <> ? AND {$table_prefix}_modcomments.parent_comments = 0";
			$params[] = 'All';
			$params[] = $user->id;
			$query2 = "SELECT {$table_prefix}_modcomments.related_to AS \"ID\", {$table_prefix}_modcomments.modcommentsid
			FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			INNER JOIN {$table_prefix}_modcomments_users ON {$table_prefix}_modcomments_users.$idCol = {$table_prefix}_modcomments.modcommentsid
			WHERE {$this->entity_table}.deleted = 0 $msgidSql AND relatedEntity.deleted = 0 $setypeCond AND visibility_comm = ? AND {$table_prefix}_modcomments_users.{$userCol} = ? AND {$table_prefix}_modcomments.parent_comments = 0";
			$params[] = 'Users';
			$params[] = $user->id;			
			$query = "select t.$idCol, MIN(t.modcommentsid) AS \"modcommentsid\" from ($query1 union $query2) t GROUP BY $idCol";
		} else {
			$query = "SELECT {$table_prefix}_modcomments.related_to AS \"ID\", MIN({$table_prefix}_modcomments.modcommentsid) AS \"modcommentsid\"
			FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			INNER JOIN {$table_prefix}_modcomments_users ON {$table_prefix}_modcomments_users.$idCol = {$table_prefix}_modcomments.modcommentsid
			WHERE {$this->entity_table}.deleted = 0 AND $msgidSql relatedEntity.deleted = 0 $setypeCond AND visibility_comm = ? AND {$table_prefix}_modcomments_users.{$userCol} = ? AND {$table_prefix}_modcomments.parent_comments = 0
			GROUP BY {$table_prefix}_modcomments.related_to";
			$params[] = 'Users';
			$params[] = $user->id;
		}
		$q = $adb->convert2Sql($query,$adb->flatten_array($params));
		//crmv@58931e crmv@64325e crmv@60402e

		// now insert into the table
		$q = "INSERT INTO {$table_prefix}_modcomments_msgrel (userid, messagesid) SELECT $userid as userid, tcomments.$idCol FROM ($q) tcomments";
		$adb->query($q);
	}
	
	/**
	 * Clean the Talks-Messages table for the specified user or messageid
	 */
	public function cleanCommentsMsgRelTable($userid = 0, $messagesid = 0) {
		global $adb, $table_prefix;
		
		$params = array();
		$wheres = array();
		$q = "DELETE FROM {$table_prefix}_modcomments_msgrel";
		
		if ($userid > 0) {
			$wheres[] = "userid = ?";
			$params[] = $userid;
		}
		if ($messagesid > 0) {
			$wheres[] = "messagesid = ?";
			$params[] = $messagesid;
		}
		if (count($wheres) > 0) {
			$q .= " WHERE ".implode(' AND ', $wheres);
		}
		
		$adb->pquery($q, $params);
	}
	// crmv@63349e

	function getRelatedModComments_tmp($return_query=false,$userid='') { // crmv@63349
		
		if (empty($userid)) {
			global $current_user;
			$user = $current_user;
		} else {
			$user = CRMEntity::getInstance('Users');
			$user->retrieveCurrentUserInfoFromFile($userid);
		}
		
		static $ids_msg_with_comments = array();
		static $ids_msg_with_comments_presence = array();
		if (empty($ids_msg_with_comments_presence[$user->id])) $ids_msg_with_comments_presence[$user->id] = false;
		if (!$return_query && $ids_msg_with_comments_presence[$user->id]) {
			return $ids_msg_with_comments[$user->id];
		}
		
		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND relatedEntity.setype = 'Messages'";
		}
		
		global $adb, $table_prefix;
		//crmv@58931 crmv@60402
		$params = Array();
		if($adb->isMssql()){
			$col_arr = array('user');
			$adb->format_columns($col_arr);
			$userCol = $col_arr[0];
		} else {
			$userCol = 'user';
		}	
		
		if ($user->column_fields['receive_public_talks'] == '1') {
			$query1 = "SELECT {$table_prefix}_modcomments.related_to AS \"id\" FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			WHERE {$this->entity_table}.deleted = 0 AND relatedEntity.deleted = 0 $setypeCond and visibility_comm = ? AND {$this->entity_table}.smownerid <> ? group by {$table_prefix}_modcomments.related_to";
			$params[] = 'All';
			$params[] = $user->id;
			$query2="SELECT {$table_prefix}_modcomments.related_to AS \"id\" FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			INNER JOIN {$table_prefix}_modcomments_users ON {$table_prefix}_modcomments_users.id = {$table_prefix}_modcomments.modcommentsid
			WHERE {$this->entity_table}.deleted = 0 AND relatedEntity.deleted = 0 $setypeCond AND visibility_comm = ? AND {$table_prefix}_modcomments_users.{$userCol} = ?  group by {$table_prefix}_modcomments.related_to";
			$params[] = 'Users';
			$params[] = $user->id;			
			$query = "select id from ($query1 union $query2) t group by id";
		} else {
			$query="SELECT {$table_prefix}_modcomments.related_to AS \"id\" FROM {$table_prefix}_modcomments
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$table_prefix}_modcomments.modcommentsid
			INNER JOIN {$this->entity_table} relatedEntity ON relatedEntity.crmid = {$table_prefix}_modcomments.related_to
			INNER JOIN {$table_prefix}_messages ON {$table_prefix}_messages.messagesid = relatedEntity.crmid
			INNER JOIN {$table_prefix}_modcomments_users ON {$table_prefix}_modcomments_users.id = {$table_prefix}_modcomments.modcommentsid
			WHERE {$this->entity_table}.deleted = 0 AND relatedEntity.deleted = 0 $setypeCond AND visibility_comm = ? AND {$table_prefix}_modcomments_users.{$userCol} = ?  group by {$table_prefix}_modcomments.related_to";
			$params[] = 'Users';
			$params[] = $user->id;				
		}
		//crmv@58931e crmv@64325e crmv@60402e
		if ($return_query) {
			return $adb->convert2Sql($query,$adb->flatten_array($params));
		}
		$result = $adb->pquery($query,$params);
		$tmp = array();
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$tmp[] = $row['id'];
			}
		}
		
		$ids_msg_with_comments[$user->id] = $tmp;
		$ids_msg_with_comments_presence[$user->id] = true;
		
		return $tmp;
	}

	function magicHTML($body, $uid, $data='') {
		$squirrelmail = new Squirrelmail($this,true);
		// quick and dirty trick to remove scripts, because the method magicHTML is buggy
		// it removes everything between 2 tags, but without checking if they are closed in the
		// middle
		$body = preg_replace('/<script\s.*?<\/script>/i', '', $body);
		$html = $squirrelmail->magicHTML($body, $uid, $data, $this->folder, false);	//TODO ultimo parametro per convertire i mailto:... in link interni
		$content_ids = $squirrelmail->getContentIds();
		return array('html'=>$html,'content_ids'=>$content_ids);
	}

	// crmv@49398
	function saveCleanedBody($messageid, $body, $content_ids = array()) {
		global $adb, $table_prefix;
		$messageid = intval($messageid);
		if (is_array($content_ids)) $content_ids = Zend_Json::encode($content_ids);
		$adb->pquery("update {$this->table_name} set content_ids = ? where {$this->table_index} = ?", array($content_ids, $messageid));
		$adb->updateClob($this->table_name,'cleaned_body',"{$this->table_index} = $messageid",$body);
	}
	// crmv@49398e
	
	//crmv@59097
	function stripHTML($body) {
		
		//To remove all the hidden text not displayed on a webpage
		function strip_html_tags($str){
		    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
		    $str = preg_replace(
		        array(// Remove invisible content
		            '@<head[^>]*?>.*?</head>@siu',
		            '@<style[^>]*?>.*?</style>@siu',
		            '@<script[^>]*?.*?</script>@siu',
		            '@<noscript[^>]*?.*?</noscript>@siu',
		            ),
		        "", //replace above with nothing
		        $str );
		    $str = replaceWhitespace($str);
		    $str = strip_tags($str);
		    return $str;
		}
		
		//To replace all types of whitespace with a single space
		function replaceWhitespace($str) {
		    $result = $str;
		    foreach (array(
		    "  ", " \t",  " \r",  " \n",
		    "\t\t", "\t ", "\t\r", "\t\n",
		    "\r\r", "\r ", "\r\t", "\r\n",
		    "\n\n", "\n ", "\n\t", "\n\r",
		    ) as $replacement) {
		    	$result = str_replace($replacement, $replacement[0], $result);
		    }
		    return $str !== $result ? replaceWhitespace($result) : $result;
		}
		
		$body = strip_html_tags($body);
		$body = replaceWhitespace($body);
		
		return $body;
	}
	//crmv@59097e

	function getRecipientEmails($field='') {
		if (empty($field)) {
			$fields = array('mto','mcc','mbcc');
		} else {
			$fields = array($field);
		}
		$recipients = array();
		foreach ($fields as $field) {
			$email = $this->column_fields[$field];
			$full = $this->column_fields[$field.'_f'];
			if (substr_count($full,',') > substr_count($email,',')) {
				$email = trim($full);
			}
			if (strpos($email,',') !== false) {
				$emails = explode(',',$email);
			} else {
				$emails = array($email);
			}
			if (!empty($emails)) {
				foreach($emails as $email) {
					$recipients[$field][] = $this->cleanEmail($email);
				}
			}
		}
		return $recipients;
	}

	function getAddressName($email,$name,$full,$textlength_check=false) {
		global $default_charset;
		$name = html_entity_decode($name, ENT_QUOTES, $default_charset);
		$name = trim(trim($name),'"');
		if (empty($name)) {
			$name = trim($email);
		}
		if (substr_count($full,',') > substr_count($email,',')) {
			$name = trim($full);
		}
		if ($textlength_check) {
			global $listview_max_textlength;
			$listview_max_textlength_tmp = $listview_max_textlength;
			$listview_max_textlength = 30;
			$name = textlength_check($name);
			$listview_max_textlength = $listview_max_textlength_tmp;
		}
		return $name;
	}

	function getAddressImage($mode,$email,$business_card) {
		if (strpos($email,', ') !== false) {
			$avatar = 'modules/Messages/src/img/avatar_multiple.png';
			$img = "<img src='$avatar' border='0'>";
			return $img;
		}
		$module = $business_card['module'];
		$id = $business_card['id'];
		$name = $business_card['name'];
		$type = getSingleModuleName($module);
		switch ($module) {
			case 'Users':
				$avatar = getUserAvatar($id);
				$img = "<img title='$type' alt='$name' src='$avatar' border='0' class='userAvatar'>";
				break;
			case 'Contacts':
				$avatar = 'modules/Messages/src/img/avatar_contact.png';
				$img = "<img title='$type' alt='$name' src='$avatar' border='0'>";
				break;
			case 'Accounts':
				$avatar = 'modules/Messages/src/img/avatar_account.png';
				$img = "<img title='$type' alt='$name' src='$avatar' border='0'>";
				break;
			case 'Leads':
				$avatar = 'modules/Messages/src/img/avatar_lead.png';
				$img = "<img title='$type' alt='$name' src='$avatar' border='0'>";
				break;
			case 'Vendors':
				$avatar = 'modules/Messages/src/img/avatar_vendor.png';
				$img = "<img title='$type' alt='$name' src='$avatar' border='0'>";
				break;
			default:
				$avatar = 'modules/Messages/src/img/avatar_new.png';
				if ($mode == 'addsender') {
					$title = getTranslatedString('Add sender','Messages');
				} elseif ($mode == 'addrecipient') {
					$title = getTranslatedString('Add recipient','Messages');
				}
				$img = "<a href='javascript:;' onClick=\"LPOP.openPopup('Messages', '{$this->id}','{$mode}');\"><img title='$title' alt='$title' src='$avatar' border='0'></a>"; // crmv@43864
				break;
		}
		return $img;
	}

	function saveCache($ids) {

		if (empty($ids)) return;

		global $adb, $table_prefix;
		global $current_user, $default_charset;

		$filtered = array();	// apply filters
		$crmid = array();
		$skipped_uids = array();
		$err_uids = array();	//crmv@50124
		foreach ($ids as $messageId => $uid) {
			/*
			$uid = self::$mail->getUniqueId($messageId);
			$uid = 17130;
			$messageId = self::$mail->getNumberByUniqueId($uid);
			*/
			//crmv@62140
			if (empty(self::$mail)) {
				$this->resetMailResource();
				$this->getZendMailStorageImap();
				$this->selectFolder($this->folder);
			}
			//crmv@62140e
			try {
				$message = self::$mail->getMessage($messageId);
			} catch (Zend\Mail\Exception\RuntimeException $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				// ignore parse errors and continue
				//crmv@50124 if error status remain 2 -- $skipped_uids[] = $uid;
				//echo "$messageId($uid),";
				$error_message = $e->getMessage();
				if ($error_message == 'unique id not found') {
					$skipped_uids[] = $uid;
				} elseif ($error_message == 'the single id was not found in response') {
					$err_uids[] = $uid;
				}
				continue;
			}
			/*
			if ($uid == 5545) {
				echo '<pre>';
				print_r($message);
				echo '</pre>';
				die;
			}
			*/
			
			//crmv@57876	crmv@59492
			/*
			$memory_usage = memory_get_usage();
			try {
				$message_size = $message->getSize();
			} catch (Zend\Mail\Exception\RuntimeException $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$message_size = 0;
			}
			if ($message_size > $memory_usage) {
				// error status remain 2 but there isn't fatal error
				continue;
			}
			*/
			//crmv@57876e	crmv@59492e
			
			//echo "$current_user->id,$this->account,$this->folder,$uid\n";
			$data = $this->getMessageData($message,$messageId);	//crmv@59094
			if (empty($data)) {
				//crmv@50124 if error status remain 2 -- $skipped_uids[] = $uid;
				continue;
			}
			
			// crmv@64178e
			// skip the message if no messageid is present
			if (empty($data['header']['Messageid'])) {
				$err_uids[] = $uid;
				continue;
			}
			// crmv@64178e
			
			$date = $this->imap2DbDate($data['header']['Date']);	//crmv@49480
			// crmv@86123 - try other headers for the date
			if ((empty($date) || substr($date,0,10) == '1970-01-01') && !empty($data['header']['X-MDArrival-Date'])) {
				$date = $this->imap2DbDate($data['header']['X-MDArrival-Date']);
			}
			if ((empty($date) || substr($date,0,10) == '1970-01-01') && !empty($data['header']['Delivery-Date'])) {
				$date = $this->imap2DbDate($data['header']['Delivery-Date']);
			}
			// crmv@86123e

			// check if exists
			$existingCrmid = 0;
			//crmv@81338
			$query = "select messagesid, xuid from {$this->table_name} inner join {$this->entity_table} on {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index} where {$this->entity_table}.deleted = 0 and mtype = ? and messageid = ? and smownerid = ? and account = ? and folder = ? and subject = ?";
			$params = array('Webmail',$data['header']['Messageid'],$current_user->id,$this->account,$this->folder,$data['header']['Subject']);
			if (!empty($date) && substr($date,0,10) != '1970-01-01') {
				$query .= " and mdate = ?";
				$params[] = $date;
			}
			$res = $adb->pquery($query, $params);
			//crmv@81338e
			if ($res && $adb->num_rows($res) > 0) {
				$existingCrmid = $adb->query_result_no_html($res, 0, 'messagesid');
				$existingUid = $adb->query_result_no_html($res, 0, 'xuid');
				//crmv@59094
				if ($this->update_duplicates && $existingCrmid > 0) {
					// do nothing
				} elseif ($existingCrmid > 0) {	//crmv@57585
					// crmv@90388 - set as error, not skipped, otherwise they are downloaded again nex time
					//$skipped_uids[] = $uid;
					$err_uids[] = $uid;
					// crmv@90388e
					continue;
				} elseif ($existingUid >= $uid) {	// if lower/equal uid, skip - crmv@58645
					//crmv@50124
					// set status = 1 : don't check again
					$err_uids[] = $uid;
					//crmv@50124e
					continue;
				}
				//crmv@59094e
			}
			
			$body = '';
			if (isset($data['text/html'])) {
				$body = $data['text/html'];
				$body = str_replace('&lt;','&amp;lt;',$body);
				$body = str_replace('&gt;','&amp;gt;',$body);
			} elseif (isset($data['text/plain'])) {
				$body = nl2br(htmlentities($data['text/plain'], ENT_COMPAT, $default_charset));
			}
			$body = preg_replace('/[\xF0-\xF7].../s', '', $body);	//crmv@65555

			// crmv@68357
			if (!empty($data['text/calendar'])) {
				$data['icals'] = $data['text/calendar'];
			}
			// crmv@68357e
			
			if ($data['header']['Content-Class'] == 'VTECRM-DRAFT') $data['flags']['draft'] = 'on';	//crmv@84628

			$focus = CRMentity::getInstance('Messages');
			$focus->column_fields = array(
				'subject'=>$data['header']['Subject'],
				'description'=>$body,
				'mdate'=>$date,

				'mfrom'=>$data['header']['From']['email'],
				'mfrom_n'=>$data['header']['From']['name'],
				'mfrom_f'=>$data['header']['From']['full'],

				'mto'=>$data['header']['To']['email'],
				'mto_n'=>$data['header']['To']['name'],
				'mto_f'=>$data['header']['To']['full'],

				'mcc'=>$data['header']['Cc']['email'],
				'mcc_n'=>$data['header']['Cc']['name'],
				'mcc_f'=>$data['header']['Cc']['full'],

				'mbcc'=>$data['header']['Bcc']['email'],
				'mbcc_n'=>$data['header']['Bcc']['name'],
				'mbcc_f'=>$data['header']['Bcc']['full'],

				'mreplyto'=>$data['header']['ReplyTo']['email'],
				'mreplyto_n'=>$data['header']['ReplyTo']['name'],
				'mreplyto_f'=>$data['header']['ReplyTo']['full'],

				'messageid'=>$data['header']['Messageid'],
				'in_reply_to'=>$data['header']['In-Reply-To'],
				'mreferences'=>$data['header']['References'],
				'thread_index'=>$data['header']['Thread-Index'],
				'xmailer'=>$data['header']['Xmailer'],
				'xuid'=>$uid,
				'account'=>$this->account,
				'folder'=>self::$mail->getCurrentFolder(),
				'seen'=>$data['flags']['seen'],
				'answered'=>$data['flags']['answered'],
				'flagged'=>$data['flags']['flagged'],
				'forwarded'=>$data['flags']['forwarded'],
				'draft'=>$data['flags']['draft'],	//crmv@84628

				'assigned_user_id'=>$current_user->id,
				'mtype'=>'Webmail',

				'other'=>$data['other'],
				'icals'=>$data['icals'], // crmv@68357
			);

			// apply filters
			$filtered_status = $focus->applyFilters($messageId,$filtered);
			if ($filtered_status) {
				$skipped_uids[] = $uid;
				continue;
			}

			//crmv@63453
			$retrySave = false;
			try {	//crmv@44482
				if ($existingCrmid > 0) {
					$focus->id = $existingCrmid;
					$focus->mode = 'edit';
				}
				$dieOnErrorTmp = $adb->dieOnError; $adb->dieOnError = false;
				$focus->save('Messages', true);
				$adb->dieOnError = $dieOnErrorTmp;
				$crmid[] = $focus->id;
			} catch (Exception $e) {
				//ERR_SAVING_IN_DB
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				$retrySave = true;
			}
			if ($retrySave) {
				try {
					$adb = new PearDatabase();
					$adb->connect();
					$columns = $focus->column_fields;
					$focus = CRMentity::getInstance('Messages');
					$focus->column_fields = $columns;
					$focus->column_fields['description'] = substr($focus->column_fields['description'],0,50000);
					if ($existingCrmid > 0) {
						$focus->id = $existingCrmid;
						$focus->mode = 'edit';
					}
					$focus->save('Messages');
					$crmid[] = $focus->id;
				} catch (Exception $e) {
					//ERR_SAVING_IN_DB
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				}
			}
			//crmv@63453e
		}
		$this->setSavedMessages($crmid);
		$this->setSkippedUids($skipped_uids);
		$this->setErrUids($err_uids);	//crmv@50124
		$this->fetchFiltered($filtered);	// apply filters
	}

	function setSavedMessages($crmid) {
		if (!empty($this->saved_messages)) {
			$this->saved_messages = array_merge($this->saved_messages, $crmid);
		} else {
			$this->saved_messages = $crmid;
		}
	}

	function getSavedMessages() {
		return $this->saved_messages;
	}
	
	function setSkippedUids($uids) {
		if (!empty($this->skipped_uids)) {
			$this->skipped_uids = array_merge($this->skipped_uids, $uids);
		} else {
			$this->skipped_uids = $uids;
		}
	}

	function getSkippedUids() {
		return $this->skipped_uids;
	}
	
	//crmv@50124
	function setErrUids($uids) {
		if (!empty($this->err_uids)) {
			$this->err_uids = array_merge($this->err_uids, $uids);
		} else {
			$this->err_uids = $uids;
		}
	}

	function getErrUids() {
		return $this->err_uids;
	}
	//crmv@50124e

	function saveModComment($crmid,$messageid) {
		global $current_user;
		$focus = CRMEntity::getInstance('Messages');
		$focus->retrieve_entity_info_no_html($crmid,'Messages');
		if ($messageid == $focus->column_fields['messageid']) {
			if (isPermitted('ModComments', 'EditView', '') == 'yes') {
				$modObj = CRMEntity::getInstance('ModComments');
				$modObj->column_fields['commentcontent'] = vtlib_purify(strip_tags($_REQUEST['comment']));
				$modObj->column_fields['related_to'] = $crmid;
				$modObj->column_fields['visibility_comm'] = vtlib_purify($_REQUEST['ModCommentsMethod']);
				$modObj->column_fields['users_comm'] = vtlib_purify($_REQUEST['users_comm']);
				$modObj->column_fields['assigned_user_id'] = $current_user->id;
				$modObj->save('ModComments');
			}
		}
	}

	function saveCacheLink($column_fields) {
		global $current_user;

		require_once('modules/Emails/class.phpmailer.php');
		$mailer = new PHPMailer();
		$uniq_id = md5(uniqid(time()));
		$messageid = sprintf('<%s@%s>', $uniq_id, $mailer->ServerHostname()); // in this way, we won't duplicate messages

		$xuid = '';
		$mfrom = (!empty($column_fields['mfrom']) ? $column_fields['mfrom'] : '');
		$mto = (!empty($column_fields['mto']) ? $column_fields['mto'] : '');
		$mcc = (!empty($column_fields['mcc']) ? $column_fields['mcc'] : '');
		$mbcc = (!empty($column_fields['mbcc']) ? $column_fields['mbcc'] : '');
		$mreplyto = (!empty($column_fields['mreplyto']) ? $column_fields['mreplyto'] : '');

		if ($column_fields['mtype'] != 'Link'){
			$account = '';
			if ($column_fields['account'] !== '') {
				$account = $column_fields['account'];
			} elseif (!empty($mfrom)) {
				$focusEmails = CRMentity::getInstance('Emails');
				$account = $focusEmails->getFromEmailAccount($mfrom);
			}
			if ($account === '') {
				$main_account = $this->getMainUserAccount();
				$account = $main_account['id'];
			}
			$this->setAccount($account);
			$specialFolders = $this->getSpecialFolders($column_fields);
		}
		else{
			$specialFolders = Array('INBOX'=>'','Sent'=>'','Drafts'=>'','Trash'=>'');
			$account = $column_fields['account'];	//crmv@86304	crmv@80216
		}

		$focus = CRMentity::getInstance('Messages');
		// crmv@66378
		$focus->column_fields = array_merge($column_fields, array(
			'subject'=>(!empty($column_fields['subject']) ? $column_fields['subject'] : ''),
			'description'=>(!empty($column_fields['description']) ? $column_fields['description'] : ''),
			'mdate'=>(!empty($column_fields['mdate']) ? $column_fields['mdate'] : date('Y-m-d H:i:s')),
			'mfrom'=>$mfrom,
			'mfrom_n'=>(!empty($column_fields['mfrom_n']) ? $column_fields['mfrom_n'] : ''),
			'mfrom_f'=>(!empty($column_fields['mfrom_f']) ? $column_fields['mfrom_f'] : $mfrom),
			'mto'=>$mto,
			'mto_n'=>(!empty($column_fields['mto_n']) ? $column_fields['mto_n'] : ''),
			'mto_f'=>(!empty($column_fields['mto_f']) ? $column_fields['mto_f'] : $mto),
			'mcc'=>$mcc,
			'mcc_n'=>(!empty($column_fields['mcc_n']) ? $column_fields['mcc_n'] : ''),
			'mcc_f'=>(!empty($column_fields['mcc_f']) ? $column_fields['mcc_f'] : $mcc),
			'mbcc'=>$mbcc,
			'mbcc_n'=>(!empty($column_fields['mbcc_n']) ? $column_fields['mbcc_n'] : ''),
			'mbcc_f'=>(!empty($column_fields['mbcc_f']) ? $column_fields['mbcc_f'] : $mbcc),
			'mreplyto'=>$mreplyto,
			'mreplyto_n'=>(!empty($column_fields['mreplyto_n']) ? $column_fields['mreplyto_n'] : ''),
			'mreplyto_f'=>$mreplyto,
			'in_reply_to'=>(!empty($column_fields['in_reply_to']) ? $column_fields['in_reply_to'] : ''),
			'mreferences'=>(!empty($column_fields['mreferences']) ? $column_fields['mreferences'] : ''),
			'thread_index'=>(!empty($column_fields['thread_index']) ? $column_fields['thread_index'] : ''),
			'xmailer'=>(!empty($column_fields['xmailer']) ? $column_fields['xmailer'] : 'VTECRM-WEBMAIL'),
			'xuid'=>(!empty($column_fields['xuid']) ? $column_fields['xuid'] : $xuid),
			'messageid'=>(!empty($column_fields['messageid']) ? $column_fields['messageid'] : $messageid),
			'seen'=>(!empty($column_fields['seen']) ? $column_fields['seen'] : '1'),
			'answered'=>(!empty($column_fields['answered']) ? $column_fields['answered'] : '0'),
			'flagged'=>(!empty($column_fields['flagged']) ? $column_fields['flagged'] : '0'),
			'forwarded'=>(!empty($column_fields['forwarded']) ? $column_fields['forwarded'] : '0'),
			'folder'=>(!empty($column_fields['folder']) ? $column_fields['folder'] : $specialFolders['Sent']),
			'assigned_user_id'=>(!empty($column_fields['assigned_user_id']) ? $column_fields['assigned_user_id'] : $current_user->id),
			'mtype'=>(!empty($column_fields['mtype']) ? $column_fields['mtype'] : 'Link'),
			'mvisibility'=>(!empty($column_fields['mvisibility']) ? $column_fields['mvisibility'] : ''),
			'send_mode'=>(!empty($column_fields['send_mode']) ? $column_fields['send_mode'] : ''),
			'other'=>(!empty($column_fields['other']) ? $column_fields['other'] : ''),
			'parent_id'=>(!empty($column_fields['parent_id']) ? $column_fields['parent_id'] : ''),
			'recipients'=>(!empty($column_fields['recipients']) ? $column_fields['recipients'] : ''),
			'account'=>$account,
		));
		// crmv@66378e
		$focus->save('Messages');
		return $focus->id;
	}

	function deleteCache($ids) {
		if (empty($ids)) return;
		foreach ($ids as $crmid => $uid) {
			if ($this->haveRelations($crmid,'','-')) {
				global $adb, $table_prefix;
				$adb->pquery("update {$this->table_name} set mtype = ? where {$this->table_index} = ?",array('Link',$crmid));
				$this->saveAllDocuments($id);	//crmv@63475
				$this->beforeTrashFunctions($crmid);
			} else {
				parent::trash('Messages', $crmid);
			}
		}
		$this->cleanDraftCache(array_keys($ids));
	}

	function trash($module, $id) {	// move to Trash
		if (empty($this->column_fields['folder']) || empty($this->column_fields['xuid']) || empty($this->column_fields['account'])) {
			$this->retrieve_entity_info($id,$module);
		}
		if ($this->column_fields['mtype'] == 'Webmail') {
			$this->cleanDraftCache($id);
			$specialFolders = $this->getSpecialFolders();
			if ($this->column_fields['folder'] == $specialFolders['Trash'] && $this->haveRelations($id,'','-')) {
				global $adb, $table_prefix;
				$adb->pquery("update {$this->table_name} set mtype = ? where {$this->table_index} = ?",array('Link',$id));
				$this->saveAllDocuments($id);	//crmv@63475
				$this->beforeTrashFunctions($id);
				
				$this->addToPropagationCron('trash', array(
					'userid'=>$this->column_fields['assigned_user_id'],
					'account'=>$this->column_fields['account'],
					'folder'=>$this->column_fields['folder'],
					'uid'=>$this->column_fields['xuid'],
					'fetch'=>false
				));
			} else {
				parent::trash($module, $id);
				$this->addToPropagationCron('trash', array(
					'userid'=>$this->column_fields['assigned_user_id'],
					'account'=>$this->column_fields['account'],
					'folder'=>$this->column_fields['folder'],
					'uid'=>$this->column_fields['xuid'],
					'fetch'=>true
				));
			}
		} else {
			parent::trash($module, $id);
		}
	}

	function propagateTrash($userid,$account,$folder,$uid,$fetch=false) {
		$focus = CRMEntity::getInstance('Messages');

		$focus->setAccount($account);
		$focus->getZendMailStorageImap($userid);
		$focus->selectFolder($folder);
		$messageId = self::$mail->getNumberByUniqueId($uid);
		
		$specialFolders = $focus->getSpecialFolders();
		if ($folder == $specialFolders['Trash'] || $folder == $specialFolders['Drafts']) {	//crmv@49923
			self::$mail->removeMessage($messageId);
		} else {
			self::$mail->moveMessage($messageId,$specialFolders['Trash']);
		}
		
		//fetch new messages from Trash folder
		if ($fetch) {
			global $current_user;
			$tmp = $current_user->id;
			$current_user->id = $userid;
			
			$focus->fetchNews($specialFolders['Trash']);
			
			$current_user->id = $tmp;
		}
	}

	function cleanDraftCache($ids) {
		global $adb, $table_prefix, $current_user;
		if (!is_array($ids)) {
			$ids = array($ids);
		}
		$draftids = array();
		$result = $adb->pquery("SELECT messagehash FROM {$table_prefix}_messages
								WHERE {$table_prefix}_messages.messagesid IN (".generateQuestionMarks($ids).")",$ids);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$draftids[] = $row['messagehash'];
			}
			$adb->pquery("DELETE FROM {$table_prefix}_messages_drafts WHERE userid = ? AND messagehash IN (".generateQuestionMarks($draftids).")",array($current_user->id,$draftids));
		}
	}

	function markAsViewed($userid,$skip_update_flag='no') {
		parent::markAsViewed($userid);
		if ($skip_update_flag != 'yes') {
			$this->setFlag('seen',1);
		}
	}

	//crmv@44179
	function massSetFlag($flag,$value,$ids) {
		global $adb, $current_user, $current_account, $current_folder;
		if (!empty($ids) && is_array($ids)) {
			foreach ($ids as $id) {
				$this->addToPropagationCron('flag', array('id'=>$id,'flag'=>$flag,'value'=>$value));
			}
			if (!empty($ids)) {
				$adb->pquery("update {$this->table_name} set {$flag} = ? where messagesid in (".implode(',',$ids).") and {$flag} <> ?",array($value,$value));
			}
			if ($flag == 'seen') {
				$this->reloadCacheFolderCount($current_user->id,$current_account,$current_folder);
			} elseif ($flag == 'flagged') {
				$this->reloadCacheFolderCount($current_user->id,$current_account,'Flagged');
			}
		}
	}

	function setFlag($flag,$value) {
		global $adb, $table_prefix, $current_user;  // crmv@49398
		$status = false;
		if ($this->column_fields[$flag] != $value																					// if flag change
			&& !empty($this->column_fields['assigned_user_id']) && $current_user->id == $this->column_fields['assigned_user_id']) {	// if message is assigned to me (not in folder Shared)
			$this->addToPropagationCron('flag', array('id'=>$this->id,'flag'=>$flag,'value'=>$value));
			$adb->pquery("update {$this->table_name} set {$flag} = ? where messagesid = ?",array($value,$this->id));
			$adb->pquery("update {$table_prefix}_crmentity set modifiedtime = ? where crmid = ?", array($adb->formatDate(date('Y-m-d H:i:s'), true), $this->id)); // crmv@49398 crmv@69690
			$this->column_fields[$flag] = $value;
			if ($flag == 'seen') {
				$this->reloadCacheFolderCount($this->column_fields['assigned_user_id'],$this->column_fields['account'],$this->column_fields['folder']);				
			} elseif ($flag == 'flagged') {
				$this->reloadCacheFolderCount($this->column_fields['assigned_user_id'],$this->column_fields['account'],'Flagged');				
			}
			$status = true;
		}
		return $status;
	}
	//crmv@44179e

	function propagateSetFlag($messagesid,$flag,$value) {
		global $adb, $table_prefix;
		
		$focus = CRMEntity::getInstance('Messages');
		$error = $focus->retrieve_entity_info($messagesid,'Messages',false);
		if (!empty($error)) {
			throw new Exception($error);
		}
		$focus->id = $messagesid;
		
		$focus->resetMailResource();
		$focus->getZendMailStorageImap($focus->column_fields['assigned_user_id']);
		$focus->selectFolder($focus->column_fields['folder']);
		$messageId = self::$mail->getNumberByUniqueId($focus->column_fields['xuid']);

		//Get current flags with server call
		$message = self::$mail->getMessage($messageId);
		$current_flags = $flags = $message->getFlags();
		//Get current flags using cached flags
		//$current_flags = $flags = $focus->getCacheFlags();	//need $focus->id

		switch ($flag) {
			case 'seen':
				if ($value == '1') {
					$flags = array_merge($flags,array(Zend\Mail\Storage::FLAG_SEEN));
				} elseif ($value == '0') {
					unset($flags[array_search(Zend\Mail\Storage::FLAG_SEEN, $flags)]);	//crmv@49432
				}
				break;
			case 'flagged':
				if ($value == '1') {
					$flags = array_merge($flags,array(Zend\Mail\Storage::FLAG_FLAGGED));
				} elseif ($value == '0') {
					unset($flags[array_search(Zend\Mail\Storage::FLAG_FLAGGED, $flags)]);	//crmv@49432
				}
				break;
			case 'answered':
				if ($value == '1') {
					$flags = array_merge($flags,array(Zend\Mail\Storage::FLAG_ANSWERED));
				}
				break;
			case 'forwarded':
				if ($value == '1') {
					$flags = array_merge($flags,array('$Forwarded','Forwarded'));
				}
				break;
		}
		if ($current_flags != $flags) {
			try {
				self::$mail->setFlags($messageId, $flags);
				return true;
			} catch (Zend\Mail\Storage\Exception\RuntimeException $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				//crmv@49432
				if (empty($flags))
					return self::$protocol->store($current_flags, $messageId, null, '-');
				else
				//crmv@49432e
					throw new Exception($e->getMessage());
			}
		}
	}

	function getPreviewBody($rawValue) {
		global $default_charset, $listview_max_textlength, $current_user;
		$listview_max_textlength_tmp = $listview_max_textlength;
		$listview_max_textlength = 120;

		$temp_val = preg_replace("/(<\/?)(\w+)([^>]*>)/i","",$rawValue);
		$temp_val = str_replace('&nbsp;',' ',$temp_val);
		$temp_val = html_entity_decode($temp_val, ENT_QUOTES, $default_charset);

		$search = array(
			'@<title[^>]*?>.*?</title>@si',		// Strip out title tag
			'@<script[^>]*?>.*?</script>@si',	// Strip out javascript
			'@<style[^>]*?>.*?</style>@siU',	// Strip style tags properly
		);
		$temp_val = preg_replace($search, "\n", $temp_val);

		$temp_val = preg_replace('/\s+/',' ',strip_tags($temp_val));
		$value = textlength_check($temp_val);
		$listview_max_textlength = $listview_max_textlength_tmp;

		return $value;
	}

	function cleanEmail($email) {
		if (strpos($email,'<') !== false) {
			$email = substr($email,strpos($email,'<')+1);
		}
		if (strpos($email,'>') !== false) {
			$email = substr($email,0,strpos($email,'>'));
		}
		return trim($email);
	}

	// crmv@107655
	function getBusinessCard($type) {
		if ($type == 'TO') {
			$email = $this->column_fields['mto'];
			$full = $this->column_fields['mto_f'];
		} elseif ($type == 'FROM') {
			$email = $this->column_fields['mfrom'];
			$full = $this->column_fields['mfrom_f'];
		} elseif ($type == 'CC') {
			$email = $this->column_fields['mcc'];
			$full = $this->column_fields['mcc_f'];
		}
		if (substr_count($full,',') > substr_count($email,',')) {
			$email = trim($full);
		}
		if (strpos($email,',') !== false) {
			$emails = explode(',',$email);
		} else {
			$emails = array($email);
		}
		$entitiesInfo = array();
		if (!empty($emails)) {
			foreach($emails as $email) {
				$email = $this->cleanEmail($email);
				$entityid = '';
				$entity = $this->getEntitiesFromEmail($email,false,false,array('Users','Contacts','Accounts','Leads','Vendors'),true);
				if (!empty($entity)) {
					$entityid = $entity['crmid'];
					$entitytype = $entity['module'];
				}
				$entityInfo = array();
				if (!empty($entityid)) {
					$retrieveFields = array();
					if ($entitytype == 'Contacts') {
						$retrieveFields = array('account_id', 'salutationtype', 'mobile', 'phone', 'homephone', 'otherphone');
					} elseif ($entitytype == 'Accounts') {
						$retrieveFields = array('bill_city', 'phone', 'otherphone');
					} elseif ($entitytype == 'Leads') {
						$retrieveFields = array('company', 'leadsource', 'mobile', 'phone');
					} elseif ($entitytype == 'Vendors') {
						$retrieveFields = array('website', 'phone');
					} elseif ($entitytype == 'Users') {
						$retrieveFields = array('title', 'department', 'phone_mobile', 'phone_work', 'phone_home', 'phone_other');
					}
					if (count($retrieveFields) > 0) {
						$skypeFields = $this->getUitypeFields($entitytype, 85);	// skype uitype
						if (!empty($skypeFields)) {
							foreach ($skypeFields as $sfield) $retrieveFields[] = $sfield['name'];
							$retrieveFields = array_unique($retrieveFields);
						}
					}
					
					$entityFocus = CRMEntity::getInstance($entitytype);
					$entityFocus->id = $entityid;
					$error = $entityFocus->retrieve_entity_info($entityid,$entitytype,false, $retrieveFields);
					if ($entitytype == 'Users') $error = '';
					if (!empty($error)) continue;
					$entityName = $entityFocus->getRecordName();	//crmv@104310
					if ($entitytype == 'Users') {
						$entityInfo = array(
							'module'=>$entitytype,
							'id'=>$entityid,
							'name'=>getUserFullName($entityid),
							'title'=>implode(' - ',array_filter(array($entityFocus->column_fields['title'],$entityFocus->column_fields['department']))),
						);
						$phone = array(
							'phone_mobile'=>array('label'=>getTranslatedString('Mobile',$entitytype),'value'=>$entityFocus->column_fields['phone_mobile']),
							'phone_work'=>array('label'=>getTranslatedString('Office Phone',$entitytype),'value'=>$entityFocus->column_fields['phone_work']),
							'phone_home'=>array('label'=>getTranslatedString('Home Phone',$entitytype),'value'=>$entityFocus->column_fields['phone_home']),
							'phone_other'=>array('label'=>getTranslatedString('Other Phone',$entitytype),'value'=>$entityFocus->column_fields['phone_other']),
						);
					} elseif ($entitytype == 'Contacts') {
						$accName = '';
						if (!empty($entityFocus->column_fields['account_id'])) {
							$accEntityName = getEntityName('Accounts',$entityFocus->column_fields['account_id']);
							if (!empty($accEntityName)) {
								$accName = array_values($accEntityName);
								$accName = $accName[0];
							}
						}
						if ($entityFocus->column_fields['salutationtype'] != '--None--') {
							$salutationtype = getTranslatedString($entityFocus->column_fields['salutationtype'],$entitytype);
						}
						$entityInfo = array(
							'module'=>$entitytype,
							'id'=>$entityid,
							'name'=>implode(' ',array_filter(array($salutationtype,$entityName))),
							'accountid'=>$entityFocus->column_fields['account_id'],
							'accountname'=>$accName,
						);
						$phone = array(
							'mobile'=>array('label'=>getTranslatedString('Mobile',$entitytype),'value'=>$entityFocus->column_fields['mobile']),
							'phone'=>array('label'=>getTranslatedString('Office Phone',$entitytype),'value'=>$entityFocus->column_fields['phone']),
							'homephone'=>array('label'=>getTranslatedString('Home Phone',$entitytype),'value'=>$entityFocus->column_fields['homephone']),
							'otherphone'=>array('label'=>getTranslatedString('Other Phone',$entitytype),'value'=>$entityFocus->column_fields['otherphone']),
						);
					} elseif ($entitytype == 'Accounts') {
						$entityInfo = array(
							'module'=>$entitytype,
							'id'=>$entityid,
							'name'=>$entityName,
							'bill_city'=>$entityFocus->column_fields['bill_city'],
						);
						$phone = array(
							'phone'=>array('label'=>getTranslatedString('Phone',$entitytype),'value'=>$entityFocus->column_fields['phone']),
							'otherphone'=>array('label'=>getTranslatedString('Other Phone',$entitytype),'value'=>$entityFocus->column_fields['otherphone']),
						);
					} elseif ($entitytype == 'Leads') {
						$entityInfo = array(
							'module'=>$entitytype,
							'id'=>$entityid,
							'name'=>$entityName,
							'company'=>$entityFocus->column_fields['company'],
							'leadsource'=>$entityFocus->column_fields['leadsource'],
						);
						$phone = array(
							'mobile'=>array('label'=>getTranslatedString('Mobile',$entitytype),'value'=>$entityFocus->column_fields['mobile']),
							'phone'=>array('label'=>getTranslatedString('Phone',$entitytype),'value'=>$entityFocus->column_fields['phone']),
						);
					} elseif ($entitytype == 'Vendors') {
						$entityInfo = array(
							'module'=>$entitytype,
							'id'=>$entityid,
							'name'=>$entityName,
							'website'=>$entityFocus->column_fields['website'],
						);
						$phone = array(
							'phone'=>array('label'=>getTranslatedString('Phone',$entitytype),'value'=>$entityFocus->column_fields['phone']),
						);
					}
					// check for skype fields
					if ($entityInfo && !empty($skypeFields)) {
						if (!is_array($phone)) $phone = array();
						foreach ($skypeFields as $sfield) {
							$value = trim($entityFocus->column_fields[$sfield['name']]);
							if (!empty($value)) {
								$phone['skype'] = array('label'=>$sfield['label'],'value'=>$value);
								// take the first valid one
								break;
							}
						}
					}
					if (!empty($phone)) {
						foreach ($phone as $k => $v) {
							if (empty($v['value'])) {
								unset($phone[$k]);
							}
						}
					}
					$entityInfo['phone'] = $phone;
					$entityInfo['module_permitted'] = (isPermitted($entitytype, 'DetailView', $entityid) == 'yes');
				}
				$entityInfo['email'] = $email;
				$entitiesInfo[] = $entityInfo;
			}
		}
		return $entitiesInfo;
	}
	
	/**
	 * Return the field names of all the field of the specified uitypes
	 */
	protected function getUitypeFields($module, $uitypes) {
		global $current_user;
		
		$fields = array();
		if (!is_array($uitypes)) $uitypes = array($uitypes);
		
		$RC = RCache::getInstance();
		$allFields = $RC->get('ws_fields_mod_'.$module);
		
		if ($allFields == null) {
			require_once('include/Webservices/DescribeObject.php');
			try {
				$modinfo = vtws_describe($module, $current_user);
				$allFields = $modinfo['fields'];
			} catch (Exception $e) {
				// ignore errors and skip the fields
				$allFields = array();
			}
			$RC->set('ws_fields_mod_'.$module, $allFields);
		}
		
		if ($allFields && is_array($allFields)) {
			foreach ($allFields as $field) {
				if (in_array($field['uitype'], $uitypes)) {
					$fields[] = $field;
				}
			}
		}
		
		return $fields;
	}
	// crmv@107655e

	function getLuckyMessage($account,$folder,$record='') {
		global $adb, $table_prefix, $current_user;
		$id = '';
		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		$query = "SELECT {$this->table_name}.messagesid FROM {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.messagesid
				WHERE deleted = 0 $setypeCond AND smownerid = ? AND seen = ?";
		// crmv@64325e
		$params = array($current_user->id,1);
		if ($account == 'all') {
			$folders = $this->getAllSpecialFolders($folder);
			$tmp = array();
			foreach($folders as $account => $folder) {
				$tmp[] = "({$this->table_name}.account = ? AND {$this->table_name}.folder = ?)";
				$params[] = array($account,$folder['INBOX']);
			}
			$query .= ' AND ('.implode(' OR ',$tmp).')';
		} else {
			$query .= " AND account = ? AND folder = ?";
			$params[] = $account;
			$params[] = $folder;
		}
		if (!empty($record)) {
			if (!is_array($record)) {
				$record = array($record);
			}
			$result = $adb->pquery("SELECT messagesid, mdate FROM {$table_prefix}_messages WHERE messagesid IN (".generateQuestionMarks($record).")",$record);
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					if (!empty($row['mdate'])) {
						$query .= "	AND (messagesid <> ? AND mdate <= ?)";
						$params[] = $row['messagesid'];
						$params[] = $row['mdate'];
					}
				}
			}
		}
		$query .= "	ORDER BY mdate DESC";
		$result = $adb->limitPquery($query,0,1,$params);
		if ($result && $adb->num_rows($result) > 0) {
			$id = $adb->query_result($result,0,'messagesid');
		}
		return $id;
	}

	// crmv@48677
	function haveAttachments($id, $excludeDisposition = array()) {	//crmv@65648 
		global $adb,$table_prefix;

		$params = array($id,$this->other_contenttypes_attachment);

		if (!is_array($excludeDisposition)) $excludeDisposition = array_filter(array($excludeDisposition));
		if (count($excludeDisposition) > 0) {
			$dispQuery = " AND contentdisposition not in (".generateQuestionMarks($excludeDisposition).")";
			$params[] = $excludeDisposition;
		} else {
			$dispQuery = '';
		}

		$query = "select messagesid
			from {$this->table_name}_attach
			where {$this->table_index} = ?
			and (
				contenttype IN (".generateQuestionMarks($this->other_contenttypes_attachment).")
				OR (contentdisposition IS NOT NULL $dispQuery)
			)";
		$result = $adb->pquery($query,$params);

		if ($result && $adb->num_rows($result) > 0) {
			return true;
		}
		return false;
	}

	function getAttachments($excludeDisposition = array()) {	//crmv@65648 
		global $adb,$table_prefix;

		$attachments = array();

		$params = array($this->id,$this->other_contenttypes_attachment);

		if (!is_array($excludeDisposition)) $excludeDisposition = array_filter(array($excludeDisposition));
		if (count($excludeDisposition) > 0) {
			$dispQuery = " AND contentdisposition not in (".generateQuestionMarks($excludeDisposition).")";
			$params[] = $excludeDisposition;
		} else {
			$dispQuery = '';
		}

		// crmv@68357 - if there is an embedded invitation/reply in ical format, don't show it as attachment unless it has a filename
		$icalExcludeSql = " AND NOT (contentmethod IN (".generateQuestionMarks($this->ical_methods).") AND contenttype = 'text/calendar' AND (contentname IS NULL OR contentname = 'Unknown'))";
		$params[] = $this->ical_methods;
		$query = "select *
			from {$this->table_name}_attach
			where {$this->table_index} = ?
			and (
				contenttype IN (".generateQuestionMarks($this->other_contenttypes_attachment).")
				OR (contentdisposition IS NOT NULL $dispQuery)
			) $icalExcludeSql";
		// crmv@68357e
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				// crmv@88997
				if (empty($row['document'])) {	// search if the same attach is already saved
					$query1 = "SELECT {$this->table_name}_attach.* FROM {$this->table_name}_attach
						INNER JOIN {$this->table_name} ON {$this->table_name}_attach.{$this->table_index} = {$this->table_name}.{$this->table_index}
						INNER JOIN {$table_prefix}_crmentity ON setype = ? AND document = {$table_prefix}_crmentity.crmid
						WHERE {$table_prefix}_crmentity.deleted = 0 AND messagehash = ? AND {$this->table_name}.{$this->table_index} <> ? AND document IS NOT NULL AND document > 0
						AND contentid = ?";
					$params1 = array('Documents', $this->column_fields['messagehash'], $this->id, $row['contentid']);
					if ($row['content_id'] == null) {
						$query1 .= ' AND content_id IS NULL';
					} else {
						$query1 .= ' AND content_id = ?';
						$params1[] = $row['content_id'];
					}
					if ($row['contentname'] == null) {
						$query1 .= ' AND contentname IS NULL';
					} else {
						$query1 .= ' AND contentname = ?';
						$params1[] = $row['contentname'];
					}
					$result1 = $adb->pquery($query1, $params1);
					if ($result1 && $adb->num_rows($result1) > 0) {
						$row['document'] = $adb->query_result($result1,0,'document');
						$adb->pquery("update {$this->table_name}_attach set document = ? where {$this->table_index} = ? and contentid = ?", array($row['document'], $this->id, $row['contentid']));
					}
				}
				// crmv@88997e

				$target = '';
				if (stripos($row['contenttype'],'image') !== false || $row['contenttype'] == 'text/rfc822-headers') {	//crmv@53651
					$target = '_blank';
				}
				$document = '';
				if (!empty($row['document'])) {
					$result1 = $adb->pquery("SELECT crmid FROM {$this->entity_table} WHERE crmid = ? AND deleted = 0",array($row['document']));
					if ($result1 && $adb->num_rows($result1) > 0) {
						$document = $row['document'];
					}
				}
				if ($row['contentid'] < 0) {
					$attachmentid = $adb->query_result($adb->pquery("select * from ".$table_prefix."_seattachmentsrel where crmid = ?", array($document)),0,'attachmentsid');
					$link = "index.php?module=uploads&action=downloadfile&fileid=$attachmentid&entityid=$document";
				} else {
					$link = "index.php?module=Messages&action=MessagesAjax&file=Download&record={$this->id}&contentid={$row['contentid']}";
				}
				$action_download = true;
				$action_save = true;
				$action_link = true;
				//crmv@62340	crmv@62414
				$action_view = false;
				$action_view_JSfunction = false;
				$action_label = false;
				$extension = substr(strrchr($row['contentname'], "."), 1);
				if(in_array(strtolower($extension),$this->viewerJS_supported_extensions)){
					$action_view = true;
					$action_view_JSfunction=$this->action_view_JSfunction_array[strtolower($extension)];
					$action_label = 'LBL_VIEW_DOCUMENT';
				}
				if(in_array(strtolower($extension),$this->view_image_supported_extensions)){
					$action_view = true;
					$action_view_JSfunction=$this->action_view_JSfunction_array[strtolower($extension)];
					$action_label = 'LBL_VIEW_DOCUMENT';
				}
				// crmv@107356
				if(strtolower($extension) == 'eml' || $row['contenttype'] == 'message/rfc822'){
					$action_view = true;
					$action_view_JSfunction=$this->action_view_JSfunction_array[strtolower($extension) ?: 'eml'];
					$action_label = 'LBL_VIEW_AS_EMAIL';
				}
				// crmv@107356e
				if($this->isEML()){
					$action_save = false;
					$action_link = false;
				}
				$attachments[] = array(
					'action_download'=>$action_download,
					'action_save'=>$action_save,
					'action_link'=>$action_link,
					'action_view'=>$action_view,
					'action_view_JSfunction'=>$action_view_JSfunction,
					'action_view_label'=>$action_label,
					'contentid'=>$row['contentid'],
					'name'=>$row['contentname'],
					'link'=>$link,
					'img'=>'modules/Messages/src/img/attach.gif',
					'target'=>$target,
					'document'=>$document,
				);
				//crmv@62340e	crmv@62414e
			}
		}
		return $attachments;
	}
	// crmv@48677e

	function getAttachmentsInfo() {
		global $adb,$table_prefix;
		$attachments = array();
		$result = $adb->pquery("select * from {$this->table_name}_attach where {$this->table_index} = ?",array($this->id));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$attachments[$row['contentid']]['parameters'] = array(
					'content_id'=>$row['content_id'],
					'name'=>$row['contentname'],
					'contenttype'=>$row['contenttype'],
					'contentdisposition'=>$row['contentdisposition'],
					'charset'=>$row['contentcharset'],
					'encoding'=>$row['contentencoding'],
					'method'=>$row['contentmethod'], // crmv@68357
					'size'=>$row['size'],	//crmv@65328
				);
			}
		}
		return $attachments;
	}

	// crmv@68357 crmv@81126
	function processIcalReply($uuid, $recurrIdx, $content) {
		global $adb, $table_prefix;
		
		$recurrIdx = intval($recurrIdx) ?: 0;
		$calendar = CRMEntity::getInstance('Calendar');
		
		// check for an existing event
		$activityid = $calendar->getCrmidFromUuid($uuid, $recurrIdx);
		if ($activityid > 0 && isPermitted('Calendar', 'DetailView', $activityid) == 'yes') {
			// first link with that event
			$this->save_related_module('Messages', $this->id, 'Calendar', $activityid);
			
			// then parse the event to get my address
			$vcalendar = new VTEvcalendar();
			$r = $vcalendar->parse($content);
			if ($r === false) return false;
			
			// get the event
			$event = $vcalendar->getComponent('vevent');
			if (empty($event)) $event = $vcalendar->getComponent('vtodo');
			
			$att = $event->getProperty('ATTENDEE', false, true);
			$attMail = preg_replace('/^MAILTO:/i', '', $att['value']);
			$part = $att['params']['PARTSTAT'];
			$partNo = 0;
			if ($part == 'DECLINED') {
				$partNo = 1;
			} elseif ($part == 'ACCEPTED') {
				$partNo = 2;
			}
			if (!empty($attMail) && $partNo > 0) {
				// now get invitees 
				$updateList = array();
				$invitees = $calendar->getInvitees($activityid);
				foreach ($invitees as $inv) {
					if ($inv['type'] == 'Contacts' && (strcasecmp($inv['email1'], $attMail) == 0 || strcasecmp($inv['email2'], $attMail) == 0)) {
						// ok, this is the invitee
						$updateList[$inv['id']] = $partNo;
					}
				}
				// and update the partecipations
				if (count($updateList) > 0) {
					foreach ($updateList as $inviteeid => $partecipation) {
						// now, this is ugly!!
						$from = 'invite_con';
						$_REQUEST['partecipation'] = $partecipation;
						$_REQUEST['activityid'] = $activityid;
						$_REQUEST['userid'] = $inviteeid;
						include('modules/Calendar/SavePartecipation.php');
					}
				}
			}
		}
		return true;
	}
	
	function processIcalRequest($uuid, $recurrIdx, $content) {
		global $current_user;
		
		$recurrIdx = intval($recurrIdx) ?: 0;
		$calendar = CRMEntity::getInstance('Calendar');
		
		// check for an existing event and link it to the email if I'm one of the invitees
		$activityid = $calendar->getCrmidFromUuid($uuid, $recurrIdx);
		if ($activityid > 0 && isPermitted('Calendar', 'DetailView', $activityid) == 'yes') {
			if ($this->id && $calendar->isUserInvited($activityid, $current_user->id)) {
				// ok, I'm invited, link the event to the message
				$this->save_related_module('Messages', $this->id, 'Calendar', $activityid);
			}
		}
	}
	// crmv@81126e
	
	function sendIcalReply($icalid, $answer = 'yes') {
		global $current_user;
		$ical = $this->getIcals($icalid);
		$ical = $ical[0];
		if (empty($ical)) return false;

		$myself = $ical['myemail'];
		// parse the event
		$vcalendar = new VTEvcalendar();
		$r = $vcalendar->parse($ical['content']);
		if ($r === false) return false;
		
		// get the timezone
		$tzone = $vcalendar->getComponent("vtimezone");
		
		// prepare the new calendar
		$newcal = new VTEvcalendar();
		if ($tzone) $newcal->addComponent($tzone);
		if ($vcalendar->version) $newcal->setVersion($vcalendar->version);
		if ($vcalendar->prodid) $newcal->prodid = $vcalendar->prodid;
		if ($vcalendar->calscale) $newcal->setCalscale($vcalendar->calscale);
		$newcal->setMethod('REPLY');
		
		// get the original event
		$event = $vcalendar->getComponent('vevent');
		if (empty($event)) $event = $vcalendar->getComponent('vtodo');
		
		// search for myself
		$myselfInvitee = null;
		while ($att = $event->getProperty('ATTENDEE', false, true)) {
			$amail = preg_replace('/^mailto:/i', '', $att['value']);
			if (strcasecmp($amail, $myself) == 0) {
				// myself
				$myselfInvitee = $att;
				break;
			}
		}
		if (!$myselfInvitee) return false;
		
		// remove all attendees
		while ($event->deleteProperty('ATTENDEE')) ;
		
		// add myself with participation
		$myselfInvitee['params']['PARTSTAT'] = ($answer == 'yes' ? 'ACCEPTED' : 'DECLINED');
		unset($myselfInvitee['params']['RSVP']);
		
		$event->setAttendee($myselfInvitee['value'], $myselfInvitee['params']);
		
		// get some params
		$organizer = $event->getProperty('ORGANIZER', false, true);
		$subject = $event->getProperty('SUMMARY');
		
		// add it to the new calendar
		$newcal->addComponent($event);
		$out = $newcal->createCalendar();
		if (empty($out)) return false;
		$out = trim($out);
		
		// now prepare the email and send it!
		$attachment = array(
			array(
				'sourcetype' => 'string',
				'content' => $out,
				'contenttype' => 'text/calendar',
				'altbody' => true,
				'charset' => 'UTF-8',
				'encoding' => '7bit',
				'method' => 'REPLY',
			),
			array(
				'sourcetype' => 'string',
				'filename' => 'invite.ics',
				'content' => $out,
				'contenttype' => 'application/ics',
			),
		);
		
		// find the sender (organizator, otherwise the sender of the email)
		$to_email = preg_replace('/^mailto:/i', '', $organizer['value']) ?: $ical['sender'];

		$myname = $myselfInvitee['params']['CN'] ?: getUserFullName($current_user->id);
		if ($answer == 'yes') {
			$description = "$myname ({$myself}) ".getTranslatedString('LBL_INVITATION_ACCEPTED', 'Calendar');
			$email_subject = getTranslatedString('LBL_INVITATION_ACCEPTED_SUBJECT', 'Calendar').": $subject";
		} else {
			$description = "$myname ({$myself}) ".getTranslatedString('LBL_INVITATION_DECLINED', 'Calendar');
			$email_subject = getTranslatedString('LBL_INVITATION_DECLINED_SUBJECT', 'Calendar').": $subject";
		}
		
		// send
		// crmv@78362
		$myemail = $myself ?: getUserEmailId('id', $current_user->id); 
		$mail_status = send_mail('Emails',$to_email,$myname,$myemail,$email_subject,$description, '', '', $attachment);
		// crmv@78362e
		
		if ($mail_status == 1) {
			$this->setIcalPartecipation($icalid, $answer == 'yes' ? 2 : 1);
		}

		return ($mail_status == 1);
	}
	
	//crmv@81126
	function createEventFromIcal($icalid, &$activityid) {
		global $current_user, $table_prefix, $adb;
		
		$ical = $this->getIcals($icalid);
		$ical = $ical[0];
		if (empty($ical)) return false;
		
		// parse the ical
		$vcalendar = new VTEvcalendar();
		$r = $vcalendar->parse($ical['content']);
		if ($r === false) return false;
		
		// get the event or todo
		$isTodo = false;
		$event = $vcalendar->getComponent('vevent');
		if (empty($event)) {
			$event = $vcalendar->getComponent('vtodo');
			if (empty($event)) return false;
			$isTodo = true;
		}
		
		$calendar = CRMEntity::getInstance('Calendar');
		$messagesid = $ical['messagesid'];
		
		// check for an existing event
		$activityid = $calendar->getCrmidFromUuid($ical['uuid'], $ical['recurring_idx']);

		if ($activityid > 0) {
			// get the owner and the invitees, if I'm the owner, do nothing, if I'm an invitee, update the participation
			$owner = getSingleFieldValue($table_prefix.'_crmentity', 'smownerid', 'crmid', $activityid);
			if ($owner == $current_user->id) {
				// it's mine, do nothing
			} else {
				// check if I'm one of the invitees
				if ($messagesid && $calendar->isUserInvited($activityid, $current_user->id)) {
					// ok, it's me, set the answer to yes!
					$calendar->setUserInvitationAnswer($activityid , $current_user->id, 2); // 2 = yes!
				}
			}
		} else {
			// create a new one
			$res = $vcalendar->generateArray($event,$isTodo ? 'vtodo' : "vevent");
			if (!$res['description']) $res['description'] = '';
			$calendar->column_fields = array_merge($calendar->column_fields,$res);
			$calendar->column_fields['assigned_user_id'] = $current_user->id;
			$calendar->save('Calendar');
			if (empty($calendar->id)) return false;
			$activityid = $calendar->id;
			
			// add the invitees (users only, no notifications)
			if (is_array($ical['values']['invitees'])) {
				$calInvitees = array();
				$calInviteesPart = array();
				foreach ($ical['values']['invitees'] as $invitee) {
					if ($invitee['record'] && $invitee['record']['module'] == 'Users') {
						$userid = $invitee['record']['crmid'];
						if ($userid != $current_user->id) {
							$calInvitees[] = $userid;
							$calInviteesPart[$userid] = $invitee['partecipation'];
						}
					}
				}
				if (count($calInvitees) > 0) {
					$calendar->insertIntoInviteeTable('Calendar', $calInvitees, $calInviteesPart);
				}
			}
		}
		
		// and save again the relation, to be sure
		if ($messagesid > 0) {
			$this->save_related_module('Messages', $messagesid, 'Calendar', $activityid);
		}
		
		return true;
	}
	
	function deleteEventFromIcal($icalid) {
		global $current_user, $table_prefix, $adb;
		
		$ical = $this->getIcals($icalid);
		$ical = $ical[0];
		if (empty($ical)) return false;
		
		$calendar = CRMEntity::getInstance('Calendar');
		$activityid = $calendar->getCrmidFromUuid($ical['uuid'], $ical['recurring_idx']);
		if ($activityid > 0) {
			$owner = getSingleFieldValue($table_prefix.'_crmentity', 'smownerid', 'crmid', $activityid);
			if ($owner == $current_user->id) {
				// I'm the owner, recreate the event for all the users that accepted
				$usersInv = array();
				$invitees = $calendar->getInvitees($activityid);
				if (is_array($invitees)) {
					foreach ($invitees as $inv) {
						if ($inv['type'] == 'Users' && $inv['id'] != $current_user->id && $inv['partecipation'] == 2) {
							$usersInv[] = $inv['id'];
						}
					}
				}
				// delete the event
				$calendar->trash('Calendar', $activityid);
				
				// now create a new one with the first user found (the other invitees will be added automatically)
				if (count($usersInv) > 0) {
					// change the user
					$saveCurrentUser = $current_user;
					$current_user = CRMEntity::getInstance('Users');
					$current_user->retrieveCurrentUserInfoFromFile($usersInv[0]);
					$this->createEventFromIcal($icalid);
					// switch back
					$current_user = $saveCurrentUser;
				}
			} else {
				// otherwise reply no, and unlink from message
				$this->cancelEventFromUuid($ical['uuid'], $ical['recurring_idx']);
			}
		}
		
		return true;
	}
	
	function cancelEventFromIcal($icalid) {
		global $current_user, $table_prefix, $adb;
		
		$ical = $this->getIcals($icalid);
		$ical = $ical[0];
		if (empty($ical)) return false;
		
		return $this->cancelEventFromUuid($ical['uuid'], $ical['recurring_idx']);
	}
	
	// send the invitation answer If I'm invited to an event
	function cancelEventFromUuid($uuid, $recurrIdx = 0) {
		global $current_user, $table_prefix, $adb;
		
		$calendar = CRMEntity::getInstance('Calendar');
		$activityid = $calendar->getCrmidFromUuid($uuid, $recurrIdx);
		if ($activityid > 0) {
			$owner = getSingleFieldValue($table_prefix.'_crmentity', 'smownerid', 'crmid', $activityid);
			if ($owner != $current_user->id) {
				// check If I'm invited, and change my partecipation
				if ($calendar->isUserInvited($activityid, $current_user->id)) {
					$calendar->setUserInvitationAnswer($activityid , $current_user->id, 1); // 1 = no!
				}
				// and remove the link with the email
				if ($this->id > 0) {
					$this->unlinkRelationship($this->id, 'Calendar', $activityid);
				}
			}
		}
		
		return true;
	}
	//crmv@81126e
	
	function setIcalPartecipation($icalid, $part = 0) {
		global $adb, $table_prefix;
		
		$adb->pquery("UPDATE {$this->table_name}_ical SET partecipation = ? WHERE messagesid = ? AND sequence = ?", array($part, $this->id, $icalid));
	}
	
	function getIcals($icalid = null) {
		global $adb,$table_prefix;
		$icals = array();
		
		$calFocus = CRMEntity::getInstance('Calendar');
		$query = "SELECT 
				{$this->table_name}_ical.*,
				{$this->table_name}.mfrom as sender,
				{$this->table_name}_account.email as myemail
			FROM {$this->table_name}_ical 
			INNER JOIN {$this->table_name} ON {$this->table_name}_ical.messagesid = {$this->table_name}.messagesid
			LEFT JOIN {$this->table_name}_account ON {$this->table_name}_account.id = {$this->table_name}.account
			WHERE {$this->table_name}_ical.{$this->table_index} = ?";
		$params = array($this->id);
		if ($icalid > 0) {
			$query .= " AND sequence = ?";
			$params[] = $icalid;
		}
		$result = $adb->pquery($query, $params);
		if ($result && $adb->num_rows($result) > 0) {
			while ($row = $adb->fetchByAssoc($result, -1, false)) {
				$row['method'] = trim($row['method']);
				$row['activityid'] = $calFocus->getCrmidFromUuid($row['uuid'], $row['recurring_idx']); // crmv@81126
				
				$vcalendar = new VTEvcalendar();
				$r = $vcalendar->parse($row['content']);
				if ($r === false) continue;
				$event = $vcalendar->getComponent('vevent');
				if (!$event) continue;
				$values = $vcalendar->generateArray($event, 'vevent');
				
				$values['subject'] = nl2br(htmlentities($values['subject'], ENT_COMPAT, 'UTF-8'));
				$values['location'] = nl2br(htmlentities($values['location'], ENT_COMPAT, 'UTF-8'));
				$values['description_html'] = nl2br(htmlentities($values['description'], ENT_COMPAT, 'UTF-8'));
				$values['description_html'] = $this->linkToTags($values['description_html']);
				
				// format the when
				$values['when_formatted'] = $this->formatIcalDateRange($values['date_start'].' '.$values['time_start'], $values['due_date'].' '.$values['time_end']);
				$row['values'] = $values;
				$icals[] = $row;
			}
		}
		return $icals;
	}
	
	// quick function to get only the start datetime, in local timezone
	function getIcalStartDate($icalid, &$icalRow) {
		global $default_timezone;
		$icalRow = $this->getIcals($icalid);
		$icalRow = $icalRow[0];
		if (empty($icalRow)) return false;
		
		$vcalendar = new VTEvcalendar();
		$r = $vcalendar->parse($icalRow['content']);
		if ($r === false) return false;
		
		$event = $vcalendar->getComponent('vevent');
		if (!$event) $event = $vcalendar->getComponent('vtodo');
		if (!$event) return false;
		
		$dt = $event->getProperty('DTSTART');
		$dt = $vcalendar->strtodatetime($dt);
		$dt = $dt[0].' '.$dt[1];
		
		return $dt;
	}
	
	function formatIcalDateRange($start, $end, $allday = false) {
		$monthList = array(
			'LBL_MONTH_JANUARY',
			'LBL_MONTH_FEBRUARY',
			'LBL_MONTH_MARCH',
			'LBL_MONTH_APRIL',
			'LBL_MONTH_MAY',
			'LBL_MONTH_JUNE',
			'LBL_MONTH_JULY',
			'LBL_MONTH_AUGUST',
			'LBL_MONTH_SEPTEMBER',
			'LBL_MONTH_OCTOBER',
			'LBL_MONTH_NOVEMBER',
			'LBL_MONTH_DECEMBER',
		);
		$ts1 = strtotime($start);
		$ts2 = strtotime($end);
		if (!$ts1 || !$ts2) return null;
		$day1 = substr($start, 0, 10);
		$day2 = substr($end, 0, 10);
		$date = '';
		$dow = date('w', $ts1);
		$mn = date('m', $ts1);
		$date .= getTranslatedString('LBL_DAY'.$dow, 'Calendar');
		$date .= date(' j ', $ts1);
		$date .= getTranslatedString($monthList[$mn-1]);
		$date .= date(' Y', $ts1);
		if ($day1 == $day2) {
			// same day
			if (!$allday) $date .= ', '.date('H:i', $ts1).' - '.date('H:i', $ts2) ;
		} else {
			// spans on multiple days
			$dow2 = date('w', $ts2);
			$mn2 = date('m', $ts2);
			if (!$allday) $date .= ', '.date('H:i', $ts1);
			$date .= ' - ';
			$date .= getTranslatedString('LBL_DAY'.$dow2, 'Calendar');
			$date .= date(' j ', $ts2);
			$date .= getTranslatedString($monthList[$mn2-1]);
			$date .= date(' Y', $ts2);
			if (!$allday) $date .= ', '.date('H:i', $ts2);
		}
		return $date;
	}
	
	function linkToTags($text) {
		global $adb;
		preg_match_all("/([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/",$text,$links1);
		preg_match_all("/((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/",$text,$links2);
		$links = array_merge($links1,$links2);
		if (is_array($links)) {
			$links = $adb->flatten_array(array_filter($links));
			if (is_array($links)) {
				$links = array_filter($links,create_function('$var','if ($var == "" || $var == "www") return false; else return true;'));
				if (is_array($links)) {
					$links = array_unique($links);
				}
			}
		}
		
		$text = preg_replace("/(^|[\n ])([\w]+?:\/\/.*?[^ \"\n\r\t<]*)/","\\1<a href=\"\\2\" target=\"_blank\">\\2</a>",$text);
		$text = preg_replace("/(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:\/[^ \"\t\n\r<]*)?)/","\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>",$text);
		$text = preg_replace("/,\"|\.\"|\)\"|\)\.\"|\.\)\"/","\"",$text);
		
		$searchkey = '';
		if (!empty($links)) {
			// clean links
			foreach ($links as $url) {
				$dirty_url = str_ireplace($searchkey, '<mark>'.$searchkey.'</mark>', $url);
				$text = str_ireplace($dirty_url, $url, $text);
			}
			// replace marks
			foreach ($links as $url) {
				if (strlen($url) > 60) {
					$first_part = str_ireplace($searchkey, '<mark>'.$searchkey.'</mark>', substr($url,0,45));
					$last_part = str_ireplace($searchkey, '<mark>'.$searchkey.'</mark>', substr($url,-12));
					$link = $first_part.'...'.$last_part;
				} else {
					$link = str_ireplace($searchkey, '<mark>'.$searchkey.'</mark>', $url);
				}
				$text = str_replace(">$url<",'>'.$link.'<',$text);
			}
		}
		
		return $text;
	}
	// crmv@68357e

	// crmv@91980 crmv@113417
	// put the cleaned body in the description
	function retrieve_entity_info($record, $module, $dieOnError=true, $onlyFields = array()) {
	
		$return = parent::retrieve_entity_info($record, $module, $dieOnError, $onlyFields);
		if (empty($this->column_fields['description']) && !empty($this->column_fields['cleaned_body'])) {
			$this->column_fields['description'] = $this->column_fields['cleaned_body'];
		}
		
		return $return;
	}
	
	function retrieve_entity_info_no_html($record, $module, $dieOnError=true, $onlyFields = array()) {
	
		$return = parent::retrieve_entity_info_no_html($record, $module, $dieOnError, $onlyFields);
		if (empty($this->column_fields['description']) && !empty($this->column_fields['cleaned_body'])) {
			$this->column_fields['description'] = $this->column_fields['cleaned_body'];
		}
		
		return $return;
	}
	// crmv@113417e
	
	// avoid to save the description, use directly the cleaned body
	function save($module_name,$longdesc=false,$offline_update=false,$triggerEvent=true) {
	
		// save the description for later
		$this->description_backup = $this->column_fields['description'];
		$this->column_fields['description'] = null;
		
		// call the parent
		return parent::save($module_name,$longdesc,$offline_update,$triggerEvent);
	}
	// crmv@91980e

	function save_module($module) {
		global $adb, $table_prefix;

		// crmv@64325
		$setypeCond = '';
		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		//crmv@44482 : check if saving in _crmentity has been successfully completed
		$result = $adb->pquery("SELECT crmid FROM {$table_prefix}_crmentity WHERE crmid = ? $setypeCond",array($this->id));
		// crmv@64325e

		if ($adb->num_rows($result) == 0) {
			throw new Exception('ERR_SAVING_IN_DB');
		}

		if (empty($this->column_fields['messageid'])) {
			require_once('modules/Emails/class.phpmailer.php');
			$mailer = new PHPMailer();
			$uniq_id = md5(uniqid(time()));
			$messageid = sprintf('<%s@%s>', $uniq_id, $mailer->ServerHostname());

			$adb->pquery("update {$this->table_name} set messageid = ? where {$this->table_name}.{$this->table_index} = ?", array($messageid, $this->id));
			$this->column_fields['messageid'] = $messageid;
		}

		//crmv@37004 crmv@81338 crmv@86194
		// save the hash when saving the record
		$specialFolders = $this->getSpecialFolders(false);
		if (!empty($this->column_fields['folder']) && $this->column_fields['folder'] == $specialFolders['Drafts']) {
			$hash = $this->getMessageHash($this->column_fields['messageid'], '');
		} else {
			$cleanSubject = html_entity_decode($this->column_fields['subject'], ENT_COMPAT, 'UTF-8');
			$hash = $this->getMessageHash($this->column_fields['messageid'], $cleanSubject);
		}
		if ($hash && $this->id) {
			$adb->pquery("update {$this->table_name} set messagehash = ? where {$this->table_name}.{$this->table_index} = ?", array($hash, $this->id));
			$this->column_fields['messagehash'] = $hash;
		}
		//crmv@37004e crmv@81338e crmv@86194e

		// crmv@109127
		// recover ModComments relations of deleted Messages
		$query = "SELECT {$this->relation_table}.{$this->relation_table_otherid}, {$table_prefix}_crmentity.crmid as oldmessagesid
					FROM {$this->relation_table}
					INNER JOIN {$this->table_name} ON {$this->table_name}.messagehash = {$this->relation_table}.{$this->relation_table_id}
					INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$this->table_name}.{$this->table_index}
					WHERE deleted = ? AND {$this->relation_table}.{$this->relation_table_id} = ? AND {$this->relation_table}.{$this->relation_table_othermodule} = ?";
		$result = $adb->pquery($query,array(1,$hash,'ModComments'));
		if ($result && $adb->num_rows($result) > 0) {
			while ($row = $adb->fetchByAssoc($result, -1, false)) {
				$modcommentsid = $row[$this->relation_table_otherid];
				$oldMessagesid = $row['oldmessagesid'];
				$adb->pquery("update {$table_prefix}_modcomments set related_to = ? where modcommentsid = ?",array($this->id,$modcommentsid));
				$adb->pquery("UPDATE {$table_prefix}_modcomments_msgrel SET messagesid = ? WHERE messagesid = ?", array($this->id, $oldMessagesid));
			}
		}
		// crmv@109127e

		// thread
		if (empty($this->column_fields['mreferences']) && !empty($this->column_fields['in_reply_to'])) {
			$adb->pquery("update {$this->table_name} set mreferences = ? where {$this->table_name}.{$this->table_index} = ?", array($this->column_fields['in_reply_to'], $this->id));
			$this->column_fields['mreferences'] = $this->column_fields['in_reply_to'];
		}
		// crmv@85493
		$this->deleteMrefs($this->id);
		$this->insertMrefs($this->id, $this->column_fields['mreferences']);
		// crmv@85493e
		if ($this->column_fields['mtype'] == 'Webmail') {
			//$this->updateThreadCount($hash);
			$this->updateThreadCount();
		}

		// save attachments information
		if (!empty($this->column_fields['other'])) {
			if ($this->mode == 'edit') {
				$adb->pquery("delete from {$this->table_name}_attach where {$this->table_index} = ?",array($this->id));
			}
			foreach ($this->column_fields['other'] as $id => $content) {
				//crmv@65328 crmv@68357
				$adb->pquery("insert into {$this->table_name}_attach ({$this->table_index},contentid,content_id,contentname,contenttype,contentdisposition,contentcharset,contentencoding,contentmethod,size) values (?,?,?,?,?,?,?,?,?,?)",
					array($this->id,$id,$content['parameters']['content_id'],$content['parameters']['name'],$content['parameters']['contenttype'],$content['parameters']['contentdisposition'],$content['parameters']['charset'],$content['parameters']['encoding'],$content['parameters']['method'],$content['parameters']['size']));
				//crmv@65328e crmv@68357e
			}
		}
		
		//crmv@63475 recover attach relations of deleted Messages
		$query = "SELECT {$table_prefix}_messages_attach.*
					FROM {$this->table_name}
					INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$this->table_name}.{$this->table_index}
					INNER JOIN {$table_prefix}_messages_attach ON {$this->table_name}.messagesid = {$table_prefix}_messages_attach.messagesid
					WHERE deleted = ? AND messagehash = ? AND document IS NOT NULL";
		$result = $adb->pquery($query,array(1,$hash));
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByASsoc($result)) {
				$adb->pquery("update {$table_prefix}_messages_attach set document = ? where messagesid = ? and contentid = ?",array($row['document'],$this->id,$row['contentid']));
			}
		}
		//crmv@63475e

		// crmv@68357 crmv@81126 - save text/calendar parts to be able to show the invitation/reply
		if (!empty($this->column_fields['icals'])) {
			foreach ($this->column_fields['icals'] as $seq => $ical) {
				// extract uid
				$uuid = null;
				$method = '';
				$recurrIdx = 0;
				if (preg_match('/^UID:(.*)$/m', $ical, $matches)) $uuid = $matches[1];
				if (preg_match('/^METHOD:(.*)$/m', $ical, $matches)) $method = $matches[1];
				if (preg_match('/^SEQUENCE:([0-9]+)$/m', $ical, $matches)) $recurrIdx = intval($matches[1]) ?: 0;
				if ($uuid) {
					$res = $adb->pquery("SELECT messagesid FROM {$this->table_name}_ical WHERE messagesid = ? AND sequence = ?", array($this->id,$seq));
					if ($res && $adb->num_rows($res) > 0) {
						// update
						$adb->pquery("UPDATE {$this->table_name}_ical SET uuid = ?, recurring_idx = ?, method = ?, content = ? WHERE messagesid = ? AND sequence = ?", array($uuid, $recurrIdx, $method, $ical, $this->id,$seq));
					} else {
						// insert
						$adb->pquery("INSERT INTO {$this->table_name}_ical ({$this->table_index},sequence,uuid,recurring_idx,method,content) values (?,?,?,?,?,?)", array($this->id,$seq, $uuid, $recurrIdx, $method, $ical));
					}
					if ($method == 'REPLY') {
						$this->processIcalReply($uuid, $recurrIdx, $ical);
					} elseif ($method == 'REQUEST') {
						$this->processIcalRequest($uuid, $recurrIdx, $ical);
					}
				}
			}
		}
		// crmv@68357e crmv@81126e
		
		//crmv@46760
		if (isset($_FILES) && !empty($_FILES) && isset($_REQUEST['element'])){
			$elements = @Zend_Json::decode($_REQUEST['element']);
			if (isset($elements) && $elements['hasattachments'] == 'True' && $elements['external_plugin'] == 'true'){
				$files_arr = $_FILES;
				$contentid = 0;
				foreach($files_arr as $fileindex => $files){
					if($files['name'] != '' && $files['size'] > 0){
						$_FILES = Array();
						$_FILES['filename'] = $files;
						//TODO:check if other plugins than outlook put unique id before real name...
						if (strpos($files['name'],"_")!== false){
							$files['name'] = explode("_",$files['name'],2);
							$files['name'] = $files['name'][1];
						}
						// Create document record
						//crmv@86304
						$resFolder = $adb->pquery("select folderid from {$table_prefix}_crmentityfolder where foldername = ?", array('Message attachments'));
						($resFolder && $adb->num_rows($resFolder) > 0) ? $folderid = $adb->query_result($resFolder,0,'folderid') : $folderid = 1;
						//crmv@86304e
						$document = CRMEntity::getInstance('Documents');
						$document->column_fields['notes_title']      = $files['name'];
						$document->column_fields['filelocationtype'] = 'I';
						$document->column_fields['folderid']         = $folderid;	//crmv@86304
						$document->column_fields['filestatus']    	 = 1; // Active
						$document->column_fields['assigned_user_id'] = $this->column_fields['assigned_user_id'];
						$document->parentid = $this->id;
						if (method_exists($document,'autoSetBUMC')) $document->autoSetBUMC('Documents',$current_user->id);	//crmv@93302
						$document->save('Documents');
						$documentid = $document->id;
						if ($documentid != ''){
							$params = Array(
							$this->table_index=>$id,
								'messagesid'=>$this->id,
								'contentid'=>$contentid,
								'contentname'=>$files['name'],
								'contenttype'=>$files['type'],
								'contentdisposition'=>'external attachment',
								'document'=>$documentid,
								'size'=>$files['size'],	//crmv@65328
							);
							$sql = "insert into {$this->table_name}_attach (".implode(",",array_keys($params)).") values (".generateQuestionMarks($params).")";
							$adb->pquery($sql,$params);
						}
						$contentid++;
					}
				}
			}
		}
		//crmv@46760e

		// crmv@49398 crmv@56409 crmv@91980
		if ($this->id > 0 && !empty($this->description_backup)) {
			// clean the body and save it
			if (empty($this->column_fields['cleaned_body'])) {
			$attachments_info = $this->getAttachmentsInfo();
			$message_data = array('other'=>$attachments_info);
				$description = str_replace('&amp;', '&', $this->description_backup);
			$magicHTML = $this->magicHTML($description, $this->column_fields['xuid'], $message_data);
			$this->saveCleanedBody($this->id, $magicHTML['html'], $magicHTML['content_ids']);
			$this->column_fields['cleaned_body'] = $magicHTML['html'];
			$this->column_fields['content_ids'] = $magicHTML['content_ids'];
		}
			// save the phone numbers
			$numbers = $this->extractPhoneNumbers($this->description_backup);
			if (count($numbers) > 0) {
				$this->deletePhoneNumbers($this->id);
				$this->savePhoneNumbers($this->id, $numbers);
			}
		}
		// and unset, to release memory
		unset($this->description_backup);
		// crmv@49398e crmv@56409e crmv@91980e

		// set recipients
		if (!empty($this->column_fields['recipients'])) {
			$adb->pquery("delete from {$table_prefix}_messages_recipients where messagesid = ?",array($this->id));
			$this->setRecipients($this->id,$this->column_fields['recipients']);
		}

		// set/update relations
		if (!empty($this->column_fields['parent_id'])) {
			$ids = array_filter(explode('|', $this->column_fields['parent_id']));
			foreach ($ids as $relid) {
				list($elid, $fieldid) = explode('@', $relid, 2);
				if (strpos($elid,'x') !== false) {
					$elid = explode('x',$elid);
					$elid = $elid[1];
				}
				$mod = getSalesEntityType($elid);
				if ($mod) {
					$this->save_related_module_small($messageid, $mod, $elid);
				}
			}
		}
	}

	// crmv@81338	crmv@81889
	function getParentMessage($id,$folder,$prev_mid=array()) {
		global $adb, $table_prefix;

		$focus = CRMEntity::getInstance('Messages');
		$focus->retrieve_entity_info_no_html($id,'Messages');

		if (!empty($focus->column_fields['mreferences'])) {
			if (preg_match_all('/<[^<>]+>/',$focus->column_fields['mreferences'],$matches) && !empty($matches[0])) {
				$references = $matches[0];
				foreach($references as $reference) {
					$mid = trim($reference);
					if (is_array($prev_mid) && count($prev_mid) > 0 && in_array($mid, $prev_mid)) {
						return $id;
					}
					//crmv@64337
					if ($this->hasOwnCrmColumns) {
						$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
											WHERE msg_deleted = 0 AND msg_smownerid = ? AND {$this->table_name}.folder = ? AND {$this->table_name}.messageid = ?",
											array($focus->column_fields['assigned_user_id'],$folder,$mid));
					} else {
						// crmv@64325
						$setypeCond = '';
						if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
							$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
						}
						$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
												INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
												WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.folder = ? AND {$this->table_name}.messageid = ?",
												array($focus->column_fields['assigned_user_id'],$folder,$mid));
						// crmv@64325e
					}
					//crmv@64337e
					if ($result && $adb->num_rows($result)>0) {
						$prev_mid[] = $mid;
						return $this->getParentMessage($adb->query_result_no_html($result,0,$this->table_index),$folder,array_unique($prev_mid));
					} else {	// search father in other folders (ex. Sent) and so search the next father in the current folder
						//crmv@64337
						if ($this->hasOwnCrmColumns) {
							$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
												WHERE msg_deleted = 0 AND msg_smownerid = ? AND {$this->table_name}.folder <> ? AND {$this->table_name}.messageid = ?",
												array($focus->column_fields['assigned_user_id'],$folder,$mid));
						} else {
							// crmv@64325
							$setypeCond = '';
							if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
								$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
							}
							$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
													INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
													WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.folder <> ? AND {$this->table_name}.messageid = ?",
													array($focus->column_fields['assigned_user_id'],$folder,$mid));
							// crmv@64325e
						}
						//crmv@64337e
						if ($result && $adb->num_rows($result)>0) {
							$result1 = $adb->pquery("SELECT in_reply_to FROM {$this->table_name} WHERE {$this->table_index} = ?", array($adb->query_result_no_html($result,0,$this->table_index)));
							if ($result1 && $adb->num_rows($result1) > 0) {
								$in_reply_to = $adb->query_result_no_html($result1,0,'in_reply_to');
								if (!empty($in_reply_to)) {
									$mid = trim($in_reply_to);
									//crmv@64337
									if ($this->hasOwnCrmColumns) {
										$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
																WHERE msg_deleted = 0 AND msg_smownerid = ? AND {$this->table_name}.folder = ? AND {$this->table_name}.messageid = ?",
																array($focus->column_fields['assigned_user_id'],$folder,$mid));
									} else {
										// crmv@64325
										$setypeCond = '';
										if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
											$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
										}
										$result = $adb->pquery("SELECT {$this->table_name}.{$this->table_index} FROM {$this->table_name}
																INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
																WHERE deleted = 0 $setypeCond AND smownerid = ? AND {$this->table_name}.folder = ? AND {$this->table_name}.messageid = ?",
																array($focus->column_fields['assigned_user_id'],$folder,$mid));
										// crmv@64325e
									}
									//crmv@64337e
									if ($result && $adb->num_rows($result)>0) {
										$prev_mid[] = $mid;
										return $this->getParentMessage($adb->query_result_no_html($result,0,$this->table_index),$folder,array_unique($prev_mid));
									}
								}
							}
						}
					}
				}
			}
		}
		return $id;
	}
	// crmv@81338e	crmv@81889e

	function updateThreadCount() {
		$folder = $this->column_fields['folder'];
		$father = $this->getParentMessage($this->id,$folder);
		$this->insertIntoTh($folder,$father,$this->id);
		$adopt_result = $this->adoptChildren($folder,$father);
		if (!$adopt_result && !empty($this->column_fields['mreferences'])) {
			$this->referenceChildren($folder,$this->column_fields['mreferences']);
			//TODO: $this->adoptReferenceChildren($folder,$this->column_fields['mreferences']);
		}
		$this->updateLastSon($folder);
	}

	function insertIntoTh($folder,$father,$son) {
		global $adb, $table_prefix;
		$adb->pquery("delete from {$table_prefix}_messages_th where folder = ? and father = ? and son = ?",array($folder,$son,$son));	//prevent duplicate rows (only 1 row for son)
		if ($adb->isMysql()) {
			$adb->pquery("insert ignore into {$table_prefix}_messages_th (folder,father,son) values (?,?,?)",array($folder,$father,$son));
		} else {
			$result = $adb->pquery("SELECT * FROM {$table_prefix}_messages_th
									WHERE {$table_prefix}_messages_th.folder = ? AND {$table_prefix}_messages_th.father = ? AND {$table_prefix}_messages_th.son = ?",
									array($folder,$father,$son));
			if (!$result || $adb->num_rows($result) == 0) {
				$adb->pquery("insert into {$table_prefix}_messages_th (folder,father,son) values (?,?,?)",array($folder,$father,$son));
			}
		}
	}

	function adoptChildren($folder,$id) {
		global $adb, $table_prefix;
		$messageid = '';
		$result = $adb->pquery("SELECT {$this->table_name}.messageid FROM {$this->table_name}
								INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
								WHERE deleted = 0 AND {$this->table_name}.{$this->table_index} = ?",array($id));
		if ($result && $adb->num_rows($result)>0) {
			$messageid = $adb->query_result_no_html($result,0,'messageid');	//crmv@81889
		}
		if (!empty($messageid)) {
			// crmv@64325
			$setypeCond = '';
			if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
				$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
			}
			// crmv@85493
			$result = $adb->pquery(
				"SELECT {$this->table_name}.messagesid FROM {$this->table_name}
				INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
				INNER JOIN {$table_prefix}_messages_mref ON {$table_prefix}_messages_mref.messagesid = {$this->table_name}.{$this->table_index}
				WHERE deleted = 0 $setypeCond AND smownerid = ? AND folder = ? AND {$table_prefix}_messages_mref.mreference = ?",
				array($this->column_fields['assigned_user_id'],$folder,$messageid)
			);
			// crmv@64325e crmv@85493e
			if ($result && $adb->num_rows($result)>0) {
				while($row=$adb->fetchByAssoc($result)) {
					if ($adb->isMysql()) {
						$adb->pquery("update ignore {$table_prefix}_messages_th set father = ? where father = ?",array($id,$row['messagesid']));
					} else {	//TODO
						$adb->pquery("update {$table_prefix}_messages_th set father = ? where father = ?",array($id,$row['messagesid']));
					}
				}
				return true;
			}
		}
		return false;
	}

	function updateLastSon($folder,$father='') {
		global $adb, $table_prefix;
		if (empty($father)) {
			$father = $this->getFather($this->id, $folder);
		}
		if (!empty($father)) {
			$children = $this->getChildren($father,$folder);
			if (!empty($children)) {
				global $adb, $table_prefix;
				$lastson = $children[0];
				if (!empty($lastson)) {
					$adb->pquery("update {$table_prefix}_messages set lastson = ? where messagesid = ?",array($lastson,$father));
				}
				$children = array_diff($children,array($father));
				if (!empty($children)) {
					$adb->pquery("update {$table_prefix}_messages set lastson = null where messagesid IN (".generateQuestionMarks($children).")",array($children));
				}
			}
		}
	}

	function referenceChildren($folder,$mreferences) {
		// se trovo un Messaggio piu vecchio con reference simile al mio diventa mio padre
		global $adb, $table_prefix;
		
		// crmv@85493
		$reflist = $this->splitMrefs($mreferences);
		if (count($reflist) == 0) return false;

		if (PerformancePrefs::getBoolean('CRMENTITY_PARTITIONED')) {
			$setypeCond = "AND {$table_prefix}_crmentity.setype = 'Messages'";
		}
		
		$result = $adb->limitpQuery(
			"SELECT {$this->table_name}.messagesid FROM {$this->table_name}
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
			INNER JOIN {$table_prefix}_messages_mref ON {$table_prefix}_messages_mref.messagesid = {$this->table_name}.{$this->table_index}
			WHERE deleted = 0 $setypeCond AND smownerid = ? AND folder = ? AND {$table_prefix}_messages_mref.mreference IN (".generateQuestionMarks($reflist).")
				AND {$this->table_name}.{$this->table_index} <> ? AND mdate < ?
			ORDER BY mdate DESC",
			0,1,
			array($this->column_fields['assigned_user_id'],$folder,$reflist,$this->id,$this->column_fields['mdate'])
		);
		// crmv@85493e

		if ($result && $adb->num_rows($result)>0) {
			$messagesid = $adb->query_result($result,0,$this->table_index);
			$father = $this->getFather($messagesid, $folder);
			if (!empty($messagesid) && !empty($father)) {
				$this->insertIntoTh($folder,$father,$this->id);
			}
			return true;
		}
		return false;
	}

	// crmv@85493
	function rebuildMrefTable() {
		global $adb, $table_prefix;
		
		// empty the table
		if ($adb->isMysql()) {
			$adb->query("TRUNCATE TABLE {$table_prefix}_messages_mref");
		} else {
			$adb->query("DELETE FROM {$table_prefix}_messages_mref");
		}
		
		$query = "SELECT messagesid, mreferences FROM {$this->table_name}
			INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
			WHERE deleted = 0 AND mreferences IS NOT NULL";
		($adb->isMssql()) ? $query .= " AND mreferences NOT LIKE ''" : $query .= " AND mreferences != ''";
		$result = $adb->query($query);
		if ($result && $adb->num_rows($result)>0) {
			while ($row = $adb->fetchByAssoc($result, -1, false)) {
				$messagesid = $row[$this->table_index];
				$refs = trim($row['mreferences']);
				if ($refs) {
					$this->insertMrefs($messagesid, $refs);
				}
			}
		}
	}
	
	function splitMrefs($mrefs) {
		$list = array();
		
		// convert strange spaces to regular space
		$mrefs = str_replace(array("\t", "\n", "\r"), "", $mrefs);
		
		// split
		$refs = array_filter(array_map('trim', explode(' ', $mrefs)));
		
		if (count($refs) > 0) {
			foreach ($refs as $mref) {
				// now explode again, because some stupid mrefs are not space separated
				$refs2 = preg_split('/><|>,\s*</', $mref, null, PREG_SPLIT_NO_EMPTY);
				if (count($refs2) > 1) {
					foreach ($refs2 as $mref) {
						if ($mref[0] != '<') $mref = "<".$mref;
						if (substr($mref, -1) != '>') $mref .= ">";
						$list[] = $mref;
					}
				} else {
					// single mref
					$mref = trim($mref, ",;");
					if ($mref[0] != '<') $mref = "<".$mref;
					if (substr($mref, -1) != '>') $mref .= ">";
					$list[] = $mref;
				}
			}
		}
		
		return $list;
	}
	
	function insertMrefs($messagesid, $mrefs) {
		$refs = $this->splitMrefs($mrefs);
		if (is_array($refs) && count($refs) > 0) {
			foreach ($refs as $mref) {
				$this->insertMref($messagesid,$mref);
			}
		}
	}
	
	function insertMref($messagesid, $mref) {
		global $adb, $table_prefix;
		
		// sanitize mref
		$mref = trim(str_replace(array('&lt;', '&gt;'), array('<', '>'), $mref));
		
		// insert
		if ($adb->isMysql()) {
			$adb->pquery("INSERT IGNORE INTO {$table_prefix}_messages_mref (messagesid, mreference) VALUES (?,?)", array($messagesid, $mref));
		} else {
			$result = $adb->pquery("SELECT messagesid FROM {$table_prefix}_messages_mref WHERE messagesid = ? AND mreference = ?",array($messagesid, $mref));
			if ($result && $adb->num_rows($result) == 0) {
				$adb->pquery("INSERT INTO {$table_prefix}_messages_mref (messagesid, mreference) VALUES (?,?)", array($messagesid, $mref));
			}
		}
	}
	
	function deleteMref($messagesid, $mref) {
		global $adb, $table_prefix;
		
		$adb->pquery("DELETE FROM {$table_prefix}_messages_mref WHERE messagesid = ? AND mreference = ?",array($messagesid, $mref));
	}
	
	function deleteMrefs($messagesid) {
		global $adb, $table_prefix;
		
		$adb->pquery("DELETE FROM {$table_prefix}_messages_mref WHERE messagesid = ?",array($messagesid));
	}
	
	function getMrefs($messagesid) {
		// TODO
	}
	
	function searchMref($search) {
		$msgids = array();
		// TODO
		return $msgids;
	}
	// crmv@85493e

	/* TODO
	function adoptReferenceChildren($folder,$mreferences) {
		// se trovo Messaggi piu recenti con reference simile al mio diventano miei figli
		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT {$this->table_index} FROM {$this->table_name}
								INNER JOIN {$this->entity_table} ON {$this->entity_table}.crmid = {$this->table_name}.{$this->table_index}
								WHERE deleted = 0 AND smownerid = ? AND folder = ? AND mreferences LIKE ? AND {$this->table_name}.{$this->table_index} <> ? AND mdate >= ?",
								array($this->column_fields['assigned_user_id'],$folder,"%{$mreferences}%",$this->id,$this->column_fields['mdate']));
		if ($result && $adb->num_rows($result)>0) {
			while ($row=$adb->fetchByAssoc($result)) {
				$messagesid = $row[$this->table_index];
				$father = $this->getFather($messagesid, $folder);
				if (!empty($messagesid) && !empty($father) && ($messagesid == $father)) {
					//delete vecchie righe...
					$this->insertIntoTh($folder,$this->id,$messagesid);
				}
			}
			return true;
		}
		return false;
	}
	*/
	function getFather($record,$folder='') {
		global $adb, $table_prefix, $current_folder;
		if (empty($folder)) {
			$folder = $current_folder;
		}
		$query = "SELECT messageFather.messagesid
				FROM {$table_prefix}_messages_th
				INNER JOIN {$table_prefix}_messages messageSon ON messageSon.messagesid = {$table_prefix}_messages_th.son
				INNER JOIN {$this->entity_table} entitySon ON entitySon.crmid = messageSon.messagesid
				INNER JOIN {$table_prefix}_messages messageFather ON messageFather.messagesid = {$table_prefix}_messages_th.father
				INNER JOIN {$this->entity_table} entityFather ON entityFather.crmid = messageFather.messagesid
				WHERE entityFather.deleted = 0 AND entitySon.deleted = 0
				AND {$table_prefix}_messages_th.folder = ? AND messageSon.messagesid = ?";
		$result = $adb->pquery($query,array($folder,$record));
		if ($result && $adb->num_rows($result) > 0) {
			$father = $adb->query_result($result,0,'messagesid');
			return $father;
		}
		return false;
	}

	function getChildren($father,$folder='',$return_count=false,$select='') {
		global $adb, $table_prefix, $current_folder;
		if (empty($folder)) {
			$folder = $current_folder;
		}
		if (empty($select)) {
			$select = 'DISTINCT messageSon.messagesid';
		}
		$query = "SELECT $select as \"messagesid\"";
		if ($adb->isMssql() || $adb->isOracle()) $query .= ", messageSon.mdate";	//crmv@63611
		$query .= " FROM {$table_prefix}_messages_th
				INNER JOIN {$table_prefix}_messages messageFather ON messageFather.messagesid = {$table_prefix}_messages_th.father
				INNER JOIN {$this->entity_table} entityFather ON entityFather.crmid = messageFather.messagesid
				INNER JOIN {$table_prefix}_messages messageSon ON messageSon.messagesid = {$table_prefix}_messages_th.son
				INNER JOIN {$this->entity_table} entitySon ON entitySon.crmid = messageSon.messagesid
				WHERE entityFather.deleted = 0 AND entitySon.deleted = 0
				AND {$table_prefix}_messages_th.folder = ? AND messageFather.messagesid = ?
				ORDER BY messageSon.mdate DESC";
		$result = $adb->pquery($query,array($folder,$father));
		$count = $adb->num_rows($result);
		if ($result && $count > 0) {
			if ($return_count) {
				return $count;
			} else {
				$children = array();
				while($row=$adb->fetchByAssoc($result)) {
					$children[] = $row['messagesid'];
				}
				return $children;
			}
		}
	}

	function getParents($record,$folder='') {
		$father = $this->getFather($record,$folder);
		if ($father) {
			$children = $this->getChildren($father,$folder);
			if (!empty($children)) {
				$children = array_diff($children,array($record));
				return $children;
			}
		}
		return false;
	}

	function appendMessage($sendmail, $account, $specialFolder, $parentids='') {	//crmv@84628
		$this->setAccount($account);
		$specialFolders = $this->getSpecialFolders(false);	//crmv@53929
		$folder = $specialFolders[$specialFolder];	//crmv@84628
		if (empty($folder)) {
			return false;
		}
		//crmv@53929
		try {
			$this->getZendMailStorageImap();
		} catch (Exception $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			return false;
		}
		//crmv@53929e

		//crmv@34888
		$sendmail->error_count = 0; // reset errors
        $sendmail->SetMessageType();
        //crmv@34888e
		$sendmail->Mailer = 'sendmail';
		if ($specialFolder == 'Drafts') $sendmail->addCustomHeader("Content-Class: VTECRM-DRAFT");	//crmv@84628
		$header = str_replace("\n","\r\n",$sendmail->CreateHeader($sendmail->message_id));
		$body = str_replace("\n","\r\n",$sendmail->CreateBody());

		if (empty($body)) {
			$body = $sendmail->AltBody;
		}
		$message = "$header\r\n"."$body\r\n";
		$flags = array(Zend\Mail\Storage::FLAG_SEEN);
		if ($specialFolder == 'Drafts') $flags[] = Zend\Mail\Storage::FLAG_DRAFT;	//crmv@84628
		try {
			self::$mail->appendMessage($message, $folder, $flags);
			// set/update relations
			if (!empty($parentids)) {
				$ids = array_filter(explode('|', $parentids));
				foreach ($ids as $relid) {
					list($elid, $fieldid) = explode('@', $relid, 2);
					if (strpos($elid,'x') !== false) {
						$elid = explode('x',$elid);
						$elid = $elid[1];
					}
					$mod = getSalesEntityType($elid);
					if ($mod) {
						($specialFolder == 'Drafts') ? $this->save_related_module_small($sendmail->message_id, $mod, $elid, '') : $this->save_related_module_small($sendmail->message_id, $mod, $elid, $sendmail->Subject);	// crmv@81338 crmv@86194
					}
				}
			}
			return true;
		} catch (Zend\Mail\Exception\RuntimeException $e) {
			$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
			return false;
		}
	}

	function moveMessage($folder,$skip_fetch=false) {
		parent::trash('Messages', $this->id);
		$this->addToPropagationCron('move', array(
			'userid'=>$this->column_fields['assigned_user_id'],
			'account'=>$this->column_fields['account'],
			'folder'=>$this->column_fields['folder'],
			'uid'=>$this->column_fields['xuid'],
			'new_folder'=>$folder,
			'skip_fetch'=>$skip_fetch
		));
	}
	
	function propagateMoveMessage($userid,$account,$folder,$uid,$new_folder,$skip_fetch=false) {
		$focus = CRMEntity::getInstance('Messages');
				
		$focus->setAccount($account);
		$focus->getZendMailStorageImap($userid);
		$focus->selectFolder($folder);
		self::$mail->moveMessage(self::$mail->getNumberByUniqueId($uid),$new_folder);

		//fetch new message from destination folder
		if (!$skip_fetch) {
			global $current_user;
			$tmp = $current_user->id;
			$current_user->id = $userid;
			
			$focus->fetchNews($new_folder);
			
			$current_user->id = $tmp;
		}
	}

	function massMoveMessage($account,$old_folder,$folder) {
		global $adb, $table_prefix, $currentModule, $current_user;
		$ids = getListViewCheck($currentModule);

		if (!empty($ids) && is_array($ids)) {
			$idstring = implode(',',$ids);
			$result = $adb->query("SELECT messagesid, xuid FROM {$this->table_name} WHERE messagesid in ({$idstring})");
			if ($result && $adb->num_rows($result) > 0) {
				while($row=$adb->fetchByAssoc($result)) {
					parent::trash('Messages', $row['messagesid']);
					$uids[] = $row['xuid'];
				}
				$this->addToPropagationCron('move_mass', array(
					'userid'=>$current_user->id,
					'account'=>$account,
					'folder'=>$old_folder,
					'uid'=>$uids,
					'new_folder'=>$folder,
				));
			}
		}
	}
	
	function propagateMassMoveMessage($userid,$account,$folder,$uids,$new_folder) {
		$focus = CRMEntity::getInstance('Messages');

		$focus->setAccount($account);
		$focus->getZendMailStorageImap($userid);
		$focus->selectFolder($folder);
		foreach($uids as $uid) {
			self::$mail->moveMessage(self::$mail->getNumberByUniqueId($uid),$new_folder);
		}
		
		global $current_user;
		$tmp = $current_user->id;
		$current_user->id = $userid;
		
		$focus->fetchNews($new_folder,count($uids));
		
		$current_user->id = $tmp;
	}

	function beforeTrashFunctions($record) {
		$focus = CRMEntity::getInstance('Messages');
		$focus->id = $record;
		//crmv@80636
		$result = $focus->retrieve_entity_info($record,'Messages',false);
		if (in_array($result,array('LBL_RECORD_DELETE','LBL_RECORD_NOT_FOUND'))) {
			return false;
		}
		//crmv@80636e
		// functions
		$focus->removeFromThread();
	}

	function removeFromThread() {
		global $adb, $table_prefix;
		$record = $this->id;
		$folder = $this->column_fields['folder'];
		$father = $this->getFather($record,$folder);
		if (empty($father)) {
			$father = $record;
		}
		if ($record == $father) {
			$children = $this->getChildren($record,$folder);
			if (!empty($children)) {
				// delete _messages_th
				$adb->pquery("delete from {$table_prefix}_messages_th where father = ? and folder = ? and son = ?",array($record,$folder,$record));
				// if it has children, set new father and reload lastson
				if (count($children) > 1) {
					$newfather = $children[count($children)-2];
					$adb->pquery("update {$table_prefix}_messages_th set father = ? where folder = ? and father = ?",array($newfather,$folder,$record));
					$this->updateLastSon($folder,$newfather);
				}
			}
		} else {
			// if it is son, delete from _messages_th and reload lastson
			$adb->pquery("delete from {$table_prefix}_messages_th where father = ? and folder = ? and son = ?",array($father,$folder,$record));
			$this->updateLastSon($folder,$father);
		}
	}

	function getNonAdminAccessControlQuery($module,$user,$scope='',$join_cond=''){
		return '';
	}
	
	//crmv@47243	crmv@61173
	function getNonAdminUserAccessQuery($user,$parentRole,$userGroups){
		$defOrgSharingPermission = getAllDefaultSharingAction();
		if ($defOrgSharingPermission[getTabid('Messages')] == 8) {
			global $table_prefix;
			$query = "select id from (SELECT id from ".$table_prefix."_users where id = '$user->id'";
			return $query;
		} else {
			return parent::getNonAdminUserAccessQuery($user,$parentRole,$userGroups);		
		}
	}
	//crmv@47243e	crmv@61173e

	// crmv@63349
	function getQueryExtraJoin() {
		//crmv@79192
		$sql = '';
		global $table_prefix, $currentModule, $current_user, $current_folder, $current_account;
		if ($current_folder == 'Flagged') {
			$sql .= " INNER JOIN (
				SELECT MIN({$this->table_name}.{$this->table_index}) AS {$this->table_index}
				FROM {$this->table_name} 
				INNER JOIN {$this->entity_table} ON {$this->table_name}.{$this->table_index} = {$this->entity_table}.crmid 
				WHERE {$this->entity_table}.deleted = 0
				  AND {$this->entity_table}.smownerid = {$current_user->id}
				  AND {$this->table_name}.mtype = 'Webmail'
				  AND {$this->table_name}.account = {$current_account}
				  AND {$this->table_name}.flagged = 1
				GROUP BY {$this->table_name}.messagehash
			) flagged_messages ON flagged_messages.{$this->table_index} = {$this->table_name}.{$this->table_index}";
		}
		if (PerformancePrefs::getBoolean('USE_TEMP_TABLES', true)) {
			$sql .= $this->getQueryExtraJoin_tmp();
		} else {
			$sql .= $this->getQueryExtraJoin_notmp();
		}
		return $sql;
		//crmv@79192e
	}

	function getQueryExtraJoin_notmp() {
		global $adb, $table_prefix, $current_folder, $current_user;

		$sql = $query = '';
		if ($current_folder == 'Shared') {
			$tableName = $table_prefix."_modcomments_msgrel";
			$sql = " INNER JOIN $tableName ON $tableName.userid = {$current_user->id} AND $tableName.messagesid = {$this->table_name}.{$this->table_index}";
		}
		return $sql;
	}
	// crmv@63349e

	function getQueryExtraJoin_tmp() { // crmv@63349
		global $adb, $current_folder, $current_user;
		$sql = $query = '';
		if ($current_folder == 'Shared') {
			$query = $this->getRelatedModComments(true);
		}
		if (!empty($query)) {
			$tableName = 'vt_tmp_s_'.$current_user->id;
			if ($adb->isMysql()) {
				$query = "create temporary table IF NOT EXISTS $tableName(id int(11) primary key) ignore ".$query;
				$result = $adb->query($query);
			} else {
				if (!$adb->table_exist($tableName,true)){
					Vtiger_Utils::CreateTable($tableName,"id I(11) NOTNULL PRIMARY",true,true);
				}
				$tableName = $adb->datadict->changeTableName($tableName);
				$query = "insert into $tableName $query where not exists (select * from $tableName where $tableName.id = un_table.id)";
				$result = $adb->query($query);
			}
			$sql = " INNER JOIN $tableName ON $tableName.id = {$this->table_name}.{$this->table_index}";
		}
		return $sql;
	}

	function getQueryExtraWhere() {
		global $current_account, $current_folder, $current_user, $thread;
		$sql = '';
		if ($current_folder == 'Links') {
			$sql .= " and {$this->table_name}.mtype = 'Link'";
			$sql .= " and {$this->entity_table}.smownerid = {$current_user->id}";
		} elseif (in_array($current_folder, array('Shared','Flagged'))) {	//crmv@79192
			// do nothing, checks done in getQueryExtraJoin
		} elseif (!empty($current_folder)) {
			$account_condition = " and {$this->table_name}.account = '{$current_account}'";
			if ($current_account == 'all') {
				$folders = $this->getAllSpecialFolders('INBOX');
				$tmp = array();
				foreach($folders as $account => $folder) {
					$tmp[] = "({$this->table_name}.account = '{$account}' AND {$this->table_name}.folder = '{$folder['INBOX']}')";
				}
				$account_condition = ' AND ('.implode(' OR ',$tmp).')';
			} else {
				$sql .= " and {$this->table_name}.folder = '$current_folder'";
			}
			$sql .= " and {$this->entity_table}.smownerid = {$current_user->id}";
			$sql .= " and {$this->table_name}.mtype = 'Webmail'";
		}
		$sql .= $account_condition;
		if (!empty($thread)) {
			$children = $this->getChildren($thread);
			if (!empty($children)) {
				$sql .= " and {$this->table_name}.messagesid in (".implode(',',$children).")";
			} else {
				$sql .= " and {$this->table_name}.messagesid in (0)";	//force empty list
			}
		}
		return trim($sql);
	}

	//crmv@47243	crmv@61173
	function getAdvancedPermissionFunction($is_admin,$module,$actionname,$record_id='') {
		
		require('user_privileges/requireUserPrivileges.php'); // crmv@39110
		
		if (in_array($actionname,array('Import','Export','Merge','DuplicatesHandling'))) {
			return 'no';
		} elseif (in_array($actionname,array('PopupDetailForm'))) {	// real check done in the file
			return 'yes';
		}
		//if (!$is_admin && !empty($record_id)) {	//crmv@44747: give to admin all permissions for workflow
		if (!empty($record_id)) {	//crmv@55336
			global $current_user, $adb, $table_prefix;
			
			$smownerid = getSingleFieldValue($table_prefix.'_crmentity', 'smownerid', 'crmid', $record_id);

			// the owner can do everything (performance fix, avoid following code when not needed)
			if ($smownerid == $current_user->id) return 'yes';
			
			// only owner can delete
			if ($actionname == 'Delete' && $current_user->id != $smownerid) {
				//echo 'delete';
				return 'no';
			}

			$mvisibility = getSingleFieldValue($table_prefix.'_messages', 'mvisibility', 'messagesid', $record_id);
			if ($mvisibility == 'Public') {
				//echo 'public';
				return 'yes';
			}

			//crmv@61173
			$mtype = getSingleFieldValue($table_prefix.'_messages', 'mtype', 'messagesid', $record_id);
			if ($mtype == 'Link' && in_array($actionname,array('EditView','Delete'))) {
				//echo 'link';
				return 'no';
			}
			//crmv@61173e

			$account = getSingleFieldValue($table_prefix.'_messages', 'account', 'messagesid', $record_id);
			$this->setAccount($account);
			// crmv@63349
			if (PerformancePrefs::getBoolean('USE_TEMP_TABLES', true)) {
				if (in_array($record_id,$this->getRelatedModComments())) return 'yes';
			} else {
				if ($this->isMessageRelatedModComments($record_id)) return 'yes';
			}
			// crmv@63349e

			$tabid = getTabid($module);
			
			// check owner
			// crmv@63349
			if (PerformancePrefs::getBoolean('USE_TEMP_TABLES', true)) {
				$tableName = 'vt_tmp_u'.$current_user->id;
				$sharingRuleInfoVariable = $module.'_share_read_permission';
				$sharingRuleInfo = $$sharingRuleInfoVariable;
				if(!empty($sharingRuleInfo) && (count($sharingRuleInfo['ROLE']) > 0 ||
						count($sharingRuleInfo['GROUP']) > 0
						|| count($sharingRuleInfo['USR']) > 0)) {
					$tableName = $tableName.'_t'.$tabid;
				}elseif(!empty($scope)) {
					$tableName .= '_t'.$tabid;
				}
				if (empty($current_user_parent_role_seq)) {
					$user_role = $current_user->column_fields['roleid'];
					$user_role_info = getRoleInformation($user_role);
					$current_user_parent_role_seq = $user_role_info[$user_role][1];
				}
				if (empty($current_user_groups)) {
					$userGroupFocus = new GetUserGroups();
					$userGroupFocus->getAllUserGroups($current_user->id);
					$current_user_groups = $userGroupFocus->user_groups;
				}
				$this->setupTemporaryTable($tableName, $tabid, $current_user, $current_user_parent_role_seq, $current_user_groups);
				if($adb->isMssql()) $tableName = $adb->datadict->changeTableName($tableName);	//crmv@60402
				$result = $adb->pquery("select id from $tableName where id = ?",array($smownerid));
				if ($result && $adb->num_rows($result) > 0) {
					return 'yes';
				}
			} else {
				if ($smownerid != $current_user->id) {
					$tutables = TmpUserTables::getInstance();
					$tumtables = TmpUserModTables::getInstance();
					if ($tutables->hasSubUser($current_user->id, $smownerid) || $tumtables->hasSubUser('Messages', $current_user->id, $smownerid)) {
						return 'yes';
					}
				} else {
					return 'yes';
				}
			}
			// crmv@63349e

			$defOrgSharingPermission = getAllDefaultSharingAction();
			if ($defOrgSharingPermission[$tabid] == 0) {
				$rm = RelationManager::getInstance();
				$relIds = $rm->getRelatedIds($module,$record_id);
				foreach($relIds as $id) {
					$m = getSalesEntityType($id);
					if (isPermitted($m, 'DetailView', $id) == 'yes' && in_array($actionname,array('DetailView','Download','DownloadAttachments','Print','PrintHeader'))) {	//crmv@61173 crmv@66929 crmv@89037
						//echo 'ereditato '.$defOrgSharingPermission[$tabid];
						return 'yes';
					}
				}
				//crmv@56829
				$result = $adb->pquery("select id from {$table_prefix}_messages_recipients where messagesid = ?",array($record_id));
				if ($result && $adb->num_rows($result) > 0) {
					while($row=$adb->fetchByAssoc($result)) {
						$id = $row['id'];
						$m = getSalesEntityType($id);
						if (isPermitted($m, 'DetailView', $id) == 'yes') return 'yes';
					}
				}
				//crmv@56829e
			}
			
			return 'no';
		}
	}
	//crmv@47243e	crmv@61173e

	/*
	 * $params : array width column_fields of message
	 * ex. $params = array('subject'=>'Test','description'=>'test message','mto'=>'to@domain.com','mfrom'=>'from@domain.org',...);
	 * NB. you can also relate message to records by $params['parent_id'] (permitted formats: 12, 3x12, 3x12|3x14, 12@200|14@202)
	 *
	 * TODO : gestire l'invio di allegati passando in send_mail_attachment una stringa o un array di percorsi di file da inviare
	 */
	function send($params,$append=true) {
		$mail_tmp = (!empty($params['mail_tmp']) ? $params['mail_tmp'] : '');
		$mail_status = send_mail(
			'Emails',
			$params['mto'],
			(!empty($params['mfrom_n']) ? $params['mfrom_n'] : $params['mfrom']),
			$params['mfrom'],
			$params['subject'],
			$params['description'],
			$params['mcc'],
			$params['mbcc'],
			(!empty($params['send_mail_attachment']) ? $params['send_mail_attachment'] : 'all'),
			(!empty($params['send_mail_emailid']) ? $params['send_mail_emailid'] : 0),
			(!empty($params['send_mail_logo']) ? $params['send_mail_logo'] : ''),
			(!empty($params['send_mail_newsletter_params']) ? $params['send_mail_newsletter_params'] : ''),
			$mail_tmp,
			(!empty($params['send_mail_messageid']) ? $params['send_mail_messageid'] : ''),
			(!empty($params['send_mail_message_mode']) ? $params['send_mail_message_mode'] : '')
		);
		if ($append) {
			$append_status = false;

			$mainAccount = $this->getMainUserAccount();
			$account = (!empty($params['account']) ? $params['account'] : $mainAccount['id']);

			if ($mail_status == 1 && !empty($account)) {
				try {
					$append_status = append_mail(
						$mail_tmp,
						$account,
						$params['parent_id'],
						$params['mto'],
						(!empty($params['mfrom_n']) ? $params['mfrom_n'] : $params['mfrom']),
						$params['mfrom'],
						$params['subject'],
						$params['description'],
						$params['mcc'],
						$params['mbcc']
					);
				} catch (Exception $e) {
					$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
					//echo $e->getMessage()."\n";
				}
			}
			if ($append_status === false) {
				$focus = CRMentity::getInstance('Messages');
				$focus->saveCacheLink($params);
			}
		}
		return $mail_status;
	}

	function setRecipients($messagesid,$recipientids) {
		global $adb, $table_prefix;
		if (!is_array($recipientids)) {
			$recipientids = explode('|',$recipientids);
		}
		$recipientids = array_filter($recipientids);
		if (!empty($recipientids)) {
			foreach ($recipientids as $relid) {
				list($elid, $fieldid) = explode('@', $relid, 2);
				// check existence
				$r = $adb->pquery("select messagesid from {$table_prefix}_messages_recipients where messagesid = ? and id = ? and fieldid = ?",array($messagesid,$elid,$fieldid));
				if ($r && $adb->num_rows($r) == 0) {
					$adb->pquery("insert into {$table_prefix}_messages_recipients (messagesid,id,fieldid) values (?,?,?)",array($messagesid,$elid,$fieldid));
				}
			}
		}
	}

	function getRecipients($format='array') {
		global $adb, $table_prefix;
		$recipientids = array();
		$result = $adb->pquery("select id, fieldid from {$table_prefix}_messages_recipients where messagesid = ?",array($this->id));
		if($result && $adb->num_rows($result)) {
			while($row=$adb->fetchByAssoc($result)) {
				$recipientids[] = $row['id'].'@'.$row['fieldid'];
			}
		}
		if ($format == 'string') {
			return implode('|',$recipientids);
		} else {
			return $recipientids;
		}
	}

	function setSendMode($messagesid,$send_mode) {
		global $adb, $table_prefix;
		$adb->pquery("update {$table_prefix}_messages set send_mode = ? where messagesid = ?",array($send_mode,$messagesid));
	}

	function setVisibility($messagesid,$visibility) {
		global $adb, $table_prefix;
		$adb->pquery("update {$table_prefix}_messages set mvisibility = ? where messagesid = ?",array($visibility,$messagesid));
	}

	function checkThreadFlag($flag,$id,$thread) {
		//TODO do a unique query and cache values
		global $current_account, $current_folder, $current_user, $adb, $table_prefix;
		if ($flag == 'unseen') {
			$condition = 'AND seen = 0';
		} elseif ($flag == 'flagged') {
			$condition = 'AND flagged = 1';
		}
		$query = "select messagesid
				from {$table_prefix}_messages
				inner join {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid
				where deleted = 0 ".$condition;
		$children = $this->getChildren($thread,'',false,'distinct messageSon.messagehash');
		$query .= " and {$this->relation_table_id} in (".generateQuestionMarks($children).")";
		$params = array($children);
		if ($current_account != 'all') {
			$query .= " and account = ?";
			$params[] = $current_account;
		}
		$query .= " and folder = ? and smownerid = ?";
		$params[] = array($current_folder,$current_user->id);
		$result = $adb->pquery($query,$params);
		if ($result && $adb->num_rows($result) > 0) {
			return true;
		}
		return false;
	}

	//crmv@44037
	function getAccountSignature($id) {
		$account = $this->getUserAccounts('',$id);
		$return = $account[0]['signature'];
		$return = str_replace("\r",'',$return);
		$return = str_replace("\n",'',$return);
		return $return;
	}
	//crmv@44037e
	
	//crmv@3086m
	function relatedlist_preview_link($module, $entity_id, $current_module, $header, $relation_id) {
		return null;
	}
	//crmv@3086me
	
	//crmv@48693
	function getAdvancedSearchOptionString($old_mode=false,&$controller,&$queryGenerator) {
		$module = $queryGenerator->getModule();
		$meta = $queryGenerator->getMeta($module);
		$moduleFields = $meta->getModuleFields();
		$i =0;
		foreach ($moduleFields as $fieldName=>$field) {
			if(!in_array($field->getPresence(), array('0','2'))){
				continue;
			}
			if(!in_array($fieldName, array('subject','description','mdate','seen'))){
				continue;
			}
			if($field->getFieldDataType() == 'reference' || $field->getFieldDataType() == 'owner') {
				$typeOfData = 'V';
			} else if($field->getFieldDataType() == 'boolean') {
				$typeOfData = 'C';
			} else {
				$typeOfData = $field->getTypeOfData();
				$typeOfData = explode("~",$typeOfData);
				$typeOfData = $typeOfData[0];
			}
			$label = getTranslatedString($field->getFieldLabelKey(), $module);
			if(empty($label)) {
				$label = $field->getFieldLabelKey();
			}
			$selected = '';
			if($i++ == 0) {
				$selected = "selected";
			}
			// place option in array for sorting later
			if ($old_mode){
				$tableName = $field->getTableName();
				$columnName = $field->getColumnName();
				$OPTION_SET[$fieldName] = "<option value=\'$tableName.$columnName::::$typeOfData\' $selected>$label</option>";
			}
			else
				$OPTION_SET[$fieldName] = "<option value=\'$fieldName::::$typeOfData\' $selected>$label</option>";
		}
		if (!is_array($OPTION_SET)) return '';
		
		$options = array(
			"<option value=\'senders::::V\'>".getTranslatedString('Senders','Messages')."</option>",
			"<option value=\'recipients::::V\'>".getTranslatedString('Recipients','Messages')."</option>",
			$OPTION_SET['subject'],
			$OPTION_SET['description'],
			$OPTION_SET['mdate'],
			$OPTION_SET['seen'],
			// TODO	"<option value=\'links::::C\'>".getTranslatedString('LBL_FLAG_LINK','Messages')."</option>",
		);
		return implode('',$options);
	}
	
	function addUserSearchConditions($input,&$queryGenerator) {
		global $log,$default_charset;
		if($input['searchtype']=='advance') {
			if(empty($input['search_cnt'])) {
				return ;
			}
			$noOfConditions = vtlib_purify($input['search_cnt']);
			if($input['matchtype'] == 'all') {
				$matchType = $queryGenerator::$AND;
			} else {
				$matchType = $queryGenerator::$OR;
			}
			if($queryGenerator->getconditionInstanceCount() > 0) {
				$queryGenerator->startGroup($queryGenerator::$AND);
			} else {
				$queryGenerator->startGroup('');
			}
			for($i=0; $i<$noOfConditions; $i++) {
				$fieldInfo = 'Fields'.$i;
				$condition = 'Condition'.$i;
				$value = 'Srch_value'.$i;

				list($fieldName,$typeOfData) = explode("::::",str_replace('\'','',
						stripslashes($input[$fieldInfo])));
				$moduleFields = $queryGenerator->getModuleFields();
				$field = $moduleFields[$fieldName];
				
				if (in_array($fieldName,array('senders','recipients'))) {
					$whereFields = $queryGenerator->getWhereFields();
					if(($i-1) >= 0 && !empty($whereFields)) {
						$queryGenerator->addConditionGlue($matchType);
					}
					$operator = str_replace('\'','',stripslashes($input[$condition]));
					$searchValue = $input[$value];
					$searchValue = urldecode($searchValue); //crmv@60585
					$searchValue = function_exists(iconv) ? @iconv("UTF-8",$default_charset,
							$searchValue) : $searchValue;
					if (in_array($operator,array('n','k'))) {
						$intertnalGlue = $queryGenerator::$AND;
					} elseif (in_array($operator,array('e','s','ew','c'))) {
						$intertnalGlue = $queryGenerator::$OR;
					}
					$queryGenerator->startGroup('');
					if ($fieldName == 'senders') {
						$queryGenerator->addCondition('mfrom', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mfrom_n', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mfrom_f', $searchValue, $operator);
					} elseif ($fieldName == 'recipients') {
						$queryGenerator->addCondition('mto', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mto_n', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mto_f', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mcc', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mcc_n', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mcc_f', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mbcc', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mbcc_n', $searchValue, $operator);
						$queryGenerator->addConditionGlue($intertnalGlue);
						$queryGenerator->addCondition('mbcc_f', $searchValue, $operator);
					}
					$queryGenerator->endGroup();
				/* TODO
				} elseif ($fieldName == 'links') {
					$operator = str_replace('\'','',stripslashes($input[$condition]));
					$searchValue = $input[$value];
					$searchValue = urldecode($searchValue); //crmv@60585
					$searchValue = function_exists(iconv) ? @iconv("UTF-8",$default_charset,
							$searchValue) : $searchValue;
					$subselectCondition = '';
					if (($operator == 'e' && $searchValue == 'Yes') || ($operator == 'n' && $searchValue == 'No')) {
						$subselectCondition = 'in';
					} elseif (($operator == 'n' && $searchValue == 'Yes') || ($operator == 'e' && $searchValue == 'No')) {
						$subselectCondition = 'not in';
					}
					if (!empty($subselectCondition)) {
						$whereFields = $queryGenerator->getWhereFields();
						if(($i-1) >= 0 && !empty($whereFields)) {
							$queryGenerator->addConditionGlue($matchType);
						}
						global $table_prefix;
						$sql = "{$table_prefix}_messages.messagehash $subselectCondition (select messagehash from {$table_prefix}_messagesrel)";
						$queryGenerator->appendToWhereClause($sql);
					}
				*/
				} elseif ($fieldName == 'mdate') {
					if (!$field)
						continue;
					$type = $field->getFieldDataType();
					$whereFields = $queryGenerator->getWhereFields();
					if(($i-1) >= 0 && !empty($whereFields)) {
						$queryGenerator->addConditionGlue($matchType);
					}
					$operator = str_replace('\'','',stripslashes($input[$condition]));
					if (in_array($operator,array('custom','yesterday','today','lastweek','thisweek','lastmonth','thismonth','last60days','last90days'))) {
						$searchValue = urldecode($input[$value]);
						$searchValue = function_exists(iconv) ? @iconv("UTF-8",$default_charset,
							$searchValue) : $searchValue;
						list($start,$end) = explode('|##|',$searchValue);
						if (strlen($start) == 10) $start .= ' 00:00:00';
						if (strlen($end) == 10) $end .= ' 23:59:59';
						$queryGenerator->startGroup('');
						$queryGenerator->addCondition('mdate', $start, 'h');
						$queryGenerator->addConditionGlue($queryGenerator::$AND);
						$queryGenerator->addCondition('mdate', $end, 'm');
						$queryGenerator->endGroup();
					} else {
						$searchValue = $input[$value];
						$searchValue = urldecode($searchValue); //crmv@60585
						$searchValue = function_exists(iconv) ? @iconv("UTF-8",$default_charset,
								$searchValue) : $searchValue;
						$queryGenerator->addCondition($fieldName, $searchValue, $operator);
					}
				} else {
					if (!$field)
						continue;
					$type = $field->getFieldDataType();
					$whereFields = $queryGenerator->getWhereFields();
					if(($i-1) >= 0 && !empty($whereFields)) {
						$queryGenerator->addConditionGlue($matchType);
					}
					$operator = str_replace('\'','',stripslashes($input[$condition]));
					$searchValue = $input[$value];
					$searchValue = urldecode($searchValue); //crmv@60585
					$searchValue = function_exists(iconv) ? @iconv("UTF-8",$default_charset,
							$searchValue) : $searchValue;
					$queryGenerator->addCondition($fieldName, $searchValue, $operator);
				}
			}
			$queryGenerator->endGroup();
		} else {
			return 'continue';
		}
	}
	
	function getAdvCriteriaJS() {
		
		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$yesterday  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));

		$currentmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
		$currentmonth1 = date("Y-m-t");
		$lastmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
		$lastmonth1 = date("Y-m-t", strtotime("-1 Month"));
		$nextmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")+1, "01",   date("Y")));
		$nextmonth1 = date("Y-m-t", strtotime("+1 Month"));

		$lastweek0 = date("Y-m-d",strtotime("-2 week Sunday"));
		$lastweek1 = date("Y-m-d",strtotime("-1 week Saturday"));

		$thisweek0 = date("Y-m-d",strtotime("-1 week Sunday"));
		$thisweek1 = date("Y-m-d",strtotime("this Saturday"));

		$nextweek0 = date("Y-m-d",strtotime("this Sunday"));
		$nextweek1 = date("Y-m-d",strtotime("+1 week Saturday"));

		$next7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+6, date("Y")));
		$next30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+29, date("Y")));
		$next60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+59, date("Y")));
		$next90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+89, date("Y")));
		$next120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+119, date("Y")));

		$last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		$last30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-29, date("Y")));
		$last60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-59, date("Y")));
		$last90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-89, date("Y")));
		$last120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-119, date("Y")));

		$currentFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")));
		$currentFY1 = date("Y-m-t",mktime(0, 0, 0, "12", date("d"),   date("Y")));
		$lastFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")-1));
		$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")-1));

		$nextFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")+1));
		$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")+1));

		if(date("m") <= 3)
		{
			$cFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")-1));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")-1));
		}
		else if(date("m") > 3 and date("m") <= 6)
		{
			$pFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$nFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
		}
		else if(date("m") > 6 and date("m") <= 9)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "04","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "06","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
		}
		else if(date("m") > 9 and date("m") <= 12)
		{
			$nFq = date("Y-m-d",mktime(0, 0, 0, "01","01",date("Y")+1));
			$nFq1 = date("Y-m-d",mktime(0, 0, 0, "03","31",date("Y")+1));
			$pFq = date("Y-m-d",mktime(0, 0, 0, "07","01",date("Y")));
			$pFq1 = date("Y-m-d",mktime(0, 0, 0, "09","30",date("Y")));
			$cFq = date("Y-m-d",mktime(0, 0, 0, "10","01",date("Y")));
			$cFq1 = date("Y-m-d",mktime(0, 0, 0, "12","31",date("Y")));
		}
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		$sjsStr = '<script language="JavaScript" type="text/javaScript">
			var js_date_format = "'.$date_format.'";
			function showADvSearchDateRange(index, type)
			{
				if (type!="custom")
				{
					document.advSearch.elements["startdate"+index].readOnly=true;
					document.advSearch.elements["enddate"+index].readOnly=true;
					getObj("jscal_trigger_date_start"+index).style.visibility="hidden";
					getObj("jscal_trigger_date_end"+index).style.visibility="hidden";
				}
				else
				{
					document.advSearch.elements["startdate"+index].readOnly=false;
					document.advSearch.elements["enddate"+index].readOnly=false;
					getObj("jscal_trigger_date_start"+index).style.visibility="visible";
					getObj("jscal_trigger_date_end"+index).style.visibility="visible";
				}
				if( type == "today" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($today).'";
				}
				else if( type == "yesterday" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($yesterday).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($yesterday).'";
				}
				else if( type == "tomorrow" )
				{

					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($tomorrow).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($tomorrow).'";
				}
				else if( type == "thisweek" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($thisweek0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($thisweek1).'";
				}
				else if( type == "lastweek" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($lastweek0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($lastweek1).'";
				}
				else if( type == "nextweek" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($nextweek0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($nextweek1).'";
				}
				else if( type == "thismonth" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($currentmonth0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($currentmonth1).'";
				}
				else if( type == "lastmonth" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($lastmonth0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($lastmonth1).'";
				}
				else if( type == "nextmonth" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($nextmonth0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($nextmonth1).'";
				}
				else if( type == "next7days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($next7days).'";
				}
				else if( type == "next30days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($next30days).'";
				}
				else if( type == "next60days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($next60days).'";
				}
				else if( type == "next90days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($next90days).'";
				}
				else if( type == "next120days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($today).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($next120days).'";
				}
				else if( type == "last7days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($last7days).'";
					document.advSearch.elements["enddate"+index].value =  "'.getDisplayDate($today).'";
				}
				else if( type == "last30days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($last30days).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($today).'";
				}
				else if( type == "last60days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($last60days).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($today).'";
				}
				else if( type == "last90days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($last90days).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($today).'";
				}
				else if( type == "last120days" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($last120days).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($today).'";
				}
				else if( type == "thisfy" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($currentFY0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($currentFY1).'";
				}
				else if( type == "prevfy" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($lastFY0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($lastFY1).'";
				}
				else if( type == "nextfy" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($nextFY0).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($nextFY1).'";
				}
				else if( type == "nextfq" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($nFq).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($nFq1).'";
				}
				else if( type == "prevfq" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($pFq).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($pFq1).'";
				}
				else if( type == "thisfq" )
				{
					document.advSearch.elements["startdate"+index].value = "'.getDisplayDate($cFq).'";
					document.advSearch.elements["enddate"+index].value = "'.getDisplayDate($cFq1).'";
				}
				else
				{
					document.advSearch.elements["startdate"+index].value = "";
					document.advSearch.elements["enddate"+index].value = "";
				}
				setAdvSearchIntervalDateValue(index);
			}
		</script>';
		return $sjsStr;
	}
	//crmv@48693e
	
	//crmv@63475
	function saveAllDocuments($record) {
		global $adb, $table_prefix;
		$result = $adb->pquery("SELECT * FROM {$table_prefix}_messages_attach WHERE messagesid = ? AND document IS NULL AND contentmethod IS NULL",array($record)); // crmv@68357
		if ($result && $adb->num_rows($result) > 0) {
			while($row=$adb->fetchByAssoc($result)) {
				$this->saveDocument($record,$row['contentid']);
			}
		}
	}
	function saveDocument($record,$contentid,$linkto=null,$linkto_module=null,$content_part=null,$decode_attachment=true) {	//crmv@84807	crmv@86304

		global $adb, $table_prefix, $root_directory, $currentModule;
		
		// If contentid has been already converted in Document we use the existing Document
		$documentid = '';
		$result = $adb->pquery("SELECT {$table_prefix}_messages_attach.document FROM {$table_prefix}_messages_attach
								INNER JOIN {$table_prefix}_crmentity ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages_attach.document
								WHERE deleted = 0 AND {$table_prefix}_messages_attach.messagesid = ? AND {$table_prefix}_messages_attach.contentid = ?",
								array($record,$contentid));
		if ($result && $adb->num_rows($result) > 0) {
			$documentid = $adb->query_result($result,0,'document');
		}
		if (empty($documentid)) {
			$this->retrieve_entity_info($record,$currentModule);
			$userid = $this->column_fields['assigned_user_id'];
			//crmv@84807
			if (empty($content_part)) {
				$uid = $this->column_fields['xuid'];
			
				$this->setAccount($this->column_fields['account']);
				$this->getZendMailStorageImap($userid);
				$this->selectFolder($this->column_fields['folder']);
			
				$messageId = self::$mail->getNumberByUniqueId($uid);
				$message = self::$mail->getMessage($messageId);
				$parts = $this->getMessageContentParts($message,$id,true);	//crmv@59492
				$content_part = $parts['other'][$contentid];
			}
			if (!empty($content_part)) {
				$parameters = $content_part['parameters'];
				//crmv@86304
				// crmv@111124
				$FS = FileStorage::getInstance();
				$filename = $FS->sanitizeFilename($parameters['name']);
				// crmv@111124e
				$current_id = $adb->getUniqueID($table_prefix."_crmentity");
				$date_var = date('Y-m-d H:i:s');
				$upload_file_path = decideFilePath();
				// crmv@105191
				$destPath = $root_directory.$upload_file_path.$current_id."_".$filename;
				if (!empty($content_part['file'])) {	// path of existing file
					copy($content_part['file'], $destPath);
				} else {								// content of file
					$str = $content_part['content'];
					if ($decode_attachment) $str = $this->decodeAttachment($str,$parameters['encoding'],$parameters['charset']);
					
					$fp = fopen($destPath, 'wb');
					fwrite($fp,$str,strlen($str));
					fclose ($fp);
				}
				//crmv@86304e
				//crmv@84807e
		
				$sql1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) values(?,?,?,?,?,?)";
				$params1 = array($current_id, $userid, $userid, "Documents Attachment", $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
				$adb->pquery($sql1, $params1);
		
				$sql2 = "insert into ".$table_prefix."_attachments(attachmentsid, name, type, path) values(?,?,?,?)";
				$params2 = array($current_id, $filename, $parameters['contenttype'], $upload_file_path);
				$adb->pquery($sql2, $params2);
		
				// Create document record
				//crmv@86304
				$resFolder = $adb->pquery("select folderid from {$table_prefix}_crmentityfolder where foldername = ?", array('Message attachments'));
				($resFolder && $adb->num_rows($resFolder) > 0) ? $folderid = $adb->query_result($resFolder,0,'folderid') : $folderid = 1;
				//crmv@86304e
				$document = CRMEntity::getInstance('Documents');
				$document->column_fields['notes_title']      = $filename;
				$document->column_fields['filename']         = $filename;
				$document->column_fields['filestatus']       = 1;
				$document->column_fields['filelocationtype'] = 'I';
				$document->column_fields['folderid']         = $folderid;	//crmv@86304
				$document->column_fields['assigned_user_id'] = $userid;
				$document->column_fields['filesize'] = filesize($destPath);
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				$document->column_fields['filetype'] = finfo_file($finfo, $destPath);
				finfo_close($finfo);
				// crmv@105191e

				if (method_exists($document,'autoSetBUMC')) $document->autoSetBUMC('Documents',$current_user->id);	//crmv@93302
				$document->save('Documents');
				$documentid = $document->id;
		
				// Link file attached to document
				$adb->pquery("insert into ".$table_prefix."_seattachmentsrel(crmid, attachmentsid) values(?,?)",Array($documentid, $current_id));
		
				// Link documentid to the real attachment for faster next relations
				$adb->pquery("update {$table_prefix}_messages_attach set document = ? where messagesid = ? and contentid = ?",array($documentid, $record, $contentid));
			}
		}
		// crmv@42752 crmv@110370
		if (!empty($linkto) && !empty($document)) {
			$ids = array();
			// Split the string of ids
			$ids = array_filter(explode(",", trim($linkto,",")));
			// Link document to linkto
			$document->save_related_module('Documents', $documentid, $linkto_module, $ids);
		}
		// Link document to message in any case
		$this->save_related_module($currentModule, $record, 'Documents', $documentid);
		// crmv@42752e crmv@110370e
	}
	//crmv@63475e
	
	//crmv@62340
	public function parseEmlFile($filepath){
		$zend_mail_storage_message = new \Zend\Mail\Storage\Message(array(
			'file' => $filepath
		));
		
		return $zend_mail_storage_message;
	}
	
	//crmv@84807
	public function saveEmlAttachments($record,$other){
		global $adb;
		if (!empty($other)){
			foreach($other as $contentid => $tmp_files){
				$this->saveDocument($record,$contentid,null,null,$other[$contentid]);
			}
		}
		return true;
	}
	//crmv@84807e
	
	public function isEML(){
		$messageid = $this->column_fields['messageid'];
		$compare_str = '_eml';
		$cnt = -1 * abs(strlen($compare_str));
		if(substr($messageid,$cnt) == $compare_str){
			return true;
		}
		else{
			return false;
		}
		
	}
	//crmv@62340e

	// crmv@62340 crmv@84807 crmv@88981 crmv@90941
	public function parseEML($contentid, &$messagesid, &$error=null, $str=null) {
		global $adb, $table_prefix, $default_charset;
		
		$userid = $this->column_fields['assigned_user_id'];
		$uid = $this->column_fields['xuid'];
		$accountid = $this->column_fields['account'];
		$folder = $this->column_fields['folder'];

		if (empty($str)) {
			// if the message is already scanned by mailconverter
			if ($this->column_fields['mtype'] == 'Link') {
				$new_messageid = $this->column_fields['messageid']."_{$contentid}_eml";
				$result1 = $adb->pquery("SELECT 
										messagesid 
										FROM {$table_prefix}_messages 
										INNER JOIN {$table_prefix}_crmentity 
											ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid 
											AND deleted = 0 
										WHERE messageid = ?", array($new_messageid));
				if ($result1 && $adb->num_rows($result1) > 0) {
					$messagesid = $adb->query_result_no_html($result1,0,'messagesid');
					return true;
				} else {
					return false;
				}
			}
			
			$this->setAccount($accountid);
			$this->getZendMailStorageImap($userid);
			$this->selectFolder($folder);
		
			$messageId = $this->getMailResource()->getNumberByUniqueId($uid);
			$message = $this->getMailResource()->getMessage($messageId);
			$parts = $this->getMessageContentParts($message,null,true);
		
			if (!empty($parts['other'][$contentid])) {
				$content = $parts['other'][$contentid];
				$str = $content['content'];
				$str = $this->decodeAttachment($str,$content['parameters']['encoding'],$content['parameters']['charset']);
			}
		} else {
			$this->loadZendFramework();
		}
		
		if (!empty($str)) {
			$savepath = "./cache/emlattach_{$this->id}_{$contentid}.eml";
			$r = @file_put_contents($savepath,$str);
			if (!$r) {
				$error = 'Unable to save the temporary file';
				return false;
			}
			
			try {
				$eml_message = $this->parseEmlFile($savepath);
			} catch (Exception $e) {
				$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
				@unlink($savepath);
				$error = 'Malformed eml attachment';
				return false;
			}
			unlink($savepath);
			
			$headers = $this->getMessageHeader($eml_message);
			$data = $this->getMessageData($eml_message,$headers['Messageid'], true);
			
			if ($this->column_fields['mtype'] == 'Link') {
				$new_messageid = $this->column_fields['messageid']."_{$contentid}_eml";
			} else {
				$new_messageid = $headers['Messageid']."_{$contentid}_eml";
			}
			
			//check for mail already "scanned"
			$result1 = $adb->pquery("SELECT 
									messagesid 
									FROM {$table_prefix}_messages 
									INNER JOIN {$table_prefix}_crmentity 
										ON {$table_prefix}_crmentity.crmid = {$table_prefix}_messages.messagesid 
										AND deleted = 0 
									WHERE messageid = ?", array($new_messageid));
			if ($result1 && $adb->num_rows($result1) > 0) {
				$messagesid = $adb->query_result_no_html($result1,0,'messagesid');
				return true;
			} else{
				$date = $this->imap2DbDate($data['header']['Date']);	//crmv@49480

				$body = '';
				if (isset($data['text/html'])) {
					$body = $data['text/html'];
					$body = str_replace('&lt;','&amp;lt;',$body);
					$body = str_replace('&gt;','&amp;gt;',$body);
				} elseif (isset($data['text/plain'])) {
					$body = nl2br(htmlentities($data['text/plain'], ENT_COMPAT, $default_charset));
				}
				$body = preg_replace('/[\xF0-\xF7].../s', '', $body);	//crmv@65555
				
				$column_fields = array(
					'subject'=>$data['header']['Subject'],
					'description'=>$body,
					'mdate'=>$date,
					'mfrom'=>$data['header']['From']['email'],
					'mfrom_n'=>$data['header']['From']['name'],
					'mfrom_f'=>$data['header']['From']['full'],
					'mto'=>$data['header']['To']['email'],
					'mto_n'=>$data['header']['To']['name'],
					'mto_f'=>$data['header']['To']['full'],
					'mcc'=>$data['header']['Cc']['email'],
					'mcc_n'=>$data['header']['Cc']['name'],
					'mcc_f'=>$data['header']['Cc']['full'],
					'mbcc'=>$data['header']['Bcc']['email'],
					'mbcc_n'=>$data['header']['Bcc']['name'],
					'mbcc_f'=>$data['header']['Bcc']['full'],
					'mreplyto'=>$data['header']['ReplyTo']['email'],
					'mreplyto_n'=>$data['header']['ReplyTo']['name'],
					'mreplyto_f'=>$data['header']['ReplyTo']['full'],
					'messageid'=>$new_messageid,
					'in_reply_to'=>$data['header']['In-Reply-To'],
					'xuid'=>0,
					'account'=>$accountid,
					'folder'=>'',
					'assigned_user_id'=>$userid,
					'mtype'=>'Link',
					'other'=>$data['other'],
					'parent_id'=>"",
					'mvisibility'=>'Public',
				);

				$newfocus = CRMentity::getInstance('Messages');
				$messagesid = $newfocus->saveCacheLink($column_fields);
				if(!empty($messagesid)){
					$newfocus->saveEmlAttachments($messagesid,$data['other']);
					return true;
				} else {
					$error = 'Unable to save the attachment';
					return false;
				}
			}
		}
		$error = 'Unknown error';
		return false;
	}
	// crmv@62340e crmv@84807e crmv@88981e crmv@90941e
	
	//crmv@65328
	function getAttachmentsSize($messageId) {
		global $adb, $table_prefix;
		$size = 0;
		$atts = $this->getAttachmentsInfo();
		if (!empty($atts)) {
			if (empty($atts[0]['parameters']['size'])) {
				if ($this->column_fields['mtype'] == 'Link') {
					$sql = "select t.* from {$table_prefix}_messages_attach a 
					inner join {$table_prefix}_seattachmentsrel s on s.crmid = a.document
					inner join {$table_prefix}_notes n on n.notesid = a.document
					inner join {$table_prefix}_attachments t on t.attachmentsid = s.attachmentsid
					inner join {$table_prefix}_crmentity e on e.crmid = t.attachmentsid
					where messagesid = ? and coalesce(a.document,'') <> '' and e.deleted=0";
					$params = Array($this->id);
					$res = $adb->pquery($sql,$params);
					if ($res && $adb->num_rows($res)>0) {
						while($row=$adb->fetchByAssoc($res)) {
							$filewithpath = $root_directory.$row['path'].$row['attachmentsid']."_".$row['name'];
							if (is_file($filewithpath)) {
								$size += filesize($filewithpath);
							}
						}
					}
				} else {
					$this->setAccount($this->column_fields['account']);
					$this->getZendMailStorageImap($this->column_fields['assigned_user_id']);
					$this->selectFolder($this->column_fields['folder']);
					try {
						$messageId = self::$mail->getNumberByUniqueId($this->column_fields['xuid']);
					} catch(Exception $e) {
						$this->logException($e,__FILE__,__LINE__,__METHOD__);	//crmv@90390
						if ($e->getMessage() == 'unique id not found') {
							return;
						}
					}
					$size = self::$mail->getSize($messageId);
				}
			} else {
				foreach($atts as $contentid => $att) {
					$size += (int)$att['parameters']['size'];
				}
			}
		}
		return $size;
	}
	//crmv@65328e
	
	//crmv@80250
	function isSupportedInlineFormat($filename) {
		$extension = substr(strrchr($filename, "."), 1);
		if(in_array(strtolower($extension),$this->inline_image_supported_extensions)){
			return true;
		}
		return false;
	}
	//crmv@80250e
	
	//crmv@91321
	function isConvertableFormat($filename) {
		$extension = substr(strrchr($filename, "."), 1);
		if(in_array(strtolower($extension),$this->inline_image_convertible_extensions)){
			return true;
		}
		return false;
	}
	//crmv@91321e

	//crmv@81766
	function convertInternalMailerLinks($description) {
		// trasforma in link al compositore interno gli indirizzi email
		$description = preg_replace("/(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)/i","\\1<a href=\"javascript:InternalMailer('\\2@\\3','','','','email_addy');\">\\2@\\3</a>",$description);
		// sostituisce mailto con il compositore interno
		$r = '`\<a([^>]+)href\=\"mailto\:([^">]+)\"([^>]*)\>(.*?)\<\/a\>`ism';
		if (preg_match_all($r, $description, $regs, PREG_SET_ORDER)) {
			foreach ($regs as $reg) {
				list($email,$params) = explode('?',$reg[2]);
				//TODO manage $params (subject, body, ecc.)
				$internal_mailto = "javascript:InternalMailer('{$email}','','','','email_addy');";
				$mailto_after = '<a'.$reg[1].'href="'.$internal_mailto.'"'.$reg[3].'>'.$reg[4].'</a>';
				$description = str_replace($reg[0], $mailto_after, $description);
			}
		}
		return $description;
	}
	//crmv@81766e

	//crmv@86304
	function internalAppendMessage($mail,$account,$parentid,$to,$from_name,$from_address,$subject,$description,$cc,$bcc,$send_mode) {
		$mreplyto = array();
		$mreplyto_n = array();
		if (!empty($mail->ReplyTo)) {
			foreach($mail->ReplyTo as $r) {
				$mreplyto[] = $r[0];
				$mreplyto_n[] = $r[1];
			}
		}
		if (!empty($mail->CustomHeader)) {
			foreach($mail->CustomHeader as $c) {
				if ($c[0] == 'In-Reply-To') $in_reply_to = trim($c[1]);
				elseif ($c[0] == 'References') $mreferences = trim($c[1]);
			}
		}
		$other = array();
		if (!empty($mail->attachment)) {
			foreach($mail->attachment as $a) {
				$content = $file = '';
				($a[5]) ? $content = $a[0] : $file = $a[0];
				$other[] = array(
					'parameters'=>array(
						'name'=>$a[2],
						'contenttype'=>$a[4],
						'contentdisposition'=>$a[6],
						'encoding'=>$a[3],
					),
					'content'=>$content,
					'file'=>$file,
				);
			}
		}
		$record = $this->saveCacheLink(array(
			'subject'=>$subject,
			'description'=>$description,
			'mfrom'=>$from_address,
			'mfrom_n'=>$from_name,
			'mfrom_f'=>"$from_name <$from_address>",
			'mto'=>(is_array($to)) ? implode(', ',$to) : $to,
			'mcc'=>(is_array($cc)) ? implode(', ',$cc) : $cc,
			'mbcc'=>(is_array($bcc)) ? implode(', ',$bcc) : $bcc,
			'mreplyto'=>$replyTo['emails'],
			'mreplyto_n'=>$replyTo['names'],
			'in_reply_to'=>$in_reply_to,
			'mreferences'=>$mreferences,
			'xmailer'=>null,
			'messageid'=>$mail->message_id,
			'send_mode'=>$send_mode,
			'other'=>$other,
			'parent_id'=>$_REQUEST['relation'],
			'recipients'=>$parentid,
			//crmv@80216
			'mtype'=>'Link',
			'account'=>$account,
			//crmv@80216e
		));
		if (!empty($record) && !empty($other)) {
			foreach($other as $contentid => $content){
				$this->saveDocument($record,$contentid,null,null,$content,false);
			}
		}
	}
	//crmv@86304e

	// crmv@91980
	function extractPhoneNumbers($description) {
		$allPhoneNumbers = array();
		if (empty($description)) return $allPhoneNumbers;
		
		if (preg_match_all('/href\s*=\s*[\'"](tel|sms):([%0-9 +.)(-]+)[\'"]/i', $description, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$type = strtolower(trim($match[1]));
				$num = trim(rawurldecode($match[2]));
				if (($type == 'tel' || $type == 'sms') && !empty($num)) {
					$allPhoneNumbers[] = array(
						'number' => $num,
						'type' => ($type == 'sms' ? 'mobile' : 'phone')
					);
				}
			}
		}
		return $allPhoneNumbers;
	}
	
	// retrieve them from the table
	function getPhoneNumbers($messagesid) {
		global $adb, $table_prefix;
		
		$list = array();
		$res = $adb->pquery("SELECT phone, type FROM {$table_prefix}_messages_ntel WHERE messagesid = ?", array($messagesid));
		if ($res && $adb->num_rows($res) > 0) {
			while ($row = $adb->fetchByASsoc($res,-1, false)) {
				$list[] = array('number' => $row['phone'], 'type' => $row['type']);
			}
		}
		return $list;
	}
	
	// save the numbers in the table
	function savePhoneNumbers($messagesid, $numbers) {
		global $adb, $table_prefix;
		
		$inserts = array();
		foreach ($numbers as $entry) {
			$inserts[] = array($messagesid, $entry['number'], $entry['type']);
		}
		
		if (count($inserts) > 0) {
			$adb->bulkInsert($table_prefix."_messages_ntel", array('messagesid', 'phone', 'type'), $inserts);
		}
		
	}
	
	function deletePhoneNumbers($messagesid) {
		global $adb, $table_prefix;
		
		$adb->pquery("DELETE FROM {$table_prefix}_messages_ntel WHERE messagesid = ?", array($messagesid));
	}
	// crmv@91980e

	//crmv@94282
	function get_navigation_values($list_query_count,$url_string,$currentModule,$type='',$forusers=false,$viewid = '') {
		return Zend_Json::encode(Array('nav_array'=>Array(),'rec_string'=>''));
	}
	//crmv@94282e

}