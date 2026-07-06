<?php
/**
 * auth.php
 * Endpoint: POST /api/auth.php
 *
 * Dipanggil setelah user berhasil login Google di Android (via Firebase Auth SDK).
 * Android mengirim firebase_uid, name, email, photo_url dari hasil login Google.
 * Kalau user belum ada di database -> dibuatkan baru (role default 'user').
 * Kalau sudah ada -> data user dikembalikan (login).
 *
 * Body JSON contoh:
 * {
 *   "firebase_uid": "abc123xyz",
 *   "name": "Ezra Rahmaditya",
 *   "email": "ezra@gmail.com",
 *   "photo_url": "https://lh3.googleusercontent.com/xxx"
 * }
 */

require_once '../config/response.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError("Method tidak diizinkan, gunakan POST", 405);
}

$input = getJsonInput();

// ---- Validasi input wajib ----
if (empty($input['firebase_uid']) || empty($input['name']) || empty($input['email'])) {
    sendError("firebase_uid, name, dan email wajib diisi", 422);
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    sendError("Format email tidak valid", 422);
}

$firebase_uid = trim($input['firebase_uid']);
$name = trim($input['name']);
$email = trim($input['email']);
$photo_url = isset($input['photo_url']) ? trim($input['photo_url']) : null;

try {
    // Cek apakah user sudah pernah login sebelumnya
    $stmt = $pdo->prepare("SELECT * FROM users WHERE firebase_uid = ?");
    $stmt->execute([$firebase_uid]);
    $user = $stmt->fetch();

    if ($user) {
        // User lama -> langsung login
        sendSuccess($user, "Login berhasil");
    } else {
        // User baru -> insert ke database
        $stmt = $pdo->prepare(
            "INSERT INTO users (firebase_uid, name, email, photo_url, role) VALUES (?, ?, ?, ?, 'user')"
        );
        $stmt->execute([$firebase_uid, $name, $email, $photo_url]);
        $newUserId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$newUserId]);
        $newUser = $stmt->fetch();

        sendSuccess($newUser, "Registrasi & login berhasil");
    }
} catch (PDOException $e) {
    sendError("Gagal proses login: " . $e->getMessage(), 500);
}
