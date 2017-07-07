{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@46468 *}
<script type="text/javascript">
{literal}
jQuery(document).ready(function(){
	parent.jQuery('.fancybox-close').unbind();
	parent.jQuery('.fancybox-close').bind('click', function(){
		parent.location.reload();
	});
	parent.jQuery('.fancybox-overlay').unbind();
	parent.jQuery('.fancybox-overlay').bind('click', function(){
		parent.location.reload();
	});
});
{/literal}
</script>