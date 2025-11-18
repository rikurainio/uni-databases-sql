<?php
session_start();
$error = $_SESSION['transfer_error'] ?? null;
$success = !empty($_SESSION['transfer_success']);
$amount = $_SESSION['amount'] ?? null;
$from_owner = $_SESSION['from_owner'] ?? null;
$to_owner = $_SESSION['to_owner'] ?? null;

unset(
    $_SESSION['transfer_error'],
    $_SESSION['transfer_success'],
    $_SESSION['amount'],
    $_SESSION['from_owner'],
    $_SESSION['to_owner'],
    $_SESSION['from_account'],
    $_SESSION['to_account']
);
?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Tilinsiirron tulos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background: #f5f5f5;
        }
        .box {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="box">
        <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php elseif ($success && $from_owner && $to_owner && $amount !== null): ?>
            <div class="success">
                <?php
                $from_owner_esc = htmlspecialchars($from_owner, ENT_QUOTES, 'UTF-8');
                $to_owner_esc = htmlspecialchars($to_owner, ENT_QUOTES, 'UTF-8');
                $amount_formatted = number_format((float)$amount, 2, ',', ' ');
                echo "{$from_owner_esc} on siirtänyt {$amount_formatted} euroa henkilölle {$to_owner_esc}.";
                ?>
            </div>
        <?php else: ?>
            <div class="error">
                Tilinsiirron tietoja ei löytynyt. Yritä uudelleen.
            </div>
        <?php endif; ?>

        <a href="index.php">Takaisin tilinsiirtolomakkeelle</a>
    </div>
</body>
</html>


