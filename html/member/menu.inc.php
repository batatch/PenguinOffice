<?
  $menutext .= "
<CENTER>
<TABLE><FORM ACTION=\"./\">
<TR><TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD VALIGN=TOP ALIGN=CENTER BGCOLOR=#999999><FONT COLOR=#FFFFFF>グループ一覧</TD></TR>
<TR BGCOLOR=#FFFFFF><TD BGCOLOR=#FFFFFF>
";
  ## グループ一覧取得
  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR=#FFFFFF WIDTH=100%>";
  $menutext .= "<TR>";
  $menutext .= "<TD NOWRAP WIDTH=12>";
  if ($mem_group=="all" || $mem_group=="") {
    $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13 ALT=\"\">";
  }
  $menutext .= "</TD>";
  $menutext .= "<TD STYLE=\"line-height:13px\">";
  $menutext .= "<A HREF=\"$toppath/member/?new_group=all\">";
  $menutext .= "全員";
  $menutext .= "</A>";
  $menutext .= "</TD>";

  $menutext .= "<TR><TD COLSPAN=2><HR SIZE=1></TD></TR>";

  $sql = "SELECT id,name from groups ORDER BY seqno";
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  for ($i=0;$i<$cnt;$i++) {
    $row = pg_fetch_array($res,$i);
    $menutext .= "<TR>";
    $menutext .= "<TD NOWRAP WIDTH=12>";
    if ($mem_group==$row["id"]) {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13 ALT=\"\">";
    }
    $menutext .= "</TD>";
    $menutext .= "<TD STYLE=\"line-height:13px\">";
    $menutext .= "<A HREF=\"$toppath/member/?new_group=".$row["id"]."\">";
    $menutext .= $row["name"];
    $menutext .= "</A>";
    $menutext .= "</TD>";
    $menutext .= "</TR>";
  }
  $menutext .= "</TABLE>

</TD></TR></TABLE>
</TD></TR></TABLE>
</CENTER>
";
?>