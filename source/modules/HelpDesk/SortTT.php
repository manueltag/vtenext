<?php
/*********************************************************************************
** The contents of this file are subject to the Mozilla Public License. You may
*not use this file except in compliance with the License
* The Original Code is: JPLTSolucio, S,L, Open Source based on vTiger CRM
* The Initial Developer of the Original Code is JPLTSolucio, S.L.
* Portions created by vtiger are Copyright (C) vtiger. All Rights Reserved.
*
 ********************************************************************************/

require_once('modules/HelpDesk/HelpDesk.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/CommonUtils.php');

global $adb,$app_strings,$mod_strings,$products_per_page,$focus;

function getUserSelectBox($user_array)
{
    global $adb,$current_user;

    $user_combo = '<select name="user_helpdesk">';
    foreach($user_array as $user_id => $user_name)
    {
        $selected = '';
        if($user_id == $current_user->id)
        {   
            $selected = 'selected';
        }
        $user_combo .= '<OPTION value="'.$user_id.'" '.$selected.'>'.$user_name.'</OPTION>';
    }
    $user_combo .= '</select>';

    return $user_combo;
}

function printButtons() {
	global $table_prefix;
	print '<br><p align=center>';
    print '<input title="'.getTranslatedString('sortReturn','Timecards').' [Alt+'.getTranslatedString('sortReturnKey').']" accessKey="'.getTranslatedString('sortReturnKey','Timecards').'" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="'.getTranslatedString('sortReturn','Timecards').'" style="width:90px">';
    //print '<input title="'.getTranslatedString('sortClose','Timecards').' [Alt+'.getTranslatedString('sortCloseKey').']" accessKey="'.getTranslatedString('sortCloseKey','Timecards').'" class="crmbutton small cancel" onclick="window.close()" type="button" name="button" value="'.getTranslatedString('sortClose','Timecards').'" style="width:90px">';
    print '</p>';
}

