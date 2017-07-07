{* crmv@124738 *}
<ul id="Buttons_List_Contestual" class="vteUlTable">

	{if !empty($BUTTONS)}
		{foreach key=button_check item=button_label from=$BUTTONS}
			{if $button_check eq 'back'}
				<li style="padding-right:10px">
					<div class="smallerCircle iconCircle">
						<a href='index.php?module={$MODULE}&action=index'>
							{if $FOLDERID > 0}
								<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link"  title="{$APP.LBL_GO_BACK}">undo</i>
							{else}
								<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link"  title="{$APP.LBL_FOLDERS}">folder</i>
							{/if}
						</a>
					</div>
				</li>
			{/if}
		{/foreach}
	{/if}

	{if $MODULE eq 'Home' && $REQUEST_ACTION eq 'index'}
		<li>
			<div class="dropdown">
				<span data-toggle="dropdown">
					<div class="smallerCircleGreen iconCircle">
						<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_HOME_ADDWINDOW'|getTranslatedString:$MODULE}">add</i>
					</div>
				</span>

				<div id="addWidgetDropDown" class="dropdown-menu"> {*<!-- crmv@18756 -->*}
					<ul class="widgetDropDownList">
						<li>
							<a href="javascript:chooseType('Module');setFilter($('selmodule_id'));" class="drop_down" id="addmodule">
								{$MOD.LBL_HOME_MODULE}
							</a>
						</li>
						{if $ALLOW_RSS eq "yes"}
							<li>
								<a href="javascript:chooseType('RSS');showFloatingDiv('addWidgetsDiv', null, {ldelim}modal:true{rdelim});" class="drop_down" id="addrss">
									{$MOD.LBL_HOME_RSS}
								</a>
							</li>
						{/if}
						{* crmv@30014 removed dashboards, add charts *}
						{if $ALLOW_CHARTS eq "yes"}
							<li>
								<a href="javascript:chooseType('Charts');" class="drop_down" id="addchart">
									{$APP.SINGLE_Charts}
								</a>
							</li>
						{/if}
						{* crmv@30014e *}
						<!-- this has been commented as some websites are opening up in full page (they have a target="_top")-->
						<li>
							<a href="javascript:chooseType('URL');showFloatingDiv('addWidgetsDiv', null, {ldelim}modal:true{rdelim});" class="drop_down" id="addURL">
								{$MOD.LBL_URL}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</li>{*crmv@23264*}
	{elseif $CHECK.EditView eq 'yes' || ($MODULE eq 'Projects' && ( $ISPROJECTADMIN eq 'yes' || $ISPROJECTLEADER eq 'yes'))}
		{* crmv@2963m *}
		{if $MODULE eq 'Messages'}
			<li>
				{*
				<div class="smallerCircle iconCircle">
					<a href="javascript:;" onclick="OpenCompose('','create');" style="text-decoration:none;"><i class="vteicon" style="vertical-align:middle">add</i>&nbsp;{'LBL_COMPOSE'|getTranslatedString:'Messages'}</a>
				</div>
				*}
				<div class="smallerCircleGreen iconCircle" onclick="OpenCompose('','create');">
					<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_COMPOSE'|getTranslatedString:'Messages'}">add</i>
				</div>
			</li>
			<li>
				{*
				<div class="smallerCircle iconCircle">
					<a href="javascript:;" onclick="fetch();" style="text-decoration:none;">
						<i class="vteicon" id="fetchImg" style="vertical-align:middle;">autorenew</i>
						{include file="LoadingIndicator.tpl" LIID="fetchImgLoader" LIEXTRASTYLE="display:none" LIOLDMODE=true}
						&nbsp;{'LBL_FETCH'|getTranslatedString:'Messages'}
					</a>
				</div>
				*}
				<a href="javascript:;" onclick="fetch();" style="text-decoration:none;">
					<i data-toggle="tooltip" data-placement="bottom" class="vteicon" title="{'LBL_FETCH'|getTranslatedString:'Messages'}" id="fetchImg">autorenew</i>
					{include file="LoadingIndicator.tpl" LIID="fetchImgLoader" LIEXTRASTYLE="display:none" LIOLDMODE=true}
				</a>
			</li>
			<li>
				<div class="smallerCircle iconCircle">
					<a href="javascript:;" onclick="openPopup('index.php?module=Messages&action=MessagesAjax&file=Settings/index','','','auto',600,500);"><i class="vteicon" title="{$APP.LBL_SETTINGS}" style="vertical-align:middle;">settings</i></a>
				</div>
			</li>
		{* crmv@2963me *}
		{elseif $MODULE neq 'Calendar' && $HIDE_BUTTON_CREATE neq true} {* crmv@30014 *}
			<li>
				<div class="smallerCircleGreen iconCircle">
					<a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}&folderid={$FOLDERID}">
						<i data-toggle="tooltip" data-placement="bottom" class="vteicon" title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}">add</i>
					</a>
				</div>
			</li> {* crmv@30967 *}
		{/if}
	{/if}
	{* crmv@81193 *}
	{if $MODULE eq 'Calendar' && $REQUEST_ACTION eq 'index'}
		<li id="CalendarAddButton"></li>	{* crmv@20480 *}
	{/if}
	{* crmv@81193e *}
	{* crmv@29386 *}
	{if $MODULE eq 'Webforms'}
		<li>
			<div class="smallerCircle iconCircle">
				<a href="index.php?module={$MODULE}&action=WebformsEditView&return_action=DetailView&parenttab={$CATEGORY}"><i class="vteicon add" data-toggle="tooltip" data-placement="bottom" title="{$APP.LBL_CREATE_BUTTON_LABEL} {$SINGLE_MOD|getTranslatedString:$MODULE}">add</i></a>
			</div>
		</li>
	{/if}
	{* crmv@29386e *}
	{if $MODULE eq 'Reports'}
		<li>
			<div class="smallerCircle iconCircle">
				<a href="javascript:;" onclick="Reports.createNew('{$FOLDERID}')"><i class="vteicon" data-toggle="tooltip" data-placement="bottom" title="{'LBL_CREATE_REPORT'|@getTranslatedString:$MODULE}">add</i></a>
			</div>		
		</li> {* crmv@97237 *}
		{* crmv@29686 crmv@30967 - removed *}
	{/if}
	{if $MODULE eq 'Home' && $REQUEST_ACTION eq 'index'}
		<li>
			<div class="smallerCircle iconCircle">
				<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" onClick="showFloatingDiv('changeLayoutDiv', null, {ldelim}modal:true{rdelim});" title="{'LBL_HOME_LAYOUT'|getTranslatedString:$MODULE}">view_module</i>
			</div>
		</li>
		<li>
			<div class="smallerCircle iconCircle">
				<a href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}&scroll=home_page_components&return_module=Home&return_action=index'>
					<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link"  title="{$APP.LBL_SETTINGS} {$MODULELABEL}">settings</i>
				</a>
			</div>
		</li>
	{/if}
	{* crmv@20209 crmv@81193 *}
	{if $MODULE eq 'Calendar' && $REQUEST_ACTION eq 'index'}
		{assign var=scroll value="LBL_CALENDAR_CONFIGURATION"|getTranslatedString:"Users"}
		{assign var=scroll value=$scroll|replace:' ':'_'}
		{if $smarty.cookies.crmvWinMaxStatus eq 'close'}
			{assign var="minStatus" value=""}
		{else}
			{assign var="minStatus" value="maximize"}
		{/if}
		<li>
			<div class="smallerCircle iconCircle">
				<a href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}&scroll={$scroll}&return_module=Calendar&return_action=index'>
					<i class="vteicon" data-toggle="tooltip" data-placement="bottom" title="{$APP.LBL_SETTINGS} {$MODULELABEL}">settings</i>
				</a>
			</div>
		</li>
	{/if}
	{* crmv@20209e crmv@81193e *}
	
	{* crmv@24189 *}
	{if $REQUEST_ACTION neq 'UnifiedSearch'}
		{$SDK->getMenuButton('contestual',$MODULE)}
		{$SDK->getMenuButton('contestual',$MODULE,$REQUEST_ACTION)}
	{/if}
	{* crmv@24189e *}
	
	{* crmv@20640 crmv@105193 *}
	{if $CAN_ADD_HOME_BLOCKS || $CAN_ADD_HOME_VIEWS || $CAN_DELETE_HOME_VIEWS}
		{assign var="CAN_EDIT_HOMEVIEW" value="yes"}
	{else}
		{assign var="CAN_EDIT_HOMEVIEW" value="no"}
	{/if}
	{if $CHECK.moduleSettings eq 'yes' && ($IS_ADINN == 1 || $CAN_EDIT_HOMEVIEW == 'yes')}
		<li class="dropdown" id="moduleSettingsTd">
			{if $IS_ADMIN eq 1}
				<span data-toggle="dropdown">
					<div class="smallerCircle iconCircle">
						<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_CONFIGURATION'|getTranslatedString:'Settings'}">settings</i>
					</div>
				</span>
			{else}
				<span data-toggle="dropdown">
					<div class="smallerCircle iconCircle">
						<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{$APP.LBL_CONFIG_PAGE}" onclick="ModuleHome.toggleEditMode()">settings</i>
					</div>
				</span>
			{/if}
			<div class="dropdown-menu" id="moduleSettings_sub" style="width:200px;">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr><td><a class="drop_down" href="javascript:;" onclick="ModuleHome.toggleEditMode()">{$APP.LBL_CONFIG_PAGE}</a></td></tr>
					{if $IS_ADMIN eq 1}
						<tr><td><a class="drop_down" href="index.php?module=Settings&amp;action=ModuleManager&amp;module_settings=true&amp;formodule={$MODULE}&amp;parenttab=Settings">{$APP.LBL_ADVANCED}</a></td></tr>
					{/if}
				</table>
			</div>
      	</li>
		<li id="moduleSettingsResetTd" style="display:none">
			<a href="javascript:;" onclick="ModuleHome.toggleEditMode()">
				<div class="smallerCircle iconCircle">
					<i class="vteicon md-link" data-toggle="tooltip" data-placement="bottom" title="{$APP.LBL_DONE_BUTTON_TITLE}">settings</i>
				</div>
				<span style="vertical-align:6px">{$APP.LBL_DONE_BUTTON_TITLE}</span>
			</a>
		</li>
	{/if}
	{* crmv@20640e crmv@105193e *}
	
	{if $MODULE neq 'Home' && $MODULE neq 'Messages' && ($REQUEST_ACTION eq 'index' || $REQUEST_ACTION eq 'ListView')}
	<li>
		<div class="dropdown">
			<div class="smallerCircle iconCircle dropdown-toggle" data-toggle="dropdown">
				<i data-toggle="tooltip" data-placement="bottom" class="vteicon md-link" title="{'LBL_OTHER'|getTranslatedString:'Users'}">reorder</i>
			</div>
			<div class="dropdown-menu dropdown-menu-left" style="box-shadow:none;">
				<div class="crmvDiv" style="max-height:500px; overflow-y:auto; padding:0px 5px 5px 5px; display:table; width:230px;">
				<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
				
					{if !empty($BUTTONS)}
						{foreach key=button_check item=button_label from=$BUTTONS}
							{if $button_check eq 'del'}
								<div class="turboliftEntry1 btn delete" style="display:block" onclick="return massDelete('{$MODULE}')">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 's_mail'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return eMail('{$MODULE}',this)">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 's_fax'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return Fax('{$MODULE}',this)">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 's_sms'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return mailer_export()">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 's_cmail'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return mailer_export()">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 'c_status'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return change(this,'changestatus')">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 'mailer_exp'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return mailer_export()">
									<div>
										{$button_label}
									</div>
								</div>
							{elseif $button_check eq 'mass_edit'}
								<div class="turboliftEntry1 btn" style="display:block" onclick="return mass_edit(this, 'massedit', '{$MODULE}', '{$CATEGORY}')">
									<div>
										{$button_label}
									</div>
								</div>
		                     {/if}
						{/foreach}
						<div class="turboliftEntry1 btn" style="display:block" onClick="selectAllIds();">
							{if ($ALL_IDS eq 1)}
								<div id="select_all_button_top" value="{$APP.LBL_UNSELECT_ALL_IDS}">{$APP.LBL_UNSELECT_ALL_IDS}</div>
							{else}
								<div id="select_all_button_top" value="{$APP.LBL_SELECT_ALL_IDS}">{$APP.LBL_SELECT_ALL_IDS}</div>
							{/if}
						</div>
					{/if}
					
					{* vtlib customization: Custom link buttons on the List view basic buttons *}
					{if $CUSTOM_LINKS && $CUSTOM_LINKS.LISTVIEWBASIC}
						{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEWBASIC}
							{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
							{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
							{if $customlink_label eq ''}
								{assign var="customlink_label" value=$customlink_href}
							{else}
								{* Pickup the translated label provided by the module *}
								{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
							{/if}
							<div class="turboliftEntry1 btn" style="display:block" onclick="{$customlink_href}">
								<div>
									{$customlink_label}
								</div>
							</div>
						{/foreach}
					{/if}
					
					{* vtlib customization: Custom link buttons on the List view *}
					{if $CUSTOM_LINKS && !empty($CUSTOM_LINKS.LISTVIEW)}
						{foreach item=CUSTOMLINK from=$CUSTOM_LINKS.LISTVIEW}
							{assign var="customlink_href" value=$CUSTOMLINK->linkurl}
							{assign var="customlink_label" value=$CUSTOMLINK->linklabel}
							{if $customlink_label eq ''}
								{assign var="customlink_label" value=$customlink_href}
							{else}
								{* Pickup the translated label provided by the module *}
								{assign var="customlink_label" value=$customlink_label|@getTranslatedString:$CUSTOMLINK->module()}
							{/if}
							<div class="turboliftEntry1 btn" style="display:block" onclick="{$customlink_href}">
								<div>
									{$customlink_label}
								</div>
							</div>
						{/foreach}
					{/if}
					
					{* vtlib customization: Hook to enable import/export button for custom modules. Added CUSTOM_MODULE *}
					{if $MODULE eq 'Assets' || $MODULE eq 'ServiceContracts' || $MODULE eq 'Vendors' || $MODULE eq 'HelpDesk' || $MODULE eq 'Contacts' || $MODULE eq 'Leads' || $MODULE eq 'Accounts' || $MODULE eq 'Potentials' || $MODULE eq 'Products' || $MODULE eq 'Services' || $MODULE eq 'Calendar' || $CUSTOM_MODULE eq 'true'} {* crmv@32465 *}
				   		{if $CHECK.Import eq 'yes' && $MODULE neq 'Calendar'}
							<div class="turboliftEntry1 btn" style="display:block" onclick="location.href='index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}'">
								<div class="row no-gutter">
									<div class="col-sm-12">
										<div class="col-sm-6 vcenter text-left">
											<span>{$APP.LBL_IMPORT}</span>
										</div><!-- 
										 --><div class="col-sm-6 vcenter text-right">
											<i class="vteicon md-text">file_download</i>
										</div>
									</div>
								</div>
							</div>
						{elseif  $CHECK.Import eq 'yes' && $MODULE eq 'Calendar' && $REQUEST_ACTION eq 'ListView'} {* crmv@104881 *}
							<div class="turboliftEntry1 btn" style="display:block" onclick="showFloatingDiv('CalImport', this);">
								<div class="row no-gutter">
									<div class="col-sm-12">
										<div class="col-sm-6 vcenter text-left">
											<span>{$APP.LBL_IMPORT}</span>
										</div><!-- 
										 --><div class="col-sm-6 vcenter text-right">
											<i class="vteicon md-text">file_download</i>
										</div>
									</div>
								</div>
							</div>
						{/if}
						{if $CHECK.Export eq 'yes' && $MODULE neq 'Calendar'}
							<div class="turboliftEntry1 btn" style="display:block" onclick="return selectedRecords('{$MODULE}','{$CATEGORY}')">
								<div class="row no-gutter">
									<div class="col-sm-12">
										<div class="col-sm-6 vcenter text-left">
											<span>{$APP.LBL_EXPORT}</span>
										</div><!-- 
										 --><div class="col-sm-6 vcenter text-right">
											<i class="vteicon md-text">file_upload</i>
										</div>
									</div>
								</div>
							</div>
						{elseif  $CHECK.Export eq 'yes' && $MODULE eq 'Calendar' && $REQUEST_ACTION eq 'ListView'}  {* crmv@104881 *}
							<div class="turboliftEntry1 btn" style="display:block" onclick="showFloatingDiv('CalExport', this);">
								<div class="row no-gutter">
									<div class="col-sm-12">
										<div class="col-sm-6 vcenter text-left">
											<span>{$APP.LBL_EXPORT}</span>
										</div><!-- 
										 --><div class="col-sm-6 vcenter text-right">
											<i class="vteicon md-text">file_upload</i>
										</div>
									</div>
								</div>
							</div>
						{/if}
					{elseif $MODULE eq 'Documents' && $CHECK.Export eq 'yes' && $REQUEST_ACTION eq 'ListView'} {* crmv@30967 *}
						<div class="turboliftEntry1 btn" style="display:block" onclick="return selectedRecords('{$MODULE}','{$CATEGORY}')">
							<div class="row no-gutter">
								<div class="col-sm-12">
									<div class="col-sm-6 vcenter text-left">
										<span>{$APP.LBL_EXPORT}</span>
									</div><!-- 
									 --><div class="col-sm-6 vcenter text-right">
										<i class="vteicon md-text">file_upload</i>
									</div>
								</div>
							</div>
						</div>
					{/if}
					{* crmv@8719 *}
					{if $MODULE eq 'Contacts' || $MODULE eq 'Leads' || $MODULE eq 'Accounts'|| $MODULE eq 'Products'|| $MODULE eq 'Potentials'|| $MODULE eq 'HelpDesk'|| $MODULE eq 'Vendors' || $CUSTOM_MODULE eq 'true'}
						{if $CHECK.DuplicatesHandling eq 'yes'}
							<div class="turboliftEntry1 btn" style="display:block" onclick="MergeFieldsAjax()">
								<div class="row no-gutter">
									<div class="col-sm-12">
										<div class="col-sm-6 vcenter text-left">
											<span>{$APP.LBL_FIND_DUPLICATES}</span>
										</div><!-- 
										 --><div class="col-sm-6 vcenter text-right">
											<i class="vteicon md-text">pageview</i>
										</div>
									</div>
								</div>
							</div>
						{/if}
					{/if}
					
				</td></tr></table>
				</div>
			</div>
		</div>
	</li>
	{/if}
	
</ul>