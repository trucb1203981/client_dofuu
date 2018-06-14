<!DOCTYPE html>
<html>
<head>
	<title>Welcome Email</title>
</head>

<body>
	<h2>Kính chào {{$user['name']}}</h2>
	<br/>
	Bạn đã đăng ký tài khoản là {{$user['email']}} , Vui lòng chọn đường dẫn bên dưới để kích hoạt tài khoản
	<br/>
	<a href="{{url('http://dofuu.com/login/activation/').$user->active->token}}">Verify Email</a>
	<br/>
	<h6>Trân trọng</h6>
</body>

</html>