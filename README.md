# [How-To] Number Verification & 2FA

## Use Case
Verifying that a user owns a phone number has a few similar uses. A common mobile application use case is a one time 
verification on registration or installation. 

First the mobile application collects the number for the user or identifies it automatically. Then a code is sent to 
the number via SMS, and the application either prompts the user to provide that code, or monitors the message inbox to 
verify the code automatically.

The same basic process can be used to add second factor authentication (2FA) to a login system - although now the 
verification flow happens on each login. 

Adding a second factor to a login system, in this case a mobile phone, increases security as a physical device is 
needed along with the username and password.

When a user attempts to login, a code is sent to their phone, and they're prompted for that code to successfully 
complete the login.

## How-To
We'll build a simple verification web interface using two PHP scripts.

The first will accept a form `POST` with the phone number to be verified. When a number is received, a unique code will 
be generated, and stored in the current session.

    session_start();
    $number = $_POST['number'];
    $code = rand(1000, 9999); // random 4 digit code
    $_SESSION['code'] = $code; // store code for later
    
[*View in Context*](https://github.com/Nexmo/Verify/blob/master/how-to/index.php#L5-L8)

We then send the code to the user by making an HTTP call to the Nexmo API.
    
    $url = 'https://rest.nexmo.com/sms/json?' . http_build_query(array(
            'api_key' => NEXMO_KEY,
            'api_secret' => NEXMO_SECRET,
            'from' => NEXMO_FROM,
            'to' => $number,
            'text' => 'Your verification code is: ' . $code
        ));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
[*View in Context*](https://github.com/Nexmo/Verify/blob/master/how-to/index.php#L10-L21)

The second script will accept a form `POST` with the user provided code, and verify that it matches the one set in the 
first step. 

    if(isset($_POST['code'])){
        session_start();
        if($_SESSION['code'] == $_POST['code']){
            $text = "Your phone number has been confirmed.";
        } else {
            $text = "Sorry that code could not be verified.";
        }
    }
    
[*View in Context*](https://github.com/Nexmo/Verify/blob/master/how-to/confirm.php#L3-L10)

Now we just add a little HTML to tie all this to a simple user interface [and complete the example](https://github.com/Nexmo/Verify/tree/master/how-to).

## Next Steps
If this were a login page, the user wouldn't be prompted for a phone number, that would already be associated with 
their login credentials. 

As with any example of a security related process, there are concerns not addressed by this how-to. Session 
configurations vary, and depending on the situation may not be a secure way to store the verification code. While 
`rand()` is a simple way to get a random number, it's only psudo-random. 

Before you implement a number verification routine, do you own research into the security implications involved.

If you're verifying a number from a mobile device, it's better not to make the requests directly directly from the 
mobile application. Doing that requires you to compile your API credentials into the application, as well as send the 
verification code from the device itself - risking a malicious user intercepting the code. 

In that case, build the number verification as a separate web service that your mobile application uses. 

## Demo Service
Along with the simple verification web interface, [there's a demo verification service built as an API itself](https://github.com/Nexmo/Verify/tree/master/service). 

The same [security warnings apply](https://github.com/Nexmo/Verify/tree/master/service#security), 
this is a simple example designed to show how a verification service would work.

Get the demo running on a [PHP server](https://github.com/Nexmo/Verify/tree/master/service#installation), 
then use the [service to verify a number](https://github.com/Nexmo/Verify/tree/master/service#usage).