<?php
/***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************/

//crmv@26666
function getOracleReservedWords() {
	$reserved_words = array("access","else","modify","start","add","exclusive","noaudit",
							"select","all","exists","nocompress","session","alter","file",
							"not","set","and","float","notfound","share","any","for",
							"nowait","size","arraylen","from","null","smallint","as",
							"grant","number","sqlbuf","asc","group","of","successful",
							"audit","having","offline","synonym","between","identified",
							"on","sysdate","by","immediate","online","table","char","in",
							"option","then","check","increment","or","to","cluster",
							"index","order","trigger","column","initial","pctfree",
							"uid","comment","insert","prior","union","compress",
							"integer","privileges","unique","connect","intersect",
							"public","update","create","into","raw","user","current",
							"is","rename","validate","date","level","resource","values",
							"decimal","like","revoke","varchar","default","lock",
							"row","varchar2","delete","long","rowid","view","desc",
							"maxextents","rowlabel","whenever","distinct","minus",
							"rownum","where","drop","mode","rows","with",
							"mandatory", "shared");
	return $reserved_words;
}
//crmv@26666e
function getMsSQLReservedWords() {
	$reserved_words = getOracleReservedWords();
	$reserved_words[] = 'bulk';
	return $reserved_words;
}
?>