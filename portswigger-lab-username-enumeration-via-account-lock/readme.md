# Lab: Username enumeration via account lock

[Lab: Username enumeration via account lock](https://portswigger.net/web-security/learning-paths/authentication-vulnerabilities/password-based-vulnerabilities/authentication/password-based/lab-username-enumeration-via-account-lock#)


This lab is vulnerable to username enumeration by blocking the user from trying to sign in ONLY if the username is correct but the user has made 5 incorrect logins in a row.

We can easily use this knowledge to brute-force the login mechanism by sending the same username 5 times in a row with any random fake password that we know is incorrect. We're just looking at the response from the server that tells us `you've been blocked for doing too many incorrect logins` as when the server sends this response, we'll know we have a correct username.

Using this username, we can simply just do a dictionary attack on the password by sending the same username but with a list of passwords that portswigger provides us (and provided in the passwords.txt file in this folder)

## The script

The script included in this simply duplicates each username 5 times and saves it to a file (usernames.txt file in this folder) as we want to try each username 5 times to see if we get blocked or not.

## Extra tip

You can setup an autopause feature in burp suite in the `intruder` module where burp suite will pause the attack even if unfinished once a configured string has been found in the response. Check the settings menu in the `intruder` module to see more cool options in Burp Suite!


### The solution (SPOILERS)

Too lazy to do it yourself? The solution for me was

<details>
  <summary>Spoiler alert. the solution for me was:</summary>
  ai:1111
</details>