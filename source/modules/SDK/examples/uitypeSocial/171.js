var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"liico.png\" align=\"left\" alt=\"Linkedin\" title=\"Linkedin\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://www.linkedin.com/in/"+tagValue+"\">http://www.linkedin.com/in/"+tagValue+"</a>";
}