/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *******************************************************************************/

/**
 * this function is used to show hide the columns in the add widget div based on the option selected
 * @param string typeName - the selected option
 */
function chooseType(typeName){
	$('status').style.display="inline";
	$('stufftype_id').value=typeName;

	var typeLabel = typeName;
	if(alert_arr[typeName] != null && alert_arr[typeName] != "" && alert_arr[typeName] != 'undefined'){
		typeLabel = alert_arr[typeName];
	}
	$('divHeader').innerHTML="<b>"+alert_arr.LBL_ADD+typeLabel+"</b>";
	if(typeName=='Module'){
		$('moduleNameRow').style.display="block";
		$('moduleFilterRow').style.display="block";
		$('modulePrimeRow').style.display="block";
		$('showrow').style.display="block";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('homeURLField').style.display = "none";
		$('chartRow').style.display = "none"; // crmv@30014
	}else if(typeName=='DashBoard'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('rssRow').style.display="none";
		$('showrow').style.display="none";
		$('dashNameRow').style.display="block";
		$('dashTypeRow').style.display="block";
		$('StuffTitleId').style.display="block";
		$('homeURLField').style.display = "none";
		$('chartRow').style.display = "none"; // crmv@30014
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&dash=dashboard',
				onComplete: function(response){
					var responseVal=response.responseText;
					$('selDashName').innerHTML=response.responseText;
					show('addWidgetsDiv');
					placeAtCenter($('addWidgetsDiv'));
					$('status').style.display="none";
				}
			}
		);
	}else if(typeName=='RSS'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="block";
		$('rssRow').style.display="block";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('status').style.display="none";
		$('homeURLField').style.display = "none";
		$('chartRow').style.display = "none"; // crmv@30014
	}else if(typeName=='Default'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="none";
		$('url_id').style.display = "none";
		$('chartRow').style.display = "none"; // crmv@30014
	}else if(typeName == 'URL'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('status').style.display="none";
		$('homeURLField').style.display = "block";
		$('chartRow').style.display = "none"; // crmv@30014
	}
	// crmv@30014
	else if(typeName == 'Charts'){
		$('moduleNameRow').style.display="none";
		$('moduleFilterRow').style.display="none";
		$('modulePrimeRow').style.display="none";
		$('showrow').style.display="none";
		$('rssRow').style.display="none";
		$('dashNameRow').style.display="none";
		$('dashTypeRow').style.display="none";
		$('StuffTitleId').style.display="block";
		$('homeURLField').style.display = "none";
		$('chartRow').style.display = "block";
		new Ajax.Request(
				'index.php',
				{queue: {position: 'end', scope: 'command'},
					method: 'post',
					postBody:'module=Charts&action=ChartsAjax&file=GetHomeCharts&type=picklist',
					onComplete: function(response){
						var responseVal=response.responseText;
						$('selChartName').innerHTML = response.responseText;
						$('status').style.display="none";
						
						// crmv@120738
						if (theme_config && theme_config.primary_menu_position == 'left') {
							showFloatingDiv('addWidgetsDiv', null, { modal: true });
						} else {
							show('addWidgetsDiv');
							placeAtCenter($('addWidgetsDiv'));
						}
						// crmv@120738e
					}
				}
		);
	}
	// crmv@30014e
}

/**
 * this function is used to set the filter list when the module name is changed
 * @param string modName - the modula name for which you want the filter list
 */
function setFilter(modName){
	var modval=modName.value;
	document.getElementById('savebtn').disabled = true;
	if(modval!=""){
		new Ajax.Request(
       		'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&modname='+modval,
				onComplete: function(response){
					var responseVal=response.responseText;
					$('selModFilter_id').innerHTML=response.responseText;
					setPrimaryFld(document.getElementById('selFilterid'));
					
					// crmv@120738
					if (theme_config && theme_config.primary_menu_position == 'left') {
						showFloatingDiv('addWidgetsDiv', null, { modal: true });
					} else {
						show('addWidgetsDiv');
						placeAtCenter($('addWidgetsDiv'));
					}
					// crmv@120738e
				}
			}
		);
	}
}

