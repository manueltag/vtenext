<title><?php echo $title; ?></title>
<meta charset="UTF-8" />
<link rel="SHORTCUT ICON" href="<?php echo get_logo_install('favicon'); ?>" />	<!-- crmv@18123 -->
<script type="text/javascript" src="include/js/jquery.js"></script>	<!-- crmv@26523 -->
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<link href="themes/next/vte_bootstrap.css" rel="stylesheet" type="text/css" />
<link href="themes/next/style.css" rel="stylesheet" type="text/css" />
<link href="themes/next/install.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="include/js/jquery.js"></script>
<script type="text/javascript" src="themes/next/js/material/material.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery.material.options.withRipples += ",.crmbutton:not(.withoutripple)";
		jQuery.material.init();
	});
</script>