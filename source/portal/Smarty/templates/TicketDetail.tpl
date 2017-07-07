{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
<!-- Javascrip -->
<script src="js/jquery-1.11.0.js"></script>
<script src="js/bootstrap.min.js"></script>

<script type="text/javascript" src="js/raty/lib/jquery.raty.js"></script> <!-- Star -->
<script type="text/javascript" src="js/raty/lib/pregis_script_raty.js"></script>
<script type="text/javascript" src="js/general.js"></script>

<div class="row rowbotton">
	<div class="col-md-10  col-sm-5 col-xs-4">
		<button align="left" class="btn btn-default" type="button" value="{'LBL_BACK_BUTTON'|getTranslatedString}" onclick="location.href='index.php?module=HelpDesk&action=index'"/>{'LBL_BACK_BUTTON'|getTranslatedString}</button>
	</div>	
<!-- 	<div class="col-md-2 col-sm-3 col-xs-4"> 
		<input class="btn btn-info" class="crmbutton small cancel" name="newticket" type="button" value="{'LBL_NEW_TICKET'|getTranslatedString}" onclick="location.href='index.php?module=HelpDesk&action=index&fun=newticket'"> 
	</div> -->

	{if $TICKETSTATUS !='Closed'|getTranslatedString}
	<div class="col-md-2 col-sm-4 col-xs-6 space" style="float:right">
		<button class="btn btn-primary" name="srch" type="button" value="{'LBL_CLOSE_TICKET'|getTranslatedString}" onClick="location.href='index.php?module=HelpDesk&action=index&fun=close_ticket&ticketid={$TICKETID}'">{'LBL_CLOSE_TICKET'|getTranslatedString}</button>
	</div>
	{/if}
</div>

{if $TICKETSTATUS eq 'Closed'|getTranslatedString}
	<div class="alert alert-danger alert-dismissable">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		{'Closed'|getTranslatedString}
	</div>
{/if}
	
<div class="row fieldrow">
<input type="hidden" name="ticketid" id="ticketid" value="{$TICKETID}">
	{foreach from=$FIELDLIST item=FIELD}
		{foreach from=$FIELD item=VALUE}
			<div class="col-md-12 here">
				<h3><small>{$VALUE.0}</small></h3>
			</div>
			<div class="col-md-12 linerow ">
			{if $VALUE.0 eq 'description'|getTranslatedString}
				<h5>{$VALUE.1}</h5>
			{elseif $VALUE.0 eq 'Valutation Support'|getTranslatedString}
				<input id="valuestars" type="hidden" value={$VALUE.1}>
				<div id="stars"></div>
			{elseif $VALUE.0 eq 'Status'|getTranslatedString}
				<input type="hidden" value="{$VALUE.1}" id="status">
				<h3>{$VALUE.1|getTranslatedString}</h3>
			{else}
				<h3>{$VALUE.1|getTranslatedString}</h3>
			{/if}
			</div>
		{/foreach}
	{/foreach}
</div>

{if $TICKETSTATUS != 'Closed'|getTranslatedString} <!--crmv@57342 -->

<form name="comments" action="index.php" method="post">
	<input type="hidden" name="module" value="HelpDesk">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="fun" value="detail">
	<input type="hidden" name="ticketid" id="ticketid" value="{$TICKETID}">

	<div class="panel panel-default">
        <div class="panel-heading" class="col-md-8">
        	<i class="fa fa-comments fa-fw"></i> 
				{'LBL_ADD_COMMENT'|getTranslatedString}

			<div class="btn-group pull-right" id="comments">
				<input class="btn btn-success" title="{'LBL_SUBMIT'|getTranslatedString}" accesskey="S" class="small" style="height: 30px" name="submit" value="{'LBL_SEND'|getTranslatedString}" type="submit" onclick="this.form.module.value='HelpDesk';this.form.action.value='index';this.form.fun.value='updatecomment'; if(trim(this.form.comments.value) != '')	return true; else return false;"/>
			</div>
        </div>
		<textarea name="comments" class="form-control" rows="12"></textarea>
	</div>
</form>
{/if}

<div class="panel panel-default">
	<div class="panel-heading">
    	<i class="fa fa-comments fa-fw"></i> 
		{'LBL_TICKET_COMMENTS'|getTranslatedString}
		<span class="badge">{$BADGE}</span>
		<div class="btn-group pull-right" id="comments">
			<a id="comments-close" type="button" class="btn btn-default btn-xs">
            	<i class="fa fa-angle-down"></i>
            </a>
		</div>
	</div>
	<div class="panel-body" id="panel-comments">
		{if !empty($COMMENTS)}
			<ul class="timeline">
				{foreach from=$COMMENTS key=num item=comm}
					{if $comm.ownertype eq 'customer'}
 					<li>
                		<div class="timeline-badge users">
							<i class="fa fa-check"></i>
                    	</div>
                    	<div class="timeline-panel">
                    		<div class="timeline-heading">
                        		<h4 class="timeline-title">{$num} {'LBL_COMMENT_BY'|getTranslatedString} : {$comm.owner}</h4>
                            	<p>
									<small class="text-muted">
										<i class="fa fa-clock-o"></i>
									 	{$comm.createdtime}
									</small>
                             	</p>
 							</div>
                        	<div class="timeline-body">
                        		<p>{$comm.comment}</p>
                        	</div>
						</div>
					</li>
					{else}
					<li class="timeline-inverted">
                 		<div class="timeline-badge vtecrm">
							<i class="fa-vtecrm"></i>
                  		</div>
						<div class="timeline-panel">
							<div class="timeline-heading">
                         		<h4 class="timeline-title">{$num} {'LBL_COMMENT_BY'|getTranslatedString} : {$comm.owner}</h4>
                       			<p>
									<small class="text-muted">
										<i class="fa fa-clock-o"></i>
										{$comm.createdtime}
									</small>
                             	</p>
							</div>
							<div class="timeline-body">
                       			<p>{$comm.comment}</p>
							</div>
						</div>
					</li>
					{/if}
				{/foreach}
			</ul>
		{else}
			<b>{'LBL_NO_COMMENTS'|getTranslatedString}</b>
		{/if}
	</div>
</div>

{if $FILES.0 != "#MODULE INACTIVE#"}
	<!--  Added for Attachment -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-upload"></i>
			{'LBL_ATTACHMENTS'|getTranslatedString}
		</div>
		{foreach from=$FILES item=FILE}
			<div class="row" id="TickestList">
				{if !empty($FILE.filelocationtype)} 	
					{if $FILE.filelocationtype eq "I" || $FILE.filelocationtype eq "B"}
						<a href="index.php?downloadfile=true&fileid={$FILE.fileid}&filename={$FILE.filename}&filetype={$FILE.filetype}&filesize={$FILE.filesize}&ticketid={$TICKETID}">
							{assign var=TMP value=$TICKETID|cat:'_'}
							{$FILE.filename|ltrim:$TMP}
						</a>
					{/if}
					{if $FILE.filelocationtype eq "E"}
						<a href="{$FILE.filename}">{$FILE.filename}</a>
					{/if}
				{else}
					{'NO_ATTACHMENTS'|getTranslatedString}
				{/if}
			</div>
		{/foreach}
	</div>
{/if}

{if !empty($UPLOADSTATUS)}
	<div class="row">
		<div class="alert alert-danger alert-dismissable uploader">
 			<button type="button" class="close" data-dismiss="alert">&times;</button>
 			{'LBL_FILE_UPLOADERROR'|getTranslatedString} {$UPLOADSTATUS}
		</div>		
	</div>
{/if}


{if $TICKETSTATUS != 'Closed'|getTranslatedString && $FILES.0 != "#MODULE INACTIVE#"}				
<form name="fileattachment" method="post" enctype="multipart/form-data" action="index.php">
	<input type="hidden" name="module" value="HelpDesk">
	<input type="hidden" name="action" value="index">
	<input type="hidden" name="fun" value="uploadfile">
	<input type="hidden" name="ticketid" value="{$TICKETID}">

	<div class="panel panel-default">
		<div class="panel-heading">
			<i class="fa fa-upload"></i>
			{'LBL_ATTACH_FILE'|getTranslatedString}
		</div>
						
	<div class="row Attachment">
		<div class="col-md-6 col-sm-8 col-xs-12">
			<input type="file" size="50" name="customerfile" class="detailedViewTextBox" onchange="validateFilename(this)" />
			<input type="hidden" name="customerfile_hidden"/>
		</div>
						
		<div class=" col-md-4  col-sm-4 col-xs-12">
			<input class="crmbutton small cancel" name="Attach" type="submit" value="{'LBL_ATTACH'|getTranslatedString}">
		</div>
	</div>
</form>
{/if}


<script>
// Closes Open comments
{literal}
$("#comments-close").click(function(e) {
e.preventDefault();
	$("#panel-comments").toggleClass("active");
});

// Stars crmv@80441
var punteggio = document.getElementById("valuestars").value; 
var status = document.getElementById("status").value; 
if(punteggio == 0.00 && (status == 'Closed' || status == 'Chiuso' || status == 'Richiesta risolta')){
	readOnlyValue = false;
}else{
	readOnlyValue = true;
}
jQuery(document).ready(function () {
	jQuery('#stars').raty({
		path: 'js/raty/lib/img/',
		score: punteggio,
		readOnly: readOnlyValue,
	});
});
if(punteggio == 0.00 && (status == 'Closed' || status == 'Chiuso' || status == 'Richiesta risolta')){
	$(document).ready(function () {
		var ticketid = document.getElementById("ticketid").value; 
		jQuery('#stars').raty({
			path: 'js/raty/lib/img',
			click: function(score, evt) {
				var res = getFile('index.php?mode=saveTicketStars&valutation_support='+score+'&ticketid='+ticketid);
				jQuery('#stars').raty({
					path: 'js/raty/lib/img/',
					readOnly: true,
				});
			}
		});
	});
}
//  crmv@80441e
{/literal}
</script>				