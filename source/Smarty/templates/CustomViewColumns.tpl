{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
<table cellspacing="5" cellpadding="0" width="100%">
	<tr>
		<td>
			<div class="dvtCellInfo">
				<select name="column1" id="column1" onChange="checkDuplicate();" class="detailedViewTextBox">
                	<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN1}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN1}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column2" id="column2" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN2}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN2}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column3" id="column3" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN3}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN3}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column4" id="column4" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN4}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN4}
				</select>
			</div>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>
			<div class="dvtCellInfo">
				<select name="column5" id="column5" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN5}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN5}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column6" id="column6" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN6}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN6}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column7" id="column7" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN7}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN7}
				</select>
			</div>
		</td>
		<td>
			<div class="dvtCellInfo">
				<select name="column8" id="column8" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN8}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN8}
				</select>
			</div>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>
			<div class="dvtCellInfo">
				<select name="column9" id="column9" onChange="checkDuplicate();" class="detailedViewTextBox">
					<option value="">{$MOD.LBL_NONE}</option>
					{foreach item=filteroption key=label from=$CHOOSECOLUMN9}
						<optgroup label="{$label}" class=\"select\" style=\"border:none\">
						{foreach item=text from=$filteroption}
							{assign var=option_values value=$text.text}
							<option {$text.selected} value={$text.value}>
							{if $MOD.$option_values neq ''}
								{if $DATATYPE.0.$option_values eq 'M'}
									{$MOD.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
								{else}
                                        {$MOD.$option_values}
                                {/if}
                        	{elseif $APP.$option_values neq ''}
                                {if $DATATYPE.0.$option_values eq 'M'}
                                	{$APP.$option_values}   {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$APP.$option_values}
                                {/if}
                        	{else}
                                {if $DATATYPE.0.$option_values eq 'M'}
                            		{$option_values}    {$APP.LBL_REQUIRED_SYMBOL}
                                {else}
									{$option_values}
                                {/if}
							{/if}
							</option>
						{/foreach}
						</optgroup>
					{/foreach}
					{$CHOOSECOLUMN9}
				</select>
			</div>
		</td>
	     <td>&nbsp;</td>
	     <td>&nbsp;</td>
	     <td>&nbsp;</td>
	     <td>&nbsp;</td>
	</tr>
</table>