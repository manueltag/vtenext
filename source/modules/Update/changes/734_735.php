<?php
global $adb, $table_prefix, $current_user;
$result = $adb->pquery("select id from {$table_prefix}_systems where server_type = ?",array('email_imap'));
if ($result && $adb->num_rows($result) > 0) {
	$account = $adb->query_result($result,0,'id');
	$result = $adb->query("select * from {$table_prefix}_mail_accounts");
	if ($result && $adb->num_rows($result) > 0) {
		$focus = CRMEntity::getInstance('Messages');
		require_once('include/utils/encryption.php');
		$de_crypt = new Encryption();
		while($row=$adb->fetchByAssoc($result)) {
			if (!empty($row['mail_username'])) {
				$current_user->id = $row['user_id'];
				$focus->saveAccount('',$account,$row['mail_username'],$de_crypt->decrypt($row['mail_password']),1,$row['mail_username']);
			}
		}
	}
}
$data_dir = 'modules/Messages/src/squirrelmail_old_conf/data';
if (file_exists($data_dir)) {
	$result = $adb->query("SELECT user_id, mail_username FROM {$table_prefix}_mail_accounts INNER JOIN {$table_prefix}_users ON {$table_prefix}_mail_accounts.user_id = {$table_prefix}_users.id");
	if ($result && $adb->num_rows($result) > 0) {
		$focus = CRMEntity::getInstance('Messages');
		require_once('modules/Messages/src/Squirrelmail.php');
		while($row=$adb->fetchByAssoc($result)) {
			$filters = array();
			$mailfetch = array();
			$username = $row['mail_username'];
			if (file_exists("$data_dir/$username.pref")) {
				// Migrate Filters - start
				for ($i = 0; $fltr = Squirrelmail::getPref($data_dir, $username, 'filter' . $i); $i++) {
					$ary = explode(',', $fltr);
					$filters[$i]['where'] = $ary[0];
					$filters[$i]['what'] = str_replace('###COMMA###', ',', $ary[1]);
					$filters[$i]['folder'] = $ary[2];
				}
				// Migrate Filters - end
				// Migrate Pop3 - start
				$mailfetch_server_number = Squirrelmail::getPref($data_dir, $username, 'mailfetch_server_number', 0);
				$mailfetch_cypher = Squirrelmail::getPref( $data_dir, $username, 'mailfetch_cypher' );
				if ($mailfetch_server_number<1) {
					$mailfetch_server_number=0;
				}
				for ($i=0;$i<$mailfetch_server_number;$i++) {
					$mailfetch[$i]['server'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_server_$i");
					$mailfetch[$i]['port'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_port_$i");
					$mailfetch[$i]['user'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_user_$i");
					$mailfetch[$i]['pass'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_pass_$i");
					if( $mailfetch_cypher == 'on' ) $mailfetch[$i]['pass'] = Squirrelmail::decrypt($mailfetch_pass_[$i]);
					$mailfetch[$i]['subfolder'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_subfolder_$i");
					$mailfetch[$i]['lmos'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_lmos_$i");
					if ($mailfetch[$i]['lmos'] == 'on') {
						$mailfetch[$i]['lmos'] = 1;
					} else {
						$mailfetch[$i]['lmos'] = 0;
					}
					$mailfetch[$i]['uidl'] = Squirrelmail::getPref($data_dir, $username, "mailfetch_uidl_$i");
				}
				// Migrate Pop3 - end
			}
			if (!empty($filters)) {
				global $current_user;
				$current_user->id = $row['user_id'];
				$where_list = array(
					'From'=>'from',
					'To'=>'to',
					'Cc'=>'cc',
					'To or Cc'=>'to_or_cc',
					'Subject'=>'subject',
					'Header'=>'body',
				);
				$account = $focus->getMainUserAccount();
				if (!empty($account)) {
					foreach($filters as $filter) {
						$focus->setFilter($account['id'],$where_list[$filter['where']],$filter['what'],$filter['folder']);
					}
				}
			}
			if (!empty($mailfetch)) {
				global $current_user;
				$current_user->id = $row['user_id'];
				$account = $focus->getMainUserAccount();
				foreach($mailfetch as $mf) {
					if (!empty($mf['uidl'])) {
						$focus->setPop3($mf['server'],$mf['port'],$mf['user'],$mf['pass'],'',$account['id'],$mf['subfolder'],$mf['lmos'],1,$mf['uidl']);
					}
				}
			}
		}
	}
}
?>