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

require_once('include/Zend/Json.php');
	
if($_REQUEST['faqid'] != ''){
	$faqid = Zend_Json::decode($_REQUEST['faqid']);
   
}	

$faq_array = $_SESSION['faq_array'];
$customerid = $_SESSION['customer_id'];
$sessionid = $_SESSION['customer_sessionid'];

for($i=0;$i<count($faq_array);$i++)
{

	if($faqid == $faq_array[$i]['id'])
	{
		$faq_id = $faq_array[$i]['id'];
		$faq_module_no = $faq_array[$i]['faqno'];
		$faq_createdtime = $faq_array[$i]['faqcreatedtime'];
		$faq_modifiedtime = $faq_array[$i]['faqmodifiedtime'];
		$faq_productid = $faq_array[$i]['product_id'];
		$faq_category = $faq_array[$i]['category'];		
		$comments_array = $_SESSION['faq_array'][$i]['comments'];
		$createdtime_array = $_SESSION['faq_array'][$i]['createdtime'];

		$comments_count = count($comments_array);
		
		$smarty->assign('QUESTION',$faq_array[$i]['question']);
		$smarty->assign('ANSWER',$faq_array[$i]['answer']);
		
		$comments_array_smarty = array();
		
		for($j=0;$j<$comments_count;$j++)
		{
			$comments_array_smarty[$j]['comment'] = $comments_array[$j];
			$comments_array_smarty[$j]['date'] = $createdtime_array[$j];
		}
		
		$smarty->assign('BADGE',$comments_count);
		$smarty->assign('COMMENTS',$comments_array_smarty);
		
		$module = 'Documents';
		$params = array('id' => "$faqid",'module'=>"$module", 'contactid'=>"$customerid",'sessionid'=>"$sessionid");
		$result = $client->call('get_documents', $params, $Server_Path, $Server_Path);
//		$list .=  getblock_fieldlistview($result,$module);	   	
			
		$smarty->assign('DOCUMENTS',$result);
	 	$smarty->assign('FAQID',$faqid); 
	 	$smarty->assign('PAGEOPTION',getPageOption());
	   
	}
}

//This is added to get the FAQ details as a Popup on Mouse over
$list .= getArticleIdTime($faq_module_no,$faq_productid,$faq_category,$faq_createdtime,$faq_modifiedtime);

// echo $list;
$smarty->assign('LIST',$list);
$smarty->assign('MODULE',$_REQUEST['module']);
$smarty->display('FaqDetail.tpl');
?>
