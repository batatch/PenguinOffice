package MIME;
# Copyright (C) 1993-94,1997 Noboru Ikuta <noboru@ikuta.ichihara.chiba.jp>
#
# mimer.pl: MIME decoder library Ver.2.02 (1997/12/30)

$main'mimer_version = 2.02;

# $B%$%s%9%H!<%k(B : @INC $B$N%G%#%l%/%H%j!JDL>o$O(B /usr/local/lib/perl$B!K$K%3%T!<(B
#                $B$7$F2<$5$$!#(B
#
# $B;HMQNc(B1 : require 'mimer.pl';
#           $from = "From: Noboru Ikuta / =?ISO-2022-JP?B?GyRCQDhFRBsoQg==?=";
#           $from .= "\n\t=?ISO-2022-JP?B?GyRCPjobKEI=?=";
#           $from .= " <noboru\@ikuta.ichihara.chiba.jp>";
#           print &mimedecode($from, "EUC");
#
# $B;HMQNc(B2 : # UNIX$B$G(BBase64$B%G%3!<%I$9$k>l9g(B
#           require 'mimer.pl';
#           undef $/;
#           $body = <>;
#           print &bodydecode($body);
#           print &bdeflush;
#
# &bodydecode($data,$coding):
#   Base64$B7A<0$^$?$O(BQuoted-Printable$B7A<0$N%G!<%?$r%G%3!<%I$9$k!#(B
#   $BBh(B2$B%Q%i%a!<%?$K(B"qp"$B$^$?$O(B"b64"$B$r;XDj$9$k$3$H$K$h$j%3!<%G%#%s%07A<0(B
#   $B$r;X<($9$k$3$H$,$G$-$k!#Bh(B2$B%Q%i%a!<%?$r>JN,$9$k$H(BBase64$B7A<0$H$7$F(B
#   $B=hM}$5$l$k!#(B
#   Base64$B7A<0$N%G%3!<%I$N>l9g$O!"(B4$B%P%$%HC10L$GJQ49$9$k$N$G!"EO$5$l$?(B
#   $B%G!<%?$N$&$AH>C<$JItJ,$O%P%C%U%!$KJ]B8$5$l<!$K8F$P$l$?$H$-$K=hM}(B
#   $B$5$l$k!#:G8e$K%P%C%U%!$K;D$C$?%G!<%?$O(B&bdeflush$B$r8F$V$3$H$K$h$j=hM}(B
#   $B$5$l%P%C%U%!$+$i%/%j%"$5$l$k!#(B
#   Quoted-Printable$B7A<0$N%G%3!<%I$N>l9g$O!">e5-$N%P%C%U%!$O;HMQ$7$J$$(B
#   $B$N$G(B&bdeflush$B$r8F$VI,MW$O$J$$$,8F$s$G$b9=$o$J$$(B($B2?$b$7$J$$(B)$B!#(B
#
# &bdeflush($coding):
#   $BBh(B1$B%Q%i%a!<%?$K(B"b64"$B$^$?$O(B"qp"$B$r;XDj$9$k$3$H$K$h$j!"$=$l$>$l(BBase64
#   $B7A<0$^$?$O(BQuoted-Printable$B7A<0$N%G%3!<%I$r;XDj$9$k$3$H$,$G$-$k!#(B
#   $BBh(B1$B%Q%i%a!<%?$K2?$b;XDj$7$J$1$l$P(BBase64$B7A<0$H$7$F=hM}$5$l$k!#(B
#   &bodydecode$B$,=hM}$7;D$7$?%G!<%?$,%P%C%U%!$K;D$C$F$$$l$P!"$=$l$r=hM}(B
#   $B$7%P%C%U%!$r%/%j%"$9$k!#(B
#   Base64$B$N%G%3!<%I$N>l9g!"@5>o$K%(%s%3!<%I$5$l$?%G!<%?$G$"$l$P(B4$B%P%$%H(B
#   $B$NG\?t$ND9$5$N$O$:$J$N$G:G8e$K%G!<%?$,%P%C%U%!>e$K;D$k$3$H$O9M$($i$l(B
#   $B$J$$$,!"0l$D$N%G!<%?$r(B(1$B2s$^$?$O2?2s$+$KJ,$1$F(B)&bodydecode$B$7$?8e$K(B
#   $BG0$N$?$a(B1$B2s8F$V$3$H$r?d>)$9$k!#(B
#
# &mimedecode($text,$kanjicode):
#   $BBh(B1$B%Q%i%a!<%?$N%G!<%?Cf$K(Bencoded-word("=?ISO2022-JP?B?"$B$H(B"?="$B$K0O$^(B
#   $B$l$?J8;zNs!"(BRFC2047$B;2>H(B)$B$,$"$l$P$=$NItJ,$rA*$S=P$7$F%G%3!<%I$9$k!#(B
#   $BBh(B2$B%Q%i%a!<%?$H$7$F(B"EUC"$B$^$?$O(B"SJIS"$B$r;XDj$9$k$H%G%3!<%I$7$?ItJ,$N(B
#   $BF|K\8lJ8;zNsItJ,$r;XDj$7$?J8;z%3!<%I$KJQ49$9$k!#(B
#   $BBh(B2$B%Q%i%a!<%?$r>JN,(B($B$^$?$OL58z$JCM$r;XDj(B)$B$9$k$H(BJIS$B%3!<%I$rJV$9!#(B
#   RFC2047$B$K4p$E$-!"(Bencoded-word$B$G$O$5$^$l$?(BLWS($B6uGr(B)$B$O:o=|$9$k!#(B
#
# $BG[I[>r7o(B : $BCx:n8"$OJ|4~$7$^$;$s$,!"G[I[!&2~JQ$O<+M3$H$7$^$9!#2~JQ$7$F(B
#            $BG[I[$9$k>l9g$O!"%*%j%8%J%k$H0[$J$k$3$H$rL@5-$7!"%*%j%8%J%k(B
#            $B$N%P!<%8%g%s%J%s%P!<$K2~JQHG%P!<%8%g%s%J%s%P!<$rIU2C$7$?7A(B
#            $BNc$($P(B Ver.2.02-XXXXX $B$N$h$&$J%P!<%8%g%s%J%s%P!<$rIU$1$F2<(B
#            $B$5$$!#$J$*!"(BCopyright$BI=<($OJQ99$7$J$$$G$/$@$5$$!#(B
#
# $BCm0U(B : &mimedecode$B$r(Bjperl1.X($B$N(B2$B%P%$%HJ8;zBP1~%b!<%I(B)$B$G;HMQ$9$k$H$-$O!"(B
#        tr/// $B$N=q$-J}$,0[$J$j$^$9$N$G!"I,MW$K1~$8$F(B 'sub j2e'$B$N%3%a%s%H(B(#)
#        $B$rIU$1BX$($F$/$@$5$$!#(Bjperl1.4$B$r(B-Llatin$B%*%W%7%g%sIU$-$G;HMQ$9$k(B
#        $B>l9g$*$h$S(B EUC$BJQ495!G=$r;H$o$J$$>l9g$O$=$NI,MW$O$"$j$^$;$s!#(B
#        $B$J$*!"(BPerl5$BBP1~$N(Bjperl$B$O;n$7$?$3$H$,$J$$$N$G$I$N$h$&$JF0:n$K$J$k(B
#        $B$+$o$+$j$^$;$s!#(B
#
# $B;2>H(B : RFC1468, RFC2045, RFC2047

