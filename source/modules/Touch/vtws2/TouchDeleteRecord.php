<?php

class TouchDeleteRecord extends TouchWSClass {

	public $validateModule = true;

	public function process(&$request) {
		global $current_user, $touchUtils;

		$module = $request['module'];
		$recordid = intval($request['record']);

		if ($recordid > 0 && $module != '') {
			// fix for stupid calendar - removed
			//if ($module == 'Calendar') $module = 'Events';
			$response = $touchUtils->wsRequest($current_user->id,'delete',
				array('id'=>vtws_getWebserviceEntityId($module, $recordid))
			);
			$record = $response['result'];
		}

		if ($response['success'] === true) {
			return $this->success();
		} elseif (!empty($response['error'])) {
			return $this->error($response['error']);
		} else {
			return $this->error('Unknown error');
		}
	}

}

