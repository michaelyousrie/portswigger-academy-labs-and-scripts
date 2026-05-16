# WARNING

This repo contains *SPOILERS* for Port Swigger web security academy and solutions to the labs there. Be warned before reading any further.

## This repo is all about my journey through Port Swigger's Academy

I'm learning Web Security through [Port Swigger's Academy](https://portswigger.net/web-security/) and I deemed it cool that I add all the helper scripts that I generate along the way, as well as some write ups about each lab as I go through it.


## Why?

Because I love cybersecurity and I love sharing my findings with the community. I learnt from the community, and it's only fair that I give back to the community.

### Prep work

I've added [foxy-proxy](https://chromewebstore.google.com/detail/foxyproxy/gcknhkkoolaabfmlnjonogaaifnjlfnp) to my chrome browser (also available in [firefox](https://addons.mozilla.org/en-US/firefox/addon/foxyproxy-standard/)) and added burp suite as a proxy there. Burp Suite runs on `127.0.0.1:8080` by default without a username or a password. So it should be pretty simple to add those details in foxy proxy.

The trick here is to export the burp certificate and add this to your browser so browsers don't freak out about https traffic going through burp suite. [This guide shows you how to do that](https://portswigger.net/burp/documentation/desktop/external-browser-config/certificate/ca-cert-chrome-windows)

With this done, I can easily move all of my chrome traffic through my burp suite which allows me to log, inspect, dissect, analyze and manipulate HTTP traffic super easily. Super useful for doing these labs.