<?php
require '../db.php';
require '../classes/OverdueInvoicesReport.php';

$sortField = $_GET['sort_field'] ?? 'invoice_number';
$sortOrder = $_GET['sort_order'] ?? 'ASC';
$companyFilter = $_GET['company_filter'] ?? '';

$overdueInvoicesReport = new OverdueInvoicesReport($pdo);
$overdueInvoices = $overdueInvoicesReport->generate($sortField, $sortOrder, $companyFilter);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zestawienie zaległych rachunków</title>
</head>
<body>
    <h1>Zestawienie zaległych rachunków</h1>
    
    <form method="GET" action="">
        <input type="text" name="company_filter" placeholder="Filtruj według nazwy firmy" value="<?php echo htmlspecialchars($companyFilter); ?>">
        <select name="sort_field">
            <option value="invoice_number" <?php echo ($sortField == 'invoice_number') ? 'selected' : ''; ?>>Numer rachunku</option>
            <option value="payment_due_date" <?php echo ($sortField == 'payment_due_date') ? 'selected' : ''; ?>>Termin płatności</option>
            <option value="total_amount" <?php echo ($sortField == 'total_amount') ? 'selected' : ''; ?>>Całkowita kwota</option>
            <option value="total_paid" <?php echo ($sortField == 'total_paid') ? 'selected' : ''; ?>>Kwota zapłacona</option>
        </select>
        <select name="sort_order">
            <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Rosnąco</option>
            <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Malejąco</option>
        </select>
        <button type="submit">Zastosuj</button>
    </form>

    <ul>
        <?php if (empty($overdueInvoices)): ?>
            <li>Brak zaległych rachunków.</li>
        <?php else: ?>
            <?php foreach ($overdueInvoices as $invoice): ?>
                <li>
                    Numer rachunku: <?php echo htmlspecialchars($invoice['invoice_number']); ?>, 
                    Firma: <?php echo htmlspecialchars($invoice['company_name']); ?>, 
                    Całkowita kwota: <?php echo htmlspecialchars($invoice['total_amount']); ?>, 
                    Termin płatności: <?php echo htmlspecialchars($invoice['payment_due_date']); ?>, 
                    Kwota zapłacona: <?php echo htmlspecialchars($invoice['total_paid']); ?>,
                    Kwota zadłużenia: <?php echo htmlspecialchars($invoice['total_amount'] - $invoice['total_paid']); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <a href="../index.php">Powrót do raportów</a>
</body>
</html>
