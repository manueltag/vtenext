/* crmv@101683 */
if(isAdmin == "1")
	getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text+"&nbsp;</a>";
else
	getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text;