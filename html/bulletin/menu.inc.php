<?
  $menutext .= "
<TABLE><FORM ACTION=\"./\">
<TR>
<TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>

<CENTER>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$toppath/bulletin/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/bulletin.gif\" ALIGN=ABSMIDDLE ALT=\"BBS\" BORDER=0><FONT COLOR=#FFFFFF> BBS</TD></TR>
<TR>
<TD STYLE=\"line-height:15px\" BGCOLOR=#FFFFFF VALIGN=TOP>
";
  // BBS情報の取得(配列も生成)
  $sql_board = "SELECT seqno,id,name,admin_id,view_group_ids,view_user_ids FROM boards ORDER BY seqno";
  $res_board = pg_query($conn,$sql_board);
  $cnt_board = pg_num_rows($res_board);
  if ($cnt_board>0) {
	  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";

    for($i=0;$i<$cnt_board;$i++) {
      $row_board  = pg_fetch_array($res_board,$i);
      $board_name = $row_board["name"];
      $board_id   = $row_board["id"];

      $group_ids = $row_board["view_group_ids"];
      $user_ids  = $row_board["view_user_ids"];

      $view = false;
      if ($admin_sign=="t" || $row_board["admin_id"]==$login_id || ($group_ids=="" && $user_ids=="")) {
        $view = true;
      } else {
        if ($group_ids!="") {
          $view_group_id = split(",",$group_ids);
          if (sizeof($view_group_id)>0) while (list($seq,$id)=each($view_group_id)) {
            if (sizeof($groups)>0) {
              reset($groups);
              while (list($gseq,$gid)=each($groups)) {
                if ($gid==$id) { $view = true; break; }
              }
            }
          }
        }
        if ($user_ids!="") {
          $view_user_id = split(",",$user_ids);
          if (sizeof($view_user_id)>0) while (list($seq,$id)=each($view_user_id)) {
            if ($login_id==$id) { $view = true; break; }
          }
        }
      }
      if ($view) {
        $boards[$row_board["id"]] = array($row_board["name"],$row_board["admin_id"]);
        if (empty($board)) {
          $board = $row_board["id"];
        }
        if ($board==$row_board["id"]) {
          $board_admin_id = $row_board["admin_id"];
        }
        // 投稿件数の取得 もとい
#        $sql_post = "SELECT count(*) FROM boards_post WHERE board_id='$board_id'";

        // BBSの未読数を獲得
        $sql_post  = "SELECT count(boards_post.seqno) as count ";
        $sql_post .= "FROM boards_post LEFT JOIN (SELECT * FROM boards_res WHERE boards_res.user_id='$login_id') AS boards_res ON boards_post.board_id = boards_res.board_id AND boards_post.seqno = boards_res.seqno ";
#        $sql_post .= "WHERE boards_post.board_id='$board_id' AND boards_post.user_id<>'$login_id' AND boards_res.createstamp is NULL ";
        $sql_post .= "WHERE boards_post.board_id='$board_id' AND boards_res.createstamp is NULL ";
        $res_post = pg_query($conn,$sql_post);
        $cnt_post = pg_num_rows($res_post);
        if ($cnt_post>0) {
          $row_post = pg_fetch_array($res_post,0);
          $count = $row_post["count"];
        } else {
          $count = 0;
        }

				// ▼マーク
			  $menutext .= "<TR><TD WIDTH=16>";
        if ($board!=$row_board["id"]) {
			    $menutext .= "&nbsp;";
			  } else {
		      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
			  }
			  $menutext .= "</TD><TD>";

        // BBS名書き出し
        if ($board!=$row_board["id"]) {
          $menutext .= "<A HREF=\"$toppath/bulletin/?board=$board_id\">";
        }
        $menutext .= $board_name;
        $menutext .= "</A>";
        if ($count>0) {
          $menutext .= " ($count)";
        }
        $menutext .= "</TD></TR>\n";
      }
    }
		$menutext .= "</TABLE>";
  }

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>

<BR>
";
?>