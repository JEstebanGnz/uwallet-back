<!DOCTYPE html>
<html>
<head>
    <title>Google 2FA Validation</title>
</head>
<body>
<h1>Validar OTP</h1>
<form method="POST" action="{{route('2fa.validate') }}">
    @csrf
    <label for="otp">One Time Password</label>
    <input type="text" name="otp" id="otp" required>
    <button type="submit">Aceptar</button>
</form>
@if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</body>
</html>
