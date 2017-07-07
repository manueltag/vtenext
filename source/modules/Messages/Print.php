<?php
/* crmv@42801 */
$record = $_REQUEST['record'];
?>
<frameset rows="99, *"> <!-- crmv@107654 -->
<frame name="top_frame" src="index.php?module=Messages&action=MessagesAjax&file=PrintHeader&record=<?php echo $record; ?>" scrolling="no" noresize="noresize" frameborder="0" />
<frame name="bottom_frame" src="index.php?module=Messages&action=MessagesAjax&file=DetailView&mode=Print&record=<?php echo $record; ?>" frameborder="0" />
</frameset>