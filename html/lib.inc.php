<?php
/*
  lib.inc.php
  関数
*/
function is_tel($s) {
  $result = true;
  for ($i=0;$i<strlen($s);$i++) {
    $ASC = ord(substr($s,$i,1));
    if (( $ASC<ord("0") or $ASC>ord("9") ) and $ASC>ord("-")) {
      $result = false;
    }
  }
  return $result;
}

function is_email($s) {
  $result = false;
  if (eregi("^[^@]+@[^.]+\..+",$s)) {
    $result = true;
  }
  return $result;
}

function is_date($s) {
  $result = false;
  if (preg_match("/^[12]\d{3}(\/|-)(0?[1-9]|1[0-2])(\/|-)(0?[1-9]|[12]\d|3[01])$/",$s)) {
    // 西暦4桁
    $result = true;
  } elseif (preg_match("/^\d{2}(\/|-)(0?[1-9]|1[0-2])(\/|-)(0?[1-9]|[12]\d|3[01])$/",$s)) {
    // 西暦2桁
    $result = true;
  }
  return $result;
}

/* 半角英数字のみ */
function is_ANK($s) {
  $result = true;
  if (eregi("[^a-zA-Z0-9]+",$s)) {
    $result = false;
  }
  return $result;
}

/* 英小文字置換関数 */
function lowercase($str) { 
  $ord1 = ord("A");
  $ord2 = ord("a");
  $res = $str;
  for ($i=0;$i<26;$i++) {
    $res = mb_ereg_replace(chr($ord1+$i), chr($ord2+$i), $res);
  }
  return $res; 
}

/* 英大文字置換関数 */
function uppercase($str) { 
  $ord1 = ord("a");
  $ord2 = ord("A");
  $res = $str;
  for ($i=0;$i<26;$i++) {
    $res = mb_ereg_replace(chr($ord1+$i), chr($ord2+$i), $res);
  }
  return $res; 
}

function textsafe($str) {
  $res = trim($str);
  $res = stripslashes($res);
  $res = str_replace('"',"”",$res);
  $res = str_replace("&","&amp;",$res);
  $res = str_replace("<","&lt;",$res);
  $res = str_replace(">","&gt;",$res);
  return $res;
}

function wiki_textsafe($str) {
  $res = str_replace("<","&lt;",$str);
  $res = str_replace(">","&gt;",$res);
  return $res;
}

function db_textsafe($str) {
  $res = trim($str);
  $res = str_replace("\\","￥",$res);
  $res = str_replace("'" ,"’",$res);
  return $res;
}

function view_textsafe($str) {
  $res = trim($str);
  $res = stripslashes($res);
  $res = str_replace("<","&lt;",$res);
  $res = str_replace(">","&gt;",$res);
  return $res;
}

function mail_textsafe($str) {
  $res = trim($str);
  $res = stripslashes($res);
  $res = str_replace("&lt;","<",$res);
  $res = str_replace("&gt;",">",$res);
  $res = str_replace("&amp;","&",$res);
  return $res;
}

/* 半角カナ⇒全角カナ & 制御文字のカット */
function arrange_kana($str) {
  $res = $str;
  $res = mb_convert_kana($res, "KV");
  $res = mb_convert_kana($res, "a");
  $res = str_replace("'", "’", $res);
  $res = str_replace('"',"”", $res);
#  $res = stripslashes($res);
  $res = str_replace("\\", "&yen;", $res);
  $res = str_replace("\n"  ,""  ,$res);
  $res = str_replace("\r"  ,""  ,$res);
  $res = str_replace("\t"  ," "  ,$res);
  $res = strip_tags($res);
  return $res;
}

function arrange_hiragana($str) {
  $res = arrange_kana($str);
  $res = mb_convert_kana($res, "c");
  return $res;
}

function arrange_katakana($str) {
  $res = arrange_kana($str);;
  $res = mb_convert_kana($res, "C");
  return $res;
}

/* 半角カナ⇒全角カナ & 制御文字のカット(改行はそのまま) */
function arrange_kana2($str) {
  $res = $str;
  $res = mb_convert_kana($res, "KV");
  $res = str_replace("'","’",$res);
  $res = str_replace('"',"”",$res);
  $res = str_replace("\\", "&yen;", $res);
  $res = str_replace("\t"  ," "  ,$res);
  $res = strip_tags($res);
  return $res;
}

/* 全角⇒半角 & 制御文字のカット */
function arrange_ascii($str) {
  $res = $str;
  $res = mb_convert_kana($res, "KV");
  $res = mb_convert_kana($res, "a");
  $res = str_replace("'","’",$res);
  $res = str_replace('"',"”",$res);
  $res = strip_tags($res);
  $res = mb_convert_encoding($res, "EUC","SJIS");
  $len = strlen($res);
  $res2= "";
  if ($len <> 0) {
    for ($i=0;$i<$len;$i++) {
      $c = substr($res,$i,1);
#print $c.":".ord($c)."<br>";
      if (ord($c)<>142) {
        $res2 = $res2.$c;
      } else {
        $i = $i + 1;
      }
    }
  }
  $res2 = mb_convert_encoding($res2, "SJIS", "EUC");
  return $res2;
}

/* 全角⇒半角(小文字) & 制御文字のカット */
function arrange_ascii_lower($str) {
  $res = $str;
  $res = mb_convert_kana($res, "a");
  $res = str_replace("'","’",$res);
  $res = str_replace('"',"”",$res);
  $res = strip_tags($res);
  $res = mb_convert_encoding($res, "EUC","SJIS");
  $len = strlen($res);
  $res2= "";
  if ($len <> 0) {
    for ($i=0;$i<$len;$i++) {
      $c = substr($res,$i,1);
#print $c.":".ord($c)."<br>";
      if (ord($c)<>142) {
        $res2 = $res2.$c;
      } else {
        $i = $i + 1;
      }
    }
  }
  $res2 = mb_convert_encoding($res2, "SJIS", "EUC");
  $res2 = strtolower($res2);
  return $res2;
}

