<?php
print "<!-- part_todo.php -->";
if ($login) {
	// ¥¿¥¤¥È¥ëÉ½¼¨
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./todo/\">È÷ËºÏ¿</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./todo/add/?p=top\"><IMG SRC=\"./image/todosadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"¿·µ¬¥á¥â\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD COLSPAN=2>";
	
	// È÷ËºÏ¿
	$sql = "select * from todos where user_id='".$login_id."' order by seqno desc";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	if ($cnt==0) {
		print "<FONT COLOR=#CCCCCC>¤Ê¤·</FONT>\n";
	} else {
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		for($i=0;$i<$cnt;$i++){
			$row = pg_fetch_array($res,$i);
			
			print "<TR>";
			
			// ¥á¥âÆâÍÆ
			print "<TD VALIGN=TOP ALIGN=LEFT>";
			if ($row["priority"]==1) {
				print "<FONT COLOR=#FF0000>";
			} elseif ($row["priority"]==2) {
				print "<FONT COLOR=#FF6600>";
			} else {
				print "<FONT COLOR=#FFEE00>";
			}
			print "¡ü</FONT>&nbsp;";
			print "<A HREF=\"./todo/?p=top&n=".$row["seqno"]."\">";
			$subject = mb_strcut($row["subject"],0,60,"EUC-JP");
			if (trim($subject)=="") {
				$subject = "(no subject)";
			}
			print $subject;
			print "</A>";
			print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row["updatestamp"]))."<BR>";
			print "&nbsp; (".mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row["body"]),0,60,"EUC-JP").")";
			
			print "</TD></TR>\n";
			if ($i<$cnt-1) print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
		}
		//
		print "</TABLE>";
	}
	print "</TD></TR></TABLE>";
}
?>