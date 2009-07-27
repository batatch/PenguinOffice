<?php
print "<!-- part_room.php -->";
if ($login) {
	// »ÜÀßÍ½Ìó
	$sql = "SELECT rooms.id as id,min(type) as type,min(name) as name,min(manage_id) as manage_id FROM rooms LEFT JOIN (SELECT room_id,min(user_id) as user_id FROM rooms_reserve WHERE date>='".date("Y-m-d",time())."' AND date<='".date("Y-m-d",time()+86400*7)."' AND user_id='$login_id' GROUP BY room_id) as reserve ON rooms.id=reserve.room_id WHERE (manage_id='$login_id' OR reserve.user_id='$login_id') GROUP BY rooms.id ORDER BY type";
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	if ($cnt>0) {
		$bgsign = false;
		// ¥¿¥¤¥È¥ëÉ½¼¨
		print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
		
		print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
		print "<TD NOWRAP><A HREF=\"./room/\">»ÜÀß¡¦ÀßÈ÷Í½Ìó</A></TD>";
		print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./room/add/?p=top\">ÅÐÏ¿<IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"¿·µ¬Í½Ìó\"></A></TD>";
		print "</TR>";
		
		print "<TR><TD ALIGN=CENTER COLSPAN=2>";
		
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		for($i=0;$i<$cnt;$i++){
			$row = pg_fetch_array($res,$i);
			$room_id   = $row["id"];
			$room_name = $row["name"];
			$manage_id = $row["manage_id"];
			
			print "<TR BGCOLOR=$bg_light>";
			print "<TD colspan=3>";
			print "<A HREF=\"./room/?new_target=r$room_id\"><FONT COLOR=$bodyForeColor STYLE=\"font-weight:normal\">$room_name</A>";
			print "&nbsp;";
			print "<A HREF=\"./room/add/?p=top&id=$room_id\"><IMG SRC=\"./image/entrysadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALT=\"Í½Ìó¤ÎÄÉ²Ã\"></A>";
			print "<BR>";
			print "";
			print "</TD></TR>";
			
			print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
			
			$sql_reserve  = "SELECT * FROM rooms_reserve ";
			$sql_reserve .= "WHERE ";
			$sql_reserve .= "room_id='$room_id'";
			if ($manage_id==$login_id) {
				#          $sql_reserve .= "";
			} else {
				$sql_reserve .= " AND user_id='$login_id'";
			}
			$sql_reserve .= " AND date>='".date("Y-m-d",time())."' AND date<='".date("Y-m-d",time()+86400*7)."' ";
			$sql_reserve .= "ORDER BY date,coalesce(timefrom,'1970-04-14 00:00:00'),coalesce(timeto,'1970-04-14 00:00:00')";
			$res_reserve = pg_query($conn,$sql_reserve);
			$cnt_reserve = pg_num_rows($res_reserve);
			
			if ($cnt_reserve==0) {
				print "<TR><TD colspan=3><FONT COLOR=#CCCCCC>";
				print "¡¦¤Ê¤·<BR>\n";
				print "</TD></TR>";
				
			} else {
				for($j=0;$j<$cnt_reserve;$j++){
					print "<TR><TD colspan=3>";
					
					$row_reserve = pg_fetch_array($res_reserve,$j);
					// room_id¤Çrooms¤ÎÌ¾Á°
					$sql_room = "select * from rooms where id='".$row_reserve["room_id"]."'";
					$res_room = pg_query($conn,$sql_room);
					if (pg_num_rows($res_room)>0){
						$row_name = pg_fetch_array($res_room,0);
						$room_name = $row_name["name"];
					} else {
						$room_name = "";
					}
					// user_id¤Çrooms¤ÎÌ¾Á°
					$sql_user = "select * from users where id='".$row_reserve["user_id"]."'";
					$res_user = pg_query($conn,$sql_user);
					if (pg_num_rows($res_user)>0){
						$row_user = pg_fetch_array($res_user,0);
						$user_name = $row_user["name_ryaku"];
					} else {
						$user_name = "";
					}
					$t_f = datetime2timestamp($row_reserve["timefrom"]);
					$t_t = datetime2timestamp($row_reserve["timeto"]);
					
					print "¡¦";
					print "<A HREF=\"./room/add/?p=top&s=".$row_reserve["seqno"]."\">";
					$notes = split("\r\n",$row_reserve["note"]);
					if (sizeof($notes)>0) print $notes[0];
					if (sizeof($notes)>1) print "...";
					print "</A>";
					print "(".$user_name.")";
					
					print " <FONT COLOR=#666666>";
					print "";
					if (date("Y",datetime2timestamp($row_reserve["date"]))<>date("Y",datetime2timestamp($row_reserve["date"]))) {
						print date("Y/n/j",datetime2timestamp($row_reserve["date"]));
					} else {
						print date("n/j",datetime2timestamp($row_reserve["date"]));
					}
					print " ";
					print date("H:i",$t_f)."-".date("H:i",$t_t);
					print "";
					
					print "</A><BR>\n";
					print "</TD></TR>";
				}
			}
		}
		print "</TABLE>";
		print "</TD></TR></TABLE>";
	}
}
?>