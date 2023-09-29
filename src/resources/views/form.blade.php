<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Example</title>
</head>
<body>
    @if(session('success'))
        <div>{{ session('success') }}</div>
    @endif

    <form action="/form" method="post">
        @csrf
        <label>
            Url:
            <input type="text" name="url" required>
        </label>
        <br>
        <label>
            Method
            <input type="text" name="method" required>
        </label>
        <br>
        <label>
             Body:
            <input type="text" name="body" required>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
