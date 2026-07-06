<?php
/**
 * community_detail.php
 *
 * GET /api/community_detail.php?id=3
 *   Mengambil detail 1 komunitas beserta gallery foto & jumlah member.
 *
 * PUT /api/community_detail.php?id=3
 *   Update data komunitas (khusus admin pemilik komunitas).
 *   Body JSON (semua field opsional, isi yang mau diubah saja):
 *   { "name": "...", "description": "...", "photo_url": "...", "whatsapp_link": "..." }
 *
 * DELETE /api/community_detail.php?id=3
 *   Menghapus komunitas (khusus admin pemilik).
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    sendError("Parameter id komunitas wajib diisi", 422);
}
$communityId = (int) $_GET['id'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare(
            "SELECT c.*, sc.name AS category_name, u.name AS admin_name,
                    (SELECT COUNT(*) FROM community_members cm WHERE cm.community_id = c.id) AS member_count
             FROM communities c
             JOIN sport_categories sc ON sc.id = c.category_id
             JOIN users u ON u.id = c.admin_id
             WHERE c.id = ?"
        );
        $stmt->execute([$communityId]);
        $community = $stmt->fetch();

        if (!$community) {
            sendError("Komunitas tidak ditemukan", 404);
        }

        $stmt = $pdo->prepare("SELECT * FROM community_gallery WHERE community_id = ? ORDER BY uploaded_at ASC");
        $stmt->execute([$communityId]);
        $community['gallery'] = $stmt->fetchAll();

        sendSuccess($community, "Detail komunitas berhasil diambil");
    } catch (PDOException $e) {
        sendError("Gagal mengambil detail komunitas: " . $e->getMessage(), 500);
    }

} elseif ($method === 'PUT') {
    $input = getJsonInput();

    $fields = [];
    $params = [];

    if (isset($input['name'])) {
        if (strlen(trim($input['name'])) < 3) {
            sendError("Nama komunitas minimal 3 karakter", 422);
        }
        $fields[] = "name = ?";
        $params[] = trim($input['name']);
    }
    if (isset($input['description'])) {
        $fields[] = "description = ?";
        $params[] = trim($input['description']);
    }
    if (isset($input['photo_url'])) {
        $fields[] = "photo_url = ?";
        $params[] = $input['photo_url'];
    }
    if (isset($input['whatsapp_link'])) {
        if (!filter_var($input['whatsapp_link'], FILTER_VALIDATE_URL) ||
            strpos($input['whatsapp_link'], 'chat.whatsapp.com') === false) {
            sendError("Link WhatsApp tidak valid", 422);
        }
        $fields[] = "whatsapp_link = ?";
        $params[] = trim($input['whatsapp_link']);
    }

    if (empty($fields)) {
        sendError("Tidak ada data yang diubah", 422);
    }

    $params[] = $communityId;

    try {
        $sql = "UPDATE communities SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $stmt = $pdo->prepare("SELECT * FROM communities WHERE id = ?");
        $stmt->execute([$communityId]);

        sendSuccess($stmt->fetch(), "Komunitas berhasil diperbarui");
    } catch (PDOException $e) {
        sendError("Gagal memperbarui komunitas: " . $e->getMessage(), 500);
    }

} elseif ($method === 'DELETE') {
    try {
        $stmt = $pdo->prepare("DELETE FROM communities WHERE id = ?");
        $stmt->execute([$communityId]);
        sendSuccess([], "Komunitas berhasil dihapus");
    } catch (PDOException $e) {
        sendError("Gagal menghapus komunitas: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
