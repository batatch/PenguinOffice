#!/usr/bin/perl

print "storemail\n";

if ($#ARGV != 1) { exit; }
$tmpdir = $ARGV[0];
$userid = $ARGV[1];

# 一時ファイル名の生成
($sec,$min,$hour,$mday,$mon,$year) = localtime(time);
$year += 1900;
$mon++;
if($mon<10)  { $mon  = "0".$mon; }
if($mday<10) { $mday = "0".$mday; }
if($hour<10) { $hour = "0".$hour; }
if($min<10)  { $min  = "0".$min; }
if($sec<10)  { $sec  = "0".$sec; }
#$r = int(rand(99999));
$r = $$;

if($r<10)       { $r = "0000".$r; }
elsif($r<100)   { $r = "000".$r; }
elsif($r<1000)  { $r = "00".$r; }
elsif($r<10000) { $r = "0".$r; }
$filename = $year.$mon.$mday.$hour.$min.$sec.$r;

# Mailをバッファに取り込み
while (<STDIN>) {
  $buf .= $_;
}
if ($buf eq "") { exit(0); }

$i = 0;

open OUT, ">> ".$tmpdir."/".$filename;
print OUT $buf;
close OUT;

exit(0);
