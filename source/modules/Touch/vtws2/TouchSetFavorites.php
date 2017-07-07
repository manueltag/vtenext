<?php
/* crmv@34559 */

class TouchSetFavorites extends TouchWSClass {

	public function process(&$request) {
		global $current_user;

		require_once('modules/SDK/src/Favorites/Utils.php');

		$ids = array_map('intval', explode(':', $request['records']));
		foreach ($ids as $id) {
			setFavorite($current_user->id, $id);
		}

		return $this->success();
	}
}

