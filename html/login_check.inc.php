<?
  $refresh = false;  $login = false;

  if (!$conn) { /* ���դ� Y-m-d h:n:s �˸��� */
    $conn = pg_connect($pgsql_connect);
    $res = pg_query($conn,"SET DATESTYLE TO 'ISO'");
  } else {
    $res = pg_query($conn,"SET DATESTYLE TO 'ISO'");
  }

  if (!empty($_COOKIE["pgo"])) {
    // ���å�������
    if (!empty($_COOKIE["pgo"])) {
      $login_str = $_COOKIE["pgo"];
      if (trim($sid)=="") $sid = $_COOKIE["PHPSESSID"];

      // �桼����¸�߳�ǧ
      $sql = "SELECT * FROM users WHERE session_id='".$sid."'";
      $res = pg_query($conn,$sql);
      $cnt = pg_num_rows($res);
      if ($cnt==1) {
        $row = pg_fetch_array($res,0);
        if (crypt($row["id"],$sid)==$login_str) {
          $headertext .= "Set-Cookie: pgo=".urlencode($login_str)."; path=".$toppath."/;\n";
          $login_id = $row["id"];
          $login_name = euc2sjis($row["name"]);
          $login = true;
        }
      } elseif ($cnt>1) {
        $sql = "UPDATE users SET session_id=NULL where session_id='".$sid."'";
        pg_query($conn,$sql);
      } else {
        $MES[] = "Ʊ���桼����ID��¾PC����Υ����󤬤��ä����ἫưŪ�˥������Ȥ��ޤ�����";
        $login = false;
        $headertext .= "Set-Cookie: pgo=; expires=".gmdate("D, d M Y H:i:s",time()-3600)." GMT; path="."/;\n";
      }
    } else {
      $login = false;
    }
  }

  // �ѿ�Ÿ��
  $login_no      = 0;
  $admin_sign    = "f";
  $download_sign = "f";
  if ($login) {
    $sql = "select * from users where id='".$login_id."'";
    $res = pg_query($conn,$sql);
    $cnt = pg_num_rows($res);
    if ($cnt>0) {
      $row = pg_fetch_array($res,0);
      $login_no           = $row["seqno"];
      $login_name         = $row["name"];
      $login_email        = $row["email"];
      $passwd_updatestamp = $row["passwd_updatestamp"];
      $admin_sign         = $row["admin_sign"];
      $download_sign      = $row["download_sign"];
      $workflow_sign      = $row["workflow_sign"];
      $address_flag       = $row["address_flag"];
    } else {
      $admin_sign         = 'f';
      $sv_sign            = 'f';
      $download_sign      = 'f';
      $workflow_sign      = 'f';
      $address_flag       = 3;
    }
  } else {
    $admin_sign = 'f';
    $sv_sign = 'f';
    $download_sign = 'f';
  }
