<?php
/**
 * categories.php
 * Endpoint: GET /api/categories.php
 * Mengambil semua kategori olahraga yang tersedia (Basket, Lari, Futsal, dst).
 */

require_once '../config/response.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError("Method tidak diizinkan, gunakan GET", 405);
}

try {
    $stmt = $pdo->query("SELECT * FROM sport_categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
    sendSuccess($categories, "Daftar kategori berhasil diambil");
} catch (PDOException $e) {
    sendError("Gagal mengambil kategori: " . $e->getMessage(), 500);
}
