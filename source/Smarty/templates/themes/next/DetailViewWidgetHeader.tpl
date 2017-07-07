{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}

<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td class="dvInnerHeader">
			{assign var=WIDGET_TITLE value=$CUSTOM_LINK_DETAILVIEWWIDGET|vtlib_widget_title}
			{if empty($WIDGET_TITLE)}
				{assign var=WIDGET_TITLE value=$CUSTOM_LINK_DETAILVIEWWIDGET->linklabel|getTranslatedString:$MODULE}
			{/if}
			<b>{$WIDGET_TITLE}</b>
		</td>
	</tr>
</table>
