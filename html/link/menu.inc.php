<?php
  // ディレクトリツリー表示のコントロール用(SESSIONを利用)
  $pathon  = stripslashes(urldecode($_GET["pathon"]));
  $pathoff = stripslashes(urldecode($_GET["pathoff"]));

  if (!empty($pathon)) {
    $_SESSION[$pathon] = "on";
  } elseif (!empty($pathoff)) {
    $_SESSION[$pathoff] = "off";
  }
  if (!empty($linkpath) && empty($pathon) && empty($pathoff)) {
    if ($_SESSION["link".$linkpath] != "on") {
      $_SESSION["link".$linkpath] = "on";
    }
  }
  if (empty($_SESSION["/"])) {
    $_SESSION["/"] = "on";
  }

  $menutext .= "<TABLE><TR>\n";
  $menutext .= "<FORM ACTION=\"/link/search/\" METHOD=POST>\n";
  $menutext .= "<TD><IMG SRC=\"$toppath/image/search.gif\" WIDTH=16 HEIGHT=16 BORDER=0 ALT=\"検索\" ALIGN=ABSMIDDLE>";
  $menutext .= "<INPUT TYPE=TEXT NAME=\"kwd\" VALUE=\"$kwd\" SIZE=15 STYLE=\"width:98px\">";
  $menutext .= "<INPUT TYPE=SUBMIT VALUE=\"検索\" STYLE=\"width:36px\">";
  $menutext .= "</TD>\n";
  $menutext .= "</FORM>\n";
  $menutext .= "</TR></TABLE>\n";
  $menutext .= "<CENTER>
<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 WIDTH=160 BGCOLOR=#666666><TR><TD>
<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=158 BGCOLOR=#666666>
<TR><TD VALIGN=TOP BGCOLOR=#999999><FONT COLOR=#FFFFFF><A HREF=\"$toppath/link/\" STYLE=\"color:#FFFFFF\"><IMG SRC=\"$toppath/image/foldersadd.gif\" ALIGN=ABSMIDDLE BORDER=0 ALT=\"リンク\"> フォルダリスト</A>
</TD></TR>
<TR><TD STYLE=\"line-height:13px;font-size:8pt\" BGCOLOR=#FFFFFF ALIGN=LEFT>
";

// ディレクトリリストチェック
function check_tree($dirname) {
#echo $level.":".$dirname."<BR>";
  global $conn,$linkpath,$view_order;

  $sql = "SELECT type FROM links WHERE (path='".$dirname."' OR path='".$dirname."/')";
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  if ($cnt==0) {
    return "0/0";
  }
  // 獲得
  $dirs = 0;
  $files = 0;
  for ($i=0;$i<$cnt;$i++) { 
    $row = pg_fetch_array($res,$i);
    if ($row["type"]=="dir") {
      $dirs++;
    }
    if ($row["type"]=="link") {
      $files++;
    }
  }
  return $dirs."/".$files;
}

