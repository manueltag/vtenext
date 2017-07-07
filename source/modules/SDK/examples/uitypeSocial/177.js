var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"qzico.png\" align=\"left\" alt=\"QZone\" title=\"QZone\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://"+tagValue+".qzone.qq.com\">http://"+tagValue+".qzone.qq.com</a>";
}