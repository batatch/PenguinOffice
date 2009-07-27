<?php
print "<!-- part_mail.php -->";
if ($login) {
	// タイトル表示
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./mail/\">伝言メモ</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./mail/add/?p=top\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"新規メモ\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD COLSPAN=2>";
	
	// 伝言(受信)
	$sql = "SELECT *,mail_res.res_seqno FROM mail LEFT JOIN (SELECT seqno as res_seqno FROM mail_res WHERE user_id='$login_id') AS mail_res ON mail.seqno = mail_res.res_seqno WHERE user_id='$login_id' AND \"to\" ~* '(^|,)$login_id(,|$)' AND res_seqno IS NULL ORDER BY createstamp DESC,seqno DESC";
	# $sql = "SELECT * FROM mail WHERE user_id='$login_id' AND \"to\" ~* '(^|,)$login_id(,|$)' ORDER BY createstamp DESC,seqno DESC";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	if ($cnt==0) {
		print "<FONT COLOR=#CCCCCC>未読なし</FONT>\n";
	} else {
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		for($i=0;$i<$cnt;$i++){
			$row = pg_fetch_array($res,$i);
			
			print "<TR>";
			$name_from = get_first("users","name_ryaku","id='".$row["from"]."'","");
			
			// メモ内容
			print "<TD VALIGN=TOP ALIGN=LEFT>";
			if ($row["address_seqno"]>0) {
				$sql_addr = "SELECT * FROM address WHERE seqno=".$row["address_seqno"];
				$res_addr = pg_query($conn,$sql_addr);
				$cnt_addr = pg_num_rows($res_addr);
				if ($cnt_addr>0) {
					$row_addr = pg_fetch_array($res_addr,0);
					print "<A HREF=\"./address/edit/?s=".$row["address_seqno"]."\">";
					$corp_name = $row_addr["corp_name"];
					$corp_name = ereg_replace("株式会社","",$corp_name);
					$corp_name = ereg_replace("有限会社","",$corp_name);
					$corp_name = ereg_replace("合資会社","",$corp_name);
					$corp_name = ereg_replace("合名会社","",$corp_name);
					$corp_name = ereg_replace("財団法人","",$corp_name);
					$corp_name = ereg_replace("社団法人","",$corp_name);
					$corp_name = ereg_replace("学校法人","",$corp_name);
					$corp_name = ereg_replace("宗教法人","",$corp_name);
					$corp_name = ereg_replace("特定非営利活動法人","",$corp_name);
					$corp_name = ereg_replace("NPO法人","",$corp_name);
					$corp_name = ereg_replace("社会福祉法人","",$corp_name);
					$corp_name = ereg_replace("公益法人","",$corp_name);
					$corp_name = ereg_replace("医療法人","",$corp_name);
					$corp_name = ereg_replace("事業協同組合","",$corp_name);
					$corp_name = ereg_replace("協同組合連合会","",$corp_name);
					$corp_name = ereg_replace("企業組合","",$corp_name);
					$corp_name = ereg_replace("協業組合","",$corp_name);
					$corp_name = ereg_replace("振興組合","",$corp_name);
					$corp_name = trim($corp_name);
					$corp_name = eregi_replace("　","",$corp_name);
					print $corp_name." ";
					print $row_addr["person_name"];
					print "</A> 様より<BR>";
				}
			}
			
			if ($row["priority"]==1) {
				print "<FONT COLOR=#FF0000>";
			} elseif ($row["priority"]==2) {
				print "<FONT COLOR=#FF6600>";
			} else {
				print "<FONT COLOR=#FFEE00>";
			}
			print "●</FONT>&nbsp;";
			print "<A HREF=\"./mail/add/?edit=e&n=".$row["seqno"]."\">";
			print mb_strcut($row["body"],0,50,"EUC-JP");
			print "</A>";
			print "(".$name_from.")";
			
			print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row["createstamp"]))."<BR>";
			
			print "</TD></TR>\n";
			if ($i<$cnt-1) print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
			
		}
		
		//
		print "</TABLE>";
	}
	print "</TD></TR></TABLE>";
}
?>