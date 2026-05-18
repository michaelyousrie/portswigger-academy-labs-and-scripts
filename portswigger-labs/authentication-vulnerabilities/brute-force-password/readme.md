# Lab: Broken brute-force protection, IP block

[Lab: Broken brute-force protection, IP block](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/password-based-vulnerabilities/authentication/password-based/lab-broken-bruteforce-protection-ip-block)


# SPOILER ALERT

This lab is aimed to exploit a weakness in the implemented anti brute force mechanism which basically blocks your IP address from performing login requests for 1 minute if you do 3 incorrect passwords in a row. However, the weakness is in the implementation that whenever a successful login happens, the failed attempts reset.

We can so easily exploit this by successfully logging in once after 2 failed login attempts.

This is why the usernames and passwords txt files have repeated `wiener` and `peter` (which are correct credentials for the user that the lab gives you) entries after every 2 attempts.

## What's included

A list of usernames and passwords that I used in Burp Suite to solve the lab in the correct order as well as the PHP script I used to generate the txt files.