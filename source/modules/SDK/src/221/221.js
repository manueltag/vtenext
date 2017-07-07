var notaccess = document.getElementById(txtBox);
displayValue = notaccess.options[notaccess.selectedIndex].text;
getObj(dtlView).innerHTML = "<a href=\"index.php?module=Settings&action=RoleDetailView&parenttab=Settings&roleid="+tagValue+"\">"+displayValue+"</a>";