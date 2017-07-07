<?php
/*
    Copyright 2005 Rolando Gonzalez (rolosworld@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**** config  *****/

/**
 * MySQL server configuration
 */
include_once('config.php');
require_once('include/utils/utils.php');

/**
 * Constants for the chat
 */
$chat_conf = array();
$chat_conf['alive_time'] = "30"; // time users should report to be online, in seconds.
$chat_conf['msg_limit'] = "10"; // maximum msg's to send in one request.

/*************************************************************/
/*** YOU SHOULD NOT NEED TO EDIT ANYTHING ELSE BELOW THIS. ***/
/*************************************************************/

session_name("AjaxPopupChat");
//session_save_path("sessions");
session_start();

/**** handler *****/
/**
 * Chat object
 */
class Chat
{
  // stores the string to be returned
  var $json;
  
  function Chat()
  {
	global $adb,$table_prefix;
    $this->json = '';
    
    // las message id received by user
    if(!isset($_SESSION["mlid"]))
      {
		//crmv@19692
		$result = $adb->query('select max(id) as id from '.$table_prefix.'_chat_users');
		if ($result && $adb->num_rows($result)>0) {
			$_SESSION["mlid"] = $adb->query_result($result,0,'id'); 
		} else {
			$_SESSION["mlid"] = 0;
		}
		//crmv@19692e
      }
    
    // when the las user list was sended.
    if(!isset($_SESSION["lul"]))
      {
	$_SESSION["lul"] = 0;
      }

    // check if user is active.
    if(!isset($_SESSION['chat_user']))
      {
	$res = $adb->pquery("delete from ".$table_prefix."_chat_users where session=?", array(session_id()));	//crmv@19692
	$this->setUserNick();
      }
    else
      {
	$res = $adb->pquery("update ".$table_prefix."_chat_users set ping=? where session=?", array(date('Y-m-d H:i:s'),session_id()));	//crmv@19866
	if($adb->getAffectedRowCount($res) == 0)
	  {
	    $this->setUserNick();
	  }
      }
    
    switch($_POST['submode'])
      {
	// request all the json data at once.
      case 'get_all':
	global $chat_conf;
	$this->lastMsgId();
	
	$this->json = '[%s]';
	$this->getAllPVChat();
	$pvchat = $this->json;

	$this->json = '[%s]';
	$this->getPubChat();
	$pchat = $this->json;

	$this->json = '';
	if(time() - $_SESSION["lul"] > $chat_conf['alive_time'])
	  {
	    $_SESSION["lul"] = time();
	    $this->json = '[%s]';
	    $this->getUserList();
	  }
	$ulist = $this->json;
	
	$tmp = array();
	$this->json = '{%s}';
	if(strlen($ulist) > 0)
	  $tmp[] = '"ulist":'.$ulist;
	
	if(strlen($pvchat) > 0)
	  $tmp[] = '"pvchat":'.$pvchat;
	
	if(strlen($pchat) > 0)
	  $tmp[] = '"pchat":'.$pchat;
	
	$this->json = sprintf($this->json, implode(',',$tmp));
	break;

	// user is submiting a msg
      case 'submit':
	$this->submit($_POST['msg'],intval($_POST['to']));
	break;

	// user closed a private chat
      case 'pvclose':
	$this->pvClose(intval($_POST['to']));
	break;

      default:
	break;
      }
  }
  
  /**
   * returns the JSON created
   */
  function getAJAX()
  {
    return $this->json;
  }
  
  /**
   * Sets the user initial nickname.
   */
  function setUserNick()
  {
	global $current_user, $adb,$table_prefix;
    $res = $adb->pquery("select id from ".$table_prefix."_chat_users where session=?", array(session_id()));
    if($adb->num_rows($res) > 0)
      {
	$line = $adb->fetch_array($res);
	$_SESSION['chat_user'] = $line['id'];
	return;
      }
    
	$id = $adb->getUniqueID($table_prefix."_chat_users");
    $sql = "insert into ".$table_prefix."_chat_users(id,nick,session,ping,ip) values (?,?,?,?,?)";
    $params = array($id,$current_user->user_name, session_id(),date('Y-m-d H:i:s'),$_SERVER['REMOTE_ADDR']);
	$res = $adb->pquery($sql, $params);
	$_SESSION['chat_user'] = $id;	//crmv@19692
  }
  
  /**
   * generate the available users list
   */
  function getUserList()
  {
    global $chat_conf, $adb,$table_prefix;
    $tmp = '';
	//crmv@19866
    $sql = "delete from ".$table_prefix."_chat_users where ping < ".$adb->database->OffsetDate(-($chat_conf['alive_time']/86400));
	$res = $adb->query($sql);
	//crmv@19866e
    $res = $adb->pquery("select id,nick from ".$table_prefix."_chat_users", array());
    if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    while($line = $adb->fetch_array($res))
    {
		if($line['id'] != $_SESSION['chat_user'])
	  		$tmp .= '{"uid":'.$line['id'].',"nick":"'.$line['nick'].'"},';
    }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }

  /**
   * Sets user last post received.
   */
  function lastMsgId()
  {
    if(isset($_POST['mlid']) && intval($_POST['mlid']) > $_SESSION["mlid"])
      $_SESSION["mlid"] = intval($_POST['mlid']);
  }

