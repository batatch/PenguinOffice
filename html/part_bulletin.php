<?php
print "<!-- part_bulletin.php -->";
if ($login) {
	// BBS情報の取得(配列も生成)
	$sql_board = "SELECT * FROM boards ORDER BY seqno";
	$res_board = pg_query($conn,$sql_board);
	$cnt_board = pg_num_rows($res_board);
	if ($cnt_board==0) {
		#      print "<TR><TD>BBSなし</TD></TR>";
	} else {
		// タイトル表示
		print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
		
		print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
		print "<TD NOWRAP><A HREF=\"./bulletin/\">ＢＢＳ</A></TD>";
		print "<TD NOWRAP ALIGN=RIGHT><A HREF=\"./bulletin/post/?p=top\"><IMG SRC=\"./image/todosadd.gif\" WIDTH=10 HEIGHT=9 BORDER=0 ALIGN=ABSMIDDLE ALT=\"新規発言\"></A></TD>";
		print "</TR>";
		
		print "<TR><TD ALIGN=CENTER COLSPAN=2>";
		
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		
		$boarddraw = false;
		for($i=0;$i<$cnt_board;$i++) {
			$use = false;
			$row_board  = pg_fetch_array($res_board,$i);
			$manage_id  = $row_board["admin_id"];
			$boardsid   = $row_board["id"];
			$boardsname = $row_board["name"];
			$group_ids  = $row_board["view_group_ids"];
			$user_ids   = $row_board["view_user_ids"];
			if ($manage_id==$login_id || ($group_ids=="" && $user_ids=="")) {
				## 管理者 or 掲示板管理者 or 制約のない掲示板
				$use = true;
			} else {
				if ($group_ids!="") {
					$view_group_id = split(",",$group_ids);
					if (sizeof($view_group_id)>0) while (list($seq,$id)=each($view_group_id)) {
						$chkcnt = get_count("users_entry","user_id='$login_id' AND group_id='$id'");
						if ($chkcnt>0) { $use = true; break; }
					}
				}
				if ($user_ids!="") {
					$view_user_id = split(",",$user_ids);
					if (sizeof($view_user_id)>0) while (list($seq,$id)=each($view_user_id)) {
						if ($login_id==$id) { $use = true; break; }
					}
				}
			}
			if ($use) {
				$boards[$boardsid] = array($boardsname,$manage_id);
				if (empty($board)) {
					$board = $row_board["id"];
				}
				
				if ($board==$row_board["id"]) {
					$board_admin_id = $row_board["admin_id"];
				}
				// 投稿件数の取得 もとい
				#          $sql_post = "SELECT count(*) FROM boards_post WHERE board_id='$boardsid'";
				
				// BBSの未読数を獲得
				$sql_post  = "SELECT count(boards_post.seqno) as count ";
				$sql_post .= "FROM boards_post LEFT JOIN (SELECT * FROM boards_res WHERE boards_res.user_id='$login_id') AS boards_res ON boards_post.board_id = boards_res.board_id AND boards_post.seqno = boards_res.seqno ";
				$sql_post .= "WHERE boards_post.board_id='$boardsid' AND boards_res.createstamp is NULL ";
				$res_post = pg_query($conn,$sql_post);
				$cnt_post = pg_num_rows($res_post);
				if ($cnt_post>0) {
					$row_post = pg_fetch_array($res_post,0);
					$cnt = $row_post["count"];
				} else {
					$cnt = 0;
				}
				
				if ($cnt>0) {
					$boarddraw = true;
					if (!$boardsw) { $boardbg = $bg_dark; $boardsw = true; }
					else           { $boardbg = $bg_light; $boardsw = false; }
					// BBS名書き出し
					print "<TR BGCOLOR=$bg_light><TD NOWRAP>";
					print "<A HREF=\"./bulletin/?p=top&board=$boardsid\">";
					print $boardsname;
					print "</A>";
					print " ($cnt)";
					print "</TD></TR>";
					print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
					
					$sql_post = "SELECT boards_post.seqno as seqno,subject,body,stamp,boards_post.user_id as user_id FROM boards_post LEFT JOIN (SELECT * FROM boards_res WHERE boards_res.user_id='$login_id') AS boards_res ON boards_post.board_id = boards_res.board_id AND boards_post.seqno = boards_res.seqno WHERE boards_post.board_id='$boardsid' AND boards_res.createstamp is NULL ORDER BY stamp DESC LIMIT 5";
					
					$res_post = pg_query($conn,$sql_post);
					$cnt_post = pg_num_rows($res_post);
					if ($cnt_post>0) {
						for ($j=0;$j<$cnt_post;$j++) {
							print "<TR><TD NOWRAP>";
							$row_post = pg_fetch_array($res_post,$j);
							$subject = $row_post["subject"];
							if ($subject=="") { $subject="(no subject)"; }
							if (strlen($subject)>40) $subject = mb_strcut($subject,0,40).".";
							print "・<A HREF=\"./bulletin/view/?p=top&board=$boardsid&no=".$row_post["seqno"]."\">";
							print $subject;
							print "</A>";
							print "&nbsp;(".get_first("users","name_ryaku","id='".$row_post["user_id"]."'","不明または削除").")";
							print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row_post["stamp"]))."<BR>";
							print "&nbsp;&nbsp;(".mb_strcut(preg_replace("/\x0D\x0A|\x0D|\x0A|\r\n/"," ",$row_post["body"]),0,60,"EUC-JP")."..)";
							
							print "</TD></TR>";
							if ($j<$cnt_post-1) print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
						}
					}
				}
			}
		}
		if (!$boarddraw) {
			print "<TR><TD><FONT COLOR=#CCCCCC>未読なし</TD></TR>\n";
		}
		print "</TABLE>";
		print "</TD></TR></TABLE>";
	}
}
?>