switch ($_REQUEST['PRC']) {
   case 'seePendingWork':

    if (empty($_REQUEST['user_helpdesk'])){
      print '<BR><B><font color="red">'.getTranslatedString('sortUserNotDefined').'</font></B>';
      printButtons();
      break;
    }
    
    /* buscamos agente */
    $username = getUserFullName($_REQUEST['user_helpdesk']);
    /* empezamos formulario */
    print '<H3 align=center>'.getTranslatedString('sortWOforUser')." $username</H3>";
    
    /* buscamos sus trabajos pendientes ordenados por antiguedad */
    $select  = 'select '.$table_prefix.'_troubletickets.ticketid,title,createdtime,parent_id,'.$table_prefix.'_troubletickets.status ';
    $select .= 'from '.$table_prefix.'_troubletickets inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_troubletickets.ticketid=crmid ';
    $select .= ' inner join '.$table_prefix.'_ticketcf on '.$table_prefix.'_ticketcf.ticketid='.$table_prefix.'_troubletickets.ticketid ';
    $select .= "where '.$table_prefix.'_troubletickets.status!='Closed' and smownerid=".$_REQUEST['user_helpdesk'].' and deleted=0 ';
    $select .= 'order by createdtime;';
       
    $resots = $adb->query($select);
    $NROT=$adb->num_rows($resots);

    if ($NROT==0) {
      print '<BR><B><font color="red">'.getTranslatedString('sortNoWOPending').'</font></B>';
      printButtons();
      break;
    }

     print '<FORM ACTION="index.php?module=HelpDesk&action=SortTT&return_module=HelpDesk&return_action=index" method=post>';
     print '<input type=hidden name="user_helpdesk" value="'.$_REQUEST['user_helpdesk'].'">';
     print '<input type=hidden name=PRC value=seeOrderedWork>';
     /* imprimimos trabajos */
      print '<table width=90% border=0 nosave align=center>';
      print '<tr><th>'.getTranslatedString('sortOrder').'</th><th align=left>'.getTranslatedString('sortTitle').'</th><th align=left>'.$mod_strings['LBL_CREATED_DATE'].'</th><th align=left>'.$mod_strings['LBL_ACCOUNT'].'/'.$mod_strings['LBL_CONTACT'].'</th><th align=left>'.$mod_strings['LBL_STATUS'].'</th></tr>';
      for ($j=0;$j<$NROT;$j++) {
      	$row=$adb->fetchByAssoc($resots,$j);
        print '<tr valign=top';
        if ($j % 2 != 0) {
          print ' bgcolor="#e6e6e6">';
        } else {
         print '>';
        }
        print '<td align=center><input type=text size=2 name="o'.$j.'"><input type=hidden name="p'.$j.'" value="'.$row['ticketid'].'"></td>';
        print '<td>'.$row['title'].'</td>';
        print '<td>'.$row['createdtime'].'</td>';
        if (empty($row['parent_id'])) {
                $ref_name=getTranslatedString('MSG_NoSalesEntity');
        } elseif (getSalesEntityType($row['parent_id'])=='Accounts') {
                $ref_name=getAccountName($row['parent_id']);
        } else {
                $ref_name=getContactName($row['parent_id']);
        }
        print '<td><font size="-1">'.$ref_name.'</font></td>';
        print '<td><font size="-1">'.$row['status'].'</font></td></tr>';
      }
      print '</table>';
      print '<p align=center><input title="'.getTranslatedString('sortReport').' [Alt+'.getTranslatedString('sortReportKey').']" accessKey="'.getTranslatedString('sortReportKey').'" class="crmbutton small cancel" onclick="submit()" type="button" name="PRC" value="'.getTranslatedString('sortReport').'" style="width:100px">';
      print '<input title="'.getTranslatedString('sortReturn').' [Alt+'.getTranslatedString('sortReturnKey').']" accessKey="'.getTranslatedString('sortReturnKey').'" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="'.getTranslatedString('sortReturn').'" style="width:90px">';
      //print '<input title="'.getTranslatedString('sortClose').' [Alt+'.getTranslatedString('sortCloseKey').']" accessKey="'.getTranslatedString('sortCloseKey').'" class="crmbutton small cancel" onclick="window.close()" type="button" name="button" value="'.getTranslatedString('sortClose').'" style="width:90px">';
      include('modules/VteCore/footer.php');	//crmv@30447
      break;
    
   /* termina ver trabajos */

   case 'seeOrderedWork':

    if (empty($_REQUEST['user_helpdesk'])){
      print '<BR><B><font color="red">'.getTranslatedString('sortUserNotDefined').'</font></B>';
      printButtons();
      break;
    }
    
    /* buscamos agente */
    $username = getUserFullName($_REQUEST['user_helpdesk']);
    
    /* ordenamos sus trabajos pendientes */
    /* primero necesito saber cuantos hay */
    $select  = 'select count(*) as cnt ';
    $select .= 'from '.$table_prefix.'_troubletickets inner join '.$table_prefix.'_crmentity on '.$table_prefix.'_troubletickets.ticketid=crmid ';
    $select .= "where ".$table_prefix."_troubletickets.status!='Closed' and smownerid=".$_REQUEST['user_helpdesk'].' and deleted=0 ';
    $resots = $adb->query($select);
    $NROT=$adb->query_result($resots,0,0);
    /* ahora monto un array con valores pasados de ordenacion y queda ordenado por los numeros pasados */
    for ($i=0;$i<$NROT;$i++) {
      $ord="o".$i;
      $ot="p".$i;
      if (!empty($_REQUEST[$ord])) $treballs[$_REQUEST[$ord]]=$_REQUEST[$ot];
    }

    /* empezamos listado */
    print '<table width=95% cellpadding=0 cellspacing=0 border=0><tr>';
    print '<td align=center><H3>'.getTranslatedString('sortPendingWO').$username.'</H3></td>';
    print '<td align=right width=150>'.date('Y/m/d').'</td></tr></table>';
    print '<table width=100% border=0 nosave><tbody><tr>';
    print '<th width=4% align=left>'.$mod_strings['LBL_TICKET'].'</th><th width=43% align=left>'.$mod_strings['LBL_ACCOUNT'].'/'.$mod_strings['LBL_CONTACT'].'</th><th width=43% align=left>'.getTranslatedString('sortMaterial').'</th></tr>';
    for($i=0; $i<=count($treballs); $i++) {
        /* buscamos el trabajo */
        $select  = 'select ticketid,title,parent_id,description ';
        $select .= 'from '.$table_prefix.'_troubletickets ';
        $select .= 'inner join '.$table_prefix.'_crmentity on crmid=ticketid ';
        $select .= 'where ticketid='.$treballs[$i];
        $resots = $adb->query($select);
        if ($adb->num_rows($resots)==0) continue;
        /* lo imprimimos */
        $row=$adb->fetchByAssoc($resots,0);
        print '<tr valign=top>';
        print '<td style="border-top:dotted;"><font size=-1>'.$row['title'].'</font></td>';
        if (empty($row['parent_id'])) {
            $ref_name=getTranslatedString('MSG_NoSalesEntity');
            $ref_tel  = '';
            $ref_mov  = '';
            $ref_dir  = '';
            $ref_pob  = '';
        } elseif (getSalesEntityType($row['parent_id'])=='Contacts') {
            $sql = "select firstname,lastname,phone,mobile,mailingstreet,mailingcity from ".$table_prefix."_contactdetails,".$table_prefix."_contactaddress where contactid=".$row['parent_id']." and contactid=contactaddressid";
            $rs = $adb->query($sql);
            $ref_name = $adb->query_result($rs,0,'firstname').' '.$adb->query_result($rs,0,'lastname');
            $ref_tel  = $adb->query_result($rs,0,'phone');
            $ref_mov  = $adb->query_result($rs,0,'mobile');
            $ref_dir  = $adb->query_result($rs,0,'mailingstreet');
            $ref_pob  = $adb->query_result($rs,0,'mailingcity');
        } else {
            $sql = "select accountname,phone,otherphone,bill_street,bill_city from ".$table_prefix."_account,".$table_prefix."_accountbillads where accountid=".$row['parent_id']." and accountid=accountaddressid";
            $rs = $adb->query($sql);
            $ref_name = $adb->query_result($rs,0,'accountname');
            $ref_tel  = $adb->query_result($rs,0,'phone');
            $ref_mov  = $adb->query_result($rs,0,'otherphone');
            $ref_dir  = $adb->query_result($rs,0,'street');
            $ref_pob  = $adb->query_result($rs,0,'city');
        }
        print '<td style="border-top:dotted;"><font size=-1>'.$ref_name.'<br/>';
        print $ref_dir.' - '.$ref_pob.'<br/>';
        print $ref_tel.' - '.$ref_mov.'<br/>';
        print '<hr align=center width=85%/>';
        print $row['description'];
        print '</font></td>';
        $select = "select tcunits,description
                   from ".$table_prefix."_tttimecards
                   inner join ".$table_prefix."_crmentity on crmid=tmecardsid 
                   where ticket_id='".$row['ticketid']."' and tcunits!=0 and deleted=0;";
        $restcs = $adb->query($select);
        print '<td style="border-top:dotted;"><font size=-1>';
        if ($adb->num_rows($restcs)!=0) {
          /* lo imprimimos */
          $nrtc=$adb->num_rows($restcs);
          for ($tcs=0;$tcs<$nrtc;$tcs++) {
            $rowtc = $adb->fetchByAssoc($restcs,$tcs);
            print $rowtc['tcunits'].' => '.nl2br(decode_html(stripslashes($rowtc['description']))).'<br/>';
          }
        } else {
          print '&nbsp;';
        }
        print '</font></td>';
    }
    print '</tbody></table>';
    exit;
    break;
    
   /* termina generar listado */

   default:
       /* si no hacemos nada, sacamos usuarios */
       print '<H2 align=center>'.getTranslatedString('sortWO').'</H2>';
       print '<FORM ACTION="index.php?module=HelpDesk&action=SortTT&return_module=HelpDesk&return_action=index" method=post>';
       print '<input type=hidden name=PRC value=seePendingWork>';
       print '<table align=center border=0 cellpadding=0 cellspacing=0><tbody><tr><td width=70 align=center>'.$mod_strings['LBL_ASSIGNED_TO'].'</td><td>';
       $user_array = get_user_array(false);
       print getUserSelectBox($user_array);
       print '</td>';
       print '<td width=100 align=center><input title="'.getTranslatedString('sortSeeWO').' [Alt+'.getTranslatedString('sortSeeWOKey').']" accessKey="'.getTranslatedString('sortSeeWOKey').'" class="crmbutton small cancel" onclick="submit()" type="button" name="PRC" value="'.getTranslatedString('sortSeeWO').'" style="width:110px"></TD>';
       //print '<td width=100 align=center><input title="'.getTranslatedString('sortClose').' [Alt+'.getTranslatedString('sortCloseKey').']" accessKey="'.getTranslatedString('sortCloseKey').'" class="crmbutton small cancel" onclick="window.close()" type="button" name="button" value="'.getTranslatedString('sortClose').'" style="width:90px"></TD>';
       print "</tbody></table></form>";
       include('modules/VteCore/footer.php');	//crmv@30447
       break;
}
  
?>
