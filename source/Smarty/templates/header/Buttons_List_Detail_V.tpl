{* crmv@18592 crmv@82419 crmv@128159 *}
{assign var="toggleState" value=$smarty.cookies.togglePin}
{if empty($toggleState)}
	{assign var="toggleState" value="disabled"}
{/if}
<div id="Buttons_Detail" class="vteCenterHeader" data-minified="{$toggleState}">
	{if $MODULE eq 'Calendar'}
		<div style="float:left">
			<ul class="vteUlTable" style="padding-right:5px">
				<li>
				 	<button class="crmbutton small edit" onclick="listToCalendar('Today')">{'LBL_DAY'|@getTranslatedString:$MODULE}</button>
					<button class="crmbutton small edit" onclick="listToCalendar('This Week')">{'LBL_WEEK'|@getTranslatedString:$MODULE}</button>
					<button class="crmbutton small edit" onclick="listToCalendar('This Month')">{'LBL_MONTH'|@getTranslatedString:$MODULE}</button>
					<button class="crmbutton small edit" onclick="location.href = 'index.php?action=ListView&module=Calendar&parenttab={$CATEGORY}'">{'LBL_CAL_TO_FILTER'|@getTranslatedString:$MODULE}</button>
				</li>
			</ul>
		</div>
	{/if}
	<div style="float:left">
		{include file="Buttons_List_Contestual.tpl"}
	</div>
	<div style="float:left">
		{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
  		{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
  		{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
 		<span class="dvHeaderText">
			<span class="recordTitle1">{$SINGLE_MOD|@getTranslatedString:$MODULE}</span>
			{if $SHOW_RECORD_NUMBER eq true}
				[ {$USE_ID_VALUE} ]
			{/if}
			{$NAME}&nbsp;<span style="font-weight:normal;">{$UPDATEINFO}</span>
		</span>
		{* crmv@25620 *}
 		<script type="text/javascript">
			updateBrowserTitle('{$SINGLE_MOD|@getTranslatedString:$MODULE} - {$NAME} [{$USE_ID_VALUE}]');
		</script>
		{* crmv@25620e *}
	</div>
	<div style="float:right">
		<ul id="Buttons_List_3">
			{if $FOLDERID > 0}
			<li>
				<a href="index.php?module={$MODULE}&action=ListView&folderid={$FOLDERID}"><img src="{'folderback.png'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_GO_BACK}" title="{$APP.LBL_GO_BACK}" align="absbottom" border="0" /></a>
			</li>
			{/if}
			<li>
				{if $PERFORMANCE_CONFIG.DETAILVIEW_RECORD_NAVIGATION}
			 		{if $INVENTORY_VIEW eq 'true'}
						{if $privrecord neq ''}
							<img align="top" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.png'|@vtiger_imageurl:$THEME}">
						{else}
							<img align="top" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.png'|@vtiger_imageurl:$THEME}">
						{/if}
						{if $privrecord neq '' || $nextrecord neq ''}
							<img align="top" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdTop" id="jumpBtnIdTop" src="{'rec_jump.png'|@vtiger_imageurl:$THEME}" height="24">
						{/if}
						{if $nextrecord neq ''}
							<img align="top" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}'" name="nextrecord" src="{'rec_next.png'|@vtiger_imageurl:$THEME}">
						{else}
							<img align="top" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.png'|@vtiger_imageurl:$THEME}">
						{/if}
			 		{else}
						{if $privrecord neq ''}
							<img align="top" title="{$APP.LNK_LIST_PREVIOUS}" accessKey="{$APP.LNK_LIST_PREVIOUS}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$privrecord}&parenttab={$CATEGORY}&start={$privrecordstart}'" name="privrecord" value="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev.png'|@vtiger_imageurl:$THEME}">
						{else}
							<img align="top" title="{$APP.LNK_LIST_PREVIOUS}" src="{'rec_prev_disabled.png'|@vtiger_imageurl:$THEME}">
						{/if}
						{if $privrecord neq '' || $nextrecord neq ''}
							<img align="top" title="{$APP.LBL_JUMP_BTN}" accessKey="{$APP.LBL_JUMP_BTN}" onclick="var obj = this;var lhref = getListOfRecords(obj, '{$MODULE}',{$ID},'{$CATEGORY}');" name="jumpBtnIdTop" id="jumpBtnIdTop" src="{'rec_jump.png'|@vtiger_imageurl:$THEME}" height="24">
						{/if}
						{if $nextrecord neq ''}
							<img align="top" title="{$APP.LNK_LIST_NEXT}" accessKey="{$APP.LNK_LIST_NEXT}" onclick="location.href='index.php?module={$MODULE}&viewtype={$VIEWTYPE}&action=DetailView&record={$nextrecord}&parenttab={$CATEGORY}&start={$nextrecordstart}'" name="nextrecord" src="{'rec_next.png'|@vtiger_imageurl:$THEME}">
						{else}
							<img align="top" title="{$APP.LNK_LIST_NEXT}" src="{'rec_next_disabled.png'|@vtiger_imageurl:$THEME}">
						{/if}
					{/if}
				{/if}
			</li>
				
			{if $MODULE eq 'Webforms'}
				{* do nothing *}
			{else}
				<li class="pull-right">
					<div class="tableBox">
						<div class="contentCenter">
							<div class="dropdown">
								<div class="smallerCircle iconCircle dropdown-toggle" data-toggle="dropdown">
									<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_OTHER'|getTranslatedString:'Users'}" onclick="jQuery('.loadDetailViewWidget').click();">reorder</i>
								</div>
								<div id="detailViewActionsContainer" class="dropdown-menu dropdown-menu-right" style="box-shadow:none;">
									<div class="crmvDiv" style="max-height:500px; overflow-y:auto; padding:0px 5px 5px 5px;display:table">
										{include file="DetailViewActions.tpl"}
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			{/if}
				
			<li class="pull-right">
				<div class="tableBox">
					{if $MODULE neq 'Users' && $MODULE neq 'Webforms'}
						<div class="contentCenter" style="padding: 0px 10px">
							<i id="favoriteImg" class="vteicon md-link" title="{$APP.LBL_FAVORITE}" onClick="set_favorite({$ID});">{$ID|getFavoriteCls}</i>
						</div>
						{if $MODULE neq 'ChangeLog' && $MODULE neq 'ModNotifications'}
							<div class="contentCenter" style="padding: 0px 10px">
								{assign var=FOLLOWIMG value=$ID|@getFollowImg}
								{if preg_match('/_on/', $FOLLOWIMG)}
									{assign var=FOLLOWTITLE value='LBL_UNFOLLOW'|getTranslatedString:'ModNotifications'}
								{else}
									{assign var=FOLLOWTITLE value='LBL_FOLLOW'|getTranslatedString:'ModNotifications'}
								{/if}
								<i id="followImg" class="vteicon md-link" title="{$FOLLOWTITLE}" onClick="ModNotificationsCommon.follow({$ID});">{$ID|getFollowCls}</i>
							</div>
						{/if}
					{/if}
					
			 		{if $MODULE eq 'Users'}
			 			<div class="contentCenter">
			 				{$EDIT_BUTTON}
			 			</div>
			 		{/if}
			
					{if $EDIT_DUPLICATE eq 'permitted'}
						<div class="contentCenter" style="padding: 0px 3px">
							<div class="smallerCircleGreen iconCircle">
								<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$APP.LBL_EDIT_BUTTON_LABEL}" onclick="DetailView.return_module.value='{$MODULE}'; DetailView.return_action.value='DetailView'; DetailView.return_id.value='{$ID}';DetailView.module.value='{$MODULE}'; submitFormForAction('DetailView','EditView');">create</i>
							</div>
						</div>
					{/if}
			
					{if $MODULE eq 'Webforms'}
						<button id="edit_form" name="edit_form" class="crmbutton small edit" onclick="Webforms.editForm({$WEBFORMMODEL->getId()})">{'LBL_EDIT_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
						<button id="show_html" name="show_html" class="crmbutton small create" onclick="Webforms.getHTMLSource({$WEBFORMMODEL->getId()})">{'LBL_SOURCE'|@getTranslatedString:$MODULE}</button>
						<button id="delete_form" name="delete_form" class="crmbutton small delete" onclick="return Webforms.deleteForm('action_form',{$WEBFORMMODEL->getId()})">{'LBL_DELETE_BUTTON_LABEL'|@getTranslatedString:$MODULE}</button>
					{/if}
					
					{if $SHOW_TURBOLIFT_LINK_BUTTON}
						<div class="contentCenter" style="padding: 0px 3px">
							<div class="smallerCircle iconCircle">
								<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_LINK_ACTION'|@getTranslatedString:'Messages'}" onclick="LPOP.openPopup('{$MODULE}', '{$ID}', '{$MODE}');">link</i>
							</div>
						</div>
					{/if}
				</div>
			</li>
				
			<li class="pull-right">
				{if $SHOW_DETAIL_TRACKER}
					{include file="modules/SDK/src/CalendarTracking/DetailTracking.tpl"}
				{/if}
			</li>
			
			<li class="pull-right">
				<span id="vtbusy_info" style="display:none;position:absolute;right:-30px;top:50%;transform:translateY(-50%);">{include file="LoadingIndicator.tpl"}</span>
			</li>
		</ul>
	</div>
</div>
<div class="vteCenterHeaderWhite"></div>
<script>calculateButtonsList3();</script>
{*<!-- crmv@18592e -->*}

{include file="header/HideMenuJS.tpl"}

{* crmv@26986 *}
<script>
var enable_favorite = 'yes';
function set_favorite(id) {ldelim}
	if (enable_favorite == 'no') return false;
	enable_favorite = 'no';
	var onstarImg = '{'favorites_on.png'|@vtiger_imageurl:$THEME}';
	var offstarImg = '{'favorites_off.png'|@vtiger_imageurl:$THEME}';
	$("vtbusy_info").style.display="inline";
	{literal}
	jQuery.ajax({
		url: 'index.php?module=SDK&action=SDKAjax&file=src/Favorites/SetFavorite&record='+id,
		success: function(data){
			var res = data.split('###');
			if (res[0] == 'favorite') {
				//jQuery('#favoriteImg').attr('src',onstarImg);
				jQuery('#favoriteImg').text('star');
			} else {
				//jQuery('#favoriteImg').attr('src',offstarImg);
				jQuery('#favoriteImg').text('star_border');
			}
			jQuery('#favorites_button').show();
			jQuery('#favorites_list').html(res[1]);
			$("vtbusy_info").style.display="none";
			enable_favorite = 'yes';
		}
	});
	{/literal}
{rdelim}
</script>
{* crmv@26986e *}
