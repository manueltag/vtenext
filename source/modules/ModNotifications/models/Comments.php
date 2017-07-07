<?php
//crmv@start
class ModNotifications_CommentsModel {

	protected $data;
	static $ownerNamesCache = array();
	static $ownerPhotoCache = array();

	function __construct($datarow) {
		$this->data = $datarow;
	}

	function author() {
		if (in_array($this->data['mod_not_type'],array('Ticket portal replied','Ticket portal created','Calendar invitation answer yes contact','Calendar invitation answer no contact'))) {
			return $this->data['from_email_name'];
		}
		$authorid = $this->data['smcreatorid'];
		if(!isset(self::$ownerNamesCache[$authorid])) {
			self::$ownerNamesCache[$authorid] = trim(getUserFullName($authorid));
		}
		return self::$ownerNamesCache[$authorid];
	}

	function authorPhoto() {
		if (in_array($this->data['mod_not_type'],array('Ticket portal replied','Ticket portal created','Calendar invitation answer yes contact','Calendar invitation answer no contact'))) {
			return getPortalAvatar();
		}
		global $theme;
		$authorid = $this->data['smcreatorid'];
		if(!isset(self::$ownerPhotoCache[$authorid])) {
			self::$ownerPhotoCache[$authorid] = getUserAvatar($authorid);
		}
		return self::$ownerPhotoCache[$authorid];
	}

	function timestamp(){
		return getTranslatedString('LBL_DAY'.date('w',strtotime($this->data['createdtime'])),'Calendar').' '.getDisplayDate($this->data['createdtime']);
	}

	function timestampAgo(){
		if (in_array($this->data['createdtime'],array('','1970-01-01'))) {
			return '';
		}
		$difference = time() - strtotime($this->data['createdtime']);
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60", "60", "24", "7", "4.35", "12", "10");
		for($j = 0; isset($lengths[$j]) && $difference >= $lengths[$j]; $j++) {
			$difference /= $lengths[$j];
		}
		$difference = round($difference);
		if($difference != 1) {
			$periods[$j].= "s";
		}
		if ($difference == 0 &&  $periods[$j] == 'seconds') {
			$text = getTranslatedString('lbl_now','ModComments');
		} else {
			$period = $difference.' '.getTranslatedString('lbl_'.$periods[$j],'ModComments');
			$text = sprintf(getTranslatedString('LBL_AGO','ModComments'), $period);
		}
		return $text;
	}

