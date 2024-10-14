<?php

class OverdueInvoicesReport
{
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function generate(string $sortField = 'invoice_number', string $sortOrder = 'ASC', string $companyFilter = ''): array
    {
        $sql = "
            SELECT 
                invoices.invoice_number, 
                invoices.payment_due_date, 
                invoices.total_amount, 
                clients.company_name,
                IFNULL(SUM(payments.payment_amount), 0) AS total_paid
            FROM 
                invoices
            JOIN 
                clients ON invoices.client_id = clients.id
            LEFT JOIN 
                payments ON invoices.id = payments.invoice_id
            WHERE 
                invoices.payment_due_date < CURDATE()
        ";

        if (!empty($companyFilter)) {
            $sql .= " AND clients.company_name LIKE :company_name";
        }

        $sql .= "
            GROUP BY 
                invoices.id, clients.company_name
            HAVING 
                total_paid < invoices.total_amount
        ";

        if ($sortField === 'total_paid') {
            $sortOrder = 'DESC'; 
        }

        $sql .= "
            ORDER BY 
                $sortField $sortOrder
        ";

        $stmt = $this->pdo->prepare($sql);

        if (!empty($companyFilter)) {
            $stmt->bindValue(':company_name', '%' . $companyFilter . '%');
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