/**
 * this function is used to set the field list when the module name is changed
 * @param string modName - the modula name for which you want the field list
 */
function setPrimaryFld(Primeval){
	primecvid=Primeval.value;
	var fldmodule = $('selmodule_id').options[$('selmodule_id').selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
		method: 'post',
		postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&primecvid='+primecvid+'&fieldmodname='+fldmodule,
		onComplete: function(response){
			var responseVal=response.responseText;
			$('selModPrime_id').innerHTML=response.responseText;
			$('selPrimeFldid').selectedIndex = 0;
			$('status').style.display="none";
			document.getElementById('savebtn').disabled = false;
		}
	}
	);
}

/**
 * this function displays the div for selecting the number of rows in a widget
 * @param string sid - the id of the widget for which the div is being displayed
 */
function showEditrow(sid){
	$('editRowmodrss_'+sid).className="show_tab";
}

/**
 * this function is used to hide the div for selecting the number of rows in a widget
 * @param string editRow - the id of the div
 */
function cancelEntries(editRow){
	$(editRow).className="hide_tab";
}

/**
 * this function is used to save the maximum entries that a widget can display
 * @param string selMaxName - the widget name
 */
function saveEntries(selMaxName){
	sidarr=selMaxName.split("_");
	sid=sidarr[1];
	$('refresh_'+sid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+sid)
	showmax=$(selMaxName).value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&showmaxval='+showmax+'&sid='+sid,
			onComplete: function(response){
				var responseVal=response.responseText;
				eval(response.responseText);
				$('refresh_'+sid).innerHTML='';
			}
		}
	);
}

//crmv@30014
function saveHomeChart(selSize){
	sidarr = selSize.split("_");
	sid = sidarr[1];
	$('refresh_'+sid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+sid)
	showmax = $(selSize).value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Charts&action=ChartsAjax&file=SaveHomeChart&size='+showmax+'&stuffid='+sid,
			onComplete: function(response){
				location.reload();
			}
		}
	);
}
//crmv@30014e


/**
 * this function is used to save the url of a widget
 * @param string selurl
 */
function saveEditurl(selurl){
	sidarr=selurl.split("_");
	sid=sidarr[1];
	$('refresh_'+sid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+sid)
	url=$(selurl).value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&url='+url+'&sid='+sid,
			onComplete: function(response){
				var responseVal=response.responseText;
				eval(response.responseText);
				$('refresh_'+sid).innerHTML='';
			}
		}
	);
}

/**
 * this function is used to save the dashboard values
 */
function saveEditDash(dashRowId){
	$('refresh_'+dashRowId).innerHTML=$('vtbusy_homeinfo').innerHTML;
	cancelEntries('editRowmodrss_'+dashRowId);
	var dashVal='';
	var iter=0;
	for(iter=0;iter<3;iter++){
		if($('dashradio_'+[iter]).checked)
			dashVal=$('dashradio_'+[iter]).value;
	}
	did=dashRowId;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&dashVal='+dashVal+'&did='+did,
			onComplete: function(response){
				var responseVal=response.responseText;
				eval(response.responseText);
				$('refresh_'+did).innerHTML='';
			}
		}
	);
}

/**
 * this function is used to delete widgets form the home page
 * @param string sid - the stuffid of the widget
 */
