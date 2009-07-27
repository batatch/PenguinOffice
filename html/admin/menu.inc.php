<?
  $menutext .= "<A HREF=\"$toppath/admin/setting/\">管理者設定</A><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<A HREF=\"$toppath/admin/holiday/\">祭日設定</A><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/groups/\">グループ設定</a><BR>\n";
  $menutext .= "<a href=\"$toppath/admin/users/\">ユーザー設定</a><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/locations/\">行き先名称設定</a><BR>\n";
  $menutext .= "<BR>\n";
	if (file_exists($basepath.$toppath."/schedule")) {
	  $menutext .= "<a href=\"$toppath/admin/schedule_kind/\">予定表区分設定</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/room")) {
	  $menutext .= "<a href=\"$toppath/admin/rooms_type/\">施設区分設定</a><BR>\n";
	  $menutext .= "<a href=\"$toppath/admin/rooms/\">施設設定</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/bulletin")) {
	  $menutext .= "<a href=\"$toppath/admin/boards/\">BBSルーム設定</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/address")) {
	  $menutext .= "<a href=\"$toppath/admin/address_kind/\">住所録区分設定</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
	if (file_exists($basepath.$toppath."/workflow")) {
	  $menutext .= "<a href=\"$toppath/admin/flows/\">フロー設定</a><BR>\n";
	  $menutext .= "<BR>\n";
	}
  $menutext .= "<a href=\"$toppath/admin/datas/\">データ管理</a><BR>\n";
  $menutext .= "<BR>\n";
  $menutext .= "<a href=\"$toppath/admin/logs/\">ログ表示</a><BR>\n";
  $menutext .= "<a href=\"$toppath/admin/logs2/\">詳細ログ表示</a><BR>\n";
#  $menutext .= "<BR>\n";
#  $menutext .= "<a href=\"$toppath/admin/info.php\">PHPinfo</a><BR>\n";
?>