<?
  // ¥·¥¹¥Æ¥àÌ¾
  $system_shortname = "PenguinOffice"; // ¢« ¥á¡¼¥ëÍÑ
  $system_name  = "PenguinOffice Ver2.0.1";

  // ¥µ¥Ö¥É¥á¥¤¥óÌ¾¤ò³ÍÆÀ(ASPÄó¶¡»þÍÑ)
  $envHostName = getenv("HTTP_HOST");
  unset($subdomain);
  if (eregi("\.gw\.tech-arts\.co\.jp($|:)",$envHostName)) {
    $envHostNames = split("\.",$envHostName);
    $subdomain = $envHostNames[0];
  }

  // ÀßÃÖ¥Ñ¥¹
  $basedir = "/usr/local/penguinoffice";
  if (isset($subdomain))
      $basedir .= "/$subdomain";
  $basepath = $basedir."/html";

  // ¥É¥á¥¤¥óÌ¾
  $domain = "gw.tech-arts.co.jp";
  if (isset($subdomain))
      $domain = $subdomain.'.'.$domain;

  // DB´ØÏ¢
  $server    = "localhost";
  $port      = "5432"; 
  $user      = "postgres";
  $pg_passwd = "postgres";
  $db        = "penguinoffice";
  if (isset($subdomain))
      $db        .= "_$subdomain";

  $network   = "internet";

  $pgsql_connect = "";
  if ($server<>"")    { $pgsql_connect .= "host=".$server." "; }
  if ($port<>"")      { $pgsql_connect .= "port=".$port." "; }
  if ($user<>"")      { $pgsql_connect .= "user=".$user." "; }
  if ($pg_passwd<>"") { $pgsql_connect .= "password=".$pg_passwd." "; }
  if ($db<>"")        { $pgsql_connect .= "dbname=".$db." "; }

  if (!$conn) {
    $conn = pg_connect($pgsql_connect);
    pg_query($conn,"SET DATESTYLE TO 'ISO'");
  } else {
    pg_query($conn,"SET DATESTYLE TO 'ISO'");
  }

  $sql_admin = "SELECT * FROM admin";
  $res_admin = pg_query($conn,$sql_admin);
  if (pg_num_rows($res_admin)>0){
    $row_admin    = pg_fetch_array($res_admin,0);
    $service_name = $row_admin["service_name"];
    if ($service_name == "") $service_name = "Ì¤ÀßÄê";
    $homeurl      = $row_admin["url"];
    $logoutsecond = $row_admin["logoutsecond"];
    $interval     = $row_admin["interval"];
    $restday[0]   = $row_admin["sunday"];
    $restday[1]   = $row_admin["monday"];
    $restday[2]   = $row_admin["tuesday"];
    $restday[3]   = $row_admin["wednesday"];
    $restday[4]   = $row_admin["thursday"];
    $restday[5]   = $row_admin["friday"];
    $restday[6]   = $row_admin["saturday"];
  } else {
    $homeurl      = 0;
    $service_name = "Ì¤ÀßÄê";
    $logoutsecond = 3600;
    $interval     = 5;
    $restday[0]   = "t";
    $restday[1]   = "f";
    $restday[2]   = "f";
    $restday[3]   = "f";
    $restday[4]   = "f";
    $restday[5]   = "f";
    $restday[6]   = "t";
  }

  // ¥í¥°¤ÎÍ­¸ú´ü¸Â
  $logday = 90;

  // ¥Ú¡¼¥¸Éý
  $tablewidth = 740;

  // ¶¦ÍÑ¥Õ¥©¥ë¥À¤Î³ÊÇ¼¥Ç¥£¥ì¥¯¥È¥ê(Í×777)
  $folderpath = $basedir."/folder";
  $folderpath_web = "./";
  $path_level = 5; // ºîÀ®²ÄÇ½¤Ê³¬ÁØ¥ì¥Ù¥ë

  $circularspath = $basedir."/circular";
  $workflowpath  = $basedir."/workflow";

  $foldermembers = "/¥á¥ó¥Ð¡¼¥Ç¥£¥ì¥¯¥È¥ê";

  // WWW¥µ¡¼¥Ð¡¼Æ°ºî¥É¥á¥¤¥ó¤Þ¤¿¤ÏIP¥¢¥É¥ì¥¹(CookieÍÑ)
  if (getenv("SERVER_PORT")==80) {
    $access = "http://";
  } else {
    $access = "https://";
  }

  //¥¯¥Ã¥­¡¼Ì¾
  $cookiename = "penguinoffice2:login";

  //¥È¥Ã¥×¥Ú¡¼¥¸¤Ø¤Î¥Ñ¥¹
  $toppath = "";

  // ´ÉÍý¼Ô¥á¡¼¥ë¥¢¥É¥ì¥¹
  $webmaster = "it@tech-arts.co.jp"; 

	// ³°Éô¥³¥Þ¥ó¥É¥Õ¥ë¥Ñ¥¹»ØÄê(Win´Ä¶­¤Ø¤Î°Ü¹Ô»þ¤Ë¤ÏÂåÂØ¤¬É¬Í×¤È¤Ê¤ê¤Þ¤¹)
	$cmd_du = "/usr/bin/du";
	
  // ¶¦ÄÌ HTMLÀßÄê¡õ²èÌÌÇÛ¿§¥Ñ¥¿¡¼¥ó
  $menuobj_style="font-size:8.5pt";

  // ²ñµÄ¼¼¤ÎÉ½¼¨¹Ô¿ô
  $board_rows_posted = 20; # 1¥Ú¡¼¥¸¤ÎÉ½¼¨È¯¸À¿ô
  $board_rows_thread = 20; # 1¥Ú¡¼¥¸¤ÎÉ½¼¨¥¹¥ì¥Ã¥É¿ô

  // ½»½êÏ¿¤ÎÉ½¼¨¹Ô¿ô
  $address_rows_corp   = 15; # 1¥Ú¡¼¥¸¤ÎÉ½¼¨È¯¸À¿ô
  $address_rows_people = 15; # 1¥Ú¡¼¥¸¤ÎÉ½¼¨È¯¸À¿ô

  // ²óÍ÷ÈÄ¤Î±ÜÍ÷¹Ô¿ô
  $circular_rows = 20;

  // ¥ï¡¼¥¯¥Õ¥í¡¼¤Î±ÜÍ÷¹Ô¿ô
  $workflow_rows = 20;

  // ¥¢¥Ã¥×¥í¡¼¥É¥Õ¥¡¥¤¥ë¤ÎºÇÂç¥µ¥¤¥º
  $upload_max = 204800000;

  // ·ÈÂÓÈÇ¤Î³Æ¼ïÀßÄê
  $mobile_rows = 9; // ´ðËÜÃÍ
  $mobile_bbsrows = 9;
  $mobile_bbspostrows = 9;

  // ¥á¥Ë¥å¡¼²èÁü¤È¥Æ¥­¥¹¥È
  $schedules_text = "Í½ÄêÉ½";
  $bulletins_text = "£Â£Â£Ó";
  $mails_text     = "ÅÁ¸À¥á¥â";
  $rooms_text     = "»ÜÀßÍ½Ìó";
  $circulas_text  = "²óÍ÷ÈÄ";
  $todos_text     = "È÷ËºÏ¿";
  $folders_text   = "¶¦Í­¥Õ¥©¥ë¥À";
  $links_text     = "¥ê¥ó¥¯½¸";
  $address_text   = "½»½êÏ¿";
  $chats_text     = "¥Á¥ã¥Ã¥È";
  $workflow_text  = "¥ï¡¼¥¯¥Õ¥í¡¼";

  $schedules_subtext = "schedules";
  $bulletins_subtext = "bulletins";
  $mails_subtext     = "mails";
  $rooms_subtext     = "rooms";
  $circulas_subtext  = "circulas";
  $todos_subtext     = "todos";
  $folders_subtext   = "folders";
  $links_subtext     = "links";
  $address_subtext   = "address";
  $chats_subtext     = "chat";
  $workflow_subtext  = "workflow";

  $schedules_comment = "¸Ä¿Í¤ä½êÂ°¥°¥ë¡¼¥×¤Î¥¹¥±¥¸¥å¡¼¥ë¤¬³ÎÇ§¤Ç¤­¤Þ¤¹";
  $bulletins_comment = "Æ¤ÏÀ¤¹¤ë¤¿¤á¤Î£Â£Â£Ó¤Ç¤¹";
  $mails_comment     = "¸Ä¿Í¤ä¥°¥ë¡¼¥×°¸¤Ë¥á¥Ã¥»¡¼¥¸¤òÁ÷¿®¤¹¤ë¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $rooms_comment     = "²ñµÄ¼¼¤ä»ÜÀß¤ÎÍ½Ìó¤ò¹Ô¤¦¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $todos_comment     = "Í¥Àè½ç°Ì¤ò¤Ä¤±¤Æ¥á¥â¤òÅÐÏ¿¤·¤Æ¤ª¤¯¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $circulas_comment  = "²óÍ÷ÈÄ¤Î¤è¤¦¤ËÊ£¿ô¤ÎÁê¼êÀè¤Ë¾ðÊó¤òÇÛ¿®¤¹¤ë¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $folders_comment   = "¥Ñ¥½¥³¥ó¾å¤Î¥Õ¥¡¥¤¥ë¤ò¥¢¥Ã¥×¥í¡¼¥É¤·¤ÆÁ´°÷¤Ç¶¦Í­¤¹¤ë¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $links_comment     = "¤è¤¯»È¤¦¥ê¥ó¥¯¤òÅÐÏ¿¤·¤Æ¤ª¤¯¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $address_comment   = "¼è°úÀè¤äÍ§¿Í¡¦ÃÎ¿Í¤Î½»½ê¤òÅÐÏ¿¤·¤Æ¤ª¤¯¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $chats_comment     = "¥¤¥ó¥¿¡¼¥Í¥Ã¥È¤Î¤É¤³¤«¤é¤Ç¤â¥Á¥ã¥Ã¥È¤ò¤¹¤ë¤³¤È¤¬¤Ç¤­¤Þ¤¹";
  $workflow_comment  = "¼ÒÆâãÈµÄ¤ò±ß³ê¤Ë¤¹¤¹¤á¤ë¤³¤È¤¬¤Ç¤­¤Þ¤¹";

  $birthday_comment = "¤µ¤ó¤ÎÃÂÀ¸Æü¤Ç¤¹";

  $border64 = 0;
  $border32 = 0;

  $title_backcolor  = "#005599";
  $title_forecolor1 = "#330066"; // ÍËÆü¿§¤ò»È¤¦¤¿¤áÌµ¸ú
  $title_forecolor2 = "#FFFFFF";
  $title_forecolor3 = "#FFFFFF";

  $logout_backcolor = "#003366";
  $logout_forecolor = "#FFFFFF";

  $bottom_backcolor = "#006699";
  $bottom_forecolor = "#FFFFFF";

  $menu_backcolor   = "#BBCCEE";
  $menu_forecolor   = "#000000";

  $indexmenu_backcolor   = "#99CCFF";
  $indexmenu_forecolor   = "#FFFFFF";

  $bodyForeColor   = "#333366";
  $bodyLinkColor   = "#0033FF";
  $bodyVLinkColor  = "#0033FF";
  $bodyALinkColor  = "#0033FF";
  $bodyHLinkColor  = "#FF0000"; // Hover
  $bodyBackColor   = "#F4F4FF";
