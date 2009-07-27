<?
  $menutext .= "
<CENTER>
<TABLE><FORM ACTION=\"./\"><TR><TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$toppath/circular/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/circular.gif\" ALIGN=ABSMIDDLE ALT=\"回覧板\" BORDER=0><FONT COLOR=#FFFFFF> 回覧板</TD></TR>
<TR>
<TD STYLE=\"line-height:16px;text-align:left\" BGCOLOR=#FFFFFF VALIGN=TOP>
";
  // 回覧板情報の取得(配列も生成)
  $sql_count = "SELECT result_sign,count(*) as count FROM circulas WHERE user_id='$login_id' GROUP BY result_sign";
  $res_count = pg_query($conn,$sql_count);
  $cnt_count = pg_num_rows($res_count);
  if ($cnt_count>0) {
    for($i=0;$i<$cnt_count;$i++) {
      $row_count = pg_fetch_array($res_count,$i);
      if ($row_count["result_sign"]=="t") {
        $from_has += $row_count["count"];
      } else {
        $from_not += $row_count["count"];
      }
    }
  }

  $sql_count2 = "SELECT circulas_ret.result_sign,count(*) as count FROM circulas LEFT JOIN (SELECT refno,result_sign FROM circulas_ret WHERE user_id ='$login_id') AS circulas_ret ON circulas.seqno = circulas_ret.refno WHERE user_id_to ~* '(^|,)$login_id(,|$)' GROUP BY circulas_ret.result_sign";
  $res_count2 = pg_query($conn,$sql_count2);
  $cnt_count2 = pg_num_rows($res_count2);
  if ($cnt_count2>0) {
    for($i=0;$i<$cnt_count2;$i++) {
      $row_count2 = pg_fetch_array($res_count2,$i);
      if ($row_count2["result_sign"]=="t") {
        $to_has += $row_count2["count"];
      } else {
        $to_not += $row_count2["count"];
      }
    }
  }

  // 回覧板名書き出し
  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";
  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="notread" && $viewrange=="to") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($to_not==0) {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=notread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">未読</FONT></A><BR>";
  } else {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=notread&viewrange=to\">未読</A> (".number_format($to_not).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="hasread" && $viewrange=="to") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($to_has==0) {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=hasread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">既読</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=hasread&viewrange=to\" STYLE=\"font-weight:normal;\">既読</A> (".number_format($to_has).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

  $menutext .= "<TR><TD COLSPAN=2>";
  $menutext .= "<HR SIZE=1>\n";
  $menutext .= "</TD></TR>\n";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="notread" && $viewrange=="from") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($from_not==0) {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=notread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">回覧中</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=notread&viewrange=from\">回覧中</A> (".number_format($from_not).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="hasread" && $viewrange=="from") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($from_has==0) {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=hasread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">回覧済み</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/circular/?viewtype=hasread&viewrange=from\" STYLE=\"font-weight:normal;\">回覧済み</A> (".number_format($from_has).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";

  $menutext .= "</TABLE>\n";

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>

<BR>
";
?>