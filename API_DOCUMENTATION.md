# Dokumentasi REST API - FutClub

Base URL (emulator): `http://10.0.2.2/futclub-backend/api/`

---

### 1. POST auth.php — Login/Registrasi Google
**Request:**
```json
{
  "firebase_uid": "abc123xyz",
  "name": "Ezra Rahmaditya",
  "email": "ezra@gmail.com",
  "photo_url": "https://lh3.googleusercontent.com/xxx"
}
```
**Response (200):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "id": 1,
    "firebase_uid": "abc123xyz",
    "name": "Ezra Rahmaditya",
    "email": "ezra@gmail.com",
    "photo_url": "https://lh3.googleusercontent.com/xxx",
    "role": "user"
  }
}
```

---

### 2. GET categories.php — Daftar Kategori
**Response (200):**
```json
{
  "success": true,
  "message": "Daftar kategori berhasil diambil",
  "data": [
    { "id": 3, "name": "Futsal", "icon_name": "ic_futsal" },
    { "id": 1, "name": "Basket", "icon_name": "ic_basketball" }
  ]
}
```

---

### 3. POST user_categories.php — Simpan Kategori Pilihan User
**Request:**
```json
{ "user_id": 1, "category_ids": [1, 3, 5] }
```
**Response (200):**
```json
{
  "success": true,
  "message": "Kategori berhasil disimpan",
  "data": { "user_id": 1, "category_ids": [1, 3, 5] }
}
```

---

### 4. PUT set_role.php — Set Role User
**Request:**
```json
{ "user_id": 1, "role": "admin" }
```
**Response (200):**
```json
{
  "success": true,
  "message": "Role berhasil diperbarui",
  "data": { "id": 1, "name": "Ezra Rahmaditya", "email": "ezra@gmail.com", "photo_url": null, "role": "admin" }
}
```

---

### 5. GET communities.php?category_ids=1,3 — Daftar Komunitas
**Response (200):**
```json
{
  "success": true,
  "message": "Daftar komunitas berhasil diambil",
  "data": [
    {
      "id": 2,
      "admin_id": 1,
      "category_id": 3,
      "name": "Futsal Depok Squad",
      "description": "Komunitas futsal santai tiap weekend",
      "photo_url": "https://.../futsal.jpg",
      "whatsapp_link": "https://chat.whatsapp.com/xxxx",
      "category_name": "Futsal",
      "admin_name": "Ezra Rahmaditya",
      "member_count": 12
    }
  ]
}
```

---

### 6. POST communities.php — Buat Komunitas Baru (Admin)
**Request:**
```json
{
  "admin_id": 1,
  "category_id": 3,
  "name": "Futsal Depok Squad",
  "description": "Komunitas futsal santai tiap weekend",
  "photo_url": "https://.../futsal.jpg",
  "whatsapp_link": "https://chat.whatsapp.com/xxxx"
}
```
**Response (200):** data komunitas yang baru dibuat.

**Response Error contoh (422) — link WA salah:**
```json
{ "success": false, "message": "Link WhatsApp tidak valid, gunakan link grup WhatsApp yang benar" }
```

---

### 7. GET community_detail.php?id=2 — Detail Komunitas + Gallery
**Response (200):**
```json
{
  "success": true,
  "message": "Detail komunitas berhasil diambil",
  "data": {
    "id": 2,
    "name": "Futsal Depok Squad",
    "description": "...",
    "member_count": 12,
    "gallery": [
      { "id": 5, "photo_url": "https://.../g1.jpg" },
      { "id": 6, "photo_url": "https://.../g2.jpg" }
    ]
  }
}
```

---

### 8. PUT community_detail.php?id=2 — Edit Komunitas
**Request:**
```json
{ "description": "Deskripsi baru komunitas" }
```

---

### 9. POST join_community.php — Join Komunitas
**Request:**
```json
{ "community_id": 2, "user_id": 4 }
```
**Response (409) — sudah join sebelumnya:**
```json
{ "success": false, "message": "User sudah bergabung di komunitas ini" }
```

---

### 10. GET members.php?community_id=2 — Daftar Member
**Response (200):**
```json
{
  "success": true,
  "message": "Daftar member berhasil diambil",
  "data": [
    { "id": 4, "name": "Budi", "photo_url": "https://...", "joined_at": "2026-07-01 10:00:00" }
  ]
}
```

---

### 11. POST gallery.php — Tambah Foto Gallery (maks 3)
**Request:**
```json
{ "community_id": 2, "photo_url": "https://.../g3.jpg" }
```
**Response Error (422) — sudah 3 foto:**
```json
{ "success": false, "message": "Gallery sudah mencapai batas maksimal 3 foto" }
```

---

### 12. GET users.php?id=1 — Profil User
**Response (200):**
```json
{
  "success": true,
  "data": { "id": 1, "name": "Ezra Rahmaditya", "email": "ezra@gmail.com", "photo_url": null, "role": "admin" }
}
```

### 13. PUT users.php?id=1 — Edit Profil
**Request:**
```json
{ "name": "Ezra R.", "photo_url": "https://.../new.jpg" }
```
