<?
  $menutext .= "
<TABLE><FORM ACTION=\"$toppath/address/search/search.phtml\" METHOD=POST>
<TR>
<TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>

<CENTER>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$toppath/address/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/address.gif\" ALIGN=ABSMIDDLE ALT=\"住所録\" BORDER=0> <A HREF=\"$toppath/address/?new_kind=all\"><FONT COLOR=#FFFFFF>住所録</A></TD></TR>
<TR>
<TD STYLE=\"line-height:15px\" BGCOLOR=#FFFFFF VALIGN=TOP>
";

	$menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";
  $menutext .= "<TR><TD WIDTH=16>";
  if (empty($adr_kind) || $adr_kind == "all") {
	  $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
	} else {
	  $menutext .= "&nbsp;";
	}
	$menutext .= "</TD><TD>";
  if (empty($adr_kind) || $adr_kind == "all") {
		$menutext .= "全て";
  } else {
		$menutext .= "<A HREF=\"$toppath/address/?new_kind=all\">全て";
	} 
	$menutext .= " (".number_format(get_count("address","")).")";
	$menutext .= "</TD></TR>";

  $menutext .= "<TR><TD HEIGHT=1 COLSPAN=2 BGCOLOR=#999999></TD></TR>";

  // address_kind情報の取得(配列も生成)
  $sql_kind = "SELECT * FROM address_kind ORDER BY seqno";
  $res_kind = pg_query($conn,$sql_kind);
  $cnt_kind = pg_num_rows($res_kind);
  if ($cnt_kind>0) {

    for($i=0;$i<$cnt_kind;$i++) {
      $row_kind  = pg_fetch_array($res_kind,$i);
      $kind_name = $row_kind["value"];
      $kind_key  = $row_kind["key"];

      // 住所録の登録済み件数を獲得(正規表現使用のためPostgreSQLに依存)
      // (データ数が増えた場合は前処理を行うか、kind_listの持ち方をに変えたほうがいい by 折笠@ペンギン)
      $sql  = "SELECT count(seqno) as count FROM address WHERE kind_list ~* '(^|,)".$kind_key."(,|$)'";
      $res = pg_query($conn,$sql);
      $cnt = pg_num_rows($res);
      if ($cnt>0) {
        $row = pg_fetch_array($res,0);
        $count = $row["count"];
      } else {
        $count = 0;
      }

			// ▼マーク
		  $menutext .= "<TR><TD WIDTH=16>";
      if ($adr_kind != $kind_key) {
		    $menutext .= "&nbsp;";
		  } else {
		    $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
		  }
		  $menutext .= "</TD><TD>";

      if ($adr_kind != $kind_key) {
        $menutext .= "<A HREF=\"$toppath/address/?new_kind=$kind_key\">";
      }
      $menutext .= $kind_name;
      $menutext .= "</A>";
      if ($count>0) {
        $menutext .= " (".number_format($count).")";
      }
      $menutext .= "</TD></TR>\n";
    }
	}

	$menutext .= "<TR><TD HEIGHT=1 COLSPAN=2 BGCOLOR=#999999></TD></TR>";

  $menutext .= "<TR><TD WIDTH=16>";
  if ($adr_kind == "kindisnone") {
	  $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
	} else {
	  $menutext .= "&nbsp;";
	}
	$menutext .= "</TD><TD>";
  if ($adr_kind == "kindisnone") {
		$menutext .= "分類未設定";
  } else {
		$menutext .= "<A HREF=\"$toppath/address/?new_kind=kindisnone\">分類未設定";
	} 
	$menutext .= " (".number_format(get_count("address","kind_list='' or kind_list is null")).")";
	$menutext .= "</TD></TR>";

  $menutext .= "</TABLE>\n";

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>

<BR>
";
?>