//  $bodyImage       = "";

  $td_back      = "#FFFFFF";
  $td_back_left = "#DDDDFF";

  $bg_dark      = "#DDDDFF";
  $bg_light     = "#EEEEFF";

  $sch_back     = "#6699FF";

  // ¥Ø¥Ã¥ÀÍÑ¥Æ¥­¥¹¥È¤Î¥¯¥ê¥¢
  $headertext  = "Content-Type: text/html; charset=EUC-JP\n";
  $headertext .= "Expires: Mon, 26 Jul 1997 05:00:00 GMT\n";
  $headertext .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
  $headertext .= "Cache-Control: no-cache, must-revalidate, max_age=0\n";
  $headertext .= "Pragma: no-cache\n";

  // ÍøÍÑ¼ÔÍÑ HTMLÀßÄê¡õ²èÌÌÇÛ¿§¥Ñ¥¿¡¼¥ó
  if (!empty($_GET["fontsize"]) && is_numeric($_GET["fontsize"])) {
    $fontsize = $_GET["fontsize"];
    $headertext .= "Set-Cookie: fontsize=".$fontsize."; path=/; expires=".date("l, d-M-Y H:i:s T",time()+15552000).";\n";
  } else {
    if (!empty($_COOKIE["fontsize"]) && is_numeric($_COOKIE["fontsize"])) {
      $fontsize = $_COOKIE["fontsize"];
    } else {
      $fontsize = 9;
    }
  }
  $css  = "BODY,TABLE { \n";
  $css .= "FONT-SIZE:".$fontsize."pt; FONT-FAMILY: Verdana, sans-serif, '£Í£Ó £Ð¥´¥·¥Ã¥¯', 'Osaka';\n";
  $css .= "SCROLLBAR-FACE-COLOR: #DDDDDD;\n";
  $css .= "SCROLLBAR-HIGHLIGHT-COLOR:#FFFFFF;\n";
  $css .= "OVERFLOW: AUTO;\n";
  $css .= "SCROLLBAR-SHADOW-COLOR: #777777;\n";
  $css .= "SCROLLBAR-3DLIGHT-COLOR: #777777;\n";
  $css .= "SCROLLBAR-ARROW-COLOR: #777777;\n";
  $css .= "SCROLLBAR-TRACK-COLOR: #CCCCCC;\n";
  $css .= "SCROLLBAR-DARKSHADOW-COLOR: #CCCCCC\n";
  $css .= " }\n";
