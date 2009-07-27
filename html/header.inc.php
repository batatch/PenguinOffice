<?
  session_start();
  if (!isset($_SESSION['scnt'])) {
    $_SESSION['scnt'] = 0;
  } else {
    $_SESSION['scnt']++;
  }
  $sid = session_id();

  global $_COOKIE;

  $login_no = 0;
  $login_id = "";
  $login_name = "";
  $admin_sign = "f";

  $user_agent  = getenv("HTTP_USER_AGENT");
  $remote_addr = getenv("REMOTE_ADDR");
  $remote_host = getenv("REMOTE_HOST");
  $script_name = getenv("SCRIPT_NAME");
  $request_uri = getenv("REQUEST_URI");
  $referer     = getenv("HTTP_REFERER");
  $host_name   = getenv("HTTP_HOST");
  $server_port = getenv("SERVER_PORT");

  if (!$conn) { /* 日付を Y-m-d h:n:s に固定 */
    $conn = pg_connect($pgsql_connect);
    $res = pg_query($conn,"SET DATESTYLE TO 'ISO'");
  } else {
    $res = pg_query($conn,"SET DATESTYLE TO 'ISO'");
  }

  // ケータイチェック
  $user_agent = getenv("HTTP_USER_AGENT");
  // iモード以外除外
  if (eregi("^DoCoMo\/",$user_agent) or eregi("^J\-PHONE\/",$user_agent) or preg_match("/UP\.Browser/",$user_agent)) {
    header("Location: ".$access.$domain.$toppath."/i/");
    exit;
  }

  switch($_REQUEST["mode"]){
  case "login":
    // 通常ログイン
    if (empty($_COOKIE["PHPSESSID"])) {
      $headerr[] = "ご利用のブラウザのCookieを使用可能にしてください<BR>既に使用可能になっている場合は、ご利用のマシンの日時が正しく設定されているか確認してください";
    } else {
      global $_POST;

#      if ($_COOKIE["PHPSESSID"] != $_POST["sid"]) {
      if ($_COOKIE["PHPSESSID"] != $sid) {
        $headerr[] = "Cookieが利用できません。ご利用のブラウザのCookieを使用可能にしてください<BR>";
      }
      /* 未入力チェック */
      if ($userid=="") {
        $headerr[] = "ユーザーIDが指定されていません";
      } else {
        if ($pwd=="") {
          $headerr[] = "パスワードが指定されていません";
        } else {
          $pwd_c = $pwd;
          $pwd_cr = crypt($pwd_c,substr($pwd_c,1,2));

          // ログイン
          $sql  = "SELECT * FROM users ";
          $sql .= "WHERE id='".$userid."' AND (passwd='".$pwd_cr."' or passwd='".$pwd_c."')";
#          $sql .= "WHERE id='".$userid."' AND passwd='".$pwd_cr."'";
          $res = pg_query($conn, $sql);
          $cnt = pg_num_rows($res);

          if ($cnt==0) {
            $headerr[] = "指定されたユーザーID/パスワードでの登録が見つかりません";
          } else {
            // ログイン
            $row = pg_fetch_array($res,0);
            $login_str = crypt($_POST["userid"],$sid);
            $login = true; $login_now = true;
            $login_id = $row["id"];

            $refresh = true;

            $headertext .= "Set-Cookie: pgo=".urlencode($login_str)."; path="."/;\n";

            $sql  = "UPDATE users SET session_id='$sid', session_laststamp='now()' ";
            $sql .= "where id='".$_POST["userid"]."'";
            pg_query($conn, $sql);
          }
        }
      }
    }
    break;
  case "dlogin":
    // ダイレクトログイン
    if ($sn != "") {
      $sql = "SELECT * FROM users WHERE onetime_passwd='".$sn."'";
      $res = pg_query($conn,$sql);
      $cnt = pg_num_rows($res);
      if ($cnt>0) {
        $row = pg_fetch_array($res,0);

        $login_str = crypt($row["id"],$sid);
        $login = true; $login_now = true;
        $login_id = $row["id"];

#        $refresh = true;

        $headertext .= "Set-Cookie: pgo=".urlencode($login_str)."; path="."/;\n";

        $sql  = "UPDATE users SET session_id='$sid', session_laststamp='now()' ";
        $sql .= "where id='".$row["id"]."'";
        pg_query($conn, $sql);
      }
    }
    break;

  case "logout":
    // ログアウト
    $refresh = false;
    $login = false;

    // セッションID解除処理
    if (!empty($_COOKIE["pgo"])) {
      $login_str = $_COOKIE["pgo"];
      if (trim($sid)=="") $sid = $_COOKIE["PHPSESSID"];
      // ユーザー存在確認
      $sql = "SELECT * FROM users WHERE session_id='".$sid."'";
      $res = pg_query($conn,$sql);
      $cnt = pg_num_rows($res);
      if ($cnt>0) {
        $sql = "UPDATE users SET session_id=NULL where session_id='".$sid."'";
        pg_query($conn,$sql);
      }
    }
    $headertext .= "Set-Cookie: ".session_name()." expires=".gmdate("D, d M Y H:i:s",time()-3600)." GMT; path="."/;\n";
    $_SESSION = array(); 
    session_unset();
    session_destroy();

    // Cookie処理
    $headertext .= "Set-Cookie: pgo=; expires=".gmdate("D, d M Y H:i:s",time()-3600)." GMT; path="."/;\n";
    $headertext .= "Location: ".$access.$domain."/?logout_now=true;\n";
    break;

  default:
    // 通常処理
    $refresh = false; $login = false;

    if (!empty($_COOKIE["pgo"])) {
      $login_str = $_COOKIE["pgo"];
      if (trim($sid)=="") $sid = $_COOKIE["PHPSESSID"];

      // ユーザー存在確認
      $sql = "SELECT * FROM users WHERE session_id='".$sid."'";
      $res = pg_query($conn,$sql);
      $cnt = pg_num_rows($res);
      if ($cnt==1) {
        $row = pg_fetch_array($res,0);
        if (crypt($row["id"],$sid)==$login_str) {
          $headertext .= "Set-Cookie: pgo=".urlencode($login_str)."; path=".$toppath."/;\n";
          $login_id = $row["id"];
          $login_name = euc2sjis($row["name"]);
          $login = true;
        }
      } elseif ($cnt>1) {
        $sql = "UPDATE users SET session_id=NULL where session_id='".$sid."'";
        pg_query($conn,$sql);
      } else {
        $headerr[] = "同じユーザーIDで他PCからのログインがあったため自動的にログアウトしました。";
        $login = false;
        $headertext .= "Set-Cookie: pgo=; expires=".gmdate("D, d M Y H:i:s",time()-3600)." GMT; path="."/;\n";
      }
    } else {
      $login = false;
    }
    break;
  }

  // 変数展開
  $login_no      = 0;
  $admin_sign    = "f";
  $download_sign = "f";

  if ($login) {
    $sql = "select * from users where id='".$login_id."'";
    $res = pg_query($conn,$sql);
    $cnt = pg_num_rows($res);
    if ($cnt>0) {
      $row = pg_fetch_array($res,0);
      $login_no           = $row["seqno"];
      $login_name         = $row["name"];
      $login_email        = $row["email"];
      $passwd_updatestamp = $row["passwd_updatestamp"];
      $admin_sign         = $row["admin_sign"];
      $download_sign      = $row["download_sign"];
      $workflow_sign      = $row["workflow_sign"];
      $address_flag       = $row["address_flag"];
    } else {
      $login_no           = 0;
      $admin_sign         = 'f';
      $sv_sign            = 'f';
      $download_sign      = 'f';
      $workflow_sign      = 'f';
      $address_flag       = 3;
    }
  } else {
    $admin_sign = 'f';
    $sv_sign = 'f';
    $download_sign = 'f';
  }
  // チェック用Cookie
