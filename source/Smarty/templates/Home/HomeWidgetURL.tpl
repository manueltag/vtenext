<!--/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/-->
{* crmv@3079m *}
{* crmv@29079 *}
{if $STUFFTYPE eq 'SDKIframe'}
	{assign var="URL" value="$URL&stuffid=$WIDGETID"}
{/if}
<iframe id="url_contents_{$WIDGETID}" src="{$URL}" frameborder="0" scrolling="auto" width="100%" sandbox="allow-same-origin allow-scripts allow-popups allow-forms"></iframe> {* crmv@105924 *}
{if $URL|strpos:'&widget=DetailViewBlockCommentWidget' neq false}
	{assign var="URL_TEMP" value=$URL|cat:"&target_frame=url_contents_"|cat:$WIDGETID|cat:'&indicator=refresh_'|cat:$WIDGETID}
	{assign var="URL" value=""}
	<script type="text/javascript" id="loadModCommentsNewsScript_{$WIDGETID}">
		loadModCommentsNews(ModCommentsCommon.default_number_of_news,'url_contents_{$WIDGETID}','refresh_{$WIDGETID}');
		jQuery('#url_contents_{$WIDGETID}').attr('height','610px');
	</script>
{elseif $STUFFTYPE eq 'Iframe'}
	<script type="text/javascript" id="eval_script">
		jQuery('#url_contents_{$WIDGETID}').css('height','460px');
	</script>
{elseif $STUFFTYPE eq 'SDKIframe'}
	<script type="text/javascript" id="eval_script">
		jQuery('#url_contents_{$WIDGETID}').css('height',jQuery('#stuff_{$WIDGETID} div.MatrixBorder').innerHeight()-5);
	</script>
	</script>
{else}
	<script type="text/javascript" id="eval_script">
		jQuery('#url_contents_{$WIDGETID}').css('height',jQuery('#stuff_{$WIDGETID} div.MatrixBorder').innerHeight()-5);
		jQuery('#url_contents_{$WIDGETID}').css('height',jQuery('#stuff_{$WIDGETID} div.MatrixBorderURL').innerHeight()-5);
	</script>
{/if}
{* crmv@29079e *}
{* crmv@3079me *}