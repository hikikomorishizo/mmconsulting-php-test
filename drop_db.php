<?php
require 'db.php';

$tables = [
    'invoice_items',
    'payments',
    'invoices',
    'clients',
];

foreach ($tables as $table) {
    $stmt = $pdo->prepare("DROP TABLE IF EXISTS $table");
    $stmt->execute();
}

echo "Wszystkie tabele zostały pomyślnie usunięte.";
?>

<a href="index.php">Powrót do raportów</a>