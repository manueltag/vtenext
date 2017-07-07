{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@OPER6288 crmv@102334 *}

<script language="JavaScript" type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/dtlviewajax.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="{"include/js/ListView.js"|resourcever}"></script>
<script language="JavaScript" type="text/javascript" src="include/js/KanbanView.js"></script>
<script language="javascript" type="text/javascript" src="include/js/jquery_plugins/slimscroll/jquery.slimscroll.min.js"></script>

{include file='Buttons_List.tpl'}
<div id="Buttons_List_Kanban">
	<table id="bl3" border=0 cellspacing=0 cellpadding=2 width=100% class="small">
		<tr height="34">
			<td align="right" width="100%" style="padding-right:5px;">
				<!-- Filters -->
                <table border=0 cellspacing=0 cellpadding=0 class="small"><tr>
					<td style="padding-right:20px" nowrap>
						<a href="index.php?module={$MODULE}&amp;action=HomeView&amp;modhomeid={$MODHOMEID}&viewmode=ListView"><i class="vteicon disabled" style="cursor:pointer" title="{'LBL_LIST'|getTranslatedString}" data-toggle="tooltip">view_headline</i></a>
						<i class="vteicon" title="Kanban" data-toggle="tooltip">view_column</i>
					</td>
                    <td>{$APP.LBL_VIEW}</td>
                    <td style="padding-left:5px;padding-right:5px">
						<div class="dvtCellInfo">
							<select name="viewname" id="viewname" class="detailedViewTextBox" onchange="showDefaultCustomView(this,'{$MODULE}','{$CATEGORY}','{$FOLDERID}','KanbanView')">{$CUSTOMVIEW_OPTION}</select>
						</div>
					</td>
					<td>
						{* crmv@21723 crmv@21827 crmv@22622 *}
						{if $HIDE_CUSTOM_LINKS neq '1'}
							<div class="dropdown">
								<span data-toggle="dropdown"><i data-toggle="tooltip" class="vteicon md-link" id="filter_option_img" title="{$APP.LBL_FILTER_OPTIONS}">settings</i></span>
								<div class="dropdown-menu" id="customLinks" style="width:150px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%">
										{* crmv@22259 *}
										{if $ALL eq 'All'}
											<tr>
												<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&duplicate=true&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_DUPLICATE}</a></td>
											</tr>
											<tr>
												<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_CREATEVIEW}</a></td>
											</tr>
									    {else}
											{if $CV_EDIT_PERMIT eq 'yes'}
												<tr>
													<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_EDIT}</a></td>
												</tr>
											{/if}
											<tr>
												<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&duplicate=true&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_DUPLICATE}</a></td>
											</tr>
											{if $CV_DELETE_PERMIT eq 'yes'}
												<tr>
													<td><a class="drop_down" href="javascript:confirmdelete('index.php?module=CustomView&action=Delete&dmodule={$MODULE}&record={$VIEWID}&parenttab={$CATEGORY}&return_action=index')">{$APP.LNK_CV_DELETE}</a></td>
												</tr>
											{/if}
											{if $CUSTOMVIEW_PERMISSION.ChangedStatus neq '' && $CUSTOMVIEW_PERMISSION.Label neq ''}
												<tr>
											   		<td><a class="drop_down" href="#" id="customstatus_id" onClick="ChangeCustomViewStatus({$VIEWID},{$CUSTOMVIEW_PERMISSION.Status},{$CUSTOMVIEW_PERMISSION.ChangedStatus},'{$MODULE}','{$CATEGORY}')">{$CUSTOMVIEW_PERMISSION.Label}</a></td>
											   	</tr>
											{/if}
											<tr>
												<td><a class="drop_down" href="index.php?module={$MODULE}&action=CustomView&parenttab={$CATEGORY}&return_action=index">{$APP.LNK_CV_CREATEVIEW}</a></td>
											</tr>
									    {/if}
									    {* crmv@22259e *}
									</table>
								</div>
							</div>
						{/if}
						{* crmv@21723e crmv@21827e crmv@22622e *}
						{* crmv@29617 crmv@42752 *}
						{if $HIDE_CV_FOLLOW neq '1'}
							{* crmv@83305 *}
							{assign var=FOLLOWIMG value=$VIEWID|@getFollowImg:'customview'}
							{if preg_match('/_on/', $FOLLOWIMG)}
								{assign var=FOLLOWTITLE value='LBL_UNFOLLOW'|getTranslatedString:'ModNotifications'}
							{else}
								{assign var=FOLLOWTITLE value='LBL_FOLLOW'|getTranslatedString:'ModNotifications'}
							{/if}
							<i data-toggle="tooltip" id="followImgCV" title="{$FOLLOWTITLE}" class="vteicon md-link" onClick="ModNotificationsCommon.followCV();">{$VIEWID|@getFollowCls:'customview'}</i>
							{* crmv@83305e *}
						{/if}
						{* crmv@29617e crmv@42752e *}
					</td> {* crmv@30967 *}
					{* crmv@7634 *}
					{if $OWNED_BY eq 0}
						<td style="padding-left:10px" nowrap>{$APP.LBL_ASSIGNED_TO}</td>
						<td style="padding-left:5px;"><div class="dvtCellInfo">{$LV_USER_PICKLIST}</div></td>
					{/if}
					{* crmv@7634e *}
				</tr></table>
			</td>
		</tr>
	</table>
</div>

<div id="KanbanViewContents" class="small" style="width:100%;position:relative;">
	{include file='KanbanGrid.tpl'}
</div>