var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"orico.png\" align=\"left\" alt=\"Orkut\" title=\"Orkut\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://www.orkut.com/Main#Profile?uid="+tagValue+"\">http://www.orkut.com/Main#Profile?uid="+tagValue+"</a>";
}