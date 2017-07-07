<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php');
global $current_user,$table_prefix;
 $fldmodule=$_REQUEST['fld_module'];
 $fldlabel=$_REQUEST['fldLabel'];
 $fldType= $_REQUEST['fieldType'];
 $parenttab=$_REQUEST['parenttab'];
 $mode=$_REQUEST['mode'];

$tabid = getTabid($fldmodule);

if(get_magic_quotes_gpc() == 1)
{
	$fldlabel = stripslashes($fldlabel);
}


//checking if the user is trying to create a custom vtiger_field which already exists  
if($mode != 'edit')
{
	$checkquery="select * from ".$table_prefix."_field where tabid='".$tabid."'and fieldlabel='".$fldlabel."'";
	$checkresult=$adb->query($checkquery);
}
else
	$checkresult=0;

if($adb->num_rows($checkresult) != 0)
{
	
	if(isset($_REQUEST['fldLength']))
	{	
		$fldlength=$_REQUEST['fldLength'];
	}
	else
	{
		 $fldlength='';
	}
	if(isset($_REQUEST['fldDecimal']))
	{
		$flddecimal=$_REQUEST['fldDecimal'];
	}
	else
	{
		$flddecimal='';
	}
	if(isset($_REQUEST['fldPickList']))
	{
		$fldPickList=$_REQUEST['fldPickList'];
	}
	else
	{
		$fldPickList='';
	}
	
		header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fldmodule."&fldType=".$fldType."&fldlabel=".$fldlabel."&parenttab=".$parenttab."&duplicate=yes");
}
else
{
	if($_REQUEST['fieldid'] == '')
	{
		$max_fieldid = $adb->getUniqueID($table_prefix."_field");
		$columnName = 'cf_'.$max_fieldid;
	}
	else
	{
		$max_fieldid = $_REQUEST['column'];
		$columnName = $max_fieldid;
	}
  
	//Assigning the vtiger_table Name
	$tableName ='';
	if($fldmodule == 'HelpDesk')
	{
		$tableName=$table_prefix.'_ticketcf';
	}
	elseif($fldmodule == 'Products')
	{
		$tableName=$table_prefix.'_productcf';
	}
	elseif($fldmodule == 'Vendors')
	{
		$tableName=$table_prefix.'_vendorcf';
	}
	elseif($fldmodule == 'PriceBooks')
	{
		$tableName=$table_prefix.'_pricebookcf';
	}
	elseif($fldmodule != '')
	{
		$tableName= $table_prefix.'_'.strtolower($fldmodule).'cf';
	}
	//Assigning the uitype
	$fldlength=$_REQUEST['fldLength'];
	$uitype='';
	$fldPickList='';
	if(isset($_REQUEST['fldDecimal']) && $_REQUEST['fldDecimal'] != '')
	{
		$decimal=$_REQUEST['fldDecimal'];
	}
	else
	{
		$decimal=0;
	}
	$type='';
	$uichekdata='';
	if($fldType == 'Text')
	{
	$uichekdata='V~O~LE~'.$fldlength;
		$uitype = 1;
		$type = "C(".$fldlength.")"; // adodb type
	}
	elseif($fldType == 'Number')
	{
		$uitype = 7;

		//this may sound ridiculous passing decimal but that is the way adodb wants
		$dbfldlength = $fldlength + $decimal + 1;
 
		$type="N(".$dbfldlength.".".$decimal.")";	// adodb type
	$uichekdata='N~O~'.$fldlength .','.$decimal;
	}
	elseif($fldType == 'Percent')
	{
		$uitype = 9;
		$type="N(5.2)"; //adodb type
		$uichekdata='N~O~2~2';
	}
	elseif($fldType == 'Currency')
	{
		$uitype = 71;
		$dbfldlength = $fldlength + $decimal + 1;
		$type="N(".$dbfldlength.".".$decimal.")"; //adodb type
	$uichekdata='N~O~'.$fldlength .','.$decimal;
	}
	elseif($fldType == 'Date')
	{
	$uichekdata='D~O';
		$uitype = 5;
		$type = "D"; // adodb type
		
	}
	elseif($fldType == 'Email')
	{
		$uitype = 13;
		$type = "C(50)"; //adodb type
		$uichekdata='E~O';
	}
	elseif($fldType == 'Phone')
	{
		$uitype = 11;
		$type = "C(30)"; //adodb type
		
		$uichekdata='V~O';
	}
	elseif($fldType == 'Picklist')
	{
		$uitype = 15;
		$type = "C(255)"; //adodb type
		$uichekdata='V~O';
	}
	elseif($fldType == 'URL')
	{
		$uitype = 17;
		$type = "C(255)"; //adodb type
		$uichekdata='V~O';
	}
	elseif($fldType == 'Checkbox')	 
        {	 
                 $uitype = 56;	 
                 $type = "C(3) default 0"; //adodb type	 
                 $uichekdata='C~O';	 
        }
	elseif($fldType == 'TextArea')	 
        {	 
                 $uitype = 21;	 
                 $type = "X"; //adodb type	 
                 $uichekdata='V~O';	 
        }
	elseif($fldType == 'MultiSelectCombo')
	{
		 $uitype = 33;
		 $type = "X"; //adodb type
		 $uichekdata='V~O';
	}
	elseif($fldType == 'Skype')
	{
		$uitype = 85;
		$type = "C(255)"; //adodb type
		$uichekdata='V~O';
	}
	
	/* No Decimal Pleaces Handling
  ** 1. add the customfield vtiger_table to the vtiger_field vtiger_table as Block4
  ** 2. fetch the contents of the custom vtiger_field and show in the UI      
	** retreiving the sequence
	**/
	
	if($_REQUEST['fieldid'] == '')
	{
		$custfld_fieldid=$adb->getUniqueID($table_prefix."_field");
	}
	else
	{
		$custfld_fieldid= $_REQUEST['fieldid'];
	}
	$custfld_sequece=$adb->getUniqueId($table_prefix."_customfield");
    	
	$blockid ='';
        //get the blockid for this custom block
        $blockid = getBlockId($tabid,'LBL_CUSTOM_INFORMATION');

        if(is_numeric($blockid))
        {
		if($_REQUEST['fieldid'] == '')
		{
			$query = "insert into ".$table_prefix."_field values(".$tabid.",".$custfld_fieldid.",'".$columnName."','".$tableName."',2,".$uitype.",'".$columnName."','".$fldlabel."',0,0,0,100,".$custfld_sequece.",$blockid,1,'".$uichekdata."',1,0,'BAS')";
			$adb->query($query);
			$adb->alterTable($tableName, $columnName." ".$type, "Add_Column");
		}
		else
		{
			$query = "update ".$table_prefix."_field set fieldlabel='".$fldlabel."',typeofdata='".$uichekdata."' where fieldid=".$_REQUEST['fieldid'];
			$adb->query($query);
		}
		//Inserting values into vtiger_profile2field vtiger_tables
		if($_REQUEST['fieldid'] == '')
		{
			$sql1 = "select * from ".$table_prefix."_profile";
			$sql1_result = $adb->query($sql1);
			$sql1_num = $adb->num_rows($sql1_result);
			for($i=0; $i<$sql1_num; $i++)
			{
				$profileid = $adb->query_result($sql1_result,$i,"profileid");
				$sql2 = "insert into ".$table_prefix."_profile2field values(".$profileid.", ".$tabid.", ".$custfld_fieldid.", 0, 1)";
				$adb->query($sql2);	 	
			}

			//Inserting values into def_org vtiger_tables
			$sql_def = "insert into ".$table_prefix."_def_org_field values(".$tabid.", ".$custfld_fieldid.", 0, 1)";
			$adb->query($sql_def);
		}

		if($fldType == 'Picklist' || $fldType == 'MultiSelectCombo')
		{
			// Creating the PickList Table and Populating Values
			if($_REQUEST['fieldid'] == ''){
				Vtiger_Utils::CreateTable(
					$table_prefix."_$columnName",
					$columnName."id I(19) NOTNULL PRIMARY ,
					$columnName C(200) NOTNULL,
					presence I(1) NOTNULL DEFAULT 1,
					picklist_valueid I(19) NOT NULL DEFAULT 0", 
					true);
			}			

			/*if($_REQUEST['fieldid'] != '' && $mode == 'edit')
			{
				$delquery = "DELETE from vtiger_".$columnName;
				$adb->query($delquery);
			}*/
			
			//Adding a  new picklist value in the picklist table
			if($mode != 'edit')
			{
				$picklistid = $adb->getUniqueID($table_prefix."_picklist");
				$sql="insert into ".$table_prefix."_picklist values($picklistid,'".$columnName."')";
				$adb->query($sql);
			}
			$roleid=$current_user->roleid;
			$qry="select picklistid from ".$table_prefix."_picklist where  name='$columnName'";
			$picklistid = $adb->query_result($adb->query($qry),0,'picklistid');
			if($_REQUEST['fieldid'] != '' && $mode == 'edit')
			{
				$sql = "delete from ".$table_prefix."_role2picklist  where picklistid=$picklistid";
				$adb->query($sql);
			}
			$pickArray = Array();
			$fldPickList =  $_REQUEST['fldPickList'];
			$pickArray = explode("\n",$fldPickList);
			$count = count($pickArray);
			for($i = 0; $i < $count; $i++)
			{
				$id = $adb->getUniqueID($table_prefix.'_'.$columnName);
				if($pickArray[$i] != '')
				{
					$picklistcount=0;
					$pickArray[$i] = trim($pickArray[$i]);
					$sql ="select $columnName from ".$table_prefix."_$columnName";
					$numrow = $adb->num_rows($adb->query($sql));
					for($x=0;$x < $numrow ; $x++)
					{
						$picklistvalues = $adb->query_result($adb->query($sql),$x,$columnName);
						if($pickArray[$i] == $picklistvalues)
						{
							$picklistcount++;
						}
					}
					if($picklistcount == 0)
					{
						$picklist_valueid = getUniquePicklistID();
						$query = "insert into ".$table_prefix."_".$columnName." values('','".addslashes($pickArray[$i])."',1,'".$picklist_valueid."')";				
						$adb->query($query);
						$sql="update ".$table_prefix."_picklistvalues_seq set id = ".++$picklist_valueid;
						$adb->query($sql);
					}
					$sql = "select picklist_valueid from ".$table_prefix."_$columnName where $columnName='".addslashes($pickArray[$i])."'";
					$pick_valueid = $adb->query_result($adb->query($sql),0,'picklist_valueid');
					$sql = "insert into ".$table_prefix."_role2picklist select roleid,$pick_valueid,$picklistid,$i from ".$table_prefix."_role";
					$adb->query($sql);
				}
			}
		}
		//Inserting into LeadMapping table - Jaguar
		if($fldmodule == 'Leads' && $_REQUEST['fieldid'] == '')
		{
			$id = $adb->getUniqueID($table_prefix."_convertleadmapping");
			$sql_def = "insert into ".$table_prefix."_convertleadmapping (cfmid,leadfid) values(?,?)";
			$params = array($id,$custfld_fieldid);
			$adb->pquery($sql_def,$params);
		}
	}
	header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fldmodule."&parenttab=".$parenttab);
}
?>