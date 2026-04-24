<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit(); }
include 'db_connect.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventory_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Property Code', 'Serial Number', 'Item Name', 'Category', 'Assigned Office', 'Officer In-Charge', 'System Assignee', 'Value', 'Purchase Date', 'Status']);

$stmt = $pdo->query("SELECT p.*, c.category_name, u.username as system_user 
                     FROM properties p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN users u ON p.responsible_user_id = u.id");
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['property_code'],
        $row['serial_number'],
        $row['item_name'],
        $row['category_name'],
        $row['assigned_office'],
        $row['officer_in_charge'],
        $row['system_user'],
        $row['value'],
        $row['purchase_date'],
        $row['status']
    ]);
}
fclose($output);
exit();