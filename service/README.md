# Simple number verification/second factor authentication service.

## Usage

Send an SMS with the PIN, and get a hash to verification:

Request: `POST /?number=13215551212`
Response: `{"hash":"0021e28230ff8ba40165e595ef7cbe21214b34d6"}`

Verify the correct PIN:

Request: `GET /0021e28230ff8ba40165e595ef7cbe21214b34d6?number=13215551212&pin=7901`
Response: `{"valid":true}`

Verify an incorrect PIN:

Request: `GET /0021e28230ff8ba40165e595ef7cbe21214b34d6?number=13215551212&pin=7900`
Response: `{"valid":false}`

## Installation

- Copy source to a web root.
- Add Nexmo credentials through environment variables, or `define()`
- Route all requests to `index.php` (in many cases this will happen by default; if not, `index.php` can be prepended
  to the `GET` requests). 

## Security

This is a simplistic example, and the method used to generate/verify the code is not at all random. If the server 
side key is known, it's relatively easy to brute force the pin. This method works well only as an example that avoids
any storage of the pin. **You should not use this in production without replacing the pin generation/verification.**
