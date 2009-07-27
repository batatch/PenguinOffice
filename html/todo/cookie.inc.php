<?
#	if ($login && sizeof($MES)==0) {
		// 事び仑え滦据
		if (!empty($_POST["viewtype"])){
	    $headertext .= "Set-Cookie: todo_c_viewtype=".$_POST["viewtype"]."; path=/; expires=".gmdate("l, d-M-Y H:i:s",time()+15552000)." GMT;\n";
			$todo_c_viewtype = $_POST["viewtype"];
		}
		if (empty($_COOKIE["todo_c_viewtype"])) $todo_c_viewtype = "begin_day";

		// 事び仑え界
		if (!empty($_POST["vieworder"])){
	    $headertext .= "Set-Cookie: todo_c_vieworder=".$_POST["vieworder"]."; path=/; expires=".gmdate("l, d-M-Y H:i:s",time()+15552000)." GMT;\n";
			$todo_c_vieworder = $_POST["vieworder"];
		}
		if (empty($_COOKIE["todo_c_vieworder"])) $todo_c_vieworder = "asc";
#	}
?>
