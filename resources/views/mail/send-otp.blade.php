<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP</title>
</head>
<body>
   <h2> @if($otp->otp_type === 'signup') Email Verification @else OTP for forgot password @endif</h2>
   <p> Hi {{ $user->name }}!  </p> 
   <p> {{$otp->otp}} is your otp, this otp is valid for 30 mintues only. </p>
</body>
</html>