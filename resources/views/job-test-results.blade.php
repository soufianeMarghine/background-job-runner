<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Test Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Job Test Results</h1>

    @foreach ($results as $result)
        <p class="{{ $result['type'] }}">{{ $result['message'] }}</p>
    @endforeach
</body>
</html>
