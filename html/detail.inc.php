<?
  if ($login && sizeof($MES)==0) {
?>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100% HEIGHT=420>
<TR WIDTH=100%>
<?
    if (trim($menutext)!="") {
?>
<TD WIDTH=170 VALIGN=TOP ALIGN=CENTER>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=170><TR>
<TD STYLE="line-height:130%"><? echo $menutext; ?></TD></TR></TABLE></TD>
<TD WIDTH=1 HEIGHT=460 BGCOLOR=#999999 STYLE="width:1px;height:460px"></TD>

<TD VALIGN=TOP><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=100%><TR><TD STYLE="line-height:13px">
<?
    } else {
?>
<TD WIDTH=100% VALIGN=TOP><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=4 WIDTH=100%><TR><TD STYLE="line-height:13px">
<?
    }
    // 
    if (trim($pagetext)=="") $pagetext = "<FONT COLOR=#FFFFFF>This [content]page is blank</FONT>\n";
    print $pagetext;

    if (sizeof(split("\/",$request_uri))>3) {
      print "<BR><DIV ALIGN=RIGHT><A HREF=\"javascript:history.back()\">&lt; ��� &gt;</A>&nbsp;</DIV><BR>";
    }
?>
</TD></TR>
</TABLE>
</TD></TR>
</TABLE>
</TD></TR>
</TABLE>
<?
  } else {
    if (sizeof($MES)>0) {
      // ���顼��
      print "<BR><BR><CENTER><FONT COLOR=#FF0000>";
      // ���顼��å���������
      while (list($key,$val)=each($MES)) {
        print $val."<BR>";
      }
      print "</CENTER>\n";
    }
  }
?>