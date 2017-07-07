<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>VTECRM - Portale</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Comments timeline CSS -->
    <link href="css/timeline.css" rel="stylesheet">
    
    <link href="css/material_design/material.min.css" rel="stylesheet">
    <link href="css/material_design/roboto.min.css" rel="stylesheet">
    <link href="css/material_design/material-fullpalette.min.css" rel="stylesheet">
    <link href="css/material_design/ripples.min.css" rel="stylesheet">
    <link href="css/material_design/ripples.min.css" rel="stylesheet"> 
    <link href="css/material_design/material-icon.css" rel="stylesheet">

<!-- Prototype library clashes with AutoComplete library in use so avoid on those pages	-->
  	{if $fun neq 'newticket'}
    	<script language="javascript" type="text/javascript" src="js/prototype.js"></script>
	{/if}

	<script language="javascript" type="text/javascript" src="js/general.js"></script>
	<script>
	{literal}
		function fnMySettings(){
			params = "last_login={$last_login}&support_start_date={$support_start_date}&support_end_date={$support_end_date}";
			window.open("MySettings.php?"+params,"MySettings","menubar=no,location=no,resizable=no,scrollbars=no,status=no,width=400,height=350,left=550,top=200");
		}
	{/literal}
	</script>
	<script type="text/javascript">
	{literal}
		function showSearchFormNow(elementid) {
			fnDown(elementid);
			//document.getElementById("tabSrch_progress").style.display = '';
			//document.getElementById("tabSrch_progress").style.margin = '-25px -75px 0px 0px';
			if($(elementid).loaded) {
				return;
			} else {
				// Squeeze the search div wrapper
				$(elementid).style.width = '100px';
			}
	
			var url = 'module=HelpDesk&action=SearchForm&ajax=true';
	
			new Ajax.Request(
				'index.php', {queue: {position: 'end', scope: 'command'},
				method: 'post',
				postBody:url,
				onComplete: function(response){
						// Set the width of search div wrapper
						$(elementid).style.width = '100%';
						$('_search_formelements_').innerHTML = response.responseText;
						$(elementid).loaded = true;
						//$(elementid+'_progress').hide();
				}
			});
		}
		{/literal}
		</script>
</head>
<body>
<!-- crmv@57342e-->
    <!-- Navigation -->
   <!-- crmv@57342 <a id="menu-toggle" href="#" class="btn btn-dark btn-lg toggle"><img src="images/open-menu.png"></a>-->
    <nav id="sidebar-wrapper">
        <ul class="sidebar-nav">
           <!-- <a id="menu-close" href="#" class="btn-light btn-lg pull-right toggle"><img src="images/open-menu.png"></a> -->
            <a id="menu-close" href="#" class="pull-right toggle">
			     <i class="material-icons material-icons-menu icon_highlight_off" style="font-size:40px;"></i>
            </a>

            <!-- crmv@57342 -->
            <li><a href="http://www.vtecrm.com/" target="_blank"><img src="images/VTE_login.png" class="logo"/></a></li>
            <li class="sidebar-brand">
            <a class="menu slidemenu-vte-more" href="index.php?module=HelpDesk&action=index&fun=newticket">
				<i class="material-icons icon_default icon_menuvte icon_new_ticket"></i>
				<span class="slidemenu-vte-label-more">{'LBL_NEW_TICKET'|getTranslatedString}</span>
			</a><hr class="hr-vte">
            	{foreach from=$showmodulemenu item=showmodule}
					<a class='menu {$showmodule.class_css}' href='index.php?module={$showmodule.module}&action=index&onlymine=true'>
						<i class='material-icons icon_default icon_{$showmodule.icon} icon_menuvte' data-first-letter="{$showmodule.first_letter}"></i>
						<span class="{$showmodule.class_css_label}">{$showmodule.module|getTranslatedString}</span>
					</a><hr class='hr-vte'>
				{/foreach}
				<a href="index.php?module=Contacts&action=index&id={$customerid}&profile=yes" class="menu slidemenu-vte">
					<i class='material-icons icon_default icon_menuvte icon_info'></i>
					<span class="slidemenu-vte-label">{'LBL_MODIFY_PROFILE'|getTranslatedString}</span>
				</a><hr class="hr-vte">
       			<a href="index.php?logout=true" class="menu slidemenu-vte">
					<i class='material-icons icon_default icon_menuvte icon_exit_to_app'></i>
					<span class="slidemenu-vte-label">{'LBL_LOG_OUT'|getTranslatedString}</span>
				</a><hr class="hr-vte">
				<div style="height:50px"></div>
            </li>
        </ul>
    </nav>
   
     <div id="page-wrapper">
     	<div class="container-fluid">		

		    <!-- jQuery Version 1.11.0 -->
		    <script src="js/jquery-1.11.0.js"></script>
		   			   	
		    <!--  overflow -->
		    <link href="js/mCustomScrollbar/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
			<script src="js/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
			<script language="javascript" type="text/javascript" src="js/slimscroll/jquery.slimscroll.min.js"></script>
			<link href="js/mCustomScrollbar/VTE.mCustomScrollbar.css" rel="stylesheet" type="text/css" />
		
		    <!-- Custom Menu JavaScript -->
		    <script>
			{literal}
		    //crmv@57342
		    $("#menu-close").click(function(e) {
		        e.preventDefault();
		        $("#sidebar-wrapper").toggleClass("active");
		        $("#page-wrapper").toggleClass("active");
		        
		        
		        if($( "#sidebar-wrapper" ).hasClass( "active" )){
					$(".material-icons-menu").removeClass("icon_highlight_off");
		        	$(".material-icons-menu").addClass('icon_menu');
		        }else{
					$(".material-icons-menu").removeClass("icon_menu");
		        	$(".material-icons-menu").addClass('icon_highlight_off');
		        }
		    });
			{/literal}
		    </script>
		    <script>
			{literal}
			  (function() {
			
			    "use strict";
			
			    var toggles = document.querySelectorAll(".c-hamburger");
			
			    for (var i = toggles.length - 1; i >= 0; i--) {
			      var toggle = toggles[i];
			      toggleHandler(toggle);
			    };
			
			    function toggleHandler(toggle) {
			      toggle.addEventListener( "click", function(e) {
			        e.preventDefault();
			        (this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
			      });
			    }
			
			  })();
			{/literal}
			</script>
		 	<!--  overflow -->
			<script>
			{literal}
				jQuery(document).ready(function (){
					//window.jQuery = $.noConflict();
					jQuery('.sidebar-nav').slimScroll({
						width: '250px',
						height: '100%',
					})
				});
			{/literal}
			</script>
</body>
</html>