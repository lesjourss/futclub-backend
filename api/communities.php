<?php
/**
 * communities.php
 *
 * GET /api/communities.php
 *   Mengambil daftar komunitas. Bisa difilter berdasarkan kategori.
 *   - Semua komunitas:              GET communities.php
 *   - Filter 1 kategori:            GET communities.php?category_id=2
 *   - Filter banyak kategori:       GET communities.php?category_ids=2,3,5
 *   Setiap komunitas menyertakan jumlah member (member_count).
 *
 * POST /api/communities.php
 *   Membuat komunitas baru (khusus admin).
 *   Body JSON:
 *   {
 *     "admin_id": 1,
 *     "category_id": 2,
 *     "name": "Futsal Depok Squad",
 *     "description": "Komunitas futsal santai setiap weekend",
 *     "photo_url": "https://.../foto.jpg",
 *     "whatsapp_link": "https://chat.whatsapp.com/xxxxx"
 *   }
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $sql = "SELECT c.*, sc.name AS category_name,
                   u.name AS admin_name,
                   (SELECT COUNT(*) FROM community_members cm WHERE cm.community_id = c.id) AS member_count
            FROM communities c
            JOIN sport_categories sc ON sc.id = c.category_id
            JOIN users u ON u.id = c.admin_id";

    $params = [];

    if (!empty($_GET['category_id'])) {
        $sql .= " WHERE c.category_id = ?";
        $params[] = (int) $_GET['category_id'];
    } elseif (!empty($_GET['category_ids'])) {
        $ids = array_filter(array_map('intval', explode(',', $_GET['category_ids'])));
        if (count($ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " WHERE c.category_id IN ($placeholders)";
            $params = $ids;
        }
    }

    $sql .= " ORDER BY c.created_at DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        sendSuccess($stmt->fetchAll(), "Daftar komunitas berhasil diambil");
    } catch (PDOException $e) {
        sendError("Gagal mengambil komunitas: " . $e->getMessage(), 500);
    }

} elseif ($method === 'POST') {

    $input = getJsonInput();

    // ---- Validasi input wajib ----
    $required = ['admin_id', 'category_id', 'name', 'description', 'whatsapp_link'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            sendError("Field '$field' wajib diisi", 422);
        }
    }

    if (!is_numeric($input['admin_id']) || !is_numeric($input['category_id'])) {
        sendError("admin_id dan category_id harus berupa angka", 422);
    }

    if (!filter_var($input['whatsapp_link'], FILTER_VALIDATE_URL) ||
        strpos($input['whatsapp_link'], 'chat.whatsapp.com') === false) {
        sendError("Link WhatsApp tidak valid, gunakan link grup WhatsApp yang benar", 422);
    }

    if (strlen(trim($input['name'])) < 3) {
        sendError("Nama komunitas minimal 3 karakter", 422);
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO communities (admin_id, category_id, name, description, photo_url, whatsapp_link)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int) $input['admin_id'],
            (int) $input['category_id'],
            trim($input['name']),
            trim($input['description']),
            $input['photo_url'] ?? null,
            trim($input['whatsapp_link'])
        ]);

        $newId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM communities WHERE id = ?");
        $stmt->execute([$newId]);

        sendSuccess($stmt->fetch(), "Komunitas berhasil dibuat");
    } catch (PDOException $e) {
        sendError("Gagal membuat komunitas: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