#  $headertext .= "Set-Cookie: check=on; path=/; expires=".gmdate("l, d-M-Y H:i:s",time()+15552000)." GMT;\n".$headertext;
#  header($headertext);
  foreach (explode("\n",$headertext) as $headerline) {
    header($headerline);
  }

  // Tracking
  if (empty($sid) || $mode=="login" || $mode=="dlogin") {
    $first_sign = "t";
  } else {
    $first_sign = "f";
  }
  $sql  = "DELETE FROM tracking WHERE calltime+'".(24*$logday).":00'<now();";
  $sql .= "INSERT INTO tracking (serial,id,ip,url,uri,ref,agent,firstsign,calltime) values (";
  $sql .= "'".$sid."',";
  $sql .= "'".$login_id."',";
  $sql .= "'".$remote_addr."',";
  $sql .= "'".db_textsafe(textsafe(mb_convert_encoding($script_name,"EUC-JP")))."',";
  $sql .= "'".db_textsafe(textsafe(mb_convert_encoding($request_uri,"EUC-JP")))."',";
  $sql .= "'".db_textsafe(textsafe(mb_convert_encoding($referer,"EUC-JP")))."',";
  $sql .= "'".db_textsafe(textsafe(mb_convert_encoding($user_agent,"EUC-JP")))."',";
  $sql .= "'".$first_sign."',";
  $sql .= "'now()')";
  print "<!--";
  pg_query($conn, "BEGIN;".$sql.";COMMIT;");
  print "-->";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML lang="ja-JP">
