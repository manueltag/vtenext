var imgdir = 'modules/SDK/examples/uitypeSocial/img/';
getObj(dtlView).innerHTML = "<img src=\""+imgdir+"bkico.png\" align=\"left\" alt=\"VKontakte\" title=\"VKontakte\"/>";
if (tagValue != '') {
  getObj(dtlView).innerHTML += "<a target=\"_blank\" href=\"http://vkontakte.ru/"+tagValue+"\">http://vkontakte.ru/"+tagValue+"</a>";
}