# Cap

[Cap Machine](https://app.hackthebox.com/machines/Cap)

This machine is best watched on the YouTube video where we go through the full thought process, including getting stuck and working through it in real time.

[YouTube Video](https://youtu.be/WIEzrwdA64c)

## Questions

> How many TCP ports are open?
>> 3

> After running a "Security Snapshot", the browser is redirected to a path of the format /[something]/[id], where [id] represents the id number of the scan. What is the [something]?
>> data

> Are you able to get to other users' scans?
>> yes

> What is the ID of the PCAP file that contains sensitive data?
>> 0

> Which application layer protocol in the pcap file can the sensitive data be found in?
>> ftp

> We've managed to collect nathan's FTP password. On what other service does this password work?
>> ssh

> What is the full path to the binary on this machine has special capabilities that can be abused to obtain root privileges?
>> /usr/bin/python3.8

## Attack Chain Summary

This box demonstrates a realistic attack chain where multiple small vulnerabilities are chained together to achieve full system compromise.

### Phase 1: Enumeration

- Start with an Nmap scan to discover open ports.

        nmap -sV -sC ${MACHINE_IP}

- Three ports are open: FTP (21), SSH (22), and HTTP (80).
- The web application has a "Security Snapshot" feature that captures network traffic and stores the results at `/data/[id]`.

### Phase 2: IDOR on PCAP Files

- After running a security snapshot, the application redirects to `/data/[id]` where the ID is an incrementing number.
- By changing the ID in the URL, we can access other users' packet captures.
- Navigating to `/data/0` reveals a PCAP file that contains sensitive data.

### Phase 3: Credential Extraction

- Download the PCAP file from `/data/0` and open it in Wireshark.
- Filter for FTP traffic. The capture contains an FTP login session with credentials sent in cleartext.
- Extract nathan's username and password from the FTP authentication packets.

### Phase 4: SSH Access

- Nathan reused his FTP password for SSH. Log in using the extracted credentials.

        ssh nathan@${MACHINE_IP}

- The user flag is in nathan's home directory.

        cat ~/user.txt

### Phase 5: Privilege Escalation

- Check for Linux binaries with special capabilities.

        getcap -r / 2>/dev/null

- `/usr/bin/python3.8` has the `cap_setuid` capability, which allows it to change the process UID.
- This means Python can set its UID to 0 (root) and spawn a root shell.

        /usr/bin/python3.8 -c 'import os; os.setuid(0); os.system("/bin/bash")'

- We are now root. Read the root flag.

        cat /root/root.txt

## Key Takeaways

- **IDOR vulnerabilities expose other users' data.** The application used sequential IDs with no authorization checks, allowing access to any user's packet capture.

- **FTP transmits credentials in cleartext.** This is why FTP should never be used for sensitive data. SFTP or SCP should be used instead.

- **Credential reuse is devastating.** Nathan used the same password for FTP and SSH. One compromised service gave access to another.

- **Linux capabilities can be as dangerous as SUID.** The `cap_setuid` capability on Python allowed us to escalate to root without needing a traditional SUID binary exploit.

- **Always check capabilities.** Running `getcap -r /` is a standard privilege escalation check that many people forget. It should be part of every Linux enumeration checklist.

## Tools Used

- Nmap
- Wireshark
- SSH
- Python3 (for privilege escalation)