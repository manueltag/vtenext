var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"gpico.png\" align=\"left\" alt=\"Google+\" title=\"Google+\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"https://plus.google.com/u/0/"+tagValue+"\">https://plus.google.com/u/0/"+tagValue+"</a>";
}