/* 全角⇒半角(大文字) & 制御文字のカット */
function arrange_ascii_upper($str) {
  $res = $str;
  $res = mb_convert_kana($res, "a");
  $res = ereg_replace("'","’",$res);
  $res = ereg_replace('"',"”",$res);
  $res = stripslashes($res);
  $res = mb_convert_encoding($res, "EUC","SJIS");
  $len = strlen($res);
  $res2= "";
  if ($len <> 0) {
    for ($i=0;$i<$len;$i++) {
      $c = substr($res,$i,1);
#print $c.":".ord($c)."<br>";
      if (ord($c)<>142) {
        $res2 = $res2.$c;
      } else {
        $i = $i + 1;
      }
    }
  }
  $res2 = mb_convert_encoding($res2, "SJIS", "EUC");
  $res2 = strtoupper($res2);
  return $res2;
}

/* テーブル出力用 */
function td_str($s) {
  if ($s=="") {
    return "&nbsp;";
  } else {
    return $s;
  }
}

/* 通常出力用(Oracle用) */
function str_str($s) {
  if ($s=="" or empty($s)) {
    return "";
  } else {
#    $str2 = mb_ereg_replace("￥", "", $str);
    return $s;
  }
}

/* 文字列整形 */
function str_cut($s,$width) {
  $outtext = "";
  if (sizeof($s) > 1 or strlen($s) > $width) {
    for ($i=0;$i<mb_strlen($s);$i++) {
      $outtext .= mb_substr($s,$i,1);
      if (strlen($outtext)>=$width-2) {
        break;
      }
    }
    return $outtext.".";
  } else {
    return $s;
  }
}

function str_rcut($s,$width) {
  $outtext = "";
  if (sizeof($s) > 1 or strlen($s) > $width) {
    $i = mb_strlen($s);
    while($i>0) {
      $outtext = mb_substr($s,$i,1).$outtext;
      if (strlen($outtext)>$width-2) {
        break;
      }
      $i--;
    }
    return ".".$outtext;
  } else {
    return $s;
  }
}

function get_last($table,$field,$where,$default) {
  global $conn;
  if ($where != "") {
    $where .= " AND ";
  }
  $where .= "not $field is null";

  $sql = "SELECT $field FROM $table WHERE $where ORDER BY $field DESC";
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  if ($cnt > 0) {
    $row = pg_fetch_array($res,0);
    $val = $row[$field];
  } else {
    $val = $default;
  }
  return $val;
}

function get_first($table,$field,$where,$default) {
  global $conn;
  if ($where != "") {
    $where .= " AND ";
  }
  $where .= "not $field is null";

  $sql = "SELECT $field FROM $table WHERE $where ORDER BY $field";
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  if ($cnt > 0) {
    $row = pg_fetch_array($res,0);
    $val = $row[$field];
  } else {
    $val = $default;
  }
  return $val;
}

function get_row($table,$fields,$where) {
  global $conn;
  if ($where != "") {
    $sql = "SELECT $fields FROM $table WHERE $where";
  } else {
    $sql = "SELECT $fields FROM $table";
  }
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  if ($cnt > 0) {
    $row = pg_fetch_array($res,0);
    return $row;
  } else {
    return array();
  }
}

function get_count($table,$where) {
  global $conn;
  if ($where != "") {
    $sql = "SELECT count(*) FROM $table WHERE $where";
  } else {
    $sql = "SELECT count(*) FROM $table";
  }
  $res = pg_query($conn,$sql);
  $cnt = pg_num_rows($res);
  if ($cnt > 0) {
    $row = pg_fetch_array($res,0);
    $val = $row[0];
  } else {
    $val = 0;
  }
  return $val;
}

function make_serial($length) {
  $s = "";
  for($i=0;$i<20;$i++) {
    mt_srand((double)microtime()*1000000);
    $r = mt_rand(1,62);
    if (($r>=1) and ($r<=26)) {
      $s .= chr(ord("a")+($r-1));
    } elseif (($r>=27) and ($r<=52)) {
      $s .= chr(ord("A")+($r-27));
    } elseif (($r>=53) and ($r<=62)) {
      $s .= chr(ord("0")+($r-53));
    }
  }
  return $s;
}

/* 年月日チェック(PHP関数のcheckdateと混同しないように) */
function chk_date($Y,$m,$d,$meshead) {
  $err = false;
  $mes = "";
  if (strlen(trim($Y)) == 0) {
    $mes .= $meshead."年が指定されていません<BR>";
    $err  = true;
  } else {
    if (!is_numeric($Y)) {
      $mes .= $meshead."年が正しくありません<BR>";
      $err  = true;
    } else {
      if (($Y>2010) or ($Y<1900)) {
        $mes .= $meshead."年は西暦で指定してください<BR>";
        $err  = true;
      } 
    }
  }
  if (strlen(trim($m)) == 0) {
    $mes .= $meshead."月が指定されていません<BR>";
    $err  = true;
  } else {
    if (!is_numeric($m)) {
      $mes .= $meshead."月が正しくありません<BR>";
      $err  = true;
    } else {
      if (($m>12) or ($m<1)) {
        $mes .= $meshead."月が正しくありません<BR>";
        $err  = true;
      } 
    }
  }
  if (strlen(trim($d)) == 0) {
    $mes .= $meshead."日が指定されていません<BR>";
    $err  = true;
  } else {
    if (!is_numeric($d)) {
      $mes .= $meshead."日が正しくありません<BR>";
      $err  = true;
    } else {
      if (($d>31) or ($d<1)) {
        $mes .= $meshead."日が正しくありません<BR>";
        $err  = true;
      } 
    }
  }
  if (!$err) {
    if (checkdate($m,$d,$Y)==false) {
      $mes .= "年月日の指定が正しくありません<BR>";
    }
  }
  if ($err) {
    return $mes;
  } else {
    return "";
  }
}

