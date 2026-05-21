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

### Common tools

I use a couple of online tools across this entire series. So here are the links for each tool here in case I forget to link to the tool during my writeup (I actually write these READMEs as I'm actively solving the lab so I might forget).

- [CrackStation](https://crackstation.net) is used to check hashes and try to crack them using a precomputed hash list.

- [CyberChef](https://gchq.github.io/CyberChef) is kinda a do-it-all string manipulation tool where you can cook up a few string manipulation, including hasing and encrypting, recipes and then it'll show you the results. Pretty useful.

- [MD5 Center](https://md5.gromweb.com/) is an MD5 decryptor that tries to decrypt and MD5 hash back to its original form.

- [Yoink!](https://yoink.michaelyousrie.com) is a shameless plug of my own *TOTALLY FREE BTW* HTTP logger tool. That's all it does really, just logs a lot of useful information about an HTTP request that is sent its way.