## MIME base64 $B%"%k%U%!%Y%C%H%F!<%V%k!J(BRFC2045$B$h$j!K(B
%code = (
"A", "000000",  "B", "000001",  "C", "000010",  "D", "000011",
"E", "000100",  "F", "000101",  "G", "000110",  "H", "000111",
"I", "001000",  "J", "001001",  "K", "001010",  "L", "001011",
"M", "001100",  "N", "001101",  "O", "001110",  "P", "001111",
"Q", "010000",  "R", "010001",  "S", "010010",  "T", "010011",
"U", "010100",  "V", "010101",  "W", "010110",  "X", "010111",
"Y", "011000",  "Z", "011001",  "a", "011010",  "b", "011011",
"c", "011100",  "d", "011101",  "e", "011110",  "f", "011111",
"g", "100000",  "h", "100001",  "i", "100010",  "j", "100011",
"k", "100100",  "l", "100101",  "m", "100110",  "n", "100111",
"o", "101000",  "p", "101001",  "q", "101010",  "r", "101011",
"s", "101100",  "t", "101101",  "u", "101110",  "v", "101111",
"w", "110000",  "x", "110001",  "y", "110010",  "z", "110011",
"0", "110100",  "1", "110101",  "2", "110110",  "3", "110111",
"4", "111000",  "5", "111001",  "6", "111010",  "7", "111011",
"8", "111100",  "9", "111101",  "+", "111110",  "/", "111111",
);

## ASCII, 7bit JIS$B$N3F!9$K%^%C%A$9$k%Q%?!<%s(B
$match_ascii = '\x1b\([BHJ]([\t\x20-\x7e]*)';
$match_jis = '\x1b\$[@B](([\x21-\x7e]{2})*)';