/* 日付変換 DATETIME型 => TIMESTAMP型 */
function datetime2timestamp($dt) {
  $w    = split(" ",$dt);
  $wYMD = split("-",$w[0]);
  if (sizeof($wYMD)==1) {
    $wYMD = split("/",$w[0]);
  }
  $wDST = split("\+",$w[1]);
  $wHMS = split(":",$wDST[0]);
  return mktime($wHMS[0], $wHMS[1], $wHMS[2], $wYMD[1], $wYMD[2], $wYMD[0]);
}

/* 日付変換 DATE型 => TIMESTAMP型 */
function date2timestamp($dt) {
  $wYMD = split("-",$dt);
  if (sizeof($wYMD)==1) {
    $wYMD = split("/",$dt);
  }
  return mktime(0, 0, 0, $wYMD[1], $wYMD[2], $wYMD[0]);
}

/* 日付変換 TIMESTAMP型 => DATETIME型*/
function timestamp2datetime($ts) {
  $w    = split(" ",$dt);
  $wY = date("Y", $ts);
  $wM = date("m", $ts);
  $wD = date("d", $ts);
  $wH = date("H", $ts);
  $wN = date("i", $ts);
  $wS = date("s", $ts);
  return $wY."/".$wM."/".$wD." ".$wH.":".$wN.":".$wS;
}

function euc2sjis($str) {
  return mb_convert_encoding($str,"SJIS","EUC-JP");
}

function utf2sjis($str) {
  return mb_convert_encoding($str,"SJIS","UTF-8");
}

function jis2sjis($str) {
  return mb_convert_encoding($str,"SJIS","JIS");
}

function sjis2euc($str) {
  return mb_convert_encoding($str,"EUC-JP","SJIS");
}

function utf2euc($str) {
  return mb_convert_encoding($str,"EUC-JP","UTF-8");
}

function jis2euc($str) {
  return mb_convert_encoding($str,"EUC-JP","JIS");
}

function sjis2utf($str) {
  return mb_convert_encoding($str,"UTF-8","SJIS");
}

function euc2utf($str) {
  return mb_convert_encoding($str,"UTF-8","EUC-JP");
}

function jis2utf($str) {
  return mb_convert_encoding($str,"UTF-8","JIS");
}

function sjis2jis($str) {
  return mb_convert_encoding($str,"JIS","SJIS");
}

function euc2jis($str) {
  return mb_convert_encoding($str,"JIS","EUC-JP");
}

function utf2jis($str) {
  return mb_convert_encoding($str,"JIS","UTF-8");
}

