<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_connect.php';

$assignment_id = $_GET['assignment_id'] ?? 0;
$stmt = $pdo->prepare("UPDATE asset_assignments SET actual_return_datetime = NOW(), status = 'returned', return_notes = ? WHERE id = ?");
$stmt->execute([$_POST['return_notes'] ?? '', $assignment_id]);

// Update asset status back to 'Available'
$asset_id = $pdo->prepare("SELECT asset_id FROM asset_assignments WHERE id = ?")->execute([$assignment_id]);
// ... fetch and update properties table
header("Location: asset_detail.php?id=$asset_id&msg=returned");