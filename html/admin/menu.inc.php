<?
  $menutext .= "<A HREF=\"$toppath/admin/setting/\">����������</A><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<A HREF=\"$toppath/admin/holiday/\">��������</A><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/groups/\">���롼������</a><BR>\n";
  $menutext .= "<a href=\"$toppath/admin/users/\">�桼��������</a><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/locations/\">�Ԥ���̾������</a><BR>\n";
  $menutext .= "<BR>\n";
	if (file_exists($basepath.$toppath."/schedule")) {
	  $menutext .= "<a href=\"$toppath/admin/schedule_kind/\">ͽ��ɽ��ʬ����</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/room")) {
	  $menutext .= "<a href=\"$toppath/admin/rooms_type/\">���߶�ʬ����</a><BR>\n";
	  $menutext .= "<a href=\"$toppath/admin/rooms/\">��������</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/bulletin")) {
	  $menutext .= "<a href=\"$toppath/admin/boards/\">BBS�롼������</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/address")) {
	  $menutext .= "<a href=\"$toppath/admin/address_kind/\">����Ͽ��ʬ����</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/workflow")) {
	  $menutext .= "<a href=\"$toppath/admin/flows/\">�ե�����</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
  $menutext .= "<a href=\"$toppath/admin/datas/\">�ǡ�������</a><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/logs/\">��ɽ��</a><BR>\n";
  $menutext .= "<a href=\"$toppath/admin/logs2/\">�ܺ٥�ɽ��</a><BR>\n";
#  $menutext .= "<BR>\n";
#  $menutext .= "<a href=\"$toppath/admin/info.php\">PHPinfo</a><BR>\n";
?>