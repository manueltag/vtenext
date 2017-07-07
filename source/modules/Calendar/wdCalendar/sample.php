<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title> My Calendar </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link href="modules/Calendar/wdCalendar/css/dailog.css" rel="stylesheet" type="text/css" />
    <link href="modules/Calendar/wdCalendar/css/calendar.css" rel="stylesheet" type="text/css" />
     <link href="themes/<?php echo $theme; ?>/dp.css" rel="stylesheet" type="text/css" />
    <link href="modules/Calendar/wdCalendar/css/alert.css" rel="stylesheet" type="text/css" />
    <link href="modules/Calendar/wdCalendar/css/main.css" rel="stylesheet" type="text/css" />

    <!-- TODO: it would be nice to use the new jquery, but the wdcalendar plugin doesn't work with the new one :(( -->
    <script src="modules/Calendar/wdCalendar/src/jquery.min.js" type="text/javascript"></script>
    
    <!-- crmv@82419 TODO: fix this awful code!!! please please include the Theme.tpl here!!! -->
    <!--link rel="stylesheet" type="text/css" href="themes/softed/vte_bootstrap.css">
    <link rel="stylesheet" type="text/css" href="themes/softed/ripples.min.css">
    <link rel="stylesheet" type="text/css" href="themes/softed/datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="themes/softed/jquery.dropdown.css"-->
    <link href="themes/<?php echo $theme; ?>/style.css" rel="stylesheet" type="text/css" /> <!-- crmv@26807 -->
    
    <!-- BOOTSTRAP does't work! TODO: fix this awful code!!! --> 
    <!-- crmv@98866 -->
    <style>
			div.checkbox span.checkbox-material span.check {
				border: 1px solid #2b577c;
				width: 18px;
		    height: 18px;
			}
		</style>
		<!-- crmv@98866 end -->
    
    <!--script language="JavaScript" type="text/javascript" src="themes/softed/js/bootstrap/bootstrap.min.js"></script-->
    <!--script language="JavaScript" type="text/javascript" src="themes/softed/js/arrive.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/js/moment-l18n.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/js/material/ripples.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/js/material/material.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/js/material/datetimepicker.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/js/jquery.dropdown.js"></script>
    <script language="JavaScript" type="text/javascript" src="themes/softed/index.js"></script -->


    <!-- crmv@17001 -->
    <script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
    <script language="JavaScript" type="text/javascript" src="include/js/jquery_plugins/form.js"></script>
    <!-- crmv@17001e -->
	
	<script type="text/javascript">bindButtons();</script> <!-- crmv@104776 -->

	<?php global $current_language; ?>	<!-- crmv@17001 -->
    <script src="modules/Calendar/wdCalendar/src/Plugins/Common.js" type="text/javascript"></script>
    <script src="modules/Calendar/wdCalendar/src/Plugins/datepicker_lang_<?php echo $current_language; ?>.js" type="text/javascript"></script>
    <script src="modules/Calendar/wdCalendar/src/Plugins/jquery.datepicker.js" type="text/javascript"></script>
       <!-- crmv@82419 -->

    <script src="modules/SDK/SDK.js" type="text/javascript"></script> <!-- crmv@29954 -->

    <script src="modules/Calendar/wdCalendar/src/Plugins/jquery.alert.js" type="text/javascript"></script>
    <script src="modules/Calendar/wdCalendar/src/Plugins/jquery.ifrmdailog.js" defer="defer" type="text/javascript"></script>
    <script src="modules/Calendar/wdCalendar/src/Plugins/wdCalendar_lang_<?php echo $current_language; ?>.js" type="text/javascript"></script>
    <script src="modules/Calendar/wdCalendar/src/Plugins/jquery.calendar.js" type="text/javascript"></script>

    <script src="modules/Calendar/script.js" type="text/javascript"></script>
    
	<!-- crmv@26807 crmv@82419 -->
	<script type="text/javascript" src="include/js/jquery_plugins/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
	<script type="text/javascript" src="include/js/jquery_plugins/fancybox/jquery.fancybox.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="include/js/jquery_plugins/fancybox/jquery.fancybox.css" media="screen" />
	<!-- crmv@26807e crmv@82419e -->

	<!-- crmv@29190 -->
	<!-- crmv@82419 crmv@82831 -->
	<link rel="stylesheet" href="include/js/jquery_plugins/ui/jquery-ui.min.css">
	<link rel="stylesheet" href="include/js/jquery_plugins/ui/jquery-ui.theme.min.css">
	<script type="text/javascript" src="include/js/jquery_plugins/ui/jquery-ui.min.js"></script>
	<script type="text/javascript">
		// fix for some collision between bootstrap and jQuery UI
		jQuery.widget.bridge('uibutton', jQuery.ui.button);
		jQuery.widget.bridge('uitooltip', jQuery.ui.tooltip);
	</script>
	
	<!-- crmv@29190e -->
	<!-- crmv@82419e -->
	
    <script type="text/javascript">
    	//crmv@17001: sostituzione '$' con 'jQuery' in questo file e anche in wdCalendar/src/Plugins
    	var eventType;	//crmv@17001
    	//crmv@17001 : StartHour
    	<?php
        	if ($current_user->column_fields['start_hour'] == '')
        		echo 'var start_hour = 0;';
        	else {
        		$tmp = explode(':',$current_user->column_fields['start_hour']);
        		echo 'var start_hour = '.$tmp[0].';';
        	}
        	//crmv@diego
        	echo 'var no_week_sunday = '.$current_user->column_fields['no_week_sunday'].';';
        	//crmv@diego e
        	//crmv@20324
        	global $current_user_cal_color;
        	echo "var current_user_color = '$current_user_cal_color';";
        	//crmv@20324e
        	echo "var empty_search_str = '".getTranslatedString('LBL_SEARCH_STRING')."';";	//crmv@29190
        	
        	//crmv@51216
        	include_once('modules/SLA/SLA.php');
			$sla_config_global = SLA::get_config();
			$holidays = $sla_config_global['HelpDesk']['holidays'];
			echo 'var holidays = '.Zend_Json::encode($holidays).';';
        	//crmv@51216e
        	
        	// crmv@68357 - prepare events to preload
        	if (isset($_REQUEST['icalid']) && $_REQUEST['from_crmid'] > 0 && $_REQUEST['from_module'] == 'Messages') {
				$messageFocus = CRMEntity::getInstance('Messages');
				$messageFocus->id = intval($_REQUEST['from_crmid']);
				$icalRow = $messageFocus->getIcals(intval($_REQUEST['icalid']));
				$icalRow = $icalRow[0];
				if ($icalRow['content']) {
					$vcalendar = new VTEvcalendar();
					$r = $vcalendar->parse($icalRow['content']);
					//if ($r === false) return false;
		
					$event = $vcalendar->getComponent('vevent');
					if (!$event) $event = $vcalendar->getComponent('vtodo');
					//if (!$event) return false;
					$values = $vcalendar->generateArray($event, $event->objName);
					// alter some values
					unset($values['invitees']);
					// js times
					$sd = strtotime($values['date_start'].' '.$values['time_start']);
					$ed = strtotime($values['due_date'].' '.$values['time_end']);
					$values['js_start_date'] = date(DateTime::RFC2822, $sd);
					$values['js_end_date'] = date(DateTime::RFC2822, $ed);
					echo "\nvar preload_events = [".Zend_Json::encode($values)."];\n";
				}
        	}
        	// crmv@68357e
        ?>
        //crmv@17001 : StartHour e
        
        jQuery(document).ready(function() {

        	//crmv@17001
        	<?php

        	//crmv@vte10usersFix
        	if ($_REQUEST['activity_view'] == 'Today')
        		echo 'var view="day";';
        	elseif ($_REQUEST['activity_view'] == 'This Month')
        		echo 'var view="month";';
//			elseif ($_REQUEST['activity_view'] == 'This Week')
			else
        		echo 'var view="week";';
        	//crmv@vte10usersFix e

        	echo 'crmv_date_format="'.$current_user->column_fields['date_format'].'";';
        	echo 'var num_days = "";';

        	$edit_permission = 'no';
        	$delete_permission = 'no';
        	if(isPermitted("Calendar","EditView") == "yes")
        		$edit_permission = 'yes';
        	if(isPermitted("Calendar","Delete") == "yes")
        		$delete_permission = 'yes';
        	echo 'edit_permission = "'.$edit_permission.'";';
        	echo 'delete_permission = "'.$delete_permission.'";';
        	// crmv@68357 - add integration for date passed from url
        	$dateConst = '';	// default is empty = today
        	if ($_REQUEST['showday']) {
				$ts = strtotime($_REQUEST['showday']);
				if ($ts > 0) {
					$month = intval(date('n', $ts))-1;
					$dateConst = date('Y, ', $ts) . $month . date(', j, G, i', $ts);
				}
        	}
        	// crmv@68357e
        	?>

            new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Calendar&action=CalendarAjax&file=wdCalendar&subfile=ajaxRequests/getEventType",
                        onComplete: function(response) {
                			eventType = response.responseText;
                		}
                }
            );
            var DATA_FEED_URL = "index.php?module=Calendar&action=CalendarAjax&file=wdCalendar&subfile=php/datafeed";
            var filter_assigned_user_id = parent.window.document.EventViewOption.filter_assigned_user_id.value;
            var tmp_url;
            //crmv@17001e

            //crmv@17001
            var op = {
                view: view,
                num_days: num_days,
                //theme:5,
                //showday: new Date("1/9/2011 16:30"),
                showday: new Date(<?php echo $dateConst; ?>),	// crmv@68357
                EditCmdhandler:Edit,
                DeleteCmdhandler:Delete,
                ViewCmdhandler:View,
                onWeekOrMonthToDay:wtd,
                onBeforeRequestData: cal_beforerequest,
                onAfterRequestData: cal_afterrequest,
                onRequestDataError: cal_onerror,
                autoload:true,
                url: DATA_FEED_URL + "&method=list&filter_assigned_user_id="+filter_assigned_user_id,	//crmv@17001
                //url: DATA_FEED_URL + "&method=list",	//crmv@17001
                quickAddUrl: DATA_FEED_URL + "&method=add",
                quickUpdateUrl: DATA_FEED_URL + "&method=update",
                quickDeleteUrl: DATA_FEED_URL + "&method=remove",
                // crmv@68357
                preloadEvents: window.preload_events || [],
                enableQuickCreate: !(window.preload_events && window.preload_events.length > 0), 
                // crmv@68357e
            };
            var $dv = jQuery("#calhead");
            var _MH = document.documentElement.clientHeight;
            var dvH = $dv.height() + 2;
            op.height = _MH - dvH;
            op.eventItems =[];

            var p = jQuery("#gridcontainer").bcalendar(op).BcalGetOp();
            jQuery("#caltoolbar").noSelect();

            jQuery("#hdtxtshow").datepicker_old({ picker: "#txtdatetimeshow", showtarget: jQuery("#txtdatetimeshow"), // crmv@97209
            		onReturn:function(r){
                            var p = jQuery("#gridcontainer").gotoDate(r).BcalGetOp();
                            if (p && p.datestrshow) {
                                jQuery("#txtdatetimeshow").text(p.datestrshow);
                                //crmv@24904
                                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);
                                //crmv@24904e
                            }
                     }
            });
			//crmv@29954
            function cal_beforerequest(type, params) {

            	if ((type == 2 || type == 4) && params != undefined) { // 1 = retrieve, 2 = add, 3 = remove, 4 = update
                	var i, vform, inputs, temp_input;
            		// create fake submit form
            		vform = document.createElement('FORM');
            		vform.name = 'createQuickTODO';
            		// input standard
            		inputs = [
            	    	{'name': 'module', 'value': 'Calendar'}
            	    ];
            	    // aggiungo inputs dai parametri
            	    for (i=0; i< params.length; ++i) {
                  	  inputs.push(params[i]);
            	    }
            	    // li inserisco nel form
            	    for (i=0; i < inputs.length; ++i) {
            	    	temp_input = document.createElement('INPUT');
            	    	temp_input.name = inputs[i].name;
            	    	temp_input.value = inputs[i].value;
                		vform.appendChild(temp_input);
            	    }
					// valido il form
            	    var sdkValidate = SDKValidate(vform);
            		if (sdkValidate) {
            			var sdkValidateResponse = eval('('+sdkValidate.responseText+')');
            			if (!sdkValidateResponse['status']) {
            				return false;
            			}
            		}
            	}
            	//crmv@vte10usersFix
            	parent.jQuery("#errorpannel_new").hide();
                parent.jQuery("#status").show();
                //crmv@vte10usersFix e
                return true; // se falso non continua con la richiesta
            }
            //crmv@29954e
            function cal_afterrequest(type)
            {
                switch(type)
                {
                    case 1:
                    	parent.jQuery("#status").hide();//crmv@vte10usersFix
                        break;
                    case 2:
                    case 3:
                    case 4:
                    	parent.jQuery("#status").hide();//crmv@vte10usersFix
                    break;
                }
                //crmv@17001
               	if (p && p.datestrshow) {
	                jQuery("#txtdatetimeshow").text(p.datestrshow);
	                //crmv@24904
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);
	                daySelected(p.datestrshow_hidden); //crmv@20210
	                //crmv@24904e
	            }

	            parent.jQuery("#txtdatetimeshow_new").html(jQuery("#txtdatetimeshow").html()); //crmv@vte10usersFix

	            //TO DO: conteggio degli eventi mostrati a video
	            /*
	            date = p.showday.getFullYear()+'-'+(p.showday.getMonth()+1)+'-'+p.showday.getDate()+' '+p.showday.getHours()+':'+p.showday.getMinutes()+':'+p.showday.getSeconds();
	            new Ajax.Request(
	                'index.php',
	                {queue: {position: 'end', scope: 'command'},
	                        method: 'post',
	                        postBody: "module=Calendar&action=CalendarAjax&file=wdCalendar&subfile=ajaxRequests/count_activities&startdate="+date+"&calendar_type="+view+"&view_filter="+filter_assigned_user_id,
	                        onComplete: function(response) {
	                			jQuery("#count_activities").text(response.responseText);
	                		}
	                }
	            );
	            */
	            //crmv@17001e
            }
            function cal_onerror(type,data)
            {
            	//crmv@32334
            	if (data['Msg']){
            		alert(data['Msg']);
            	}
            	//crmv@32334 e
            	parent.jQuery("#errorpannel_new").show();//crmv@vte10usersFix
            }
            function Edit(data)
            {
               var eurl="modules/Calendar/wdCalendar/edit.php?id={0}&start={2}&end={3}&isallday={4}&title={1}";
                if(data)
                {
                    var url = StrFormat(eurl,data);
                    OpenModelWindow(url,{ width: 600, height: 400, caption:"Manage  The Calendar",onclose:function(){
                       jQuery("#gridcontainer").reload();
                    }});
                }
            }
            function View(data)
            {
                var str = "";
                jQuery.each(data, function(i, item){
                    str += "[" + i + "]: " + item + "\n";
                });
                alert(str);
            }
            function Delete(data,callback)
            {
            	//crmv@17001
                jQuery.alerts.okButton=i18n.xgcalendar.Ok;
                jQuery.alerts.cancelButton=i18n.xgcalendar.Cancel;
                hiConfirm(i18n.xgcalendar.confirm_delete_event,i18n.xgcalendar.i_delete,function(r){ r && callback(0);});
                //crmv@17001e
            }
            function wtd(p)
            {
               if (p && p.datestrshow) {
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
                    //crmv@24904
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);
	                daySelected(p.datestrshow_hidden); //crmv@20210
	                //crmv@24904e
                }
                jQuery("#caltoolbar div.fcurrent").each(function() {
                    jQuery(this).removeClass("fcurrent");
                })
                jQuery("#showdaybtn").addClass("fcurrent");
            }
            //to show day view
            jQuery("#showdaybtn").click(function(e) {
                //document.location.href="#day";
                jQuery("#caltoolbar div.fcurrent").each(function() {
                    jQuery(this).removeClass("fcurrent");
                })
                jQuery(this).addClass("fcurrent");
                var p = jQuery("#gridcontainer").swtichView("day").BcalGetOp();
                if (p && p.datestrshow) {
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);	//crmv@24904
                }
            });
            //to show week view
            jQuery("#showweekbtn").click(function(e) {
                //document.location.href="#week";
                jQuery("#caltoolbar div.fcurrent").each(function() {
                    jQuery(this).removeClass("fcurrent");
                })
                jQuery(this).addClass("fcurrent");
                var p = jQuery("#gridcontainer").swtichView("week").BcalGetOp();
                if (p && p.datestrshow) {
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);	//crmv@24904
                }

            });
            //to show month view
            jQuery("#showmonthbtn").click(function(e) {
                //document.location.href="#month";
                jQuery("#caltoolbar div.fcurrent").each(function() {
                    jQuery(this).removeClass("fcurrent");
                })
                jQuery(this).addClass("fcurrent");
                var p = jQuery("#gridcontainer").swtichView("month").BcalGetOp();
                if (p && p.datestrshow) {
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);	//crmv@24904
                }
            });

            jQuery("#showreflashbtn").click(function(e){
                jQuery("#gridcontainer").reload();
            });

            //Add a new event
            /*
            jQuery("#faddbtn").click(function(e) {
                var url ="modules/Calendar/wdCalendar/edit.php";
                OpenModelWindow(url,{ width: 500, height: 400, caption: "Create New Calendar"});
            });
            */

            //go to today
            jQuery("#showtodaybtn").click(function(e) {
                var p = jQuery("#gridcontainer").gotoDate().BcalGetOp();
                if (p && p.datestrshow) {
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);	//crmv@24904
                }
            });
            //previous date range
            jQuery("#sfprevbtn").click(function(e) {
                var p = jQuery("#gridcontainer").previousRange().BcalGetOp();
                if (p && p.datestrshow) {
                	//crmv@20210
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
                    jQuery("#txtdatetimeshow").click();
	                jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);	//crmv@24904
                    //crmv@20210 e
                }
            });
            //next date range
            jQuery("#sfnextbtn").click(function(e) {
                var p = jQuery("#gridcontainer").nextRange().BcalGetOp();
                if (p && p.datestrshow) {
                	//crmv@20210
                    jQuery("#txtdatetimeshow").text(p.datestrshow);
                    //crmv@24904
                    jQuery("#txtdatetimeshow_hidden").val(p.datestrshow_hidden);
                    jQuery("#txtdatetimeshow").click();
                    //crmv@24904e
                    //crmv@20210 e
                }
            });
            //crmv@vte10usersFix

            //crmv@22622	//crmv@21996	//crmv@20210
            jQuery("#txtdatetimeshow").click();
            <?php
            	//if ($_COOKIE['crmvWinMaxStatus'] == 'close') {
            	//	echo 'parent.jQuery(\'#wdCalendar\').height(document.documentElement.clientHeight - parent.jQuery(\'#vte_menu\').height() + 74);';
            	//}
            ?>

            jQuery("#filterDivCalendar").height(parent.jQuery('#wdCalendar').height() - 40);  // crmv@43117
            //crmv@20210e	//crmv@21996e	//crmv@22622e

            parent.jQuery("#vte_menu_white").height(parent.jQuery("#vte_menu").height());

            var openAllDay = jQuery("#openAllDay").val(); // true = default
            var offsetAllDay = 0;
            var offsetAllDayFooter = 0;
            var container_Height = jQuery("#weekViewAllDaywk_container").height();
            var dvtec_Height = jQuery("#dvtec").height();

            //crmv@26807
            jQuery("#allDayClick").click(function() {
            	openAllDay = jQuery("#openAllDay").val();

            	if (jQuery("#open_dvtec").val() == '') {
            		jQuery("#open_weekViewAllDaywk_container").val(jQuery("#weekViewAllDaywk_container").height());
                	jQuery("#open_dvtec").val(jQuery("#dvtec").height());
            	}

            	if (openAllDay == 'true') {
                    jQuery("#weekViewAllDaywk_container").height(parseInt(jQuery("#open_dvtec").val()));
            		jQuery("#dvtec").height(parseInt(jQuery("#open_weekViewAllDaywk_container").val()));
            		jQuery("#openAllDay").val('false');
                }
                else {
                	jQuery("#weekViewAllDaywk_container").height(parseInt(jQuery("#open_weekViewAllDaywk_container").val()));
            		jQuery("#dvtec").height(parseInt(jQuery("#open_dvtec").val()));
            		jQuery("#openAllDay").val('true');
                }
            });
            //crmv@26807e
        });

        function allDayMinimizerTd_click() {
			jQuery("#allDayClick").click();
        }

        function jClickCalendar_click(showButton) {
			jQuery('#' + showButton).click();
        }

        function allDayMinimizerTd_over() {
			jQuery("#allDayMinimizerTd").css('background-color','#E8EEF7');
        }

        function allDayMinimizerTd_out() {
			jQuery("#allDayMinimizerTd").css('background-color','');
        }

        function completedTask_over() {
			jQuery("#completedTaskButton").css('background-color','#FFFFFF');
        }

        function completedTask_out() {
			jQuery("#completedTaskButton").css('background-color','#E8EEF7');
        }
        //crmv@vte10usersFix e
        //crmv@26548
        function setHeightOpenAllDay() {
        	var el_h = ''
        	tr_h = 17;
    		var compare_h = '';
    		if (jQuery("#weekViewAllDaywk_container").height() > jQuery("#dvtec").height()) {
    			compare_h = jQuery("#weekViewAllDaywk_container").height();
    		}
    		else {
    			compare_h = jQuery("#dvtec").height();
    		}
    		//crmv@26629
    		dMax = jQuery('#weekViewAllDaywk tr').length;
    		el_h = compare_h - (tr_h * (dMax - 2)) + 1.5 * tr_h;
    		jQuery('#weekViewAllDaywk tr').slice(dMax - 2).height(el_h);
    		//crmv@26629e
        }
        //crmv@26548e

    </script>