	function content() {

		global $adb, $current_user,$table_prefix;

		$focus = CRMEntity::getInstance('ModNotifications');
		
		$html = $focus->translateNotificationType($this->data['mod_not_type'],'action');

		//crmv@31126
		if ($this->data['mod_not_type'] == 'Import Completed') {
			$html .= ' <a href="index.php?module='.$this->data['description'].'&action=index">'.$this->data['description'].'</a>';
		}
		//crmv@31126e

		if ($this->data['mod_not_type'] == 'Relation') {
			$parent_id = $this->data['description'];
			$parent_module = getSalesEntityType($parent_id);
			$entityType = getSingleModuleName($parent_module,$parent_id);
			$displayValueArray = getEntityName($parent_module, $parent_id);
			if(!empty($displayValueArray)){
				foreach($displayValueArray as $key=>$value){
					$displayValue = $value;
				}
			}
			$html .= " <a href='index.php?module=$parent_module&action=DetailView&record=$parent_id' title='$entityType' target='_parent'>$displayValue</a> ($entityType) ";
			$html .= getTranslatedString('LBL_TO','ModComments');
		}
		$html .= ' '.$this->relatedToString();

		if ($this->data['mod_not_type'] == 'ListView changed') {
			$html .= '&nbsp;:<br />';
			$changes = array_filter(explode(',',$this->data['description']));
			$html_changes = '';
			if (!empty($changes)) {
				//crmv@58625
				global $list_max_entries_per_page;
				$cnt_changes = 1;
				$show_other = '';
				if (count($changes) > $list_max_entries_per_page){
					$show_other = ", ...".getTranslatedString('LBL_OTHERS')." ".(count($changes)-$list_max_entries_per_page);
				}
				foreach($changes as $id) {
					$module = getSalesEntityType($id);	
					$displayValueArray = getEntityName($module,$id);
					if(!empty($displayValueArray)){
						foreach($displayValueArray as $key=>$value){
							$displayValue = $value;
						}
					}
					$html_changes[] = "<a href='index.php?module=$module&action=DetailView&record=$id' target='_parent'>$displayValue</a>";
					$cnt_changes++;
					if ($cnt_changes > $list_max_entries_per_page){
						break;
					}
				}
				$html .= implode(', ',$html_changes).$show_other;
				//crmv@58625 e
			}
		}

		if ($this->parent_module != '' && in_array($this->data['mod_not_type'],array('Changed followed record','Changed record'))) {

			$q = "SELECT * FROM ".$table_prefix."_changelog ch INNER JOIN ".$table_prefix."_crmentity c ON ch.changelogid = c.crmid
					WHERE c.deleted = 0 AND parent_id = ? AND user_name <> ? ORDER BY changelogid DESC ";
			$ress = $adb->pquery($q,array($this->parent_id, $current_user->user_name));
			$changelogid = $adb->query_result_no_html($ress,0,"changelogid");
			$description = $adb->query_result_no_html($ress,0,"description");
			$description_elements = Zend_Json::decode($description);
			$ChangeLogFocus = CRMEntity::getInstance('ChangeLog');

			$html .= '<br /><a style="text-decoration:none;" href="javascript:void(0);" onClick="ModNotificationsCommon.toggleChangeLog(\''.$changelogid.'\');" ><i class="vteicon" id="img_'.$changelogid.'">keyboard_arrow_right</i><span style="position: relative; bottom: 7px;">'.getTranslatedString('LBL_DETAILS','ModNotifications').'</span></a>';	//crmv@104566
			$html .= '<div style="display:none;" id="div_'.$changelogid.'" name="div_'.$changelogid.'">';
			$html .= $ChangeLogFocus->getFieldsTable($description, $this->parent_module);
			$html .= '</div>';
		}

		// crmv@43194	crmv@54917
		if ($this->data['related_to'] > 0 && in_array($this->data['mod_not_type'], array('Calendar invitation', 'Calendar invitation edit'))) {
			$rowid = $this->data['crmid'];
			$checkedYes = $checkedNo = '';
			$res = $adb->pquery("select partecipation from {$table_prefix}_invitees where activityid = ? and inviteeid = ?",array($this->data['related_to'],$this->data['smownerid']));
			if ($res) $invitationAnswer = $adb->query_result($res,0,'partecipation');
			if ($invitationAnswer == 1) {
				$checkedNo = 'checked="checked"';
			} elseif ($invitationAnswer == 2) {
				$checkedYes = 'checked="checked"';
			}
			$html .= '<br> '.getTranslatedString('LBL_INVITATION_QUESTION', 'ModNotifications').'? ';
			$html .= '<a><input id="notifInvitiationAnswerYes_'.$rowid.'" name="notifInvitiationAnswer_'.$rowid.'" '.$checkedYes.' type="radio" name="" style="vertical-align:bottom" onclick="ModNotificationsCommon.acceptInvitation('.$this->data['related_to'].', '.$current_user->id.')" /><label for="notifInvitiationAnswerYes_'.$rowid.'">'.getTranslatedString('LBL_YES').'</label></a>';
			$html .= '<a><input id="notifInvitiationAnswerNo_'.$rowid.'" name="notifInvitiationAnswer_'.$rowid.'" '.$checkedNo.' type="radio" name="" style="vertical-align:bottom" onclick="ModNotificationsCommon.declineInvitation('.$this->data['related_to'].', '.$current_user->id.')" /><label for="notifInvitiationAnswerNo_'.$rowid.'">'.getTranslatedString('LBL_NO').'</label></a>';
		}
		// crmv@43194e	crmv@54917e

		//crmv@65455
		if ($this->data['mod_not_type'] == 'Import Error') {
			$desc = getTranslatedString('LBL_IMPORT_ERROR_NOTIF_DESC', 'Settings');
			$html = "<b>$html</b> ".$desc;
		}
		//crmv@65455e

		//crmv@91571
		if ($this->data['mod_not_type'] == 'MassEdit' || $this->data['mod_not_type'] == 'MassEditError') {
			$MUtils = MassEditUtils::getInstance();
			$html = $MUtils->getNotificationHtml($this->data['related_to'], $html);
		}
		//crmv@91571e

		return $html;
	}

