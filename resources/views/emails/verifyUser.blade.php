<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>
 
<body>
<h2>Welcome to the site</h2>
<br/>
Your registered email is {{$user['email']}} , Please click on the below link to verify your email account
<br/>
<a href="{{url('user/verify/'. $user['id'], $token)}}">Verify Email</a>
</body>
 
</html>