# Web Shell Upload via Path Traversal

[Web Shell Upload via Path Traversal](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/preventing-file-execution-in-user-accessible-directories/file-upload/lab-file-upload-web-shell-upload-via-path-traversal)

**Difficulty:** Practitioner

**Objective:** Upload a PHP web shell and use it to exfiltrate the contents of `/home/carlos/secret`, bypassing the server's file execution restrictions.

This lab is best watched on the YouTube video where we go through the full thought process in real time.

[YouTube Video](https://youtu.be/USQhD-6Svqk)

## Lab Overview

This lab has a file upload function for user avatars. Unlike previous file upload labs, this server is configured to prevent execution of user-supplied files in the upload directory. Simply uploading a PHP file and accessing it returns the source code as plain text rather than executing it. A secondary vulnerability needs to be exploited to bypass this restriction.

**Login credentials provided:** `wiener:peter`

## The Problem

The server accepts PHP uploads but the `/files/avatars/` directory is configured to not execute PHP files. When you upload a webshell and try to access it at `/files/avatars/exploit.php`, the server returns the raw PHP source code instead of executing it.

So the file is there, but it's useless in that directory. We need to get it somewhere the server will actually run it.

## The Exploit

### Step 1: Upload a PHP Webshell

Create a simple PHP file to read Carlos's secret:

    <?php echo file_get_contents('/home/carlos/secret'); ?>

Upload it as your avatar. The server accepts it without complaint, but accessing it at `/files/avatars/exploit.php` just shows the raw PHP code. No execution.

### Step 2: Attempt Path Traversal

The idea is to manipulate the filename so the file gets stored one directory up, in `/files/` instead of `/files/avatars/`. If `/files/` doesn't have the same execution restrictions, our PHP will run.

Intercept the upload request in Burp Suite and change the filename in the Content-Disposition header:

    Content-Disposition: form-data; name="avatar"; filename="../exploit.php"

The server responds with "The file avatars/exploit.php has been uploaded." It stripped the `../` from the filename. The traversal was blocked.

### Step 3: URL Encode the Traversal

The server is checking for `../` in the filename and removing it. But what if we encode the forward slash? URL encoding `/` gives us `%2f`.

Change the filename to:

    Content-Disposition: form-data; name="avatar"; filename="..%2fexploit.php"

The server responds with "The file avatars/../exploit.php has been uploaded." The traversal sequence survived this time. The server decoded the URL encoding after the security check, meaning the file was written to `/files/exploit.php` instead of `/files/avatars/exploit.php`.

### Step 4: Execute the Webshell

Access the file at its new location:

    GET /files/exploit.php

The server executes the PHP file and returns Carlos's secret. The `/files/` directory doesn't have the same execution restrictions as `/files/avatars/`.

Submit the secret to solve the lab.

## Why This Worked

Three things came together to make this exploit possible:

1. **Execution restrictions were directory-specific.** The server only blocked PHP execution in `/files/avatars/`, not in `/files/`. Moving the file one level up escaped the restriction.

2. **The path traversal filter was applied before URL decoding.** The server checked for `../` in the raw filename, stripped it, but then URL-decoded the result. By encoding just the `/` as `%2f`, the sequence `..%2f` bypassed the filter and was decoded to `../` afterward.

3. **No validation on where files are stored.** The server should verify that uploaded files end up in the intended directory regardless of what the client sends as a filename.

## Key Takeaways

- **Directory-level execution restrictions are not enough.** If an attacker can escape the restricted directory, the restriction is meaningless. Apply execution restrictions broadly, not just to the upload folder.

- **Security checks must happen after all decoding.** Checking input before URL decoding is a classic bypass pattern. Always normalize and decode input fully before applying security filters.

- **Never trust client-supplied filenames.** The server should generate its own filename for uploaded files rather than using anything from the Content-Disposition header. Strip path separators entirely, don't just filter for known patterns.

- **Defense in depth matters.** If the server had also validated the final storage path to ensure it was within the expected directory, the path traversal would have failed even with the encoding bypass.

## Tools Used

- Burp Suite Community Edition