  /**
   * generates the private chat data
   */
  function getAllPVChat()
  {
    global $chat_conf, $adb,$table_prefix;
    $format = '{"mlid":%s,"chat":%s,"from":"%s","msg":"%s"},';
    //crmv@19866
	$sql ="select ms.id mid,ms.chat_from mfrom,ms.chat_to mto,pv.id id,us.nick chat_from,ms.msg msg from ".$table_prefix."_chat_users us,".$table_prefix."_chat_pvchat pv,".$table_prefix."_chat_msg ms where pv.msg=ms.id and us.id=ms.chat_from and ms.id>? and ((ms.chat_from=? and ms.chat_to>0) or (ms.chat_to=? and ms.chat_from>0)) order by ms.born";
    $params = array($_SESSION['mlid'], $_SESSION['chat_user'], $_SESSION['chat_user']);
	$res = $adb->limitpQuery($sql,0,$chat_conf['msg_limit'],$params);
	//crmv@19866e
	if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    $tmp = '';
    while($line = $adb->fetch_array($res))
      {
	if($line['mfrom'] == $_SESSION['chat_user'])
	  $cid = $line['mto'];
	else
	  $cid = $line['mfrom'];

	$tmp .= sprintf($format,$line['mid'],$cid,$line['chat_from'],addslashes($line['msg']));
      }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }


  /**
   * generates the public chat data
   * NOTE: this is alpha
   */
  function getPubChat()
  {
    global $chat_conf, $adb;
    $format = '{"mlid":%s,"from":"%s","msg":"%s"},';
    $sql = "select ms.id mid,ms.chat_from mfrom,ms.chat_to mto,p.id id,us.nick chat_from,ms.msg msg from ".$table_prefix."_chat_users us,".$table_prefix."_chat_pchat p,".$table_prefix."_chat_msg ms where p.msg=ms.id and us.id=ms.chat_from and ms.id>? and ms.chat_to=0 order by ms.born";
    $params = array($_SESSION['mlid']);
	$res = $adb->limitpQuery($sql,0,$chat_conf['msg_limit'],$params);
	
	if($adb->num_rows($res)==0)
      {
	$this->json = '';
	return;
      }

    $tmp = '';
    while($line = $adb->fetch_array($res))
      {
	$tmp .= sprintf($format,$line['mid'],$line['chat_from'],addslashes($line['msg']));
      }
    $tmp = trim($tmp,',');
    $this->json = sprintf($this->json,$tmp);
  }

  /**
   * Check for special commands on message.
   */
  function msgParse($msg)
  {
	global $adb,$table_prefix;
    if(strlen($msg) == 0) return '';
    $msg = stripslashes($msg);

    if($msg[0] == '\\')
      {
	$today_date = getdate();
		  
	$words = explode(" ",$msg);
	switch($words[0])
	  {
	  case '\nick':
	    if(isset($words[1]) && strlen($words[1]) > 3)
	      {
			$res = $adb->pquery("select nick from ".$table_prefix."_chat_users where id=?", array($_SESSION['chat_user']));
			$line = $adb->fetch_array($res);
			$res = $adb->pquery("update ".$table_prefix."_chat_users set nick=? where id=?", array($words[1], $_SESSION['chat_user']));
			$msg = '\sys <span class="sysb">'.$line['nick'].'</span> changed nick to <span class="sysb">'.$words[1].'</span>';
	      }
	    break;
	    
	  case '\help':
		$msg = '\sys <br><span class="sysb">\\\\nick "nickname" </span> - change nick<br><span class="sysb">\\\\date </span> - date<br><span class="sysb">\\\\time </span> - time<br><span class="sysb">\\\\month </span> - month<br><span class="sysb">\\\\day </span> - weekday';
	   break;
	  case '\date':
       		$msg = '\sys Today is <span class="sysb">'.date('d-m-Y').'</span>';		  
	   break;	
	   case '\time':
       		$msg = '\sys The Current time is <span class="sysb">'.$today_date["hours"].':'.$today_date["minutes"].':'.$today_date["hours"].'</span>';		 break;	
	case '\month':
       		$msg = '\sys <span class="sysb">'.$today_date["month"].'</span>';		 
	break;
	case '\day':
       		$msg = '\sys <span class="sysb">'.$today_date["weekday"].'</span>';		 
	break;		
	 default:
		  
	    $msg = '\sys Bad command: '.$words[0];
	    break;
	  }
      }
    return $msg;    
  }

  /**
   * process a submited msg
   */
  function submit($msg, $to=0)
  {
	global $adb,$table_prefix;
    //UTF-8 support added - ding
    $msg = utf8RawUrlDecode($msg);
    $msg = $this->msgParse($msg);
    $msg = htmlentities($msg);
    if(strlen($msg) == 0) return;
	
	//$sql = "insert into vtiger_chat_msg set chat_from=?, chat_to=?, born=now(), msg=?";
	$id = $adb->getUniqueID($table_prefix."_chat_msg");
	//crmv@19866
    $sql = "insert into ".$table_prefix."_chat_msg(id,chat_from, chat_to, born, msg) values (?,?,?,?,?)";
    $params = array($id, $_SESSION['chat_user'], $to, date('Y-m-d H:i:s'), $msg);
    //crmv@19866e
	$res = $adb->pquery($sql, $params);
	
    $chat = "p";
    if($to != 0)
      $chat .= "v";
    $id_ = $adb->getUniqueID($table_prefix."_chat_".$chat."chat");
    $res = $adb->pquery("insert into ".$table_prefix."_chat_".$chat."chat (id,msg) values (?,?)", array($id_,$id));
  }

  /**
   * removes the private conversation msg's because someone closed it
   */
  function pvClose($to)
  {
	global $adb;
    $sql = "delete from ".$table_prefix."_chat_msg where (chat_from=? and chat_to=?) or (chat_from=? and chat_to=?)";
	$params = array($to, $_SESSION['chat_user'], $_SESSION['chat_user'], $to);
	$res = $adb->pquery($sql, $params);  
  }
}

/**** caller ****/
$chat = new Chat();
echo $chat->getAJAX();
?>
