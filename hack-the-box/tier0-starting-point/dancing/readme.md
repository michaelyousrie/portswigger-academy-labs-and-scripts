# Dancing

[Dancing Machine](https://app.hackthebox.com/machines/Dancing)

This machine is best watched on the YouTube video because we go through a bunch of concepts and other useful topics throughout this machine.

[YouTube Video](https://youtu.be/jk2jO6NfCRk)

# Questions

> What does the 3-letter acronym SMB stand for?
>> Server Message Block

> What port does SMB use to operate at?
>> 445

> What is the service name for port 445 that came up in our Nmap scan?
>> microsoft-ds

> What is the 'flag' or 'switch' that we can use with the smbclient utility to 'list' the available SMB shares on Dancing?
>> -L

> How many shares are there on Dancing?
>> 4

> What is the name of the share we are able to access in the end with a blank password?
>> WorkShares

> What is the command we can use within the SMB shell to download the files we find?
>> get

# Getting flag value

- Start by scanning the target machine with Nmap to identify open ports and services.
```bash
$ nmap -sV ${MACHINE_IP}
```

- Use smbclient to list available shares on the target.
```bash
$ smbclient -L ${MACHINE_IP}
```

- Connect to the WorkShares share using a blank password.
```bash
$ smbclient //${MACHINE_IP}/WorkShares
```

- When prompted for a password, input nothing. Just press `enter`.

- Navigate through the directories to find the flag file.
```bash
smb: \> ls
smb: \> cd James.P
smb: \> ls
smb: \> get flag.txt
```

- Disconnect from SMB and run `cat flag.txt` on your local machine to get the flag value.