	// crmv@31780 - restituisce un array, no html
	// TODO: listview
	function content_no_html() {
		global $adb, $current_user,$table_prefix;
		$ret = array();

		$focus = CRMEntity::getInstance('ModNotifications');
		$ret['action'] = $focus->translateNotificationType($this->data['mod_not_type'],'action');
		$ret['notification_type'] = $this->data['mod_not_type'];

		//crmv@31126
		/*if ($this->data['mod_not_type'] == 'Import Completed') {
			$html .= ' <a href="index.php?module='.$this->data['description'].'&action=index">'.$this->data['description'].'</a>';
		}*/
		//crmv@31126e

		if ($this->data['mod_not_type'] == 'Relation') {
			$parent_id = $this->data['description'];
			$parent_module = getSalesEntityType($parent_id);
			$entityType = getSingleModuleName($parent_module,$parent_id);
			$displayValueArray = getEntityName($parent_module, $parent_id);
			if(!empty($displayValueArray)){
				foreach($displayValueArray as $key=>$value){
					$displayValue = $value;
				}
			}
			$html .= " <a href='index.php?module=$parent_module&action=DetailView&record=$parent_id' title='$entityType' target='_parent'>$displayValue</a> ($entityType) ";
			$html .= getTranslatedString('LBL_TO','ModComments');

			$ret['related'] = array(
				'module' => $parent_module,
				'record' => $parent_id,
				'value' => $displayValue,
				'type' => $entityType
			);
		}

		$ret['item'] = $this->relatedToString(true);

		$ret['haslist'] = false;
		if ($this->data['mod_not_type'] == 'ListView changed') {
			$changes = array_filter(explode(',',$this->data['description']));
			$html_changes = '';
			if (!empty($changes)) {
				foreach($changes as $id) {
					$module = getSalesEntityType($id);
					$displayValueArray = getEntityName($module,$id);
					if(!empty($displayValueArray)){
						foreach($displayValueArray as $key=>$value){
							$displayValue = $value;
						}
					}
					$ret['list'] = array(
						'module' =>  $module,
						'record' => $id,
						'value' => $displayValue
					);
				}
				$ret['haslist'] = true;
			}
		}

		$ret['hasdetails'] = false;
		if ($this->parent_module != '' && in_array($this->data['mod_not_type'],array('Changed followed record','Changed record'))) {

			$q = "SELECT * FROM ".$table_prefix."_changelog ch INNER JOIN ".$table_prefix."_crmentity c ON ch.changelogid = c.crmid
				WHERE c.deleted = 0 AND parent_id = ? AND user_name <> ? ORDER BY changelogid DESC ";
			$ress = $adb->pquery($q,array($this->parent_id, $current_user->user_name));
			$changelogid = $adb->query_result_no_html($ress,0,"changelogid");
			$description = $adb->query_result_no_html($ress,0,"description");
			$ChangeLogFocus = CRMEntity::getInstance('ChangeLog');


			$ret['details'] = $ChangeLogFocus->getFieldsTable($description, $this->parent_module, true);
			if (is_array($ret['details'])) {
				foreach ($ret['details'] as $k=>$v) if (is_array($ret['details'][$k])) $ret['details'][$k]['changelogid'] = $changelogid;
				if (count($ret['details']) == 0) {
					unset($ret['details']);
				} else {
					$ret['hasdetails'] = true;
				}
			}

			$ret['changelogid'] = $changelogid;
		}

		//crmv@91571
		if ($this->data['mod_not_type'] == 'MassEdit' || $this->data['mod_not_type'] == 'MassEditError') {
			$MUtils = MassEditUtils::getInstance();
			$ret['massedit'] = $MUtils->getNotificationInfo($this->data['related_to']);
		}
		//crmv@91571e

		return $ret;
	}
	// crmv@31780e

	function id() {
		return $this->data['crmid'];
	}

	function relatedTo() {
		return $this->data['related_to'];
	}

	// crmv@31780
	function relatedToString($nohtml = false) {
		global $table_prefix;
		$this->parent_id = $this->relatedTo();
		if (!in_array($this->parent_id,array('',0))) {
			if ($this->data['mod_not_type'] == 'ListView changed') {
				global $adb, $app_strings;
				$result = $adb->query('SELECT * FROM '.$table_prefix.'_customview WHERE cvid = '.$this->parent_id);
				if ($result) {
					$this->parent_module = $adb->query_result($result,0,'entitytype');
					$entityType = getTranslatedString($this->parent_module,$this->parent_module);
					$viewname = $adb->query_result($result,0,'viewname');
					if ($viewname == 'All') {
						$viewname = $app_strings['COMBO_ALL'];
					} elseif($this->parent_module == 'Calendar' && in_array($viewname,array('Events','Tasks'))) {
						$viewname = $app_strings[$viewname];
					}
					$displayValue = $viewname;
				}
				if (empty($displayValue)) {
					$displayValue = $entityType;
				}
				if ($nohtml) {
					return array(
						'module' => $this->parent_module,
						'action' => 'index',
						'viewname' => $this->parent_id,
						'value' => $displayValue,
						'type' => $entityType
					);
				} else {
					return " <a href='index.php?module=$this->parent_module&action=index&viewname=$this->parent_id' title='$entityType' target='_parent'>$displayValue</a> ($entityType)";
				}
			} else {
				$this->parent_module = getSalesEntityType($this->parent_id);
				$entityType = getSingleModuleName($this->parent_module,$this->parent_id);
				$displayValueArray = getEntityName($this->parent_module, $this->parent_id);
				$displayValue = $displayValueArray[$this->parent_id];
				if (empty($displayValue)) {
					$displayValue = $entityType;
				}
				if ($nohtml) {
					return array(
						'module' => $this->parent_module,
						'action' => 'DetailView',
						'record' => $this->parent_id,
						'value' => $displayValue,
						'type' => $entityType
					);
				} else {
					// crmv@43050
					if ($this->parent_module == 'ModComments') {
						return " <a href='javascript:;' onclick=\"jQuery('#ModNotifications .closebutton').click(); top.jQuery('#ModCommentsCheckChangesImg').click();\" title='$entityType' target='_parent'>$displayValue</a> ($entityType) "; // crmv@43194
					} else {
						return " <a href='index.php?module=$this->parent_module&action=DetailView&record=$this->parent_id' title='$entityType' target='_parent'>$displayValue</a> ($entityType)";
					}
					// crmv@43050e
				}
			}
		}
	}
	// crmv@31780e

	function isUnseen() {
		if ($this->data['seen'] == 1) {
			return false;
		} else {
			return true;
		}
	}
}
//crmv@end
?>
