<?php
	function get_progress($seqno, $val, $page){
	
		for ($j = 0; $j <= 100; $j = $j + 10){
			if ($val >= $j){
				$bar[$j] = "¢£";
			} else {
				$bar[$j] = "¢¢";
			}
		}

		$ret = "<a href=\"./progress.php?n=".$seqno."&v=0&p=".$page."\">Ì¤</a><a href=\"./progress.php?n=".$seqno."&v=10&p=".$page."\">".$bar[10]."</a><a href=\"./progress.php?n=".$seqno."&v=20&p=".$page."\">".$bar[20]."</a><a href=\"./progress.php?n=".$seqno."&v=30&p=".$page."\">".$bar[30]."</a><a href=\"./progress.php?n=".$seqno."&v=40&p=".$page."\">".$bar[40]."</a><a href=\"./progress.php?n=".$seqno."&v=50&p=".$page."\">".$bar[50]."</a><a href=\"./progress.php?n=".$seqno."&v=60&p=".$page."\">".$bar[60]."</a><a href=\"./progress.php?n=".$seqno."&v=70&p=".$page."\">".$bar[70]."</a><a href=\"./progress.php?n=".$seqno."&v=80&p=".$page."\">".$bar[80]."</a><a href=\"./progress.php?n=".$seqno."&v=90&p=".$page."\">".$bar[90]."</a><a href=\"./progress.php?n=".$seqno."&v=100&p=".$page."\">".$bar[100]."</a>";
		
		return $ret;
		
	}
?>