</head>
<body>
	<div id="popupContainer" style="display:none;"></div> <!-- crmv@26807 -->
	<!-- crmv@20210 -->
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<td id="td-right">
	<!-- crmv@20210 e -->
		    <div>
		<!-- crmv@vte10usersFix -->
		      <div id="calhead" style="display:none;padding-left:1px;padding-right:1px;"> <!-- crmv@17001 -->
		            <div id="caltoolbar" class="ctoolbar" style="border-width: 1px;">

		            <!-- <div class="btnseparator"></div>
		            <div id="showtodaybtn" class="fbutton">
		                <div><span title='Click to back to today ' class="showtoday"><?php echo $app_strings['LBL_TODAY'];?></span></div>
		            </div>
		              <div class="btnseparator"></div> -->

					<div id="jClickCalendar_showdaybtn" onclick="jClickCalendar_click('showdaybtn')"></div>
					<div id="jClickCalendar_showweekbtn" onclick="jClickCalendar_click('showweekbtn')"></div>
					<div id="jClickCalendar_showmonthbtn" onclick="jClickCalendar_click('showmonthbtn')"></div>
					<div id="jClickCalendar_sfprevbtn" onclick="jClickCalendar_click('sfprevbtn')"></div>
					<div id="jClickCalendar_sfnextbtn" onclick="jClickCalendar_click('sfnextbtn')"></div>

		            <div id="showdaybtn" class="fbutton<?php if ($_REQUEST['activity_view'] == 'Today') echo " fcurrent"; ?>">
		                <div><span title='Day' class="showdayview"><?php echo $mod_strings['LBL_DAY'];?></span></div>
		            </div>
		              <div id="showweekbtn" class="fbutton<?php if ($_REQUEST['activity_view'] == 'This Week') echo " fcurrent"; ?>">
		                <div><span title='Week' class="showweekview"><?php echo $mod_strings['LBL_WEEK'];?></span></div>
		            </div>
		              <div id="showmonthbtn" class="fbutton<?php if ($_REQUEST['activity_view'] == 'This Month') echo " fcurrent"; ?>">
		                <div><span title='Month' class="showmonthview"><?php echo $mod_strings['LBL_MONTH'];?></span></div>
		            </div>
		            <div class="btnseparator"></div>
		            <div id="allDayClick" class="fbutton" style="display:none;">
		                <div><span title='allDayClick'>allDayClick</span></div>
		            </div>
		            <!-- crmv@vte10usersFix e -->

		            <!-- crmv@20210 -->
		            <!--  <div  id="showreflashbtn" class="fbutton">
		                <div><span title='Refresh view' class="showdayflash"><?php echo $app_strings['Refresh'];?></span></div>
		                </div>
		            <div class="btnseparator"></div> -->
		            <!-- crmv@20210e -->

		            <div id="sfnextbtn" title="Next" class="fbutton" style="float: right;">
		                <span class="fnext"></span>
		            </div>
		            <div id="sfprevbtn" title="Prev" class="fbutton" style="float: right;">
		              <span class="fprev"></span>
		            </div>
		            <div class="fshowdatep" style="float: right;">
						<div>
							<input type="hidden" name="txtshow" id="hdtxtshow" />
							<span id="txtdatetimeshow">Loading</span>
							<!-- crmv@24904 -->
							<input type="hidden" id="txtdatetimeshow_hidden"/>
							<!-- crmv@24904e -->
							<!-- crmv@vte10usersFix -->
							<input type="hidden" id="openAllDay" value="true"/>
							<input type="hidden" id="open_dvtec"/>
							<input type="hidden" id="open_weekViewAllDaywk_container"/>
							<!-- crmv@vte10usersFix e -->
						</div>
		            </div>
		            <!-- crmv@20210 e -->

					<div style="float: right; padding-right: 10px;">
						<div id="loadingpannel" class="fbutton"><i class="dataloader" data-loader="circle"></i></div>
						<div id="errorpannel" class="fbutton" style="display: none; color: red;"><?php echo $mod_strings['LBL_CAL_INTERRUPTED'];?></div><!-- crmv@vte10usersFix -->
					</div>

		            <!-- <div class="clear"></div> -->
		            </div>
		      </div>
		      <div>

		        <div class="t1 chromeColor">
		            &nbsp;</div>
		        <div class="t2 chromeColor">
		            &nbsp;</div>
		        <div id="dvCalMain" class="calmain printborder">
		            <div id="gridcontainer" style="overflow-y: visible;">
		            </div>
		        </div>
		        <div class="t2 chromeColor">

		            &nbsp;</div>
		        <div class="t1 chromeColor">
		            &nbsp;
		        </div>
		        </div>

			</div>
	<!-- crmv@20210 -->
		</td>
		<td width="175px" style="vertical-align:top;" id="td-calendar-users">
			<div id="filterCalendar" style="width:98%;margin:1px"></div>
			<script>$("filterCalendar").innerHTML = parent.window.document.getElementById("filterCalendar").innerHTML;</script>
			<?php if ($edit_permission == 'yes') { ?>
			<!--
	            <div id="faddbtn" class="fbutton">
					<div><span title='Click to Create New Event' class="addcal" id="wdCalendarAddButton"></span></div>
	                <script>if (parent.window.document.getElementById("addButton")) $("wdCalendarAddButton").innerHTML = parent.window.document.getElementById("addButton").innerHTML;</script>
	           	</div>
			-->
           	<?php } ?>
            <div id="filterUserCalendar"></div>
            <script>$("filterUserCalendar").innerHTML = parent.window.document.getElementById("filterUserCalendar").innerHTML;</script>
		</td>
	</table>
	<!-- crmv@20210 e -->
</body>
</html>
