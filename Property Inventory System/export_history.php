<?php
session_start();
include 'db_connect.php';

// Security: only admin
$is_admin = false;
if (isset($_SESSION['role']) && strpos(strtolower($_SESSION['role']), 'admin') !== false) {
    $is_admin = true;
}
if (!isset($_SESSION['user_id']) || !$is_admin) {
    header("Location: index.php?error=unauthorized");
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=history_export_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Timestamp', 'User', 'Action', 'Property Code', 'Details']);

$stmt = $pdo->query("SELECT h.*, u.username FROM history_log h JOIN users u ON h.user_id = u.id ORDER BY h.created_at DESC");
while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $row['created_at'],
        $row['username'],
        $row['action_type'],
        $row['property_code'],
        $row['details']
    ]);
}
fclose($output);
exit();