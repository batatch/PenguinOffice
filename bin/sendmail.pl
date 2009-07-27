#!/usr/bin/perl
use Pg;

require '/usr/local/penguinoffice/bin/mimer.pl';
require '/usr/local/penguinoffice/bin/mimew.pl';
require '/usr/local/penguinoffice/bin/jcode.pl';

$sendmail = "/usr/sbin/sendmail";

$webmaster = "it@tech-arts.co.jp";

$dbconnect = "dbname=penguinoffice";

#######################
## Init
#######################
$ENV{'TZ'} = "JST-9";
($sec,$min,$hour,$mday,$mon,$year,$wday) = localtime(time);
@week = ('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
@mons = ('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$date = sprintf("%s, %d %s %04d %02d:%02d:%02d +0900 (JST)",
                      $week[$wday],$mday,$mons[$mon],$year+1900,$hour,$min,$sec);
	
#######################
## DB OPEN
#######################
$conn = Pg::connectdb($dbconnect);
if ($conn->status eq PGRES_CONNECTION_BAD) {
  &log("not found database or account missmatch");
  exit(0);
} else {
  $sql = "select process_id from process";
  $res = $conn->exec($sql);
  $cnt = $res->ntuples;
  if ($cnt == 0) {

    # 二重起動の防止
    $sql = "INSERT INTO process (process_id) VALUES ($$)";
    $res = $conn->exec($sql);

    # 古いデータ(3日以上前の)を削除
    $sql = "DELETE FROM alarm WHERE sendflag='t' AND now()-sendstamp>'3 days'";
    $res = $conn->exec($sql);

    # 送信大賞の読み込み
    $sql = "select seqno,mailfrom,mailto,subject,fileflag,boundary from alarm where sendstamp < 'now' and sendflag='f'";    $res = $conn->exec($sql);
    $cnt = $res->ntuples;

    if ($cnt > 0) {
      while (@row = $res->fetchrow) {
        $seqno    = $row[0];
        $from     = $row[1];
        $to       = $row[2];
        $subject  = $row[3];
        $fileflag = $row[4];
        $boundary = $row[5];

        $sql2 = "select body from alarm where seqno=".$seqno;
        $res2 = $conn->exec($sql2);
        $cnt2 = $res2->ntuples;
        if ($cnt2 > 0) {
          @row2 = $res2->fetchrow;
          $body = $row2[0];
        } else {
          $body = "";
        }
        
#        $subject = mimeencode(jcode'jis($subject));
        $body =~ s/\x0D\x0A|\x0D|\x0A/\n/g;

        $subject = mimeencode(jcode'jis($subject,euc));
        $body = jcode'jis($body,euc);

        $header = "";

        $header .= "Date: ".$date."\n";
        $header .= "From: ".$from."\n";
        $header .= "Errors-To: ".$webmaster."\n";
        $header .= "Return-Path: ".$webmaster."\n";
        $header .= "To: ".$to."\n";
        $header .= "Subject: ".$subject."\n";
        $header .= "MIME-Version: 1.0\n";

        if ($fileflag ne "t") {
          $header .= "Content-Type: text/plain; charset=iso-2022-jp\n";
          $header .= "Content-Transfer-Encoding: 7bit\n";
        } else {
          $header .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
          $header .= "Content-Transfer-Encoding: 7bit\n";
        }
        $header .= "X-Mailser: PenguinOffice SendMail\n";
        $header .= "\n";

        open( MAIL, "| $sendmail -t");
        print MAIL $header.$body;
        close( MAIL );

        &log($to);

        ### 更新処理
        $sql_upd = "update alarm set sendflag='t' where seqno=".$seqno;
        $res_upd = $conn->exec($sql_upd);
      }
    }

    $sql = "DELETE FROM process WHERE process_id = ($$)";
    $res = $conn->exec($sql);
  }
}
exit(1);

sub log {
  ($sec,$min,$hour,$mday,$mon,$year) = localtime(time);
  $year+=1900;
  $mon++;

  if($mon<10)  { $mon  = "0".$mon; }
  if($mday<10) { $mday = "0".$mday; }
  if($hour<10) { $hour = "0".$hour; }
  if($min<10)  { $min  = "0".$min; }
  if($sec<10)  { $sec  = "0".$sec; }

  print "[".$year."/".$mon."/".$mday." ";
  print $hour.":".$min.":".$sec."]";
  print " ";
  print @_;
  print "\n";
}
