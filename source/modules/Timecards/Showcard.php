<?php
global $table_prefix;
$startdate = isset($_REQUEST["startdate"]) ? $_REQUEST["startdate"] : (isset($_SESSION["ttstartdate"]) ? $_SESSION["ttstartdate"] : "");
$enddate = isset($_REQUEST["enddate"]) ? $_REQUEST["enddate"] : (isset($_SESSION["ttenddate"]) ? $_SESSION["ttenddate"] : "");
$userid = isset($_REQUEST["userid"]) ? $_REQUEST["userid"] : (isset($_SESSION["ttuserid"]) ? $_SESSION["ttuserid"] : $current_user->id);
$accountid = isset($_REQUEST["accountid"]) ? $_REQUEST["accountid"] : (isset($_SESSION["ttaccountid"]) ? $_SESSION["ttaccountid"] : "");
$starttime = 0;
$endtime = 0;

if ($startdate == "" || !preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $startdate, $matches)) {
	$starttime = strtotime("last monday");
	$startdate = date("d-m-Y", $starttime);
} else {
	$starttime = mktime(0, 0, 0, $matches[2], $matches[1], $matches[3]);
}
if ($enddate == "" || !preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $enddate, $matches)) {
	$endtime = strtotime("next sunday");
	$enddate = date("d-m-Y", $endtime);
} else {
	$endtime = mktime(0, 0, 0, $matches[2], $matches[1], $matches[3]);
}
if ($userid == "" || !preg_match("/^[0-9]+$/", $userid)) {
	$userid = $current_user->id;
}
if (!preg_match("/^[0-9]+$/", $accountid)) {
	$accountid = "";
}

if (isset($_REQUEST["emailTimecard"])) {
	require_once("modules/Emails/mail.php");
	switch ($_REQUEST["emailTimecard"]) {
		case "Today":
			ob_start();
			$estarttime = mktime(0, 0, 0, date("m"), date("d"), date("y"));
			$eendtime = $estarttime + 86399;
			if (date("H") < 6) {
				$estarttime -= 86400;
				$eendtime -= 86400;
			}
?>
<html>
<head>
<style>
.dvtCellLabel, .cellLabel {
	background-color:#F5F5FF;
	border-bottom:1px solid #DADAEE;
	border-top:1px solid #FFFFFF;
	color:#545454;
	padding-left:10px;
	padding-right:10px;
	white-space:nowrap;
}
body, td {
	color:#000000;
	font-family:Arial,Helvetica,sans-serif;
	font-size:11px;
}
</style>
</head>
<body>
<table style="margin-left: auto; margin-right: auto; width: 600px;" cellpadding="0" cellspacing="0">
<?php timecardTable($estarttime, $eendtime, $current_user->id, ""); ?>
</table>
</body>
</html>
<?php
			$contents = ob_get_clean();
			$subject = "[TIMECARD] ".$current_user->user_name." for ".date("d-m-Y");
			//send_mail("HelpDesk", "brett.hooker@roarz.com,ed.colgan@roarz.com,accounts@roarz.com", "ROARZ CRM", "crm@roarz.com", $subject, $contents);
			break;
	}
}

$_SESSION["ttstartdate"] = $startdate;
$_SESSION["ttenddate"] = $enddate;
$_SESSION["ttuserid"] = $userid;

$query = "SELECT * FROM ".$table_prefix."_users ORDER BY last_name, first_name";
$result = $adb->query($query);
$userhtml = "<select name=\"userid\" id=\"userid\ style=\"width: 100px;\">\n";
$userhtml .= "<option value=\"0\">-Select User</option>\n";
while ($result && ($row = $adb->fetch_array($result))) {
	$selected = "";
	if ($row["id"] == $userid) {
		$selected = " selected=\"selected\"";
	}
	$userhtml .= "<option value=\"".$row["id"]."\"".$selected.">".$row["last_name"].", ".$row["first_name"]."</option>\n";
}
$userhtml .= "</select>\n";