function DelStuff(sid){
	if(confirm(alert_arr.SURE_TO_DELETE)){
		new Ajax.Request(
			'index.php',
			{queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&homestuffid='+sid,
				onComplete: function(response){
					var responseVal=response.responseText;
					if(response.responseText.indexOf('SUCCESS') > -1){
						var delchild = $('stuff_'+sid);
						odeletedChild = $('MainMatrix').removeChild(delchild);
						$('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr><td align="center">'+alert_arr.LBL_DELETED_SUCCESSFULLY+'</td></tr></table>';
						$('seqSettings').style.display = 'block';
						$('seqSettings').style.display = 'none';
						placeAtCenter($('seqSettings'));
						Effect.Appear('seqSettings');
						setTimeout(hideSeqSettings,3000);
					}else{
						alert(alert_arr.ERROR_DELETING_TRY_AGAIN)
					}
				}
			}
		);
	}
}

/**
 * this function loads the newly added div to the home page
 * @param string stuffid - the id of the newly created div
 * @param string stufftype - the stuff type for the new div (for e.g. rss)
 */
function loadAddedDiv(stuffid,stufftype, stuffsize){ // crmv@30014
	gstuffId = stuffid;
	if (stuffsize == undefined || stuffsize == '') stuffsize = 0; // crmv@30014
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=NewBlock&stuffid='+stuffid+'&stufftype='+stufftype,
			onComplete: function(response){
				var responseVal=response.responseText;
//				$('MainMatrix').style.display= 'none';
				$('MainMatrix').innerHTML = response.responseText + $('MainMatrix').innerHTML;
				positionDivInAccord('stuff_'+gstuffId,'',stufftype, stuffsize); // crmv@30014
				initHomePage();
				loadStuff(stuffid,stufftype);
				$('MainMatrix').style.display='block';
			}
		}
	);
}

/**
 * this function is used to reload a widgets' content based on its id and type
 * @param string stuffid - the widget id
 * @param string stufftype - the type of the widget
 */
function loadStuff(stuffid,stufftype){
	$('refresh_'+stuffid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
		    postBody:'module=Home&action=HomeAjax&file=HomeBlock&homestuffid='+stuffid+'&blockstufftype='+stufftype,
		    onComplete: function(response){
				var responseVal=response.responseText;
				jQuery('#stuffcont_'+stuffid).html(response.responseText); // crmv@82770 - changed to support script tags
				if(stufftype=="Module"){
					if($('more_'+stuffid).value != null && $('more_'+stuffid).value != '')
						$('a_'+stuffid).href = "index.php?module="+$('more_'+stuffid).value+"&action=ListView&viewname="+$('cvid_'+stuffid).value;
				}
				if(stufftype=="Default" && typeof($('a_'+stuffid)) != 'undefined'){
					if($('more_'+stuffid).value != ''){
						$('a_'+stuffid).style.display = 'block';
						var url = "index.php?module="+$('more_'+stuffid).value+"&action=index";
						if($('search_qry_'+stuffid)!=''){
							url += $('search_qry_'+stuffid).value;
						}
						$('a_'+stuffid).href = url;
					}else{
						$('a_'+stuffid).style.display = 'none';
					}
				}
				if(stufftype=="RSS"){
					$('a_'+stuffid).href = $('more_'+stuffid).value;
				}
				if(stufftype=="DashBoard"){
					$('a_'+stuffid).href = "index.php?module=Dashboard&action=index&type="+$('more_'+stuffid).value;
				}
				//crmv@29079
				if(stufftype=="Iframe"){
					if ($('loadModCommentsNewsScript_'+stuffid) != undefined)
						eval($('loadModCommentsNewsScript_'+stuffid).innerHTML);
				}		
				//crmv@29079e
				//crmv@3079m
				if (jQuery('#stuff_'+stuffid+' script#eval_script').length > 0){
					eval(jQuery('#stuff_'+stuffid+' script#eval_script').html());
				}
				//crmv@3079me					
				$('refresh_'+stuffid).innerHTML='';
		    }
		}
	);
}

