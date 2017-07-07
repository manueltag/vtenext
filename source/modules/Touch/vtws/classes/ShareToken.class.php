<?php

class TouchShareToken extends TouchWSClass {

	function process(&$request) {

		$CRMVUtils = CRMVUtils::getInstance();
		$CRMVUtils->shareTokenDuration = 30; // 30 seconds

		$module = $request['module'];
		$recordid = $request['recordid'];

		if ($module == 'Messages' && isset($request['contentid'])) {
			$contentid = intval($request['contentid']);
			$params = array('contentid'=>$contentid);
		}

		$token = $CRMVUtils->generateShareToken($module, $recordid, false, $params);

		return array('token' => $token );
	}

}

?>