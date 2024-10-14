<?php
require '../db.php';
require '../classes/OverpaymentReport.php';

$sortField = $_GET['sort_field'] ?? 'company_name';
$sortOrder = $_GET['sort_order'] ?? 'ASC';
$companyFilter = $_GET['company_filter'] ?? '';

$overpaymentReport = new OverpaymentReport($pdo);
$overpayments = $overpaymentReport->getOverpayments($sortField, $sortOrder, $companyFilter);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport o nadpłatach</title>
</head>
<body>
    <h1>Raport o nadpłatach</h1>
    
    <form method="GET" action="">
        <label for="company_filter">Filtruj po nazwie firmy:</label>
        <input type="text" name="company_filter" id="company_filter" value="<?php echo htmlspecialchars($companyFilter); ?>">

        <label for="sort_field">Sortuj według:</label>
        <select name="sort_field" id="sort_field">
            <option value="company_name" <?php echo $sortField === 'company_name' ? 'selected' : ''; ?>>Nazwa firmy</option>
            <option value="total_overpayment" <?php echo $sortField === 'total_overpayment' ? 'selected' : ''; ?>>Suma nadpłaty</option>
        </select>

        <label for="sort_order">Kierunek sortowania:</label>
        <select name="sort_order" id="sort_order">
            <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Rosnąco</option>
            <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Malejąco</option>
        </select>

        <button type="submit">Zastosuj</button>
    </form>

    <ul>
        <?php if (empty($overpayments)): ?>
            <li>Brak nadpłat.</li>
        <?php else: ?>
            <?php foreach ($overpayments as $overpayment): ?>
                <li>
                    Firma: <?php echo htmlspecialchars($overpayment['company_name']); ?>, 
                    Suma nadpłaty: <?php echo htmlspecialchars($overpayment['total_overpayment']); ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    
    <a href="../index.php">Powrót do raportów</a>
</body>
</html>