// Wiki風表示関数(実験中)
function str_wiki($str) {
	global $td_back_left,$td_back;
	//事前整形
	$str = str_replace("’","'",$str);
	$str = str_replace("&amp;","&",$str);
#	$str = str_replace("&lt;","<",$str);
#	$str = str_replace("&gt;",">",$str);

	$str = preg_replace("/\r\n|\n|\r|<BR>/i","\n",$str);
	
	//配列展開
	$arrstr = split("\n",$str);
	$rs = "";

	//変数
	$istable = false;
	$istablerow = false;
	$istablecell = false;

	$ul_level = 0;
	$ol_level = 0;
	
	while(list($seq,$s)=each($arrstr)) {
		$nobr = false;
		//<HR> is -----
		if (preg_match("/\-\-\-\-\-+/",$s,$res)) {
			$s = preg_replace("/\-\-\-\-\-+/","<HR>",$s);
			$nobr = true;
		}
		
		//**...** is <B>
	  while (true) {
			if (!preg_match("/(.*)\*\*(.+?)\*\*(.*)/",$s,$res)) break;
			$s = $res[1]."<b>".$res[2]."</b>".$res[3];
		}
		
		//__...__ is <u>
	  while (true) {
			if (!preg_match("/(.*)__(.+?)__(.*)/",$s,$res)) break;
			$s = $res[1]."<u>".$res[2]."</u>".$res[3];
		}
		//''...'' is <i>
	  while (true) {
			if (!preg_match("/(.*)''(.+?)''(.*)/",$s,$res)) break;
			$s = $res[1]."<i>".$res[2]."</i>".$res[3];
		}

		//::...:: is <center>
	  while (true) {
			if (!preg_match("/(.*)::(.+?)::(.*)/",$s,$res)) break;
			$s = $res[1]."<center>".$res[2]."</center>".$res[3];
			$nobr = true;
		}

		//<<...<< is <div align=left>
	  while (true) {
			if (!preg_match("/(.*)&lt;&lt;(.+?)&lt;&lt;(.*)/",$s,$res)) break;
			$s = $res[1]."<div align=left>".$res[2]."</div>".$res[3];
			$nobr = true;
		}

		//>>...>> is <div align=right>
	  while (true) {
			if (!preg_match("/(.*)&gt;&gt;(.+?)&gt;&gt;(.*)/",$s,$res)) break;
			$s = $res[1]."<div align=right>".$res[2]."</div>".$res[3];
			$nobr = true;
		}

		//-+...+- is <TT>
	  while (true) {
			if (!preg_match("/(.*)\-\+(.+?)\+\-(.*)/",$s,$res)) break;
			$s = $res[1]."<tt>".$res[2]."</tt>".$res[3];
		}
		
		//~~...~~ is <FONT COLOR=>
	  while (true) {
			if (!preg_match("/(.*)~~(.+?)~~(.*)/",$s,$res)) break;
			$arrtxt = split(":|\||,",$res[2],2);
			if (sizeof($arrtxt)==2) {
				$s = $res[1]."<FONT COLOR=".$arrtxt[0].">".$arrtxt[1]."</FONT>".$res[3];
			} else {
				$s = $res[1]."<FONT>".$res[2]."</FONT>".$res[3];
			}
		}

		//^...^ is BOX
	  while (true) {
			if (!preg_match("/(.*)\^(.+?)\^(.*)/",$s,$res)) break;

			$arrtxt = split(":",$res[2],4);
			if (sizeof($arrtxt)==4) {
				$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=".$arrtxt[0]." WIDTH=".$arrtxt[2]."><tr><td BGCOLOR=".$arrtxt[1].">".$arrtxt[3]."</td></tr></table>".$res[3];
			} elseif (sizeof($arrtxt)==3) {
				$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=".$arrtxt[0]."><tr><td BGCOLOR=".$arrtxt[1].">".$arrtxt[2]."</td></tr></table>".$res[3];
			} elseif (sizeof($arrtxt)==2) {
				$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=".$arrtxt[0]."><tr><td BGCOLOR=".$arrtxt[0].">".$arrtxt[1]."</td></tr></table>".$res[3];
			} else {
				$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=#999999><tr><td BGCOLOR=$td_back>".$res[2]."</td></tr></table>".$res[3];
			}
			$nobr = true;
		}
	  while (true) {
			if (!preg_match("/(.*)&#710;(.+?)&#710;(.*)/",$s,$res)) break;
			$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=#999999><tr><td BGCOLOR=$td_back>".$res[2]."</td></tr></table>".$res[3];
			$nobr = true;
		}
		
		//-=...=- is Title
	  while (true) {
			if (!preg_match("/(.*)\-=(.+?)=\-(.*)/",$s,$res)) break;
			$s = $res[1]."<font style=\"font-size:130%;font-weight:bold\">".$res[2]."</font>".$res[3];
#			$nobr = true;
		}
		
		//{...} is image
	  while (true) {
			if (!preg_match("/(.*)\{(.+?)\}(.*)/",$s,$res)) break;
			$arrtxt = split("\|",$res[2],6);
			if (sizeof($arrtxt)==1) {
				$s = $res[1]."<img src=\"".$res[2]."\">".$res[3];
			} elseif (sizeof($arrtxt)==2) {
				$s = $res[1]."<img src=\"".$arrtxt[0]."\" ALT=\"".$arrtxt[1]."\">".$res[3];
			} elseif (sizeof($arrtxt)==4) {
				$s = $res[1]."<img src=\"".$arrtxt[0]."\" ALT=\"".$arrtxt[1]."\" WIDTH=".$arrtxt[2]." HEIGHT=".$arrtxt[3].">".$res[3];
			} elseif (sizeof($arrtxt)==6) {
				$s = $res[1]."<img src=\"".$arrtxt[0]."\" ALT=\"".$arrtxt[1]."\" WIDTH=".$arrtxt[2]." HEIGHT=".$arrtxt[3]." ALIGN=".$arrtxt[4]." VALIGN=".$arrtxt[5].">".$res[3];
			} else {
				$s = $res[1]."<img src=\"".$arrtxt[0]."\" ALT=\"".$arrtxt[1]."\">".$res[3];
			}
#			$nobr = true;
		}
		
		//-> <-(-|) is <blockquote>
	  while (true) {
			if (!preg_match("/(.*)[\-]+&gt;(.*)/",$s,$res)) break;
			$s = $res[1]."<blockquote>".$res[2];
			$nobr = true;
		}
	  while (true) {
			if (!preg_match("/(.*)&lt;[\-]+(.*)/",$s,$res)) break;
			$s = $res[1]."</blockquote>".$res[2];
			$nobr = true;
		}
	  while (true) {
			if (!preg_match("/(.*)[\-]+\|(.*)/",$s,$res)) break;
			$s = $res[1]."</blockquote>".$res[2];
			$nobr = true;
		}

		// --- is <ul>
		// +++ is <ol>

		$is_ul = preg_match("/^(\-{1,4})(.*)/",$s,$ul_res);
		if (!$is_ul) {
			if ($ul_level>0) {
				for($i=0;$i<$ul_level;$i++) {
					$s = "</ul>".$s;
				}
			}
			$ul_level = 0;
		}
		if ($is_ul) {
			$s = "";
			$level = strlen($ul_res[1]);
			if ($level>$ul_level) {
				for($i=$ul_level;$i<$level;$i++) {
					$s .= "<ul>";
				}
			}
			if ($level<$ul_level) {
				for($i=$level;$i<$ul_level;$i++) {
					$s .= "</ul>";
				}
			}
			$s .= "<li>".$ul_res[2]."</li>";
			$ul_level = $level;
			$nobr = true;
		}

		$is_ol = preg_match("/^(\+{1,4})(.*)/",$s,$ol_res);
		if (!$is_ol) {
			if ($ol_level>0) {
				for($i=0;$i<$ol_level;$i++) {
					$s = "</ol>".$s;
				}
			}
			$ol_level = 0;
		}
		if ($is_ol) {
			$s = "";
			$level = strlen($ol_res[1]);
			if ($level>$ol_level) {
				for($i=$ol_level;$i<$level;$i++) {
					$s .= "<ol>";
				}
			}
			if ($level<$ol_level) {
				for($i=$level;$i<$ol_level;$i++) {
					$s .= "</ol>";
				}
			}
			$s .= "<li>".$ol_res[2]."</li>";
			$ol_level = $level;
			$nobr = true;
		}

		//[[...]] is HTML direct
	  while (true) {
			if (!preg_match("/(.*)\[\[(.+?)\]\](.*)/",$s,$res)) break;
			$s = str_replace("&lt;","<",$res[2]);
			$s = str_replace("&gt;",">",$s);
			$s = str_replace("”",'"',$s);
			$s = str_replace("’;","'",$s);
			$s = $res[1].$s.$res[3];
		}
		
		//[(...)] is New Link
	  while (true) {
			if (!preg_match("/(.*)\[\((.+?)\)\](.*)/",$s,$res)) break;
			$arrtxt = split("\|",$res[2],2);
			if (sizeof($arrtxt)==2) {
				$s = $res[1]."<a href=".$arrtxt[0]." target=\"_blank\">".$arrtxt[1]."</a>".$res[3];
			} else {
				$s = $res[1]."<a href=\"".$res[2]."\" target=\"_blank\">".$res[2]."</a>".$res[3];
			}
		}

		//[...] is Link
	  while (true) {
			if (!preg_match("/(.*)\[(.+?)\](.*)/",$s,$res)) break;
			$arrtxt = split("\|",$res[2],2);
			if (sizeof($arrtxt)==2) {
				$s = $res[1]."<a href=".$arrtxt[0].">".$arrtxt[1]."</a>".$res[3];
			} else {
				$s = $res[1]."<a href=\"".$res[2]."\">".$res[2]."</a>".$res[3];
			}
		}
		
		//<tr>〜</tr>
		if ($istable && !preg_match("/(.*)\|\|\|(.*)/",$s,$res)) {
			$arrtxt = split("\|",$s);
			if (sizeof($arrtxt)>0) {
				$s = "<tr>";
				while(list($colno,$coltext)=each($arrtxt)) {


					if (trim($coltext)!="") {
						$arrtext = preg_split("/:/i",$coltext,2);
						if (sizeof($arrtext)==2 && (!preg_match("/^\/\//",$arrtext[1]))) {
							$s .= "<td BGCOLOR=".$arrtext[0].">";
							$s .= $arrtext[1];
						} else {
							$s .= "<td BGCOLOR=$td_back>";
							$s .= $coltext;
						}
						$s .= "</td>";
					}


#					if (trim($coltext)!="") {
#						$s .= "<td BGCOLOR=$td_back>";
#						$s .= $coltext;
#						$s .= "</td>";
#					}
				}
				$s .= "</tr>";
			}
			$nobr = true;
		}

	  while (true) {
			//||| is <table> or </table>
			if (!preg_match("/(.*)\|\|\|(.*)/",$s,$res)) break;
			if ($istable) {
				$istable = false;
				$s = $res[1]."</table>".$res[2];
			} else {
				$istable = true;
				$s = $res[1]."<table BORDER=0 CELLPADDING=3 CELLSAPCING=1 BGCOLOR=#999999>".$res[2];
			}
			$nobr = true;
		}

		if ($nobr) {
			$rs .= $s;
		} else {
			$rs .= $s."\n";
		}
	}
	//事後整形
	$rs = str_replace("&","&amp;",$rs);
	$rs = str_replace("'","’",$rs);
#	$rs = str_replace("<","&lt;",$rs);
#	$rs = str_replace(">","&gt;",$rs);

	$rs = str_replace("\n","<BR>\n",$rs);
	return $rs;
}

