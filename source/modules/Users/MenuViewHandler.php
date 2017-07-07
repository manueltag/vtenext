<?php
//crmv@22622
class MenuViewHandler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb, $current_user;

		// check irs a timcard we're saving.
		if (!($data->focus instanceof Users)) {
			return;
		}

		if($eventName == 'vtiger.entity.beforesave') {
			
			if ($data->getId() == $current_user->id) {
				$focus = $data->getData();
				if ($focus['menu_view'] == 'Large Menu') {
					setcookie('crmvWinMaxStatus','open');
				} elseif ($focus['menu_view'] == 'Small Menu') {
					setcookie('crmvWinMaxStatus','close');
				}
			}
		}
	}
}
//crmv@22622e
?>