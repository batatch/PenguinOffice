<?
  $menutext .= "
<CENTER>
<TABLE><FORM ACTION=\"./\">
<TR><TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"����\" ALIGN=ABSMIDDLE><INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\"><INPUT TYPE=SUBMIT VALUE=\"����\" STYLE=\"width:36px\">
</TD></TR></FORM></TABLE>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD BGCOLOR=#999999><A HREF=\"$toppath/workflow/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/workflow.gif\" ALIGN=ABSMIDDLE ALT=\"����ե�\" BORDER=0><FONT COLOR=#FFFFFF> ����ե�</TD></TR>
<TR>
<TD STYLE=\"line-height:16px;text-align:left\" BGCOLOR=#FFFFFF VALIGN=TOP>
";
  // ��Хơ��֥�ε�ǡ����õ�
  pg_query($conn, "DELETE FROM t_workflow WHERE createstamp<'".timestamp2datetime(time()-86400*7)."'");

  // ��Хơ��֥�ξõ�
  pg_query($conn, "DELETE FROM t_workflow WHERE session_id='$sid'");

  // ��Хơ��֥�κ���
  $sql_pre = "SELECT seqno,flow_ids,result_sign FROM workflow WHERE flow_ids ~* '(^|,)".$login_id."(,|$)'";
  $res_pre = pg_query($conn, $sql_pre);
  $cnt_pre = pg_num_rows($res_pre);
  if ($cnt_pre>0) {
    for ($i=0;$i<$cnt_pre;$i++) {
      $row_pre = pg_fetch_array($res_pre,$i);

      // �оݥǡ�����ɬ�פ��ɤ�����Ƚ�����
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

      $status = "0"; //̤ʬ��Ȥ���

      if ($row_pre["result_sign"]=="") {
        // ��ǧ����λ���Ƥ��ʤ��ǡ���
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
        // ��ǧ����Ƥ���
        $status = "2";
      } elseif ($row_pre["result_sign"]=="f") {
        // ��ǧ����Ƥ���
        $status = "3";
      }
      pg_query($conn, "INSERT INTO t_workflow (session_id,seqno,status,createstamp) VALUES ('$sid',".$row_pre["seqno"].",'$status','now()')");
    }
  }

  // ����ե�����μ���(���������)
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
        // ̤��ǧ�Υ������
        $to_not += $row_count2["count"];
      } elseif ($row_count2["status"]=="2") {
        // ��ǧ�ѤߤΥ������
        $to_has += $row_count2["count"];
      } elseif ($row_count2["status"]=="3") {
        // ��ǧ�ѤߤΥ������
        $to_has2 += $row_count2["count"];
      }
    }
  }

  // ����ե�̾�񤭽Ф�
  $menutext .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=150>\n";

  // ������
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
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">̤��ǧ</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=to\">̤��ǧ</A> (".number_format($to_not).")<BR>\n";
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
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">��ǧ��</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=to\" STYLE=\"font-weight:normal;\">��ǧ��</A> (".number_format($to_has).")<BR>\n";
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
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread2&viewrange=to\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">��ǧ��</FONT></A><BR>\n";
    } else {
      $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread2&viewrange=to\" STYLE=\"font-weight:normal;\">��ǧ��</A> (".number_format($to_has2).")<BR>\n";
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

  // �����
  $menutext .= "<TR><TD WIDTH=16>";
  if ($viewtype=="notread" && $viewrange=="from") {
      $menutext .= "<IMG SRC=\"$toppath/image/tri.gif\" WIDTH=12 HEIGHT=13>";
  } else {
    $menutext .= "&nbsp;";
  }
  $menutext .= "</TD><TD>";
  if ($from_not==0) {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">�����</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=notread&viewrange=from\">�����</A> (".number_format($from_not).")<BR>\n";
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
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=from\"><FONT STYLE=\"font-weight:normal;text-decoration:none\">��ۺѤ�</FONT></A><BR>\n";
  } else {
    $menutext .= "<A HREF=\"$toppath/workflow/?viewtype=hasread&viewrange=from\" STYLE=\"font-weight:normal;\">��ۺѤ�</A> (".number_format($from_has).")<BR>\n";
  }
  $menutext .= "</TD></TR>\n";
  $menutext .= "</TABLE>\n";

  $menutext .= "</TD></TR>
</TABLE>
</TD></TR></TABLE>";

  $menutext .= "</CENTER>";

	if ($workflow_sign=="t") {
		$menutext .= "<BR>��&nbsp;<A HREF=\"$toppath/workflow/flows/\"><IMG SRC=\"$toppath/image/flow.gif\" BORDER=0 ALIGN=ABSMIDDLE> �ե��Խ�</A>";
	}
?>