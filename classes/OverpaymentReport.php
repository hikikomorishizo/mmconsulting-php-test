<?php

class OverpaymentReport {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getOverpayments(string $sortField = 'company_name', string $sortOrder = 'ASC', string $companyFilter = '') {
        $sql = "
            SELECT 
                clients.company_name, 
                (IFNULL(SUM(payments.payment_amount), 0) - SUM(invoices.total_amount)) AS total_overpayment
            FROM clients
            LEFT JOIN invoices ON clients.id = invoices.client_id
            LEFT JOIN payments ON invoices.id = payments.invoice_id
            GROUP BY clients.id
            HAVING total_overpayment > 0
        ";

        if (!empty($companyFilter)) {
            $sql .= " AND clients.company_name LIKE :company_name";
        }

        $sql .= " ORDER BY $sortField $sortOrder";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($companyFilter)) {
            $stmt->bindValue(':company_name', '%' . $companyFilter . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
