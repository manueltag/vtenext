<?php
/* crmv@31780 */
/* crmv@33311 */
/* crmv@49398 */

global $adb, $table_prefix, $login, $userId;
global $current_user;

if (!$login || !$userId) {
	echo 'Login Failed';
} else {

	// get users
	$users = array();
	$res = $adb->pquery(
		"select
			id as userid,
			user_name,
			first_name,
			last_name,
			ur.roleid,
			avatar,
			allow_generic_talks,
			receive_public_talks,
			start_hour,
			end_hour,
			cal_color
		from {$table_prefix}_users u
		inner join {$table_prefix}_user2role ur on ur.userid = u.id
		where
			u.deleted = 0 and u.status = ?",
		array('Active')
	);
	$calendar = CRMEntity::getInstance('Calendar');
	if ($res) {
		while ($row = $adb->fetchByAssoc($res)) {
			$row['complete_name'] = trim($row['first_name'].' '.$row['last_name']);
			$row['preferencies'] = array(
				// calendar start/end hours
				'calendar_start_hour' => $row['start_hour'],
				'calendar_end_hour' => $row['end_hour'],
				// calendar color
				'cal_color' => substr($row['cal_color'], 0, 6),
				// users available for calendar sharing
				'cal_users' => array_keys($calendar->getShownUserId($row['userid'])),
				'avatar' => $row['avatar'],
			);
			$users[] = $row;
		}
		// ordina per nome completo
		//usort($users, create_function('$v1, $v2', 'return strcasecmp($v1["complete_name"], $v2["complete_name"]);'));
	}

	echo Zend_Json::encode($users);
}
?>