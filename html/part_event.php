<?php
print "<!-- part_event.php -->";
if ($login) {
	if (is_numeric($evy) && is_numeric($evm) && checkdate($evm,1,$evy)) {
		$firststamp = mktime(0, 0, 0, $evm, 1, $evy);
	} else {
		$evm = date("m",time()+86400*6);
		$evy = date("Y",time()+86400*6);
		$firststamp = mktime(0, 0, 0, $evm, 1, $evy);
	}
	
	$next_y = $evy; $next_m = $evm + 1;
	if ($next_m>12) {
		$next_y = $evy + 1; $next_m = 1;
	}
	$prev_y = $evy; $prev_m = $evm - 1;
	if ($prev_m<1) {
		$prev_y = $evy- 1; $prev_m = 12;
	}
	
	$laststamp = mktime(0, 0, 0, $next_m, 1, $next_y)-86400;
	
	// 年月表示
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./schedule/\">イベント予定</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./event/add/?p=top\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"新規イベント\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD ALIGN=CENTER COLSPAN=2>";
	
	print "<TABLE BORDER=$borderwidth CELLSPACING=1 CELLPADDING=2 WIDTH=100% BGCOLOR=#999999>";
	print "<TR><TD COLSPAN=7 ALIGN=CENTER BGCOLOR=".$wcolor_titleback[1].">";
	print $evy."年".$evm."月\n";
	print "</TD></TR>\n";
	
	// 曜日表示
	print "<TR BGCOLOR=\"".$menu_backcolor."\">\n";
	for ($i=0;$i<7;$i++) {
		print "<TH ALIGN=CENTER BGCOLOR=\"".$wcolor_titleback[$i]."\"><FONT COLOR=\"".$wcolor_title[$i]."\">".$wname[$i]."</FONT>";
		print "</TH>";
	}
	print "</TR>\n";
	
	// 表示日の調整
	if (date("w",$firststamp)==0) {
		$calfirststamp = $firststamp-86400*7;
	} else {
		$calfirststamp = $firststamp-86400*(date("w",$firststamp));
	}
	if (date("w",$laststamp)==6) {
		$callaststamp = $laststamp+86400*6;
	} else {
		$callaststamp = $laststamp+86400*(6-date("w",$laststamp));
	}
	
	$todaystamp = mktime(0,0,0,date("m"),date("d"),date("Y"));
	
	$sql  = "SELECT seqno,datefrom,dateto,timefrom,timeto FROM events WHERE ";
	$sql .= "(user_id='".$login_id."' or sharelist ~* '(^|,|\t)".$login_id."(,|\t|$)') AND (";
	$sql .= "(datefrom>='".date("Y-m-d",$calfirststamp)."' AND datefrom<='".date("Y-m-d",$callaststamp)."') OR ";
	$sql .= "(dateto>='".date("Y-m-d",$calfirststamp)."' AND dateto<='".date("Y-m-d",$callaststamp)."') OR ";
	$sql .= "(datefrom<='".date("Y-m-d",$calfirststamp)."' AND dateto>='".date("Y-m-d",$callaststamp)."')";
	$sql .= ")";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	
	$column="";
	
	for ($i=0;$i<$cnt;$i++) {
		$row = pg_fetch_array($res,$i);
		
		// 時刻値計算
		if ($row["timefrom"]!="") {
			$timevalue = date("Hi",datetime2timestamp($row["timefrom"]));
		} elseif ($row["timeto"]!="") {
			$timevalue = date("Hi",datetime2timestamp($row["timefrom"])-3600);
		} else {
			$timevalue = 0;
		}
		
		if ($row["datefrom"]==$row["dateto"]) {
			// 同一日の予定
			$daynumber = date("Ymd",date2timestamp($row["datefrom"]));
			$column[$daynumber][$row["seqno"]] = $timevalue;
		} else {
			// 日をまたぐ予定
			for($storestamp=date2timestamp($row["datefrom"]);$storestamp<=date2timestamp($row["dateto"]);$storestamp=$storestamp+86400) {
				$daynumber = date("Ymd",date2timestamp($storestamp));
				if ($storestamp==date2timestamp($row["datefrom"])) {
					$column[$daynumber][$row["seqno"]] = $timevalue;
				} else {
					$column[$daynumber][$row["seqno"]] = 0;
				}
			}
		}
	}
	// カレンダー表示
	for($viewstamp=$calfirststamp;$viewstamp<=$callaststamp;$viewstamp=$viewstamp+86400) {
		if (date("w",$viewstamp)==0) print "<TR>";
		// フォント色調整
		if ($viewstamp==$todaystamp) {
			$forecolor = "#FFFFFF";
			$backcolor = $wcolor[date("w",$viewstamp)];
		} elseif ($viewstamp<$firststamp || $viewstamp>$laststamp) {
			// 当月以外
			$forecolor = "#999999";
			$backcolor = $wcolor_back[date("w",$viewstamp)];
			if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) {
				$backcolor = $wcolor_back[7];
			}
		} else {
			$forecolor = $wcolor[date("w",$viewstamp)];
			$backcolor = $wcolor_back[date("w",$viewstamp)];
			if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) {
				$forecolor = $wcolor[7];
				$backcolor = $wcolor_back[7];
			}
		}
		print "<TD ALIGN=CENTER BGCOLOR=\"".$backcolor."\" NOWRAP>";
		
		$columns = $column[date("Ymd",$viewstamp)];
		$eventcount = sizeof($columns);
		if ($eventcount>0 && is_array($columns)) {
			print "<A HREF=\"./schedule/daily.phtml?y=".date("Y",$viewstamp)."&m=".date("n",$viewstamp)."&d=".date("j",$viewstamp)."\" style=\"color:".$forecolor."\">";
		} else {
			print "<FONT COLOR=".$forecolor.">";
		}
		print date("j",$viewstamp);
		if ($eventcount>0) {
			print "</A>";
		} else {
			print "</FONT>";
		}
		print "</TD>";
		
		if (date("w",$viewstamp)==6) print "</TR>";
	}
	
	print "</TABLE>\n";
	
	if ($cnt>0 && sizeof($column)>0) {
		ksort($column);
		$r = 0;
		while(list($key,$val)=each($column)) {
			while (list($seqno,$fromtime)=each($val)) {
				$sql = "SELECT * FROM events WHERE seqno=".$seqno;
				$res = pg_query($conn,$sql);
				$cnt = pg_num_rows($res);
				if ($cnt>0) {
					$row = pg_fetch_array($res,0);
					
					print "<TR><TD>";
					print date("n/j",date2timestamp($row["datefrom"]));
					if ($row["timefrom"]!="") {
						print "&nbsp;";
						print date("H:i",datetime2timestamp($row["timefrom"]));
					}
					if ($row["datefrom"]!=$row["dateto"] || $row["timefrom"]!="") {
						print "〜";
					}
					if ($row["datefrom"]!=$row["dateto"]) {
						print date("n/j",date2timestamp($row["dateto"]));
					}
					if ($row["timeto"]!="") {
						if ($row["dateto"]!=$row["datefrom"]) {
							print "&nbsp;";
						}
						print date("H:i",datetime2timestamp($row["timeto"]));
					}
					print "<BR>";
					
					print "・"."<A HREF=\"./event/add/?p=top&s=".$seqno."\">";
					print mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row["title"]),0,100,"EUC-JP");
					print "</A><BR>";
					
					print "</TD></TR>";
					$r++;
				}
			}
		}
	}
	
	print "</TABLE>\n";
}
?>