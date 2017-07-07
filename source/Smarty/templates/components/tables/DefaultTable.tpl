{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the table
CLASS: @string the class of the table
HOVER: @boolean true/false
STRIPED: @boolean true/false
BORDERED: @boolean true/false
CONDENSED: @boolean true/false
RESPONSIVE: @boolean true/false
HEADER_ROWS: @array the header rows of the table
BODY_ROWS: @array the body rows of the table
------------------------------------- *}

{assign var=table_class value="table"}

{if $HOVER eq true}
	{assign var=table_class value="$table_class table-hover"}
{/if}

{if $STRIPED eq true}
	{assign var=table_class value="$table_class table-striped"}
{/if}

{if $BORDERED eq true}
	{assign var=table_class value="$table_class table-bordered"}
{/if}

{if $CONDENSED eq true}
	{assign var=table_class value="$table_class table-condensed"}
{/if}

{if $RESPONSIVE eq true}
	<div class="table-responsive">
{/if}

<table {if !empty($ID)}id="{$ID}"{/if} class="{$table_class}{if !empty($CLASS)} {$CLASS}{/if}">
	
	{if !empty($HEADER_ROWS) && is_array($HEADER_ROWS)}
		<thead>
			<tr>
				{foreach from=$HEADER_ROWS item=row name=header_loop}
					
					{assign var=row_value value=$row.value}
					{assign var=row_class value=$col.class}
					
					<th{if !empty($row_class)}class="$row_class"{/if}>{$row_value}</th>
					
				{/foreach}
			</tr>
		</thead>
	{/if}
	
	{if !empty($BODY_ROWS) && is_array($BODY_ROWS)}
		<tbody>
			{foreach from=$BODY_ROWS item=row name=body_loop}
				<tr>
				{foreach from=$row item=col name=body_loop}
				
					{assign var=col_value value=$col.value}
					{assign var=col_class value=$col.class}
					
					<td{if !empty($col_class)}class="$col_class"{/if}>{$col_value}</td>
					
				{/foreach}
				</tr>
			{/foreach}
		</tbody>
	{/if}
	
</table>

{if $RESPONSIVE eq true}
	</div>
{/if}
