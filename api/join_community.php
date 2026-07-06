<?php
/**
 * join_community.php
 *
 * POST /api/join_community.php
 *   User join ke sebuah komunitas.
 *   Body JSON: { "community_id": 3, "user_id": 5 }
 *
 * DELETE /api/join_community.php?community_id=3&user_id=5
 *   User keluar dari komunitas.
 */

require_once '../config/response.php';
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = getJsonInput();

    if (empty($input['community_id']) || empty($input['user_id']) ||
        !is_numeric($input['community_id']) || !is_numeric($input['user_id'])) {
        sendError("community_id dan user_id wajib diisi dengan angka", 422);
    }

    $communityId = (int) $input['community_id'];
    $userId = (int) $input['user_id'];

    try {
        // Cek apakah sudah join sebelumnya
        $stmt = $pdo->prepare("SELECT * FROM community_members WHERE community_id = ? AND user_id = ?");
        $stmt->execute([$communityId, $userId]);
        if ($stmt->fetch()) {
            sendError("User sudah bergabung di komunitas ini", 409);
        }

        $stmt = $pdo->prepare("INSERT INTO community_members (community_id, user_id) VALUES (?, ?)");
        $stmt->execute([$communityId, $userId]);

        sendSuccess(["community_id" => $communityId, "user_id" => $userId], "Berhasil bergabung ke komunitas");
    } catch (PDOException $e) {
        sendError("Gagal join komunitas: " . $e->getMessage(), 500);
    }

} elseif ($method === 'DELETE') {
    if (empty($_GET['community_id']) || empty($_GET['user_id'])) {
        sendError("Parameter community_id dan user_id wajib diisi", 422);
    }

    $communityId = (int) $_GET['community_id'];
    $userId = (int) $_GET['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM community_members WHERE community_id = ? AND user_id = ?");
        $stmt->execute([$communityId, $userId]);
        sendSuccess([], "Berhasil keluar dari komunitas");
    } catch (PDOException $e) {
        sendError("Gagal keluar dari komunitas: " . $e->getMessage(), 500);
    }

} else {
    sendError("Method tidak diizinkan", 405);
}
