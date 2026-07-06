<?php
/**
 * members.php
 * Endpoint: GET /api/members.php?community_id=3
 * Mengambil daftar user yang tergabung di komunitas tertentu.
 * Dipakai untuk mengisi RecyclerView list member di halaman detail komunitas.
 */

require_once '../config/response.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError("Method tidak diizinkan, gunakan GET", 405);
}

if (empty($_GET['community_id']) || !is_numeric($_GET['community_id'])) {
    sendError("Parameter community_id wajib diisi", 422);
}

$communityId = (int) $_GET['community_id'];

try {
    $stmt = $pdo->prepare(
        "SELECT u.id, u.name, u.photo_url, cm.joined_at
         FROM community_members cm
         JOIN users u ON u.id = cm.user_id
         WHERE cm.community_id = ?
         ORDER BY cm.joined_at ASC"
    );
    $stmt->execute([$communityId]);
    sendSuccess($stmt->fetchAll(), "Daftar member berhasil diambil");
} catch (PDOException $e) {
    sendError("Gagal mengambil daftar member: " . $e->getMessage(), 500);
}
