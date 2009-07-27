<?php
print "<!-- part_place.php -->";
if ($login) {
	// タイトル表示
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
	
	print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
	print "<TD NOWRAP><A HREF=\"./member/\">行き先ガイド</A></TD>";
	print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./member/placeedit/?p=top\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"行き先変更\"></A></TD>";
	print "</TR>";
	
	print "<TR><TD COLSPAN=2>";
	
	// 勤務情報の更新
	if (isset($_GET["duty_start"])) {
		if (get_count("users_duty","user_id='".$login_id."' AND workdate='".date("Y-m-d")."'")==0) {
			$sql = "INSERT INTO users_duty (workdate,user_id,startstamp,createstamp,updatestamp) VALUES ('".date("Y-m-d")."','".$login_id."','now','now','now')";
			$res = pg_query($conn,$sql);
		} else {
			if (get_first("users_duty","startstamp","workdate='".date("Y-m-d")."' AND user_id='".$login_id."'",0)==0) {
				$sql = "UPDATE users_duty SET startstamp='now',updatestamp='now' WHERE workdate='".date("Y-m-d")."' AND user_id='".$login_id."'";
				$res = pg_query($conn,$sql);
			}
		}
	}
	if (isset($_GET["duty_end"])) {
		if (get_count("users_duty","user_id='".$login_id."' AND workdate='".date("Y-m-d")."'")==0) {
			$sql = "INSERT INTO users_duty (workdate,user_id,endstamp,createstamp,updatestamp) VALUES ('".date("Y-m-d")."','".$login_id."','now','now','now')";
			$res = pg_query($conn,$sql);
		} else {
			if (get_first("users_duty","endstamp","workdate='".date("Y-m-d")."' AND user_id='".$login_id."'",0)==0) {
				$sql = "UPDATE users_duty SET endstamp='now',updatestamp='now' WHERE workdate='".date("Y-m-d")."' AND user_id='".$login_id."'";
				$res = pg_query($conn,$sql);
			}
		}
	}
	if (isset($_GET["duty_goes"])) {
		if (get_count("users_duty","user_id='".$login_id."' AND workdate='".date("Y-m-d")."'")==0) {
			$sql = "INSERT INTO users_duty (workdate,user_id,goesstamp,createstamp,updatestamp) VALUES ('".date("Y-m-d")."','".$login_id."','now','now','now')";
			$res = pg_query($conn,$sql);
		} else {
			if (get_first("users_duty","goesstamp","workdate='".date("Y-m-d")."' AND user_id='".$login_id."'",0)==0) {
				$sql = "UPDATE users_duty SET goesstamp='now',updatestamp='now' WHERE workdate='".date("Y-m-d")."' AND user_id='".$login_id."'";
				$res = pg_query($conn,$sql);
			}
		}
	}
	if (isset($_GET["duty_back"])) {
		if (get_count("users_duty","user_id='".$login_id."' AND workdate='".date("Y-m-d")."'")==0) {
			$sql = "INSERT INTO users_duty (workdate,user_id,backstamp,createstamp,updatestamp) VALUES ('".date("Y-m-d")."','".$login_id."','now','now','now')";
			$res = pg_query($conn,$sql);
		} else {
			if (get_first("users_duty","backstamp","workdate='".date("Y-m-d")."' AND user_id='".$login_id."'",0)==0) {
				$sql = "UPDATE users_duty SET backstamp='now',updatestamp='now' WHERE workdate='".date("Y-m-d")."' AND user_id='".$login_id."'";
				$res = pg_query($conn,$sql);
			}
		}
	}
	
	// 在席情報の更新
	// ※ ロギングしてるのでうまく使えば工程管理の実績収集なんかにつかえるかも
	if (!empty($_GET["location"]) || ($_GET["location"]=="1" || $_GET["location"]=="2")) {
		$sql  = "DELETE FROM users_location WHERE updatestamp+'".(24*60).":00'<now()";
		pg_query($conn,$sql); // 60日間以前のログを削除
		
		$lastseqno = get_last("users_location","seqno","user_id='".$login_id."'",0);
		$seqno = $lastseqno + 1;
		
		$code = $_GET["location"];
		$text = get_first("locations","name","id=".$code,"");
		if ($lastseqno>0) {
			$lastcode = get_first("users_location","code","user_id='".$login_id."' AND seqno=".$lastseqno,0);
		} else {
			$lastcode = "";
		}
		
		if ($lastcode != $code) {
			$sql  = "INSERT INTO users_location (seqno,user_id,code,text,updatestamp) VALUES (";
			$sql .=     $seqno.",";
			$sql .= "'".$login_id."',";
			$sql .= "'".$code."',";
			$sql .= "'".db_textsafe($text)."',";
			$sql .= "'now'";
			$sql .= ")";
			$res = pg_query($conn,$sql);
		}
	}
	
	// ユーザー情報の取得
	$sql_user = "SELECT id,name_ryaku,mygroup,mygrouptopsign FROM users WHERE id = '".$login_id."'";
	$res_user = pg_query($conn,$sql_user);
	$cnt_user = pg_num_rows($res_user);
	if ($cnt_user>0) {
		$row_user = pg_fetch_array($res_user,0);
		
		// 自身の情報獲得
		$users[$login_id] = $row_user["name_ryaku"];
		
		$mygroup = $row_user["mygroup"];
		
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
	
	// ユーザー分まわす
	print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1 WIDTH=100%>";
	reset($users);
	$c = 0;
	while(list($user_id,$user_name)=each($users)) {
		print "<TR>";
		print "<TD WIDTH=25% NOWRAP>";
		$user_name = get_first("users","name_ryaku","id='".$user_id."'","不明または削除");
		print "<A HREF=\"./member/?id=$user_id\">";
		print $user_name;
		print "</A>";
		print "</TD>";
		
		$sql = "SELECT * FROM users_location WHERE user_id='".$user_id."' ORDER BY updatestamp DESC";
		$res = pg_query($conn,$sql);
		$cnt = pg_num_rows($res);
		if ($cnt==0) {
			$code = "";
			$text = "未設定";
			$updatestamp = false;
		} else {
			$row = pg_fetch_array($res,0);
			$code = $row["code"];
			$text = $row["text"];
			$updatestamp = datetime2timestamp($row["updatestamp"]);
		}
		
		print "<TD WIDTH=65%>";
		if ($user_id==$login_id) {
			print "<A HREF=\"./member/placeedit/?p=top\">";
			print $text;
			print "</A>";
		} else {
			print "<A HREF=\"./member/?id=".$user_id."\">";
			print $text;
			print "</A>";
		}
		if ($updatestamp!=false) {
			print "<FONT STYLE=\"font-size:80%\"> ";
			if (date("Ymd")!=date("Ymd",$updatestamp)) {
				if (date("Y")!=date("Y",$updatestamp)) {
					print date("Y",$updatestamp)."/";
				}
				print date("n/j",$updatestamp);
				print " ";
			}
			print date("H:i",$updatestamp);
			print "</FONT>";
		}
		print "</TD>";
		
		if ($user_id==$login_id) {
			print "<FORM>";
			print "<TD WIDTH=10% NOWRAP>";
			if ($code != "1") print "<INPUT TYPE=BUTTON VALUE=\"在席\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?location=1'\">";
			if ($code == "1") print "<INPUT TYPE=BUTTON VALUE=\"不在\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?location=2'\">";
			print "</TD>";
			print "</FORM>";
		} else {
			print "<TD WIDTH=10% ALIGN=RIGHT>";
			print "<A HREF=\"./mail/add/?p=top&to=".$user_id."\">";
			print "<IMG SRC=\"./image/mail.gif\" WIDTH=13 HEIGHT=11 BORDER=0 ALT=\"伝言\">";
			print "</A>";
			print "</TD>";
		}
		
		print "</TR>";
		
		if ($user_id==$login_id) {
			print "<TR>";
			print "<TD ALIGN=RIGHT NOWRAP>";
			print "<A HREF=\"./member/dutyedit/?p=top\" STYLE=\"font-size:80%\">";
			print "タイムカード";
			print "</A>";
			print "</TD>";
			
			$sql_duty = "SELECT * FROM users_duty WHERE user_id='".$login_id."' AND workdate='".date("Y-m-d")."'";
			$res_duty = pg_query($conn,$sql_duty);
			$cnt_duty = pg_num_rows($res_duty);
			
			$startstamp = false;
			$endstamp   = false;
			$goesstamp  = false;
			$backstamp  = false;
			if ($cnt_duty>0) {
				$row_duty = pg_fetch_array($res_duty,0);
				if ($row_duty["startstamp"]!="") $startstamp = datetime2timestamp($row_duty["startstamp"]);
				if ($row_duty["endstamp"]!="")   $endstamp   = datetime2timestamp($row_duty["endstamp"]);
				if ($row_duty["goesstamp"]!="")  $goesstamp  = datetime2timestamp($row_duty["goesstamp"]);
				if ($row_duty["backstamp"]!="")  $backstamp  = datetime2timestamp($row_duty["backstamp"]);
			}
			print "<FORM>";
			print "<TD COLSPAN=2 ALIGN=RIGHT>";
			
			print "<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=1 BGCOLOR=#CCCCCC>";
			
			print "<TR BGCOLOR=#FFFFFF>";
			print "<TD ALIGN=CENTER>";
			if ($startstamp == false) {
				print "<INPUT TYPE=BUTTON VALUE=\"出社\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?duty_start'\">";
			} else {
				print "<FONT STYLE=\"font-size:80%\">";
				print "出社<BR>";
				print date("H:i",$startstamp);
			}
			print "</TD>";
			print "<TD ALIGN=CENTER>";
			if ($endstamp == false) {
				print "<INPUT TYPE=BUTTON VALUE=\"退社\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?duty_end'\">";
			} else {
				print "<FONT STYLE=\"font-size:80%\">";
				print "退社<BR>";
				print date("H:i",$endstamp);
			}
			print "</TD>";
			print "<TD ALIGN=CENTER>";
			if ($goesstamp == false) {
				print "<INPUT TYPE=BUTTON VALUE=\"外出\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?duty_goes'\">";
			} else {
				print "<FONT STYLE=\"font-size:80%\">";
				print "外出<BR>";
				print date("H:i",$goesstamp);
			}
			print "</TD>";
			print "<TD ALIGN=CENTER>";
			if ($backstamp == false) {
				print "<INPUT TYPE=BUTTON VALUE=\"戻り\" STYLE=\"font-size:80%\" ONCLICK=\"location.href='./?duty_back'\">";
			} else {
				print "<FONT STYLE=\"font-size:80%\">";
				print "戻り<BR>";
				print date("H:i",$backstamp);
			}
			print "</TD>";
			print "</TR>";
			print "</TABLE>";
			
			print "</TD>";
			print "</FORM>";
			
			print "</TR>";
		}
		
		if ($c+1<sizeof($users)) {
			print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1 COLSPAN=3><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
		}
		$c++;
	}
	print "</TABLE>";
	print "</TD></TR></TABLE>";
}
?>