{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/
-->*}
{* crmv@92218 *}

{if $CHOOSEN_ENCODING == 'AUTO'}
	<span class="small">{'LBL_DETECTED_ENCODING'|@getTranslatedString:$MODULE}</span>&nbsp;&nbsp;
	<select name="use_file_encoding" id="use_file_encoding" class="small" onchange="ImportJs.changeEncoding(jQuery(this).val())">
		{foreach key=_FILE_ENCODING item=_FILE_ENCODING_LABEL from=$SUPPORTED_FILE_ENCODING}
			{if $DETECTED_ENCODING == $_FILE_ENCODING}
				<option value="{$_FILE_ENCODING}" selected="selected">{$_FILE_ENCODING_LABEL|@getTranslatedString:$MODULE}</option>
			{else}
				<option value="{$_FILE_ENCODING}">{$_FILE_ENCODING_LABEL|@getTranslatedString:$MODULE}</option>
			{/if}
		{/foreach}
	</select>
	&nbsp;&nbsp;
{/if}