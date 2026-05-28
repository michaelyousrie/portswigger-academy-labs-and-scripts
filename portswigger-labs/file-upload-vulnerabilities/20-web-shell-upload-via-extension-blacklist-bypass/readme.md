# Web Shell Upload via Extension Blacklist Bypass

[Lab Link](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/insufficient-blacklisting-of-dangerous-file-types/file-upload/lab-file-upload-web-shell-upload-via-extension-blacklist-bypass)

**Difficulty:** Practitioner

**Objective:** Upload a PHP web shell by bypassing the extension blacklist and use it to exfiltrate the contents of `/home/carlos/secret`.

This lab is best watched on the YouTube video where we go through the full thought process in real time.

[YouTube Video](https://youtu.be/8cG-gGR1bQs)

## Lab Overview

This lab has a file upload function that blacklists certain file extensions like `.php`. However, the server runs Apache and allows `.htaccess` file uploads, which introduces a fundamental flaw: we can override the server's configuration for the upload directory and define our own rules for how files are processed.

**Login credentials provided:** `wiener:peter`

## The Problem

Uploading a file with a `.php` extension is blocked. The server checks the extension and rejects it. But the blacklist only covers known dangerous extensions and misses a much bigger attack vector: Apache configuration files.

## The Exploit

### Step 1: Discover the Restriction

Upload a PHP webshell as your avatar. The server rejects it because `.php` is blacklisted. Checking the response headers confirms the server is running Apache.

### Step 2: Upload a Malicious .htaccess File

Apache allows per-directory configuration through `.htaccess` files. If we can upload one to the avatars directory, we can tell Apache to treat any extension we want as executable PHP.

Intercept the upload request in Burp Suite and modify it:
- Change the `filename` to `.htaccess`
- Change the `Content-Type` to `text/plain`
- Replace the file contents with:

        AddType application/x-httpd-php .l33t

This directive tells Apache to process any file with the `.l33t` extension as PHP code using the mod_php module.

Send the request. The server accepts it because `.htaccess` isn't on the blacklist.

### Step 3: Upload the Webshell with the Custom Extension

Now upload the PHP webshell again, but this time change the filename from `exploit.php` to `exploit.l33t`.

    <?php echo file_get_contents('/home/carlos/secret'); ?>

The server accepts it because `.l33t` isn't on the blacklist either. Why would it be? We just invented it.

### Step 4: Execute the Webshell

Access the uploaded file:

    GET /files/avatars/exploit.l33t

Apache reads the `.htaccess` file in the directory, sees that `.l33t` should be processed as PHP, executes our webshell, and returns Carlos's secret.

Submit the secret to solve the lab.

## Why This Worked

The entire attack chain relies on one fundamental flaw: the server allows `.htaccess` uploads.

Apache's `.htaccess` files override server configuration on a per-directory basis. By uploading one, we essentially rewrote the rules for how the upload directory handles files. The extension blacklist became irrelevant because we defined a brand new extension that Apache would execute as PHP.

The blacklist approach failed because:
1. It only blocked known dangerous extensions like `.php`
2. It didn't block `.htaccess`, which is arguably more dangerous than any single script extension
3. It couldn't anticipate custom extensions because we defined them after the blacklist was written

## Key Takeaways

- **Extension blacklists are fundamentally flawed.** You can never enumerate every dangerous extension, especially when attackers can create new ones through server configuration.

- **.htaccess uploads should always be blocked.** Allowing users to upload Apache configuration files gives them control over how the server processes everything in that directory. This is almost always more dangerous than any individual script upload.

- **Use allowlists, not blacklists.** Instead of blocking known-bad extensions, only allow known-good ones like `.jpg`, `.png`, `.gif`. Anything not explicitly permitted gets rejected.

- **Disable .htaccess processing in upload directories.** Apache can be configured to ignore `.htaccess` files in specific directories using `AllowOverride None` in the main server configuration.

- **Defense in depth is essential.** Even with a perfect extension filter, uploaded files should be stored outside the web root, renamed to random strings, and served through a separate handler that never executes them.

## Tools Used

- Burp Suite Community Edition