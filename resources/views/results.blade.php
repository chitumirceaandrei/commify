<!DOCTYPE html>
<html>
<head>
    <title>Calculation Results</title>
    <style>
        body {
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333333;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-weight: bold;
        }
        input[type="number"] {
            width: 80%;
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #0056b3;
        }
        hr {
            margin: 40px 0;
        }
        p {
            font-size: 18px;
            color: #555555;
        }
        p strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Calculation Results</h1>
        <form action="{{ route('calculate') }}" method="POST">
            @csrf
            <label for="gross_salary">Gross Annual Salary:</label><br>
            <input type="number" name="gross_salary" id="gross_salary" value="{{ $grossSalary }}" step="0.01" required><br>
            <button type="submit">Calculate</button>
        </form>
        <hr>
        <p><strong>Gross Annual Salary:</strong> £ {{ number_format($grossSalary, 2) }}</p>
        <p><strong>Gross Monthly Salary:</strong> £ {{ number_format($grossMonthlySalary, 2) }}</p>
        <p><strong>Net Annual Salary:</strong> £ {{ number_format($netAnnualSalary, 2) }}</p>
        <p><strong>Net Monthly Salary:</strong> £ {{ number_format($netMonthlySalary, 2) }}</p>
        <p><strong>Annual Tax Paid:</strong> £ {{ number_format($taxPaid, 2) }}</p>
        <p><strong>Monthly Tax Paid:</strong> £ {{ number_format($monthlyTaxPaid, 2) }}</p>
    </div>
</body>
</html>
