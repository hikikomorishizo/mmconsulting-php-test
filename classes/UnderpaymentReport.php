<?php

class UnderpaymentReport {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUnderpayments(string $sortField = 'company_name', string $sortOrder = 'ASC', string $companyFilter = '') {
        $sql = "
            SELECT 
                invoices.invoice_number, 
                clients.company_name, 
                invoices.total_amount, 
                invoices.payment_due_date,
                COALESCE(SUM(payments.payment_amount), 0) AS total_paid 
            FROM invoices 
            LEFT JOIN clients ON invoices.client_id = clients.id 
            LEFT JOIN payments ON invoices.id = payments.invoice_id 
            WHERE invoices.payment_due_date >= CURDATE() 
        ";

        if (!empty($companyFilter)) {
            $sql .= " AND clients.company_name LIKE :company_name";
        }

        $sql .= "
            GROUP BY invoices.id 
            HAVING total_paid < invoices.total_amount 
        ";

        $sql .= " ORDER BY $sortField $sortOrder";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($companyFilter)) {
            $stmt->bindValue(':company_name', '%' . $companyFilter . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