#  $css .= "A:link     { text-decoration:none; font-weight:bold; color:".$bodyLinkColor."; }\n";
#  $css .= "A:active   { text-decoration:none; font-weight:bold; color:".$bodyALinkColor."; }\n";
#  $css .= "A:visited  { text-decoration:none; font-weight:bold; color:".$bodyVLinkColor."; }\n";
#  $css .= "A:hover    { text-decoration:underline; font-weight:bold; color:".$bodyHLinkColor."; }\n";
  $css .= "A:link     { text-decoration:none; font-weight:normal; color:".$bodyLinkColor."; }\n";
  $css .= "A:active   { text-decoration:none; font-weight:normal; color:".$bodyALinkColor."; }\n";
  $css .= "A:visited  { text-decoration:none; font-weight:normal; color:".$bodyVLinkColor."; }\n";
  $css .= "A:hover    { text-decoration:underline; font-weight:normal; color:".$bodyHLinkColor."; }\n";
  $css .= ".BAR { font-size: ".$fontsize."pt; color:#FFFFFF }\n";
  $css .= "A.BAR:link     { text-decoration:none; font-weight:normal; color:#FFFFFF; }\n";
  $css .= "A.BAR:active   { text-decoration:none; font-weight:normal; color:#FFFFFF; }\n";
  $css .= "A.BAR:visited  { text-decoration:none; font-weight:normal; color:#FFFFFF; }\n";
  $css .= "A.BAR:hover    { text-decoration:none; font-weight:normal; color:#FFFF00; }\n";
  $css .= ".TEXT { font-size: ".$fontsize."pt; line-height:16px; padding:5px }\n";
  $css .= ".CENTER { text-align:center }\n";
  $css .= ".HIGHLIGHT { background-color: #FFFF00; }\n";

  // ÅÁ¸À¥á¥âÍÑ Í¥Àè½ç°ÌÇÛÎó
  $priority_name  = array("¹â",     "¤ä¤ä¹â", "ÉáÄÌ",   "¤ä¤äÄã", "Äã");
  $priority_color = array("#FF3333","#FF6633","#FFFF66","#33FF66","#6666FF");

  // ¤½¤ÎÂ¾ÀßÄê
  $pagerows = 10; //¸¡º÷¥Ú¡¼¥¸¤Î¹Ô¿ô

  // ÈÆÍÑÇÛÎó
  $COUNTRY_ARR = array("ÆüËÜ"); // ¹ñÌ¾

  // ÅÔÆ»ÉÜ¸©
  $STATE_ARR = array("ËÌ³¤Æ»","ÀÄ¿¹¸©","´ä¼ê¸©","µÜ¾ë¸©","½©ÅÄ¸©","»³·Á¸©","Ê¡Åç¸©",
                     "¿·³ã¸©","ÉÙ»³¸©","ÀÐÀî¸©","Ê¡°æ¸©",
                     "»³Íü¸©","Ä¹Ìî¸©","´ôÉì¸©",
                     "ÅìµþÅÔ","¿ÀÆàÀî¸©","ÀéÍÕ¸©","ºë¶Ì¸©","·²ÇÏ¸©","ÆÊÌÚ¸©","°ñ¾ë¸©",
                     "ÀÅ²¬¸©","°¦ÃÎ¸©","»°½Å¸©",
                     "ÂçºåÉÜ","µþÅÔÉÜ","Ê¼¸Ë¸©","ÆàÎÉ¸©","¼¢²ì¸©","ÏÂ²Î»³¸©",
                     "Ä»¼è¸©","Åçº¬¸©","²¬»³¸©","¹­Åç¸©","»³¸ý¸©",
                     "ÆÁÅç¸©","¹áÀî¸©","°¦É²¸©","¹âÃÎ¸©",
                     "Ê¡²¬¸©","º´²ì¸©","Ä¹ºê¸©","·§ËÜ¸©","ÂçÊ¬¸©","µÜºê¸©","¼¯»ùÅç¸©",
                     "²­Æì¸©");

  $STATE_ARR_WEATHER = array("ËÌ³¤Æ»(Æ»ËÌ)","ËÌ³¤Æ»(Æ»Åì)","ËÌ³¤Æ»(Æ»±û)","ËÌ³¤Æ»(Æ»Æî)",
                     "ÀÄ¿¹¸©","´ä¼ê¸©","µÜ¾ë¸©","½©ÅÄ¸©","»³·Á¸©","Ê¡Åç¸©",
                     "¿·³ã¸©","ÉÙ»³¸©","ÀÐÀî¸©","Ê¡°æ¸©",
                     "»³Íü¸©","Ä¹Ìî¸©","´ôÉì¸©",
                     "ÅìµþÅÔ","¿ÀÆàÀî¸©","ÀéÍÕ¸©","ºë¶Ì¸©","·²ÇÏ¸©","ÆÊÌÚ¸©","°ñ¾ë¸©",
                     "ÀÅ²¬¸©","°¦ÃÎ¸©","»°½Å¸©",
                     "ÂçºåÉÜ","µþÅÔÉÜ","Ê¼¸Ë¸©","ÆàÎÉ¸©","¼¢²ì¸©","ÏÂ²Î»³¸©",
                     "Ä»¼è¸©","Åçº¬¸©","²¬»³¸©","¹­Åç¸©","»³¸ý¸©",
                     "ÆÁÅç¸©","¹áÀî¸©","°¦É²¸©","¹âÃÎ¸©",
                     "Ê¡²¬¸©","º´²ì¸©","Ä¹ºê¸©","·§ËÜ¸©","ÂçÊ¬¸©","µÜºê¸©","¼¯»ùÅç¸©",
                     "²­Æì¸©");

  // ÅÔÆ»ÉÜ¸©¥¨¥ê¥¢
  $STATE_ARR_A = array("ËÌ³¤Æ»","ÅìËÌ","ËÌÎ¦","ÃæÉô","´ØÅì","Åì³¤","´ØÀ¾","Ãæ¹ñ","»Í¹ñ","¶å½£","²­Æì");

  // ÅÔÆ»ÉÜ¸©¥¨¥ê¥¢ÂÐ±þÉ½
  $STATE_ARR_B = array("ËÌ³¤Æ»",
                       "ÀÄ¿¹¸©\t´ä¼ê¸©\tµÜ¾ë¸©\t½©ÅÄ¸©\t»³·Á¸©\tÊ¡Åç¸©",
                       "¿·³ã¸©\tÉÙ»³¸©\tÀÐÀî¸©\tÊ¡°æ¸©",
                       "»³Íü¸©\tÄ¹Ìî¸©\t´ôÉì¸©",
                       "ÅìµþÅÔ\t¿ÀÆàÀî¸©\tÀéÍÕ¸©\tºë¶Ì¸©\t·²ÇÏ¸©\tÆÊÌÚ¸©\t°ñ¾ë¸©",
                       "ÀÅ²¬¸©\t°¦ÃÎ¸©\t»°½Å¸©",
                       "ÂçºåÉÜ\tµþÅÔÉÜ\tÊ¼¸Ë¸©\tÆàÎÉ¸©\t¼¢²ì¸©\tÏÂ²Î»³¸©",
                       "Ä»¼è¸©\tÅçº¬¸©\t²¬»³¸©\t¹­Åç¸©\t»³¸ý¸©",
                       "ÆÁÅç¸©\t¹áÀî¸©\t°¦É²¸©\t¹âÃÎ¸©",
                       "Ê¡²¬¸©\tº´²ì¸©\tÄ¹ºê¸©\t·§ËÜ¸©\tÂçÊ¬¸©\tµÜºê¸©\t¼¯»ùÅç¸©",
                       "²­Æì¸©");

  // ÍËÆüÇÛÎó(1:·î,2:²Ð,3:¿å,4:ÌÚ,5:¶â,6:ÅÚ,0:Æü,7:º×Æü)
  $wname                = array("Æü",     "·î",     "²Ð",     "¿å",     "ÌÚ",     "¶â",     "ÅÚ"               );
  $wcolor_titleback     = array("#FFCCCC","#DADADA","#DADADA","#DADADA","#DADADA","#DADADA","#CCCCFF","#FFDDCC");
  $wcolor_title         = array("#FF0000","#000000","#000000","#000000","#000000","#000000","#0000FF","#FF6633");
  $wcolor_back          = array("#FFDADA","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#DADAFF","#FFDADA");
  $wcolor_back_startmon = array("#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#DADAFF","#FFDADA","#FFDADA");
  $wcolor               = array("#FF3333","#000066","#000066","#000066","#000066","#000066","#3333FF","#FF0000");
  $wcolor_startmon      = array("#000066","#000066","#000066","#000066","#000066","#3333FF","#FF0000","#FF3333");

  $alarm_interval       = array(5,10,15,20,30,40,50,60,90,120,180);
  $alarm_interval_name  = array("5Ê¬Á°","10Ê¬Á°","15Ê¬Á°","20Ê¬Á°","30Ê¬Á°","40Ê¬Á°","50Ê¬Á°","1»þ´ÖÁ°","1»þ´ÖÈ¾Á°","2»þ´ÖÁ°","3»þ´ÖÁ°");

  // Æü¿ôÉ½(³Æ·î¤Î·îËö¤ÎÆü¿ô¤ÈÇ¯´Ö¤ÎÆü¿ô)
  $day_365 = array(31,28,31,30,31,30,31,31,30,31,30,31,365);
  $day_366 = array(31,29,31,30,31,30,31,31,30,31,30,31,366);
?>
