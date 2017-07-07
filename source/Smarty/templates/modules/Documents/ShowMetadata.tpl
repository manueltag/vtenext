{* crmv@95157 *}

{if $ERROR}
<div>
	<p style="color:red"><b>{$ERROR}</b></p>
</div>
{/if}


{if $METADATA && $METADATA.properties}
<form id="metadataForm">
<table width="100%" border="0">
	<tr>
		<td class="dvInnerHeader" width="50%"><b>{"FieldName"|getTranslatedString:"Settings"}</b></td>
		<td class="dvInnerHeader" ><b>{"LBL_FIELD_VALUE"|getTranslatedString:"com_vtiger_workflow"}</b></td>
	</tr>
{foreach key=metakey item=metavalue from=$METADATA.properties}
	<tr>
		<td style="padding:4px;padding-left:10px">
			<div class="dvtCellLabel">
			{$metakey}
			</div>
			
		</td>
		<td>
			{if $META_EDITABLE}
				{include file="EditViewUI.tpl" uitype=1 fldvalue=$metavalue fldname=$metakey}
			{else}
				{include file="DetailViewFields.tpl" keyid=1 keyval=$metavalue}
			{/if}
		</td>
	</tr>
{/foreach}
</table>
</form>
{else}
	<p>No metadata</p>
{/if}


{if !$META_EDITABLE}
<script type="text/javascript">
{literal}
	(function() {
		jQuery('#metadataSaveButton').hide();
	})();
{/literal}
</script>
{/if}