function loadAllWidgets(widgetInfoList, batchSize){
	var batchWidgetInfoList = [];
	var widgetInfo = {};
	for(var index =0 ; index < widgetInfoList.length;++index) {
		var widgetId = widgetInfoList[index].widgetId;
		var widgetType = widgetInfoList[index].widgetType;
		widgetInfo[widgetId] = widgetType;
		$('refresh_'+widgetId).innerHTML=$('vtbusy_homeinfo').innerHTML;
		batchWidgetInfoList.push(widgetInfoList[index]);
		if((index > 0 && (index+1) % batchSize == 0) || index+1 == widgetInfoList.length) {
			jQuery.ajax({
				url: 'index.php?module=Home&action=HomeAjax&file=HomeWidgetBlockList',
				type: 'POST',
				data: '&widgetInfoList='+JSON.stringify(batchWidgetInfoList),
				dataType: 'json',
				success: function(responseVal) {
					for(var widgetId in responseVal) {
						if(responseVal.hasOwnProperty(widgetId)) {
							jQuery('#stuffcont_'+widgetId).html(responseVal[widgetId]); // crmv@82770
							$('refresh_'+widgetId).innerHTML='';
							var widgetType = widgetInfo[widgetId];
							if(widgetType=="Module" && $('more_'+widgetId).value != null &&
									$('more_'+widgetId).value != '') {
								$('a_'+widgetId).href = "index.php?module="+
								$('more_'+widgetId).value+"&action=ListView&viewname="+
								$('cvid_'+widgetId).value;
							} else if(widgetType == "Default" && typeof($('a_'+widgetId)) !=
									'undefined'){
								if(typeof $('more_'+widgetId) != 'undefined' &&
										$('more_'+widgetId).value != ''){
									$('a_'+widgetId).style.display = 'block';
									var url = "index.php?module="+$('more_'+widgetId).value+
										"&action=index";
									if($('search_qry_'+widgetId)!=''){
										url += $('search_qry_'+widgetId).value;
									}
									$('a_'+widgetId).href = url;
								}else{
									$('a_'+widgetId).style.display = 'none';
								}
							} else if(widgetType=="RSS"){
								$('a_'+widgetId).href = $('more_'+widgetId).value;
							//crmv@29079
							} else if(widgetType=="Iframe"){
								if ($('loadModCommentsNewsScript_'+widgetId) != undefined)
									eval($('loadModCommentsNewsScript_'+widgetId).innerHTML);
							//crmv@29079e
							} else if(widgetType=="DashBoard"){
								$('a_'+widgetId).href = "index.php?module=Dashboard&action=index&type="+$('more_'+widgetId).value; //crmv@24739
							}
							//crmv@3079m
							if (jQuery('#stuff_'+widgetId+' script#eval_script').length > 0){
								eval(jQuery('#stuff_'+widgetId+' script#eval_script').html());
							}								
							//crmv@3079me
						}
					}
				}
			});
			batchWidgetInfoList = [];
		}
	}
}

/**
 * this function validates the form for creating a new widget
 */
function frmValidate(){
	if(trim($('stufftitle_id').value)==""){
		alert(alert_arr.LBL_ENTER_WINDOW_TITLE);
		$('stufftitle_id').focus();
		return false;
	}
	if($('stufftype_id').value=="RSS"){
		if($('txtRss_id').value==""){
			alert(alert_arr.LBL_ENTER_RSS_URL);
			$('txtRss_id').focus();
			return false;
		}
	}
	if($('stufftype_id').value=="URL"){
		if($('url_id').value==""){
			alert(alert_arr.LBL_ENTER_URL);
			$('url_id').focus();
			return false;
		}
	}
	if($('stufftype_id').value=="Module"){
		var selLen;
		var fieldval=new Array();
		var cnt=0;
		selVal=document.Homestuff.PrimeFld;
		for(k=0;k<selVal.options.length;k++){
			if(selVal.options[k].selected){
				fieldval[cnt]=selVal.options[k].value;
				cnt= cnt+1;
			}
		}
		if(cnt>2){
			alert(alert_arr.LBL_SELECT_ONLY_FIELDS);
			selVal.focus();
			return false;
		}else{
			document.Homestuff.fldname.value=fieldval;
		}
	}
	var stufftype=$('stufftype_id').value;
	var stufftitle=$('stufftitle_id').value;
	$('stufftitle_id').value = '';
	var selFiltername='';
	var fldname='';
	var selmodule='';
	var maxentries='';
	var txtRss='';
	var seldashbd='';
	var selchart=''; // crmv@30014
	var seldashtype='';
	var seldeftype='';
	var txtURL = '';

	if(stufftype=="Module"){
		selFiltername =document.Homestuff.selFiltername[document.Homestuff.selFiltername.selectedIndex].value;
		fldname = fieldval;
		selmodule =$('selmodule_id').value;
		maxentries =$('maxentryid').value;
	}else if(stufftype=="RSS"){
		txtRss=$('txtRss_id').value;
		maxentries =$('maxentryid').value;
	}else if(stufftype=="URL"){
		txtURL=$('url_id').value;
	}else if(stufftype=="DashBoard"){
		seldashbd=$('seldashbd_id').value;
		seldashtype=$('seldashtype_id').value;
	// crmv@30014
	}else if(stufftype=="Charts"){
		selchart=$('selchart_id').value;
	// crmv@30014e
	}else if(stufftype=="Default"){
		seldeftype=document.Homestuff.seldeftype[document.Homestuff.seldeftype.selectedIndex].value;
	}

	var url="stufftype="+stufftype+"&stufftitle="+stufftitle+"&selmodule="+selmodule+"&maxentries="+maxentries+"&selFiltername="+selFiltername+"&fldname="+encodeURIComponent(fldname)+"&txtRss="+txtRss+"&seldashbd="+seldashbd+"&seldashtype="+seldashtype+"&seldeftype="+seldeftype+'&txtURL='+txtURL+'&selchart='+selchart; // crmv@30014
	var stuffarr=new Array();
	$('status').style.display="inline";

	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=Homestuff&'+url,
			onComplete: function(response){
				var responseVal=response.responseText;
				if(!response.responseText){
					alert(alert_arr.LBL_ADD_HOME_WIDGET);
					$('status').style.display="none";
					$('stufftitle_id').value='';
					$('txtRss_id').value='';
					return false;
				}else{
					hide('addWidgetsDiv');
					$('status').style.display="none";
					$('stufftitle_id').value='';
					$('txtRss_id').value='';
					eval(response.responseText);
				}
			}
		}
	);
}

