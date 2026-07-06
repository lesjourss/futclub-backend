# FutClub Backend — REST API (PHP + MySQL)

> Backend REST API untuk aplikasi Android **FutClub** — platform komunitas olahraga lokal.

**Anggota Kelompok:**
- 2411501865 - Zahfandhika Fauzan Maldini
- 2411501642 - R. Ezra Rahmaditya

---

## Deskripsi

Backend ini dibangun menggunakan **PHP native** dengan arsitektur REST API dan database **MySQL (MariaDB)** yang berjalan di atas **XAMPP**. Setiap endpoint mengembalikan response JSON dengan format standar:

```json
{
  "success": true | false,
  "message": "Pesan deskriptif",
  "data": { ... } | [ ... ] | null
}
```

---

## Struktur Folder

```
futclub-backend/
├── api/
│   ├── auth.php               # Login / registrasi via Google (Firebase)
│   ├── categories.php         # Daftar kategori olahraga
│   ├── user_categories.php    # Simpan / ambil kategori pilihan user
│   ├── set_role.php           # Set role user (admin / olahragawan)
│   ├── communities.php        # List & buat komunitas
│   ├── community_detail.php   # Detail & edit komunitas
│   ├── join_community.php     # Join komunitas
│   ├── members.php            # Daftar anggota komunitas
│   ├── gallery.php            # Kelola foto galeri komunitas
│   └── users.php              # Lihat & edit profil user
├── config/
│   └── config.php             # Konfigurasi koneksi database
├── database/
│   └── futclub.sql            # Schema + data awal database
├── uploads/                   # Folder untuk upload file (opsional)
├── API_DOCUMENTATION.md       # Dokumentasi endpoint lengkap + contoh
└── README.md
```

---

## Cara Setup di XAMPP

1. **Install XAMPP** (jika belum): https://www.apachefriends.org

2. **Copy folder ini** ke dalam `htdocs` XAMPP:
   - Windows: `C:\xampp\htdocs\futclub-backend`

3. **Jalankan Apache dan MySQL** dari XAMPP Control Panel.

4. **Import database:**
   - Buka `http://localhost/phpmyadmin`
   - Klik tab **Import** → pilih file `database/futclub.sql` → klik **Go**
   - Database `futclub_db` beserta semua tabel dan data kategori awal otomatis terbuat.

5. **Cek konfigurasi** di `config/config.php`:
   ```php
   $host = 'localhost';
   $db   = 'futclub_db';
   $user = 'root';
   $pass = '';          // default XAMPP tidak pakai password
   ```

6. **Test API** di browser:
   ```
   http://localhost/futclub-backend/api/categories.php
   ```
   Jika muncul JSON daftar kategori olahraga → backend sudah berjalan.

---

## Base URL untuk Android (Retrofit)

| Skenario | Base URL |
|----------|----------|
| Emulator Android Studio | `http://10.0.2.2/futclub-backend/api/` |
| HP fisik (WiFi sama) | `http://<IP_LAPTOP>/futclub-backend/api/` |

> **Cara cek IP laptop:** jalankan `ipconfig` di Command Prompt, lihat bagian *IPv4 Address* pada adapter WiFi yang aktif.

---

## Daftar Endpoint API

| No | Endpoint | Method | Fungsi |
|----|----------|--------|--------|
| 1 | `auth.php` | `POST` | Login / registrasi user via Google (Firebase UID) |
| 2 | `categories.php` | `GET` | Ambil semua kategori olahraga |
| 3 | `user_categories.php` | `GET` | Ambil kategori pilihan user (`?user_id=`) |
| 4 | `user_categories.php` | `POST` | Simpan kategori pilihan user (onboarding) |
| 5 | `set_role.php` | `PUT` | Set role user menjadi `admin` atau `user` |
| 6 | `communities.php` | `GET` | Daftar komunitas, opsional filter `?category_id=` |
| 7 | `communities.php` | `POST` | Buat komunitas baru (khusus Admin) |
| 8 | `community_detail.php` | `GET` | Detail komunitas + galeri foto (`?id=`) |
| 9 | `community_detail.php` | `PUT` | Edit data komunitas (`?id=`) |
| 10 | `join_community.php` | `POST` | Daftarkan user sebagai anggota komunitas |
| 11 | `members.php` | `GET` | Daftar anggota sebuah komunitas (`?community_id=`) |
| 12 | `gallery.php` | `POST` | Tambah foto galeri komunitas (maks 3 foto) |
| 13 | `users.php` | `GET` | Ambil profil user (`?id=`) |
| 14 | `users.php` | `PUT` | Update nama & foto profil user (`?id=`) |

> Dokumentasi lengkap setiap endpoint (sample request & response) ada di **`API_DOCUMENTATION.md`**.

---

## Skema Database

Database: `futclub_db`

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna dari Google Sign-In (Firebase UID, nama, email, role) |
| `sport_categories` | Daftar kategori olahraga (Futsal, Basket, Badminton, dll.) |
| `user_categories` | Relasi many-to-many: user ↔ kategori yang diminati |
| `communities` | Data komunitas olahraga yang dibuat oleh admin |
| `community_gallery` | Foto kegiatan komunitas, **maksimal 3 foto** per komunitas |
| `community_members` | Relasi many-to-many: user ↔ komunitas yang diikuti |

Jalankan `database/futclub.sql` di phpMyAdmin untuk membuat semua tabel sekaligus.

---

## Tech Stack

```
Runtime   : PHP 8 (native, tanpa framework)
Database  : MySQL / MariaDB (via XAMPP)
Server    : Apache (via XAMPP)
Auth      : Firebase Authentication (verifikasi di sisi Android)
Format    : REST API — response JSON
```

---

## Catatan

- Field `photo_url` saat ini menggunakan URL gambar eksternal (Google Drive, ImgBB, dll.).
- Validasi link WhatsApp dilakukan di backend — harus mengandung `chat.whatsapp.com`.
- Batas galeri komunitas (maks 3 foto) divalidasi di backend (`gallery.php`), bukan hanya di Android.
