<?php
session_start();

if (!empty($_SESSION['transfer_success']) || !empty($_SESSION['transfer_error'])) {
    unset($_SESSION['transfer_success'], $_SESSION['transfer_error']);
}
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilinsiirto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background: #f5f5f5;
        }
        h1 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        label {
            display: block;
            margin-top: 0.75rem;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 0.4rem;
            margin-top: 0.25rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Tilinsiirto</h1>

    <form action="process_transfer.php" method="post">
        <label for="amount">Siirrettävä summa (euroa):</label>
        <input type="number" step="0.01" min="0.01" name="amount" id="amount" required>

        <label for="from_account">Veloitettava tilinumero:</label>
        <input type="text" name="from_account" id="from_account" required>

        <label for="to_account">Tilinumero, jonne summa siirretään:</label>
        <input type="text" name="to_account" id="to_account" required>

        <button type="submit">Suorita tilinsiirto</button>
    </form>
</body>
</html>


