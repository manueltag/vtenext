{* crmv@18592 crmv@56945 crmv@128159 *}
{assign var="toggleState" value=$smarty.cookies.togglePin}
{if empty($toggleState)}
	{assign var="toggleState" value="disabled"}
{/if}
<div id="Buttons_Edit" class="vteCenterHeader" data-minified="{$toggleState}">
	<div style="float:left">
		{include file="Buttons_List_Contestual.tpl"}
	</div>
	<div style="float:left">
		<ul>
		<li>
		{* vtlib customization: use translated label if available *}
		{assign var="SINGLE_MOD_LABEL" value=$SINGLE_MOD}
		{if $APP.$SINGLE_MOD} {assign var="SINGLE_MOD_LABEL" value=$APP.SINGLE_MOD} {/if}
				
		{if $OP_MODE eq 'edit_view'} 
			{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
			{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
			<span class="lvtHeaderText">
				<span class="recordTitle1">{$APP.LBL_EDITING} {$SINGLE_MOD|@getTranslatedString:$MODULE}</span>
				{if $SHOW_RECORD_NUMBER eq true}
					[ {$USE_ID_VALUE} ]&nbsp;
				{/if}
				{$NAME}&nbsp;<span style="font-weight:normal;">{$UPDATEINFO}</span>
			</span>
			{* crmv@25620 *}
	 		<script type="text/javascript">
				updateBrowserTitle('{$APP.LBL_EDITING} {$SINGLE_MOD|@getTranslatedString:$MODULE} - {$NAME} [{$USE_ID_VALUE}]');
			</script>
			{* crmv@25620e *}
		{elseif $OP_MODE eq 'create_view'}
			{if $DUPLICATE neq 'true'}
				{assign var=create_new value="LBL_CREATING_NEW_"|cat:$SINGLE_MOD}
				{* vtlib customization: use translation only if present *}
				{assign var="create_newlabel" value=$APP.$create_new}
				{* crmv@54375 *}
				{if $create_newlabel eq ''}
					{assign var="create_newlabel_tmp" value=$SINGLE_MOD|@getTranslatedString:$MODULE}
					{assign var="create_newlabel" value=$APP.LBL_CREATING|cat:' '|cat:$create_newlabel_tmp}
				{/if}
				{if !empty($RETURN_RECORD_NAME)}
					<span class="dvHeaderText">{$RETURN_RECORD_NAME} <span style="font-weight:normal;">></span></span>
				{/if}
				<span class="recordTitle1">{$create_newlabel}</span> <br>
		 		<script type="text/javascript">
					updateBrowserTitle('{$create_newlabel}');
				</script>
				{* crmv@54375e *}
			{else}
				<span class="lvtHeaderText">{$APP.LBL_DUPLICATING} {$SINGLE_MOD|@getTranslatedString:$MODULE} "{$NAME}" </span> <br>
				{* crmv@25620 *}
		 		<script type="text/javascript">
					updateBrowserTitle('{$APP.LBL_DUPLICATING} {$SINGLE_MOD|@getTranslatedString:$MODULE} "{$NAME}"');
				</script>
				{* crmv@25620e *}
			{/if}
		{* crmv@22223 *}
		{elseif $EXIST eq "true" && $EXIST neq ''}
			<span class="recordTitle1">{$MOD.Edit_Custom_View}</span>
			{* crmv@25620 *}
	 		<script type="text/javascript">
				updateBrowserTitle('{$MOD.Edit_Custom_View}');
			</script>
			{* crmv@25620e *}
		{else}
		 	<span class="recordTitle1">{$MOD.New_Custom_View}</span>
		 	{* crmv@25620 *}
	 		<script type="text/javascript">
				updateBrowserTitle('{$MOD.New_Custom_View}');
			</script>
			{* crmv@25620e *}
		{* crmv@22223e *}
		{/if}
		</li>
		</ul>
	</div>
	<div style="float:right">
		<ul>
		{* crmv@45561 *}
		{if !empty($ERROR_STR)}
			<li><span class="errorString" style="padding-right:5px">{$ERROR_STR}</span></li>
		{/if}
		{* crmv@45561e *}
		{if $smarty.request.close_window eq 'yes'}
 			{assign var="CANCEL_LINK" value="window.close()"}
 		{else}
 			{assign var="CANCEL_LINK" value="window.history.back()"}
 		{/if}
		{if $MODULE eq 'CustomView' or $smarty.request.action eq 'CustomView'}
			<li><button title="{$APP.LBL_SAVE_BUTTON_LABEL}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button2" style="min-width: 70px;" type="submit" onClick="return customViewSubmit();">{$APP.LBL_SAVE_BUTTON_LABEL}</button>	{* crmv@29615 *}	{* crmv@31775 *}</li>
			<li><button title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" name="button2" onclick='{$CANCEL_LINK}' style="min-width: 70px;" type="button">{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
		{elseif $INVENTORY_VIEW eq 'true'}
			{* crmv@58638 *}
			{if $MODULE|isInventoryModule}
				{assign var="DISABLE_SAVE" value="disabled='disabled'"}
			{else}
				{assign var="DISABLE_SAVE" value=""}
			{/if}
			{if $OP_MODE eq 'edit_view'}
				{assign var="SAVE_ACTION" value="this.form.action.value='Save'; displaydeleted(); return validateInventory('$MODULE');"}
				<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save inventory_submit" onclick="{$SAVE_ACTION}" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" style="min-width: 70px" {$DISABLE_SAVE}></li>
				{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save inventory_submit" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 crmv@102879 *}
			{elseif $OP_MODE eq 'create_view'}
				{assign var="SAVE_ACTION" value="this.form.action.value='Save'; return validateInventory('$MODULE')"}
				{* crmv@54375 *}
				{if !empty($RETURN_ID) && !empty($RETURN_MODULE)}
					<li><input class="crmbutton backButtonSave small inventory_submit" onclick="{$SAVE_ACTION}" type="submit" name="button" value="{$APP.LBL_SAVE_AND_BACK_BUTTON_LABEL}" style="min-min-width: 70px" {$DISABLE_SAVE}></li>
					<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save inventory_submit" onclick="this.form.return2detail.value='yes'; {$SAVE_ACTION}" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" style="min-width: 70px" {$DISABLE_SAVE}></li>
					{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save inventory_submit" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 *}
				{else}
				{* crmv@54375e *}
					<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save inventory_submit" onclick="{$SAVE_ACTION}" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" style="min-width: 70px" {$DISABLE_SAVE}>
					{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save inventory_submit" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 *}
				{/if}
				<input type="hidden" name="convert_from" value="{$CONVERT_MODE}">
				<input type="hidden" name="duplicate_from" value="{$DUPLICATE_FROM}">
			{/if}
			<li><button title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="{$CANCEL_LINK}" type="button" name="button" style="min-width: 70px">{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
			{* crmv@58638e *}
		{else}
			{* crmv@27061 *} {* crmv@105416 *}
	 		{if $smarty.request.module eq 'Calendar'}
	 			{if $ACTIVITY_MODE neq 'Task'}
	 				{* crmv@54375 crmv@95751 *}
					{if $OP_MODE eq 'create_view' && !empty($RETURN_ID) && !empty($RETURN_MODULE)}
						<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton backButtonSave small" name="button" value="{$APP.LBL_SAVE_AND_BACK_BUTTON_LABEL}" onclick="this.form.action.value='Save'; jQuery(this.form).submit();" style="min-min-width: 70px;" type="button" /></li>
						<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.return2detail.value='yes'; this.form.action.value='Save'; VtigerJS_DialogBox.block(); jQuery(this.form).submit();" style="min-width: 70px;" type="button" /></li>
					{else}
	 					<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='Save'; VtigerJS_DialogBox.block(); jQuery(this.form).submit();" style="min-width: 70px;" type="button" /></li>
	 				{/if}
	 			{else}
					{if $OP_MODE eq 'create_view' && !empty($RETURN_ID) && !empty($RETURN_MODULE)}
						<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton backButtonSave small" name="button" value="{$APP.LBL_SAVE_AND_BACK_BUTTON_LABEL}" onclick="this.form.action.value='Save'; VtigerJS_DialogBox.block(); jQuery(this.form).submit();" style="min-min-width: 70px;" type="button" /></li>
						<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.return2detail.value='yes'; this.form.action.value='Save'; VtigerJS_DialogBox.block(); jQuery(this.form).submit();" style="min-width: 70px;" type="button" /></li>
					{else}
	 					<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="this.form.action.value='Save'; VtigerJS_DialogBox.block(); jQuery(this.form).submit();" style="min-width: 70px;" type="button" /></li>
	 				{/if}
	 				{* crmv@54375e crmv@95751e *}
	 			{/if}
				<li><input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="{$CANCEL_LINK}" style="min-width: 70px;" type="button" /></li>
	 		{* crmv@27061e *} {* crmv@105416e *}
			{* crmv@20054 *}
	 		{elseif $smarty.request.module eq 'Users'}
	 			<li><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small crmbutton save" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" onclick="EditView.action.value='Save'; return verify_data(EditView)" style="min-width: 70px;" type="button" /></li>
				<li><input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small crmbutton cancel" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="{$CANCEL_LINK}" style="min-width: 70px;" type="button" /></li>
	 		{* crmv@20054e *}
	 		{* crmv@29386 *}
			{elseif $MODULE eq 'Webforms'}
				<li><input title="{'LBL_SAVE_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_SAVE_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small save" onclick="javascript:return Webforms.validateForm('webform_edit','index.php?module=Webforms&action=Save')" name="button" value="{'LBL_SAVE_BUTTON_LABEL'|@getTranslatedString:$MODULE} " style="min-width: 70px" type="submit"></li>
				<li><input title="{'LBL_CANCEL_BUTTON_TITLE'|@getTranslatedString:$MODULE}" accesskey="{'LBL_CANCEL_BUTTON_KEY'|@getTranslatedString:$MODULE}" class="crmbutton small cancel" onclick="{$CANCEL_LINK}" name="button" value="{'LBL_CANCEL_BUTTON_LABEL'|@getTranslatedString:$MODULE}" style="min-width: 70px" type="button"></li>
			{* crmv@29386e *}
			{elseif $OP_MODE eq 'edit_view'}
				{assign var="SAVE_ACTION" value="this.form.action.value='Save'; displaydeleted(); SubmitForm(this.form,$ID,'$MODULE');"}
				<li><button title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="{$SAVE_ACTION}" type="button" name="button" style="min-width: 70px" >{$APP.LBL_SAVE_BUTTON_LABEL}</button><!-- ds@19--></li>
				{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 crmv@102879 *}
				<li><button title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="{$CANCEL_LINK}" type="button" name="button" style="min-width: 70px">{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
			{elseif $OP_MODE eq 'create_view'}
				{assign var="SAVE_ACTION" value="this.form.action.value='Save'; SubmitForm(this.form,'','$MODULE');"}
				{* crmv@54375 *}
				{if !empty($RETURN_ID) && !empty($RETURN_MODULE)}
					<li><button class="crmbutton backButtonSave small" onclick="{$SAVE_ACTION}" type="button" name="button" style="min-min-width: 70px">{$APP.LBL_SAVE_AND_BACK_BUTTON_LABEL}</button></li>
					<li><button title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="this.form.return2detail.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{$APP.LBL_SAVE_BUTTON_LABEL}</button></li>
					{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 *}
				{else}
				{* crmv@54375e *}
					<li><button title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="{$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{$APP.LBL_SAVE_BUTTON_LABEL}</button></li>
					{if $SHOW_RUN_PROCESSES_BUTTON}<li><button title="{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_TITLE'|getTranslatedString:'Processes'}" class="crmbutton small save" onclick="this.form.run_processes.value='yes'; {$SAVE_ACTION}" type="button" name="button" style="min-width: 70px">{'LBL_SAVE_AND_RUN_PROCESSES_BUTTON_LABEL'|getTranslatedString:'Processes'} <i class="vteicon button-image icon-module icon-processes valign-bottom" data-first-letter="P"></i></button></li>{/if}	{* crmv@100495 *}
				{/if}
				{* crmv@53056 *}
				{if $MODULE eq 'Timecards' && $smarty.request.newtcdone eq 'yes'}
					<li><button title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="this.form.module.value='HelpDesk'; this.form.action.value='DetailView'; this.form.record.value={$smarty.request.ticket_id};" type="submit" name="button" style="min-width: 70px">{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
				{else}
				{* crmv@53056e *}
					<li><button title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="{$CANCEL_LINK}" type="button" name="button" style="min-width: 70px">{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
				{/if}
			{* crmv@22223 *}
			{else}
				<li><button title="{$APP.LBL_SAVE_BUTTON_LABEL}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" name="button2" style="min-width: 70px;" type="submit" onClick="return checkDuplicate();">{$APP.LBL_SAVE_BUTTON_LABEL}</button></li>
				<li><button title="{$APP.LBL_CANCEL_BUTTON_LABEL}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" name="button2" onclick='{$CANCEL_LINK}' style="min-width: 70px;" type="button" >{$APP.LBL_CANCEL_BUTTON_LABEL}</button></li>
			{* crmv@22223e *}
			{/if}
		{/if}
		</ul>
	</div>
</div>
<div class="vteCenterHeaderWhite"></div>

{include file="header/HideMenuJS.tpl"}