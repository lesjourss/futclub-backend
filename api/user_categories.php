<?php
/**
 * user_categories.php
 *
 * POST /api/user_categories.php
 *   Menyimpan pilihan kategori olahraga milik user (bisa lebih dari satu / multi-select).
 *   Body JSON:
 *   { "user_id": 1, "category_ids": [1, 3, 5] }
 *
 * GET /api/user_categories.php?user_id=1
 *   Mengambil kategori-kategori yang sudah dipilih oleh user tersebut.
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = getJsonInput();

    // ---- Validasi input ----
    if (empty($input['user_id']) || !is_numeric($input['user_id'])) {
        sendError("user_id wajib diisi dan harus berupa angka", 422);
    }
    if (empty($input['category_ids']) || !is_array($input['category_ids'])) {
        sendError("category_ids wajib diisi dan minimal pilih 1 kategori", 422);
    }

    $userId = (int) $input['user_id'];
    $categoryIds = array_unique(array_map('intval', $input['category_ids']));

    try {
        $pdo->beginTransaction();

        // Hapus pilihan lama dulu, lalu simpan pilihan baru (biar simpel & idempotent)
        $stmt = $pdo->prepare("DELETE FROM user_categories WHERE user_id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("INSERT INTO user_categories (user_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $catId) {
            $stmt->execute([$userId, $catId]);
        }

        $pdo->commit();
        sendSuccess(["user_id" => $userId, "category_ids" => $categoryIds], "Kategori berhasil disimpan");
    } catch (PDOException $e) {
        $pdo->rollBack();
        sendError("Gagal menyimpan kategori: " . $e->getMessage(), 500);
    }

} elseif ($method === 'GET') {
    if (empty($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
        sendError("Parameter user_id wajib diisi", 422);
    }

    $userId = (int) $_GET['user_id'];

    try {
        $stmt = $pdo->prepare(
            "SELECT sc.* FROM sport_categories sc
             JOIN user_categories uc ON uc.category_id = sc.id
             WHERE uc.user_id = ?"
        );
        $stmt->execute([$userId]);
        sendSuccess($stmt->fetchAll(), "Kategori user berhasil diambil");
    } catch (PDOException $e) {
        sendError("Gagal mengambil kategori user: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
