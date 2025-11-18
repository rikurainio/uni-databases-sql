<?php
session_start();

require_once __DIR__ . '/db_config.php';

if (!empty($_SESSION['transfer_success']) || !empty($_SESSION['transfer_error'])) {
    unset($_SESSION['transfer_success'], $_SESSION['transfer_error']);
}

$accounts = [];
$db_error = null;

try {
    $conn = get_db_connection();
    $result = pg_query($conn, 'SELECT tilinumero, omistaja, summa FROM TILIT ORDER BY omistaja');
    if ($result) {
        $accounts = pg_fetch_all($result) ?: [];
    } else {
        $db_error = 'Tilien hakeminen epäonnistui.';
    }
    pg_close($conn);
} catch (Throwable $e) {
    $db_error = 'Tietokantavirhe: ' . $e->getMessage();
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
        .accounts {
            margin-top: 2rem;
            max-width: 600px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 0.6rem;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .account-number {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .copy-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            padding: 0.2rem;
            border-radius: 4px;
            transition: background 0.2s;
            position: relative;
        }
        .copy-btn:hover {
            background: rgba(0, 123, 255, 0.15);
        }
        .copy-btn:focus {
            outline: 2px solid rgba(0, 123, 255, 0.5);
        }
        .copy-btn svg {
            width: 18px;
            height: 18px;
            stroke: #007bff;
            stroke-width: 2;
            fill: none;
        }
        .copy-toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: #007bff;
            color: #fff;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
            z-index: 1000;
        }
        .copy-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 0.75rem;
            border-radius: 4px;
            margin-top: 1rem;
            max-width: 500px;
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

    <div class="accounts">
        <h2>Esimerkkitilit</h2>
        <?php if ($db_error): ?>
            <div class="error"><?php echo htmlspecialchars($db_error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php elseif (empty($accounts)): ?>
            <p>Ei tilitietoja.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Tilinumero</th>
                        <th>Omistaja</th>
                        <th>Saldo (EUR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <tr>
                            <td>
                                <div class="account-number">
                                    <span><?php echo htmlspecialchars($account['tilinumero'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <button
                                        class="copy-btn"
                                        type="button"
                                        data-copy="<?php echo htmlspecialchars($account['tilinumero'], ENT_QUOTES, 'UTF-8'); ?>"
                                        aria-label="Kopioi tilinumero"
                                    >
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                            <path d="M5 15H4a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2h9a2 2 0 0 1 2 2v1"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($account['omistaja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo number_format((float)$account['summa'], 2, ',', ' '); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="copyToast" class="copy-toast" role="status" aria-live="polite">Kopioitu</div>

    <script>
        const toast = document.getElementById('copyToast');
        let toastTimeout;

        function showToast(message) {
            toast.textContent = message;
            toast.classList.add('show');

            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }

        document.querySelectorAll('.copy-btn').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const text = btn.dataset.copy;
                try {
                    await navigator.clipboard.writeText(text);
                    showToast('Tilinumero kopioitu');
                } catch (err) {
                    showToast('Kopiointi epäonnistui');
                }
            });
        });
    </script>
</body>
</html>


