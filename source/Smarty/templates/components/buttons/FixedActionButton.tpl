{* /*+*************************************************************************************
* The contents of this file are subject to the VTECRM License Agreement
* ("licenza.txt"); You may not use this file except in compliance with the License
* The Original Code is: VTECRM
* The Initial Developer of the Original Code is VTECRM LTD.
* Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
* All Rights Reserved.
***************************************************************************************/ *}

{* -------------------------------------
ID: @string the id of the button
CLASS: @string the class of the button
POSITION: @string the css position of the button (absolute/fixed)
ORIENTATION: @string the orientation of the list (left/right/top/bottom)

BUTTON_ID: @string the id of the main button
BUTTON_CLASS: @string the class of the main button
BUTTON_TYPE: @string the type of the main button (default/primary/success/warning/danger/info)
BUTTON_ICON: @string the icon of the main button
BUTTON_MINI: @boolean true/false
------------------------------------- *}

{literal}
	<style>
		.fixed-action-btn[data-position="absolute"] {
			position: absolute;
			top: 50%;
			left:50%;
		}
		
		.fixed-action-btn[data-position="fixed"] {
			position: fixed;
		}
		
		.fixed-action-btn ul {
		    left: 0;
		    right: 0;
		    text-align: center;
		    position: absolute;
		    bottom: 64px;
		    margin: 0;
		    padding-left: 0;
    		list-style-type: none;
    		visibility: hidden;
		}
		
		.fixed-action-btn.active ul {
    visibility: visible;
}
		
		.fixed-action-btn[data-orientation="top"] ul {
			top: auto;
			bottom: 64px;
		}
		
		.fixed-action-btn[data-orientation="bottom"] ul {
			bottom: auto;
			top: 64px;
		}
		
		.fixed-action-btn[data-orientation="left"] ul {
			left: auto;
			right: 64px;
			text-align: right;
		}
		
		.fixed-action-btn[data-orientation="right"] ul {
			right: auto;
			left: 64px;
			text-align: left;
		}
		
		.fixed-action-btn[data-orientation="left"] ul,
		.fixed-action-btn[data-orientation="right"] ul {
			top: 50%;
		    transform: translateY(-50%);
			-webkit-transform: translateY(-50%);
			-moz-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
		    height: 100%;
		    width: 500px;
		}
		
		.fixed-action-btn[data-orientation="left"] ul li {
		    display: inline-block;
    		margin: 5px 15px 0 0;
    	}
    	
    	.fixed-action-btn[data-orientation="right"] ul li {
    	 	display: inline-block;
    		margin: 5px 0 0 15px;
    	}
		
		.fixed-action-btn[data-orientation="top"] ul li {
		    margin-bottom: 15px;
		}
		
		.fixed-action-btn[data-orientation="bottom"] ul li {
		    margin-top: 15px;
		}
	</style>
	
	<script type="text/javascript">
	jQuery.fn.reverse = [].reverse;
		jQuery(document).on('click', '.fixed-action-btn.click-to-toggle > a', function(e) {
      var $this = jQuery(this);
      var $menu = jQuery(this).parent();
      console.log("MENU", $this);
      if ($menu.hasClass('active')) {
        closeFABMenu($menu);
      } else {
        openFABMenu($menu);
      }
    });
    
   var openFABMenu = function (btn) {
    var $this = btn;
    if ($this.hasClass('active') === false) {

      // Get direction option
      var horizontal = $this.hasClass('horizontal');
      var offsetY, offsetX;

      if (horizontal === true) {
        offsetX = 40;
      } else {
        offsetY = 40;
      }

      $this.addClass('active');
      $this.find('ul .btn-fab').velocity(
        { scaleY: ".4", scaleX: ".4", translateY: offsetY + 'px', translateX: offsetX + 'px'},
        { duration: 0 });

      var time = 0;
      $this.find('ul .btn-fab').reverse().each( function () {
        $(this).velocity(
          { opacity: "1", scaleX: "1", scaleY: "1", translateY: "0", translateX: '0'},
          { duration: 80, delay: time });
        time += 40;
      });
    }
  };

  var closeFABMenu = function (btn) {
    var $this = btn;
    // Get direction option
    var horizontal = $this.hasClass('horizontal');
    var offsetY, offsetX;

    if (horizontal === true) {
      offsetX = 40;
    } else {
      offsetY = 40;
    }

    $this.removeClass('active');
    var time = 0;
    $this.find('ul .btn-fab').velocity("stop", true);
    $this.find('ul .btn-fab').velocity(
      { opacity: "0", scaleX: ".4", scaleY: ".4", translateY: offsetY + 'px', translateX: offsetX + 'px'},
      { duration: 80 }
    );
  };
	</script>
{/literal}

{if empty($POSITION)} {assign var=POSITION value="absolute"} {/if}
{if empty($ORIENTATION)} {assign var=ORIENTATION value="top"} {/if}

<div {if !empty($ID)}id="{$ID}"{/if} class="fixed-action-btn click-to-toggle{if !empty($CLASS)} {$CLASS}{/if}" data-position="{$POSITION}" data-orientation="{$ORIENTATION}">
	
	{include file="components/buttons/FabButton.tpl" ID=$BUTTON_ID CLASS=$BUTTON_CLASS TYPE=$BUTTON_TYPE ICON=$BUTTON_ICON MINI=$BUTTON_MINI}
	
	<ul>
		<li><a class="btn btn-fab btn-fab-mini"><i class="vteicon">insert_chart</i></a></li>
		<li><a class="btn btn-fab btn-fab-mini"><i class="vteicon">format_quote</i></a></li>
		<li><a class="btn btn-fab btn-fab-mini"><i class="vteicon">publish</i></a></li>
		<li><a class="btn btn-fab btn-fab-mini"><i class="vteicon">attach_file</i></a></li>
	</ul>
	
</div>
