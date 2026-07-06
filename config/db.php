<?php
/**
 * db.php
 * File koneksi ke database MySQL/MariaDB menggunakan PDO.
 * Semua file api/*.php akan include file ini untuk konek ke database.
 */

// --- Konfigurasi database (sesuaikan kalau setting XAMPP kamu beda) ---
$DB_HOST = "localhost";
$DB_NAME = "futclub_db";
$DB_USER = "root";      // default XAMPP
$DB_PASS = "";          // default XAMPP kosong

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS
    );
    // Supaya error ditampilkan sebagai exception (gampang di-debug)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Koneksi database gagal: " . $e->getMessage()
    ]);
    exit;
}
