<?php
// Nie tworzyłem bazy danych, ponieważ nie było to wymagane w zadaniu


class Contracts
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getContracts(int $action, int $id = 0, int $sort = 0): array
    {
        $query = "SELECT * FROM contracts WHERE kwota > 10";
        $params = [];

        if ($action == 5) {
            $query .= " AND id = :id";
            $params[':id'] = $id;

            switch ($sort) {
                case 1:
                    $query .= " ORDER BY 2, 4";
                    break;
                case 2:
                    $query .= " ORDER BY 10";
                    break;
                default:
                    $query .= " ORDER BY id"; 
                    break;
            }
        } else {
            $query .= " ORDER BY id";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function renderTable(array $contracts): string
    {
        $html = "<html><body bgcolor=\"#f0f0f0\"><br><table width=\"95%\">";

        foreach ($contracts as $contract) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($contract['id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($contract['nazwa_przedsiebiorcy']) . '</td>';

            if ($contract['kwota'] > 5) {
                $html .= '<td>' . htmlspecialchars($contract['kwota']) . '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table></body></html>';
        return $html;
    }
}

try {
    // Nie tworzyłem bazy danych, ponieważ nie było to wymagane w zadaniu
    $pdo = new PDO('mysql:host=localhost;dbname=database', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

$action = (int)($_GET['akcja'] ?? 0);
$id = (int)($_GET['i'] ?? 0);
$sort = (int)($_GET['sort'] ?? 0);

$contractsObj = new Contracts($pdo);
$contracts = $contractsObj->getContracts($action, $id, $sort);

echo $contractsObj->renderTable($contracts);
?>
