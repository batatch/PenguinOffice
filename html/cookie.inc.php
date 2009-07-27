<?php
  $rip = getenv("REMOTE_ADDR");
  if (eregi("210.153.84.",$rip)==1 or eregi("210.136.161.",$rip)==1) {
    // imodeˆ—
    if ($id != "") {
      $login = true;
      $login_id = $id;
    }
  } else {
    // ’Êíˆ—
    if (!empty($HTTP_COOKIE_VARS["penguin:login"])) {
      $login = true;
      // ‚¦‚ñ‚¿‚å[
      setcookie("penguin:login",$HTTP_COOKIE_VARS["penguin:login"],time()+1800);

      $cols = split("\t",$HTTP_COOKIE_VARS["penguin:login"]);
      $login_id = $cols[0];

    } else {
      $login = false;
      $login_id = $cols[0];
    }
  }
?>
