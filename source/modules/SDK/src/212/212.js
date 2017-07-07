/* crmv@95157 */

var notaccess =document.getElementById(txtBox);
var tagValue = notaccess.options[notaccess.selectedIndex].text;
if(tagValue == alert_arr.LBL_NOT_ACCESSIBLE)
	getObj(dtlView).innerHTML = "<font color='red'>"+get_converted_html(tagValue)+"</font>";
else
	getObj(dtlView).innerHTML = get_converted_html(tagValue);