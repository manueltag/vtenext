{include file="modules/M/generic/Header.tpl"}

<link rel="stylesheet" type="text/css" media="all" href="../../jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="../../jscalendar/calendar.js"></script>
<script type="text/javascript" src="../../jscalendar/lang/calendar-it.js"></script>
<script type="text/javascript" src="../../jscalendar/calendar-setup.js"></script>
{if $MODULE eq 'Calendar'}
	<script type="text/javascript" src="../../modules/{$MODULE}/{$MODULE}.js"></script>
	<script type="text/javascript" src="../../modules/{$MODULE}/script.js"></script>
{/if}

<script type="text/javascript">
var gVTModule = '{$MODULE}';
var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
var userDateFormat = '{$userDateFormat}';
</script>

<body>

{assign var=MODULE value=$ENTITY_NAME}
{if $MODULE eq 'Calendar' || $MODULE eq 'Events'}
{*<!-- 
{if $ENTITY_NAME eq 'Events'}
	onsubmit="return check_form();" 
{else}
	onsubmit="if (maintask_check_form()) return formValidate();"
{/if} >
-->*}
<form name="EditView" method="POST" action="index.php">
<input type="hidden" name="time_start" id="time_start">
<input type="hidden" name="view" value="{$view}">
<input type="hidden" name="hour" value="{$hour}">
<input type="hidden" name="day" value="{$day}">
<input type="hidden" name="month" value="{$month}">
<input type="hidden" name="year" value="{$year}">
<input type="hidden" name="viewOption" value="{$viewOption}">
<input type="hidden" name="subtab" value="{$subtab}">
<input type="hidden" name="maintab" value="{$maintab}">
{/if}

{include file='EditViewHidden.tpl'}

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr class="toolbar">
	<td width="5%">
	{if $selected_skin eq 'iPhone.css'}
		<a class="backButton" href="javascript:history.back();">{$APP.LBL_CANCEL_BUTTON_LABEL}</a>
	{else}
		<a class="link" href="javascript:history.back();"><img src="resources/images/iconza/royalblue/left_arrow_16x16.png" border="0"></a>
	{/if}
	</td>
	<td width="90%" align="left"><h1 class='page_title'>{$_MODULE->label}</h1></td>	
	<td width="5%" align="right" nowrap="nowrap">
		{if $selected_skin eq 'iPhone.css'}
		 	<input type="submit" class="toolButton" onclick="this.form.action.value='Save'; {if $MODULE neq 'Calendar' && $MODULE neq 'Events'}return formValidate();{/if}" name="button" value="{$APP.LBL_SAVE_LABEL}">
		{else}	
			<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="this.form.action.value='Save'; {if $MODULE neq 'Calendar'}return formValidate();{/if}" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
		{/if}
	</td>
</tr>
<tr>
	<td colspan="3">	
	
		<table width=100% cellpadding=5 cellspacing=0 border=0 class="table_detail">
			{foreach key=header item=data from=$_RECORD}
				<tr>
					<td colspan=2 class="hdrlabel">{$header}</td>
				</tr>
				{assign var="fromlink" value=""}
				{foreach key=label item=subdata from=$data}
					{foreach key=mainlabel item=maindata from=$subdata}
						{if $header eq 'Product Details'}
							<tr>
						{else}
							<tr style="height:25px">
						{/if}
						{assign var="uitype" value="$maindata[0][0]"}
						{assign var="fldlabel" value="$maindata[1][0]"}
						{assign var="fldlabel_sel" value="$maindata[1][1]"}
						{assign var="fldlabel_combo" value="$maindata[1][2]"}
						{assign var="fldname" value="$maindata[2][0]"}
						{assign var="fldvalue" value="$maindata[3][0]"}
						{assign var="secondvalue" value="$maindata[3][1]"}
						{assign var="thirdvalue" value="$maindata[3][2]"}
						{assign var="vt_tab" value="$maindata[4][0]"}
						{assign var="readonly" value="$maindata[5][0]"}
						{assign var="isadmin" value="$maindata[6][0]"}
						
						{if $readonly eq 99 and $isadmin eq 0}
							{assign var="fieldcount" value=$fieldcount+1}
							{include file="DisplayFieldsReadonly.tpl"}
						{elseif $readonly eq 100 and $isadmin eq 0}
							{include file="DisplayFieldsHidden.tpl"}
						{else}
							{assign var="fieldcount" value=$fieldcount+1}
							{include file='EditViewUI.tpl'}
						{/if}
						</tr>
					{/foreach}
				{/foreach}
			{/foreach}
		</table>
	
	</td>
</tr>
</table>

</form>

</body>

{include file="modules/M/generic/Footer.tpl"}