<HEAD>
<!--・-->
<TITLE>PENGUIN OFFICE</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<META http-equiv="page-exit" content="BlendTrans(Duration=0.2)">
<?
#  if ($refresh and sizeof($headerr)==0) {
#    print "<META http-equiv=\"refresh\" content=\"0; URL=\"/?nl=t\">\n";
#    print "</HEAD>\n";
#    print "<BODY>";
#    print "</BODY>\n";
#    print "</HTML>\n";
#    exit;
#  }
?>
<STYLE TYPE="text/css">
<?php
  echo $css;
?>
</STYLE>
</HEAD>

<?
  // BODYタグ
  print "<BODY TEXT=$bodyForeColor LINK=$bodyLinkColor VLINK=$bodyVLinkColor ALINK=$bodyALinkColor BGCOLOR=$bodyBackColor LEFTMARGIN=0 RIGHTMARGIN=0 TOPMARGIN=0 BOTTOMMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=>\n";

  $request_uri2 = preg_replace("/(\?|&)print=(on|off)/","",$request_uri);
  if (eregi("\?",$request_uri2)) {
    $printpath = $request_uri2."&print=on";
    $displaypath = $request_uri2."&print=off";
  } else {
    $printpath = $request_uri2."?print=on";
    $displaypath = $request_uri2."?print=off";
  }

  if ($print != "on") {
    $borderwidth = 0;
    print "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>";
    print "<TR BGCOLOR=$title_backcolor WIDTH=100% HEIGHT=39>\n";
    print "<TD WIDTH=20% HEIGHT=39 VALIGN=TOP NOWRAP>";
    print "<A HREF=\"$toppath/\"><IMG SRC=\"$toppath/image/logo.gif\" ALT=\"".strip_tags($system_name)."\" BORDER=0 WIDTH=129 HEIGHT=40></A>";
    print "</TD>";
    print "<TD WIDTH=60% ALIGN=CENTER HEIGHT=39 NOWRAP>";
    if ($homeurl != ""){
      print "<FONT COLOR=$title_forecolor2><B>$service_name</B></FONT>&nbsp;";
      print "<A HREF=\"$homeurl\"><IMG SRC=\"$toppath/image/house01.gif\" BORDER=0 ALT=\"ホームページ\"></A>";
    } else {
      print "<FONT COLOR=$title_forecolor2><B>$service_name</B></FONT>\n";
    }

    print "</TD><TD ALIGN=RIGHT VALIGN=BOTTOM WIDTH=20% HEIGHT=39 NOWRAP>";

    if ($login) {
      print "<FONT COLOR=$logout_forecolor>ようこそ&nbsp;<B>$login_name</B>&nbsp;様&nbsp;";
      if (getenv("SERVER_PORT")==443) {
        print "<A HREF=\"http://".$domain.$request_uri."\"><IMG SRC=\"$toppath/image/secure.gif\" ALIGN=ABSMIDDLE BORDER=0 WIDTH=12 HEIGHT=15 ALT=\"SSL\"></A>";
      } elseif (getenv("SERVER_PORT")==80) {
        print "<A HREF=\"https://".$domain.$request_uri."\"><IMG SRC=\"$toppath/image/nonsecure.gif\" ALIGN=ABSMIDDLE BORDER=0 WIDTH=12 HEIGHT=15 ALT=\"nonSSL\"></A>";
      }
      print "&nbsp;<BR>";

      print "<A HREF=\"$printpath\"><IMG SRC=\"$toppath/image/btn_print.gif\" ALT=\"PrintMode\" BORDER=0></A>";
      print "<A HREF=\"javascript:location.href='$toppath/?mode=logout'\"><IMG SRC=\"$toppath/image/btn_logout.gif\" BORDER=0 ALT=\"ログアウト\"></A>";
    }

    print "</TD></TR></TABLE>\n";
  } else {
    $borderwidth = 1;
    print "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR BGCOLOR=$menu_backcolor>";
    print "<TD ALIGN=LEFT>";
#        print "<A HREF=\"/\"><IMG SRC=\"/image/logo.gif\" ALT=\"".strip_tags($system_name)."\" BORDER=0></A>";
    print "</TD>\n";
    print "<TD ALIGN=RIGHT>";
    print "<A HREF=\"$displaypath\"><IMG SRC=\"$toppath/image/btn_display.gif\" ALT=\"DisplayMode\" BORDER=0></A>";
    print "</TD>\n";
    print "</TR>\n";
    print "<TR><TD BGCOLOR=$logout_backcolor HEIGHT=1 COLSPAN=2></TD></TR>";
    print "</TABLE>";
  }

  print "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0><TR>";

  if ($login) {
    // 今日(HELP用)
    if (empty($y) || empty($m)) {
      $tempdate = getdate();
      $y = $tempdate["year"];
      $m = $tempdate["mon"];
    }
    print "<TD WIDTH=100% ALIGN=LEFT VALIGN=TOP BGCOLOR=\"$menu_backcolor\">";

    if ($login && $print != "on") {
      print "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>";
      print "<TR>";
      if (!empty($linktext)) {
        print "<TD BGCOLOR=$logout_backcolor HEIGHT=21><FONT COLOR=#FFFFFF>&nbsp;$linktext</TD>";
      }
      print "<FORM ACTION=\"$toppath/gogoogle.phtml\" TARGET=\"_blank\"><TD BGCOLOR=$logout_backcolor ALIGN=RIGHT HEIGHT=25>";
      print "<IMG SRC=\"$toppath/image/google.gif\" ALT=\"GoogleSearchEngine\" WIDTH=56 HEIGHT=21 ALIGN=ABSMIDDLE>";
      print "<INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"".$google_kwd."\" STYLE=\"width:117px\">";
       print "<INPUT TYPE=SUBMIT VALUE=\"Go!\">";
      print "&nbsp;</TD></FORM></TR>";
      print "</TABLE>";
    }

    print "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>";

    if ($print != "on") {
      print "<script language=\"JavaScript\">\n";
      print "<!--\n";
      print "function altclick(s) {\n";
      print "	if (window.event.altKey==true) {\n";
      print "		location.href=s;\n";
      print "		return false;\n";
      print "	}\n";
      print "}\n";
      print "//-->\n";
      print "</script>\n";
      
      print "<TR WIDTH=100%>";
      print "<TD ALIGN=LEFT VALIGN=TOP STYLE=\"font-size:1px\">";
      // 予定表
      print "<A HREF=\"$toppath/schedule/?y=".date("Y")."&m=".date("n")."&d=".date("j")."\" ONCLICK=\"return altclick('$toppath/schedule/add/')\"><IMG SRC=\"$toppath/image/btn_schedule_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$schedules_comment."\"></A> ";
      // 施設予約
      print "<A HREF=\"$toppath/room/?y=".date("Y")."&m=".date("n")."&d=".date("j")."\" ONCLICK=\"return altclick('$toppath/room/add/')\"><IMG SRC=\"$toppath/image/btn_room_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$rooms_comment."\"></A> ";
      // 伝言メモ
      print "<A HREF=\"$toppath/mail/\" ONCLICK=\"return altclick('$toppath/mail/add/')\"><IMG SRC=\"$toppath/image/btn_mail_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$mails_comment."\"></A> ";
      // 備忘録
      print "<A HREF=\"$toppath/todo/\" ONCLICK=\"return altclick('$toppath/todo/add/')\"><IMG SRC=\"$toppath/image/btn_todo_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALIGN=ABSMIDDLE ALT=\"".$todos_comment."\"></A> ";
      // BBS
      print "<A HREF=\"$toppath/bulletin/\" ONCLICK=\"return altclick('$toppath/bulletin/post/')\"><IMG SRC=\"$toppath/image/btn_bulletin_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$bulletins_comment."\"></A> ";
      // 回覧板
      print "<a href=\"$toppath/circular/\" ONCLICK=\"return altclick('$toppath/circular/regist/')\"><img src=\"$toppath/image/btn_circular_s.gif\" border=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$circulas_comment."\"></a> ";
      // ワークフロー
      print "<a href=\"$toppath/workflow/\" ONCLICK=\"return altclick('$toppath/workflow/regist/')\"><img src=\"$toppath/image/btn_workflow_s.gif\" border=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$workflow_comment."\"></a> ";
      // 住所録
			if ($address_flag<=3) {
	      print "<A HREF=\"$toppath/address/\" ONCLICK=\"return altclick('$toppath/address/regist/')\"><IMG SRC=\"$toppath/image/btn_address_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$address_comment."\"></A> ";
			}
      // リンク集
      print "<A HREF=\"$toppath/link/\" ONCLICK=\"return altclick('$toppath/link/add/?type=link')\"><IMG SRC=\"$toppath/image/btn_link_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$links_comment."\"></A> ";
      // Ｗｅｂフォルダ
      if (file_exists($basepath.$toppath."/folder")) {
	      print "<A HREF=\"$toppath/folder/\" ONCLICK=\"return altclick('$toppath/folder/upload/')\"><IMG SRC=\"$toppath/image/btn_folder_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$folders_comment."\"></A> ";
			}
      // メンバー
      print "<A HREF=\"$toppath/member/\" ONCLICK=\"return altclick('$toppath/member/?id=$login_id')\"><IMG SRC=\"$toppath/image/btn_member_s.gif\" BORDER=0 WIDTH=89 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"".$member_comment."\"></A> ";

      print "</TD><TD ALIGN=RIGHT VALIGN=TOP STYLE=\"font-size:4px\">";

      if ($admin_sign=="t") {
        print "<A HREF=\"$toppath/admin/\"><img src=\"$toppath/image/btn_admin.gif\" border=0 WIDTH=62 HEIGHT=21 ALIGN=ABSMIDDLE ALT=\"管理者設定へ\"></A> ";
      }
      print "<A HREF=\"$toppath/member/?id=$login_id\"><IMG SRC=\"$toppath/image/btn_profile.gif\" BORDER=0 ALIGN=ABSMIDDLE ALT=\"個人設定へ\"></A> ";
      print "<A HREF=\"$toppath/\"><IMG SRC=\"$toppath/image/btn_top.gif\" BORDER=0 ALIGN=ABSMIDDLE ALT=\"TopPage\"></A>";

      print "</TD>";
      print "</TR>";

    }
    print "</TABLE>\n";

    print "</TD></TR></TABLE>\n";

    print "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=0>\n";
    print "<TR><TD WIDTH=100% HEIGHT=464 VALIGN=TOP ALIGN=LEFT>";
  } else {
    print "<TD WIDTH=100% ALIGN=CENTER>";
  }

  print "<TABLE CELLPADDING=0 CELLSPACING=4 WIDTH=100%><TR><TD WIDTH=100%>";

  if (!$login) {
?>
<TABLE WIDTH=100% HEIGHT=460><TR><TD ALIGN=CENTER VALIGN=TOP>
<BR>
<?
    print "<FONT COLOR=#555555>[ Mode : ";
    if (getenv("SERVER_PORT")==80) {
      print "<B>http</B> | <A HREF=\"https://".$domain.$toppath."\">https(SSL)</A>";
    } else {
      print "<A HREF=\"http://".$domain.$toppath."\">http</A> | <B>https(SSL)</B>";
    }
    print " ]</FONT><BR>";

?>
<TABLE CELLPADDING=20><TR><TD ALIGN=CENTER>
<TABLE><FORM NAME="login" ACTION="<? echo $toppath; ?>/" METHOD="POST">
<INPUT TYPE=HIDDEN NAME="mode" VALUE="login">
<TR><TD>ログインID：</TD><TD><INPUT TYPE=TEXT MAXLENGTH=60 SIZE=26 NAME="userid" VALUE="<? echo $userid; ?>" STYLE="width:100px;ime-mode:disabled">&nbsp;</TD></TR>
<TR><TD>パスワード：</TD><TD><INPUT TYPE=PASSWORD MAXLENGTH=20 SIZE=15 NAME="pwd" STYLE="width:100px;ime-mode:disabled"></TD></TR></TABLE><BR>
<INPUT TYPE=SUBMIT VALUE="LOGIN" STYLE="width:100px">
</TD></TR></FORM></TABLE>
<?
    if (sizeof($headerr)>0) {
      print "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR><TD ALIGN=CENTER><FONT COLOR=#FF0000>\n";
      while(list($key,$val)=each($headerr)) print $val."<BR>";
      print "</FONT></TD></TR></TABLE>\n";
    }
?>
</TD></TR></TABLE>
</TD></TR></TABLE>
<?
  }
?>
