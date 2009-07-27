<?php
print "<!-- part_schedule.php -->";

print "
<script language=\"JavaScript\">
<!--
function selectmonth(f) {
  var v = f.options[f.selectedIndex].value;
  var sp = \"-\"
  var spv = v.split(sp);
  if (v!=\"\" && v!=\"null\") {
    location.href='./?y='+spv[0]+'&m='+spv[1]+'&d='+spv[2]+'';
  }
}
//-->
</script>
";

if ($login) {
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	// ユーザー情報の取得
	$sql_user = "SELECT id,name_ryaku,mygroup,mygrouptopsign FROM users WHERE id = '".$login_id."'";
	$res_user = pg_query($conn,$sql_user);
	$cnt_user = pg_num_rows($res_user);
	if ($cnt_user>0) {
		$row_user = pg_fetch_array($res_user,0);
		
		// 自身の情報獲得
		$users[$login_id] = $row_user["name_ryaku"];
		
		$mygroup = $row_user["mygroup"];
		$topsign = $row_user["mygrouptopsign"];
		
		// 秘書(受け)の情報獲得
		$sql_secret = "SELECT id,name_ryaku FROM users WHERE secretary_id='$login_id' ORDER BY seqno";
		$res_secret = pg_query($conn, $sql_secret);
		$cnt_secret = pg_num_rows($res_secret);
		if ($cnt_secret>0) {
			for ($i=0;$i<$cnt_secret;$i++) {
				$row_secret = pg_fetch_array($res_secret,$i);
				$users[$row_secret["id"]] = $row_secret["name_ryaku"];
				$master_ids[$row_secret["id"]] = true;
			}
		}
		// マイグループの情報獲得
		if ($topsign=="t") {
			$mygroups = split(",",$mygroup);
			if (sizeof($mygroups)>0) {
				while(list($key,$val)=each($mygroups)) {
					$find = false;
					reset($users);
					while(list($user_id,$user_name)=each($users)) {
						if ($user_id==$val) {
							$find = true; break;
						}
					}
					if (!$find) {
						$name_ryaku = get_first("users","name_ryaku","id='".$val."'","");
						if ($name_ryaku != "") {
							$users[$val] = $name_ryaku;
						}
					}
				}
			}
		}
	}
	
	$todaystamp = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
	if (is_numeric($y) && is_numeric($m) && is_numeric($d) && checkdate($m,$d,$y)) {
		$firststamp = mktime(0, 0, 0, $m, $d, $y);
	} else {
		$firststamp = date2timestamp(timestamp2datetime(time()));
	}
	$laststamp  = $firststamp+86400*7-1;
	
	$prevweekstamp = $firststamp-86400*7;
	$nextweekstamp = $laststamp+1;
	$prevdaystamp = $firststamp-86400;
	$nextdaystamp = $firststamp+86400;
	
	$ym = date("ym",timestamp2datetime($firststamp));
	
	$prevmonthstamp = $firststamp-86400*30;
	$nextmonthstamp = $firststamp+86400*30;
	
	print "<TR HEIGHT=21 BGCOLOR=$indexmenu_backcolor>";
	print "<TD WIDTH=15% NOWRAP>";
	print "<A HREF=\"./schedule/";
	if (is_numeric($y) && is_numeric($m) && is_numeric($d) && checkdate($m,$d,$y)) {
		print "?y=$y&m=$m&d=$d";
	}
	print "\">予定表</A>";
	print "</TD>";
	
	print "<TD WIDTH=70% NOWRAP ALIGN=CENTER>";
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1><TR>";
	print "<TD>";
	
	print "<A HREF=\"./?y=".date("Y",$prevweekstamp)."&m=".date("n",$prevweekstamp)."&d=".date("j",$prevweekstamp)."\"><IMG SRC=\"../image/right2.gif\" BORDER=0 ALT=\"前週\"></A>";
	print "<A HREF=\"./?y=".date("Y",$prevdaystamp)."&m=".date("n",$prevdaystamp)."&d=".date("j",$prevdaystamp)."\"><IMG SRC=\"../image/right1.gif\" BORDER=0 ALT=\"前日\"></A>";
	print "</TD>";
	
	print "<TD ALIGN=CENTER NOWRAP>\n";
	// 期間表示
	print "<SELECT NAME=\"week\" onChange=\"selectmonth(this)\">\n";
	### 今週から6週前
	for ($ii=6;$ii>=1;$ii--){
		$prevstamp = $firststamp - 86400 * 7 * $ii;
		print "<OPTION value=\"".date("Y",$prevstamp)."-".date("n",$prevstamp)."-".date("j",$prevstamp)."\">　".date("m",$prevstamp)."月".date("d",$prevstamp)."日〜".date("m",$prevstamp+86400*6)."月".date("d",$prevstamp+86400*6)."日</OPTION>\n";
	}
	### 今週
	print "<OPTION value=\"".date("Y",$firststamp)."-".date("n",$firststamp)."-".date("j",$firststamp)."\" selected>【".date("m",$firststamp)."月".date("d",$firststamp)."日〜".date("m",$laststamp)."月".date("d",$laststamp)."日】</OPTION>\n";
	### 今週から6週後
	for ($ii=1;$ii<=6;$ii++){
		$nextstamp = $firststamp + 86400 * 7 * $ii;
		print "<OPTION value=\"".date("Y",$nextstamp)."-".date("n",$nextstamp)."-".date("j",$nextstamp)."\">　".date("m",$nextstamp)."月".date("d",$nextstamp)."日〜".date("m",$nextstamp+86400*6)."月".date("d",$nextstamp+86400*6)."日</OPTION>\n";
	}
	print "</SELECT>";
	print "</TD>";
	print "<TD ALIGN=LEFT NOWRAP>";
	print "<A HREF=\"./?y=".date("Y",$nextdaystamp)."&m=".date("n",$nextdaystamp)."&d=".date("j",$nextdaystamp)."\"><IMG SRC=\"../image/left1.gif\" BORDER=0 ALT=\"翌日\"></A>";
	print "<A HREF=\"./?y=".date("Y",$nextweekstamp)."&m=".date("n",$nextweekstamp)."&d=".date("j",$nextweekstamp)."\"><IMG SRC=\"../image/left2.gif\" BORDER=0 ALT=\"翌週\"></A>";
	
	print "</TD></TR></TABLE>";
	print "</TD>";
	print "<TD WIDTH=15% NOWRAP ALIGN=RIGHT>";
	print "<A HREF=\"./schedule/add/?p=top&y=".date("Y",$viewstamp)."&m=".date("n",$viewstamp)."&d=".date("j",$viewstamp)."\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"新規予定\"></A>";
	print "</TD>";
	print "</TR>";
	print "<TR><TD COLSPAN=3>";
	
	print "<TABLE BORDER=$borderwidth CELLSPACING=1 CELLPADDING=2 WIDTH=100% BGCOLOR=#999999>";
	
	print "<TR BGCOLOR=#CCCCCC><TD>&nbsp;</TD>";
	for($viewstamp=$firststamp;$viewstamp<=$laststamp;$viewstamp=$viewstamp+86400) {
		$bgcolor = $wcolor_titleback[date("w",$viewstamp)];
		if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) $bgcolor = $wcolor_titleback[7];
		print "<TD WIDTH=12% BGCOLOR=".$bgcolor." ALIGN=CENTER>";
		$nowym = date("ym",$viewstamp);
		$fontcolor = $wcolor[date("w",$viewstamp)];
		if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) $fontcolor = $wcolor[7];
		print "<A HREF=\"./schedule/daily.phtml?p=top&y=".date("Y",$viewstamp)."&m=".date("n",$viewstamp)."&d=".date("j",$viewstamp)."\" STYLE=\"color:".$fontcolor."\">";
		if ($ym!=$nowym) {
			print date("n/j",$viewstamp)."(".$wname[date("w",$viewstamp)].")";
		} else {
			print date("j",$viewstamp)."(".$wname[date("w",$viewstamp)].")";
		}
		print "</A>";
		$ym = $nowym;
		print "</TD>";
	}
	print "</TR>";
	
	// イベント出力
	$sql = "SELECT * FROM events WHERE (user_id='".$login_id."' OR sharelist ~* '(^|,|\t)".$login_id."(,|\t|$)') and (";
	$sql .= "(datefrom>='".date("Y-m-d",$firststamp)."' AND datefrom<='".date("Y-m-d",$laststamp)."') OR ";
	$sql .= "(dateto>='".date("Y-m-d",$firststamp)."' AND dateto<='".date("Y-m-d",$laststamp)."') OR ";
	$sql .= "(datefrom<='".date("Y-m-d",$firststamp)."' AND dateto>='".date("Y-m-d",$laststamp)."')";
	$sql .= ")";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	if ($cnt>0) {
		// イベントがあれば出力
		$eventcolumn="";
		for ($i=0;$i<$cnt;$i++) {
			$row = pg_fetch_array($res,$i);
			// 時刻値計算
			if ($row["timefrom"]!="") {
				$timevalue = date("Hi",datetime2timestamp($row["timefrom"]));
			} elseif ($row["timeto"]!="" && $row["datefrom"]==$row["dateto"]) {
				$timevalue = date("Hi",datetime2timestamp($row["timefrom"])-3600);
			} else {
				$timevalue = 0;
			}
			
			if ($row["datefrom"]==$row["dateto"]) {
				// 同一日の予定
				$daynumber = floor(date2timestamp($row["datefrom"])/86400) - floor($firststamp/86400);
				$eventcolumn[$daynumber][$row["seqno"]] = $timevalue;
			} else {
				// 日をまたぐ予定
				$c = 0;
				for ($viewstamp=$firststamp;$viewstamp<=$laststamp;$viewstamp=$viewstamp+86400) {
					$datefromstamp = date2timestamp($row["datefrom"]);
					$datetostamp   = date2timestamp($row["dateto"]);
					if ($datefromstamp<=$viewstamp && $datetostamp>=$viewstamp){
						if ($viewstamp==$datefromstamp) {
							$eventcolumn[$c][$row["seqno"]] = $timevalue;
						} else {
							$eventcolumn[$c][$row["seqno"]] = 0;
						}
					}
					$c++;
				}
			}
		}
		
		// ここからイベント描画
		print "<TR>\n";
		print "<TD WIDTH=16% BGCOLOR=\"".$indexmenu_backcolor."\" ALIGN=LEFT VALIGN=TOP>";
		print "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR><TD VALIGN=TOP NOWRAP>";
		if ($eventsign=="t") {
			print "<A HREF=\"../event/add/\">イベント</A>";
		} else {
			print "イベント";
		}
		if ($eventsign=="t") {
			print "<A HREF=\"../event/add/?ey=".$year_s."&em=".$mon_s."&ed=".$day_s."&id=".$entrys[$c][0]."\"><img src=\"../image/entrysadd2.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"イベントの追加\"></A>";
		}
		print "</TD></TR></TABLE>";
		print "</TD>";
		$c = 0;
		for ($viewstamp=$firststamp;$viewstamp<=$laststamp;$viewstamp=$viewstamp+86400) {
			if ($todaystamp==$viewstamp) {
				$bgcolor = "#AAAAFF";
			} else {
				$bgcolor = $wcolor_back[date("w",$viewstamp)];
				if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) $bgcolor = $wcolor_back[7];
			}
			print "<TD WIDTH=12% VALIGN=TOP BGCOLOR=".$bgcolor.">";
			
			$eventcolumns = $eventcolumn[$c];
			if (sizeof($eventcolumns)>0 && is_array($eventcolumns)) {
				if (sizeof($eventcolumns)>1) asort($eventcolumns);
				print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%>";
				
				$r = 0;
				$backtime = "";
				while (list($seqno,$val)=each($eventcolumns)) {
					$sql = "SELECT * FROM events WHERE seqno=".$seqno;
					$res = pg_query($conn,$sql);
					$cnt = pg_num_rows($res);
					
					print "<TR>";
					if ($r<sizeof($eventcolumns)-1) {
						print "<TD STYLE=\"border-bottom-style:dotted;border-bottom-width:1;border-bottom-color:#555555\">";
					} else {
						print "<TD>";
					}
					if ($cnt>0) {
						$row = pg_fetch_array($res,0);
						// 開始〜終了時刻の計算
						if ($row["datefrom"]==date("Y-m-d",$viewstamp) and $row["timefrom"]!="") {
							$fromtime = datetime2timestamp($row["timefrom"]);
						} elseif($row["datefrom"]!=date("Y-m-d",$viewstamp)) {
							$fromtime = datetime2timestamp("1970/04/14 00:00:00");
						} else {
							if ($row["timeto"]!="") {
								$fromtime = datetime2timestamp($row["timeto"])-7199; # 決まってなければ２時間引く
							} else {
								$fromtime = datetime2timestamp("1970/04/14 0".$hour_from.":00:00");
							}
						}
						if ($row["dateto"]==date("Y-m-d",$viewstamp) and $row["timeto"]!="") {
							$totime = datetime2timestamp($row["timeto"]);
						} elseif($row["dateto"]!=date("Y-m-d",$viewstamp)) {
							$totime = datetime2timestamp("1970/04/14 23:59:00");
						} else {
							if ($row["timefrom"]!="") {
								$totime = datetime2timestamp($row["timefrom"])+7199; # 決まってなければ２時間足す
							} else {
								$totime = datetime2timestamp("1970/04/14 0".$hour_from.":00:00");
							}
						}
						// イベント項目出力
						if ($row["datefrom"]!=$row["dateto"]) {
							print "<SPAN STYLE=\"background-color:".$menu_backcolor."\"><font color=".$menu_forecolor.">";
						}
						print "<A HREF=\"./event/add/?p=top&s=".$row["seqno"]."\" STYLE=\"font-weight:normal\">";
						if ($row["datefrom"]==date("Y-m-d",($viewstamp)) and $row["timefrom"]!="") {
							print date("H:i",datetime2timestamp($row["timefrom"]));
						}
						if (($row["datefrom"]==date("Y-m-d",($viewstamp)) and $row["timefrom"]!="") or ($row["dateto"]==date("Y-m-d",($viewstamp)) and $row["timeto"]!="")) {
							print "-";
						}
						if ($row["dateto"]==date("Y-m-d",($viewstamp)) and $row["timeto"]!="") {
							print date("H:i",datetime2timestamp($row["timeto"]));
						}
						if ($row["timefrom"]!="" or $row["timeto"]!="") {
							print "<BR>";
						}
						print mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row["title"]),0,100,"EUC-JP");
						print "</A>";
						if ($row["datefrom"]!=$row["dateto"]) {
							print "</FONT></SPAN>";
						}
					}
					print "</TD></TR>";
					$r++;
				}
				print "</TABLE>";
			}
			print "</TD>\n";
			$c++;
		}
		print "</TR>\n";
		
	}
	
	// ユーザー分まわす
	reset($users);
	while(list($user_id,$user_name)=each($users)) {
		$sql  = "SELECT seqno,datefrom,dateto,timefrom,timeto,user_id,create_user_id,viewsign,opensign,note FROM schedules WHERE user_id='".$user_id."' and (";
		$sql .= "(datefrom>='".date("Y-m-d",$firststamp)."' AND datefrom<='".date("Y-m-d",$laststamp)."') OR ";
		$sql .= "(dateto>='".date("Y-m-d",$firststamp)."' AND dateto<='".date("Y-m-d",$laststamp)."') OR ";
		$sql .= "(datefrom<='".date("Y-m-d",$firststamp)."' AND dateto>='".date("Y-m-d",$laststamp)."')";
		$sql .= ")";
		$res = pg_query($conn,$sql);
		$cnt = pg_num_rows($res);
		
		$column="";
		for ($i=0;$i<$cnt;$i++) {
			$row = pg_fetch_array($res,$i);
			if ($row["user_id"]==$login_id ||
			$row["create_user_id"]==$login_id ||
			$master_ids[$row["user_id"]] ||
			(($row["user_id"]!=$login_id && $row["create_user_id"]!=$login_id) &&
			$row["opensign"]=="t")
			) {
				// 時刻値計算
				if ($row["timefrom"]!="") {
					$timevalue = date("Hi",datetime2timestamp($row["timefrom"]));
				} elseif ($row["timeto"]!="" && $row["datefrom"]==$row["dateto"]) {
					$timevalue = date("Hi",datetime2timestamp($row["timefrom"])-3600);
				} else {
					$timevalue = 0;
				}
				
				if ($row["datefrom"]==$row["dateto"]) {
					// 同一日の予定
					$daynumber = floor(date2timestamp($row["datefrom"])/86400) - floor($firststamp/86400);
					$column[$daynumber][$row["seqno"]] = $timevalue;
				} else {
					// 日をまたぐ予定
					$c = 0;
					for ($viewstamp=$firststamp;$viewstamp<=$laststamp;$viewstamp=$viewstamp+86400) {
						$datefromstamp = date2timestamp($row["datefrom"]);
						$datetostamp   = date2timestamp($row["dateto"]);
						if ($datefromstamp<=$viewstamp && $datetostamp>=$viewstamp){
							if ($viewstamp==$datefromstamp) {
								$column[$c][$row["seqno"]] = $timevalue;
							} else {
								$column[$c][$row["seqno"]] = 0;
							}
						}
						$c++;
					}
				}
			}
		}
		
		print "<TR BGCOLOR=#CCCCCC>";
		print "<TD WIDTH=15% VALIGN=TOP BGCOLOR=".$bodyBackColor.">";
		print "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR><TD VALIGN=TOP NOWRAP>";
		print "<A HREF=\"./member/?id=$user_id\">";
		print $user_name;
		print "</A>";
		print "</TD><TD ALIGN=RIGHT>";
		if ($user_id==$login_id) {
		} else {
			print "<A HREF=\"./mail/add/?to=".$user_id."\"><IMG SRC=\"./image/mail.gif\" WIDTH=13 HEIGHT=11 BORDER=0 ALT=\"伝言\"></A>";
		}
		print "</TD></TR></TABLE>";
		print "</TD>";
		$c = 0;
		for ($viewstamp=$firststamp;$viewstamp<=$laststamp;$viewstamp=$viewstamp+86400) {
			if ($todaystamp==$viewstamp) {
				$bgcolor = "#AAAAFF";
			} else {
				$bgcolor = $wcolor_back[date("w",$viewstamp)];
				if (get_count("holiday","holiday='".date("Y-m-d",$viewstamp)."'")>0) $bgcolor = $wcolor_back[7];
			}
			print "<TD WIDTH=12% VALIGN=TOP BGCOLOR=".$bgcolor.">";
			
			$columns = $column[$c];
			if (sizeof($columns)>0 && is_array($columns)) {
				if (sizeof($columns)>1) asort($columns);
				print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%>";
				
				$r = 0;
				$backtime = "";
				while (list($seqno,$val)=each($columns)) {
					$sql = "SELECT * FROM schedules WHERE seqno=".$seqno;
					$res = pg_query($conn,$sql);
					$cnt = pg_num_rows($res);
					
					print "<TR>";
					if ($r<sizeof($columns)-1) {
						print "<TD STYLE=\"border-bottom-style:dotted;border-bottom-width:1;border-bottom-color:#555555\">";
					} else {
						print "<TD>";
					}
					
					if ($cnt>0) {
						$row = pg_fetch_array($res,0);
						//
						// 開始〜終了時刻の計算
						if ($row["datefrom"]==date("Y-m-d",$viewstamp) and $row["timefrom"]!="") {
							$fromtime = datetime2timestamp($row["timefrom"]);
						} elseif($row["datefrom"]!=date("Y-m-d",$viewstamp)) {
							$fromtime = datetime2timestamp("1970/04/14 00:00:00");
						} else {
							if ($row["timeto"]!="") {
								$fromtime = datetime2timestamp($row["timeto"])-7199; # 決まってなければ２時間引く
							} else {
								$fromtime = datetime2timestamp("1970/04/14 0".$hour_from.":00:00");
							}
						}
						if ($row["dateto"]==date("Y-m-d",$viewstamp) and $row["timeto"]!="") {
							$totime = datetime2timestamp($row["timeto"]);
						} elseif($row["dateto"]!=date("Y-m-d",$viewstamp)) {
							$totime = datetime2timestamp("1970/04/14 23:59:00");
						} else {
							if ($row["timefrom"]!="") {
								$totime = datetime2timestamp($row["timefrom"])+7199; # 決まってなければ２時間足す
							} else {
								$totime = datetime2timestamp("1970/04/14 0".$hour_from.":00:00");
							}
						}
						
						// 表示するかどうか
						$view = "off";
						if ($row["user_id"]==$login_id || $row["create_user_id"]==$login_id) {
							// 自身または共有
							$view = "on";
						} elseif ($master_ids[$row["user_id"]]) {
							// 秘書担当
							$view = "on";
						} else {
							// 自身以外
							if ($row["opensign"]=="t" and ($row["viewsign"]=="t" || $row["viewsign"]=="")) {
								// 公開予定
								$view = "on";
							} else {
								if (($righthandman_ids != "" && eregi("(^|,)".$login_id."(,|$)",$righthandman_ids))) {
									// 右腕扱い
									$view = "on";
								}
							}
						}
						
						if ($row["create_user_id"]!="" and $row["create_user_id"]!=$row["user_id"]) {
							print "<img src=\"./image/share.gif\" BORDER=0 alt=\"共有\" ALIGN=MIDDLE>";
						} elseif($row["sharelist"]!="") {
							print "<img src=\"./image/base.gif\" BORDER=0 alt=\"共有\" ALIGN=MIDDLE>";
						}
						if ($row["opensign"]!="t") {
							print "<img src=\"./image/key.gif\" BORDER=0 alt=\"非公開\" ALIGN=MIDDLE>";
						} else {
							if ($row["viewsign"]=="f" && ($row["create_user_id"]==$login_id || $row["user_id"]==$login_id || $master_ids[$row["user_id"]] || eregi("(^|,)".$login_id."(,|$)",$righthandman_ids))) {
								print "<img src=\"./image/eye.gif\" BORDER=0 alt=\"非表示\" ALIGN=MIDDLE>";
							}
						}
						if ($row["datefrom"]!=$row["dateto"]) {
							print "<SPAN STYLE=\"background-color:".$menu_backcolor."\"><font color=".$menu_forecolor.">";
						}
						
						if ($view == "on") {
							print "<A HREF=\"./schedule/add/?p=top&s=".$row["seqno"]."\" STYLE=\"font-weight:normal\">";
						} else {
							print "<FONT COLOR=".$bodyLinkColor.">";
						}
						if ($backtime!="" && $backtime>$fromtime) { // ﾌﾞｯｷﾝｸﾞ時は紫表示
						print "<FONT COLOR=#CC00CC>";
						}
						
						if ($row["datefrom"]==date("Y-m-d",($viewstamp)) and $row["timefrom"]!="") {
							print date("H:i",datetime2timestamp($row["timefrom"]));
						}
						if (($row["datefrom"]==date("Y-m-d",($viewstamp)) and $row["timefrom"]!="") or ($row["dateto"]==date("Y-m-d",($viewstamp)) and $row["timeto"]!="")) {
							print "-";
						}
						if ($row["dateto"]==date("Y-m-d",($viewstamp)) and $row["timeto"]!="") {
							print date("H:i",datetime2timestamp($row["timeto"]));
						}
						if ($row["timefrom"]!="" or $row["timeto"]!="") {
							print "<BR>";
						}
						
						if ($view == "on") {
							print mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row["note"]),0,100,"EUC-JP");
						} else {
							print "&lt;非表示&gt;";
						}
						
						if ($view=="on") {
							print "</A>";
						} else {
							print "</FONT>";
						}
						if ($row["datefrom"]!=$row["dateto"]) {
							print "</FONT></SPAN>";
						}
						
						// 前の予定終了時刻を保存
						$backtime = $totime;
					}
					print "</TD></TR>";
					$r++;
				}
				print "</TABLE>";
			}
			
			if ($user_id==$login_id || $master_ids[$user_id]) {
				print "<DIV ALIGN=RIGHT><A HREF=\"./schedule/add/?p=top&ey=".date("Y",$viewstamp)."&em=".date("n",$viewstamp)."&ed=".date("j",$viewstamp)."\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"新規予定\"></A></DIV>";
			}
			
			print "</TD>";
			$c++;
		}
		print "</TR>";
	}
	print "</TABLE>";
	print "</TD></TR></TABLE>";
}
?>