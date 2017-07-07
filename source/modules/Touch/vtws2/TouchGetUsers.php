<?php
/* crmv@31780 */
/* crmv@33311 */
/* crmv@49398 */

class TouchGetUsers extends TouchWSClass {

	protected $cacheLife = 21600;	// 6 hours

	public function clearCache() {
		global $touchCache;
		$touchCache->delete('users');
	}

	function process(&$request) {
		global $adb, $table_prefix, $touchUtils, $touchCache;

		$userid = intval($request['userid']);	// if set, retrieve only 1 user
		
		// use cache only when userid is not set
		if ($userid == 0) {
			$cachedUsers = $touchCache->get('users');
			if ($cachedUsers) return $this->success(array('users' => $cachedUsers, 'total'=>count($cachedUsers)));
		}

		// crmv@73256
		// get users
		$users = array();
		$res = $adb->pquery(
			"select
				id as userid,
				user_name,
				first_name,
				last_name,
				COALESCE(email1, email2) as email,
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
				u.deleted = 0 and u.status = ? ".($userid > 0 ? " and u.id = ?" : ""),
			array('Active', $userid)
		);
		// crmv@73256e
		
		if ($res) {
			$calendar = $touchUtils->getModuleInstance('Calendar');
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
				// crmv@104718
				$subusers = array();
				$subroles = getSubordinateRoleAndUsers($row['roleid']);
				if (is_array($subroles)) {
					foreach ($subroles as $roleid => $roleusers) {
						if (is_array($roleusers)) {
							$subusers = array_merge($subusers, array_keys($roleusers));
						}
					}
					$subusers = array_unique(array_map('intval', $subusers));
					// and remove myself
					$k = array_search(intval($row['userid']), $subusers);
					if ($k !== false) {
						unset($subusers[$k]);
						$subusers = array_values($subusers);
					}
				}
				$row['subusers'] = $subusers;
				// crmv@104718e
				$users[] = $row;
			}
			// ordina per nome completo
			//usort($users, create_function('$v1, $v2', 'return strcasecmp($v1["complete_name"], $v2["complete_name"]);'));
		}

		if ($userid == 0) {
			$touchCache->set('users', $users, $this->cacheLife);
		}
		return $this->success(array('users'=>$users, 'total'=>count($users)));
	}

}