function str_link($s) {
  global $mydomain;
  $p = 0;
  $s2 = "";
  while (true) {
    if (mb_eregi("s?https?://[-_.!~*'a-zA-Z0-9;/?:@&=+$%#]+",substr($s,$p),$reg)>0) {
      $p2 = strpos(" ".substr($s,$p),$reg[0]);
      $s2 .= substr($s,$p,$p2-1);
      if ($mydomain != "" && strpos($reg[0],$mydomain)>0) {
        $s2 .= "<A HREF=\"".$reg[0]."\" STYLE=\"font-weight:normal\">".$reg[0]."</A>";
      } else {
        $s2 .= "<A HREF=\"".$reg[0]."\" STYLE=\"font-weight:normal\" target=\"_blank\">".$reg[0]."</A>";
      }
      $p = $p + $p2 + strlen($reg[0])-1;
    } else {
      $s2 .= substr($s,$p);
      $s = $s2;
      break;
    }
  }

  $p = 0;
  $s2 = "";
  while (true) {
    if (mb_eregi("[-_.a-zA-Z0-9]+@[-_.a-zA-Z0-9]+\.[-_.a-zA-Z]+",substr($s,$p),$reg)>0) {
      $p2 = strpos(" ".substr($s,$p),$reg[0]);
      $s2 .= substr($s,$p,$p2-1);
      $s2 .= "<A HREF=\"mailto:".$reg[0]."\" STYLE=\"font-weight:normal\">".$reg[0]."</A>";
      $p = $p + $p2 + strlen($reg[0])-1;
    } else {
      $s2 .= substr($s,$p);
      $s = $s2;
      break;
    }
  }
  return $s2;
}

// 携帯用
function str_link_tel($s) {
  $p = 0;
  $s2 = "";
  while (true) {
    if (mb_eregi("[0-9][0-9]+\-[0-9][0-9]+\-[0-9][0-9][0-9][0-9]+",substr($s,$p),$reg)>0) {
      $p2 = strpos(" ".substr($s,$p),$reg[0]);
      $s2 .= substr($s,$p,$p2-1);
      $s2 .= "<A HREF=\"tel:".$reg[0]."\"\">".$reg[0]."</A>";
      $p = $p + $p2 + strlen($reg[0])-1;
    } else {
      $s2 .= substr($s,$p);
      $s = $s2;
      break;
    }
  }
  return $s2;
}

