<!DOCTYPE html>
<html lang="es">
<head>
    <title>Google 2FA Setup</title>
</head>
<body>
<h1>Set up Google 2FA</h1>
<p>Enter the following secret key into your Google Authenticator app:</p>
<p><strong>{{ $secret }}</strong></p>

<form method="GET" action="{{route('2fa.validate.view') }}">
    @csrf
    <label for="otp">One Time Password</label>
    <input type="text" name="otp" id="otp" hidden="hidden">
    <button type="submit">Go to API ROUte</button>
</form>

{{--<button onclick="window.location.href='{{ route('2fa.validate.view') }}'">Go to API Route</button>--}}
</body>
</html>



