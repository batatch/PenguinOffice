<?
  $menutext .= "
<CENTER>
<TABLE><FORM ACTION=\"./\">
<TR><TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$toppath/workflow/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/workflow.gif\" ALIGN=ABSMIDDLE ALT=\"ワークフロー\" BORDER=0><FONT COLOR=#FFFFFF> ワークフロー</TD></TR>
<TR>
<TD STYLE=\"line-height:16px;text-align:left\" BGCOLOR=#FFFFFF VALIGN=TOP>
";
  // 抽出テーブルの旧データ消去
  pg_query($conn, "DELETE FROM t_workflow WHERE createstamp<'".timestamp2datetime(time()-86400*7)."'");

  // 抽出テーブルの消去
  pg_query($conn, "DELETE FROM t_workflow WHERE session_id='$sid'");

  // 抽出テーブルの作成
  $sql_pre = "SELECT seqno,flow_ids,result_sign FROM workflow WHERE flow_ids ~* '(^|,)".$login_id."(,|$)'";
  $res_pre = pg_query($conn, $sql_pre);
  $cnt_pre = pg_num_rows($res_pre);
  if ($cnt_pre>0) {
    for ($i=0;$i<$cnt_pre;$i++) {
      $row_pre = pg_fetch_array($res_pre,$i);

      // 対象データが必要かどうかの判定処理
      $flow_ids = $row_pre["flow_ids"];
      $flow_arr = split(",",$flow_ids);
      $flow_cnt = sizeof($flow_arr);
      if($flow_cnt>0) {
        for($c=0;$c<$flow_cnt;$c++) {
          if ($flow_arr[$c]==$login_id) {
            $flow_no = $c+1;
            break;
          }
        }
      }

      $status = "0"; //未分類とする

      if ($row_pre["result_sign"]=="") {
        // 承認が完了していないデータ
        $recognize_sign = get_first("workflow_ret","recognize_sign","refno=".$row_pre["seqno"]." AND seqno=".$flow_no,"");
        if ($recognize_sign=="") {
          if ($flow_no==1) {
            $status = "1";
          } else {
            $before_res_cnt = get_count("workflow_ret","refno=".$row_pre["seqno"]." AND seqno=".($flow_no-1));
            if ($before_res_cnt>0) {
              $status = "1";
            }
          }
        } else {
          if ($recognize_sign=="t") {
            $status = "2";
          } elseif ($recognize_sign=="f") {
            $status = "3";
          }
        }
      } elseif ($row_pre["result_sign"]=="t") {
        // 承認されている
        $status = "2";
      } elseif ($row_pre["result_sign"]=="f") {
        // 否認されている
        $status = "3";
      }
      pg_query($conn, "INSERT INTO t_workflow (session_id,seqno,status,createstamp) VALUES ('$sid',".$row_pre["seqno"].",'$status','now()')");
    }
  }

  // ワークフロー情報の取得(配列も生成)
  $sql_count = "SELECT result_sign,count(*) as count FROM workflow WHERE user_id='$login_id' GROUP BY result_sign";
  $res_count = pg_query($conn,$sql_count);
  $cnt_count = pg_num_rows($res_count);
  if ($cnt_count>0) {
    for($i=0;$i<$cnt_count;$i++) {
      $row_count = pg_fetch_array($res_count,$i);
      if ($row_count["result_sign"]=="") {
        $from_not += $row_count["count"];
      } else {
        $from_has += $row_count["count"];
      }
    }
  }

  $sql_count2 = "SELECT status,count(*) as count FROM t_workflow WHERE session_id='$sid' GROUP BY status";
  $res_count2 = pg_query($conn,$sql_count2);
  $cnt_count2 = pg_num_rows($res_count2);
  if ($cnt_count2>0) {
    for($i=0;$i<$cnt_count2;$i++) {
      $row_count2 = pg_fetch_array($res_count2,$i);
      if ($row_count2["status"]=="1") {
        // 未承認のカウント
        $to_not += $row_count2["count"];
      } elseif ($row_count2["status"]=="2") {
        // 承認済みのカウント
        $to_has += $row_count2["count"];
      } elseif ($row_count2["status"]=="3") {
        // 否認済みのカウント
        $to_has2 += $row_count2["count"];
      }
    }
  }

  // ワークフロー名書き出し
  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";

  // 受け系
  $cnt_master = get_count("flows","user_ids ~* '(^|,)".$login_id."(,|$)'");
  if ($cnt_master>0) {
    $menutext .= "<TR><TD WIDTH=16>";
    if ($viewtype=="notread" && $viewrange=="to") {
      $menutext .= "<IMG SRC=\"$topath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
    } else {
      $menutext .= "&nbsp;";
    }
    $menutext .= "</TD><TD>";
    if ($to_not==0) {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">未承認</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=to\">未承認</A> (".number_format($to_not).")<BR>\n";
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
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">承認済</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=to\" STYLE=\"font-weight:normal;\">承認済</A> (".number_format($to_has).")<BR>\n";
    }
    $menutext .= "</TD></TR>\n";

    $menutext .= "<TR><TD WIDTH=16>";
    if ($viewtype=="hasread2" && $viewrange=="to") {
      $menutext .= "<IMG SRC=\"$topath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
    } else {
      $menutext .= "&nbsp;";
    }
    $menutext .= "</TD><TD>";
    if ($to_has2==0) {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread2&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">否認済</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread2&viewrange=to\" STYLE=\"font-weight:normal;\">否認済</A> (".number_format($to_has2).")<BR>\n";
    }
    $menutext .= "</TD></TR>\n";

    $menutext .= "<TR><TD COLSPAN=2>";
    $menutext .= "<HR SIZE=1>\n";
    $menutext .= "</TD></TR>\n";
  } else {
    if ($viewrange=="to") {
      $viewtype = "notread";
      $viewrange = "from";
    }
  }

  // 送り系
  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="notread" && $viewrange=="from") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($from_not==0) {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">決裁中</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=from\">決裁中</A> (".number_format($from_not).")<BR>\n";
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
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">決裁済み</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=from\" STYLE=\"font-weight:normal;\">決裁済み</A> (".number_format($from_has).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";
  $menutext .= "</TABLE>\n";

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>";

  $menutext .= "</CENTER>";

	if ($workflow_sign=="t") {
		$menutext .= "<BR>　&nbsp;<A HREF=\"$toppath/workflow/flows/\"><IMG SRC=\"$toppath/image/flow.gif\" BORDER=0 ALIGN=ABSMIDDLE> フロー編集</A>";
	}
?>