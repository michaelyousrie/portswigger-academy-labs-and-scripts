# Lab: SSRF with blacklist-based input filter

[Lab: SSRF with blacklist-based input filter](https://portswigger.net/web-security/learning-paths/ssrf-attacks/ssrf-attacks-circumventing-defenses/ssrf/lab-ssrf-with-blacklist-filter)

[YouTube Video](https://youtu.be/tL_4JD1-n5o)

# Writeup

## Lab Overview

This lab has a stock check feature that fetches data from an internal system. The developer has deployed two blacklist-based anti-SSRF defenses:

1. Blocking common localhost addresses (`localhost`, `127.0.0.1`)
2. Blocking the lowercase string `admin` in the URL path

The goal is to bypass both filters, access the admin panel, and delete the user `carlos`.

## The Defenses

### Defense 1: Localhost Blocking

The application blocks requests containing `localhost` and `127.0.0.1`. This is a common but weak SSRF mitigation because there are many alternative ways to represent the loopback address.

### Defense 2: Path Blocking

The application blocks requests containing the lowercase string `admin` in the URL path. This prevents direct access to the admin interface.

## The Bypass

Both defenses are blacklist-based, which means they only block specific known patterns. Anything not explicitly on the blacklist gets through.

### Bypassing the Localhost Block

Instead of `127.0.0.1` or `localhost`, we use the shortened IP notation:

    127.1

This is a valid IP address that resolves to `127.0.0.1`. Most systems interpret `127.1` as `127.0.0.1` but the blacklist doesn't account for this representation.

Other alternatives that often work:
- `127.0.1`
- `127.000.000.001`
- `2130706433` (decimal representation)
- `0x7f000001` (hex representation)
- `017700000001` (octal representation)

### Bypassing the Admin Path Block

The blacklist checks for the lowercase string `admin`. Simply changing the case bypasses it:

    ADMIN

URL paths are handled by the web server, and most web servers treat paths as case-insensitive. So `/ADMIN` routes to the same place as `/admin`, but the blacklist only catches the lowercase version.

## The Solution

The final payload sent through the stock check feature:

    http://127.1/ADMIN/delete?username=carlos

This single request bypasses both defenses simultaneously:
- `127.1` bypasses the localhost blacklist
- `ADMIN` bypasses the path blacklist

## Steps to Reproduce

1. Navigate to any product page and click "Check stock"
2. Intercept the request with Burp Suite
3. Find the `stockApi` parameter in the POST request
4. Change the value to `http://127.1/ADMIN`
5. Confirm you can see the admin panel in the response
6. Change the value to `http://127.1/ADMIN/delete?username=carlos`
7. Forward the request
8. Lab solved

## Key Takeaways

- **Blacklists are almost always bypassable.** There are too many ways to represent the same resource. Attackers only need to find one that isn't on the list.

- **Allowlists are better than blacklists.** Instead of blocking known-bad patterns, only permit known-good patterns. If the stock check only needs to reach specific internal hosts, only allow those exact URLs.

- **IP addresses have many representations.** `127.0.0.1`, `127.1`, `2130706433`, `0x7f000001`, and `localhost` all point to the same place. A blacklist that doesn't account for all of them is useless.

- **Case sensitivity matters.** If your filter is case-sensitive but your web server is case-insensitive, the filter is trivially bypassed.

## Tools Used

- Burp Suite Community Edition