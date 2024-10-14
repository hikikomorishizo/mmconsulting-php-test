<?php
require 'db.php';

$clients = [
    ['company_name' => 'Firma A', 'bank_account_number' => '1234567890', 'inn' => '123456789'],
    ['company_name' => 'Firma B', 'bank_account_number' => '0987654321', 'inn' => '987654321'],
    ['company_name' => 'Firma C', 'bank_account_number' => '5432167890', 'inn' => '321654987'],
];

foreach ($clients as $client) {
    $stmt = $pdo->prepare("INSERT INTO clients (company_name, bank_account_number, inn) VALUES (?, ?, ?)");
    $stmt->execute([$client['company_name'], $client['bank_account_number'], $client['inn']]);
}

$clientIds = $pdo->query("SELECT id FROM clients")->fetchAll(PDO::FETCH_COLUMN);

$invoices = [
    ['client_id' => $clientIds[0], 'invoice_number' => 'FAKT-001', 'invoice_date' => '2024-01-01', 'payment_due_date' => '2025-01-15'],
    ['client_id' => $clientIds[1], 'invoice_number' => 'FAKT-002', 'invoice_date' => '2024-01-10', 'payment_due_date' => '2024-01-20'],
    ['client_id' => $clientIds[2], 'invoice_number' => 'FAKT-003', 'invoice_date' => '2024-01-05', 'payment_due_date' => '2024-01-25'],
];

$invoiceTotals = [];

foreach ($invoices as $invoice) {
    $stmt = $pdo->prepare("INSERT INTO invoices (client_id, invoice_number, invoice_date, payment_due_date, total_amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$invoice['client_id'], $invoice['invoice_number'], $invoice['invoice_date'], $invoice['payment_due_date'], 0.00]); 
    $invoiceTotals[] = $pdo->lastInsertId(); 
}

foreach ($invoiceTotals as $index => $invoiceId) {
    $productNames = ['Produkt 1', 'Produkt 2', 'Produkt 3', 'Produkt 4', 'Produkt 5'];
    $quantity = rand(1, 5); 

    foreach ($productNames as $productName) {
        $price = rand(1, 5) * 100;

        $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$invoiceId, $productName, $quantity, $price]);
        
        $stmt = $pdo->prepare("UPDATE invoices SET total_amount = total_amount + ? * ? WHERE id = ?");
        $stmt->execute([$price, $quantity, $invoiceId]);
    }
}

$payments = [
    ['invoice_id' => $invoiceTotals[0], 'payment_description' => 'Wpłata za FAKT-001', 'payment_amount' => 5000.00, 'payment_date' => '2024-01-05', 'payer_bank_account' => '1234567890'],
    ['invoice_id' => $invoiceTotals[0], 'payment_description' => 'Wpłata za FAKT-001', 'payment_amount' => 1000.00, 'payment_date' => '2024-01-10', 'payer_bank_account' => '1234567890'],
    ['invoice_id' => $invoiceTotals[1], 'payment_description' => 'Wpłata za FAKT-002', 'payment_amount' => 15000.00, 'payment_date' => '2024-01-18', 'payer_bank_account' => '0987654321'],
];

foreach ($payments as $payment) {
    $stmt = $pdo->prepare("INSERT INTO payments (invoice_id, payment_description, payment_amount, payment_date, payer_bank_account) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$payment['invoice_id'], $payment['payment_description'], $payment['payment_amount'], $payment['payment_date'], $payment['payer_bank_account']]);
}

echo "Baza danych została pomyślnie wypełniona danymi testowymi";
?>
<a href="index.php">Powrót do raportów</a>
