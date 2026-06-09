<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Redirecting to PayFast…</title>
</head>
<body onload="document.forms[0].submit()" style="font-family: sans-serif; text-align:center; padding-top:80px;">
    <p>Redirecting you to PayFast to complete payment…</p>
    <form action="{{ $action }}" method="post">
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <noscript><button type="submit">Continue to PayFast</button></noscript>
    </form>
</body>
</html>