/**
 * this function is used to hide the default widgets
 * @param string sid - the id of the widget
 */
function HideDefault(sid){
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&stuffid='+sid+"&act=hide",
	        onComplete: function(response){
				var responseVal=response.responseText;
				if(response.responseText.indexOf('SUCCESS') > -1){
					var delchild = $('stuff_'+sid);
					odeletedChild = $('MainMatrix').removeChild(delchild);
					$('seqSettings').innerHTML= '<table cellpadding="10" cellspacing="0" border="0" width="100%" class="vtResultPop small"><tr><td align="center">'+alert_arr.LBL_WIDGET_HIDDEN+'.'+alert_arr.LBL_RESTORE_FROM_PREFERENCES+'.</td></tr></table>';
					$('seqSettings').style.display = 'block';
					$('seqSettings').style.display = 'none';
					placeAtCenter($('seqSettings'));
					Effect.Appear('seqSettings');
					setTimeout(hideSeqSettings,3000);
				}else{
					alert(alert_arr.ERR_HIDING + '.'+ alert_arr.MSG_TRY_AGAIN + '.');
				}
	        }
		}
	);
}


/**
 * this function removes the widget dropdown window
 */
function fnRemoveWindow(){
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'none';
}

/**
 * this function displays the widget dropdown window
 */
function fnShowWindow(){
	var tagName = document.getElementById('addWidgetDropDown').style.display= 'block';
}

/**
 * this function is used to postion the widgets on home on page resize
 * @param string targetDiv - the id of the target widget
 * @param string stufftitle - the title of the target widget
 * @param string stufftype - the type of the target widget
 */
