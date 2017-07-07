<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/json2.js"></script>	
<script language="JavaScript" type="text/javascript" src="include/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/jquery_plugins/form.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/<?php echo $_REQUEST['language'];?>.lang.js"></script>
</head>
<body>
<form id="lang" name="lang" action="index.php" method="post">
	<input type="hidden" name="module" value="SDK">
	<input type="hidden" name="file" value="WriteJsLang">
	<input type="hidden" name="action" value="SDKAjax">
	<input type="hidden" name="language" id="language" value="<?php echo $_REQUEST['language'];?>">
	<input type="hidden" name="params" id="params" value="">
<script>
var options = {
    dataType:'json'
};
jQuery('#lang').ajaxForm(options);
</script>
<script>
	jQuery('#params').val(JSON.stringify(alert_arr));
	jQuery('#lang').submit();
</script>	
</form>
</body>
</html>