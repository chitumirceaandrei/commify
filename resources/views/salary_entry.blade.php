<!DOCTYPE html>
<html>
<head>
    <title>Salary Entry</title>
    <meta name="description" content="Enter your gross annual salary to calculate your net annual salary.">
    <style>
        /* Modern styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            width: 400px;
            margin: 100px auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #dfe1e5;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0069d9;
        }
        .error {
            color: red;
            margin-top: 20px;
            text-align: center;
        }
        p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="{{ route('calculate') }}" method="POST">
            @csrf
            <label for="gross_salary">Gross Annual Salary:</label>
            <input type="number" name="gross_salary" id="gross_salary" step="0.01" required>
            <button type="submit">Calculate</button>
        </form>
        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
