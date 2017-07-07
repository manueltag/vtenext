<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->

<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> <!-- crmv@30683 -->
	<tbody>
        <tr>
            <td valign="top"></td>
            <td class="showPanelBg" style="padding: 5px;" valign="top" width="100%"> <!-- crmv@30683 -->

                <div align="center">
                    {include file='Buttons_List.tpl'} {* crmv@30683 *}

                    <!-- TOP -->
                    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="settingsSelUITopLine">
                        <tr>
                            <td width="50" rowspan="2" valign="top"><img src="{$IMAGE_PATH}ico-roles.gif" width="48" height="48" border="0" /></td>
                            <td class=heading2 valign=bottom><b> {$MOD.LBL_SETTINGS} > <a href="index.php?module=Settings&action=listroles&parenttab=Settings">{$CMOD.LBL_ROLES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$ROLE_NAME}&quot; </b></td> <!-- crmv@30683 -->
                        </tr>
                        <tr>
                            <td valign=top class="small">{$CMOD.LBL_VIEWING} {$CMOD.LBL_PROPERTIES} &quot;{$ROLE_NAME}&quot; {$MOD.LBL_LIST_CONTACT_ROLE} </td>
                        </tr>
                    </table>

                    <br>

                    <div style="padding:10px">

                        <form id="form" name="roleView" action="index.php" method="post" onsubmit="VtigerJS_DialogBox.block();">
                            <input type="hidden" name="module" value="Settings">
                            <input type="hidden" name="action" value="createrole">
                            <input type="hidden" name="parenttab" value="Settings">
                            <input type="hidden" name="returnaction" value="RoleDetailView">
                            <input type="hidden" name="roleid" value="{$ROLEID}">
                            <input type="hidden" name="mode" value="edit">

                            <!-- TITLE AND BUTTON -->
                            <table border="0" cellspacing="0" cellpadding="5" width="100%" class="">
                                <tr>
                                    <td class="big"><strong>{$CMOD.LBL_PROPERTIES} &quot;{$ROLE_NAME}&quot;</strong></td>
                                    <td><div align="right"><input value="   {$APP.LBL_EDIT_BUTTON_LABEL}   " title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmButton small edit" type="submit" name="Edit"></td>
                                </tr>
                            </table>
                        </form>

                        <!-- ROLE TABLE -->
                        <table class="table" width="100%"  border="0" cellspacing="0" cellpadding="5">

                            <tr class="small">
                                <td width="15%" class="small"><strong>{$CMOD.LBL_ROLE_NAME}</strong></td>
                                <td width="85%" class="">{$ROLE_NAME}</td>
                            </tr>

                            <tr class="small">
                                <td class="small"><strong>{$CMOD.LBL_REPORTS_TO}</strong></td>
                                <td class="">{$PARENTNAME}</td>
                            </tr>
                            
						</table>

						<table class="table" width="70%"  border="0" cellspacing="0" cellpadding="5">

	                        <tr class="small">
	                            <td colspan="2" class="">
	                                <div align="left"><strong>{$CMOD.LBL_ASSOCIATED_PROFILES}</strong></div>
	                            </td>
	                        </tr>
	
	                        {foreach item=elements from=$ROLEINFO.profileinfo}
	                            <tr class="small">
	                                <td width="16"><div align="center"></div></td>
	                                <td><a href="index.php?module=Settings&action=profilePrivileges&parenttab=Settings&profileid={$elements.0}&mode=view">{$elements.1}</a><br></td>
	                            </tr>
	                        {/foreach}
	
	                        {if $ROLEINFO.profileinfo_mobile neq '' && count($ROLEINFO.profileinfo_mobile) > 0}
	                            <tr class="small">
	                                <td colspan="2" class="">
	                                    <div align="left"><strong>{$CMOD.LBL_ASSOCIATED_PROFILE_MOBILE} :</strong></div>
	                                </td>
	                            </tr>
	
	                            {foreach item=elements from=$ROLEINFO.profileinfo_mobile}
	                                <tr class="small">
	                                    <td width="16"><div align="center"></div></td>
	                                    <td><a href="index.php?module=Settings&action=profilePrivileges&parenttab=Settings&profileid={$elements.0}&mode=view">{$elements.1}</a><br></td>
	                                </tr>
	                            {/foreach}
	                        {/if}
	
	                        <tr class="small">
	                            <td colspan="2" class="">
	                                <div align="left"><strong>{$CMOD.LBL_ASSOCIATED_USERS}</strong></div>
	                            </td>
	                        </tr>
	
	                        {if $ROLEINFO.userinfo.0 neq ''}
	                            {foreach item=elements from=$ROLEINFO.userinfo}
	                                <tr class="small">
	                                    <td width="16"><div align="center"></div></td>
	                                    <td><a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$elements.0}">{$elements.1}</a><br></td>
	                                </tr>
	                            {/foreach}
	                        {/if}
	                    </table>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
