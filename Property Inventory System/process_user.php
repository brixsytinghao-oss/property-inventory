<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // In a real app, use password_hash()
    $role = $_POST['role'];

    try {
        $sql = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'password' => $password,
            'role' => $role
        ]);

        // Redirect back to user list with success
        header("Location: users.php?status=created");
        exit();
    } catch (PDOException $e) {
        die("Error adding user: " . $e->getMessage());
    }
}