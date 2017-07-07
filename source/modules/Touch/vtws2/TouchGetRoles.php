<?php
/* retrieves roles  */

class TouchGetRoles extends TouchWSClass {

	function process(&$request) {
		global $adb, $table_prefix;
		
		$roles = array();
		$res = $adb->query(
			"select *
			from {$table_prefix}_role r
			inner join {$table_prefix}_role2profile rp on rp.roleid = r.roleid"
		);

		if ($res) {
			while ($row = $adb->fetchByAssoc($res, -1, false)) {
				$roles[] = $row;
			}
		}

		return $this->success(array('roles' => $roles, 'total'=>count($roles)));
	}
}