$query = "SELECT * FROM ".$table_prefix."_account a INNER JOIN ".$table_prefix."_crmentity c ON a.accountid = c.crmid WHERE c.deleted = 0 ORDER BY accountname ";
$result = $adb->query($query);
$accounthtml = "<select name=\"accountid\" id=\"userid\ style=\"width: 100px;\">\n";
$accounthtml .= "<option value=\"\">-Select Account-</option>\n";
while ($result && ($row = $adb->fetch_array($result))) {
	$selected = "";
	if ($row["accountid"] == $accountid) {
		$selected = " selected=\"selected\"";
	}
	$accounthtml .= "<option value=\"".$row["accountid"]."\"".$selected.">".$row["accountname"]."</option>\n";
}
$accounthtml .= "</select>\n";
?>

<br /><br />
<form action="index.php" method="get">
<input type="hidden" name="module" value="Timecards" />
<input type="hidden" name="action" value="Showcard" />
<table style="margin-left: auto; margin-right: auto; width: 600px;" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" colspan="2" class="moduleName"><strong>Time Card</strong></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding: 5px;">
			Start: <input name="startdate" tabindex="10" id="startdate" type="text" style="border:1px solid #bababa;" size="11" maxlength="10" value="<?php echo $startdate; ?>">
			<img src="themes/bluelagoon/images/calendar.gif" id="startdate_button">
			&nbsp;&nbsp;&nbsp;&nbsp;
			End:  <input name="enddate" tabindex="10" id="enddate" type="text" style="border:1px solid #bababa;" size="11" maxlength="10" value="<?php echo $enddate; ?>">
			<img src="themes/bluelagoon/images/calendar.gif" id="enddate_button">
			&nbsp;&nbsp;&nbsp;&nbsp;	
			<script type="text/javascript">
				Calendar.setup ({
					inputField : "startdate", ifFormat : "%d-%m-%Y", showsTime : false, button : "startdate_button", singleClick : true, step : 1
				});
				Calendar.setup ({
					inputField : "enddate", ifFormat : "%d-%m-%Y", showsTime : false, button : "enddate_button", singleClick : true, step : 1
				});
			</script>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php echo $userhtml; ?>
			<br /><br />
			<?php echo $accounthtml; ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" name="submit" value="Submit" />
		</td>
	</tr>
	<tr>
		<td colspan="2">
			T = Ticket, I = Invoice
			<br /><br />
		</td>
	</tr>
	<?php timecardTable($starttime, $endtime, $userid, $accountid); ?>
	<tr>
		<td>Email Timecard:</td>
		<td><input type="submit" name="emailTimecard" value="Today" /></td>
	</tr>
</table>
</form>

<?php

