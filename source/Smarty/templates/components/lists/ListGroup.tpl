{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the list
CLASS: @string the class of the list
ENTRIES: @array the items of the list (ListGroupItem.tpl)
------------------------------------- *}

<div {if !empty($ID)}id="{$ID}"{/if} class="list-group{if !empty($CLASS)} {$CLASS}{/if}">

	{if !empty($ENTRIES) && is_array($ENTRIES)}
	
		{assign var=count_entries value=$ENTRIES|@count}
		
		{foreach from=$ENTRIES item=entry name=entries_loop}
		
			{assign var=item_id value=$entry.id}
			{assign var=item_class value=$entry.class}
			{assign var=item_type value=$entry.type}
			{assign var=item_title value=$entry.title}
			{assign var=item_content value=$entry.content}
			{assign var=item_src value=$entry.src}
			{assign var=item_icon value=$entry.icon}
			
			{if $item_type eq 'image'}
				{include file="components/lists/ListGroupItem.tpl" ID=$item_id CLASS=$item_class TYPE=$item_type TITLE=$item_title CONTENT=$item_content SRC=$item_src}
			{elseif $item_type eq 'icon'}
				{include file="components/lists/ListGroupItem.tpl" ID=$item_id CLASS=$item_class TYPE=$item_type TITLE=$item_title CONTENT=$item_content ICON=$item_icon}
			{elseif $item_type eq 'check'}
				{include file="components/lists/ListGroupItem.tpl" ID=$item_id CLASS=$item_class TYPE=$item_type TITLE=$item_title CONTENT=$item_content}
			{/if}
			
			{if $smarty.foreach.entries_loop.iteration lt $count_entries}
				<div class="list-group-separator"></div>
			{/if}
			
		{/foreach}
	
	{/if}
	
</div>
