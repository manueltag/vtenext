{* crmv@124736 *}
{if $_mode neq 'inside_script'}
<script type="text/javascript">
{/if}
{literal}
var isInIFrame = (window.location != window.parent.location);
if (isInIFrame == true) {
	jQuery("#leftPanel").hide();
	jQuery("#rightPanel").hide();

	jQuery("#mainContent").css('margin-left','0px');
	jQuery("#mainContent").width('100%');
	jQuery(".vteCenterHeader").css('width','100%');
	
	jQuery('#turboLiftContainer .mCustomScrollbar').css('width','inherit');
	jQuery('#turboLiftContainer .mCustomScrollbar').css('right','inherit');
	jQuery('#turboLiftContainer .mCustomScrollbar').css('padding-right','15px');
} else {
	jQuery("#leftPanel").show();
	jQuery("#rightPanel").show();
}
{/literal}
{if $_mode neq 'inside_script'}
</script>
{/if}