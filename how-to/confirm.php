<?php
// get the confirmation status
if(isset($_POST['code'])){
    session_start();
    if($_SESSION['code'] == $_POST['code']){
        $text = "Your phone number has been confirmed.";
    } else {
        $text = "Sorry that code could not be verified.";
    }
}

// render the page ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="http://nexmo.com/favicon.ico">

    <title>Phone Verification Example</title>

    <!-- Bootstrap core CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div class="container">


    <form class="form-verify" role="form" method="POST" action="confirm.php">
        <h2 class="form-verify-heading">Confirm Your Code</h2>
        <input name="code" type="text" class="form-control" placeholder="Confirm code" required autofocus>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Confirm</button>
        <?php if(isset($text)): ?>
            <div class="alert alert-info" role="alert">
                <p><?php echo $text ?></p>
            </div>
        <?php endif; ?>
    </form>
</div> <!-- /container -->
</body>
</html>


