{*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@92272 crmv@97566 crmv@100972 *}

{include file='SmallHeader.tpl' HEADER_Z_INDEX=1 PAGE_TITLE="SKIP_TITLE" HEAD_INCLUDE="all" BUTTON_LIST_CLASS="navbar navbar-default"}

{php}SDK::checkJsLanguage();{/php}	{* crmv@sdk-18430 *}
{include file='CachedValues.tpl'}	{* crmv@26316 *}

<script src="{"include/js/dtlviewajax.js"|resourcever}" type="text/javascript"></script>
<script src="{"modules/Settings/ProcessMaker/resources/ProcessMakerScript.js"|resourcever}" type="text/javascript"></script>
<script src="modules/Settings/ProcessMaker/thirdparty/bpmn-js-seed/bower_components/bpmn-js/dist/bpmn-viewer.js"></script>

{* in order to enable ajax edit *}
<script type="text/javascript">
var gVTModule = 'Settings';
var default_charset = '{$default_charset}';
var fieldname = new Array();
var fieldlabel = new Array();
var fielddatatype = new Array();
var fielduitype = new Array();
</script>
<span id="crmspanid" style="display:none;position:absolute;" onmouseover="show('crmspanid');">
   <a class="edit" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<input type="hidden" id="hdtxt_IsAdmin" value="{php}global $current_user; (is_admin($current_user))?$v='1':$v='0'; echo $v;{/php}">
{* end *}

<form name="Edit" method="POST" action="index.php" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="module" value="Settings">
	<input type="hidden" name="action" value="ProcessMaker">
	<input type="hidden" name="mode" value="save">
	<input type="hidden" name="id" value="{$DATA.id}">
	<input type="hidden" name="vte_metadata" value="">
	<div style="padding:5px">
		<table border=0 cellspacing=0 cellpadding=3 width=100% class="listRow">
			<tr valign="top">
				<td width="50%">
					{include file="DetailViewUI.tpl" DIVCLASS="dvtCellInfoM" keyid=1 keymandatory=true label=$MOD.LBL_PROCESS_MAKER_RECORD_NAME AJAXEDITTABLEPERM=true keyfldname="pm_name" keyval=$DATA.name MODULE="Settings" keytblname=$TABLE_NAME ID=$DATA.id}
				</td>
				<td width="50%">
					{include file="DetailViewUI.tpl" DIVCLASS="dvtCellInfo" keyid=21 keymandatory=false label=$MOD.LBL_PROCESS_MAKER_RECORD_DESC AJAXEDITTABLEPERM=true keyfldname="pm_description" keyval=$DATA.description MODULE="Settings" keytblname=$TABLE_NAME ID=$DATA.id}
				</td>
			</tr>
			<tr valign="top">
				<td>
					{if $DATA.active eq 1}
						{assign var=ACTIVE value=$APP.yes}
					{else}
						{assign var=ACTIVE value=$APP.no}				
					{/if}
					{include file="DetailViewUI.tpl" DIVCLASS="dvtCellInfo" keyid=56 keymandatory=false label=$APP.Active AJAXEDITTABLEPERM=true keyfldname="pm_active" keyval=$ACTIVE MODULE="Settings" keytblname=$TABLE_NAME ID=$DATA.id AJAXSAVEFUNCTION="ProcessMakerScript.setActive"}
				</td>
				<td></td>
			</tr>
			<tr>
				<td colspan="2">
					{include file="FieldHeader.tpl" label=$MOD.LBL_PM_MODELER}
					{* <textarea id="xml" style="display:none">{$DATA.xml}</textarea> *}
					<textarea id="structure" style="display:none">{$DATA.structure}</textarea>
					<div id="canvas"></div>
				</td>
			</tr>
		</table>
	</div>
</form>

{literal}
<style type="text/css">
	.highlights-shape:not(.djs-connection) .djs-visual > :nth-child(1) {
		stroke: #2c80c8 !important;
	}
</style>
<script>
jQuery('#canvas').css('height', 2000);

