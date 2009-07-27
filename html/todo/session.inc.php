<?
	if ($login && sizeof($MES)==0) {
		// 優先度
		if ($_POST["s_priority"] !== NULL){
			$_SESSION["todo_s_priority"] = $_POST["s_priority"];
		}
		if ($todo_s_priority === NULL) $todo_s_priority = 0;
		// 開始年始
		if ($_POST["from_begin_year"] !== NULL){
			$_SESSION["todo_s_from_begin_year"] = $_POST["from_begin_year"];
		}
		if ($todo_s_from_begin_year === NULL) $todo_s_from_begin_year = $today["year"];
		// 開始月始
		if ($_POST["from_begin_month"] !== NULL){
			$_SESSION["todo_s_from_begin_month"] = $_POST["from_begin_month"];
		}
		if ($todo_s_from_begin_month === NULL) $todo_s_from_begin_month = 1;
		// 開始日始
		if ($_POST["from_begin_day"] !== NULL){
			$_SESSION["todo_s_from_begin_day"] = $_POST["from_begin_day"];
		}
		if ($todo_s_from_begin_day === NULL) $todo_s_from_begin_day = 1;
		// 開始年終
		if ($_POST["to_begin_year"] !== NULL){
			$_SESSION["todo_s_to_begin_year"] = $_POST["to_begin_year"];
		}
		if ($todo_s_to_begin_year === NULL) $todo_s_to_begin_year = $today["year"];
		// 開始月終
		if ($_POST["to_begin_month"] !== NULL){
			$_SESSION["todo_s_to_begin_month"] = $_POST["to_begin_month"];
		}
		if ($todo_s_to_begin_month === NULL) $todo_s_to_begin_month = 12;
		// 開始日終
		if ($_POST["to_begin_day"] !== NULL){
			$_SESSION["todo_s_to_begin_day"] = $_POST["to_begin_day"];
		}
		if ($todo_s_to_begin_day === NULL) $todo_s_to_begin_day = 31;

		// 締切日使用?
		if ($_POST["use_end_flg"]=="t") {
			$_SESSION["todo_s_use_end_flg"] = "on";
		} elseif ($_POST["use_end_flg"]=="f") {
			$_SESSION["todo_s_use_end_flg"] = "off";
		} else {
		}
#		if ($todo_s_use_end_flg === NULL) $todo_s_use_end_flg = "on";
		// 終了年始
		if ($_POST["from_end_year"] !== NULL){
			$_SESSION["todo_s_from_end_year"] = $_POST["from_end_year"];
		}
		if ($todo_s_from_end_year === NULL) $todo_s_from_end_year = $today["year"];
		// 終了月始
		if ($_POST["from_end_month"] !== NULL){
			$_SESSION["todo_s_from_end_month"] = $_POST["from_end_month"];
		}
		if ($todo_s_from_end_month === NULL) $todo_s_from_end_month = 1;
		// 終了日始
		if ($_POST["from_end_day"] !== NULL){
			$_SESSION["todo_s_from_end_day"] = $_POST["from_end_day"];
		}
		if ($todo_s_from_end_day === NULL) $todo_s_from_end_day = 1;
		// 終了年終
		if ($_POST["to_end_year"] != NULL){
			$_SESSION["todo_s_to_end_year"] = $_POST["to_end_year"];
		}
		if ($todo_s_to_end_year === NULL) $todo_s_to_end_year = $today["year"];
		// 終了月終
		if ($_POST["to_end_month"] !== NULL){
			$_SESSION["todo_s_to_end_month"] = $_POST["to_end_month"];
		}
		if ($todo_s_to_end_month === NULL) $todo_s_to_end_month = 12;
		// 終了日終
		if ($_POST["to_end_day"] !== NULL){
			$_SESSION["todo_s_to_end_day"] = $_POST["to_end_day"];
		}
		if ($todo_s_to_end_day === NULL) $todo_s_to_end_day = 31;
		// 達成率小
		if ($_POST["progress_min"] !== NULL){
			$_SESSION["todo_s_progress_min"] = $_POST["progress_min"];
		}
		if ($todo_s_progress_min === NULL) $todo_s_progress_min = 0;
		// 達成率大
		if ($_POST["progress_max"] !== NULL){
			$_SESSION["todo_s_progress_max"] = $_POST["progress_max"];
		}
		if ($todo_s_progress_max === NULL) $todo_s_progress_max = 90;

		// タイトル
		if ($_POST["title"] !== NULL){
			$_SESSION["todo_s_title"] = $_POST["title"];
		}
		if ($todo_s_title === NULL) $todo_s_title = "";

		// 現在の呼び出しアプリケーション
		if ($_GET["p"] !== NULL){
			$todo_s_p = $_GET["p"];
		}
		if ($todo_s_p === NULL) $todo_s_p = "todo";
		// ID指定
		if ($_GET["n"] !== NULL){
			$todo_s_n = $_GET["n"];
		}
		if ($todo_s_n === NULL) $todo_s_n = "";

		$begin_from_stamp = date2timestamp($todo_s_from_begin_year."/".$todo_s_from_begin_month."/".$todo_s_from_begin_day);
		$begin_to_stamp = date2timestamp($todo_s_to_begin_year."/".$todo_s_to_begin_month."/".$todo_s_to_begin_day);
		$end_from_stamp = date2timestamp($todo_s_from_end_year."/".$todo_s_from_end_month."/".$todo_s_from_end_day);
		$end_to_stamp = date2timestamp($todo_s_to_end_year."/".$todo_s_to_end_month."/".$todo_s_to_end_day);

		$begin_from_date = date("Y/m/d",$begin_from_stamp);
		$begin_to_date   = date("Y/m/d",$begin_to_stamp);
		$end_from_date   = date("Y/m/d",$end_from_stamp);
		$end_to_date     = date("Y/m/d",$end_to_stamp);

		$todo_s_from_begin_year  = date("Y",$begin_from_stamp);
		$todo_s_from_begin_month = date("n",$begin_from_stamp);
		$todo_s_from_begin_day   = date("j",$begin_from_stamp);
		$todo_s_to_begin_year  = date("Y",$begin_to_stamp);
		$todo_s_to_begin_month = date("n",$begin_to_stamp);
		$todo_s_to_begin_day   = date("j",$begin_to_stamp);

		$todo_s_from_end_year  = date("Y",$end_from_stamp);
		$todo_s_from_end_month = date("n",$end_from_stamp);
		$todo_s_from_end_day   = date("j",$end_from_stamp);
		$todo_s_to_end_year  = date("Y",$end_to_stamp);
		$todo_s_to_end_month = date("n",$end_to_stamp);
		$todo_s_to_end_day   = date("j",$end_to_stamp);

	
	}
?>