## charset=`ISO-2022-JP',encoding=`B' $B$N(B encoded-word $B$K%^%C%A$9$k%Q%?!<%s(B
$match_mime = '=\?[Ii][Ss][Oo]-2022-[Jj][Pp]\?[Bb]\?([A-Za-z0-9\+\/]+)=*\?=';

## &bodydecode $B$,;H$&=hM};D$7%G!<%?MQ%P%C%U%!(B
$bdebuf = "";

## mimedecode interface ##
sub main'mimedecode {
    local($_, $kout) = @_;
    1 while s/($match_mime)[ \t]*\n?[ \t]+($match_mime)/$1$3/o;
    s/$match_mime/&kconv(&base64decode($1))/geo;
    s/(\x1b[\$\(][BHJ@])+/$1/g;
    1 while s/(\x1b\$[B@][\x21-\x7e]+)\x1b\$[B@]/$1/;
    1 while s/(\x1b\([BHJ][\t\x20-\x7e]+)\x1b\([BHJ]/$1/;
    s/^([\t\x20-\x7e]*)\x1b\([BHJ]/$1/;
    $_;
}

## bodydecode interface ##
sub main'bodydecode {
    local($_, $coding) = @_;
    if (!defined($coding) || $coding eq "" || $coding eq "b64"){
	s/[^A-Za-z0-9\+\/\=]//g;
	$_ = $bdebuf . $_;
	local($cut) = int((length)/4)*4;
	$bdebuf = substr($_, $cut+$[);
	$_ = substr($_, $[, $cut);
	&base64decode($_);
    }elsif ($coding eq "qp"){
	&qpdecode($_);
    }
}

## &bdeflush interface ##
sub main'bdeflush {
    local($coding) = @_;
    local($ret) = "";
    if ((!defined($coding) || $coding eq "" || $coding eq "b64")
	&& $bdebuf ne ""){
        $ret = &base64decode($bdebuf);
        $bdebuf = "";
    }
    $ret;
}

## BASE64 $B%G%3!<%G%#%s%0(B
sub base64decode {
    local($bin) = @_;
    $bin = join('', @code{split(//, $bin)});
    $bin = pack("B".(length($bin)>>3<<3), $bin);
}

## Quoted-Printable $B%G%3!<%G%#%s%0(B
sub qpdecode {
    local($qptxt) = @_;

    # $B%=%U%H2~9T$r:o=|(B
    $qptxt =~ s/=\r\n//g;
    $qptxt =~ s/=\n//g;
    $qptxt =~ s/=\r//g;

    # $BITE,@Z$J>l=j$K(B`='$B$,$"$k>l9g$OI8=`%(%i!<=PNO$K%a%C%;!<%8$r=PNO$9$k(B
    if ($qptxt =~ /=[^0-9A-Za-z]/){
	print STDERR "[MIME::qpdecode] Illegal '=' character exists.\n";
    }

    # 16$B?JI=8=$NI|85(B
    $qptxt =~ s/=([0-9A-Fa-f]{2})/pack("C",hex($1))/ge;

    $qptxt;
}

## $B4A;z%3!<%IJQ49(B(JIS to EUC/SJIS)
sub kconv {
    local($_) = @_;
    if ($kout eq "EUC"){
        s/$match_jis/&j2e($1)/geo;
        s/$match_ascii/$1/go;
    }
    elsif ($kout eq "SJIS"){
        s/$match_jis/&j2s($1)/geo;
        s/$match_ascii/$1/go;
    }
    $_;
}

## 7bit JIS $B$r(B EUC $B$KJQ49(B
sub j2e {
    local($_) = @_;
    tr/\x21-\x7e/\xa1-\xfe/;  # for original perl (or jperl -Llatin)
#   tr/\x21-\x7e/\xa1-\xfe/B; # for jperl
    $_;
}

## 7bit JIS $B$r(B Shift-JIS $B$KJQ49(B
sub j2s {
    local($string);
    local(@ch) = split(//, $_[0]);
    while(($j1,$j2) = unpack("CC", shift(@ch).shift(@ch))){
        if ($j1 % 2) {
            $j1 = ($j1>>1) + ($j1 >= 0x5f ? 0xb1 : 0x71);
            $j2 += ($j2 > 0x5f ? 0x20 : 0x1f);
        }else {
            $j1 = ($j1>>1) + ($j1 > 0x5f ? 0xb0 : 0x70);
            $j2 += 0x7e;
        }
        $string .= pack("CC", $j1, $j2);
    }
    $string;
}
1;
