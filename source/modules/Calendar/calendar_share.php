<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ********************************************************************************/

/* crmv@20209 crmv@36511 crmv@3085m */

global $mode;
$cal_class = CRMEntity::getInstance('Calendar');
$shareduser_ids = $cal_class->getSharedUserId($_REQUEST['record']);
$shownduser_ids = $cal_class->getShownUserId($_REQUEST['record']);

if ($mode == 'detail') {
?>
	<tr valign="top">
		<td width="50%">
			<?php
			require_once('Smarty_setup.php');
			$smarty = new vtigerCRM_Smarty;
			$smarty->assign('label',getTranslatedString('LBL_CALENDAR_SHARING','Calendar'));
			$smarty->display('FieldHeader.tpl');
			?>
			<div class="dvtCellInfo">
				<?php
				foreach($shareduser_ids as $shar_id=>$share_user) {
					if($shar_id != '') {
						echo "$share_user<br />";
					}
				}
				?>
			</div>
		</td>
		<td width="50%">
			<?php
			require_once('Smarty_setup.php');
			$smarty = new vtigerCRM_Smarty;
			$smarty->assign('label',getTranslatedString('LBL_CALENDAR_USERS_SHOWN','Users'));
			$smarty->display('FieldHeader.tpl');
			?>
			<div class="dvtCellInfo">
				<?php
				foreach($shownduser_ids as $id=>$name) {
					if($id != '') {
						echo $name['name']."<br />";
					}
				}
				?>
			</div>
		</td>
	</tr>
<?php
}
elseif ($mode != 'create') {
	//crmv@36555
	$shareduser_list = $cal_class->getSharingUserName($_REQUEST['record']);
	$shownuser_list = $cal_class->getShownUserList($_REQUEST['record']);
	//crmv@36555 e
?>
	<input type="hidden" name="shar_userid" id="shar_userid" >
	<input type="hidden" name="shown_userid" id="shown_userid" >
	<table border=0 cellspacing=0 cellpadding=5 width=100% align=center>
		<tr>
			<td><b><?php echo getTranslatedString('LBL_CALENDAR_SHARING','Calendar');?></b></td>
		</tr>
		<tr>
			<td align="center"><!-- Calendar sharing UI-->
			<DIV id="cal_shar" style="display: block; width: 100%;">
			<table border=0 cellspacing=0 cellpadding=2 width=100%
				bgcolor="#FFFFFF">
				<tr>
					<td valign=top>
					<table border=0 cellspacing=0 cellpadding=2 width=100%>
						<tr>
							<td><b><?php echo getTranslatedString('LBL_AVL_USERS','Calendar');?></b></td>
							<td>&nbsp;</td>
							<td><b><?php echo getTranslatedString('LBL_SEL_USERS','Calendar');?></b></td>
						</tr>
						<tr>
							<td width=40% align=center valign=top>
								<div class="dvtCellInfo">
									<select name="available_users_sharing" id="available_users_sharing" class="small notdropdown" size=5 multiple style="height: 70px; width: 100%">
									<?php
									foreach($shareduser_list as $id=>$name)
									{
										if($id != '')
										echo "<option value=".$id.">".$name."</option>";
									}
									?>
									</select>
								</div>
							</td>
							<td width=20% align=center valign=middle>
								<input type=button value="<?php echo getTranslatedString('LBL_ADD_BUTTON','Calendar'); ?> >>" class="crmbutton small edit" style="width: 100%" onClick="incUser('available_users_sharing','selected_users_sharing')"><br/><br />
								<input type=button value="<< <?php echo getTranslatedString('LBL_RMV_BUTTON','Calendar'); ?> " class="crmbutton small edit" style="width: 100%" onClick="rmvUser('selected_users_sharing')"></td>
							<td>
								<div class="dvtCellInfo">
									<select name="selected_users_sharing" id="selected_users_sharing" class="small notdropdown" size=5 multiple style="height: 70px; width: 100%">
									<?php
									foreach($shareduser_ids as $shar_id=>$share_user)
									{
										if($shar_id != '')
										echo "<option value=".$shar_id.">".$share_user."</option>";
									}
									?>
									</select>
								</div>
							<td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</div>
			</td>
		</tr>
		<tr>
			<td><b><?php echo getTranslatedString('LBL_CALENDAR_USERS_SHOWN','Users');?></b></td>
		</tr>
		<tr>
			<td align="center"><!-- Calendar sharing UI-->
			<DIV id="cal_shar" style="display: block; width: 100%;">
			<table border=0 cellspacing=0 cellpadding=2 width=100%
				bgcolor="#FFFFFF">
				<tr>
					<td valign=top>
					<table border=0 cellspacing=0 cellpadding=2 width=100%>
						<tr>
							<td><b><?php echo getTranslatedString('LBL_AVL_USERS','Calendar');?></b></td>
							<td>&nbsp;</td>
							<td><b><?php echo getTranslatedString('LBL_SEL_USERS','Calendar');?></b></td>
						</tr>
						<tr>
							<td width=40% align=center valign=top>
								<div class="dvtCellInfo">
									<select name="available_users_shown" id="available_users_shown" class="small notdropdown" size=5 multiple style="height: 70px; width: 100%">
									<?php
									foreach($shownuser_list as $id=>$name)
									{
										if($id != '')
										echo "<option value=".$id.">".$name."</option>";
									}
									?>
									</select>
								</div>
							</td>
							<td width=20% align=center valign=middle>
								<input type=button value="<?php echo getTranslatedString('LBL_ADD_BUTTON','Calendar'); ?> >>" class="crmbutton small edit" style="width:100%;" onClick="incUser('available_users_shown','selected_users_shown')"><br/><br />
								<input type=button value="<< <?php echo getTranslatedString('LBL_RMV_BUTTON','Calendar'); ?> " class="crmbutton small edit" style="width:100%;" onClick="rmvUser('selected_users_shown')"></td>
							<td>
								<div class="dvtCellInfo">
									<select name="selected_users_shown" id="selected_users_shown" class="small notdropdown" size=5 multiple style="height: 70px; width: 100%">
									<?php
									foreach($shownduser_ids as $id=>$name)
									{
										if($id != '')
										echo "<option value=".$id.">".$name['name']."</option>";
									}
									?>
									</select>
								</div>
							<td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			</div>
			</td>
		</tr>
	</table>
<?php
}
?>