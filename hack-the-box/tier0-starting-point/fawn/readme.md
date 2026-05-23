# Fawn

[Fawn Machine](https://app.hackthebox.com/machines/Fawn)

This machine is best watched on the YouTube video because we go through a bunch of concepts and other useful topics throughout this machine.

[YouTube Video](https://youtu.be/cDFwk_RP2fo)

# Questions

> What does the 3-letter acronym FTP stand for?
>> File Transfer Protocol

> Which port does the FTP service listen on usually?
>> 21

> FTP sends data in the clear, without any encryption. What acronym is used for a later protocol designed to provide similar functionality to FTP but securely, as an extension of the SSH protocol?
>> SFTP

> What is the command we can use to send an ICMP echo request to test our connection to the target?
>> ping

> From your scans, what version is FTP running on the target?
>> vsftpd 3.0.3

> From your scans, what OS type is running on the target?
>> Unix

> What is the command we need to run in order to display the 'ftp' client help menu?
>> ftp -?

> What is username that is used over FTP when you want to log in without having an account?
>> anonymous

> What is the response code we get for the FTP message 'Login successful'?
>> 230

> There are a couple of commands we can use to list the files and directories available on the FTP server. One is dir. What is the other that is a common way to list files on a Linux system.
>> ls

> What is the command used to download the file we found on the FTP server?
>> get

# Getting flag value

- You need to ftp into the machine's server using anonymous login.
```bash
$ ftp ${MACHINE IP}
```

- When prompted for a username, input `anonymous`

- For a password, input nothing. So just press `enter`.

- You'll be logged in. Once in there, just download the `flag.txt` file which will be sitting in the same directory that you logged in to.
```bash
$ get flag.txt
```

- Disconnect from FTP and run a `cat flag.txt` on your local machine. You'll get the value of the flag successfully.