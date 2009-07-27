<?php
print "<!-- part_circular.php -->";
if ($login) {
	// タイトル表示
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./circular/\">回覧板</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./circular/regist/?p=top\"><IMG SRC=\"./image/todosadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"新規回覧\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD COLSPAN=2>";
	
	// 回覧板
	$sql  = "SELECT circulas.*, (circulas_ret.result_sign) AS res_sign FROM circulas LEFT JOIN (SELECT * FROM circulas_ret WHERE user_id ='$login_id') AS circulas_ret ON circulas.seqno = circulas_ret.refno WHERE ";
	$sql .= " user_id_to ~* '(^|,)$login_id(,|$)' ";
	$sql .= "AND ";
	$sql .= "(circulas_ret.result_sign='f' OR circulas_ret.result_sign is null)";
	$sql .= "ORDER BY circulas.createstamp DESC";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	if ($cnt==0) {
		print "<FONT COLOR=#CCCCCC>未回答なし</FONT>";
	} else {
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		for($i=0;$i<$cnt;$i++){
			$row = pg_fetch_array($res,$i);
			
			print "<TR>";
			
			// メモ内容
			print "<TD VALIGN=TOP>";
			print "・<A HREF=\"./circular/result/?p=top&no=".$row["seqno"]."\">";
			
			$subject = mb_strcut($row["subject"],0,60,"EUC-JP");
			if (trim($subject)=="") {
				$subject = "(no subject)";
			}
			print $subject;
			print "</A>";
			
			print "(".get_first("users","name_ryaku","id='".$row["user_id"]."'","不明または削除").")";
			print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row["createstamp"]))."<BR>";
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