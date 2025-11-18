<?php
session_start();

require_once __DIR__ . '/db_config.php';

$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$from_account = trim((string)($_POST['from_account'] ?? ''));
$to_account   = trim((string)($_POST['to_account'] ?? ''));

if ($amount === false || $amount <= 0 || $from_account === '' || $to_account === '') {
    $_SESSION['transfer_error'] = 'Virheellinen syöte.';
    header('Location: result.php');
    exit;
}

if ($from_account === $to_account) {
    $_SESSION['transfer_error'] = 'Lähde- ja kohdetili eivät voi olla samat.';
    header('Location: result.php');
    exit;
}

$_SESSION['amount'] = $amount;
$_SESSION['from_account'] = $from_account;
$_SESSION['to_account'] = $to_account;

$conn = get_db_connection();
pg_query($conn, 'BEGIN') or die('Ei onnistuttu aloittamaan tapahtumaa: ' . pg_last_error($conn));
$update_from_sql = '
    UPDATE TILIT
    SET summa = summa - $1
    WHERE tilinumero = $2
      AND summa >= $1
';
$res_from = pg_query_params($conn, $update_from_sql, [$amount, $from_account]);

if (!$res_from) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Virhe lähdetilin päivittämisessä: ' . pg_last_error($conn);
    header('Location: result.php');
    exit;
}

if (pg_affected_rows($res_from) !== 1) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Lähdetilin tilinumero on väärä tai saldoa ei ole tarpeeksi.';
    header('Location: result.php');
    exit;
}
$update_to_sql = '
    UPDATE TILIT
    SET summa = summa + $1
    WHERE tilinumero = $2
';
$res_to = pg_query_params($conn, $update_to_sql, [$amount, $to_account]);

if (!$res_to) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Virhe kohdetilin päivittämisessä: ' . pg_last_error($conn);
    header('Location: result.php');
    exit;
}

if (pg_affected_rows($res_to) !== 1) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Kohdetilin tilinumero on väärä.';
    header('Location: result.php');
    exit;
}
$select_sql = '
    SELECT tilinumero, omistaja
    FROM TILIT
    WHERE tilinumero = $1 OR tilinumero = $2
';
$res_select = pg_query_params($conn, $select_sql, [$from_account, $to_account]);

if (!$res_select || pg_num_rows($res_select) < 2) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Tilien omistajatietojen hakeminen epäonnistui.';
    header('Location: result.php');
    exit;
}

$from_owner = null;
$to_owner = null;
while ($row = pg_fetch_assoc($res_select)) {
    if ($row['tilinumero'] === $from_account) {
        $from_owner = $row['omistaja'];
    } elseif ($row['tilinumero'] === $to_account) {
        $to_owner = $row['omistaja'];
    }
}

if ($from_owner === null || $to_owner === null) {
    pg_query($conn, 'ROLLBACK') or die('Ei onnistuttu peruuttamaan tapahtumaa: ' . pg_last_error($conn));
    $_SESSION['transfer_error'] = 'Tilien omistajatietojen käsittely epäonnistui.';
    header('Location: result.php');
    exit;
}
pg_query($conn, 'COMMIT') or die('Ei onnistuttu hyväksymään tapahtumaa: ' . pg_last_error($conn));
$_SESSION['from_owner'] = $from_owner;
$_SESSION['to_owner'] = $to_owner;
$_SESSION['transfer_success'] = true;

header('Location: result.php');
exit;


