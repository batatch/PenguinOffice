<?php
print "<!-- part_workflow.php -->";
if ($login && get_count("flows","user_ids ~* '(^|,)".$login_id."(,|$)'")>0) {
	// タイトル表示
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./workflow/\">ワークフロー</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./workflow/regist/?p=top\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"新規案件\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD COLSPAN=2>";
	
	// ワークフロー
	$sql = "SELECT * FROM workflow WHERE result_sign IS NULL AND flow_ids ~* '(^|,)".$login_id."(,|$)'";
	$res = pg_query($conn, $sql);
	$cnt = pg_num_rows($res);
	
	if ($cnt==0) {
		print "<FONT COLOR=#CCCCCC>未決裁なし</FONT>\n";
	} else {
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		$v=0;
		for($i=0;$i<$cnt;$i++){
			$row = pg_fetch_array($res,$i);
			
			$flow_ids = $row["flow_ids"];
			$flow_arr = split(",",$flow_ids);
			$flow_cnt = sizeof($flow_arr);
			if($flow_cnt>0) {
				for($c=0;$c<$flow_cnt;$c++) {
					if ($flow_arr[$c]==$login_id) {
						$flow_no = $c+1;
						break;
					}
				}
			}
			
			$view = false;
			$recognize_sign = get_first("workflow_ret","recognize_sign","refno=".$row["seqno"]." AND seqno=".$flow_no,"");
			if ($recognize_sign=="") {
				if ($flow_no==1) {
					$view = true;
				} else {
					$before_res_cnt = get_count("workflow_ret","refno=".$row["seqno"]." AND seqno=".($flow_no-1));
					if ($before_res_cnt>0) {
						$view = true;
					}
				}
			}
			
			if ($view) {
				$v++;
				
				print "<TR>";
				
				// メモ内容
				print "<TD VALIGN=TOP>";
				print "・<A HREF=\"./workflow/result/?p=top&no=".$row["seqno"]."\">";
				
				$subject = mb_strcut($row["subject"],0,60,"EUC-JP");
				if (trim($subject)=="") {
					$subject = "(no subject)";
				}
				print $subject;
				print "</A>";
				
				print "(".get_first("users","name_ryaku","id='".$row["user_id"]."'","不明または削除").")";
				print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row["createstamp"]))."<BR>";
				print "&nbsp; (".mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row["body1"]),0,60,"EUC-JP").")";
				print "</TD></TR>\n";
				
				if ($i<$cnt-1) print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
			}
		}
		print "</TABLE>";
		if ($v==0) {
			print "<FONT COLOR=#CCCCCC>未決裁なし</FONT>\n";
		}
		//
	}
	print "</TD></TR></TABLE>";
}
?>