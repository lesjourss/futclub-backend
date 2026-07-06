# FutClub Backend (PHP REST API + MySQL)

Backend ini dipakai oleh aplikasi Android FutClub sesuai ketentuan UAS Mobile Programming
(wajib MySQL/MariaDB + PHP + REST API, tanpa database lokal seperti SQLite/Room).

## Cara Setup di XAMPP (Windows/Mac)

1. **Install XAMPP** kalau belum ada: https://www.apachefriends.org
2. **Copy folder ini** (`futclub-backend`) ke dalam folder `htdocs` XAMPP:
   - Windows: `C:\xampp\htdocs\futclub-backend`
   - Mac: `/Applications/XAMPP/htdocs/futclub-backend`
3. **Jalankan Apache dan MySQL** dari XAMPP Control Panel.
4. **Buat database**: buka `http://localhost/phpmyadmin`, lalu klik tab **Import**,
   pilih file `database/futclub.sql`, klik **Go**. Database `futclub_db` beserta
   tabel dan data kategori awal akan otomatis dibuat.
5. **Test API** di browser: buka `http://localhost/futclub-backend/api/categories.php`
   → kalau muncul JSON daftar kategori olahraga, berarti backend sudah jalan.

## Base URL untuk dipanggil dari Android (Retrofit)

- Kalau testing pakai **emulator Android Studio**, base URL-nya:
  ```
  http://10.0.2.2/futclub-backend/api/
  ```
  (`10.0.2.2` adalah alamat khusus yang menunjuk ke `localhost` komputer kamu, dilihat dari emulator)

- Kalau testing pakai **HP fisik** yang terhubung ke WiFi yang sama dengan laptop,
  base URL-nya pakai IP laptop kamu, misal:
  ```
  http://192.168.1.5/futclub-backend/api/
  ```
  (cek IP laptop dengan `ipconfig` di CMD / `ifconfig` di Mac/Linux)

## Daftar Endpoint

| Endpoint | Method | Fungsi |
|---|---|---|
| `auth.php` | POST | Login/registrasi via Google (Firebase) |
| `categories.php` | GET | Daftar kategori olahraga |
| `user_categories.php` | GET, POST | Ambil/simpan kategori pilihan user |
| `set_role.php` | PUT | Set role user jadi admin/olahragawan |
| `communities.php` | GET, POST | List komunitas (filter kategori) / buat komunitas baru |
| `community_detail.php` | GET, PUT, DELETE | Detail / edit / hapus komunitas |
| `join_community.php` | POST, DELETE | Join / keluar komunitas |
| `members.php` | GET | Daftar member sebuah komunitas |
| `gallery.php` | GET, POST, DELETE | Kelola foto gallery komunitas (maks 3) |
| `users.php` | GET, PUT | Lihat / edit profil user |