function timecardTable($starttime, $endtime, $userid, $accountid) {
	global $adb;

while ($starttime <= $endtime) {
	$query = "SELECT tc.*, a.*, c.*, tt.status, tt.category, u.user_name, i.invoiceid, i.invoicestatus ";
	$query .= "FROM ".$table_prefix."_timecards tc ";
	$query .= "INNER JOIN ".$table_prefix."_troubletickets tt on tc.ticket_id = tt.ticketid ";
	$query .= "INNER JOIN ".$table_prefix."_crmentity ce ON tc.timecardsid = ce.crmid ";
	$query .= "LEFT JOIN ".$table_prefix."_users u ON ce.smownerid = u.id ";
	$query .= "LEFT JOIN ".$table_prefix."_account a ON a.accountid = tt.parent_id ";
	$query .= "LEFT JOIN ".$table_prefix."_contactdetails c ON c.contactid = tt.parent_id ";
	$query .= "LEFT JOIN ".$table_prefix."_invoicetimecardsrel itr ON itr.timecardsid = tc.timecardsid ";
	$query .= "LEFT JOIN ".$table_prefix."_invoice i ON i.invoiceid = tc.invoiceid ";
	$query .= "WHERE ce.deleted = 0 ";
	$query .= "AND tc.timecarddate = '".date("Y-m-d", $starttime)."' ";
	if ($userid > 0) {
		$query .= "AND ce.smownerid = '".$userid."' ";
	}
	if ($accountid) {
		$query .= "AND a.accountid = '".$accountid."' ";
	}
	$query .= "ORDER BY accountname";
	$result = $adb->query($query);
	$html = "\n";
	$html .= "\t\t\t<table cellspacing=\"0\" cellpadding=\"5\" style=\"width 100%;\" width=\"100%\">\n";
	$current_id = -1;
	$total = 0;
	while ($result && ($row = $adb->fetch_array($result))) {
		$total += $row["duration"];
		if ($current_id != $row["parent_id"]) {
			$current_id = $row["parent_id"];
			$name = $row["accountname"];
			if ($name == "") {
				$name = $row["lastname"].", ".$row["firstname"];
			}
			$html .= "\t\t\t\t<tr>\n";
			$html .= "\t\t\t\t\t<td colspan=\"3\" class=\"dvtCellLabel\">".$name."</td>\n";
			$html .= "\t\t\t\t</tr>\n";
		}
		$html .= "\t\t\t\t<tr>\n";
		$html .= "\t\t\t\t\t<td style=\"width: 90px; border: 0px;\" nowrap=\"nowrap\" class=\"dvtCellInfo\">";
		$html .= "<strong>";
		switch ($row["timecardtype"]) {
			case "Billed":
			case "Not Billed":
				$html .= $row["timecardtype"];
				break;
			default: 
				$html .= "Waiting Approval";
				break;
		}
		$html .= "</strong><br />";
		if ($userid == 0) {
			$html .= "<div style=\"width: 10px; float: left;\">U:</div><div style=\"width: 35px; float: left;\">".$row["user_name"]."</div><div style=\"clear: both;\">";
		}
		$html .= "<div style=\"width: 12px; float: left;\">T:</div><div style=\"width: 35px; float: left;\"><a href=\"index.php?module=HelpDesk&action=DetailView&record=".$row["ticket_id"]."\">".$row["ticket_id"]."</a></div> <small>(".$row["status"].")</small>";
		$html .= "<div style=\"clear: both;\"></div>";
		$html .= "<div style=\"width: 12px; float: left;\">C:</div><div style=\"width: 35px; float: left;\">".$row["category"]."</div>";
		if ($row["invoiceid"]) {
			$html .= "<div style=\"clear: both;\"></div><div style=\"width: 12px; float: left;\">I:</div><div style=\"width: 35px; float: left;\"><a href=\"index.php?module=Invoice&action=DetailView&record=".$row["invoiceid"]."\">".$row["invoiceid"]."</a></div> <small>(".$row["invoicestatus"].")</small>";
		}
		$html .= "</td>\n";
		$html .= "\t\t\t\t\t<td style=\"width: 40px;\" style=\"border: 0px;\">".number_format($row["duration"], 2)."</td>\n";
		$html .= "\t\t\t\t\t<td class=\"dvtCellInfo\" style=\"border: 0px;\">".str_replace("\n", "<br />\n", htmlspecialchars_decode($row["shortdesc"]))."<br /> ";
		$html .= "(<a href=\"index.php?action=EditView&module=Timecards&record=".$row["timecardsid"]."&parenttab=Support\">edit</a> | ";
		$html .= "<a href=\"index.php?action=Delete&module=Timecards&record=".$row["timecardsid"]."&parenttab=Support\">del</a>";
		$html .= ($row["timecardtype"] != "Billed" ? " | <a href=\"index.php?module=Timecardsaction=ChangeType&record=".$row["timecardsid"]."&type=Billed\">bill</a>" : "");
		$html .= ($row["timecardtype"] != "Not Billed" ? " | <a href=\"index.php?module=Timecards&action=ChangeType&record=".$row["timecardsid"]."&type=Not+Billed\">don't bill</a>" : "").")</td>\n";
		$html .= "\t\t\t\t</tr>\n";
	}
	$html .= "\t\t\t\t<tr>\n";
	$html .= "\t\t\t\t\t<td class=\"dvtCellInfo\" style=\"width: 90px; border: 0px;\"><strong>Total:</strong></td>\n";
	$html .= "\t\t\t\t\t<td class=\"dvtCellInfo\" style=\"border: 0px;\" colspan=\"2\"><strong>".number_format($total, 2)."</strong></td>\n";
	$html .= "\t\t\t\t</tr>\n";
	$html .= "\t\t\t</table>\n\t\t";
?>
	<tr>
		<td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; padding: 5px; width: 80px;"><?php echo date("l", $starttime); ?><br /><?php echo date("d-m-Y", $starttime); ?></td>
		<td style="border-top: 1px solid #000000; border-right: 1px solid #000000;"><?php echo $html; ?></td>
	</tr>
<?php
	$starttime += 86400;
}

?>
	<tr>
		<td style="border-top: 1px solid #000000;" colspan="2">&nbsp;</td>
	</tr>
<?php
} ?>