// 
function str_highlight($search,$subject) {
  $searchs = split(" ",$search);

  $res = $subject;

  if (sizeof($searchs)>0) {
    while (list($key,$val)=each($searchs)) { 
      $val = str_replace("SPAN","",$val);
      $val = str_replace("CLASS","",$val);
      $val = str_replace("HIGHLIGHT","",$val);
      $val = str_replace("=","",$val);
      $val = str_replace("/","",$val);
      if ($val != "" && strlen($val)>1) {
        $res = preg_replace("/".$val."/","<SPAN CLASS=HIGHLIGHT>".$val."</SPAN>",$res);
      }
    }
  }
  return $res;
}

function ext_check($filename) {
  global $toppath;
  $p = strrpos($filename,".");
  $path = $toppath."/image/icon/";
  if ($p>0) {
    ## OS系一般
    if (preg_match("/\.txt$/i",$filename)) {
      return $path."txt.gif";
    } elseif (preg_match("/\.log$/i",$filename)) {
      return $path."log.gif";
    } elseif (preg_match("/\.csv$/i",$filename)) {
      return $path."csv.gif";
    ### 一般的な画像
    } elseif (preg_match("/(\.jpg|\.jpe|\.jpeg)$/i",$filename)) {
      return $path."jpeg.gif";
    } elseif (preg_match("/\.gif$/i",$filename)) {
      return $path."gif.gif";
    } elseif (preg_match("/(\.jp2|\.j2c|\.j2k|\.jpx)$/i",$filename)) {
      return $path."jpeg.gif";
    } elseif (preg_match("/\.png$/i",$filename)) {
      return $path."png.gif";
    } elseif (preg_match("/\.bmp$/i",$filename)) {
      return $path."bmp.gif";
    } elseif (preg_match("/\.psd$/i",$filename)) {
      return $path."psd.gif";
    } elseif (preg_match("/\.tif*$/i",$filename)) {
      return $path."tiff.gif";
    } elseif (preg_match("/\.pdf$/i",$filename)) { // PDF
      return $path."pdf.gif";
    ## 一般的なMicrosoft-Officeファイル
    } elseif (preg_match("/\.doc$/i",$filename)) { // MS-Word
      return $path."doc.gif";
    } elseif (preg_match("/\.xls$/i",$filename)) { // MS-Excel
      return $path."xls.gif";
    } elseif (preg_match("/\.ppt$/i",$filename)) { // MS-PowerPoint
      return $path."ppt.gif";
    } elseif (preg_match("/\.mdb$/i",$filename)) { // MS-Access
      return $path."mdb.gif";
    ## HTML関連
    } elseif (preg_match("/(\.html|\.htm)$/i",$filename)) {  // HTML
      return $path."html.gif";
    } elseif (preg_match("/\.shtml$/i",$filename)) { // SHTML
      return $path."shtml.gif";
    } elseif (preg_match("/\.php$/i",$filename)) {   // PHP
      return $path."php.gif";
    } elseif (preg_match("/\.php3$/i",$filename)) {  // PHP3
      return $path."php3.gif";
    } elseif (preg_match("/\.php4$/i",$filename)) {  // PHP4
      return $path."php4.gif";
    } elseif (preg_match("/\.phtml$/i",$filename)) { // PHTML
      return $path."phtml.gif";
    } elseif (preg_match("/\.pl$/i",$filename)) {    // PL
      return $path."pl.gif";
    } elseif (preg_match("/\.cgi$/i",$filename)) {   // CGI
      return $path."cgi.gif";
    ## 一般的な動画ファイル
    } elseif (preg_match("/\.avi$/i",$filename)) {
      return $path."avi.gif";
    } elseif (preg_match("/(\.mpg$|\.mpe$|\.mpeg)$/i",$filename)) {
      return $path."mpeg.gif";
    } elseif (preg_match("/\.mp2$/i",$filename)) {
      return $path."mp2.gif";
    } elseif (preg_match("/\.mov$/i",$filename)) {
      return $path."mov.gif";
    } elseif (preg_match("/\.qt$/i",$filename)) {
      return $path."qt.gif";
    ## 一般的な動画ファイル(Flash関連)
    } elseif (preg_match("/\.fla$/i",$filename)) {
      return $path."fla.gif";
    } elseif (preg_match("/\.swf$/i",$filename)) {
      return $path."swf.gif";
    ## 一般的な音声ファイル
    } elseif (preg_match("/\.mp3$/i",$filename)) {
      return $path."mp3.gif";
    } elseif (preg_match("/(\.aif$|\.aifc$|\.aiff)$/i",$filename)) {
      return $path."wav.gif";
    } elseif (preg_match("/(\.wav$|\.wave)$/i",$filename)) {
      return $path."wav.gif";
    ## その他の音声ファイル
    } elseif (preg_match("/\.au$/i",$filename)) {
      return $path."au.gif";
    } elseif (preg_match("/\.mid$/i",$filename)) {
      return $path."mid.gif";
    } elseif (preg_match("/\.midi$/i",$filename)) {
      return $path."midi.gif";
    } elseif (preg_match("/\.snd$/i",$filename)) {
      return $path."snd.gif";
    ## その他の動画ファイル
    } elseif (preg_match("/\.mpa$/i",$filename)) {
      return $path."mpa.gif";
    } elseif (preg_match("/\.m1v$/i",$filename)) {
      return $path."m1v.gif";
    } elseif (preg_match("/\.dvi$/i",$filename)) {
      return $path."dvi.gif";
    ## Flash関連
    } elseif (preg_match("/\.spa$/i",$filename)) {
      return $path."spa.gif";
    } elseif (preg_match("/\.ssk$/i",$filename)) {
      return $path."ssk.gif";
    } elseif (preg_match("/\.swt$/i",$filename)) {
      return $path."swt.gif";
    } elseif (preg_match("/\.spl$/i",$filename)) {
      return $path."spl.gif";
    } elseif (preg_match("/\.fh5$/i",$filename)) {
      return $path."fh5.gif";
    } elseif (preg_match("/\.fh7$/i",$filename)) {
      return $path."fh7.gif";
    } elseif (preg_match("/\.iff$/i",$filename)) {
      return $path."iff.gif";
    } elseif (preg_match("/\.ps$/i",$filename)) {
      return $path."ps.gif";
    } elseif (preg_match("/\.ai$/i",$filename)) {
      return $path."ai.gif";
    } elseif (preg_match("/\.cgm$/i",$filename)) { // Illustrator(cgm)
      return $path."cgm.gif";
    } elseif (preg_match("/\.cdr$/i",$filename)) { // Illustrator(cdr)
      return $path."cdr.gif";
    } elseif (preg_match("/\.dxf$/i",$filename)) { // Illustrator(dxf)
      return $path."dxf.gif";
    } elseif (preg_match("/\.dwg$/i",$filename)) { // Illustrator(dwg)
      return $path."dwg.gif";
    } elseif (preg_match("/\.emf$/i",$filename)) { // Illustrator(emf)
      return $path."emf.gif";
    } elseif (preg_match("/(\.eps$|\.epsf)$/i",$filename)) { // Illustrator(eps)
      return $path."eps.gif";
    } elseif (preg_match("/(\.fh4$|\.fh8)$/i",$filename)) { // Illustrator(fh4,fh8)
      return $path."fh4.gif";
    } elseif (preg_match("/\.flm$/i",$filename)) { // Illustrator(flm)
      return $path."flm.gif";
    } elseif (preg_match("/\.pcd$/i",$filename)) { // Illustrator(pcd)
      return $path."pcd.gif";
    ## 圧縮関連
    } elseif (preg_match("/\.rpm$/i",$filename)) {
      return $path."rpm.gif";
    } elseif (preg_match("/\.lzh$/i",$filename)) {
      return $path."lzh.gif";
    } elseif (preg_match("/\.sea$/i",$filename)) {
      return $path."sea.gif";
    } elseif (preg_match("/\.sit$/i",$filename)) {
      return $path."sit.gif";
    } elseif (preg_match("/(\.tar$|\.arc$|\.arj$|\.bdf$|\.bz2$|\.cab$|\.cpt)$/i",$filename)) {
      return $path."tar.gif";
    } elseif (preg_match("/(\.taz$|\.tgz$|\.z$|\.rar$|\.lzs$|\.gz$|\.ish)$/i",$filename)) {
      return $path."tar.gif";
    } elseif (preg_match("/\.zip$/i",$filename)) {
      return $path."zip.gif";
    } elseif (preg_match("/\.patch$/i",$filename)) {
      return $path."patch.gif";
    ## Windows関連
    } elseif (preg_match("/(\.ico$|\.icon)$/i",$filename)) {
      return $path."icon.gif";
    ## その他画像
    } elseif (preg_match("/\.tga$/i",$filename)) {
      return $path."tga.gif";
    } elseif (preg_match("/\.rle$/i",$filename)) {
      return $path."rle.gif";
    } elseif (preg_match("/\.pcx$/i",$filename)) {
      return $path."pcx.gif";
    } elseif (preg_match("/\.xpm$/i",$filename)) {
      return $path."xpm.gif";
    } elseif (preg_match("/(\.pic$|\.pct$|\.pict$|\.pic[1-2]$|\.pics)$/i",$filename)) {
      return $path."pict.gif";
    } elseif (preg_match("/\.lrg$/i",$filename)) {
      return $path."lrg.gif";
    } elseif (preg_match("/\.rtf$/i",$filename)) {
      return $path."rtf.gif";
    ## その他のMicrosoft-Officeファイル
    } elseif (preg_match("/\.dot$/i",$filename)) { // MS-Word
      return $path."dot.gif";
    } elseif (preg_match("/\.mcw$/i",$filename)) { // MS-Word
      return $path."mcw.gif";
    } elseif (preg_match("/\.xjs$/i",$filename)) { // MS-Excel
      return $path."xjs.gif";
    } elseif (preg_match("/\.xlm$/i",$filename)) { // MS-Excel
      return $path."xlm.gif";
    } elseif (preg_match("/\.xla$/i",$filename)) { // MS-Excel
      return $path."xla.gif";
    } elseif (preg_match("/\.xlc$/i",$filename)) { // MS-Excel
      return $path."xlc.gif";
    } elseif (preg_match("/\.xlw$/i",$filename)) { // MS-Excel
      return $path."xlw.gif";
    } elseif (preg_match("/\.xlt$/i",$filename)) { // MS-Excel
      return $path."xlt.gif";
    } elseif (preg_match("/\.pps$/i",$filename)) { // MS-PowerPoint
      return $path."pps.gif";
    } elseif (preg_match("/\.pot$/i",$filename)) { // MS-PowerPoint
      return $path."pot.gif";
    } elseif (preg_match("/\.ppa$/i",$filename)) { // MS-PowerPoint
      return $path."ppa.gif";
    } elseif (preg_match("/\.mdw$/i",$filename)) { // MS-Access
      return $path."mdw.gif";
    } elseif (preg_match("/\.mda$/i",$filename)) { // MS-Access
      return $path."mda.gif";
    } elseif (preg_match("/\.mde$/i",$filename)) { // MS-Access
      return $path."mde.gif";
    ### その他文書関連
    ## 一太郎
    } elseif (preg_match("/\.atr$/i",$filename)) {
      return $path."atr.gif";
    } elseif (preg_match("/\.ctl$/i",$filename)) {
      return $path."ctl.gif";
    } elseif (preg_match("/\.jxw$/i",$filename)) {
      return $path."jxw.gif";
    } elseif (preg_match("/\.jsw$/i",$filename)) {
      return $path."jsw.gif";
    } elseif (preg_match("/\.jaw$/i",$filename)) {
      return $path."jaw.gif";
    } elseif (preg_match("/\.jtw$/i",$filename)) {
      return $path."jtw.gif";
    } elseif (preg_match("/\.jbw$/i",$filename)) {
      return $path."jbw.gif";
    } elseif (preg_match("/\.juw$/i",$filename)) {
      return $path."juw.gif";
    } elseif (preg_match("/\.jfw$/i",$filename)) {
      return $path."jfw.gif";
    } elseif (preg_match("/\.jvw$/i",$filename)) {
      return $path."jvw.gif";
    } elseif (preg_match("/\.jtd$/i",$filename)) {
      return $path."jtd.gif";
    } elseif (preg_match("/\.jtt$/i",$filename)) {
      return $path."jtt.gif";
    ## 花子
    } elseif (preg_match("/\.dra$/i",$filename)) {
      return $path."dra.gif";
    } elseif (preg_match("/\.drh$/i",$filename)) {
      return $path."drh.gif";
    } elseif (preg_match("/\.jsh$/i",$filename)) {
      return $path."jsh.gif";
    } elseif (preg_match("/\.jah$/i",$filename)) {
      return $path."jah.gif";
    } elseif (preg_match("/\.jbh$/i",$filename)) {
      return $path."jbh.gif";
    } elseif (preg_match("/\.jth$/i",$filename)) {
      return $path."jth.gif";
    } elseif (preg_match("/\.juh$/i",$filename)) {
      return $path."juh.gif";
    } elseif (preg_match("/\.pst$/i",$filename)) {
      return $path."pst.gif";
    } elseif (preg_match("/\.prt$/i",$filename)) {
      return $path."prt.gif";
    } elseif (preg_match("/\.pts$/i",$filename)) {
      return $path."pts.gif";
    } elseif (preg_match("/\.pt3$/i",$filename)) {
      return $path."pt3.gif";
    } elseif (preg_match("/\.pa3$/i",$filename)) {
      return $path."pa3.gif";
    } elseif (preg_match("/\.jhd$/i",$filename)) {
      return $path."jhd.gif";
    } elseif (preg_match("/\.jht$/i",$filename)) {
      return $path."jht.gif";
    } elseif (preg_match("/\.jmg$/i",$filename)) {
      return $path."jmg.gif";
    ## OASYS
    } elseif (preg_match("/\.oas$/i",$filename)) {
      return $path."oas.gif";
    } elseif (preg_match("/\.oa2$/i",$filename)) {
      return $path."oa2.gif";
    } elseif (preg_match("/\.oa3$/i",$filename)) {
      return $path."oa3.gif";
    } elseif (preg_match("/\.grp$/i",$filename)) {
      return $path."grp.gif";
    } elseif (preg_match("/\.owk$/i",$filename)) {
      return $path."owk.gif";
    } else {
      return $path."unknown.gif";
    }

## 登録予定拡張子
#ポストスクリプト形式
#eps
#ps
#アイコン
#ico
#windows形式
#tif
#font
#scr
#sys
#dll
##inf
#cpl
#vxd
#com
#bat
#exe
#scr
#ocx
#Lotus123
#fmt
#windowsのヘルプで使われるﾌｧｲﾙ形式｡
#fts
  } else {
    return $path."unknown.gif";
  }
}

