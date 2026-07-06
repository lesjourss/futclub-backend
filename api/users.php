<?php
/**
 * users.php
 *
 * GET /api/users.php?id=1
 *   Mengambil data profil user (dipakai di ProfileActivity).
 *
 * PUT /api/users.php?id=1
 *   Update nama dan/atau foto profil user.
 *   Body JSON: { "name": "Nama Baru", "photo_url": "https://.../foto.jpg" }
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    sendError("Parameter id user wajib diisi", 422);
}
$userId = (int) $_GET['id'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, photo_url, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            sendError("User tidak ditemukan", 404);
        }
        sendSuccess($user, "Data user berhasil diambil");
    } catch (PDOException $e) {
        sendError("Gagal mengambil data user: " . $e->getMessage(), 500);
    }

} elseif ($method === 'PUT') {
    $input = getJsonInput();

    $fields = [];
    $params = [];

    if (isset($input['name'])) {
        if (strlen(trim($input['name'])) < 3) {
            sendError("Nama minimal 3 karakter", 422);
        }
        $fields[] = "name = ?";
        $params[] = trim($input['name']);
    }

    if (isset($input['photo_url'])) {
        $fields[] = "photo_url = ?";
        $params[] = $input['photo_url'];
    }

    if (empty($fields)) {
        sendError("Tidak ada data yang diubah", 422);
    }

    $params[] = $userId;

    try {
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $stmt = $pdo->prepare("SELECT id, name, email, photo_url, role FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        sendSuccess($stmt->fetch(), "Profil berhasil diperbarui");
    } catch (PDOException $e) {
        sendError("Gagal memperbarui profil: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
