<?
  $menutext .= "
<TABLE><FORM ACTION=\"./\">
<TR>
<TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=HIDDEN NAME=\"mail_search\" VALUE=\"t\"><INPUT TYPE=TEXT NAME=\"new_keyword\" VALUE=\"$mail_keyword\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>
<CENTER>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$topath/mail/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/mail.gif\" WIDTH=13 HEIGHT=11 ALIGN=ABSMIDDLE ALT=\"伝言メモ\" BORDER=0><FONT COLOR=#FFFFFF> 伝言メモ</TD></TR>
<TR>
<TD STYLE=\"line-height:16px;text-align:left\" BGCOLOR=#FFFFFF VALIGN=TOP>
";
  // 伝言メモ情報の取得(配列も生成)
  $sql_count = "SELECT count(*) as count FROM mail WHERE user_id='$login_id' AND \"from\"='$login_id'";
  $res_count = pg_query($conn,$sql_count);
  $cnt_count = pg_num_rows($res_count);
  if ($cnt_count>0) {
    for($i=0;$i<$cnt_count;$i++) {
      $row_count = pg_fetch_array($res_count,$i);
      $from_count += $row_count["count"];
    }
  }

  $sql_count2 = "SELECT count(*) as count FROM mail WHERE user_id='$login_id' AND \"to\" ~* '(^|,)$login_id(,|$)'";
  $res_count2 = pg_query($conn,$sql_count2);
  $cnt_count2 = pg_num_rows($res_count2);
  if ($cnt_count2>0) {
    for($i=0;$i<$cnt_count2;$i++) {
      $row_count2 = pg_fetch_array($res_count2,$i);
      $to_count += $row_count2["count"];
    }
  }

  // 伝言メモ名書き出し
  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($mail_type=="to") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  $menutext .= "<A HREF=\"$toppath/mail/?new_type=to\">";
  if ($to_count==0) {
    $menutext .= "<FONT STYLE=\"font-weight:normal;text-decoration:none\">受信メモ</FONT></A><BR>";
  } else {
    $menutext .= "受信メモ</A> (".number_format($to_count).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

#  $menutext .= "<TR><TD COLSPAN=2>";
#  $menutext .= "<HR SIZE=1>\n";
#  $menutext .= "</TD></TR>\n";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($mail_type=="from") {
    $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  $menutext .= "<A HREF=\"$toppath/mail/?new_type=from\">";
  if ($from_count==0) {
    $menutext .= "<FONT STYLE=\"font-weight:normal;text-decoration:none\">送信メモ</FONT></A><BR>";
  } else {
    $menutext .= "送信メモ</A> (".number_format($from_count).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

  $menutext .= "</TABLE>\n";

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>

<BR>
";
?>