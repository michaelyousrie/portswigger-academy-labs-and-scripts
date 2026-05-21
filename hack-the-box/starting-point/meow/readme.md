# Meow machine
[Meow Machine Link](https://app.hackthebox.com/machines/Meow)

# Thought Process

Since this is the very first machine in HTB, it'll be super simple. We just have to answer a few basic questions and get a very simple flag.

> This machine is super simple and is not worth a YouTube video.

# Machine Questions


> What does the acronym VM stand for?
>> Virtual Machine

> What tool do we use to interact with the operating system in order to issue commands via the command line, such as the one to start our VPN connection? It's also known as a console or shell.
>> terminal

> What service do we use to form our VPN connection into HTB labs?
>> openvpn

> What tool do we use to test our connection to the target with an ICMP echo request?
>> ping

> What is the name of the most common tool for finding open ports on a target?
>> nmap

> What service do we identify on port 23/tcp during our scans?
>> telnet
>>> Note: To get the answer for this, you have to execute `nmap {IP-OF-MACHINE}` which will show open ports and services on that machine

> What username is able to log into the target over telnet with a blank password?
>> root

> Submit root flag
>>> - The flag value will be different. To solve it, you can just `telnet {IP-OF-MACHINE}`
>>> - then you'll have to type the escape character by holding `CTRL+]` which will enter you to a telnet prompt.
>>> - Then you can type `CTRL+C` which will prompt you to login, just enter the text `root` which will log you in.
>>> - From there, just do a simple `cat flag.txt` which will output the value of the root flag.