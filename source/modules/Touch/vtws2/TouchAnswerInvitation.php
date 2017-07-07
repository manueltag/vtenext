<?php

class TouchAnswerInvitation extends TouchWSClass {
	
	public function process(&$request) {
		global $touchInst, $current_user;
		
		$recordid = intval($request['recordid']);
		$value = intval($request['participation']);
		
		$success = false;

		$from = 'users';
		$_REQUEST['partecipation'] = $value;
		$_REQUEST['activityid'] = $recordid;
		$_REQUEST['userid'] = $current_user->id;

		try {
			require('modules/Calendar/SavePartecipation.php');
			$success = true;
			$error = '';
		} catch (Exception $e) {
			$error = $e->getMessage();
			$success = false;
		}

		if ($success) {
			return $this->success(array('invitation_answer' => $value));
		} else {
			return $this->error($error);
		}
	}

}
