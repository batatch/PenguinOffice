</TD></TR></TABLE></TD></TR></TABLE>
<?
  $uri = preg_replace("/(\?|&)fontsize=\d*/","",$request_uri);
  if (eregi("\?",$uri)) {
    $fontlink = $uri."&fontsize";
  } else {
    $fontlink = $uri."?fontsize";
  }
?>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=0 BGCOLOR=#DDDDFF><TR><TD ALIGN=CENTER>
<FONT COLOR=#666666>&copy;Copyright 2001-<? echo date("Y")?> <A HREF="http://www.penguin.co.jp/" target="_blank">Penguin Factory Inc</a>.&nbsp;All rights reserverd.
</TD></TR></TABLE>

<TABLE WIDTH=100%><TR><TD ALIGN=RIGHT>
文字サイズの変更&gt; <A HREF="<? echo $fontlink ?>=8"><FONT STYLE="font-size:8pt">Aa</FONT></A> <A HREF="<? echo $fontlink ?>=9"><FONT STYLE="font-size:9pt">Aa</FONT></A> <A HREF="<? echo $fontlink ?>=10"><FONT STYLE="font-size:10pt">Aa</FONT></A> <A HREF="<? echo $fontlink ?>=11"><FONT STYLE="font-size:11pt">Aa</FONT></A>
</TD></TR></TABLE>
</BODY></HTML>
