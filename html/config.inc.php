<?
  // �����ƥ�̾
  $system_shortname = "PenguinOffice"; // �� �᡼����
  $system_name  = "PenguinOffice Ver2.0.1";

  // ���֥ɥᥤ��̾�����(ASP�󶡻���)
  $envHostName = getenv("HTTP_HOST");
  unset($subdomain);
  if (eregi("\.gw\.tech-arts\.co\.jp($|:)",$envHostName)) {
    $envHostNames = split("\.",$envHostName);
    $subdomain = $envHostNames[0];
  }

  // ���֥ѥ�
  $basedir = "/usr/local/penguinoffice";
  if (isset($subdomain))
      $basedir .= "/$subdomain";
  $basepath = $basedir."/html";

  // �ɥᥤ��̾
  $domain = "gw.tech-arts.co.jp";
  if (isset($subdomain))
      $domain = $subdomain.'.'.$domain;

  // DB��Ϣ
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
    if ($service_name == "") $service_name = "̤����";
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
    $service_name = "̤����";
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

  // ����ͭ������
  $logday = 90;

  // �ڡ�����
  $tablewidth = 740;

  // ���ѥե�����γ�Ǽ�ǥ��쥯�ȥ�(��777)
  $folderpath = $basedir."/folder";
  $folderpath_web = "./";
  $path_level = 5; // ������ǽ�ʳ��إ�٥�

  $circularspath = $basedir."/circular";
  $workflowpath  = $basedir."/workflow";

  $foldermembers = "/���С��ǥ��쥯�ȥ�";

  // WWW�����С�ư��ɥᥤ��ޤ���IP���ɥ쥹(Cookie��)
  if (getenv("SERVER_PORT")==80) {
    $access = "http://";
  } else {
    $access = "https://";
  }

  //���å���̾
  $cookiename = "penguinoffice2:login";

  //�ȥåץڡ����ؤΥѥ�
  $toppath = "";

  // �����ԥ᡼�륢�ɥ쥹
  $webmaster = "it@tech-arts.co.jp"; 

	// �������ޥ�ɥե�ѥ�����(Win�Ķ��ؤΰܹԻ��ˤ����ؤ�ɬ�פȤʤ�ޤ�)
	$cmd_du = "/usr/bin/du";
	
  // ���� HTML����������ۿ��ѥ�����
  $menuobj_style="font-size:8.5pt";

  // ��ļ���ɽ���Կ�
  $board_rows_posted = 20; # 1�ڡ�����ɽ��ȯ����
  $board_rows_thread = 20; # 1�ڡ�����ɽ������åɿ�

  // ����Ͽ��ɽ���Կ�
  $address_rows_corp   = 15; # 1�ڡ�����ɽ��ȯ����
  $address_rows_people = 15; # 1�ڡ�����ɽ��ȯ����

  // �����Ĥα����Կ�
  $circular_rows = 20;

  // ����ե��α����Կ�
  $workflow_rows = 20;

  // ���åץ��ɥե�����κ��祵����
  $upload_max = 204800000;

  // �����ǤγƼ�����
  $mobile_rows = 9; // ������
  $mobile_bbsrows = 9;
  $mobile_bbspostrows = 9;

  // ��˥塼�����ȥƥ�����
  $schedules_text = "ͽ��ɽ";
  $bulletins_text = "�££�";
  $mails_text     = "�������";
  $rooms_text     = "����ͽ��";
  $circulas_text  = "������";
  $todos_text     = "��˺Ͽ";
  $folders_text   = "��ͭ�ե����";
  $links_text     = "��󥯽�";
  $address_text   = "����Ͽ";
  $chats_text     = "����å�";
  $workflow_text  = "����ե�";

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

  $schedules_comment = "�Ŀͤ��°���롼�פΥ������塼�뤬��ǧ�Ǥ��ޤ�";
  $bulletins_comment = "Ƥ�����뤿��Σ££ӤǤ�";
  $mails_comment     = "�Ŀͤ䥰�롼�װ��˥�å��������������뤳�Ȥ��Ǥ��ޤ�";
  $rooms_comment     = "��ļ�����ߤ�ͽ���Ԥ����Ȥ��Ǥ��ޤ�";
  $todos_comment     = "ͥ���̤�Ĥ��ƥ�����Ͽ���Ƥ������Ȥ��Ǥ��ޤ�";
  $circulas_comment  = "�����ĤΤ褦��ʣ���������˾�����ۿ����뤳�Ȥ��Ǥ��ޤ�";
  $folders_comment   = "�ѥ������Υե�����򥢥åץ��ɤ��������Ƕ�ͭ���뤳�Ȥ��Ǥ��ޤ�";
  $links_comment     = "�褯�Ȥ���󥯤���Ͽ���Ƥ������Ȥ��Ǥ��ޤ�";
  $address_comment   = "������ͧ�͡��οͤν������Ͽ���Ƥ������Ȥ��Ǥ��ޤ�";
  $chats_comment     = "���󥿡��ͥåȤΤɤ�����Ǥ����åȤ򤹤뤳�Ȥ��Ǥ��ޤ�";
  $workflow_comment  = "�����ȵĤ�߳�ˤ�����뤳�Ȥ��Ǥ��ޤ�";

  $birthday_comment = "������������Ǥ�";

  $border64 = 0;
  $border32 = 0;

  $title_backcolor  = "#005599";
  $title_forecolor1 = "#330066"; // ��������Ȥ�����̵��
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

  // �إå��ѥƥ����ȤΥ��ꥢ
  $headertext  = "Content-Type: text/html; charset=EUC-JP\n";
  $headertext .= "Expires: Mon, 26 Jul 1997 05:00:00 GMT\n";
  $headertext .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
  $headertext .= "Cache-Control: no-cache, must-revalidate, max_age=0\n";
  $headertext .= "Pragma: no-cache\n";

  // ���Ѽ��� HTML����������ۿ��ѥ�����
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
  $css .= "FONT-SIZE:".$fontsize."pt; FONT-FAMILY: Verdana, sans-serif, '�ͣ� �Х����å�', 'Osaka';\n";
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

  // ��������� ͥ��������
  $priority_name  = array("��",     "����", "����",   "�����", "��");
  $priority_color = array("#FF3333","#FF6633","#FFFF66","#33FF66","#6666FF");

  // ����¾����
  $pagerows = 10; //�����ڡ����ιԿ�

  // ��������
  $COUNTRY_ARR = array("����"); // ��̾

  // ��ƻ�ܸ�
  $STATE_ARR = array("�̳�ƻ","�Ŀ���","��긩","�ܾ븩","���ĸ�","������","ʡ�縩",
                     "���㸩","�ٻ���","���","ʡ�温",
                     "������","Ĺ�","���츩",
                     "�����","�����","���ո�","��̸�","���ϸ�","���ڸ�","��븩",
                     "�Ų���","���θ�","���Ÿ�",
                     "�����","������","ʼ�˸�","���ɸ�","���츩","�²λ���",
                     "Ļ�踩","�纬��","������","���縩","������",
                     "���縩","���","��ɲ��","���θ�",
                     "ʡ����","���츩","Ĺ�긩","���ܸ�","��ʬ��","�ܺ긩","�����縩",
                     "���츩");

  $STATE_ARR_WEATHER = array("�̳�ƻ(ƻ��)","�̳�ƻ(ƻ��)","�̳�ƻ(ƻ��)","�̳�ƻ(ƻ��)",
                     "�Ŀ���","��긩","�ܾ븩","���ĸ�","������","ʡ�縩",
                     "���㸩","�ٻ���","���","ʡ�温",
                     "������","Ĺ�","���츩",
                     "�����","�����","���ո�","��̸�","���ϸ�","���ڸ�","��븩",
                     "�Ų���","���θ�","���Ÿ�",
                     "�����","������","ʼ�˸�","���ɸ�","���츩","�²λ���",
                     "Ļ�踩","�纬��","������","���縩","������",
                     "���縩","���","��ɲ��","���θ�",
                     "ʡ����","���츩","Ĺ�긩","���ܸ�","��ʬ��","�ܺ긩","�����縩",
                     "���츩");

  // ��ƻ�ܸ����ꥢ
  $STATE_ARR_A = array("�̳�ƻ","����","��Φ","����","����","�쳤","����","���","�͹�","�彣","����");

  // ��ƻ�ܸ����ꥢ�б�ɽ
  $STATE_ARR_B = array("�̳�ƻ",
                       "�Ŀ���\t��긩\t�ܾ븩\t���ĸ�\t������\tʡ�縩",
                       "���㸩\t�ٻ���\t���\tʡ�温",
                       "������\tĹ�\t���츩",
                       "�����\t�����\t���ո�\t��̸�\t���ϸ�\t���ڸ�\t��븩",
                       "�Ų���\t���θ�\t���Ÿ�",
                       "�����\t������\tʼ�˸�\t���ɸ�\t���츩\t�²λ���",
                       "Ļ�踩\t�纬��\t������\t���縩\t������",
                       "���縩\t���\t��ɲ��\t���θ�",
                       "ʡ����\t���츩\tĹ�긩\t���ܸ�\t��ʬ��\t�ܺ긩\t�����縩",
                       "���츩");

  // ��������(1:��,2:��,3:��,4:��,5:��,6:��,0:��,7:����)
  $wname                = array("��",     "��",     "��",     "��",     "��",     "��",     "��"               );
  $wcolor_titleback     = array("#FFCCCC","#DADADA","#DADADA","#DADADA","#DADADA","#DADADA","#CCCCFF","#FFDDCC");
  $wcolor_title         = array("#FF0000","#000000","#000000","#000000","#000000","#000000","#0000FF","#FF6633");
  $wcolor_back          = array("#FFDADA","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#DADAFF","#FFDADA");
  $wcolor_back_startmon = array("#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#FCFCFC","#DADAFF","#FFDADA","#FFDADA");
  $wcolor               = array("#FF3333","#000066","#000066","#000066","#000066","#000066","#3333FF","#FF0000");
  $wcolor_startmon      = array("#000066","#000066","#000066","#000066","#000066","#3333FF","#FF0000","#FF3333");

  $alarm_interval       = array(5,10,15,20,30,40,50,60,90,120,180);
  $alarm_interval_name  = array("5ʬ��","10ʬ��","15ʬ��","20ʬ��","30ʬ��","40ʬ��","50ʬ��","1������","1����Ⱦ��","2������","3������");

  // ����ɽ(�Ʒ�η�����������ǯ�֤�����)
  $day_365 = array(31,28,31,30,31,30,31,31,30,31,30,31,365);
  $day_366 = array(31,29,31,30,31,30,31,31,30,31,30,31,366);
?>
