var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"twico.png\" align=\"left\" alt=\"Twitter\" title=\"Twitter\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://twitter.com/"+tagValue+"\">http://twitter.com/"+tagValue+"</a>";
}