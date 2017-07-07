<?php
class UitypeRoleUtils {
	
	function getAllRoles() {
		require_once('modules/SDK/src/208/208Utils.php');
		$uitype208 = new EncryptedUitype();
		$roles = $uitype208->getAllRoles();
		return $roles;
	}
	
	function getRoleName($id) {
		static $roles = array();
		if (empty($roles)) {
			$tmp = $this->getAllRoles();
			foreach($tmp as $t) {
				$roles[$t['roleid']] = $t['rolename'];
			}
		}
		return $roles[$id];
	}
}