// ディレクトリリスト表示
function echo_tree($dirname,$level,$tree,$last) {
  global $conn,$groups,$login_id,$admin_sign;
  global $linkpath,$vieworder;
  $s = "";

  $sql = "SELECT * FROM links WHERE type='dir' AND (path='".$dirname."' OR path='".$dirname."/')";
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);

  for ($i=0;$i<$cnt;$i++) { 
    $row = pg_fetch_array($res,$i);
    if ($row["type"]=="dir") {

      $group_ids = $row["view_group_ids"];
      $user_ids  = $row["view_user_ids"];

      $view = false;
      if ($admin_sign=="t" || ($group_ids=="" && $user_ids=="")) {
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
        $dirs[$row["title"]] = $row["title"];
      }
    }
  }
  // ソート
  if (sizeof($dirs)>0) {
    asort($dirs);
    // 出力
    $c = 0;
    while (list($key,$val)=each($dirs)) { 
      $c++;
      if (substr($dirname,-1,1)!="/") {
        $subdirname = $dirname."/".$key;
      } else {
        $subdirname = $dirname.$key;
      }

      $ret = check_tree($subdirname);
      $rets = split("/",$ret);
      $dircount = $rets[0];
      $filecount = $rets[1];

      for ($i=0;$i<strlen($tree)-1;$i++) {
        if (substr($tree,$i,1)=="t") {
          $s .= "<IMG SRC=\"$toppath/image/icon/fol2.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
        } else {
          $s .= "<IMG SRC=\"$toppath/image/icon/folnull.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
        }
      }
      if (sizeof($dirs)==$c) {
#      if ($last==true) {
        $s .= "<IMG SRC=\"$toppath/image/icon/fol4.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
      } else {
        $s .= "<IMG SRC=\"$toppath/image/icon/fol3.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
      }

      if ($dircount>0) {
        if ($_SESSION[$subdirname]=="on") {
          $s .= "<A HREF=\"$toppath/link/?pathoff=".rawurlencode($subdirname)."\">";
          $s .= "<IMG SRC=\"$toppath/image/icon/folminus.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
        } else {
          $s .= "<A HREF=\"$toppath/link/?pathon=".rawurlencode($subdirname)."\">";
          $s .= "<IMG SRC=\"$toppath/image/icon/folplus.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
        }
        $s .= "</A>";
      } else {
#        $s .= "<IMG SRC=\"$toppath/image/icon/fol1.gif\" WIDTH=5 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
      }

      if ($filecount>0) {
        $dirshortname = mb_strcut($key,0,23-floor(($level+1)*2.5));
      } else {
        $dirshortname = mb_strcut($key,0,26-floor(($level+1)*2.5));
      }

      if ($linkpath != $subdirname && $linkpath != $subdirname."/") {
        $s .= "<A HREF=\"$toppath/link/?new_path=".urlencode($subdirname)."\">".$dirshortname."</A>";
      } else {
        $s .= "<B>".$dirshortname."</B>";
      }
      if ($filecount>0) {
        $s .= "(".$filecount.")";
      }

      $s .= "<BR>";

      if ($_SESSION[$subdirname]=="on") {
        if (sizeof($dirs)==$c) {
          $tree = substr($tree,0,strlen($tree)-1)."f";
        }
        if ($dircount>0) {
          if (sizeof($dirs)==$c) {
            $s .= echo_tree($subdirname,$level+1,$tree."t",true);
          } else {
            $s .= echo_tree($subdirname,$level+1,$tree."t",false);
          }
        } else {
          if (sizeof($dirs)==$c) {
            $s .= echo_tree($subdirname,$level+1,$tree."f",true);
          } else {
            $s .= echo_tree($subdirname,$level+1,$tree."f",false);
          }
        }
      }
    }
  }
  return $s;
}

  // メニュー内容の生成
  $ret = check_tree("/");
  $rets = split("/",$ret);
  $dircount = $rets[0];
  $filecount = $rets[1];

  if ($dircount>0) {
    if ($_SESSION["/"]=="on") {
      $menutext .= "<A HREF=\"$toppath/link/?pathoff=".rawurlencode("/")."\">";
      $menutext .= "<IMG SRC=\"$toppath/image/icon/folminus.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
    } else {
      $menutext .= "<A HREF=\"$toppath/link/?pathon=".rawurlencode("/")."\">";
      $menutext .= "<IMG SRC=\"$toppath/image/icon/folplus.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
    }
    $menutext .= "</A>";
  } else {
    $menutext .= "<IMG SRC=\"$toppath/image/icon/fol1.gif\" WIDTH=10 HEIGHT=13 ALIGN=ABSMIDDLE BORDER=0>";
  }
  if ($linkpath != "/") {
    $menutext .= "<A HREF=\"$toppath/link/?new_path=/\">/ [ルートフォルダ]</A>";
  } else {
    $menutext .= "<B>/</B> [ルートフォルダ]";
  }

  if ($filecount>0) {
    $menutext .= "(".$filecount.")";
  }

  $menutext .= "<BR>";
  if ($_SESSION["/"]=="on") {
    if ($dircount>0) {
      $menutext .= echo_tree("/",0,"t",true);
    } else {
      $menutext .= echo_tree("/",0,"f",true);
    }
  }

  $menutext .= "
</TD></TR></TABLE>
</TD></TR></TABLE>
<BR>";
#  $checkpath = $linkpath;
#  if ($checkpath=="") { $checkpath = "/"; }
#  $count_dir = substr_count($checkpath,"/");
#  $menutext .= "<A HREF=\"$toppath/link/add/?type=link&path=".urlencode($checkpath)."\">";
#  $menutext .= "<IMG SRC=\"$toppath/image/linksadd.gif\" ALIGN=ABSMIDDLE BORDER=0 ALT=\"リンク作成\"> リンク作成";
#  $menutext .= "</A><BR><BR>\n";
#  if ($count_dir<4) {
#    $menutext .= "<A HREF=\"$toppath/link/add/?type=dir&path=".urlencode($checkpath)."\">";
#    $menutext .= "<IMG SRC=\"$toppath/image/foldersadd.gif\" ALIGN=ABSMIDDLE BORDER=0 ALT=\"フォルダ作成\"> フォルダ作成";
#    $menutext .= "</A><BR>\n";
#  }
#  $menutext .= "</CENTER><BR>\n";
?>