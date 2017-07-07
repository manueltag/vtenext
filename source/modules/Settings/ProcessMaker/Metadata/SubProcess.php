<?php
/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/
/* crmv@97575 */
$smarty->assign("MODE",$type);
$smarty->assign("HEADER", $PMUtils->getHeaderList(true));
$smarty->assign("LIST", $PMUtils->getList(true,$id,$vte_metadata_arr['subprocess']));
$sub_template = 'Settings/ProcessMaker/List.tpl';