function positionDivInAccord(targetDiv,stufftitle,stufftype, stuffsize){ // crmv@30014
	var layout = $('homeLayout').value,
		spacing = 0.6,
		widgetWidth,
		dashWidth;

	// crmv@30014
	if (stuffsize == undefined || stuffsize == 0 || stuffsize == '') stuffsize = 1;
	var columns = Math.max(2, Math.min(parseInt(layout), 4));
	var stuffsize = Math.max(1, Math.min(stuffsize, columns));
	// crmv@30014e

	switch(layout){
		case '2':
			widgetWidth = 49;
			break;
		case '3':
			widgetWidth = 31;
			break;
		case '4':
		default:
			widgetWidth = 24;
			break;
	}
	dashWidth = widgetWidth*2 + spacing;
	urlwidth = 98.6;

	var mainX = parseInt(document.getElementById("MainMatrix").style.width);

	//crmv@25314
	if(stufftitle != vtdashboard_defaultDashbaordWidgetTitle && stufftype != "DashBoard" && stufftype != "URL" && stufftype != "Iframe" && stufftype != "SDKIframe"){	//crmv@25466
		var dx = (mainX * widgetWidth * stuffsize) / 100 + (stuffsize-1) * spacing;
	}else if(stufftype == "DashBoard" || stufftitle == vtdashboard_defaultDashbaordWidgetTitle || stufftype == "Iframe"){
		var dx = mainX * dashWidth / 100;
	}else if(stufftype == "URL"){
		var dx = mainX * urlwidth / 100;
	}
	//crmv@25314e
	//crmv@25466
	else if (stufftype == 'SDKIframe') {
		var widgetId = parseInt(targetDiv.substr(targetDiv.indexOf('_')+1));
		if (widgetId > 0) {
			var sdkdata = getSDKHomeIframe(widgetId);
			var size = Math.max(1, Math.min(sdkdata.size, columns));
			var dx = (mainX * widgetWidth * size) / 100 + (size-1) * spacing;
		}
	}
	//crmv@25466e
	document.getElementById(targetDiv).style.width=dx + "%";
}

/**
 * this function hides the seqSettings div
 */
function hideSeqSettings(){
	Effect.Fade('seqSettings');
}

/**
 * this function fetches the homepage dashboard
 * @param string stuffid - the id of the dashboard widget
 */
function fetch_homeDB(stuffid){
	$('refresh_'+stuffid).innerHTML=$('vtbusy_homeinfo').innerHTML;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Dashboard&action=DashboardAjax&file=HomepageDB',
			onComplete: function(response){
				$('stuffcont_'+stuffid).style.display = 'none';
				$('stuffcont_'+stuffid).innerHTML=response.responseText;
				$('refresh_'+stuffid).innerHTML='';
				Effect.Appear('stuffcont_'+stuffid);
			}
		}
	);
}

/**
 * this function initializes the homepage
 */
initHomePage = function(){
	Sortable.create(
		"MainMatrix",
		{
			constraint:false,tag:'div',overlap:'Horizontal',handle:'headerrow',
			onUpdate:function(){
				matrixarr = Sortable.serialize('MainMatrix').split("&");
				matrixseqarr=new Array();
				seqarr=new Array();
				for(x=0;x<matrixarr.length;x++){
					matrixseqarr[x]=matrixarr[x].split("=")[1];
				}
				BlockSorting(matrixseqarr);
			}
		}
	);
}

/**
 * this function is used to save the sorting order of elements when they are moved around on the home page
 * @param array matrixseqarr - the array containing the sequence of the widgets
 */
function BlockSorting(matrixseqarr){
	var sequence = matrixseqarr.join("_");
	new Ajax.Request('index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&matrixsequence='+sequence,
			onComplete: function(response){
				$('seqSettings').innerHTML=response.responseText;
				$('seqSettings').style.display = 'block';
				$('seqSettings').style.display = 'none';
				placeAtCenter($('seqSettings'));
				Effect.Appear('seqSettings');
				setTimeout(hideSeqSettings,3000);
			}
		}
	);
}

/**
 * this function checks if the current browser is IE or not
 */
function isIE(){
	return navigator.userAgent.indexOf("MSIE") !=-1;
}

/**
 * this function takes a widget id and adds scrolling property to it
 */
function addScrollBar(id){
	$('stuff_'+id).style['overflowX'] = "scroll";
	$('stuff_'+id).style['overflowY'] = "scroll";
}

/**
 * this function will display the node passed to it in the center of the screen
 */
function showOptions(id){
	var node = $(id);
	node.style.display='block';
	placeAtCenter(node);
}

/**
 * this function will hide the node passed to it
 */
function hideOptions(id){
	Effect.Fade(id);
}

/**
 * this function will be used to save the layout option
 */
function saveLayout(){
	$('status').show();
	hideOptions('changeLayoutDiv');
	var sel = $('layoutSelect');
	var layout = sel.options[sel.selectedIndex].value;
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody:'module=Home&action=HomeAjax&file=HomestuffAjax&layout='+layout,
			onComplete: function(response){
				var responseVal=response.responseText;
				window.location.href = window.location.href;
			}
		}
	);
}