(function(BpmnViewer) {
	// create viewer
	var bpmnViewer = new BpmnViewer({
		container: '#canvas'
	});
	
	// import function
	function importXML(xml) {
	
		// import diagram
		bpmnViewer.importXML(xml, function(err) {
			if (err) console.error('could not import BPMN 2.0 diagram', err);
	
			var canvas = bpmnViewer.get('canvas'),
				overlays = bpmnViewer.get('overlays'),
				elementRegistry = bpmnViewer.get('elementRegistry');

			// zoom to fit full viewport
			canvas.zoom('fit-viewport');
			
			var structure = {'shapes':{},'connections':{},'tree':{}};
			
			jQuery.each(elementRegistry.getAll(), function(index, object) {
			    if (object.constructor.name == 'Shape') {
			    	var id = object.id,
			    		type = ProcessMakerScript.formatType(object.type),
			    		dom_obj = jQuery('[data-element-id='+id+']'),
			    		subType = '';

					if (jQuery(object.businessObject.eventDefinitions).length > 0) {
						jQuery.each(object.businessObject.eventDefinitions, function(k,v){
							subType = ProcessMakerScript.formatType(v.$type);
						});
					}
					if (typeof(object.businessObject.cancelActivity) == 'boolean') {
						var cancelActivity = object.businessObject.cancelActivity;
					}
			    	if (typeof(elementRegistry.get(id+'_label')) == 'object') {
			    		var text = jQuery('[data-element-id='+id+'_label]').find('text').text();
			    	} else {
			    		var text = dom_obj.find('text').text();
			    	}
			    	
			    	var connections = {'incoming':{},'outgoing':{},'attachers':new Array()};
			    	if (object.incoming != undefined) {
				    	jQuery(object.incoming).each(function(index,connection){
				    		connections['incoming'][connection.id] = connection.source.id;
				    	});
				    }
			    	if (object.outgoing != undefined) {
				    	jQuery(object.outgoing).each(function(index,connection){
				    		connections['outgoing'][connection.id] = connection.target.id;
				    	});
				    }
				    if (object.attachers != undefined && jQuery(object.attachers).length > 0) {
				    	jQuery(object.attachers).each(function(index,attacher){
				    		connections['attachers'].push(attacher.id);
				    	});
				    }
				    structure['shapes'][id] = {'type':type,'text':text};
				    if (subType != '') structure['shapes'][id]['subType'] = subType;
				    if (typeof(cancelActivity) == 'boolean') structure['shapes'][id]['cancelActivity'] = cancelActivity;
				    structure['tree'][id] = connections;

				    if (type == 'Participant' || type == 'Lane' || type == 'TextAnnotation' || (type == 'StartEvent' && subType != 'TimerEventDefinition')) return;

					//console.log(id, type, text);
			    	dom_obj
			    	.css('cursor','pointer')
			    	.hover(function(){
		    				canvas.toggleMarker(id, 'highlights-shape');
			    		}, function(){
		    				canvas.toggleMarker(id, 'highlights-shape');
			    		})
			    	.click(function(){
		    			ProcessMakerScript.openMetadata({/literal}{$DATA.id}{literal},id,structure['shapes'][id]);
			    	});
			    } else if (object.constructor.name == 'Connection') {
			    	var id = object.id,
			    		type = ProcessMakerScript.formatType(object.type);
			    	if (typeof(elementRegistry.get(id+'_label')) == 'object') {
			    		var text = jQuery('[data-element-id='+id+'_label]').find('text').text();
			    	}
			    	structure['connections'][id] = {'type':type,'text':text};
			    }
			});
			
			if (jQuery('#structure').html() == '') {
				jQuery.ajax({
					'url': 'index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=save_structure',
					'type': 'POST',
					'data': jQuery.param({'id': {/literal}{$DATA.id}{literal}, 'structure': JSON.stringify(structure)}),
					success: function(data) {},
					error: function() {}
				});
			}
		});
	}
	
	// import xml
	//importXML(jQuery('#xml').val());
	jQuery.get('index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&mode=download&format=bpmn&id={/literal}{$DATA.id}{literal}', importXML, 'text');

})(window.BpmnJS);
</script>
{/literal}