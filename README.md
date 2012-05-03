Simple number verification/second factor authentication service.

Send an SMS with the PIN, and get a hash to verification:

Request: `POST /?number=13215551212`
Response: `{"hash":"0021e28230ff8ba40165e595ef7cbe21214b34d6"}`

Verify the correct PIN:

Request: `GET /?hash=0021e28230ff8ba40165e595ef7cbe21214b34d6&number=13215551212&pin=7901`
Response: `{"valid":true}`

Verify an incorrect PIN:

Request: `GET /?hash=0021e28230ff8ba40165e595ef7cbe21214b34d6&number=13215551212&pin=7900`
Response: `{"valid":false}`
