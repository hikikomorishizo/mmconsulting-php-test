<?php
require '../db.php';
require '../classes/UnderpaymentReport.php';

$sortField = $_GET['sort_field'] ?? 'company_name';
$sortOrder = $_GET['sort_order'] ?? 'ASC';
$companyFilter = $_GET['company_filter'] ?? '';

$underpaymentReport = new UnderpaymentReport($pdo);
$underpayments = $underpaymentReport->getUnderpayments($sortField, $sortOrder, $companyFilter);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport o niedopłatach</title>
</head>
<body>
    <h1>Raport o niedopłatach</h1>
    
    <form method="GET" action="">
        <label for="company_filter">Filtruj po nazwie firmy:</label>
        <input type="text" name="company_filter" id="company_filter" value="<?php echo htmlspecialchars($companyFilter); ?>">

        <label for="sort_field">Sortuj według:</label>
        <select name="sort_field" id="sort_field">
            <option value="company_name" <?php echo $sortField === 'company_name' ? 'selected' : ''; ?>>Nazwa firmy</option>
            <option value="total_amount" <?php echo $sortField === 'total_amount' ? 'selected' : ''; ?>>Suma rachunku</option>
            <option value="total_paid" <?php echo $sortField === 'total_paid' ? 'selected' : ''; ?>>Suma wpłat</option>
        </select>

        <label for="sort_order">Kierunek sortowania:</label>
        <select name="sort_order" id="sort_order">
            <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Rosnąco</option>
            <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Malejąco</option>
        </select>

        <button type="submit">Zastosuj</button>
    </form>

    <ul>
        <?php if (empty($underpayments)): ?>
            <li>Brak niedopłat.</li>
        <?php else: ?>
            <?php foreach ($underpayments as $underpayment): ?>
                <li>
                    Firma: <?php echo htmlspecialchars($underpayment['company_name']); ?>, 
                    Numer rachunku: <?php echo htmlspecialchars($underpayment['invoice_number']); ?>, 
                    Suma rachunku: <?php echo htmlspecialchars($underpayment['total_amount']); ?>, 
                    Suma wpłat: <?php echo htmlspecialchars($underpayment['total_paid']); ?>, 
                    Do zapłaty: <?php echo htmlspecialchars($underpayment['total_amount'] - $underpayment['total_paid']); ?>, 
                    Termin płatności: <?php echo htmlspecialchars($underpayment['payment_due_date']); ?> 
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    
    <a href="../index.php">Powrót do raportów</a>
</body>
</html>
