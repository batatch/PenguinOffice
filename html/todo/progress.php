<?
include "../config.inc.php"; include "../lib.inc.php";

//include "../header.inc.php";
//print_r($_SESSION);
//print_r($_COOKIE);
//print_r($_GET);
//echo($PHPSESSID."<br>\n");
//echo($n."<br>\n");
//echo($v."<br>\n");

$sql = "update todos set";
$sql .= " progress=".$v;
$sql .= " where user_id=(";
$sql .= " select user_id from users ";
$sql .= " where session_id='".$PHPSESSID."') and seqno=".$n;
$res = pg_query($conn,$sql);

switch ($p){
	case "todo":
		$next_page = "index.phtml";
		break;
	case "edit":
		$next_page = "index.phtml?n=".$n;
		break;
}

header("Location: ".$next_page);
?>