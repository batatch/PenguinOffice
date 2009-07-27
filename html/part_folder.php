<?php
print "<!-- part_folder.php -->";

$filecount = 5; //表示ファイル数
if ($login) {
	// Webフォルダ
	$sql  = "SELECT * FROM files WHERE ";
	$sql .= "createstamp+'".(24*3).":00'>now() ";
	$sql .= "ORDER BY updatestamp DESC";
	
	$res = pg_query($conn,$sql);
	$cnt = pg_num_rows($res);
	
	$c = 0;
	
	if ($cnt>0) {
		// タイトル表示
		print "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 WIDTH=100%>";
		
		print "<TR BGCOLOR=$indexmenu_backcolor HEIGHT=21>";
		print "<TD NOWRAP><A HREF=\"./folder/\">Webフォルダ</A></TD>";
		print "</TR>";
		
		print "<TR><TD>";
		
		print "<TABLE BORDER=0 WIDTH=100% CELLPADDING=1 CELLSPACING=0>\n";
		//
		for($i=0;$i<$cnt;$i++){
			print "<TR><TD VALIGN=TOP ALIGN=LEFT VALIGN=MIDDLE>";
			$row = pg_fetch_array($res,$i);
			if ($admin_sign != "t") {
				$sql_check = "SELECT view_group_ids,view_user_ids FROM folders WHERE path='".$row["path"]."'";
				$res_check = pg_query($conn,$sql_check);
				$cnt_check = pg_num_rows($res_check);
				if ($cnt_check>0) {
					$view = false;
					$row_check = pg_fetch_array($res_check,0);
					
					// 表示チェック
					if ($row_check["view_group_ids"]=="" && $row_check["view_user_ids"]=="") {
						$view = true;
					} else {
						if ($row_check["view_group_ids"]!="") {
							$view_group_id = split(",",$row_check["view_group_ids"]);
							if (sizeof($view_group_id)>0) while (list($seq,$id)=each($view_group_id)) {
								if (sizeof($groups)>0) {
									reset($groups);
									while (list($gseq,$gid)=each($groups)) {
										if ($gid==$id) { $view = true; break; }
									}
								}
							}
						}
						if ($row_check["view_user_ids"]!="") {
							$view_user_id = split(",",$row_check["view_user_ids"]);
							if (sizeof($view_user_id)>0) while (list($seq,$id)=each($view_user_id)) {
								if ($login_id==$id) { $view = true; break; }
							}
						}
					}
					if (!$view) continue;
				}
			}
			
			if ($row["path"]!="/") {
				$filename = $row["path"]."/".$row["filename"];
			} else {
				$filename = $row["path"].$row["filename"];
			}
			$imageicon = ext_check($filename);
			
			$linkstr = "?path=".urlencode($row["path"])."&name=".urlencode($row["filename"]);
			
			if ($download_sign=="t") print "<A HREF=\"./folder/download/".$linkstr."\">";
			print "<IMG SRC=\"".$imageicon."\" BORDER=0 ALT=\"".$filename."\" WIDTH=26 HEIGHT=26 ALIGN=LEFT>";
			if ($download_sign=="t") print "</A>";
			
			if ($download_sign=="t") print "<A HREF=\"./folder/files/".$linkstr."&p=top\">";
			print "<FONT STYLE=\"font-size:85%\">";
			print $filename;
			print "</FONT>";
			if ($download_sign=="t") print "</A><BR>";
			
			print "(".get_first("users","name_ryaku","id='".$row["user_id"]."'","").")";
			print " <FONT COLOR=#666666>".date("n/j",datetime2timestamp($row["updatestamp"]))."";
			if ($i<$cnt-1) print "<TR BGCOLOR=$bg_dark><TD HEIGHT=1><IMG SRC=\"./image/$borderwidth.gif\" WIDTH=100% HEIGHT=$borderwidth></TD></TR>";
			#        print "<BR CELAR=ALL>";
			print "</TD></TR>\n";
			$c++;
			if ($c>=$filecount) break;
		}
		//
		print "</TABLE>";
		if ($c==0) {
			print "<TR><TD><FONT COLOR=#CCCCCC>なし</TD></TR>\n";
		}
		print "</TD></TR></TABLE>";
	}
}
?>