// メール送信
function sendmail($mailto, $mailsubject, $mailbody, $mailheader, $extra) {
  $fd = popen("/usr/sbin/sendmail -t $extra", 'w');
  fputs($fd, "To: $mailto\n");
  fputs($fd, "Subject: $mailsubject\n");
  if ($mailheader) fputs($fd, $mailheader."\n");
  fputs($fd, "\n");
  fputs($fd, $mailbody);
  pclose($fd);
}

// ワークフロー用関数
function arrangetext($str,$separete) {
  $text = "";

  if ($separete=="" || $separete=="input") {
    $lines = split("\n",preg_replace("/<BR>|\r\n|\r|\n/i","\n",$str));
    while(list($seq,$line)=each($lines)) {
      $s = $line;
      while (true) {
        if (strlen($s)>76) {
          $ss = mb_strcut($s,0,76);
          $s = substr($s,strlen($ss));
        } else {
          $ss = $s;
          $s = "";
        }

        $ss = str_highlight($kwd,$ss);
        $ss = str_link(preg_replace("/\s\s/i"," &nbsp;",$ss));
        $text .= $ss."\n";

        if ($s=="") break;
      }
    }
  } elseif ($separete=="board") {
    $lines = split("\n",preg_replace("/<BR>|\r\n|\r|\n/i","\n",$str));
    while(list($seq,$line)=each($lines)) {
      $s = $line;
      while (true) {
        if (strlen($s)>76) {
          $ss = mb_strcut($s,0,76);
          $s = substr($s,strlen($ss));
        } else {
          $ss = $s;
          $s = "";
        }

        $ss = str_highlight($kwd,$ss);
        $ss = str_link(preg_replace("/\s\s/i"," &nbsp;",$ss));
        $text .= $ss."<BR>";

        if ($s=="") break;
      }
    }
  }
  return $text;
}
?>