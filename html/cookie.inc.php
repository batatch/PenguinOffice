<?php
  $rip = getenv("REMOTE_ADDR");
  if (eregi("210.153.84.",$rip)==1 or eregi("210.136.161.",$rip)==1) {
    // imode処理
    if ($id != "") {
      $login = true;
      $login_id = $id;
    }
  } else {
    // 通常処理
    if (!empty($HTTP_COOKIE_VARS["penguin:login"])) {
      $login = true;
      // えんちょー
      setcookie("penguin:login",$HTTP_COOKIE_VARS["penguin:login"],time()+1800);

      $cols = split("\t",$HTTP_COOKIE_VARS["penguin:login"]);
      $login_id = $cols[0];

    } else {
      $login = false;
      $login_id = $cols[0];
    }
  }
?>
