<?php
/**
 * gallery.php
 *
 * GET /api/gallery.php?community_id=3
 *   Mengambil semua foto gallery komunitas.
 *
 * POST /api/gallery.php
 *   Menambah foto gallery komunitas. Maksimal 3 foto per komunitas (divalidasi di sini).
 *   Body JSON: { "community_id": 3, "photo_url": "https://.../foto1.jpg" }
 *
 * DELETE /api/gallery.php?id=7
 *   Menghapus salah satu foto gallery.
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (empty($_GET['community_id']) || !is_numeric($_GET['community_id'])) {
        sendError("Parameter community_id wajib diisi", 422);
    }
    $communityId = (int) $_GET['community_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM community_gallery WHERE community_id = ? ORDER BY uploaded_at ASC");
        $stmt->execute([$communityId]);
        sendSuccess($stmt->fetchAll(), "Gallery berhasil diambil");
    } catch (PDOException $e) {
        sendError("Gagal mengambil gallery: " . $e->getMessage(), 500);
    }

} elseif ($method === 'POST') {
    $input = getJsonInput();

    if (empty($input['community_id']) || !is_numeric($input['community_id'])) {
        sendError("community_id wajib diisi dengan angka", 422);
    }
    if (empty($input['photo_url'])) {
        sendError("photo_url wajib diisi", 422);
    }

    $communityId = (int) $input['community_id'];

    try {
        // ---- Validasi maksimal 3 foto per komunitas ----
        $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM community_gallery WHERE community_id = ?");
        $stmt->execute([$communityId]);
        $total = $stmt->fetch()['total'];

        if ($total >= 3) {
            sendError("Gallery sudah mencapai batas maksimal 3 foto", 422);
        }

        $stmt = $pdo->prepare("INSERT INTO community_gallery (community_id, photo_url) VALUES (?, ?)");
        $stmt->execute([$communityId, trim($input['photo_url'])]);

        sendSuccess(["id" => $pdo->lastInsertId()], "Foto gallery berhasil ditambahkan");
    } catch (PDOException $e) {
        sendError("Gagal menambah foto gallery: " . $e->getMessage(), 500);
    }

} elseif ($method === 'DELETE') {
    if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
        sendError("Parameter id wajib diisi", 422);
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM community_gallery WHERE id = ?");
        $stmt->execute([(int) $_GET['id']]);
        sendSuccess([], "Foto gallery berhasil dihapus");
    } catch (PDOException $e) {
        sendError("Gagal menghapus foto gallery: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
