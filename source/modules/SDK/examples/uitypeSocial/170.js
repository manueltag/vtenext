var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"fbico.png\" align=\"left\" alt=\"Facebook\" title=\"Facebook\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://www.facebook.com/"+tagValue+"\">http://www.facebook.com/"+tagValue+"</a>";
}