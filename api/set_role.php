<?php
/**
 * set_role.php
 * Endpoint: PUT /api/set_role.php
 *
 * Dipanggil dari RoleSelectionActivity ketika user memilih jadi
 * "Admin Komunitas" atau "Olahragawan".
 *
 * Body JSON: { "user_id": 1, "role": "admin" }   // role: "admin" atau "user"
 */

require_once '../config/response.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendError("Method tidak diizinkan, gunakan PUT", 405);
}

$input = getJsonInput();

if (empty($input['user_id']) || !is_numeric($input['user_id'])) {
    sendError("user_id wajib diisi dengan angka", 422);
}

if (empty($input['role']) || !in_array($input['role'], ['admin', 'user'])) {
    sendError("role wajib diisi dengan nilai 'admin' atau 'user'", 422);
}

$userId = (int) $input['user_id'];
$role = $input['role'];

try {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $userId]);

    $stmt = $pdo->prepare("SELECT id, name, email, photo_url, role FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    sendSuccess($stmt->fetch(), "Role berhasil diperbarui");
} catch (PDOException $e) {
    sendError("Gagal memperbarui role: " . $e->getMessage(), 500);
}
