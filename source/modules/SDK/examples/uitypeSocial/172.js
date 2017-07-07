var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTM = "<img src=\""+imgdir+"ytico.png\" align=\"left\" alt=\"YouTube\" title=\"YouTube\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://www.youtube.com/user/"+tagValue+"\">http://www.youtube.com/user/"+tagValue+"</a>";
}